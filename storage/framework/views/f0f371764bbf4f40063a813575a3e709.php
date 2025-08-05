<!-- Модальное окно для загрузки дизайна (без AJAX) -->
<div class="modal fade" id="uploadDesignModal" tabindex="-1" aria-labelledby="uploadDesignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDesignModalLabel">
                    <i class="bi bi-upload me-2"></i>Загрузка файлов дизайна
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <form method="POST" action="<?php echo e(route('partner.projects.design.store', $project)); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="designFiles" class="form-label">Выберите файлы дизайна</label>
                        <input type="file" id="designFiles" name="files[]" class="form-control" multiple 
                               accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.pdf,.ai,.eps,.psd,.sketch,.fig,.xd,.dwg,.dxf">
                        <div class="form-text">
                            Поддерживаемые форматы: JPG, PNG, GIF, WebP, SVG, PDF, AI, EPS, PSD, Sketch, Figma, Adobe XD, DWG, DXF
                            <br>Максимальный размер файла: 50 МБ
                        </div>
                    </div>
                    
                    <!-- Предпросмотр выбранных файлов -->
                    <div id="filePreview" class="mb-4" style="display: none;">
                        <h6>Выбранные файлы:</h6>
                        <div class="list-group" id="fileList"></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="designType" class="form-label">Тип дизайна</label>
                                <select class="form-select" id="designType" name="type">
                                    <option value="concept">Концепт</option>
                                    <option value="3d">3D визуализация</option>
                                    <option value="layout">Планировка</option>
                                    <option value="sketch">Эскиз</option>
                                    <option value="render">Рендер</option>
                                    <option value="draft">Черновик</option>
                                    <option value="mood_board">Мудборд</option>
                                    <option value="color_scheme">Цветовая схема</option>
                                    <option value="furniture">Мебель</option>
                                    <option value="lighting">Освещение</option>
                                    <option value="materials">Материалы</option>
                                    <option value="final">Финальный дизайн</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="designRoom" class="form-label">Помещение</label>
                                <select class="form-select" id="designRoom" name="room">
                                    <option value="">Не указано</option>
                                    <option value="kitchen">Кухня</option>
                                    <option value="living_room">Гостиная</option>
                                    <option value="bedroom">Спальня</option>
                                    <option value="bathroom">Ванная</option>
                                    <option value="toilet">Туалет</option>
                                    <option value="hallway">Прихожая</option>
                                    <option value="balcony">Балкон</option>
                                    <option value="corridor">Коридор</option>
                                    <option value="office">Кабинет</option>
                                    <option value="children">Детская</option>
                                    <option value="pantry">Кладовая</option>
                                    <option value="garage">Гараж</option>
                                    <option value="basement">Подвал</option>
                                    <option value="attic">Чердак</option>
                                    <option value="terrace">Терраса</option>
                                    <option value="general">Общий план</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="designStyle" class="form-label">Стиль</label>
                                <select class="form-select" id="designStyle" name="style">
                                    <option value="">Не указан</option>
                                    <option value="modern">Современный</option>
                                    <option value="classic">Классический</option>
                                    <option value="minimalism">Минимализм</option>
                                    <option value="loft">Лофт</option>
                                    <option value="scandinavian">Скандинавский</option>
                                    <option value="provence">Прованс</option>
                                    <option value="high_tech">Хай-тек</option>
                                    <option value="eco">Эко</option>
                                    <option value="art_deco">Арт-деко</option>
                                    <option value="neoclassic">Неоклассика</option>
                                    <option value="fusion">Фьюжн</option>
                                    <option value="industrial">Индустриальный</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="designStage" class="form-label">Этап проекта</label>
                                <select class="form-select" id="designStage" name="stage">
                                    <option value="">Не указан</option>
                                    <option value="concept">Концепция</option>
                                    <option value="preliminary">Предварительный</option>
                                    <option value="working">Рабочий</option>
                                    <option value="final">Финальный</option>
                                    <option value="presentation">Презентационный</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="designDescription" class="form-label">Описание</label>
                        <textarea class="form-control" id="designDescription" name="description" rows="3" 
                                  placeholder="Опишите файлы дизайна..."></textarea>
                    </div>
                    
                    <!-- Прогресс-бар (скрыт по умолчанию) -->
                    <div class="upload-progress" id="uploadProgress" style="display: none;">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <span>Загрузка файлов...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary" id="uploadButton">
                        <i class="bi bi-upload me-1"></i>Загрузить файлы дизайна
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Функция предпросмотра выбранных файлов
function previewSelectedFiles() {
    const fileInput = document.getElementById('designFiles');
    const filePreview = document.getElementById('filePreview');
    const fileList = document.getElementById('fileList');
    
    if (fileInput.files.length > 0) {
        filePreview.style.display = 'block';
        fileList.innerHTML = '';
        
        Array.from(fileInput.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'list-group-item d-flex justify-content-between align-items-center';
            
            const fileName = file.name.length > 30 ? file.name.substring(0, 30) + '...' : file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2) + ' МБ';
            
            fileItem.innerHTML = `
                <div>
                    <i class="bi bi-file-earmark me-2"></i>
                    <strong>${fileName}</strong>
                    <small class="text-muted"> (${fileSize})</small>
                </div>
                <span class="badge bg-primary">${file.type || 'Неизвестный тип'}</span>
            `;
            
            fileList.appendChild(fileItem);
        });
    } else {
        filePreview.style.display = 'none';
    }
}

// Обработчик изменения выбора файлов
document.getElementById('designFiles').addEventListener('change', previewSelectedFiles);

// Показываем прогресс при отправке формы
document.querySelector('#uploadDesignModal form').addEventListener('submit', function(e) {
    const uploadButton = document.getElementById('uploadButton');
    const uploadProgress = document.getElementById('uploadProgress');
    
    uploadButton.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Загружаем...';
    uploadButton.disabled = true;
    uploadProgress.style.display = 'block';
});

// Сброс формы при закрытии модального окна
document.getElementById('uploadDesignModal').addEventListener('hidden.bs.modal', function () {
    const form = this.querySelector('form');
    const uploadButton = document.getElementById('uploadButton');
    const uploadProgress = document.getElementById('uploadProgress');
    const filePreview = document.getElementById('filePreview');
    
    form.reset();
    uploadButton.innerHTML = '<i class="bi bi-upload me-1"></i>Загрузить файлы дизайна';
    uploadButton.disabled = false;
    uploadProgress.style.display = 'none';
    filePreview.style.display = 'none';
});
</script>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/modals/upload-design-standard.blade.php ENDPATH**/ ?>