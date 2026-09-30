<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'saleItems']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sales = $query->latest()->paginate(15)->withQueryString();

        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new sale.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $medicines = Medicine::active()
            ->where('stock_quantity', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('medicines'));
    }

    /**
     * Store a newly created sale in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'    => 'nullable|string|max:150',
            'customer_phone'   => 'nullable|string|max:20',
            'customer_email'   => 'nullable|email|max:100',
            'payment_method'   => 'required|in:cash,card,online',
            'sale_type'        => 'required|in:cash,credit',
            'amount_paid'      => 'required|numeric|min:0',
            'due_date'         => 'nullable|date|after_or_equal:today',
            'discount'         => 'required|numeric|min:0',
            'tax'              => 'required|numeric|min:0',
            'notes'            => 'nullable|string|max:500',
            'items'            => 'required|json',
        ]);

        $items = json_decode($validated['items'], true);

        if (empty($items)) {
            return back()->withErrors(['items' => 'Please add at least one item to the sale.']);
        }

        $saleType = $validated['sale_type'];

        try {
            DB::transaction(function () use ($validated, $items, $saleType) {
                $subtotal = 0;
                $saleItems = [];
                $medicineIds = array_column($items, 'medicine_id');
                $medicines = Medicine::whereIn('id', $medicineIds)->get()->keyBy('id');

                foreach ($items as $item) {
                    $medicine = $medicines->get($item['medicine_id']);

                    if (!$medicine) {
                        throw new \Exception('Medicine not found.');
                    }

                    if ($medicine->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$medicine->name}");
                    }

                    $itemTotal = $medicine->selling_price * $item['quantity'];
                    $subtotal += $itemTotal;

                    $saleItems[] = [
                        'medicine_id' => $medicine->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $medicine->selling_price,
                        'total_price' => $itemTotal,
                    ];
                }

                $fixedDiscount = (float) ($validated['discount'] ?? 0);
                $percentageDiscount = (float) ($validated['discount_percent'] ?? 0);
                $discount = min($fixedDiscount + ($subtotal * $percentageDiscount / 100), $subtotal);
                $tax = $validated['tax'];
                $total = $subtotal - $discount + $tax;
                $amountPaid = $validated['amount_paid'];
                $changeAmount = max(0, $amountPaid - $total);

                $dueAmount = 0;
                $paidAt = null;
                if ($saleType == 'credit') {
                    $dueAmount = $total - $amountPaid;
                    if ($dueAmount <= 0) {
                        $dueAmount = 0;
                    }
                }

                $sale = Sale::create([
                    'invoice_number' => Sale::generateInvoiceNumber(),
                    'user_id' => Auth::id(),
                    'customer_name' => $validated['customer_name'] ?? null,
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'customer_email' => $validated['customer_email'] ?? null,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'discount' => $discount,
                    'total' => $total,
                    'amount_paid' => $amountPaid,
                    'change_amount' => $changeAmount,
                    'payment_method' => $validated['payment_method'],
                    'sale_type' => $saleType,
                    'due_amount' => $dueAmount,
                    'due_date' => $validated['due_date'] ?? null,
                    'paid_at' => $paidAt,
                    'status' => 'completed',
                    'notes' => $validated['notes'] ?? null,
                ]);

                $now = now();
                $saleItemRows = [];
                $stockTransactionRows = [];
                $stockUpdates = [];

                foreach ($saleItems as $item) {
                    $saleItemRows[] = [
                        'sale_id' => $sale->id,
                        'medicine_id' => $item['medicine_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $stockTransactionRows[] = [
                        'medicine_id' => $item['medicine_id'],
                        'user_id' => Auth::id(),
                        'type' => 'out',
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_price'],
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'notes' => 'Sale #' . $sale->invoice_number,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if (!isset($stockUpdates[$item['medicine_id']])) {
                        $stockUpdates[$item['medicine_id']] = 0;
                    }

                    $stockUpdates[$item['medicine_id']] += $item['quantity'];
                }

                DB::table('sale_items')->insert($saleItemRows);
                DB::table('stock_transactions')->insert($stockTransactionRows);

                foreach ($stockUpdates as $medicineId => $quantity) {
                    DB::table('medicines')
                        ->where('id', $medicineId)
                        ->decrement('stock_quantity', $quantity);
                }
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()->route('sales.index')->with('success', 'Sale completed successfully.');
    }

    /**
     * Show the form for editing the specified sale.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\View\View
     */
    public function edit(Sale $sale)
    {
        $sale->load(['user', 'saleItems.medicine.category']);

        $medicines = Medicine::active()
            ->with('category')
            ->orderBy('name')
            ->get();

        $saleItems = $sale->saleItems->map(function ($item) {
            return [
                'medicine_id' => $item->medicine_id,
                'name' => $item->medicine->name,
                'unit_price' => (float) $item->unit_price,
                'quantity' => (int) $item->quantity,
            ];
        })->values()->all();

        return view('sales.edit', compact('sale', 'medicines', 'saleItems'));
    }

    /**
     * Update the specified sale.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_name'    => 'nullable|string|max:150',
            'customer_phone'   => 'nullable|string|max:20',
            'customer_email'   => 'nullable|email|max:100',
            'payment_method'   => 'required|in:cash,card,online',
            'sale_type'        => 'required|in:cash,credit',
            'amount_paid'      => 'required|numeric|min:0',
            'due_date'         => 'nullable|date|after_or_equal:today',
            'discount'         => 'required|numeric|min:0',
            'tax'              => 'required|numeric|min:0',
            'notes'            => 'nullable|string|max:500',
            'items'            => 'required|json',
        ]);

        $items = json_decode($validated['items'], true);

        if (empty($items)) {
            return back()->withErrors(['items' => 'Please add at least one item to the sale.']);
        }

        $saleType = $validated['sale_type'];

        try {
            DB::transaction(function () use ($validated, $items, $sale, $saleType) {
                foreach ($sale->saleItems as $item) {
                    Medicine::where('id', $item->medicine_id)
                        ->increment('stock_quantity', $item->quantity);
                }

                $sale->saleItems()->delete();
                DB::table('stock_transactions')
                    ->where('reference_type', 'sale')
                    ->where('reference_id', $sale->id)
                    ->delete();

                $subtotal = 0;
                $saleItemRows = [];
                $stockTransactionRows = [];
                $stockUpdates = [];
                $medicineIds = array_column($items, 'medicine_id');
                $medicines = Medicine::whereIn('id', $medicineIds)->get()->keyBy('id');

                foreach ($items as $item) {
                    $medicine = $medicines->get($item['medicine_id']);

                    if (!$medicine) {
                        throw new \Exception('Medicine not found.');
                    }

                    if ($medicine->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$medicine->name}");
                    }

                    $itemTotal = $medicine->selling_price * $item['quantity'];
                    $subtotal += $itemTotal;

                    $saleItemRows[] = [
                        'sale_id' => $sale->id,
                        'medicine_id' => $medicine->id,
                        'quantity' => $item['quantity'],
                        'unit_price' => $medicine->selling_price,
                        'total_price' => $itemTotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $stockTransactionRows[] = [
                        'medicine_id' => $medicine->id,
                        'user_id' => Auth::id(),
                        'type' => 'out',
                        'quantity' => $item['quantity'],
                        'unit_price' => $medicine->selling_price,
                        'total_price' => $itemTotal,
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'notes' => 'Sale #' . $sale->invoice_number,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    if (!isset($stockUpdates[$medicine->id])) {
                        $stockUpdates[$medicine->id] = 0;
                    }

                    $stockUpdates[$medicine->id] += $item['quantity'];
                }

                $fixedDiscount = (float) ($validated['discount'] ?? 0);
                $percentageDiscount = (float) ($validated['discount_percent'] ?? 0);
                $discount = min($fixedDiscount + ($subtotal * $percentageDiscount / 100), $subtotal);
                $tax = $validated['tax'];
                $total = $subtotal - $discount + $tax;
                $amountPaid = $validated['amount_paid'];
                $changeAmount = max(0, $amountPaid - $total);

                $dueAmount = 0;
                if ($saleType == 'credit') {
                    $dueAmount = $total - $amountPaid;
                    if ($dueAmount <= 0) {
                        $dueAmount = 0;
                    }
                }

                $sale->update([
                    'customer_name' => $validated['customer_name'] ?? null,
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'customer_email' => $validated['customer_email'] ?? null,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'discount' => $discount,
                    'total' => $total,
                    'amount_paid' => $amountPaid,
                    'change_amount' => $changeAmount,
                    'payment_method' => $validated['payment_method'],
                    'sale_type' => $saleType,
                    'due_amount' => $dueAmount,
                    'due_date' => $validated['due_date'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                    'status' => 'completed',
                ]);

                DB::table('sale_items')->insert($saleItemRows);
                DB::table('stock_transactions')->insert($stockTransactionRows);

                foreach ($stockUpdates as $medicineId => $quantity) {
                    DB::table('medicines')
                        ->where('id', $medicineId)
                        ->decrement('stock_quantity', $quantity);
                }
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['items' => $e->getMessage()]);
        }

        return redirect()->route('sales.show', $sale)->with('success', 'Sale updated successfully.');
    }

    /**
     * Display the specified sale.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\View\View
     */
    public function show(Sale $sale)
    {
        $sale->load(['user', 'saleItems.medicine.category']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Display the invoice for the specified sale.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\View\View
     */
    public function invoice(Sale $sale)
    {
        $sale->load(['user', 'saleItems.medicine.category']);

        return view('sales.invoice', compact('sale'));
    }

    /**
     * Remove the specified sale and restore stock.
     *
     * @param  \App\Models\Sale  $sale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->saleItems as $item) {
                Medicine::where('id', $item->medicine_id)
                    ->increment('stock_quantity', $item->quantity);
            }

            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }
}
