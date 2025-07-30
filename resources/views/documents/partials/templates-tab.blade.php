<div class="row">
    <div class="col-12">
        @if($templates->count() > 0)
            <div class="row">
                @foreach($templates as $template)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0">{{ $template->name }}</h6>
                                <span class="badge bg-secondary">{{ $template->document_type }}</span>
                            </div>
                            <div class="card-body">
                                @if($template->description)
                                    <p class="card-text text-muted small">{{ $template->description }}</p>
                                @endif
                                
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> {{ $template->creator->name }}<br>
                                        <i class="fas fa-calendar"></i> {{ $template->created_at->format('d.m.Y') }}
                                    </small>
                                </div>
                                
                                @if($template->variables && count($template->variables) > 0)
                                    <div class="mb-2">
                                        <small class="text-info">
                                            <i class="fas fa-code"></i> Переменные: 
                                            @foreach($template->variables as $variable)
                                                <span class="badge bg-light text-dark me-1">&#123;&#123;{{ $variable }}&#125;&#125;</span>
                                            @endforeach
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100" role="group">
                                    <a href="{{ route('document-templates.show', $template) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> Просмотр
                                    </a>
                                    <button type="button" 
                                            class="btn btn-primary btn-sm btn-use-template" 
                                            data-template-id="{{ $template->id }}">
                                        <i class="fas fa-plus"></i> Использовать
                                    </button>
                                    <a href="{{ route('document-templates.edit', $template) }}" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-edit"></i> Изменить
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Пагинация -->
            <div class="d-flex justify-content-center">
                @if($templates->hasPages())
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @if ($templates->onFirstPage())
                                <li class="page-item disabled"><span class="page-link">‹</span></li>
                            @else
                                <li class="page-item"><a class="page-link" href="#" data-page="{{ $templates->currentPage() - 1 }}" data-tab="templates">‹</a></li>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($templates->getUrlRange(1, $templates->lastPage()) as $page => $url)
                                @if ($page == $templates->currentPage())
                                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="#" data-page="{{ $page }}" data-tab="templates">{{ $page }}</a></li>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($templates->hasMorePages())
                                <li class="page-item"><a class="page-link" href="#" data-page="{{ $templates->currentPage() + 1 }}" data-tab="templates">›</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">›</span></li>
                            @endif
                        </ul>
                    </nav>
                @endif
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Нет доступных шаблонов</h5>
                <a href="{{ route('document-templates.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus"></i> Создать первый шаблон
                </a>
            </div>
        @endif
    </div>
</div>
