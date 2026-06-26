<?php
  $cartItems = session('cart', []);
  $serverTotal = collect($cartItems)->sum(fn($i) => (float)($i['price'] ?? 0) * (int)($i['quantity'] ?? 1));
?>

<div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="cartOffcanvasLabel">Tu carrito</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
  </div>

  <div class="offcanvas-body d-flex flex-column p-0">
    
    <div id="cartItems"
         class="list-group list-group-flush mb-0 flex-grow-1"
         style="min-height:120px; max-height:60vh; overflow:auto;"></div>

    
    <div id="cartEmpty" class="text-center py-4 d-none">
      <div class="display-6 mb-2"><i class="bi bi-bag-x"></i></div>
      <p class="mb-3 text-muted">Tu carrito está vacío</p>
      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Seguir comprando</button>
    </div>

    <style>
      .offcanvas-footer{
        position: sticky; bottom: 0; background: #fff;
        border-top: 1px solid rgba(0,0,0,.1); box-shadow: 0 -2px 8px rgba(0,0,0,.04); z-index: 3;
      }
    </style>
    <div class="offcanvas-footer p-3">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-semibold">Total</span>
        <span id="cartTotal" class="fw-bold">S/ <?php echo e(number_format($serverTotal, 2)); ?></span>
      </div>

      <div class="d-grid gap-2">
        <button id="btnClearCart" type="button" class="btn btn-outline-secondary w-100">
          Vaciar
        </button>
        <?php if(auth()->guard()->check()): ?>
          <a href="<?php echo e(Route::has('checkout') ? route('checkout') : url('/checkout')); ?>"
             class="btn btn-dark w-100" <?php if($serverTotal <= 0): echo 'disabled'; endif; ?>>
            Ir a pagar
          </a>
        <?php else: ?>
          <a href="<?php echo e(route('login')); ?>" class="btn btn-primary w-100">
            Iniciar sesión para pagar
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php /**PATH D:\6 TO CICLO\procafes\resources\views/partials/cart-offcanvas.blade.php ENDPATH**/ ?>