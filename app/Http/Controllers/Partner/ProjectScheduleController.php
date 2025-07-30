<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectStage;
use App\Models\ProjectEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProjectScheduleController extends Controller
{
    public function __construct()
    {
        // Доступ к расписанию проектов для партнеров, сотрудников, прорабов, клиентов и админов
        // Сметчики НЕ имеют доступа к планированию проектов (только к сметам)
        // Клиенты имеют доступ только на чтение
        $this->middleware(['auth', 'role:partner,employee,foreman,client,admin']);
    }

    /**
     * Проверка доступа к проекту
     */
    private function checkProjectAccess(Project $project)
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            return true;
        }
        
        if ($user->hasRole('partner')) {
            return $project->partner_id === $user->id;
        }
        
        if (($user->hasRole('employee') || $user->hasRole('foreman')) && isset($user->employeeProfile)) {
            return $project->partner_id === $user->employeeProfile->partner_id;
        }
        
        if ($user->hasRole('client')) {
            // Для клиентов - проверяем номер телефона пользователя с номером в проекте
            $userPhone = $user->phone ?? $user->email; // fallback на email если нет телефона
            
            if (!$userPhone) {
                abort(403, 'Нет доступа к этому проекту');
            }
            
            // Очищаем номера телефонов от символов для сравнения
            $userPhoneClean = preg_replace('/[^0-9]/', '', $userPhone);
            $projectPhoneClean = preg_replace('/[^0-9]/', '', $project->client_phone);
            
            if ($userPhoneClean === $projectPhoneClean) {
                return true;
            }
            
            abort(403, 'Нет доступа к этому проекту');
        }
        
        abort(403, 'Нет доступа к этому проекту');
    }

    /**
     * Получить все данные расписания проекта
     */
    public function index(Request $request, Project $project)
    {
        try {
            // Добавляем отладочную информацию
            $userRoles = 'not authenticated';
            if (auth()->check()) {
                $user = auth()->user();
                $roles = [];
                
                // Добавляем роль по умолчанию
                if ($user->defaultRole) {
                    $roles[] = $user->defaultRole->name;
                }
                
                // Добавляем дополнительные роли
                foreach ($user->roles as $role) {
                    if (!in_array($role->name, $roles)) {
                        $roles[] = $role->name;
                    }
                }
                
                $userRoles = implode(', ', $roles);
            }
            
            \Log::info('Schedule index called', [
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'is_authenticated' => auth()->check(),
                'user_roles' => $userRoles
            ]);
            
            $this->checkProjectAccess($project);
            
            // Сортируем этапы: незавершенные сначала, завершенные в конце
            $stages = $project->stages()
                ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
                
            $events = $project->events()->orderBy('event_date', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'stages' => $stages,
                'events' => $events
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке данных расписания: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Получить сводку по расписанию проекта
     */
    public function summary(Request $request, Project $project)
    {
        try {
            $this->checkProjectAccess($project);
            
            $stages = $project->stages()->get();
            $events = $project->events()->get();
            
            $totalStages = $stages->count();
            $completedStages = $stages->where('status', 'completed')->count();
            $inProgressStages = $stages->where('status', 'in_progress')->count();
            $pendingStages = $stages->where('status', 'pending')->count();
            
            $upcomingEvents = $events->where('event_date', '>=', now())->count();
            $pastEvents = $events->where('event_date', '<', now())->count();
            
            // Прогресс проекта
            $progress = $totalStages > 0 ? round(($completedStages / $totalStages) * 100, 1) : 0;
            
            // Ближайшие события
            $nextEvents = $events->where('event_date', '>=', now())
                ->sortBy('event_date')
                ->take(5)
                ->map(function ($event) {
                    return [
                        'id' => $event->id,
                        'title' => $event->title,
                        'event_date' => $event->event_date->format('d.m.Y H:i'),
                        'type' => $event->type,
                        'status' => $event->status,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'summary' => [
                    'stages' => [
                        'total' => $totalStages,
                        'completed' => $completedStages,
                        'in_progress' => $inProgressStages,
                        'pending' => $pendingStages,
                        'progress' => $progress
                    ],
                    'events' => [
                        'total' => $events->count(),
                        'upcoming' => $upcomingEvents,
                        'past' => $pastEvents,
                        'next_events' => $nextEvents
                    ],
                    'timeline' => [
                        'project_start' => $project->start_date ? $project->start_date->format('d.m.Y') : null,
                        'project_end' => $project->end_date ? $project->end_date->format('d.m.Y') : null,
                        'first_stage_start' => $stages->where('start_date', '!=', null)->min('start_date'),
                        'last_stage_end' => $stages->where('end_date', '!=', null)->max('end_date'),
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки сводки по расписанию'
            ], 500);
        }
    }

    // ===== ЭТАПЫ =====

    /**
     * Добавить этап к проекту
     */
    public function storeStage(Request $request, Project $project)
    {
        $this->checkProjectAccess($project);

        // Логируем входящие данные для отладки
        Log::info('Stage creation request data:', $request->all());

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'nullable|string|in:not_started,in_progress,completed,on_hold',
                'planned_start_date' => 'nullable|date',
                'planned_end_date' => 'nullable|date|after_or_equal:planned_start_date',
                'actual_start_date' => 'nullable|date',
                'actual_end_date' => 'nullable|date|after_or_equal:actual_start_date',
                'duration_days' => 'nullable|integer|min:0',
                'progress' => 'nullable|numeric|min:0|max:100', // Изменено с integer на numeric для поддержки десятичных значений
                'order' => 'nullable|integer|min:0',
            ]);

            Log::info('Stage validation passed:', $validated);

            $validated['project_id'] = $project->id;
            
            // Устанавливаем статус по умолчанию, если не указан
            if (!isset($validated['status'])) {
                $validated['status'] = 'not_started';
            }
            
            // Если порядок не указан, ставим в конец
            if (!isset($validated['order'])) {
                $lastOrder = $project->stages()->max('order') ?? 0;
                $validated['order'] = $lastOrder + 1;
            }

            $stage = ProjectStage::create($validated);

            Log::info('Stage created successfully:', $stage->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Этап успешно добавлен',
                'stage' => $stage
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Stage validation failed:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Stage creation error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при создании этапа'
            ], 500);
        }
    }

        /**
     * Получить данные этапа
     */
    public function showStage(Project $project, ProjectStage $stage)
    {
        $this->checkProjectAccess($project);
        
        if ($stage->project_id !== $project->id) {
            return response()->json(['message' => 'Этап не найден'], 404);
        }

        return response()->json([
            'success' => true,
            'stage' => $stage
        ]);
    }

    /**
     * Обновить этап
     */
    public function updateStage(Request $request, Project $project, ProjectStage $stage)
    {
        $this->checkProjectAccess($project);
        
        if ($stage->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:not_started,in_progress,completed,on_hold',
            'planned_start_date' => 'nullable|date',
            'planned_end_date' => 'nullable|date|after_or_equal:planned_start_date',
            'order' => 'nullable|integer|min:0',
        ]);

        $stage->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Этап успешно обновлен',
            'stage' => $stage->fresh()
        ]);
    }

        /**
     * Удалить этап
     */
    public function destroyStage(Project $project, ProjectStage $stage)
    {
        $this->checkProjectAccess($project);
        
        if ($stage->project_id !== $project->id) {
            return response()->json(['message' => 'Этап не найден'], 404);
        }

        $stage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Этап успешно удален'
        ]);
    }

        /**
     * Пометить этап как завершенный
     */
    public function completeStage(Project $project, ProjectStage $stage)
    {
        $this->checkProjectAccess($project);
        
        if ($stage->project_id !== $project->id) {
            return response()->json(['message' => 'Этап не принадлежит данному проекту'], 403);
        }

        $stage->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Этап успешно завершен',
            'stage' => $stage->fresh()
        ]);
    }

    /**
     * Пометить событие как завершенное
     */
    public function completeEvent(Project $project, ProjectEvent $event)
    {
        $this->checkProjectAccess($project);
        
        if ($event->project_id !== $project->id) {
            return response()->json(['message' => 'Событие не принадлежит данному проекту'], 403);
        }

        $event->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Событие успешно завершено',
            'event' => $event->fresh()
        ]);
    }

    // ===== СОБЫТИЯ =====

    /**
     * Добавить событие к проекту
     */
    public function storeEvent(Request $request, Project $project)
    {
        $this->checkProjectAccess($project);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:meeting,delivery,inspection,milestone,other',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:planned,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $validated['project_id'] = $project->id;

        $event = ProjectEvent::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Событие успешно добавлено',
            'event' => $event
        ]);
    }

    /**
     * Получить данные события
     */
    public function showEvent(Project $project, ProjectEvent $event)
    {
        $this->checkProjectAccess($project);
        
        if ($event->project_id !== $project->id) {
            abort(404);
        }

        return response()->json([
            'success' => true,
            'event' => $event
        ]);
    }

    /**
     * Обновить событие
     */
    public function updateEvent(Request $request, Project $project, ProjectEvent $event)
    {
        $this->checkProjectAccess($project);
        
        if ($event->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:meeting,delivery,inspection,milestone,other',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
        ]);

        $event->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Событие успешно обновлено',
            'event' => $event->fresh()
        ]);
    }

    /**
     * Удалить событие
     */
    public function destroyEvent(Project $project, ProjectEvent $event)
    {
        $this->checkProjectAccess($project);
        
        if ($event->project_id !== $project->id) {
            abort(404);
        }

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Событие успешно удалено'
        ]);
    }

    // ===== ЧАСТИЧНАЯ ЗАГРУЗКА ДЛЯ AJAX =====

    /**
     * Получить этапы в формате JSON для AJAX
     */
    public function getStagesPartial(Project $project)
    {
        try {
            $this->checkProjectAccess($project);
            
            // Сортируем этапы: незавершенные сначала, завершенные в конце
            $stages = $project->stages()
                ->orderByRaw("CASE WHEN status = 'completed' THEN 1 ELSE 0 END")
                ->orderBy('order', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
            
            return response()->json([
                'success' => true,
                'stages' => $stages
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки этапов: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить события в формате JSON для AJAX
     */
    public function getEventsPartial(Project $project)
    {
        try {
            $this->checkProjectAccess($project);
            
            $events = $project->events()
                ->orderBy('event_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'events' => $events
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка загрузки событий: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получить счетчики для обновления бейджей
     */
    public function getCounts(Project $project)
    {
        $this->checkProjectAccess($project);
        
        $stagesCount = $project->stages()->count();
        $completedStagesCount = $project->stages()->where('status', 'completed')->count();
        $eventsCount = $project->events()->count();
        
        return response()->json([
            'success' => true,
            'counts' => [
                'stages' => $stagesCount,
                'completed_stages' => $completedStagesCount,
                'events' => $eventsCount
            ]
        ]);
    }
}
