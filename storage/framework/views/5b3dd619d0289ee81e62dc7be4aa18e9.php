<!-- Модальное окно для загрузки схем -->
<div class="modal fade" id="uploadSchemeModal" tabindex="-1" aria-labelledby="uploadSchemeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadSchemeModalLabel">
                    <i class="bi bi-diagram-3 me-2"></i>Загрузить схемы
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('partner.projects.schemes.upload', $project)); ?>" method="POST" enctype="multipart/form-data" id="uploadSchemeForm">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Зона загрузки файлов -->
                    <div class="upload-zone mb-4" id="schemeUploadZone">
                        <div class="upload-content text-center p-4 border border-dashed rounded">
                            <i class="bi bi-diagram-3 display-4 text-muted mb-3"></i>
                            <h5>Выберите схемы для загрузки</h5>
                            <p class="text-muted mb-3">Поддерживаемые форматы: JPG, PNG, GIF, WEBP, SVG, PDF, DWG, DXF, AI</p>
                            <input type="file" id="schemeFileInput" name="schemes[]" multiple 
                                   accept=".jpeg,.jpg,.png,.gif,.webp,.svg,.pdf,.dwg,.dxf,.ai" class="form-control mb-3" required>
                            <small class="text-muted">Максимальный размер файла: 50 МБ</small>
                        </div>
                    </div>
                    
                    <!-- Дополнительные параметры -->
                    <div class="row">
                        <div class="col-md-6">
                            <label for="schemeTypeSelect" class="form-label">Тип схемы</label>
                            <select class="form-select" id="schemeTypeSelect" name="scheme_type">
                                <option value="">Выберите тип</option>
                                <option value="electrical">Электрика</option>
                                <option value="plumbing">Сантехника</option>
                                <option value="ventilation">Вентиляция</option>
                                <option value="layout">Планировка</option>
                                <option value="structure">Конструкция</option>
                                <option value="technical">Техническая схема</option>
                                <option value="construction">Строительный чертеж</option>
                                <option value="other">Другое</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="schemeRoomSelect" class="form-label">Помещение</label>
                            <select class="form-select" id="schemeRoomSelect" name="room">
                                <option value="">Выберите помещение</option>
                                <option value="kitchen">Кухня</option>
                                <option value="living_room">Гостиная</option>
                                <option value="bedroom">Спальня</option>
                                <option value="bathroom">Ванная</option>
                                <option value="hallway">Прихожая</option>
                                <option value="general">Общий план</option>
                                <option value="other">Другое</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <label for="schemeDescription" class="form-label">Описание (необязательно)</label>
                            <textarea class="form-control" id="schemeDescription" name="description" rows="3" 
                                      placeholder="Добавьте описание к схемам..."></textarea>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-upload me-2"></i>Загрузить схемы
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.upload-zone {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.upload-zone:hover {
    border-color: #28a745;
    background-color: #f8f9fa;
}

.upload-content {
    padding: 2rem;
}
</style>
                           <?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/tabs/modals/scheme-modal.blade.php ENDPATH**/ ?>