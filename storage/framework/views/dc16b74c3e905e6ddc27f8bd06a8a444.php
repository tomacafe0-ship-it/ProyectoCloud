<?php $__env->startSection('title','Panel | PROCAFES'); ?>

<?php $__env->startPush('styles'); ?>
<style>
  :root{
    --pcf-primary:#f2dd6c;
    --pcf-dark:#3e350e;
    --pcf-bg:#faf8ef;
  }

  body{
    background:var(--pcf-bg);
  }

  .chip{
    display:flex;
    align-items:center;
    gap:.5rem;

    background:#fff;

    border:1px solid rgba(0,0,0,.08);

    padding:.65rem .8rem;

    border-radius:.75rem;

    white-space:nowrap;
  }

  .chip i{
    color:var(--pcf-dark);
  }

  .stat-card{
    border:1px solid rgba(0,0,0,.06);
  }

  .stat-ico{
    width:44px;
    height:44px;

    border-radius:.75rem;

    display:grid;

    place-items:center;

    background:var(--pcf-primary);

    color:var(--pcf-dark);
  }

  .btn-procafes{
    background:var(--pcf-dark);

    color:#fff;

    border:0;
  }

  .btn-procafes:hover{
    filter:brightness(1.08);

    color:#fff;
  }

  .link-muted{
    color:#6c757d;

    text-decoration:none;
  }

  .link-muted:hover{
    color:#495057;
  }

  .scroll-x{
    overflow:auto;
  }

  .shadow-soft{
    box-shadow:
    0 .5rem 1rem rgba(
      0,
      0,
      0,
      .06
    ) !important;
  }

  .badge-soft{
    background:
    var(
      --pcf-primary
    );

    color:
    var(
      --pcf-dark
    );
  }

  .mini-help{
    font-size:.8rem;

    color:#6c757d;
  }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('admin-content'); ?>

<div class="d-flex align-items-center justify-content-between mb-3">

    <h1 class="h4 mb-0">
        Panel
    </h1>

    <div class="d-flex gap-2">

        <a
            class="btn btn-sm btn-outline-secondary"

            href="<?php echo e(route('home')); ?>"

            target="_blank">

            <i class="bi bi-shop-window me-1"></i>

            Ver tienda

        </a>

        <a

            href="<?php echo e(route('admin.products.create')); ?>"

            class="btn btn-sm btn-procafes">

            <i class="bi bi-plus-lg me-1"></i>

            Nuevo producto

        </a>

        
        <div class="dropdown">

            <button

                class="btn btn-sm btn-outline-dark dropdown-toggle"

                data-bs-toggle="dropdown">

                <i class="bi bi-download me-1"></i>

                Reportes

            </button>

            <ul class="dropdown-menu dropdown-menu-end">

                
                <li>

                    <a

                        class="dropdown-item"

                        href="<?php echo e(route('admin.reports.revenue')); ?>">

                        <i class="bi bi-graph-up me-2"></i>

                        Ingresos históricos

                    </a>

                </li>

                
                <li>

                    <a

                        class="dropdown-item"

                        href="<?php echo e(route('admin.reports.best')); ?>">

                        <i class="bi bi-trophy me-2"></i>

                        Productos más vendidos

                    </a>

                </li>

                
                <li>

                    <a

                        class="dropdown-item"

                        href="<?php echo e(route('admin.reports.products')); ?>">

                        <i class="bi bi-box-seam me-2"></i>

                        Inventario general

                    </a>

                </li>

                
                <li>

                    <a

                        class="dropdown-item"

                        href="<?php echo e(route('admin.reports.orders')); ?>">

                        <i class="bi bi-receipt me-2"></i>

                        Órdenes

                    </a>

                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                
                <li>

                    <a

                        class="dropdown-item"

                        href="<?php echo e(route('admin.reports.ventas')); ?>">

                        <i class="bi bi-bar-chart-line me-2"></i>

                        Rendimiento productos

                    </a>

                </li>

                
                <li>

                    <a

                        class="dropdown-item"

                        href="<?php echo e(route('admin.reports.inventario')); ?>">

                        <i class="bi bi-exclamation-triangle me-2"></i>

                        Reposición inventario

                    </a>

                </li>

                
                <li>

                    <a

                        class="dropdown-item"

                        href="<?php echo e(route('admin.reports.tendencias')); ?>">

                        <i class="bi bi-graph-up-arrow me-2"></i>

                        Tendencias consumo

                    </a>

                </li>

            </ul>

        </div>

    </div>

</div>


<div class="row g-3 mb-3">

    <div class="col-12 col-md-6 col-xl-3">

        <div class="card stat-card shadow-soft">

            <div class="card-body d-flex align-items-center gap-3">

                <div class="stat-ico">

                    <i class="bi bi-coin fs-5"></i>

                </div>

                <div class="flex-grow-1">

                    <div class="small text-muted">

                        Ingresos totales

                    </div>

                    <div class="fs-4 fw-bold">

                        S/
                        <?php echo e(number_format(
                            $stats['revenue'] ?? 0,
                            2
                        )); ?>


                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="col-12 col-md-6 col-xl-3">

        <div class="card stat-card shadow-soft">

            <div class="card-body d-flex align-items-center gap-3">

                <div class="stat-ico">

                    <i class="bi bi-bag-check fs-5"></i>

                </div>

                <div class="flex-grow-1">

                    <div class="small text-muted">

                        Órdenes

                    </div>

                    <div class="fs-4 fw-bold">

                        <?php echo e(number_format(
                            $stats['orders'] ?? 0
                        )); ?>


                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\6 TO CICLO\procafes\resources\views/dashboard.blade.php ENDPATH**/ ?>