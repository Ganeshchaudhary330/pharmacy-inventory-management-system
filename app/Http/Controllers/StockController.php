<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Display a listing of stock transactions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = StockTransaction::with(['medicine', 'user']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by medicine
        if ($request->filled('medicine_id')) {
            $query->where('medicine_id', $request->medicine_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()
            ->paginate(15)
            ->withQueryString();

        $medicines = Medicine::active()->orderBy('name')->get();

        return view('stock.index', compact('transactions', 'medicines'));
    }

    /**
     * Show the form for stock in.
     *
     * @return \Illuminate\View\View
     */
    public function createStockIn()
    {
        $medicines = Medicine::active()->orderBy('name')->get();
        return view('stock.stock-in', compact('medicines'));
    }

    /**
     * Process stock in.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stockIn(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity'    => 'required|integer|min:1',
            'unit_price'  => 'required|numeric|min:0',
            'notes'       => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            // Update medicine stock
            $medicine = Medicine::findOrFail($validated['medicine_id']);
            $medicine->increment('stock_quantity', $validated['quantity']);

            // Update purchase price if provided
            if ($validated['unit_price'] > 0) {
                $medicine->update(['purchase_price' => $validated['unit_price']]);
            }

            // Create transaction record
            StockTransaction::create([
                'medicine_id' => $validated['medicine_id'],
                'user_id'     => Auth::id(),
                'type'        => 'in',
                'quantity'    => $validated['quantity'],
                'unit_price'  => $validated['unit_price'],
                'total_price' => $validated['quantity'] * $validated['unit_price'],
                'reference_type' => 'purchase',
                'notes'       => $validated['notes'],
            ]);
        });

        return redirect()->route('stock.index')
            ->with('success', 'Stock added successfully.');
    }

    /**
     * Show the form for stock out.
     *
     * @return \Illuminate\View\View
     */
    public function createStockOut()
    {
        $medicines = Medicine::active()->where('stock_quantity', '>', 0)->orderBy('name')->get();
        return view('stock.stock-out', compact('medicines'));
    }

    /**
     * Process stock out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function stockOut(Request $request)
    {
        $validated = $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity'    => 'required|integer|min:1',
            'unit_price'  => 'required|numeric|min:0',
            'notes'       => 'nullable|string|max:500',
        ]);

        $medicine = Medicine::findOrFail($validated['medicine_id']);

        // Check if sufficient stock is available
        if ($medicine->stock_quantity < $validated['quantity']) {
            return back()
                ->withInput()
                ->withErrors(['quantity' => 'Insufficient stock. Available: ' . $medicine->stock_quantity]);
        }

        DB::transaction(function () use ($validated, $medicine) {
            // Update medicine stock
            $medicine->decrement('stock_quantity', $validated['quantity']);

            // Create transaction record
            StockTransaction::create([
                'medicine_id' => $validated['medicine_id'],
                'user_id'     => Auth::id(),
                'type'        => 'out',
                'quantity'    => $validated['quantity'],
                'unit_price'  => $validated['unit_price'],
                'total_price' => $validated['quantity'] * $validated['unit_price'],
                'reference_type' => 'adjustment',
                'notes'       => $validated['notes'],
            ]);
        });

        return redirect()->route('stock.index')
            ->with('success', 'Stock removed successfully.');
    }
}
