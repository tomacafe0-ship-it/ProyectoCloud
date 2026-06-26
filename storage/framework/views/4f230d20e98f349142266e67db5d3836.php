<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

  <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo.png')); ?>">
  <link rel="shortcut icon" href="<?php echo e(asset('images/logo.png')); ?>">

  <title><?php echo $__env->yieldContent('title', 'PROCAFES'); ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

  <style>
    .bg-procafes { background-color:#f2dd6c; }
    .btn-procafes-dark { background-color:#3e350e; color:#fff; }
    .btn-procafes-dark:hover { filter:brightness(1.1); }
    .btn-procafes-accent { background-color:#daad29; color:#3e350e; }
    .btn-procafes-accent:hover { filter:brightness(1.05); }
    .link-procafes { color:#3e350e; }
    .link-procafes:hover { color:#2c250a; }
  </style>

  <?php if(class_exists(\Livewire\Livewire::class)): ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

  <?php endif; ?>

  <script>
    window.Laravel = {
      csrfToken: "<?php echo e(csrf_token()); ?>",
      routes: {
        index: "<?php echo e(url('cart')); ?>",
        add:   "<?php echo e(url('cart/add')); ?>",
        base:  "<?php echo e(url('cart')); ?>",
        clear: "<?php echo e(url('cart')); ?>"
      }
    };
  </script>

  <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="bg-light">

  <?php if ($__env->exists('partials.header')) echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  
  <div class="container mt-3">
    <?php if(session('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if(session('info')): ?>
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?php echo e(session('info')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e($errors->first()); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
  </div>

  <main class="<?php echo $__env->yieldContent('main_class', 'container py-4'); ?>">
    <?php if (! empty(trim($__env->yieldContent('content')))): ?>
      <?php echo $__env->yieldContent('content'); ?>
    <?php else: ?>
      <?php echo e($slot ?? ''); ?>

    <?php endif; ?>
  </main>

  <?php echo $__env->renderWhen(View::exists('partials.cart-offcanvas'), 'partials.cart-offcanvas', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>

  <?php if(class_exists(\Livewire\Livewire::class)): ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

  <?php endif; ?>

  <script>
    window.App = {
      isAuth: <?php echo json_encode(auth()->check(), 15, 512) ?>,
      routes: {
        checkout: "<?php echo e(Route::has('checkout') ? route('checkout') : ''); ?>",
        login: "<?php echo e(route('login')); ?>"
      }
    };
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo e(asset('js/cart.js')); ?>"></script>

  <script>
/** Wishlist Toggle (AJAX) + contador en header */
document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const wlCountEl = document.getElementById('wishlistCount');
  const toggleUrl = "<?php echo e(route('wishlist.toggle')); ?>";

  function setCount(n) {
    if (wlCountEl) wlCountEl.textContent = n;
  }

  function updateButton(btn, added) {
    const txt = btn.querySelector('.js-wl-text');
    const icon = btn.querySelector('i');
    if (added) {
      btn.classList.remove('btn-outline-danger');
      btn.classList.add('btn-danger','active');
      if (icon) icon.className = 'bi bi-heart-fill me-1';
      if (txt) txt.textContent = 'En favoritos';
    } else {
      btn.classList.add('btn-outline-danger');
      btn.classList.remove('btn-danger','active');
      if (icon) icon.className = 'bi bi-heart me-1';
      if (txt) txt.textContent = 'Añadir a favoritos';
    }
  }

  async function toggleWishlist(productId) {
    const res = await fetch(toggleUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
      body: JSON.stringify({ product_id: productId })
    });
    if (!res.ok) throw new Error('Toggle error');
    return await res.json();
  }

  // Intercepta formularios .js-wishlist-toggle (home + wishlist)
  document.body.addEventListener('submit', async (ev) => {
    const form = ev.target.closest('.js-wishlist-toggle');
    if (!form) return;

    ev.preventDefault();
    if (!window.App?.isAuth) { window.location.href = "<?php echo e(route('login')); ?>"; return; }

    const productId = form.getAttribute('data-product');
    const btn = form.querySelector('button');

    btn?.setAttribute('disabled','disabled');
    try {
      const data = await toggleWishlist(productId);
      setCount(data.count ?? 0);

      // Si estoy en el listado (home): solo cambia aspecto del botón
      updateButton(btn, data.added);

      // Si estoy en la página de wishlist y se quitó, remueve la card
      if (!data.added) {
        const card = form.closest('.js-wishlist-card');
        if (card) card.remove();
      }
    } catch (e) {
      console.error(e);
    } finally {
      btn?.removeAttribute('disabled');
    }
  });
});
</script>
    <!-- 🔰 BOTÓN FLOTANTE DE WHATSAPP -->
<a href="https://wa.me/+51955236237?text=Hola%20PROCAFES,%20quisiera%20hacer%20un%20pedido%20o%20tengo%20una%20consulta."
   target="_blank"
   class="whatsapp-float"
   aria-label="Chatea con nosotros por WhatsApp">
  <img src="<?php echo e(asset('images/whatsapp.png')); ?>" alt="WhatsApp" loading="lazy">
</a>

<style>
/* ====== BOTÓN FLOTANTE WHATSAPP ====== */
.whatsapp-float {
  position: fixed;
  width: 75px; /* 🔹 Más grande */
  height: 75px;
  bottom: 25px;
  right: 25px;
  background-color: #25D366;
  border-radius: 50%;
  box-shadow: 0 3px 15px rgba(0,0,0,0.25);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform .25s ease-in-out, box-shadow .25s;
}
.whatsapp-float:hover {
  transform: scale(1.08);
  box-shadow: 0 5px 18px rgba(0,0,0,0.35);
}

/* Imagen dentro del círculo */
.whatsapp-float img {
  width: 48px; /* 🔹 Tamaño del ícono */
  height: 48px;
  object-fit: contain;
  border-radius: 50%;
}

/* 🔸 Versión móvil */
@media (max-width:768px){
  .whatsapp-float {
    width: 65px;
    height: 65px;
    bottom: 20px;
    right: 20px;
  }
  .whatsapp-float img {
    width: 42px;
    height: 42px;
  }
}
</style>
<!-- 🔰 FIN BOTÓN WHATSAPP -->
>



  <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index:1080;"></div>

  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\6 TO CICLO\procafes\resources\views/layouts/app.blade.php ENDPATH**/ ?>