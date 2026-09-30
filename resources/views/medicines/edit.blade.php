@extends('layouts.app')
@section('title', 'Edit Medicine')
@section('page-title', 'Edit Medicine')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="table-container">
            <form action="{{ route('medicines.update', $medicine) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Medicine Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $medicine->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Generic Name</label>
                        <input type="text" name="generic_name" class="form-control" value="{{ old('generic_name', $medicine->generic_name) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $medicine->category_id)==$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Supplier</label>
                        <select name="supplier_id" class="form-select">
                            <option value="">Select</option>
                            @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id', $medicine->supplier_id)==$sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Manufacturer</label>
                        <input type="text" name="manufacturer" class="form-control" value="{{ old('manufacturer', $medicine->manufacturer) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $medicine->sku) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Batch Number</label>
                        <input type="text" name="batch_number" class="form-control" value="{{ old('batch_number', $medicine->batch_number) }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Expiry Date</label>
                        <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', $medicine->expiry_date ? $medicine->expiry_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Purchase Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control" value="{{ old('purchase_price', $medicine->purchase_price) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Selling Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="selling_price" class="form-control" value="{{ old('selling_price', $medicine->selling_price) }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Stock Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', $medicine->stock_quantity) }}" required>
                        @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Low Stock Threshold <span class="text-danger">*</span></label>
                        <input type="number" name="low_stock_threshold" class="form-control @error('low_stock_threshold') is-invalid @enderror" value="{{ old('low_stock_threshold', $medicine->low_stock_threshold) }}" required>
                        @error('low_stock_threshold')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $medicine->status)=='active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $medicine->status)=='inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <textarea name="description" class="form-control" rows="2" placeholder="Description">{{ old('description', $medicine->description) }}</textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                    <a href="{{ route('medicines.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
