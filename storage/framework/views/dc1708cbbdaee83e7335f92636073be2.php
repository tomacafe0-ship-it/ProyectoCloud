<?php $__env->startSection('title','Inicio | PROCAFES'); ?>

<?php $__env->startSection('content'); ?>
<?php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;
?>

<div class="container-fluid">
  <div class="row g-3">

    
    <aside class="col-12 col-lg-3">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h5 class="mb-3">Filtrar</h5>

          <form method="GET" action="<?php echo e(route('home')); ?>" class="vstack gap-3">

            
            <div>
              <label class="form-label">Buscar</label>
              <input
                type="text"
                name="q"
                value="<?php echo e(request('q','')); ?>"
                placeholder="Café, molido, etc."
                class="form-control"
              >
            </div>

            
            <div>
              <label class="form-label">Categoría</label>
              <select name="category" class="form-select">
                <option value="">Todas</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($c->id); ?>" <?php if((string)request('category') === (string)$c->id): echo 'selected'; endif; ?>>
                    <?php echo e($c->name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            
            <div>
              <label class="form-label">Marca</label>
              <select name="brand" class="form-select">
                <option value="">Todas</option>
                <?php $__currentLoopData = $brands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($b->id); ?>" <?php if((string)request('brand') === (string)$b->id): echo 'selected'; endif; ?>>
                    <?php echo e($b->name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            
            <div class="row g-2">
              <div class="col">
                <label class="form-label">Min (S/)</label>
                <input type="number" step="0.01" min="0" name="min" value="<?php echo e(request('min')); ?>" class="form-control">
              </div>
              <div class="col">
                <label class="form-label">Max (S/)</label>
                <input type="number" step="0.01" min="0" name="max" value="<?php echo e(request('max')); ?>" class="form-control">
              </div>
            </div>

            
            <div>
              <label class="form-label">Ordenar por</label>
              <select name="sort" class="form-select">
                <option value="new"        <?php if(request('sort','new')==='new'): echo 'selected'; endif; ?>>Nuevos primero</option>
                <option value="price_asc"  <?php if(request('sort')==='price_asc'): echo 'selected'; endif; ?>>Precio: menor a mayor</option>
                <option value="price_desc" <?php if(request('sort')==='price_desc'): echo 'selected'; endif; ?>>Precio: mayor a menor</option>
              </select>
            </div>

            <div class="d-grid gap-2 mt-1">
              <button class="btn btn-procafes-dark">Aplicar filtros</button>
              <a href="<?php echo e(route('home')); ?>" class="btn btn-light border">Limpiar</a>
            </div>
          </form>
        </div>
      </div>
    </aside>

    
    <section class="col-12 col-lg-9">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h4 class="mb-0">Productos</h4>
        <small class="text-muted"><?php echo e($products->total()); ?> resultados</small>
      </div>

      <?php if(!$products->count()): ?>
        <div class="alert alert-info">No se encontraron productos con los filtros seleccionados.</div>
      <?php else: ?>
        <div class="row g-3">
          <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-6 col-md-4">
              <div class="card h-100 shadow-sm border-0">

                
                <div class="ratio ratio-1x1 bg-light">
                  <?php if($p->image && Storage::disk('public')->exists($p->image)): ?>
                    <img src="<?php echo e(Storage::url($p->image)); ?>" alt="<?php echo e($p->name); ?>" class="w-100 h-100 object-fit-cover">
                  <?php else: ?>
                    <img src="https://via.placeholder.com/600x600?text=Producto" alt="<?php echo e($p->name); ?>" class="w-100 h-100 object-fit-cover">
                  <?php endif; ?>
                </div>

                
                <div class="card-body">
                  <div class="small text-muted mb-1">
                    <?php echo e($p->category->name ?? '—'); ?> <?php if($p->brand): ?> • <?php echo e($p->brand->name); ?> <?php endif; ?>
                  </div>
                  <h6 class="card-title mb-1" title="<?php echo e($p->name); ?>"><?php echo e(Str::limit($p->name, 50)); ?></h6>
                  <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">S/ <?php echo e(number_format($p->price, 2)); ?></span>
                    <span class="badge <?php echo e($p->stock > 0 ? 'text-bg-success' : 'text-bg-secondary'); ?>">
                      <?php echo e($p->stock > 0 ? 'Stock: '.$p->stock : 'Sin stock'); ?>

                    </span>
                  </div>
                </div>

                
                <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
                  <div class="vstack gap-2">

                    
                    <button
                      type="button"
                      class="btn btn-procafes-accent w-100 btn-add-to-cart"
                      data-id="<?php echo e($p->id); ?>"
                      data-name="<?php echo e($p->name); ?>"
                      data-price="<?php echo e($p->price); ?>"
                      data-image="<?php echo e($p->image ? Storage::url($p->image) : 'https://via.placeholder.com/600x600?text=Producto'); ?>"
                      data-url="#"
                      <?php echo e($p->stock > 0 ? '' : 'disabled'); ?>

                    >
                      <i class="bi bi-cart-plus me-1"></i> Agregar al carrito
                    </button>

                    
                    <?php if(auth()->guard()->check()): ?>
                      <form class="js-wishlist-toggle d-grid" data-product="<?php echo e($p->id); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-outline-danger w-100">
                          <i class="bi bi-heart me-1"></i>
                          <span class="js-wl-text">Añadir a favoritos</span>
                        </button>
                      </form>
                    <?php else: ?>
                      <a href="<?php echo e(route('login')); ?>" class="btn btn-outline-danger w-100">
                        <i class="bi bi-heart me-1"></i> Añadir a favoritos
                      </a>
                    <?php endif; ?>

                  </div>
                </div>

              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mt-3">
          <?php echo e($products->links('pagination::bootstrap-5')); ?>

        </div>
      <?php endif; ?>
    </section>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\6 TO CICLO\procafes\resources\views/home.blade.php ENDPATH**/ ?>