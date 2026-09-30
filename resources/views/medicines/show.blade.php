@extends('layouts.app')
@section('title', $medicine->name)
@section('page-title', 'Medicine Details')

@section('content')
<div class="row g-4">
    <div class="col-md-4">
        <div class="table-container">
            <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>Medicine Info</h5>
            <table class="table table-sm">
                <tr><th>Name</th><td>{{ $medicine->name }}</td></tr>
                <tr><th>Generic Name</th><td>{{ $medicine->generic_name ?? 'N/A' }}</td></tr>
                <tr><th>Category</th><td>{{ $medicine->category->name ?? 'N/A' }}</td></tr>
                <tr><th>Supplier</th><td>{{ $medicine->supplier->name ?? 'N/A' }}</td></tr>
                <tr><th>Manufacturer</th><td>{{ $medicine->manufacturer ?? 'N/A' }}</td></tr>
                <tr><th>Batch Number</th><td>{{ $medicine->batch_number ?? 'N/A' }}</td></tr>
                <tr><th>SKU</th><td>{{ $medicine->sku ?? 'N/A' }}</td></tr>
                <tr><th>Expiry Date</th><td class="{{ $medicine->isExpired() ? 'text-danger fw-bold' : '' }}">{{ $medicine->expiry_date ? $medicine->expiry_date->format('d M Y') : 'N/A' }}</td></tr>
                <tr><th>Status</th><td>{{ $medicine->isExpired() ? 'Expired' : 'Active' }}</td></tr>
            </table>
            <a href="{{ route('medicines.edit', $medicine) }}" class="btn btn-warning w-100"><i class="fas fa-edit me-1"></i>Edit Medicine</a>
        </div>
    </div>
    <div class="col-md-8">
        <div class="table-container">
            <h5 class="fw-bold mb-3"><i class="fas fa-history me-2"></i>Stock Transaction History</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                            <th>Reference</th>
                            <th>User</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($medicine->stockTransactions as $t)
                        <tr>
                            <td>{{ $t->created_at->format('d M H:i') }}</td>
                            <td>{{ $t->type == 'in' ? 'IN' : 'OUT' }}</td>
                            <td>{{ $t->quantity }}</td>
                            <td>Rs. {{ number_format($t->unit_price, 2) }}</td>
                            <td>Rs. {{ number_format($t->total_price, 2) }}</td>
                            <td>{{ $t->reference_type ?? 'N/A' }}</td>
                            <td>{{ $t->user->name ?? 'N/A' }}</td>
                            <td>{{ Str::limit($t->notes, 30) ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted">No transactions yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
