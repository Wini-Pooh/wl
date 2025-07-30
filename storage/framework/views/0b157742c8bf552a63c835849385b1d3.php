<?php $__env->startSection('head'); ?>
    <script src="<?php echo e(asset('js/phone-mask.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="auth-brand">
        <h1><i class="bi bi-building-gear me-2"></i><?php echo e(config('app.name', 'REM System')); ?></h1>
        <p>Система управления ремонтными проектами</p>
    </div>

    <div class="auth-form">
        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="phone" class="form-label">Номер телефона</label>
                <input id="phone" type="tel" 
                       class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       name="phone" 
                       value="<?php echo e(old('phone')); ?>" 
                       required 
                       autocomplete="tel" 
                       autofocus
                       placeholder="+7 (999) 123-45-67">

                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback">
                        <?php echo e($message); ?>

                    </div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Пароль</label>
                <input id="password" type="password" 
                       class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       name="password" 
                       required 
                       autocomplete="current-password">

                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback">
                        <?php echo e($message); ?>

                    </div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>>
                <label class="form-check-label" for="remember">
                    Запомнить меня
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
            </button>

            <div class="auth-links">
                <?php if(Route::has('password.request')): ?>
                    <a href="<?php echo e(route('password.request')); ?>">
                        Забыли пароль?
                    </a>
                <?php endif; ?>
                
                <?php if(Route::has('register')): ?>
                    <div class="mt-2">
                        <span class="text-muted">Нет аккаунта?</span>
                        <a href="<?php echo e(route('register')); ?>" class="ms-1">
                            Зарегистрироваться
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('image-content'); ?>
    <h2>Добро пожаловать!</h2>
    <p>Войдите в свой аккаунт для управления ремонтными проектами, создания смет и контроля финансов.</p>
    <div class="mt-4">
        <i class="bi bi-tools" style="font-size: 4rem; opacity: 0.7;"></i>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/auth/login.blade.php ENDPATH**/ ?>