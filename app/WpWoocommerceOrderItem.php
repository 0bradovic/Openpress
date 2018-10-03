<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WpWoocommerceOrderItem extends Model
{
    //
    protected $table = 'wp_woocommerce_order_items';

    protected $primaryKey = 'order_item_id';

    protected $fillable = [
        'order_item_id',
        'order_item_name',
        'order_item_type',
        'order_id'
    ];

    public $timestamps = false;
}
