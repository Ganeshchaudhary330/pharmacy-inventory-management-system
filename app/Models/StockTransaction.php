<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'medicine_id',
        'user_id',
        'type',
        'quantity',
        'unit_price',
        'total_price',
        'reference_type',
        'reference_id',
        'notes',
    ];

    /**
     * Get the medicine that owns the transaction.
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get the user that created the transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include stock in transactions.
     */
    public function scopeStockIn($query)
    {
        return $query->where('type', 'in');
    }

    /**
     * Scope a query to only include stock out transactions.
     */
    public function scopeStockOut($query)
    {
        return $query->where('type', 'out');
    }
}
