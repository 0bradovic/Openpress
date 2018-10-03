<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaOrderProduct extends Model
{
    //

    protected $table = 'ocfa_order_product';

    protected $primaryKey = 'order_product_id';

    protected $fillable = [
        'order_product_id',
        'order_id',
        'product_id',
        'name',
        'model',
        'quantity',
        'price',
        'total',
        'tax',
        'reward'
    ];
}
