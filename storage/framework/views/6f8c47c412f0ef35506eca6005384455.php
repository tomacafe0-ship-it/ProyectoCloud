<header class="border-bottom bg-procafes">
  <div class="container d-flex align-items-center justify-content-between py-2">

    
    <a href="<?php echo e(route('home')); ?>" class="d-flex align-items-center text-decoration-none">
      <img src="<?php echo e(asset('images/logo.png')); ?>" alt="PROCAFES" width="36" height="36" class="me-2">
      <strong class="text-dark">PROCAFES</strong>
    </a>

    
    <form action="<?php echo e(route('home')); ?>" method="GET" class="flex-grow-1 mx-3" style="max-width:680px;">
      <div class="input-group">
        <input
          type="text"
          name="q"
          value="<?php echo e(request('q')); ?>"
          class="form-control"
          placeholder="Buscar productos...">
        <button class="btn btn-outline-secondary" type="submit">
          <i class="bi bi-search"></i>
        </button>
      </div>
    </form>

    
    <div class="d-flex align-items-center gap-2">

      
      <a href="<?php echo e(route('nosotros')); ?>" class="btn btn-link link-procafes text-decoration-none">Nosotros</a>
      <a href="<?php echo e(route('ubicanos')); ?>" class="btn btn-link link-procafes text-decoration-none">Ubícanos</a>

      
      <?php
        $wishlistCount = auth()->check()
          ? \App\Models\Wishlist::where('user_id', auth()->id())->count()
          : 0;
      ?>
      <a href="<?php echo e(route('wishlist.index')); ?>"
         class="btn btn-sm position-relative"
         style="background:#E0CF61;border:none;color:#3E350E;">
        <i class="bi bi-heart"></i>
        <span id="wishlistCount"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          <?php echo e($wishlistCount); ?>

        </span>
      </a>

      
      <button type="button"
              class="btn btn-sm position-relative"
              style="background:#E0CF61;border:none;color:#3E350E;"
              data-bs-toggle="offcanvas"
              data-bs-target="#cartOffcanvas"
              aria-controls="cartOffcanvas">
        <i class="bi bi-cart"></i>
        <span id="cartBadge"
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">0</span>
      </button>

      
      <?php if(auth()->guard()->check()): ?>
        <div class="dropdown">
          <button class="btn btn-sm dropdown-toggle"
                  data-bs-toggle="dropdown"
                  style="background:#E0CF61;border:none;color:#3E350E;">
            Hola, <?php echo e(Str::limit(auth()->user()->name, 10)); ?>

          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?php echo e(route('customer.dashboard')); ?>">Panel</a></li>
            <li><a class="dropdown-item" href="<?php echo e(route('wishlist.index')); ?>">Mis favoritos</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button class="dropdown-item">Cerrar sesión</button>
              </form>
            </li>
          </ul>
        </div>
      <?php else: ?>
        <a href="<?php echo e(route('login')); ?>" class="btn btn-sm btn-outline-dark">Iniciar sesión</a>
        <a href="<?php echo e(route('register')); ?>" class="btn btn-sm btn-procafes-dark">Registrarse</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<?php /**PATH D:\6 TO CICLO\procafes\resources\views/partials/header.blade.php ENDPATH**/ ?>