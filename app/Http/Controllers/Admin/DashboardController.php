<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('es');

        // ==========================================
        // FILTRO DE CATEGORÍA
        // ==========================================

        $categoryId =
            (int) $request->query(
                'category_id',
                0
            );

        // ==========================================
        // ÚLTIMOS 12 MESES
        // ==========================================

        $now =
            Carbon::now()
            ->startOfMonth();

        $start =
            (clone $now)
            ->subMonths(11);

        $end =
            (clone $now)
            ->addMonth();

        $months =
            collect(range(0, 11))
            ->map(
                fn($i) =>
                (clone $start)
                ->addMonths($i)
            );

        $labels = $months
            ->map(
                fn($m) =>
                ucfirst(
                    $m->isoFormat('MMM')
                ) . ' ' .
                $m->year
            )
            ->values()
            ->all();

        // ==========================================
        // ESTADOS PAGADOS
        // ==========================================

        $paidStatuses = [
            'paid',
            'shipped'
        ];

        // ==========================================
        // INGRESOS POR MES
        // ==========================================

        $sumQuery =
            DB::table(
                'orders as o'
            )

            ->selectRaw("
                DATE_FORMAT(
                    o.created_at,
                    '%Y-%m'
                ) as ym,

                SUM(
                    o.total_price
                ) as total
            ")

            ->whereBetween(
                'o.created_at',
                [
                    $start,
                    $end
                ]
            )

            ->whereIn(
                'o.status',
                $paidStatuses
            );

        // filtro categoría

        if ($categoryId > 0) {

            $sumQuery
                ->join(
                    'order_items as oi',
                    'oi.order_id',
                    '=',
                    'o.id'
                )

                ->join(
                    'products as p',
                    'p.id',
                    '=',
                    'oi.product_id'
                )

                ->where(
                    'p.categories_id',
                    $categoryId
                );
        }

        $rows = $sumQuery
            ->groupBy('ym')
            ->pluck(
                'total',
                'ym'
            );

        $revenue =
            $months->map(
                function ($m) use ($rows) {

                    $key =
                        $m->format(
                            'Y-m'
                        );

                    return round(
                        (float)
                        ($rows[$key] ?? 0),
                        2
                    );
                }
            )
            ->values()
            ->all();

        // ==========================================
        // CARDS RESUMEN
        // ==========================================

        $stats = [

            'revenue' => round(
                array_sum($revenue),
                2
            ),

            'orders' =>
                Order::whereIn(
                    'status',
                    $paidStatuses
                )->count(),

            'products' =>
                Product::count(),

            'customers' =>
                User::where(
                    'role',
                    'customer'
                )->count(),
        ];

        // ==========================================
        // CATEGORÍAS
        // ==========================================

        $chips =
            Category::query()

            ->select('name')

            ->orderBy('name')

            ->limit(12)

            ->pluck('name')

            ->map(
                fn($n) => [
                    'i' => 'bi-dot',
                    't' => $n
                ]
            )

            ->values()

            ->all();

        $categories =
            Category::select([
                'categories_id as id',
                'name'
            ])

            ->orderBy(
                'name',
                'asc'
            )

            ->get();

        // ==========================================
        // TOP 5 PRODUCTOS
        // ==========================================

        $best =
            DB::table(
                'order_items as oi'
            )

            ->join(
                'orders as o',
                'o.id',
                '=',
                'oi.order_id'
            )

            ->join(
                'products as p',
                'p.id',
                '=',
                'oi.product_id'
            )

            ->whereBetween(
                'o.created_at',
                [
                    $start,
                    $end
                ]
            )

            ->whereIn(
                'o.status',
                $paidStatuses
            )

            ->when(
                $categoryId > 0,

                fn($q) =>
                $q->where(
                    'p.categories_id',
                    $categoryId
                )
            )

            ->select([
                'p.id',
                'p.name',
                'p.image',

                DB::raw("
                    SUM(
                        oi.quantity
                    ) as qty_sold
                "),

                DB::raw("
                    SUM(
                        oi.subtotal
                    ) as amount
                "),
            ])

            ->groupBy(
                'p.id',
                'p.name',
                'p.image'
            )

            ->orderByDesc(
                'qty_sold'
            )

            ->limit(5)

            ->get()

            ->map(
                function ($row) {

                    $img =
                        $row->image;

                    if (
                        $img &&
                        !Str::startsWith(
                            $img,
                            [
                                'http://',
                                'https://',
                                '//'
                            ]
                        )
                    ) {

                        $img =
                            Storage::disk(
                                'public'
                            )->exists($img)

                            ?

                            Storage::url($img)

                            :

                            asset(
                                'images/no-image.png'
                            );
                    }

                    if (!$img) {

                        $img =
                            'https://via.placeholder.com/56';
                    }

                    return [

                        'id' =>
                            $row->id,

                        'name' =>
                            $row->name,

                        'orders' =>
                            (int)
                            $row->qty_sold,

                        'total' =>
                            round(
                                (float)
                                $row->amount,
                                2
                            ),

                        'img' =>
                            $img,
                    ];
                }
            )

            ->toArray();

        // ==========================================
        // VIEW
        // ==========================================

        return view(
            'dashboard',
            [

                'labels' =>
                    $labels,

                'revenue' =>
                    $revenue,

                'stats' =>
                    $stats,

                'chips' =>
                    $chips,

                'best' =>
                    $best,

                'categories' =>
                    $categories,

                'categoryId' =>
                    $categoryId,
            ]
        );
    }
}