<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Exports\ArrayExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // ==========================================
    // ESTADOS PAGADOS
    // ==========================================

    protected function paidStatuses(): array
    {
        return [
            'paid',
            'shipped'
        ];
    }

    // ==========================================
    // 1. REPORTE DE VENTAS E INGRESOS
    // ==========================================

    public function revenueExcel()
    {
        $rows = [[
            'Mes',
            'Pedidos',
            'Ingresos Totales',
            'Ticket Promedio',
            'Estado'
        ]];

        $data = DB::table('orders')
            ->selectRaw("
                DATE_FORMAT(created_at,'%Y-%m') as mes,
                COUNT(*) as pedidos,
                SUM(total_price) as ingresos
            ")
            ->whereIn('status', $this->paidStatuses())
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        foreach ($data as $d) {

            $ticket =
                $d->pedidos > 0
                ? $d->ingresos / $d->pedidos
                : 0;

            $estado =
                $ticket >= 50
                ? 'ALTO'
                : 'NORMAL';

            $rows[] = [
                $d->mes,
                $d->pedidos,
                round($d->ingresos, 2),
                round($ticket, 2),
                $estado
            ];
        }

        return $this->downloadExcel(
            'reporte_ventas_ingresos.xlsx',
            $rows
        );
    }

    // ==========================================
    // 2. PRODUCTOS RENTABLES
    // ==========================================

    public function profitableProductsExcel()
    {
        $rows = [[
            'Producto ID',
            'Producto',
            'Categoría',
            'Stock',
            'Cantidad Vendida',
            'Ingresos Generados',
            'Participación %'
        ]];

        $data = DB::table('order_items as oi')
            ->join(
                'products as p',
                'p.id',
                '=',
                'oi.product_id'
            )
            ->leftJoin(
                'categories as c',
                'c.categories_id',
                '=',
                'p.categories_id'
            )
            ->join(
                'orders as o',
                'o.id',
                '=',
                'oi.order_id'
            )
            ->whereIn(
                'o.status',
                $this->paidStatuses()
            )
            ->selectRaw("
                p.id,
                p.name as producto,
                c.name as categoria,
                p.stock,
                SUM(oi.quantity) as vendidos,
                SUM(oi.subtotal) as ingresos
            ")
            ->groupBy(
                'p.id',
                'p.name',
                'c.name',
                'p.stock'
            )
            ->orderByDesc('ingresos')
            ->get();

        $totalIngresos =
            $data->sum('ingresos');

        foreach ($data as $d) {

            $participacion =
                $totalIngresos > 0
                ? ($d->ingresos / $totalIngresos) * 100
                : 0;

            $rows[] = [
                $d->id,
                $d->producto,
                $d->categoria ?? 'Sin categoría',
                $d->stock,
                $d->vendidos,
                round($d->ingresos, 2),
                round($participacion, 2) . '%'
            ];
        }

        return $this->downloadExcel(
            'productos_rentables.xlsx',
            $rows
        );
    }

    // ==========================================
    // 3. INVENTARIO INTELIGENTE
    // ==========================================

    public function inventoryExcel()
    {
        $rows = [[
            'Producto',
            'Stock Actual',
            'Stock Mínimo',
            'Estado',
            'Ventas Históricas',
            'Prioridad'
        ]];

        foreach (Product::all() as $p) {

            $ventas = DB::table('order_items')
                ->where(
                    'product_id',
                    $p->id
                )
                ->sum('quantity');

            $estado =
                $p->stock <= $p->stock_minimo
                ? 'REABASTECER'
                : 'NORMAL';

            $prioridad =
                $ventas >= 50
                ? 'ALTA'
                : ($ventas >= 20
                    ? 'MEDIA'
                    : 'BAJA');

            $rows[] = [
                $p->name,
                $p->stock,
                $p->stock_minimo,
                $estado,
                $ventas,
                $prioridad
            ];
        }

        return $this->downloadExcel(
            'inventario_inteligente.xlsx',
            $rows
        );
    }

    // ==========================================
    // 4. CLIENTES FRECUENTES
    // ==========================================

    public function frequentCustomersExcel()
    {
        $rows = [[
            'Cliente ID',
            'Cliente',
            'Correo',
            'Cantidad Pedidos',
            'Total Gastado',
            'Clasificación'
        ]];

        $data = DB::table('users as u')
            ->join(
                'orders as o',
                'u.id',
                '=',
                'o.user_id'
            )
            ->where(
                'u.role',
                'customer'
            )
            ->whereIn(
                'o.status',
                $this->paidStatuses()
            )
            ->selectRaw("
                u.id,
                u.name,
                u.email,
                COUNT(o.id) as pedidos,
                SUM(o.total_price) as total_gastado
            ")
            ->groupBy(
                'u.id',
                'u.name',
                'u.email'
            )
            ->orderByDesc('pedidos')
            ->get();

        foreach ($data as $d) {

            $clasificacion =
                $d->pedidos >= 10
                ? 'VIP'
                : ($d->pedidos >= 5
                    ? 'FRECUENTE'
                    : 'OCASIONAL');

            $rows[] = [
                $d->id,
                $d->name,
                $d->email,
                $d->pedidos,
                round($d->total_gastado, 2),
                $clasificacion
            ];
        }

        return $this->downloadExcel(
            'clientes_frecuentes.xlsx',
            $rows
        );
    }

    // ==========================================
    // JSON DASHBOARD INGRESOS
    // ==========================================

    public function revenueJson(Request $request)
    {
        $to = Carbon::now();

        $from = (clone $to)
            ->subMonths(12);

        $rows = DB::table('orders')
            ->selectRaw("
                DATE_FORMAT(created_at,'%Y-%m') as label,
                SUM(total_price) as revenue
            ")
            ->whereBetween(
                'created_at',
                [$from, $to]
            )
            ->whereIn(
                'status',
                $this->paidStatuses()
            )
            ->groupBy('label')
            ->orderBy('label')
            ->get();

        return response()->json([
            'ok' => true,
            'labels' => $rows->pluck('label'),
            'data' => $rows->pluck('revenue')
        ]);
    }

    // ==========================================
    // DESCARGAR EXCEL
    // ==========================================

    protected function downloadExcel(
        string $filename,
        array $rows
    ) {
        return Excel::download(
            new ArrayExport($rows),
            $filename
        );
    }
}