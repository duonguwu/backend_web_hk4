<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Product extends Model
{
    use HasFactory;
    // const CREATED_AT    = 'product_created';
    // const UPDATED_AT    = 'product_updated';

    // protected $table    = 'products';
    protected $fillable = ['_id', 'name', 'description', 'brand', 'category', 'gender', 'weight', 'quantity', 'image', 'rating', 'price', 'newPrice', 'trending'];
    // protected $guarded  = ['_id'];

    // protected $primaryKey = '_id';
    // public $incrementing = false;
    // protected $dates = ['product_created', 'product_updated'];
    // protected $dateFormat = 'y-m-d H:i:s';

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $model->_id = Uuid::uuid4()->toString(); // Tạo UUID mới khi tạo bản ghi mới
    //     });
    // }
}
