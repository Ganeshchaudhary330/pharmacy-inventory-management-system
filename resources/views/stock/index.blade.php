@extends('layouts.app')
@section('title', 'Stock Transactions')
@section('page-title', 'Stock Management')

@section('content')
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('stock.create-in') }}" class="btn btn-success"><i class="fas fa-plus me-1"></i>Stock In</a>
    <a href="{{ route('stock.create-out') }}" class="btn btn-danger"><i class="fas fa-minus me-1"></i>Stock Out</a>
</div>

<div class="table-container">
    <form method="GET" class="d-flex flex-wrap gap-2 mb-3">
        <select name="type" class="form-select" style="width:130px" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="in" {{ request('type')=='in' ? 'selected' : '' }}>Stock In</option>
            <option value="out" {{ request('type')=='out' ? 'selected' : '' }}>Stock Out</option>
        </select>
        <select name="medicine_id" class="form-select" style="width:200px" onchange="this.form.submit()">
            <option value="">All Medicines</option>
            @foreach($medicines as $m)
            <option value="{{ $m->id }}" {{ request('medicine_id')==$m->id ? 'selected' : '' }}>{{ $m->name }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" class="form-control" style="width:160px" value="{{ request('date_from') }}" placeholder="From">
        <input type="date" name="date_to" class="form-control" style="width:160px" value="{{ request('date_to') }}" placeholder="To">
        <button class="btn btn-primary" type="submit"><i class="fas fa-filter me-1"></i>Filter</button>
        <a href="{{ route('stock.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr><th>Date</th><th>Medicine</th><th>Type</th><th>Qty</th><th>Unit Price</th><th>Total</th><th>Reference</th><th>User</th><th>Notes</th></tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                <tr>
                    <td>{{ $t->created_at->format('d M Y H:i') }}</td>
                    <td><a href="{{ route('medicines.show', $t->medicine) }}" class="text-decoration-none">{{ $t->medicine->name ?? 'N/A' }}</a></td>
                    <td>{{ $t->type=='in' ? 'IN' : 'OUT' }}</td>
                    <td>{{ $t->quantity }}</td>
                    <td>Rs. {{ number_format($t->unit_price, 2) }}</td>
                    <td>Rs. {{ number_format($t->total_price, 2) }}</td>
                    <td>{{ $t->reference_type ?? 'N/A' }}</td>
                    <td>{{ $t->user->name ?? 'N/A' }}</td>
                    <td>{{ Str::limit($t->notes, 30) ?? 'N/A' }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No transactions found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end">{{ $transactions->links() }}</div>
</div>
@endsection
