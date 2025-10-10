<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        $orders = $query->get();

        $totalRevenue = $orders->sum('total_price');
        $totalOrders = $orders->count();

        $topProducts = DB::table('order_items')
            ->join('vendor_mobiles', 'order_items.product_id', '=', 'vendor_mobiles.id')
            ->join('brands', 'vendor_mobiles.brand_id', '=', 'brands.id')
            ->join('models', 'vendor_mobiles.model_id', '=', 'models.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('vendors', 'order_items.vendor_id', '=', 'vendors.id')
            ->when($request->filled('vendor_id'), function ($q) use ($request) {
                $q->where('order_items.vendor_id', $request->vendor_id);
            })
            ->when($request->filled('start_date') && $request->filled('end_date'), function ($q) use ($request) {
                $q->whereBetween('orders.created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59',
                ]);
            })
            ->select(
                'brands.name as brand_name',
                'models.name as model_name',
                'vendors.name as vendor_name',
                DB::raw('SUM(order_items.quantity) as qty'),
                DB::raw('SUM(order_items.quantity * order_items.price) as revenue')
            )
            ->groupBy('brands.name', 'models.name', 'vendors.name')
            ->orderByDesc('qty')
            ->take(10)
            ->get();

        $vendors = \App\Models\Vendor::select('id', 'name')->get();

        // 👇 AJAX request ho to sirf rows return karo
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.reports.partials.table_rows', compact('topProducts'))->render(),
                'totals' => [
                    'products' => $topProducts->count(),
                    'orders' => $totalOrders,
                    'revenue' => number_format($topProducts->sum(fn($p) => (float) $p->revenue), 2),
                ]
            ]);
        }

        return view('admin.reports.index', compact('totalRevenue', 'totalOrders', 'topProducts', 'vendors'));
    }
}
