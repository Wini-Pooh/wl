

<?php $__env->startSection('styles'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('styles'); ?>
    <link href="<?php echo e(asset('css/' . $pageConfig['cssFile'])); ?>" rel="stylesheet">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-content'); ?>
    <?php echo $__env->make('partner.projects.tabs.' . $pageConfig['tabFile'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <?php if(isset($pageConfig['modalFile'])): ?>
        <?php echo $__env->make('partner.projects.tabs.modals.' . $pageConfig['modalFile'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <?php echo \Illuminate\View\Factory::parentPlaceholder('scripts'); ?>
    <script>
        $(document).ready(function() {
            console.log('=== <?php echo e($pageConfig['pageName']); ?> ===');
            
            initPage();
            
            // Показываем сообщения если есть
            <?php if(session('success')): ?>
                showMessage('<?php echo e(session('success')); ?>', 'success');
            <?php endif; ?>
            
            <?php if(session('error')): ?>
                showMessage('<?php echo e(session('error')); ?>', 'error');
            <?php endif; ?>
        });
        
        function initPage() {
            console.log('<?php echo e($pageConfig['initIcon']); ?> Инициализация <?php echo e($pageConfig['pageNameLower']); ?>...');
            
            // Инициализируем базовые обработчики
            initBaseHandlers();
            
            // Инициализируем фильтры
            initFilterHandlers();
            
            // Инициализируем специфичные обработчики
            <?php echo e($pageConfig['initFunction']); ?>();
            
            console.log('✅ <?php echo e($pageConfig['pageNameFormatted']); ?> инициализирована');
        }
        
        function initBaseHandlers() {
            console.log('🎯 Инициализация базовых обработчиков...');
            
            // Обработчик переключения фильтров
            $('#toggleFilters').off('click').on('click', function() {
                const content = $('#filtersContent');
                const icon = $(this).find('.bi');
                content.slideToggle(300);
                icon.toggleClass('bi-chevron-down bi-chevron-up');
            });
            
            // Обработчик модального окна
            $('[data-modal-type="<?php echo e($pageConfig['modalType']); ?>"]').off('click').on('click', function() {
                $('#<?php echo e($pageConfig['modalId']); ?>').modal('show');
            });
        }
        
        function initFilterHandlers() {
            console.log('🔍 Инициализация обработчиков фильтров...');
            
            // Сброс фильтров
            $('.btn-reset-filters').on('click', function(e) {
                e.preventDefault();
                window.location.href = $(this).data('reset-url');
            });
        }
        
        function <?php echo e($pageConfig['initFunction']); ?>() {
            console.log('<?php echo e($pageConfig['handlerIcon']); ?> Инициализация обработчиков <?php echo e($pageConfig['pageNameLower']); ?>...');
            
            // Обработчик предварительного просмотра файлов
            $('#<?php echo e($pageConfig['fileInputId']); ?>').on('change', function() {
                previewSelectedFiles();
            });
        }
        
        // Функция предварительного просмотра файлов
        function previewSelectedFiles() {
            const files = document.getElementById('<?php echo e($pageConfig['fileInputId']); ?>').files;
            const preview = document.getElementById('filePreview');
            const previewList = document.getElementById('previewList');
            
            if (files.length > 0) {
                previewList.innerHTML = '';
                
                Array.from(files).forEach((file, index) => {
                    const listItem = document.createElement('div');
                    listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                    listItem.innerHTML = `
                        <div>
                            <i class="bi <?php echo e($pageConfig['fileIcon']); ?> me-2"></i>
                            ${file.name}
                            <small class="text-muted">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">${index + 1}</span>
                    `;
                    previewList.appendChild(listItem);
                });
                
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }
        
        // Функция для показа сообщений
        function showMessage(message, type = 'info') {
            const alertClass = type === 'success' ? 'alert-success' : 
                             type === 'error' ? 'alert-danger' : 'alert-info';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Добавляем сообщение в начало контейнера
            $('#<?php echo e($pageConfig['tabContentId']); ?>').prepend(alertHtml);
            
            // Автоматически скрываем через 5 секунд
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
        
        // Функция подтверждения удаления
        function confirmDelete(<?php echo e($pageConfig['itemIdParam']); ?>, filename) {
            if (confirm(`Вы уверены, что хотите удалить <?php echo e($pageConfig['itemNameAccusative']); ?> "${filename}"?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `<?php echo e($pageConfig['deleteRoute']); ?>`.replace('__ID__', <?php echo e($pageConfig['itemIdParam']); ?>);
                
                // Добавляем CSRF токен
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '<?php echo e(csrf_token()); ?>';
                
                // Добавляем метод DELETE
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                
                form.appendChild(csrfInput);
                form.appendChild(methodInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        <?php if(isset($pageConfig['viewRoute'])): ?>
        // Функция открытия просмотра
        function <?php echo e($pageConfig['openFunction']); ?>(<?php echo e($pageConfig['itemIdParam']); ?>) {
            window.open(`<?php echo e($pageConfig['viewRoute']); ?>`.replace('__ID__', <?php echo e($pageConfig['itemIdParam']); ?>), '_blank');
        }
        <?php endif; ?>
        
        console.log('✅ Скрипты <?php echo e($pageConfig['pageNameLower']); ?> загружены');
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('partner.projects.layouts.project-base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/partner/projects/pages/_template.blade.php ENDPATH**/ ?>