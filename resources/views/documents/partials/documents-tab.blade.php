<div class="row">
    <div class="col-12">
        @if($documents->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Документ</th>
                            <th>Тип</th>
                            <th>Проект</th>
                            @if($tab === 'received')
                                <th>Отправитель</th>
                                <th>Получен</th>
                            @elseif($tab === 'created')
                                <th>Получатель</th>
                                <th>Статус</th>
                            @elseif($tab === 'signed')
                                <th>Участники</th>
                                <th>Подписан</th>
                            @endif
                            <th>Статус подписи</th>
                            <th width="200">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                        <div>
                                            <strong>{{ $document->title }}</strong>
                                            @if($document->file_path)
                                                <br><small class="text-muted">
                                                    <i class="fas fa-paperclip"></i> {{ $document->original_name }}
                                                    ({{ $document->formatted_size }})
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $document->type_name }}</span>
                                </td>
                                <td>
                                    @if($document->project)
                                        <a href="{{ route('projects.show', $document->project) }}" 
                                           class="text-decoration-none">
                                            {{ $document->project->title }}
                                        </a>
                                    @else
                                        <span class="text-muted">Не привязан</span>
                                    @endif
                                </td>
                                
                                @if($tab === 'received')
                                    <td>{{ $document->sender->name }}</td>
                                    <td>{{ $document->created_at->format('d.m.Y H:i') }}</td>
                                @elseif($tab === 'created')
                                    <td>
                                        @if($document->recipient_type === 'user')
                                            {{ $document->recipient->name ?? 'Пользователь удален' }}
                                        @else
                                            {{ $document->recipient_type }}
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($document->status) {
                                                'draft' => 'bg-secondary',
                                                'sent' => 'bg-info',
                                                'received' => 'bg-success',
                                                'signed' => 'bg-primary',
                                                'expired' => 'bg-warning',
                                                'rejected' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $document->status_name }}</span>
                                    </td>
                                @elseif($tab === 'signed')
                                    <td>
                                        <small>
                                            <strong>Отправитель:</strong> {{ $document->sender->name }}<br>
                                            <strong>Получатель:</strong> 
                                            @if($document->recipient_type === 'user')
                                                {{ $document->recipient->name ?? 'Пользователь удален' }}
                                            @else
                                                {{ $document->recipient_type }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>{{ $document->signed_at->format('d.m.Y H:i') }}</td>
                                @endif
                                
                                <td>
                                    @php
                                        $signatureClass = match($document->signature_status) {
                                            'not_required' => 'bg-light text-dark',
                                            'pending' => 'bg-warning',
                                            'signed' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'expired' => 'bg-secondary',
                                            default => 'bg-light text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $signatureClass }}">
                                        {{ $document->signature_status_name }}
                                    </span>
                                    @if($document->digital_signature)
                                        <br><small class="text-success">
                                            <i class="fas fa-shield-alt"></i> ЭЦП
                                        </small>
                                    @endif
                                </td>
                                
                                <td>
                                    <div class="btn-group-vertical btn-group-sm" role="group">
                                        <!-- Просмотр -->
                                        <a href="{{ route('documents.show', $document) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> Просмотр
                                        </a>
                                        
                                        <!-- Скачивание -->
                                        @if($document->file_path)
                                            <a href="{{ route('documents.download', $document) }}" 
                                               class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-download"></i> Скачать
                                            </a>
                                        @endif
                                        
                                        <!-- Подписание (только для полученных документов) -->
                                        @if($tab === 'received' && $document->requiresSignature())
                                            <button type="button" 
                                                    class="btn btn-outline-success btn-sm btn-sign" 
                                                    data-document-id="{{ $document->id }}">
                                                <i class="fas fa-signature"></i> Подписать
                                            </button>
                                        @endif
                                        
                                        <!-- Отправка (только для созданных черновиков) -->
                                        @if($tab === 'created' && $document->status === 'draft')
                                            <button type="button" 
                                                    class="btn btn-outline-info btn-sm btn-send" 
                                                    data-document-id="{{ $document->id }}">
                                                <i class="fas fa-paper-plane"></i> Отправить
                                            </button>
                                        @endif
                                        
                                        <!-- Удаление (только для несубмежных документов) -->
                                        @if($tab === 'created' && !$document->isSigned())
                                            <button type="button" 
                                                    class="btn btn-outline-danger btn-sm btn-delete" 
                                                    data-document-id="{{ $document->id }}">
                                                <i class="fas fa-trash"></i> Удалить
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Пагинация -->
            <div class="d-flex justify-content-center">
                @if($documents->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($documents->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">‹</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="#" data-page="{{ $documents->currentPage() - 1 }}" data-tab="{{ $tab }}">‹</a></li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($documents->getUrlRange(1, $documents->lastPage()) as $page => $url)
                                @if ($page == $documents->currentPage())
                                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="#" data-page="{{ $page }}" data-tab="{{ $tab }}">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($documents->hasMorePages())
                                <li class="page-item"><a class="page-link" href="#" data-page="{{ $documents->currentPage() + 1 }}" data-tab="{{ $tab }}">›</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">›</span></li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">
                    @if($tab === 'received')
                        Нет полученных документов
                    @elseif($tab === 'created')
                        Вы еще не создали ни одного документа
                    @elseif($tab === 'signed')
                        Нет подписанных документов
                    @endif
                </h5>
                @if($tab !== 'received')
                    <a href="{{ route('documents.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus"></i> Создать первый документ
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
