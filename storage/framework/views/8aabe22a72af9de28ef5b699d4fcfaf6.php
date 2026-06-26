<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-5">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <h4 class="mb-1">Iniciar sesión</h4>
          <p class="text-muted mb-4">Usa tu correo y contraseña para continuar.</p>

          
          <!--[if BLOCK]><![endif]--><?php if($errors->any()): ?>
            <div class="alert alert-danger py-2">
              <?php echo e($errors->first()); ?>

            </div>
          <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

          <form wire:submit.prevent="login" class="vstack gap-3">
            <div>
              <label class="form-label">Correo electrónico</label>
              <input type="email"
                     class="form-control <?php $__errorArgs = ['state.email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                     wire:model.defer="state.email"
                     placeholder="tu@correo.com" required>
              <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['state.email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div>
              <label class="form-label">Contraseña</label>
              <input type="password"
                     class="form-control <?php $__errorArgs = ['state.password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                     wire:model.defer="state.password"
                     placeholder="••••••••" required>
              <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['state.password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember"
                       wire:model.defer="state.remember">
                <label class="form-check-label" for="remember">Recordarme</label>
              </div>

              <!--[if BLOCK]><![endif]--><?php if(Route::has('password.request')): ?>
                <a href="<?php echo e(route('password.request')); ?>" class="small">¿Olvidaste tu contraseña?</a>
              <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <button class="btn btn-procafes-dark w-100" wire:loading.attr="disabled">
              <span wire:loading.remove>Ingresar</span>
              <span wire:loading>Ingresando…</span>
            </button>
          </form>

          
          <!--[if BLOCK]><![endif]--><?php if(Route::has('auth.google.redirect')): ?>
            <hr class="my-4">
            <a href="<?php echo e(route('auth.google.redirect')); ?>" class="btn btn-outline-secondary w-100">
              <i class="bi bi-google me-2"></i> Continuar con Google
            </a>
          <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

          <p class="text-center mt-4 mb-0">
            ¿No tienes cuenta?
            <!--[if BLOCK]><![endif]--><?php if(Route::has('register')): ?>
              <a href="<?php echo e(route('register')); ?>">Regístrate</a>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php /**PATH D:\6 TO CICLO\procafes\resources\views/livewire/pages/auth/login.blade.php ENDPATH**/ ?>