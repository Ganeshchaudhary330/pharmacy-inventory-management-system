@extends('layouts.app')
@section('title', 'Transaction Log')
@section('page-title', 'Transaction Log Report')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-primary">{{ $transactions->count() }}</h3><small>Total Transactions</small></div></div>
    <div class="col-md-4"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-success">{{ $totalStockIn }}</h3><small>Total Stock In</small></div></div>
    <div class="col-md-4"><div class="card stat-card p-3 text-center"><h3 class="fw-bold text-danger">{{ $totalStockOut }}</h3><small>Total Stock Out</small></div></div>
</div>

<div class="table-container">
    <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
        <select name="type" class="form-select" style="width:130px" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="in" {{ request('type')=='in' ? 'selected' : '' }}>Stock In</option>
            <option value="out" {{ request('type')=='out' ? 'selected' : '' }}>Stock Out</option>
        </select>
        <input type="date" name="date_from" class="form-control" style="width:160px" value="{{ request('date_from') }}">
        <input type="date" name="date_to" class="form-control" style="width:160px" value="{{ request('date_to') }}">
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
        <a href="{{ route('reports.transactions') }}" class="btn btn-secondary">Reset</a>
        <button onclick="window.print()" class="btn btn-primary ms-auto"><i class="fas fa-print me-1"></i>Print</button>
    </form>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>Date</th><th>Medicine</th><th>Type</th><th>Qty</th><th>Unit Price</th><th>Total</th><th>Reference</th><th>User</th></tr></thead>
            <tbody>
                @forelse($transactions as $t)
                <tr>
                    <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                    <td>{{ $t->medicine->name ?? 'N/A' }}</td>
                    <td>{{ $t->type=='in' ? 'IN' : 'OUT' }}</td>
                    <td>{{ $t->quantity }}</td>
                    <td>Rs. {{ number_format($t->unit_price, 2) }}</td>
                    <td>Rs. {{ number_format($t->total_price, 2) }}</td>
                    <td>{{ $t->reference_type ?? 'N/A' }}</td>
                    <td>{{ $t->user->name ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No transactions found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
