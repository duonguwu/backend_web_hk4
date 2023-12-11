<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    // Tên trường ID
    //public $primaryKey = '_id';

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price * 1000, 0, '', '.');
    }
}
