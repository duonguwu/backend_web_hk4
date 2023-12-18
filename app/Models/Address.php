<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'fullname',
        'mobile',
        'flat',
        'area',
        'city',
        'pincode',
        'user_id',
    ];
}
