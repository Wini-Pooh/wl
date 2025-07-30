@if($events->count() > 0)
@foreach($events as $event)
<div class="card mb-3 border-start border-4 border-{{ $event->status == 'completed' ? 'success' : ($event->status == 'planned' ? 'primary' : 'danger') }}" data-event-id="{{ $event->id }}">
    <div class="card-body">
        <div class=" justify-content-between align-items-start">
            <div class="flex-grow-1 me-3">
                <h6 class="mb-2">
                    <i class="bi bi-{{ $event->type == 'meeting' ? 'calendar-event' : ($event->type == 'delivery' ? 'truck' : ($event->type == 'inspection' ? 'search' : ($event->type == 'milestone' ? 'flag' : 'info-circle'))) }} me-2"></i>
                    {{ $event->title }}
                    <span class="badge bg-{{ $event->status == 'completed' ? 'success' : ($event->status == 'planned' ? 'primary' : 'danger') }} ms-2">
                        @if($event->status == 'completed')
                            Завершено
                        @elseif($event->status == 'planned')
                            Запланировано
                        @else
                            Отменено
                        @endif
                    </span>
                </h6>
                <div class="text-muted small mb-2">
                    <span class="badge bg-light text-dark me-2">
                        @if($event->type == 'meeting')
                            Встреча
                        @elseif($event->type == 'delivery')
                            Доставка
                        @elseif($event->type == 'inspection')
                            Проверка
                        @elseif($event->type == 'milestone')
                            Веха
                        @else
                            Другое
                        @endif
                    </span>
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d.m.Y') : 'Дата не указана' }}
                    @if($event->event_time)
                        <i class="bi bi-clock ms-2 me-1"></i>{{ $event->event_time }}
                    @endif
                    @if($event->location)
                        <i class="bi bi-geo-alt ms-2 me-1"></i>{{ $event->location }}
                    @endif
                </div>
                @if($event->description)
                <p class="mb-2">{{ $event->description }}</p>
                @endif
                @if($event->notes)
                <small class="text-muted"><strong>Заметки:</strong> {{ $event->notes }}</small>
                @endif
            </div>
            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-success" title="Редактировать" onclick="editEvent({{ $event->id }})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-outline-danger" title="Удалить" onclick="deleteEvent({{ $event->id }})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            @endif
        </div>
    </div>
</div>
@endforeach
@else
<div class="text-center py-5">
    <i class="bi bi-calendar-event text-muted" style="font-size: 4rem;"></i>
    <h5 class="text-muted mt-3">События не добавлены</h5>
    <p class="text-muted">Добавьте важные события и встречи для контроля хода проекта</p>
    <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addEventModal">
        <i class="bi bi-plus"></i> Добавить первое событие
    </button>
</div>
@endif
