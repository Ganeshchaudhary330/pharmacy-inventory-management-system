<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'generic_name',
        'category_id',
        'supplier_id',
        'batch_number',
        'sku',
        'purchase_price',
        'selling_price',
        'stock_quantity',
        'low_stock_threshold',
        'expiry_date',
        'manufacturer',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expiry_date' => 'date',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
    ];

    /**
     * Get the category that owns the medicine.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier that owns the medicine.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the stock transactions for the medicine.
     */
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    /**
     * Get the sale items for the medicine.
     */
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Scope a query to only include active medicines.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include low stock medicines.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                     ->where('stock_quantity', '>', 0);
    }

    /**
     * Scope a query to only include out of stock medicines.
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', '<=', 0);
    }

    /**
     * Scope a query to only include expired medicines.
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Scope a query to only include expiring soon medicines (within 30 days).
     */
    public function scopeExpiringSoon($query)
    {
        return $query->whereBetween('expiry_date', [now(), now()->addDays(30)]);
    }

    /**
     * Check if medicine is low stock.
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    /**
     * Check if medicine is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date < now();
    }
}
