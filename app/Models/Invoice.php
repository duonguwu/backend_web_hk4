<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'address_id',
        'total_items',
        'actual_price',
        'total_price',
        'payment_method',
    ];

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class);
    }
}
