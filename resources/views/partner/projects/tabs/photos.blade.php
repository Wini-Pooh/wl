<!-- Фотографии проекта -->
<div id="photos-tab-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-camera me-2"></i>
            <span class="d-none d-md-inline">ФОТО-ОТЧЕТ</span>
            <span class="d-md-none">Фото</span>
            (<span id="photoCount">{{ $photos->count() }}</span>)
        </h5>
        @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline ms-2">Загрузить фото</span>
        </button>
        @endif
    </div>

    <!-- Форма фильтров -->
    <form method="GET" action="{{ route('partner.projects.photos', $project) }}" class="mb-4">
        <!-- Мобильные фильтры -->
        <div class="filters-mobile-container d-md-none mb-4">
            <button class="filters-toggle-mobile" type="button" data-bs-toggle="collapse" 
                    data-bs-target="#photoFiltersMobile" aria-expanded="false" aria-controls="photoFiltersMobile">
                <span>
                    <i class="bi bi-funnel me-2"></i>
                    Фильтры фото
                </span>
                <i class="bi bi-chevron-down chevron"></i>
            </button>
            
            <div class="collapse" id="photoFiltersMobile">
                <div class="p-3">
                    <div class="mobile-grid">
                        <div>
                            <label for="photoTypeFilterMobile" class="form-label">Тип фото</label>
                            <select class="form-select" id="photoTypeFilterMobile" name="category">
                                <option value="">Все типы</option>
                                @php
                                    $categoryOptions = [
                                        'before' => 'До ремонта',
                                        'after' => 'После ремонта',
                                        'process' => 'Процесс работы',
                                        'materials' => 'Материалы',
                                        'problems' => 'Проблемы',
                                        'design' => 'Дизайн',
                                        'furniture' => 'Мебель',
                                        'decor' => 'Декор'
                                    ];
                                @endphp
                                @foreach($categoryOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $filters['category'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="photoLocationFilterMobile" class="form-label">Помещение</label>
                            <select class="form-select" id="photoLocationFilterMobile" name="location">
                                <option value="">Все помещения</option>
                                @php
                                    $locationOptions = [
                                        'kitchen' => 'Кухня',
                                        'living_room' => 'Гостиная',
                                        'bedroom' => 'Спальня',
                                        'bathroom' => 'Ванная',
                                        'toilet' => 'Туалет',
                                        'hallway' => 'Прихожая',
                                        'balcony' => 'Балкон',
                                        'other' => 'Другое'
                                    ];
                                @endphp
                                @foreach($locationOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $filters['location'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="photoSortFilterMobile" class="form-label">Сортировка</label>
                            <select class="form-select" id="photoSortFilterMobile" name="sort">
                                @php
                                    $sortOptions = [
                                        'newest' => 'Сначала новые',
                                        'oldest' => 'Сначала старые',
                                        'name_asc' => 'По имени (А-Я)',
                                        'name_desc' => 'По имени (Я-А)'
                                    ];
                                @endphp
                                @foreach($sortOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $filters['sort'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="photoSearchFilterMobile" class="form-label">Поиск</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="photoSearchFilterMobile" 
                                       name="search" value="{{ $filters['search'] }}" placeholder="Поиск...">
                                <button class="btn btn-outline-secondary" type="button" onclick="this.parentElement.querySelector('input').value=''; this.closest('form').submit();">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="action-buttons-mobile mt-3">
                        <button type="submit" class="btn btn-primary me-2">Применить фильтры</button>
                        <a href="{{ route('partner.projects.photos', $project) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-2"></i>Сбросить все фильтры
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Десктопные фильтры -->
        <div class="card mb-4 d-none d-md-block" id="photoFiltersCard">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-funnel me-2"></i>Фильтры фотографий</h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="toggleFilters">
                    <i class="bi bi-chevron-up" id="toggleFiltersIcon"></i>
                </button>
            </div>
            <div class="card-body" id="filtersContent">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <label for="photoTypeFilter" class="form-label">Тип фото</label>
                        <select class="form-select" id="photoTypeFilter" name="category">
                            <option value="">Все типы</option>
                            @foreach($categoryOptions as $value => $label)
                                <option value="{{ $value }}" {{ $filters['category'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                            @foreach(['demolition', 'floors', 'walls', 'ceiling', 'electrical', 'plumbing', 'heating', 'doors', 'windows'] as $category)
                                @php 
                                    $categoryLabels = [
                                        'demolition' => 'Демонтаж',
                                        'floors' => 'Полы',
                                        'walls' => 'Стены',
                                        'ceiling' => 'Потолки',
                                        'electrical' => 'Электрика',
                                        'plumbing' => 'Сантехника',
                                        'heating' => 'Отопление',
                                        'doors' => 'Двери',
                                        'windows' => 'Окна'
                                    ];
                                @endphp
                                <option value="{{ $category }}" {{ $filters['category'] == $category ? 'selected' : '' }}>{{ $categoryLabels[$category] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label for="photoLocationFilter" class="form-label">Помещение</label>
                        <select class="form-select" id="photoLocationFilter" name="location">
                            <option value="">Все помещения</option>
                            @foreach($locationOptions as $value => $label)
                                <option value="{{ $value }}" {{ $filters['location'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label for="photoSortFilter" class="form-label">Сортировка</label>
                        <select class="form-select" id="photoSortFilter" name="sort">
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" {{ $filters['sort'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                            <option value="size_asc" {{ $filters['sort'] == 'size_asc' ? 'selected' : '' }}>По размеру (меньше)</option>
                            <option value="size_desc" {{ $filters['sort'] == 'size_desc' ? 'selected' : '' }}>По размеру (больше)</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <label for="photoSearchFilter" class="form-label">Поиск</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="photoSearchFilter" 
                                   name="search" value="{{ $filters['search'] }}" placeholder="Поиск по названию...">
                            <button class="btn btn-outline-secondary" type="button" onclick="this.parentElement.querySelector('input').value=''; this.closest('form').submit();">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-search me-1"></i>Применить фильтры
                                </button>
                                <a href="{{ route('partner.projects.photos', $project) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Сбросить все
                                </a>
                                @if(array_filter($filters))
                                    <span class="text-muted small align-self-center">
                                        Активные фильтры: {{ count(array_filter($filters)) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Галерея фотографий -->
    @if($photos->count() > 0)
        <div id="photoGallery" class="row g-3">
            @foreach($photos as $photo)
                @php
                    $categoryName = $categoryOptions[$photo->category] ?? $photo->category ?? 'Без категории';
                    $locationName = $locationOptions[$photo->location] ?? $photo->location ?? '';
                    
                    // Генерируем URL для изображения
                    $imageUrl = $photo->url ?? asset('storage/' . $photo->path);
                @endphp
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card photo-card" data-id="{{ $photo->id }}" data-category="{{ $photo->category }}" data-location="{{ $photo->location }}">
                        <div class="photo-preview position-relative">
                            <img src="{{ $imageUrl }}" 
                                 alt="{{ $photo->original_name ?? $photo->filename }}" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;"
                                 loading="lazy">
                            <div class="photo-badges position-absolute top-0 start-0 p-2">
                                <span class="badge bg-primary me-1">{{ $categoryName }}</span>
                                @if($locationName)
                                    <span class="badge bg-secondary">{{ $locationName }}</span>
                                @endif
                            </div>
                            <div class="photo-actions position-absolute top-0 end-0 p-2">
                                <a href="{{ route('partner.projects.photos.show', [$project, $photo->id]) }}" 
                                   target="_blank" class="btn btn-sm btn-light me-1" title="Просмотр">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                                <form method="POST" action="{{ route('partner.projects.photos.delete', [$project, $photo->id]) }}" 
                                      class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить эту фотографию? Это действие нельзя отменить.')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-light" title="Удалить">
                                        <i class="bi bi-trash text-danger"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <h6 class="card-title text-truncate mb-2" title="{{ $photo->original_name ?? $photo->filename }}">
                                {{ $photo->original_name ?? $photo->filename }}
                            </h6>
                            <div class="text-muted small">
                                @if($photo->file_size)
                                    {{ number_format($photo->file_size / 1024, 1) }} КБ
                                @endif
                                @if($photo->created_at)
                                    <br>{{ $photo->created_at->format('d.m.Y') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Пустое состояние -->
        <div id="emptyPhotoState" class="text-center py-5">
            <i class="bi bi-images display-1 text-muted"></i>
            <h5 class="mt-3">Нет фотографий</h5>
            <p class="text-muted">
                @if(array_filter($filters))
                    По заданным фильтрам фотографии не найдены. 
                    <a href="{{ route('partner.projects.photos', $project) }}">Сбросить фильтры</a>
                @else
                    Загрузите фотографии проекта, нажав кнопку "Загрузить фото" вверху страницы
                @endif
            </p>
        </div>
    @endif
</div>

<!-- Модальное окно для загрузки фотографий -->
@if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPhotoModalLabel">
                    <i class="bi bi-camera me-2"></i>
                    Загрузить фотографии
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('partner.projects.photos.upload', $project) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="photoFiles" class="form-label">Выберите фотографии</label>
                        <input type="file" class="form-control" id="photoFiles" name="files[]" 
                               multiple accept="image/*" required>
                        <div class="form-text">
                            Поддерживаемые форматы: JPG, PNG, GIF, WebP. Максимальный размер файла: 10 МБ.
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label for="photoCategory" class="form-label">Категория</label>
                            <select class="form-select" id="photoCategory" name="category">
                                <option value="">Выберите категорию</option>
                                @foreach($categoryOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                                @foreach(['demolition', 'floors', 'walls', 'ceiling', 'electrical', 'plumbing', 'heating', 'doors', 'windows'] as $category)
                                    <option value="{{ $category }}">{{ $categoryLabels[$category] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="photoLocation" class="form-label">Помещение</label>
                            <select class="form-select" id="photoLocation" name="location">
                                <option value="">Выберите помещение</option>
                                @foreach($locationOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="photoDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="photoDescription" name="description" 
                                  rows="3" placeholder="Дополнительное описание фотографий..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отменить</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i>Загрузить фотографии
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
