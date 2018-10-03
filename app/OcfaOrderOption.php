<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaOrderOption extends Model
{
    //
    protected $table = 'ocfa_order_option';

    protected $primaryKey = 'order_option_id';

    protected $fillable = [
        'order_option_id',
        'order_id',
        'order_product_id',
        'product_option_id',
        'product_option_value_id',
        'name',
        'value',
        'type',
        
    ];
}
