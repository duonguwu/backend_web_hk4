<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartDetail extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'quantity'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Khởi tạo giá trị mặc định nếu cần
        $this->quantity = $attributes['quantity'] ?? 1;
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', '_id');
    }
}
