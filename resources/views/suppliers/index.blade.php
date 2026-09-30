@extends('layouts.app')
@section('title', 'Suppliers')
@section('page-title', 'Suppliers')

@section('content')
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" class="d-flex gap-2">
            <div class="input-group" style="width:300px">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search suppliers..." value="{{ request('search') }}">
            </div>
            <select name="status" class="form-select" style="width:150px" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @if(request()->anyFilled(['search','status']))<a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Clear</a>@endif
        </form>
        <a href="{{ route('suppliers.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Supplier</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr><th>#</th><th>Name</th><th>Contact Person</th><th>Phone</th><th>Email</th><th>Medicines</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td class="fw-bold">{{ $s->name }}</td>
                    <td>{{ $s->contact_person ?? 'N/A' }}</td>
                    <td>{{ $s->phone ?? 'N/A' }}</td>
                    <td>{{ $s->email ?? 'N/A' }}</td>
                    <td>{{ $s->medicines_count }}</td>
                    <td>{{ $s->status=='active' ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('suppliers.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No suppliers found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end">{{ $suppliers->links() }}</div>
</div>
@endsection
