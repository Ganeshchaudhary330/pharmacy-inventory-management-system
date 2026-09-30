@extends('layouts.app')

@section('title', 'Dashboard - Pharmacy Inventory')
@section('page-title', 'Dashboard')

@section('content')
<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Total Medicines</div>
                        <div class="h3 mb-0 fw-bold">{{ $totalMedicines }}</div>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-start border-warning border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Low Stock</div>
                        <div class="h3 mb-0 fw-bold text-warning">{{ $lowStockMedicines }}</div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Expired</div>
                        <div class="h3 mb-0 fw-bold text-danger">{{ $expiredMedicines }}</div>
                    </div>
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card stat-card border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase fw-bold">Today Sales</div>
                        <div class="h3 mb-0 fw-bold text-success">${{ number_format($todaySales, 2) }}</div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card stat-card">
            <div class="card-header bg-white border-0">
                <h5 class="card-title fw-bold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Monthly Sales (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card stat-card">
            <div class="card-header bg-white border-0">
                <h5 class="card-title fw-bold mb-0"><i class="fas fa-bell me-2 text-warning"></i>Alerts</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Low Stock Items</span>
                        <span class="badge bg-warning">{{ $lowStockMedicines }}</span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-warning" style="width: {{ $totalMedicines > 0 ? ($lowStockMedicines/$totalMedicines)*100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Out of Stock</span>
                        <span class="badge bg-danger">{{ $outOfStockMedicines }}</span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-danger" style="width: {{ $totalMedicines > 0 ? ($outOfStockMedicines/$totalMedicines)*100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Expired Medicines</span>
                        <span class="badge bg-danger">{{ $expiredMedicines }}</span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-danger" style="width: {{ $totalMedicines > 0 ? ($expiredMedicines/$totalMedicines)*100 : 0 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Expiring Soon (30 days)</span>
                        <span class="badge bg-info">{{ $expiringSoonMedicines }}</span>
                    </div>
                    <div class="progress" style="height:8px">
                        <div class="progress-bar bg-info" style="width: {{ $totalMedicines > 0 ? ($expiringSoonMedicines/$totalMedicines)*100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-6">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Low Stock Medicines</h5>
                <a href="{{ route('reports.low-stock') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Threshold</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockList as $medicine)
                        <tr>
                            <td>{{ $medicine->name }}</td>
                            <td>{{ $medicine->category->name ?? 'N/A' }}</td>
                            <td class="fw-bold text-danger">{{ $medicine->stock_quantity }}</td>
                            <td>{{ $medicine->low_stock_threshold }}</td>
                            <td>
                                @if($medicine->stock_quantity <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @else
                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No low stock medicines</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-calendar-times me-2 text-danger"></i>Expired Medicines</h5>
                <a href="{{ route('reports.expired') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th>Category</th>
                            <th>Stock</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expiredList as $medicine)
                        <tr>
                            <td>{{ $medicine->name }}</td>
                            <td>{{ $medicine->category->name ?? 'N/A' }}</td>
                            <td>{{ $medicine->stock_quantity }}</td>
                            <td class="text-danger">{{ $medicine->expiry_date->format('d M Y') }}</td>
                            <td><span class="badge bg-danger">Expired</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No expired medicines</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-2">
    <div class="col-xl-6">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-history me-2 text-info"></i>Recent Transactions</h5>
                <a href="{{ route('stock.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Medicine</th>
                            <th>Type</th>
                            <th>Qty</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->format('d M H:i') }}</td>
                            <td>{{ $transaction->medicine->name ?? 'N/A' }}</td>
                            <td>
                                @if($transaction->type == 'in')
                                    <span class="badge bg-success">IN</span>
                                @else
                                    <span class="badge bg-danger">OUT</span>
                                @endif
                            </td>
                            <td>{{ $transaction->quantity }}</td>
                            <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No recent transactions</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="fas fa-shopping-cart me-2 text-success"></i>Recent Sales</h5>
                <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                        <tr>
                            <td><a href="{{ route('sales.show', $sale) }}" class="text-decoration-none">{{ $sale->invoice_number }}</a></td>
                            <td>{{ $sale->customer_name ?? 'Walk-in' }}</td>
                            <td>{{ $sale->saleItems->count() }}</td>
                            <td class="fw-bold">${{ number_format($sale->total, 2) }}</td>
                            <td>{{ $sale->created_at->format('d M H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No recent sales</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Sales Chart
    var ctx = document.getElementById('salesChart').getContext('2d');
    var monthlySales = @json($monthlySales);

    var labels = monthlySales.map(function(item) {
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[item.month - 1] + ' ' + item.year;
    });

    var data = monthlySales.map(function(item) {
        return item.total_sales;
    });

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sales ($)',
                data: data,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: function(value) { return '$' + value; } } }
            }
        }
    });
});
</script>
@endpush
