<?php $__env->startSection('head'); ?>
    <script src="<?php echo e(asset('js/phone-mask.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="auth-brand">
        <h1><i class="bi bi-building-gear me-2"></i><?php echo e(config('app.name', 'REM System')); ?></h1>
        <p>Создайте аккаунт для работы с проектами</p>
    </div>

    <div class="auth-form">
        <form method="POST" action="<?php echo e(route('register')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="name" class="form-label">Имя</label>
                <input id="name" type="text" 
                       class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       name="name" 
                       value="<?php echo e(old('name')); ?>" 
                       required 
                       autocomplete="name" 
                       autofocus
                       placeholder="Введите ваше имя">

                <?php $__errorArgs = ['name'];
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
                <label for="email" class="form-label">Email <span class="text-muted">(необязательно)</span></label>
                <input id="email" type="email" 
                       class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                       name="email" 
                       value="<?php echo e(old('email')); ?>" 
                       autocomplete="email"
                       placeholder="example@mail.com">

                <?php $__errorArgs = ['email'];
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
                       autocomplete="new-password"
                       placeholder="Минимум 8 символов">

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

            <div class="form-group">
                <label for="password-confirm" class="form-label">Подтверждение пароля</label>
                <input id="password-confirm" type="password" 
                       class="form-control" 
                       name="password_confirmation" 
                       required 
                       autocomplete="new-password"
                       placeholder="Повторите пароль">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus me-2"></i>Зарегистрироваться
            </button>

            <div class="auth-links">
                <span class="text-muted">Уже есть аккаунт?</span>
                <a href="<?php echo e(route('login')); ?>" class="ms-1">
                    Войти
                </a>
            </div>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('image-content'); ?>
    <h2>Присоединяйтесь к нам!</h2>
    <p>Создайте аккаунт и начните эффективно управлять своими ремонтными проектами уже сегодня.</p>
    <div class="mt-4">
        <i class="bi bi-house-gear" style="font-size: 4rem; opacity: 0.7;"></i>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        // Дополнительная инициализация маски телефона для страницы регистрации
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📝 Инициализация страницы регистрации...');
            
            // Убеждаемся, что маска применена
            const phoneInput = document.getElementById('phone');
            if (phoneInput && typeof window.initPhoneMask === 'function') {
                window.initPhoneMask(phoneInput);
            }
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\rem\resources\views/auth/register.blade.php ENDPATH**/ ?>