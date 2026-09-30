<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with summary statistics.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Total counts
        $totalMedicines  = Medicine::count();
        $totalCategories = Category::count();
        $totalSuppliers  = Supplier::count();
        $totalSales      = Sale::count();

        // Stock statistics
        $lowStockMedicines  = Medicine::lowStock()->count();
        $outOfStockMedicines = Medicine::outOfStock()->count();
        $expiringSoonMedicines = Medicine::expiringSoon()->count();

        // Recent transactions (last 10)
        $recentTransactions = StockTransaction::with(['medicine', 'user'])
            ->latest()
            ->take(10)
            ->get();

        // Recent sales (last 10)
        $recentSales = Sale::with(['user', 'saleItems'])
            ->latest()
            ->take(10)
            ->get();

        // Low stock medicines list
        $lowStockList = Medicine::with('category')
            ->lowStock()
            ->orderBy('stock_quantity')
            ->take(10)
            ->get();

        // Monthly sales chart data (last 6 months)
        $monthlySales = Sale::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total) as total_sales')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->where('status', 'completed')
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        // Today's sales
        $todaySales = Sale::whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total');

        // This month's sales
        $monthSales = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('total');

        return view('dashboard', compact(
            'totalMedicines',
            'totalCategories',
            'totalSuppliers',
            'totalSales',
            'lowStockMedicines',
            'outOfStockMedicines',
            'expiringSoonMedicines',
            'recentTransactions',
            'recentSales',
            'lowStockList',
            'monthlySales',
            'todaySales',
            'monthSales'
        ));
    }
}
