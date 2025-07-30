@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Приветствие для всех пользователей -->
            <div class="welcome-section mb-4">
                <div class="card border-0 bg-gradient-primary ">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h1 class="h3 mb-2">Добро пожаловать, {{ $user->name }}!</h1>
                                @if($user->isClient())
                                    <p class="mb-0 opacity-75">Здесь вы можете отслеживать статус ваших проектов и получать актуальную информацию о ходе работ.</p>
                                @elseif($user->isPartner())
                                    <p class="mb-0 opacity-75">Управляйте своими проектами, отслеживайте прогресс и получайте последние обновления системы.</p>
                                @elseif($user->isEmployee() || $user->isForeman())
                                    <p class="mb-0 opacity-75">Добро пожаловать в рабочую панель. Здесь вы найдете актуальную информацию по проектам и новости системы.</p>
                                @elseif($user->isAdmin())
                                    <p class="mb-0 opacity-75">Панель администратора. Управляйте системой и отслеживайте общую статистику.</p>
                                @else
                                    <p class="mb-0 opacity-75">Добро пожаловать в систему управления проектами.</p>
                                @endif
                            </div>
                            <div class="col-lg-4 text-end">
                                <i class="bi bi-house-heart-fill display-4 opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($user->isClient())
                <!-- Секция для клиентов -->
                <div class="row">
                    <!-- Статистика проектов -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center">
                                <div class="display-6 text-primary mb-2">{{ $projectsCount }}</div>
                                <h6 class="card-title text-muted">Ваших проектов</h6>
                            </div>
                        </div>
                    </div>

                    <!-- Быстрый доступ к проектам -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">
                                    <i class="bi bi-building text-primary me-2"></i>
                                    Ваши проекты
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($projects->count() > 0)
                                    <div class="row">
                                        @foreach($projects as $project)
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card border project-card h-100">
                                                    <div class="card-body p-3">
                                                        <h6 class="card-title">{{ $project->object_type }}</h6>
                                                        <p class="card-text small text-muted mb-2">
                                                            <i class="bi bi-geo-alt me-1"></i>
                                                            {{ $project->object_city }}, {{ $project->object_street }}
                                                        </p>
                                                        <span class="badge bg-{{ $project->project_status == 'completed' ? 'success' : ($project->project_status == 'in_progress' ? 'warning' : 'secondary') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $project->project_status)) }}
                                                        </span>
                                                        <div class="mt-2">
                                                            <small class="text-muted">{{ $project->created_at->format('d.m.Y') }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($projectsCount > 3)
                                        <div class="text-center mt-3">
                                            <a href="#" class="btn btn-outline-primary">Посмотреть все проекты</a>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-building display-4 text-muted mb-3"></i>
                                        <p class="text-muted">У вас пока нет проектов</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($user->isPartner() || $user->isEmployee() || $user->isAdmin())
                <!-- Секция для партнеров и сотрудников -->
                <div class="row">
                    <!-- Статистика -->
                    <div class="col-lg-12 mb-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm bg-primary ">
                                    <div class="card-body text-center">
                                        <div class="display-6 mb-2">{{ $totalProjects }}</div>
                                        <h6 class="card-title">Всего проектов</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm bg-warning ">
                                    <div class="card-body text-center">
                                        <div class="display-6 mb-2">{{ $activeProjects }}</div>
                                        <h6 class="card-title">Активных проектов</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm bg-success ">
                                    <div class="card-body text-center">
                                        <div class="display-6 mb-2">{{ $totalProjects - $activeProjects }}</div>
                                        <h6 class="card-title">Завершенных</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Последние проекты -->
                    <div class="col-lg-8 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">
                                    <i class="bi bi-clock-history text-primary me-2"></i>
                                    Последние проекты
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($recentProjects->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($recentProjects as $project)
                                            <div class="list-group-item border-0 px-0">
                                                <div class="row align-items-center">
                                                    <div class="col-8">
                                                        <h6 class="mb-1">{{ $project->client_first_name }} {{ $project->client_last_name }}</h6>
                                                        <p class="mb-1 small text-muted">{{ $project->object_type }} - {{ $project->work_type }}</p>
                                                        <small class="text-muted">{{ $project->created_at->format('d.m.Y') }}</small>
                                                    </div>
                                                    <div class="col-4 text-end">
                                                        <span class="badge bg-{{ $project->project_status == 'completed' ? 'success' : ($project->project_status == 'in_progress' ? 'warning' : 'secondary') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $project->project_status)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-folder display-4 text-muted mb-3"></i>
                                        <p class="text-muted">Проекты не найдены</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Новости и обновления -->
                    <div class="col-lg-4 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">
                                    <i class="bi bi-newspaper text-primary me-2"></i>
                                    Новости и обновления
                                </h5>
                            </div>
                            <div class="card-body">
                                @foreach($news as $item)
                                    <div class="news-item mb-3 pb-3 @if(!$loop->last) border-bottom @endif">
                                        <div class="d-flex align-items-start">
                                            <div class="news-icon me-2">
                                                @if($item['type'] == 'update')
                                                    <i class="bi bi-arrow-up-circle text-primary"></i>
                                                @elseif($item['type'] == 'feature')
                                                    <i class="bi bi-star text-warning"></i>
                                                @else
                                                    <i class="bi bi-gear text-info"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="news-title mb-1">{{ $item['title'] }}</h6>
                                                <p class="news-description small text-muted mb-1">{{ $item['description'] }}</p>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($item['date'])->format('d.m.Y') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- FAQ -->
                    <div class="col-lg-12 mb-4">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0">
                                <h5 class="mb-0">
                                    <i class="bi bi-question-circle text-primary me-2"></i>
                                    Часто задаваемые вопросы
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="accordion" id="faqAccordion">
                                    @foreach($faq as $index => $item)
                                        <div class="accordion-item border-0 mb-3">
                                            <h2 class="accordion-header" id="heading{{ $index }}">
                                                <button class="accordion-button collapsed bg-light border-0 rounded" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                                    {{ $item['question'] }}
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}" data-bs-parent="#faqAccordion">
                                                <div class="accordion-body bg-light">
                                                    {{ $item['answer'] }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
}

.project-card {
    transition: transform 0.2s;
    cursor: pointer;
}

.project-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.news-item:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #495057;
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.welcome-section .card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2rem;
    }
    
    .display-6 {
        font-size: 1.5rem;
    }
    
    .welcome-section h1 {
        font-size: 1.5rem;
    }
}
</style>
@endsection
