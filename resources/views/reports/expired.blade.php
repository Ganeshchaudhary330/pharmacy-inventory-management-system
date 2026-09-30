@extends('layouts.app')
@section('title', 'Expired Medicines Report')
@section('page-title', 'Expired Medicines Report')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="card stat-card p-3 text-center border-danger"><h3 class="fw-bold text-danger">{{ $medicines->count() }}</h3><small>Expired Items</small></div></div>
    <div class="col-md-6"><div class="card stat-card p-3 text-center border-danger"><h3 class="fw-bold text-danger">Rs. {{ number_format($totalLoss, 2) }}</h3><small>Total Loss Value</small></div></div>
</div>
<div class="table-container">
    <button onclick="window.print()" class="btn btn-primary mb-3"><i class="fas fa-print me-1"></i>Print Report</button>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>#</th><th>Medicine</th><th>Category</th><th>Batch</th><th>Stock Qty</th><th>Purchase Price</th><th>Loss Value</th><th>Expiry Date</th></tr></thead>
            <tbody>
                @forelse($medicines as $m)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $m->name }}</td>
                    <td>{{ $m->category->name ?? 'N/A' }}</td>
                    <td>{{ $m->batch_number ?? 'N/A' }}</td>
                    <td>{{ $m->stock_quantity }}</td>
                    <td>Rs. {{ number_format($m->purchase_price, 2) }}</td>
                    <td class="text-danger fw-bold">Rs. {{ number_format($m->stock_quantity * $m->purchase_price, 2) }}</td>
                    <td class="text-danger">{{ $m->expiry_date->format('d M Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No expired medicines found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
