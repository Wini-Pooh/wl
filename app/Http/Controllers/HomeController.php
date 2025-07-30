<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // Данные для клиентов
        if ($user->isClient()) {
            // Получаем проекты клиента (по имени, телефону или email)
            $projects = Project::where(function($query) use ($user) {
                $userPhone = preg_replace('/[^0-9]/', '', $user->phone ?? '');
                if ($userPhone) {
                    $query->whereRaw("REPLACE(REPLACE(REPLACE(client_phone, ' ', ''), '-', ''), '+', '') LIKE ?", ["%{$userPhone}%"]);
                }
                if ($user->email) {
                    $query->orWhere('client_email', $user->email);
                }
            })->orderBy('created_at', 'desc')->take(3)->get();
            
            $data['projects'] = $projects;
            $data['projectsCount'] = Project::where(function($query) use ($user) {
                $userPhone = preg_replace('/[^0-9]/', '', $user->phone ?? '');
                if ($userPhone) {
                    $query->whereRaw("REPLACE(REPLACE(REPLACE(client_phone, ' ', ''), '-', ''), '+', '') LIKE ?", ["%{$userPhone}%"]);
                }
                if ($user->email) {
                    $query->orWhere('client_email', $user->email);
                }
            })->count();
            
            // Последний проект для быстрого доступа
            $data['lastProject'] = $projects->first();
        }

        // Данные для партнеров и их сотрудников
        if ($user->isPartner() || $user->isEmployee() || $user->isAdmin()) {
            // Получаем ID партнера
            $partnerId = null;
            if ($user->isPartner()) {
                $partnerId = $user->id;
            } elseif ($user->isEmployee() || $user->isForeman() || $user->isEstimator()) {
                $employeeProfile = $user->employeeProfile;
                if ($employeeProfile && $employeeProfile->status === 'active') {
                    $partnerId = $employeeProfile->partner_id;
                }
            }
            
            if ($partnerId || $user->isAdmin()) {
                $query = Project::query();
                
                if (!$user->isAdmin() && $partnerId) {
                    $query->where('partner_id', $partnerId);
                }
                
                $data['recentProjects'] = $query->orderBy('created_at', 'desc')->take(5)->get();
                $data['totalProjects'] = $query->count();
                $data['activeProjects'] = $query->whereIn('project_status', ['in_progress', 'design', 'materials_preparation'])->count();
                $data['completedProjects'] = $query->where('project_status', 'completed')->count();
                
                // Статистика по месяцам
                $data['monthlyStats'] = $this->getMonthlyProjectStats($query);
            }
            
            // Новости и обновления
            $data['news'] = $this->getNewsAndUpdates();
            
            // FAQ
            $data['faq'] = $this->getFAQ();
        }

        $data['user'] = $user;
        
        return view('home', $data);
    }

    /**
     * Получить статистику проектов по месяцам
     */
    private function getMonthlyProjectStats($query)
    {
        $stats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = (clone $query)->whereYear('created_at', $date->year)
                                  ->whereMonth('created_at', $date->month)
                                  ->count();
            $stats[] = [
                'month' => $date->format('M'),
                'count' => $count
            ];
        }
        return $stats;
    }

    /**
     * Получить новости и обновления
     */
    private function getNewsAndUpdates()
    {
        return [
            [
                'title' => 'Обновление системы управления проектами',
                'date' => '2025-07-25',
                'description' => 'Добавлены новые функции для работы со схемами и документами проектов. Теперь вы можете загружать CAD-файлы и автоматически определять программное обеспечение.',
                'type' => 'update'
            ],
            [
                'title' => 'Новые возможности в системе смет',
                'date' => '2025-07-20',
                'description' => 'Улучшена система расчета смет с поддержкой шаблонов и автоматическим расчетом общей стоимости проекта.',
                'type' => 'feature'
            ],
            [
                'title' => 'Расширение функций для мобильных устройств',
                'date' => '2025-07-15',
                'description' => 'Оптимизирован интерфейс для работы на планшетах и смартфонах. Добавлена возможность загрузки фото с камеры устройства.',
                'type' => 'improvement'
            ]
        ];
    }

    /**
     * Получить часто задаваемые вопросы
     */
    private function getFAQ()
    {
        return [
            [
                'question' => 'Как загрузить документы проекта?',
                'answer' => 'Перейдите в раздел "Проекты", выберите нужный проект и откройте вкладку "Документы". Нажмите кнопку "Загрузить документы" и выберите файлы на вашем устройстве.'
            ],
            [
                'question' => 'Как работать со сметами?',
                'answer' => 'В разделе "Сметы" вы можете создавать новые сметы, использовать шаблоны или копировать существующие. Система автоматически рассчитает общую стоимость проекта.'
            ],
            [
                'question' => 'Как добавить схемы проекта?',
                'answer' => 'В разделе проекта откройте вкладку "Схемы". Вы можете загружать изображения, PDF-файлы и CAD-файлы (DWG, DXF, STEP и др.). Система автоматически определит тип файла.'
            ],
            [
                'question' => 'Как отследить статус проекта?',
                'answer' => 'Статус проекта отображается в списке проектов и на странице деталей. Вы можете отслеживать этапы выполнения работ и ключевые события.'
            ],
            [
                'question' => 'Кто имеет доступ к проектам?',
                'answer' => 'Партнеры и их сотрудники имеют полный доступ к своим проектам. Клиенты могут просматривать только свои проекты. Прорабы имеют доступ к назначенным им проектам.'
            ]
        ];
    }
}
