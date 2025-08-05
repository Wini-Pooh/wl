<!-- Модальное окно для загрузки фотографий (без AJAX) -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?php echo e(route('partner.projects.photos.upload', $project)); ?>" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadPhotoModalLabel">
                        <i class="bi bi-camera me-2"></i>
                        Загрузить фотографии
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Выбор файлов -->
                    <div class="mb-4">
                        <label for="photoFiles" class="form-label">
                            <i class="bi bi-images me-1"></i>
                            Выберите фотографии
                        </label>
                        <input type="file" 
                               class="form-control" 
                               id="photoFiles" 
                               name="files[]" 
                               multiple 
                               accept="image/*" 
                               required
                               onchange="previewSelectedFiles()">
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Поддерживаемые форматы: JPG, PNG, GIF, WebP. 
                            Максимальный размер файла: 10 МБ.
                        </div>
                    </div>
                    
                    <!-- Предпросмотр выбранных файлов -->
                    <div id="filePreview" class="mb-4" style="display: none;">
                        <!-- Сюда будет добавлен список файлов через JavaScript -->
                    </div>
                    
                    <!-- Метаданные -->
                    <div class="row">
                        <div class="col-md-6">
                            <label for="photoCategory" class="form-label">Категория</label>
                            <select class="form-select" id="photoCategory" name="category">
                                <option value="">Выберите категорию</option>
                                <option value="before">До ремонта</option>
                                <option value="after">После ремонта</option>
                                <option value="process">Процесс работы</option>
                                <option value="progress">Ход работ</option>
                                <option value="materials">Материалы</option>
                                <option value="problems">Проблемы</option>
                                <option value="design">Дизайн</option>
                                <option value="furniture">Мебель</option>
                                <option value="decor">Декор</option>
                                <option value="demolition">Демонтаж</option>
                                <option value="floors">Полы</option>
                                <option value="walls">Стены</option>
                                <option value="ceiling">Потолок</option>
                                <option value="electrical">Электрика</option>
                                <option value="plumbing">Сантехника</option>
                                <option value="heating">Отопление</option>
                                <option value="doors">Двери</option>
                                <option value="windows">Окна</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="photoLocation" class="form-label">Помещение</label>
                            <select class="form-select" id="photoLocation" name="location">
                                <option value="">Выберите помещение</option>
                                <option value="living_room">Гостиная</option>
                                <option value="kitchen">Кухня</option>
                                <option value="bedroom">Спальня</option>
                                <option value="bathroom">Ванная</option>
                                <option value="toilet">Туалет</option>
                                <option value="hallway">Прихожая</option>
                                <option value="balcony">Балкон</option>
                                <option value="storage">Кладовка</option>
                                <option value="office">Кабинет</option>
                                <option value="garage">Гараж</option>
                                <option value="basement">Подвал</option>
                                <option value="attic">Чердак</option>
                                <option value="exterior">Фасад</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label for="photoDescription" class="form-label">Описание</label>
                        <textarea class="form-control" 
                                  id="photoDescription" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Дополнительное описание для всех загружаемых фотографий..."></textarea>
                    </div>
                    
                    <!-- Прогресс загрузки (показывается после отправки формы) -->
                    <div id="uploadProgress" class="mt-3" style="display: none;">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Загрузка...</span>
                            </div>
                            <span>Загрузка фотографий на сервер...</span>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>
                        Отменить
                    </button>
                    <button type="submit" class="btn btn-primary" id="uploadButton">
                        <i class="bi bi-upload me-1"></i>
                        Загрузить фотографии
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Показываем прогресс при отправке формы
document.querySelector('#uploadPhotoModal form').addEventListener('submit', function() {
    document.getElementById('uploadProgress').style.display = 'block';
    document.getElementById('uploadButton').disabled = true;
    document.getElementById('uploadButton').innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Загружаем...';
});

// Сброс формы при закрытии модального окна
document.getElementById('uploadPhotoModal').addEventListener('hidden.bs.modal', function () {
    this.querySelector('form').reset();
    document.getElementById('filePreview').style.display = 'none';
    document.getElementById('uploadProgress').style.display = 'none';
    document.getElementById('uploadButton').disabled = false;
    document.getElementById('uploadButton').innerHTML = '<i class="bi bi-upload me-1"></i>Загрузить фотографии';
});
</script>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/modals/upload-photo-standard.blade.php ENDPATH**/ ?>