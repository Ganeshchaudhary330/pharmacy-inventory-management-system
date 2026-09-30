@extends('layouts.app')
@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <form method="GET" class="d-flex gap-2">
                <div class="input-group" style="width:300px">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search categories..." value="{{ request('search') }}">
                </div>
                <select name="status" class="form-select" style="width:150px" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @if(request()->anyFilled(['search','status']))
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Clear</a>
                @endif
            </form>
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Category</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Medicines</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr>
                    <td>{{ $category->id }}</td>
                    <td class="fw-bold">{{ $category->name }}</td>
                    <td>{{ Str::limit($category->description, 50) ?? 'N/A' }}</td>
                    <td>{{ $category->medicines_count }}</td>
                    <td>{{ $category->status == 'active' ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $category->created_at->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No categories found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-end">{{ $categories->links() }}</div>
</div>
@endsection
