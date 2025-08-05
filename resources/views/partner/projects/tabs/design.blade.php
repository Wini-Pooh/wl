<!-- Дизайн проекта -->
<div id="design-tab-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            <i class="bi bi-paint-bucket me-2"></i>
            <span class="d-none d-md-inline">Дизайн проекта</span>
            <span class="d-md-none">Дизайн</span>
            (<span id="designCount">{{ $designFiles->total() ?? 0 }}</span>)
        </h5>
        @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
        <button class="btn btn-primary" data-modal-type="design">
            <i class="bi bi-plus-lg"></i>
            <span class="d-none d-md-inline ms-2">Загрузить дизайн</span>
        </button>
        @endif
    </div>

    <!-- Фильтры с обычными формами -->
    <div class="card mb-4" id="designFiltersCard">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-funnel me-2"></i>Фильтры и поиск
                @if(count(array_filter($filters ?? [])))
                    <small class="text-muted ms-2">Активно: {{ count(array_filter($filters ?? [])) }}</small>
                @endif
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('partner.projects.design', $project) }}" class="needs-validation" novalidate>
                <div class="row g-3">
                    <!-- Поиск по названию -->
                    <div class="col-12">
                        <label for="designSearchFilter" class="form-label">Поиск по названию</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" id="designSearchFilter" name="search" 
                                   value="{{ $filters['search'] ?? '' }}"
                                   placeholder="Введите название файла дизайна..." autocomplete="off">
                            @if(!empty($filters['search']))
                            <a href="{{ route('partner.projects.design', $project) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Фильтры в адаптивной строке -->
                    <div class="col-6 col-md-3">
                        <label for="designTypeFilter" class="form-label">Тип дизайна</label>
                        <select class="form-select" id="designTypeFilter" name="design_type">
                            <option value="">Все типы</option>
                            <option value="3d" {{ ($filters['design_type'] ?? '') == '3d' ? 'selected' : '' }}>3D визуализация</option>
                            <option value="layout" {{ ($filters['design_type'] ?? '') == 'layout' ? 'selected' : '' }}>Планировка</option>
                            <option value="sketch" {{ ($filters['design_type'] ?? '') == 'sketch' ? 'selected' : '' }}>Эскиз</option>
                            <option value="render" {{ ($filters['design_type'] ?? '') == 'render' ? 'selected' : '' }}>Рендер</option>
                            <option value="draft" {{ ($filters['design_type'] ?? '') == 'draft' ? 'selected' : '' }}>Черновик</option>
                            <option value="concept" {{ ($filters['design_type'] ?? '') == 'concept' ? 'selected' : '' }}>Концепт</option>
                            <option value="detail" {{ ($filters['design_type'] ?? '') == 'detail' ? 'selected' : '' }}>Детализация</option>
                            <option value="material" {{ ($filters['design_type'] ?? '') == 'material' ? 'selected' : '' }}>Материалы</option>
                            <option value="elevation" {{ ($filters['design_type'] ?? '') == 'elevation' ? 'selected' : '' }}>Развертка</option>
                            <option value="section" {{ ($filters['design_type'] ?? '') == 'section' ? 'selected' : '' }}>Разрез</option>
                            <option value="specification" {{ ($filters['design_type'] ?? '') == 'specification' ? 'selected' : '' }}>Спецификация</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="designRoomFilter" class="form-label">Помещение</label>
                        <select class="form-select" id="designRoomFilter" name="room">
                            <option value="">Все помещения</option>
                            <option value="kitchen" {{ ($filters['room'] ?? '') == 'kitchen' ? 'selected' : '' }}>Кухня</option>
                            <option value="living_room" {{ ($filters['room'] ?? '') == 'living_room' ? 'selected' : '' }}>Гостиная</option>
                            <option value="bedroom" {{ ($filters['room'] ?? '') == 'bedroom' ? 'selected' : '' }}>Спальня</option>
                            <option value="bathroom" {{ ($filters['room'] ?? '') == 'bathroom' ? 'selected' : '' }}>Ванная</option>
                            <option value="toilet" {{ ($filters['room'] ?? '') == 'toilet' ? 'selected' : '' }}>Туалет</option>
                            <option value="hallway" {{ ($filters['room'] ?? '') == 'hallway' ? 'selected' : '' }}>Прихожая</option>
                            <option value="balcony" {{ ($filters['room'] ?? '') == 'balcony' ? 'selected' : '' }}>Балкон</option>
                            <option value="corridor" {{ ($filters['room'] ?? '') == 'corridor' ? 'selected' : '' }}>Коридор</option>
                            <option value="office" {{ ($filters['room'] ?? '') == 'office' ? 'selected' : '' }}>Кабинет</option>
                            <option value="children" {{ ($filters['room'] ?? '') == 'children' ? 'selected' : '' }}>Детская</option>
                            <option value="pantry" {{ ($filters['room'] ?? '') == 'pantry' ? 'selected' : '' }}>Кладовая</option>
                            <option value="garage" {{ ($filters['room'] ?? '') == 'garage' ? 'selected' : '' }}>Гараж</option>
                            <option value="basement" {{ ($filters['room'] ?? '') == 'basement' ? 'selected' : '' }}>Подвал</option>
                            <option value="attic" {{ ($filters['room'] ?? '') == 'attic' ? 'selected' : '' }}>Чердак</option>
                            <option value="terrace" {{ ($filters['room'] ?? '') == 'terrace' ? 'selected' : '' }}>Терраса</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="designStyleFilter" class="form-label">Стиль</label>
                        <select class="form-select" id="designStyleFilter" name="style">
                            <option value="">Все стили</option>
                            <option value="modern" {{ ($filters['style'] ?? '') == 'modern' ? 'selected' : '' }}>Современный</option>
                            <option value="classic" {{ ($filters['style'] ?? '') == 'classic' ? 'selected' : '' }}>Классический</option>
                            <option value="minimalism" {{ ($filters['style'] ?? '') == 'minimalism' ? 'selected' : '' }}>Минимализм</option>
                            <option value="loft" {{ ($filters['style'] ?? '') == 'loft' ? 'selected' : '' }}>Лофт</option>
                            <option value="scandinavian" {{ ($filters['style'] ?? '') == 'scandinavian' ? 'selected' : '' }}>Скандинавский</option>
                            <option value="provence" {{ ($filters['style'] ?? '') == 'provence' ? 'selected' : '' }}>Прованс</option>
                            <option value="high_tech" {{ ($filters['style'] ?? '') == 'high_tech' ? 'selected' : '' }}>Хай-тек</option>
                            <option value="eco" {{ ($filters['style'] ?? '') == 'eco' ? 'selected' : '' }}>Эко</option>
                            <option value="art_deco" {{ ($filters['style'] ?? '') == 'art_deco' ? 'selected' : '' }}>Арт-деко</option>
                            <option value="neoclassic" {{ ($filters['style'] ?? '') == 'neoclassic' ? 'selected' : '' }}>Неоклассика</option>
                            <option value="fusion" {{ ($filters['style'] ?? '') == 'fusion' ? 'selected' : '' }}>Фьюжн</option>
                            <option value="industrial" {{ ($filters['style'] ?? '') == 'industrial' ? 'selected' : '' }}>Индустриальный</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label for="designSortFilter" class="form-label">Сортировка</label>
                        <select class="form-select" id="designSortFilter" name="sort">
                            <option value="newest" {{ ($filters['sort'] ?? 'newest') == 'newest' ? 'selected' : '' }}>Сначала новые</option>
                            <option value="oldest" {{ ($filters['sort'] ?? '') == 'oldest' ? 'selected' : '' }}>Сначала старые</option>
                            <option value="name_asc" {{ ($filters['sort'] ?? '') == 'name_asc' ? 'selected' : '' }}>По названию (А-Я)</option>
                            <option value="name_desc" {{ ($filters['sort'] ?? '') == 'name_desc' ? 'selected' : '' }}>По названию (Я-А)</option>
                            <option value="size_asc" {{ ($filters['sort'] ?? '') == 'size_asc' ? 'selected' : '' }}>По размеру (возрастание)</option>
                            <option value="size_desc" {{ ($filters['sort'] ?? '') == 'size_desc' ? 'selected' : '' }}>По размеру (убывание)</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Применить фильтры
                            </button>
                            <a href="{{ route('partner.projects.design', $project) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Сбросить
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
                            <button type="button" class="btn btn-outline-primary" id="toggleFiltersBtn" title="Свернуть/развернуть фильтры">
                                <i class="bi bi-chevron-up"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Дополнительные опции (скрываемые) -->
                <div class="row g-3 mt-2" id="advancedFilters" style="display: none;">
                    <div class="col-md-3">
                        <label for="designSortFilter" class="form-label">Сортировка</label>
                        <select class="form-select" id="designSortFilter" name="sort">
                            <option value="newest">Сначала новые</option>
                            <option value="oldest">Сначала старые</option>
                            <option value="name_asc">По имени (А-Я)</option>
                            <option value="name_desc">По имени (Я-А)</option>
                            <option value="size_asc">По размеру (возрастание)</option>
                            <option value="size_desc">По размеру (убывание)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="designDateFromFilter" class="form-label">Дата создания от</label>
                        <input type="date" class="form-control" id="designDateFromFilter" name="date_from">
                    </div>
                    <div class="col-md-3">
                        <label for="designDateToFilter" class="form-label">Дата создания до</label>
                        <input type="date" class="form-control" id="designDateToFilter" name="date_to">
                    </div>
                    <div class="col-md-3">
                        <label for="designSizeFilter" class="form-label">Размер файла</label>
                        <select class="form-select" id="designSizeFilter" name="file_size">
                            <option value="">Любой размер</option>
                            <option value="small">Маленькие (до 1 МБ)</option>
                            <option value="medium">Средние (1-10 МБ)</option>
                            <option value="large">Большие (больше 10 МБ)</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Список файлов дизайна -->
    <div id="designGallery" class="row g-3">
        @if($designFiles->count() > 0)
            @foreach($designFiles as $designFile)
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card design-file-card h-100">
                        <div class="design-preview position-relative">
                            @if(in_array(strtolower(pathinfo($designFile->original_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $designFile->getUrl() }}" alt="{{ $designFile->original_name }}" class="img-fluid">
                            @else
                                <div class="text-center p-4">
                                    <i class="bi bi-file-earmark-text display-4 text-muted"></i>
                                    <div class="mt-2 small text-muted">{{ strtoupper(pathinfo($designFile->original_name, PATHINFO_EXTENSION)) }}</div>
                                </div>
                            @endif
                            
                            <!-- Действия с файлом -->
                            @if(\App\Helpers\UserRoleHelper::canSeeActionButtons())
                            <div class="design-file-actions position-absolute top-0 end-0 p-2">
                                <div class="btn-group-vertical">
                                    <a href="{{ route('partner.projects.design.download', [$project, $designFile]) }}" 
                                       class="btn btn-sm btn-outline-light" title="Скачать">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form method="POST" action="{{ route('partner.projects.design.destroy', [$project, $designFile]) }}" 
                                          class="d-inline" onsubmit="return confirm('Вы уверены, что хотите удалить этот файл?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Удалить">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="card-body">
                            <h6 class="card-title text-truncate" title="{{ $designFile->original_name }}">
                                {{ $designFile->original_name }}
                            </h6>
                            
                            <div class="row g-1 small text-muted">
                                @if($designFile->design_type)
                                <div class="col-12">
                                    <span class="badge bg-primary me-1">{{ $designFile->design_type }}</span>
                                </div>
                                @endif
                                
                                @if($designFile->room)
                                <div class="col-12">
                                    <i class="bi bi-house-door me-1"></i>{{ $designFile->room }}
                                </div>
                                @endif
                                
                                @if($designFile->style)
                                <div class="col-12">
                                    <i class="bi bi-palette me-1"></i>{{ $designFile->style }}
                                </div>
                                @endif
                                
                                <div class="col-12">
                                    <i class="bi bi-file-earmark me-1"></i>{{ number_format($designFile->file_size / 1024, 0) }} КБ
                                </div>
                                
                                <div class="col-12">
                                    <i class="bi bi-clock me-1"></i>{{ $designFile->created_at->format('d.m.Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Пустое состояние -->
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-folder2-open display-1 text-muted"></i>
                    <h5 class="mt-3">Нет файлов дизайна</h5>
                    <p class="text-muted">
                        @if(count(array_filter($filters ?? [])))
                            По заданным фильтрам ничего не найдено. 
                            <a href="{{ route('partner.projects.design', $project) }}">Сбросить фильтры</a>
                        @else
                            Загрузите файлы дизайна проекта, нажав кнопку "Загрузить дизайн" вверху страницы
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- Пагинация -->
    @if($designFiles->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $designFiles->links() }}
        </div>
    @endif
</div>
