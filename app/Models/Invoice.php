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
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class);
    }
}
