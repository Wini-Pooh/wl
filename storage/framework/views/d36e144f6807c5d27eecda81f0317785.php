<?php if($paginator->hasPages()): ?>
    <nav class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center text-muted">
            <small>
                Показано <?php echo e($paginator->firstItem() ?? 0); ?>-<?php echo e($paginator->lastItem() ?? 0); ?> 
                из <?php echo e($paginator->total()); ?> записей
            </small>
        </div>
        
        <ul class="pagination pagination-modern mb-0">
            
            <?php if($paginator->onFirstPage()): ?>
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="bi bi-chevron-left"></i>
                    </span>
                </li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <?php if(is_string($element)): ?>
                    <li class="page-item disabled">
                        <span class="page-link"><?php echo e($element); ?></span>
                    </li>
                <?php endif; ?>

                
                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $paginator->currentPage()): ?>
                            <li class="page-item active">
                                <span class="page-link"><?php echo e($page); ?></span>
                            </li>
                        <?php else: ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($paginator->hasMorePages()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            <?php else: ?>
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            <?php endif; ?>
        </ul>
        
        <?php if($paginator->hasPages()): ?>
            <div class="d-flex align-items-center">
                <small class="text-muted me-2">Страница</small>
                <select class="form-select form-select-sm pagination-page-select" style="width: auto;" onchange="window.location.href = this.value">
                    <?php for($i = 1; $i <= $paginator->lastPage(); $i++): ?>
                        <option value="<?php echo e($paginator->url($i)); ?>" <?php echo e($i == $paginator->currentPage() ? 'selected' : ''); ?>>
                            <?php echo e($i); ?>

                        </option>
                    <?php endfor; ?>
                </select>
                <small class="text-muted ms-2">из <?php echo e($paginator->lastPage()); ?></small>
            </div>
        <?php endif; ?>
    </nav>
<?php endif; ?>
<?php /**PATH C:\OSPanel\domains\rem\resources\views/custom/pagination.blade.php ENDPATH**/ ?>