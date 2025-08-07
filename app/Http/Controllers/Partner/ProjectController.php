<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectPhoto;
use App\Models\Employee;
use App\Models\User;
use App\Helpers\ProjectAccessHelper;
use App\Traits\HasSubscriptionLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProjectController extends Controller
{
    use HasSubscriptionLimits;
    
    public function __construct()
    {
        // Доступ к проектам для партнеров, сотрудников, прорабов, клиентов и админов
        // Сметчики НЕ имеют доступа к проектам (только к сметам)
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
        
        // Ограничиваем доступ к созданию и редактированию для прорабов и клиентов
        $this->middleware(['role:partner,employee,admin'])->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Проверяет доступ пользователя к проекту
     */
    private function checkProjectAccess(Project $project)
    {
        /** @var User $user */
        $user = Auth::user();
        return ProjectAccessHelper::canAccessProject($user, $project);
    }

    /**
     * Отображение списка проектов
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Получаем проекты в зависимости от роли пользователя
        $projectsQuery = Project::query();
        
        if ($user->hasRole('admin')) {
            // Админ видит все проекты
            $projectsQuery = Project::with(['partner']);
        } elseif ($user->hasRole('partner')) {
            // Партнер видит только свои проекты
            $projectsQuery = $user->projects()->with(['partner']);
        } elseif ($user->hasRole('employee') || $user->hasRole('foreman')) {
            // Сотрудники и прорабы видят проекты, к которым имеют доступ
            $projectsQuery = $user->assignedProjects()->with(['partner']);
        } elseif ($user->hasRole('client')) {
            // Клиенты видят только проекты, где они указаны как клиенты (по телефону)
            $projectsQuery = Project::where('client_phone', $user->phone)->with(['partner']);
        }
        
        // Фильтрация по поисковому запросу
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $projectsQuery->where(function($query) use ($search) {
                $query->where('client_first_name', 'like', "%{$search}%")
                      ->orWhere('client_last_name', 'like', "%{$search}%")
                      ->orWhere('client_phone', 'like', "%{$search}%")
                      ->orWhere('object_city', 'like', "%{$search}%")
                      ->orWhere('object_street', 'like', "%{$search}%");
            });
        }
        
        // Фильтрация по статусу
        if ($request->has('status') && !empty($request->status)) {
            $projectsQuery->where('project_status', $request->status);
        }
        
        // Сортировка
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedSortFields = ['created_at', 'updated_at', 'client_first_name', 'project_status', 'start_date', 'end_date'];
        if (in_array($sortBy, $allowedSortFields)) {
            $projectsQuery->orderBy($sortBy, $sortDirection);
        }
        
        // Пагинация
        $projects = $projectsQuery->paginate(12);
        
        return view('partner.projects.index', compact('projects'));
    }

    /**
     * Показать конкретный проект (перенаправляет на главную страницу проекта)
     */
    public function show(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        // По умолчанию показываем главную страницу проекта
        return redirect()->route('partner.projects.main', $project);
    }

    /**
     * Показать финансовую информацию проекта
     */
    public function showFinance(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        $project->load([
            'works',
            'materials',
            'transports'
        ]);
        
        return view('partner.projects.pages.finance', compact('project'));
    }

    /**
     * Показать расписание проекта
     */
    public function showSchedule(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        $project->load([
            'stages',
            'events'
        ]);
        
        return view('partner.projects.pages.schedule', compact('project'));
    }

    /**
     * Показать главную страницу проекта
     */
    public function showMain(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        $project->load([
            'stages',
            'events',
            'works',
            'materials',
            'employees'
        ]);
        
        return view('partner.projects.pages.main', compact('project'));
    }

    /**
     * Показать фотографии проекта (без AJAX)
     */
    public function showPhotos(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        // Получаем фильтры из запроса
        $filters = [
            'category' => $request->get('category'),
            'location' => $request->get('location'),
            'search' => $request->get('search'),
            'sort' => $request->get('sort', 'newest')
        ];
        
        // Строим запрос с фильтрами
        $query = $project->photos();
        
        // Применяем фильтры
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        
        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }
        
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('original_name', 'LIKE', '%' . $filters['search'] . '%')
                  ->orWhere('filename', 'LIKE', '%' . $filters['search'] . '%')
                  ->orWhere('comment', 'LIKE', '%' . $filters['search'] . '%');
            });
        }
        
        // Применяем сортировку
        switch ($filters['sort']) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('original_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('original_name', 'desc');
                break;
            case 'size_asc':
                $query->orderBy('file_size', 'asc');
                break;
            case 'size_desc':
                $query->orderBy('file_size', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        // Используем пагинацию
        $photos = $query->paginate(20)->appends($request->query());
        
        // Получаем уникальные категории и локации для фильтров
        // Исправляем ошибку с DISTINCT и ORDER BY - используем getQuery() чтобы обойти orderBy в связи
        $categories = ProjectPhoto::where('project_id', $project->id)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values()
            ->toArray();
            
        $locations = ProjectPhoto::where('project_id', $project->id)
            ->whereNotNull('location')
            ->distinct()
            ->pluck('location')
            ->sort()
            ->values()
            ->toArray();
        
        // Предустановленные опции для фильтров
        $categoryOptions = [
            'before' => 'До ремонта',
            'after' => 'После ремонта',
            'process' => 'Процесс работы',
            'progress' => 'Ход работ',
            'materials' => 'Материалы',
            'problems' => 'Проблемы',
            'design' => 'Дизайн',
            'furniture' => 'Мебель',
            'decor' => 'Декор',
            'demolition' => 'Демонтаж',
            'floors' => 'Полы',
            'walls' => 'Стены',
            'ceiling' => 'Потолок',
            'electrical' => 'Электрика',
            'plumbing' => 'Сантехника',
            'heating' => 'Отопление',
            'doors' => 'Двери',
            'windows' => 'Окна'
        ];
        
        $locationOptions = [
            'living_room' => 'Гостиная',
            'kitchen' => 'Кухня',
            'bedroom' => 'Спальня',
            'bathroom' => 'Ванная',
            'toilet' => 'Туалет',
            'hallway' => 'Прихожая',
            'balcony' => 'Балкон',
            'storage' => 'Кладовка',
            'office' => 'Кабинет',
            'garage' => 'Гараж',
            'basement' => 'Подвал',
            'attic' => 'Чердак',
            'exterior' => 'Фасад'
        ];
        
        $sortOptions = [
            'newest' => 'Сначала новые',
            'oldest' => 'Сначала старые', 
            'name_asc' => 'По имени (А-Я)',
            'name_desc' => 'По имени (Я-А)',
            'size_asc' => 'По размеру (меньше)',
            'size_desc' => 'По размеру (больше)'
        ];
        
        return view('partner.projects.pages.photos-standard', compact(
            'project', 
            'photos', 
            'filters', 
            'categories', 
            'locations',
            'categoryOptions',
            'locationOptions', 
            'sortOptions'
        ));
    }

    /**
     * Показать дизайн проекта
     */
    public function showDesign(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        // Получаем фильтры из запроса
        $filters = [
            'search' => $request->get('search'),
            'design_type' => $request->get('design_type'),
            'room' => $request->get('room'),
            'style' => $request->get('style'),
            'sort' => $request->get('sort', 'newest')
        ];
        
        // Строим запрос с фильтрами
        $query = $project->designFiles();
        
        // Применяем фильтры
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['search'] . '%')
                  ->orWhere('original_name', 'LIKE', '%' . $filters['search'] . '%');
            });
        }
        
        if (!empty($filters['design_type'])) {
            $query->where('design_type', $filters['design_type']);
        }
        
        if (!empty($filters['room'])) {
            $query->where('room', $filters['room']);
        }
        
        if (!empty($filters['style'])) {
            $query->where('style', $filters['style']);
        }
        
        // Применяем сортировку
        switch ($filters['sort']) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('original_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('original_name', 'desc');
                break;
            case 'size_asc':
                $query->orderBy('file_size', 'asc');
                break;
            case 'size_desc':
                $query->orderBy('file_size', 'desc');
                break;
            default: // newest
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        // Используем пагинацию
        $designFiles = $query->paginate(12)->appends($request->query());
        
        // Опции для select'ов
        $designTypeOptions = [
            '3d' => '3D визуализация',
            'layout' => 'Планировка',
            'sketch' => 'Эскиз',
            'render' => 'Рендер',
            'draft' => 'Черновик',
            'concept' => 'Концепт',
            'mood_board' => 'Мудборд',
            'color_scheme' => 'Цветовая схема',
            'furniture' => 'Мебель',
            'lighting' => 'Освещение',
            'materials' => 'Материалы',
            'final' => 'Финальный дизайн',
        ];
        
        $roomOptions = [
            'kitchen' => 'Кухня',
            'living_room' => 'Гостиная',
            'bedroom' => 'Спальня',
            'bathroom' => 'Ванная',
            'toilet' => 'Туалет',
            'hallway' => 'Прихожая',
            'balcony' => 'Балкон',
            'corridor' => 'Коридор',
            'office' => 'Кабинет',
            'children' => 'Детская',
            'pantry' => 'Кладовая',
            'garage' => 'Гараж',
            'basement' => 'Подвал',
            'attic' => 'Чердак',
            'terrace' => 'Терраса',
            'general' => 'Общий план',
        ];
        
        $styleOptions = [
            'modern' => 'Современный',
            'classic' => 'Классический',
            'minimalism' => 'Минимализм',
            'loft' => 'Лофт',
            'scandinavian' => 'Скандинавский',
            'provence' => 'Прованс',
            'high_tech' => 'Хай-тек',
            'eco' => 'Эко',
            'art_deco' => 'Арт-деко',
            'neoclassic' => 'Неоклассика',
            'fusion' => 'Фьюжн',
            'industrial' => 'Индустриальный',
        ];
        
        $sortOptions = [
            'newest' => 'Сначала новые',
            'oldest' => 'Сначала старые',
            'name_asc' => 'По названию (А-Я)',
            'name_desc' => 'По названию (Я-А)',
            'size_asc' => 'По размеру (возрастание)',
            'size_desc' => 'По размеру (убывание)',
        ];
        
        return view('partner.projects.pages.design-standard', compact(
            'project', 
            'designFiles', 
            'filters',
            'designTypeOptions',
            'roomOptions',
            'styleOptions',
            'sortOptions'
        ));
    }

    /**
     * Показать конкретный файл дизайна
     */
    public function showDesignFile(Project $project, $fileId)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }

        $designFile = $project->designFiles()->with('uploader')->findOrFail($fileId);

        return view('partner.projects.pages.design-view', compact('project', 'designFile'));
    }

    /**
     * Показать схемы проекта
     */
    public function showSchemes(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        // Получаем фильтры из запроса
        $filters = [
            'search' => $request->get('search'),
            'scheme_type' => $request->get('scheme_type'),
            'room' => $request->get('room'),
            'sort' => $request->get('sort', 'created_at_desc')
        ];
        
        // Строим запрос с фильтрами
        $query = $project->schemes();
        
        // Применяем фильтры
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['search'] . '%')
                  ->orWhere('original_name', 'LIKE', '%' . $filters['search'] . '%');
            });
        }
        
        if (!empty($filters['scheme_type'])) {
            $query->where('scheme_type', $filters['scheme_type']);
        }
        
        if (!empty($filters['room'])) {
            $query->where('room', $filters['room']);
        }
        
        // Применяем сортировку
        switch ($filters['sort']) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('original_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('original_name', 'desc');
                break;
            case 'size_asc':
                $query->orderBy('file_size', 'asc');
                break;
            case 'size_desc':
                $query->orderBy('file_size', 'desc');
                break;
            default: // created_at_desc
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        // Используем пагинацию
        $schemes = $query->paginate(12)->appends($request->query());
        
        // Опции для select'ов
        $schemeTypeOptions = [
            'electrical' => 'Электрика',
            'plumbing' => 'Сантехника',
            'ventilation' => 'Вентиляция',
            'layout' => 'Планировка',
            'structure' => 'Конструкция',
            'technical' => 'Техническая схема',
            'construction' => 'Строительный чертеж',
            'other' => 'Другое',
        ];
        
        $roomOptions = [
            'kitchen' => 'Кухня',
            'living_room' => 'Гостиная',
            'bedroom' => 'Спальня',
            'bathroom' => 'Ванная',
            'hallway' => 'Прихожая',
            'general' => 'Общий план',
            'other' => 'Другое',
        ];
        
        $sortOptions = [
            'created_at_desc' => 'Сначала новые',
            'created_at_asc' => 'Сначала старые',
            'name_asc' => 'По названию (А-Я)',
            'name_desc' => 'По названию (Я-А)',
            'size_asc' => 'По размеру (возрастание)',
            'size_desc' => 'По размеру (убывание)',
        ];
        
        return view('partner.projects.pages.schemes', compact(
            'project', 
            'schemes', 
            'filters',
            'schemeTypeOptions',
            'roomOptions',
            'sortOptions'
        ));
    }

    /**
     * Загрузить схемы проекта
     */
    public function uploadSchemes(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }

        $request->validate([
            'schemes' => 'required|array',
            'schemes.*' => 'file|mimes:jpeg,png,gif,webp,svg,pdf,dwg,dxf,ai|max:51200', // 50MB
            'scheme_type' => 'nullable|string|max:50',
            'room' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('schemes') as $file) {
            try {
                // Генерируем уникальное имя файла
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Путь для сохранения
                $directory = "projects/{$project->id}/schemes";
                $filePath = $directory . '/' . $fileName;
                
                // Сохраняем файл
                $file->storeAs($directory, $fileName, 'public');
                
                // Создаем запись в БД
                $project->schemes()->create([
                    'name' => $fileName,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'scheme_type' => $request->get('scheme_type', 'technical'),
                    'room' => $request->get('room'),
                    'description' => $request->get('description'),
                    'uploaded_by' => auth()->id(),
                ]);
                
                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = "Ошибка загрузки файла {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        if ($uploadedCount > 0) {
            $message = "Успешно загружено схем: {$uploadedCount}";
            if (!empty($errors)) {
                $message .= ". Ошибки: " . implode('; ', $errors);
            }
            return redirect()->route('partner.projects.schemes', $project)->with('success', $message);
        } else {
            return redirect()->route('partner.projects.schemes', $project)->with('error', 'Не удалось загрузить ни одной схемы. ' . implode('; ', $errors));
        }
    }

    /**
     * Удалить схему проекта
     */
    public function deleteScheme(Project $project, $schemeId)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }

        try {
            $scheme = $project->schemes()->findOrFail($schemeId);
            
            // Удаляем файл из хранилища
            if (Storage::disk('public')->exists($scheme->file_path)) {
                Storage::disk('public')->delete($scheme->file_path);
            }
            
            // Удаляем запись из БД
            $scheme->delete();
            
            return redirect()->route('partner.projects.schemes', $project)->with('success', 'Схема успешно удалена');
        } catch (\Exception $e) {
            return redirect()->route('partner.projects.schemes', $project)->with('error', 'Ошибка при удалении схемы: ' . $e->getMessage());
        }
    }

    /**
     * Скачать схему проекта
     */
    public function downloadScheme(Project $project, $schemeId)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }

        try {
            $scheme = $project->schemes()->findOrFail($schemeId);
            
            if (!Storage::disk('public')->exists($scheme->file_path)) {
                return redirect()->route('partner.projects.schemes', $project)->with('error', 'Файл не найден');
            }
            
            $fileContent = Storage::disk('public')->get($scheme->file_path);
            return response($fileContent, 200, [
                'Content-Type' => $scheme->mime_type,
                'Content-Disposition' => 'attachment; filename="' . $scheme->original_name . '"'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('partner.projects.schemes', $project)->with('error', 'Ошибка при скачивании схемы: ' . $e->getMessage());
        }
    }

        /**
     * Показать документы проекта
     */
    public function showDocuments(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }
        
        // Получаем фильтры из запроса
        $filters = [
            'search' => $request->get('search', ''),
            'document_type' => $request->get('document_type', ''),
            'status' => $request->get('status', ''),
            'date_from' => $request->get('date_from', ''),
            'date_to' => $request->get('date_to', ''),
            'sort' => $request->get('sort', 'created_at_desc')
        ];
        
        // Строим запрос с фильтрами
        $query = $project->documents();
        
        // Применяем фильтры
        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('original_name', 'LIKE', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'LIKE', '%' . $filters['search'] . '%');
            });
        }
        
        if (!empty($filters['document_type'])) {
            $query->where('document_type', $filters['document_type']);
        }
        
        if (!empty($filters['status'])) {
            $query->where('signature_status', $filters['status']);
        }
        
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        // Применяем сортировку
        switch ($filters['sort']) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('original_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('original_name', 'desc');
                break;
            case 'size_asc':
                $query->orderBy('file_size', 'asc');
                break;
            case 'size_desc':
                $query->orderBy('file_size', 'desc');
                break;
            default: // created_at_desc
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        // Используем пагинацию
        $documents = $query->paginate(12)->appends($request->query());
        
        // Опции для select'ов
        $documentTypeOptions = [
            'contract' => 'Договор',
            'invoice' => 'Счет',
            'report' => 'Отчет',
            'specification' => 'Спецификация',
            'technical' => 'Техническая документация',
            'permit' => 'Разрешение',
            'certificate' => 'Сертификат',
            'other' => 'Другое',
        ];
        
        $statusOptions = [
            'unsigned' => 'Не подписан',
            'signing' => 'На подписании',
            'signed' => 'Подписан',
            'rejected' => 'Отклонен',
        ];
        
        $sortOptions = [
            'created_at_desc' => 'Сначала новые',
            'created_at_asc' => 'Сначала старые',
            'name_asc' => 'По названию (А-Я)',
            'name_desc' => 'По названию (Я-А)',
            'size_asc' => 'По размеру (возрастание)',
            'size_desc' => 'По размеру (убывание)',
        ];
        
        return view('partner.projects.pages.documents', compact(
            'project', 
            'documents', 
            'filters',
            'documentTypeOptions',
            'statusOptions',
            'sortOptions'
        ));
    }

    /**
     * Показать форму создания нового проекта
     */
    public function create()
    {
        return view('partner.projects.create');
    }

    /**
     * Сохранить новый проект
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Обязательные поля
            'client_first_name' => 'required|string|max:255',
            'client_last_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:255',
            'object_type' => 'required|string|max:255',
            'work_type' => 'required|string|max:255',
            'project_status' => 'required|string|max:255',
            
            // Паспортные данные
            'passport_series' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'passport_issued_by' => 'nullable|string|max:255',
            'passport_issued_date' => 'nullable|date',
            'passport_department_code' => 'nullable|string|max:255',
            
            // Личные данные
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            
            // Адрес прописки
            'registration_postal_code' => 'nullable|string|max:255',
            'registration_city' => 'nullable|string|max:255',
            'registration_street' => 'nullable|string|max:255',
            'registration_house' => 'nullable|string|max:255',
            'registration_apartment' => 'nullable|string|max:255',
            
            // Характеристики объекта
            'apartment_number' => 'nullable|string|max:255',
            'object_city' => 'nullable|string|max:255',
            'object_street' => 'nullable|string|max:255',
            'object_house' => 'nullable|string|max:255',
            'object_entrance' => 'nullable|string|max:255',
            'object_area' => 'nullable|numeric|min:0',
            'camera_link' => 'nullable|string|max:255',
            
            // Сроки
            'contract_date' => 'nullable|date',
            'work_start_date' => 'nullable|date',
            'estimated_end_date' => 'nullable|date',
            'contract_number' => 'nullable|string|max:255',
        ]);
        
        // Добавляем ID партнера
        $validated['partner_id'] = Auth::id();
        
        // Устанавливаем финансовые показатели по умолчанию
        $validated['work_cost'] = 0.00;
        $validated['materials_cost'] = 0.00;
        $validated['additional_work_cost'] = 0.00;
        $validated['total_cost'] = 0.00;
        
        try {
            $project = Project::create($validated);
            
            return redirect()
                ->route('partner.projects.show', $project)
                ->with('success', 'Проект успешно создан');
                
        } catch (\Exception $e) {
            Log::error('Error creating project: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Ошибка при создании проекта');
        }
    }

    /**
     * Загрузить документы проекта
     */
    public function uploadDocuments(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }

        $request->validate([
            'documents' => 'required|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf,zip,rar,7z,jpg,jpeg,png,gif,webp,svg|max:51200', // 50MB
            'document_type' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
        ]);

        $uploadedCount = 0;
        $errors = [];

        foreach ($request->file('documents') as $file) {
            try {
                // Генерируем уникальное имя файла
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = "projects/{$project->id}/documents/{$fileName}";
                
                // Загружаем файл
                Storage::disk('public')->put($filePath, file_get_contents($file));
                
                // Создаем запись в БД
                $project->documents()->create([
                    'name' => $fileName,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'document_type' => $request->get('document_type', 'other'),
                    'status' => $request->get('status', 'active'),
                    'description' => $request->get('description'),
                    'uploaded_by' => auth()->id(),
                ]);
                
                $uploadedCount++;
            } catch (\Exception $e) {
                $errors[] = "Ошибка загрузки файла {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        if ($uploadedCount > 0) {
            $message = "Успешно загружено документов: {$uploadedCount}";
            if (!empty($errors)) {
                $message .= ". Ошибки: " . implode('; ', $errors);
            }
            return redirect()->route('partner.projects.documents', $project)->with('success', $message);
        } else {
            return redirect()->route('partner.projects.documents', $project)->with('error', 'Не удалось загрузить ни одного документа. ' . implode('; ', $errors));
        }
    }

    /**
     * Удалить документ проекта
     */
    public function deleteDocument(Project $project, $documentId)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }

        try {
            $document = $project->documents()->findOrFail($documentId);
            
            // Удаляем файл из хранилища
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            
            // Удаляем запись из БД
            $document->delete();
            
            return redirect()->route('partner.projects.documents', $project)->with('success', 'Документ успешно удален');
            
        } catch (\Exception $e) {
            Log::error('Error deleting document: ' . $e->getMessage());
            return redirect()->route('partner.projects.documents', $project)->with('error', 'Ошибка удаления документа');
        }
    }

    /**
     * Скачать документ проекта
     */
    public function downloadDocument(Project $project, $documentId)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }

        try {
            $document = $project->documents()->findOrFail($documentId);
            
            if (!Storage::disk('public')->exists($document->file_path)) {
                abort(404, 'Файл не найден');
            }
            
            return response()->download(
                storage_path('app/public/' . $document->file_path),
                $document->original_name
            );
            
        } catch (\Exception $e) {
            Log::error('Error downloading document: ' . $e->getMessage());
            abort(404, 'Файл не найден');
        }
    }

    /**
     * Показать форму редактирования проекта
     */
    public function edit(Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }

        // Проверяем права на редактирование
        /** @var User $user */
        $user = Auth::user();
        if (!$user->hasRole(['partner', 'employee', 'admin'])) {
            abort(403, 'Недостаточно прав для редактирования проекта');
        }

        // Получаем список сотрудников для назначения
        $employees = Employee::all();

        return view('partner.projects.edit', compact('project', 'employees'));
    }

    /**
     * Обновить проект
     */
    public function update(Request $request, Project $project)
    {
        // Проверка доступа
        if (!$this->checkProjectAccess($project)) {
            abort(403, 'Нет доступа к этому проекту');
        }

        // Проверяем права на редактирование
        /** @var User $user */
        $user = Auth::user();
        if (!$user->hasRole(['partner', 'employee', 'admin'])) {
            abort(403, 'Недостаточно прав для редактирования проекта');
        }

        $validated = $request->validate([
            // Обязательные поля
            'client_first_name' => 'required|string|max:255',
            'client_last_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:255',
            'object_type' => 'required|string|max:255',
            'work_type' => 'required|string|max:255',
            'project_status' => 'required|string|max:255',
            
            // Паспортные данные
            'passport_series' => 'nullable|string|max:255',
            'passport_number' => 'nullable|string|max:255',
            'passport_issued_by' => 'nullable|string|max:255',
            'passport_issued_date' => 'nullable|date',
            'passport_department_code' => 'nullable|string|max:255',
            
            // Личные данные
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'client_email' => 'nullable|email|max:255',
            
            // Адрес прописки
            'registration_postal_code' => 'nullable|string|max:255',
            'registration_city' => 'nullable|string|max:255',
            'registration_street' => 'nullable|string|max:255',
            'registration_house' => 'nullable|string|max:255',
            'registration_apartment' => 'nullable|string|max:255',
            
            // Характеристики объекта
            'apartment_number' => 'nullable|string|max:255',
            'object_city' => 'nullable|string|max:255',
            'object_street' => 'nullable|string|max:255',
            'object_house' => 'nullable|string|max:255',
            'object_entrance' => 'nullable|string|max:255',
            'object_area' => 'nullable|numeric|min:0',
            'camera_link' => 'nullable|string|max:255',
            
            // Финансовые показатели (только для отображения, не обновляются)
            // work_cost, materials_cost, additional_work_cost, total_cost - автоматически из смет
            
            // Сроки
            'contract_date' => 'nullable|date',
            'work_start_date' => 'nullable|date',
            'estimated_end_date' => 'nullable|date',
            'contract_number' => 'nullable|string|max:255',
        ]);

        try {
            $project->update($validated);

            return redirect()
                ->route('partner.projects.show', $project)
                ->with('success', 'Проект успешно обновлен');

        } catch (\Exception $e) {
            Log::error('Error updating project: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении проекта');
        }
    }

    /**
     * Поиск проектов по номеру телефона
     */
    public function searchByPhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);
        
        /** @var User $user */
        $user = Auth::user();
        
        $query = Project::where('client_phone', 'like', '%' . $request->phone . '%');
        
        // Фильтрация в зависимости от роли пользователя
        if ($user->hasRole('admin')) {
            // Админ видит все проекты
        } elseif ($user->hasRole('partner')) {
            // Партнер видит только свои проекты
            $query->where('partner_id', $user->id);
        } elseif ($user->hasRole('employee') || $user->hasRole('foreman')) {
            // Сотрудники и прорабы видят проекты своего партнера
            $projects = ProjectAccessHelper::getAccessibleProjects($user);
            $projectIds = $projects->pluck('id');
            $query->whereIn('id', $projectIds);
        }
        
        $projects = $query->get();
        
        return response()->json($projects);
    }
}
