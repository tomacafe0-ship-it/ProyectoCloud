<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Público / Base
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\RegisterController;

// Cliente autenticado
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\BoletaController as CustomerBoletaController;

// Checkout propio
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentDemoController;

// Admin
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\BrandController     as AdminBrandController;
use App\Http\Controllers\Admin\ProductController   as AdminProductController;
use App\Http\Controllers\Admin\UserController      as AdminUserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\OrderController;

// Mercado Pago
use App\Http\Controllers\Payment\MercadoPagoController;
use App\Http\Controllers\Payment\MercadoPagoWebhookController;

// Modelos
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| BINDINGS
|--------------------------------------------------------------------------
*/
Route::bind('brand', fn ($v) => Brand::query()
        ->whereKey($v)
        ->firstOrFail()
);
Route::bind('category', fn ($v) => Category::query()
        ->whereKey($v)
        ->firstOrFail()
);
Route::bind('product', fn ($v) => Product::query()
        ->whereKey($v)
        ->firstOrFail()
);

/*
|--------------------------------------------------------------------------
| RUTAS PUBLICAS
|--------------------------------------------------------------------------
*/
Route::get('/',[HomeController::class, 'index'])->name('home');
Route::view('/nosotros','nosotros')->name('nosotros');
Route::view('/ubicanos','ubicanos')->name('ubicanos');

/*
|--------------------------------------------------------------------------
| CARRITO
|--------------------------------------------------------------------------
*/
Route::prefix('cart')->name('cart.') ->group(function () {
        Route::get('/',[CartController::class, 'index'])->name('index');
        Route::post('/add',[CartController::class, 'add'])->name('add');
        Route::patch('/{rowId}',[CartController::class, 'update'])->name('update');
        Route::delete('/{rowId}',[CartController::class, 'remove'])->name('remove');
        Route::delete('/',
[CartController::class, 'clear']
        )->name('clear');
    });

/*
|--------------------------------------------------------------------------
| LOGIN / REGISTRO
|--------------------------------------------------------------------------
*/

Route::view(
    '/login',
    'auth.login'
)

->middleware('guest')

->name('login');

Route::post(
    '/register',
    [RegisterController::class, 'store']
)

->middleware('guest')

->name('register.store');

/*
|--------------------------------------------------------------------------
| GOOGLE OAUTH
|--------------------------------------------------------------------------
*/

Route::prefix('auth/google')

    ->name('auth.google.')

    ->group(function () {

        Route::get(
            '/redirect',
            [GoogleController::class, 'redirect']
        )->name('redirect');

        Route::get(
            '/callback',
            [GoogleController::class, 'callback']
        )->name('callback');
    });

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post(
    '/logout',

    function (
        Request $request
    ) {

        Auth::guard('web')
            ->logout();

        $request
            ->session()
            ->invalidate();

        $request
            ->session()
            ->regenerateToken();

        return redirect()
            ->route('home');
    }

)

->middleware('auth')

->name('logout');

/*
|--------------------------------------------------------------------------
| CLIENTE AUTENTICADO
|--------------------------------------------------------------------------
*/

Route::middleware('auth')

->group(function () {

    Route::get(
        '/cliente',
        [
            CustomerDashboardController::class,
            'index'
        ]
    )

    ->name(
        'customer.dashboard'
    );

    Route::post(
        '/cliente/foto',
        [
            CustomerDashboardController::class,
            'updatePhoto'
        ]
    )

    ->name(
        'customer.photo.update'
    );

    Route::get(
        '/cliente/pedidos/{order}/boleta',

        [
            CustomerBoletaController::class,
            'download'
        ]
    )

    ->name(
        'customer.boleta.download'
    );

    Route::view(
        '/profile',
        'profile'
    )

    ->name(
        'profile'
    );

    Route::view(
        '/mis-productos',
        'products'
    )

    ->name(
        'customer.products'
    );

    /*
    |--------------------------------------------------------------------------
    | WISHLIST
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/wishlist',
        [
            WishlistController::class,
            'index'
        ]
    )

    ->name(
        'wishlist.index'
    );

    Route::post(
        '/wishlist/add/{product}',
        [
            WishlistController::class,
            'store'
        ]
    )

    ->name(
        'wishlist.add'
    );

    Route::delete(
        '/wishlist/remove/{product}',
        [
            WishlistController::class,
            'destroy'
        ]
    )

    ->name(
        'wishlist.remove'
    );

    Route::post(
        '/wishlist/toggle',
        [
            WishlistController::class,
            'toggle'
        ]
    )

    ->name(
        'wishlist.toggle'
    );

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/checkout',
        [
            CheckoutController::class,
            'index'
        ]
    )

    ->name(
        'checkout'
    );

    Route::post(
        '/checkout',
        [
            CheckoutController::class,
            'process'
        ]
    )

    ->name(
        'checkout.process'
    );

    Route::get(
        '/payments/redirect',
        [
            PaymentDemoController::class,
            'redirect'
        ]
    )

    ->name(
        'payments.redirect'
    );

    Route::get(
        '/payments/response',
        [
            PaymentDemoController::class,
            'response'
        ]
    )

    ->name(
        'payments.response'
    );
});

/*
|--------------------------------------------------------------------------
| AREA ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'admin'
])

->prefix('admin')

->name('admin.')

->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [
            AdminDashboardController::class,
            'index'
        ]
    )

    ->name(
        'dashboard'
    );

    /*
    |--------------------------------------------------------------------------
    | CRUDS
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'categories',
        AdminCategoryController::class
    )

    ->except(['show']);

    Route::resource(
        'brands',
        AdminBrandController::class
    )

    ->except(['show']);

    Route::resource(
        'products',
        AdminProductController::class
    )

    ->except(['show']);

    Route::resource(
        'users',
        AdminUserController::class
    )

    ->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | REPORTES
    |--------------------------------------------------------------------------
    */

    Route::prefix(
        'reports'
    )

    ->name(
        'reports.'
    )

    ->group(function () {

        Route::get(
            '/revenue',
            [
                ReportController::class,
                'revenueExcel'
            ]
        )

        ->name(
            'revenue'
        );

        Route::get(
            '/revenue.json',
            [
                ReportController::class,
                'revenueJson'
            ]
        )

        ->name(
            'revenue.json'
        );

        Route::get(
            '/best-sellers',
            [
                ReportController::class,
                'bestSellersExcel'
            ]
        )

        ->name(
            'best'
        );

        Route::get(
            '/products',
            [
                ReportController::class,
                'productsExcel'
            ]
        )

        ->name(
            'products'
        );

        Route::get(
            '/orders',
            [
                ReportController::class,
                'ordersExcel'
            ]
        )

        ->name(
            'orders'
        );

        Route::get(
            '/ventas-productos',
            [
                ReportController::class,
                'salesPerformanceExcel'
            ]
        )

        ->name(
            'ventas'
        );

        Route::get(
            '/inventario-decision',
            [
                ReportController::class,
                'inventoryDecisionExcel'
            ]
        )

        ->name(
            'inventario'
        );

        Route::get(
            '/tendencias',
            [
                ReportController::class,
                'trendsExcel'
            ]
        )

        ->name(
            'tendencias'
        );
    });

    /*
    |--------------------------------------------------------------------------
    | BILLING
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/billing',
        [
            BillingController::class,
            'index'
        ]
    )

    ->name(
        'billing.index'
    );

    Route::post(
        '/billing/lookup',
        [
            BillingController::class,
            'lookup'
        ]
    )

    ->name(
        'billing.lookup'
    );

    Route::post(
        '/billing/pdf',
        [
            BillingController::class,
            'pdf'
        ]
    )

    ->name(
        'billing.pdf'
    );

    /*
    |--------------------------------------------------------------------------
    | ORDENES
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'orders',
        OrderController::class
    )

    ->only([
        'index',
        'show'
    ])

    ->names(
        'orders'
    );

    Route::patch(
        '/orders/{order}/status',
        [
            OrderController::class,
            'updateStatus'
        ]
    )

    ->name(
        'orders.status'
    );
});

/*
|--------------------------------------------------------------------------
| MERCADO PAGO
|--------------------------------------------------------------------------
*/

Route::get(
    '/pagos/checkout',
    [
        MercadoPagoController::class,
        'checkout'
    ]
)

->name(
    'mp.checkout'
);

Route::post(
    '/pagos/crear-preferencia',
    [
        MercadoPagoController::class,
        'createPreference'
    ]
)

->name(
    'mp.preference'
);

Route::get(
    '/pagos/exito',
    [
        MercadoPagoController::class,
        'success'
    ]
)

->name(
    'mp.success'
);

Route::get(
    '/pagos/pendiente',
    [
        MercadoPagoController::class,
        'pending'
    ]
)

->name(
    'mp.pending'
);

Route::get(
    '/pagos/error',
    [
        MercadoPagoController::class,
        'failure'
    ]
)

->name(
    'mp.failure'
);

Route::post(
    '/webhooks/mercadopago',
    [
        MercadoPagoWebhookController::class,
        'handle'
    ]
)

->name(
    'mp.webhook'
);

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';