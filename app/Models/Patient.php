<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'total_due',
    ];

    protected $casts = [
        'total_due' => 'decimal:2',
    ];

    /**
     * Get the sales for the patient.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
