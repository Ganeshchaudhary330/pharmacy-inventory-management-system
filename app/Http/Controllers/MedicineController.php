<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    /**
     * Display a listing of medicines.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Medicine::with(['category', 'supplier']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('manufacturer', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by stock level
        if ($request->filled('stock_filter')) {
            switch ($request->stock_filter) {
                case 'low':
                    $query->lowStock();
                    break;
                case 'out':
                    $query->outOfStock();
                    break;
                case 'expiring':
                    $query->expiringSoon();
                    break;
            }
        }

        $medicines = $query->orderBy('name')->get();

        $categories = Category::active()->orderBy('name')->get();
        $suppliers  = Supplier::active()->orderBy('name')->get();

        return view('medicines.index', compact('medicines', 'categories', 'suppliers'));
    }

    /**
     * Show the form for creating a new medicine.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $suppliers  = Supplier::active()->orderBy('name')->get();

        return view('medicines.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created medicine in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:150',
            'generic_name'       => 'nullable|string|max:150',
            'category_id'        => 'required|exists:categories,id',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'batch_number'       => 'nullable|string|max:50',
            'sku'                => 'nullable|string|max:50|unique:medicines',
            'purchase_price'     => 'required|numeric|min:0',
            'selling_price'      => 'required|numeric|min:0|gte:purchase_price',
            'stock_quantity'     => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:1',
            'expiry_date'        => 'nullable|date|after:today',
            'manufacturer'       => 'nullable|string|max:150',
            'description'        => 'nullable|string|max:1000',
            'status'             => 'required|in:active,inactive',
        ]);

        Medicine::create($validated);

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine added successfully.');
    }

    /**
     * Display the specified medicine.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\View\View
     */
    public function show(Medicine $medicine)
    {
        $medicine->load(['category', 'supplier', 'stockTransactions' => function ($query) {
            $query->latest()->take(20);
        }, 'stockTransactions.user']);

        return view('medicines.show', compact('medicine'));
    }

    /**
     * Show the form for editing the specified medicine.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\View\View
     */
    public function edit(Medicine $medicine)
    {
        $categories = Category::active()->orderBy('name')->get();
        $suppliers  = Supplier::active()->orderBy('name')->get();

        return view('medicines.edit', compact('medicine', 'categories', 'suppliers'));
    }

    /**
     * Update the specified medicine in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Medicine $medicine)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:150',
            'generic_name'       => 'nullable|string|max:150',
            'category_id'        => 'required|exists:categories,id',
            'supplier_id'        => 'nullable|exists:suppliers,id',
            'batch_number'       => 'nullable|string|max:50',
            'sku'                => ['nullable', 'string', 'max:50', Rule::unique('medicines')->ignore($medicine->id)],
            'purchase_price'     => 'required|numeric|min:0',
            'selling_price'      => 'required|numeric|min:0|gte:purchase_price',
            'stock_quantity'     => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:1',
            'expiry_date'        => 'nullable|date|after:today',
            'manufacturer'       => 'nullable|string|max:150',
            'description'        => 'nullable|string|max:1000',
            'status'             => 'required|in:active,inactive',
        ]);

        $medicine->update($validated);

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine updated successfully.');
    }

    /**
     * Remove the specified medicine from storage.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Medicine $medicine)
    {
        // Check if medicine has sale items
        if ($medicine->saleItems()->count() > 0) {
            return redirect()->route('medicines.index')
                ->with('error', 'Cannot delete medicine with sales history.');
        }

        $medicine->stockTransactions()->delete();
        $medicine->delete();

        return redirect()->route('medicines.index')
            ->with('success', 'Medicine deleted successfully.');
    }

    /**
     * Get medicines for AJAX select (used in sales).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMedicinesJson(Request $request)
    {
        $query = Medicine::active()->with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $medicines = $query->orderBy('name')->get();

        return response()->json($medicines);
    }
}
