@if($stages->count() > 0)
<div class="timeline">
    @foreach($stages as $stage)
    <div class="timeline-item" data-stage-id="{{ $stage->id }}">
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <h6 class="mb-2">
                    {{ $stage->name }}
                    <span class="badge bg-{{ $stage->status == 'completed' ? 'success' : ($stage->status == 'in_progress' ? 'primary' : ($stage->status == 'on_hold' ? 'warning' : 'secondary')) }} ms-2">
                        @if($stage->status == 'completed')
                            Завершен
                        @elseif($stage->status == 'in_progress')
                            В работе
                        @elseif($stage->status == 'on_hold')
                            Приостановлен
                        @else
                            Не начато
                        @endif
                    </span>
                </h6>
                @if($stage->description)
                <p class="text-muted mb-2">{{ $stage->description }}</p>
                @endif
                <div class="row text-muted small">
                    <div class="col-md-12">
                        @if($stage->planned_start_date && $stage->planned_end_date)
                        <div class="mb-1">
                            <strong>Плановые даты:</strong> 
                            <span class="badge bg-light text-dark">
                                {{ \Carbon\Carbon::parse($stage->planned_start_date)->format('d.m.Y') }} - 
                                {{ \Carbon\Carbon::parse($stage->planned_end_date)->format('d.m.Y') }}
                            </span>
                        </div>
                        @elseif($stage->planned_start_date)
                        <div class="mb-1">
                            <strong>План. начало:</strong> 
                            <span class="badge bg-light text-dark">{{ \Carbon\Carbon::parse($stage->planned_start_date)->format('d.m.Y') }}</span>
                        </div>
                        @elseif($stage->planned_end_date)
                        <div class="mb-1">
                            <strong>План. окончание:</strong> 
                            <span class="badge bg-light text-dark">{{ \Carbon\Carbon::parse($stage->planned_end_date)->format('d.m.Y') }}</span>
                        </div>
                        @endif
                        
                        @if($stage->actual_start_date && $stage->actual_end_date)
                        <div class="mb-1">
                            <strong>Фактические даты:</strong> 
                            <span class="badge bg-success text-white">
                                {{ \Carbon\Carbon::parse($stage->actual_start_date)->format('d.m.Y') }} - 
                                {{ \Carbon\Carbon::parse($stage->actual_end_date)->format('d.m.Y') }}
                            </span>
                        </div>
                        @elseif($stage->actual_start_date)
                        <div class="mb-1">
                            <strong>Факт. начало:</strong> 
                            <span class="badge bg-info text-white">{{ \Carbon\Carbon::parse($stage->actual_start_date)->format('d.m.Y') }}</span>
                        </div>
                        @elseif($stage->actual_end_date)
                        <div class="mb-1">
                            <strong>Факт. окончание:</strong> 
                            <span class="badge bg-success text-white">{{ \Carbon\Carbon::parse($stage->actual_end_date)->format('d.m.Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @if($stage->progress > 0)
                <div class="progress mt-2" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $stage->progress }}%" aria-valuenow="{{ $stage->progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <small class="text-muted">Прогресс: {{ $stage->progress }}%</small>
                @endif
            </div>
            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary" title="Редактировать" onclick="editStage({{ $stage->id }})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-outline-danger" title="Удалить" onclick="deleteStage({{ $stage->id }})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-5">
    <i class="bi bi-list-task text-muted" style="font-size: 4rem;"></i>
    <h5 class="text-muted mt-3">Этапы не добавлены</h5>
    <p class="text-muted">Создайте первый этап для структурирования проекта</p>
    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addStageModal">
        <i class="bi bi-plus"></i> Добавить первый этап
    </button>
</div>
@endif
