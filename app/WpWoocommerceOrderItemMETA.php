<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WpWoocommerceOrderItemMETA extends Model
{
    //

    protected $table = 'wp_woocommerce_order_itemmeta';

    protected $primaryKey = 'meta_id';

    protected $fillable = [
        'meta_id', 'order_item_id', 
        'meta_key', 'meta_value'
    ];

    public $timestamps = false;
}
