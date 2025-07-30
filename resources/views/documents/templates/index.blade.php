@extends('layouts.app')

@section('title', 'Шаблоны документов')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Шаблоны документов</h3>
                    <a href="{{ route('document-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Создать шаблон
                    </a>
                </div>

                <div class="card-body">
                    @if($templates->count() > 0)
                        <div class="row">
                            @foreach($templates as $template)
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 template-card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="card-title mb-0">{{ $template->name }}</h6>
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($template->document_type) }}
                                            </span>
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
                                                        <i class="fas fa-code"></i> Переменных: {{ count($template->variables) }}
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
                                                <a href="{{ route('documents.create', ['template' => $template->id]) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-plus"></i> Создать
                                                </a>
                                                <a href="{{ route('document-templates.edit', $template) }}" 
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Пагинация -->
                        <div class="d-flex justify-content-center">
                            {{ $templates->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Нет созданных шаблонов</h5>
                            <p class="text-muted">Создайте первый шаблон для быстрого создания документов</p>
                            <a href="{{ route('document-templates.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus"></i> Создать первый шаблон
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.template-card {
    transition: transform 0.2s;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush
