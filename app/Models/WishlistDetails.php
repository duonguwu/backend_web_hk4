<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistDetails extends Model
{
    use HasFactory;
    protected $fillable = ['wishlist_id', 'product_id'];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', '_id'); // Giả sử bạn có một model Product
    }
}
