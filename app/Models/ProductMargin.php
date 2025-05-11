<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMargin extends Model
{
    protected $fillable = [
        'product_id',
        'min_quantity',
        'max_quantity',
        'company_margin',
        'distributor_margin',
        'shop_margin',
    ];
}
