<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Sale;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Show inventory report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function inventory(Request $request)
    {
        $query = Medicine::with(['category', 'supplier']);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->lowStock();
                    break;
                case 'out':
                    $query->outOfStock();
                    break;
                case 'active':
                    $query->where('stock_quantity', '>', 0);
                    break;
            }
        }

        $medicines = $query->orderBy('name')->get();

        $totalValue = $medicines->sum(function ($m) {
            return $m->stock_quantity * $m->purchase_price;
        });

        $totalSellingValue = $medicines->sum(function ($m) {
            return $m->stock_quantity * $m->selling_price;
        });

        $categories = \App\Models\Category::all();

        return view('reports.inventory', compact('medicines', 'totalValue', 'totalSellingValue', 'categories'));
    }

    /**
     * Show sales report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function sales(Request $request)
    {
        $query = Sale::with(['user', 'saleItems'])->where('status', 'completed');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $sales = $query->latest()->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total');
        $totalDiscount = $sales->sum('discount');
        $totalTax = $sales->sum('tax');
        $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Daily sales for chart
        $dailySales = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('SUM(total) as total')
        )
        ->where('status', 'completed')
        ->when($request->filled('date_from'), function ($q) use ($request) {
            return $q->whereDate('created_at', '>=', $request->date_from);
        })
        ->when($request->filled('date_to'), function ($q) use ($request) {
            return $q->whereDate('created_at', '<=', $request->date_to);
        })
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return view('reports.sales', compact(
            'sales', 'totalSales', 'totalRevenue', 'totalDiscount',
            'totalTax', 'averageSale', 'dailySales'
        ));
    }

    /**
     * Show low stock report.
     *
     * @return \Illuminate\View\View
     */
    public function lowStock()
    {
        $medicines = Medicine::with(['category', 'supplier'])
            ->where(function ($query) {
                $query->lowStock()
                    ->orWhere->outOfStock();
            })
            ->orderBy('stock_quantity')
            ->get();

        return view('reports.low-stock', compact('medicines'));
    }

    /**
     * Show transaction log report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function transactions(Request $request)
    {
        $query = StockTransaction::with(['medicine', 'user']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->get();

        $totalStockIn = $transactions->where('type', 'in')->sum('quantity');
        $totalStockOut = $transactions->where('type', 'out')->sum('quantity');

        return view('reports.transactions', compact('transactions', 'totalStockIn', 'totalStockOut'));
    }
}
