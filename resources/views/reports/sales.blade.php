@extends('layouts.app')
@section('title', 'Sales Report')
@section('page-title', 'Sales Report')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-primary">{{ $totalSales }}</h3><small>Total Sales</small></div></div>
    <div class="col-md-3"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-success">Rs. {{ number_format($totalRevenue, 2) }}</h3><small>Total Revenue</small></div></div>
    <div class="col-md-3"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-info">Rs. {{ number_format($averageSale, 2) }}</h3><small>Average Sale</small></div></div>
    <div class="col-md-3"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-warning">Rs. {{ number_format($totalDiscount, 2) }}</h3><small>Total Discount</small></div></div>
</div>

<div class="table-container mb-4">
    <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
        <input type="date" name="date_from" class="form-control" style="width:160px" value="{{ request('date_from') }}">
        <input type="date" name="date_to" class="form-control" style="width:160px" value="{{ request('date_to') }}">
        <select name="payment_method" class="form-select" style="width:150px" onchange="this.form.submit()">
            <option value="">All Methods</option>
            <option value="cash" {{ request('payment_method')=='cash' ? 'selected' : '' }}>Cash</option>
            <option value="card" {{ request('payment_method')=='card' ? 'selected' : '' }}>Card</option>
            <option value="online" {{ request('payment_method')=='online' ? 'selected' : '' }}>Online</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
        <a href="{{ route('reports.sales') }}" class="btn btn-secondary">Reset</a>
        <button onclick="window.print()" class="btn btn-primary ms-auto"><i class="fas fa-print me-1"></i>Print</button>
    </form>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Invoice</th><th>Customer</th><th>Items</th><th>Subtotal</th><th>Discount</th><th>Tax</th><th>Total</th><th>Method</th><th>Date</th></tr></thead>
            <tbody>
                @forelse($sales as $s)
                <tr>
                    <td>{{ $s->invoice_number }}</td>
                    <td>{{ $s->customer_name ?? 'Walk-in' }}</td>
                    <td>{{ $s->saleItems->count() }}</td>
                    <td>Rs. {{ number_format($s->subtotal, 2) }}</td>
                    <td>Rs. {{ number_format($s->discount, 2) }}</td>
                    <td>Rs. {{ number_format($s->tax, 2) }}</td>
                    <td class="fw-bold">Rs. {{ number_format($s->total, 2) }}</td>
                    <td>{{ $s->payment_method }}</td>
                    <td>{{ $s->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No sales found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($dailySales->count() > 0)
<div class="table-container">
    <h5 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2"></i>Daily Sales Summary</h5>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead><tr><th>Date</th><th>Sales Count</th><th>Total Revenue</th></tr></thead>
            <tbody>
                @foreach($dailySales as $d)
                <tr><td>{{ $d->date }}</td><td>{{ $d->count }}</td><td>Rs. {{ number_format($d->total, 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
