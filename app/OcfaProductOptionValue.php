<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaProductOptionValue extends Model
{
    //
    protected $table = 'ocfa_product_option_value';

    //protected $primaryKey = 'product_option_value_id';

    protected $fillable = [
        'product_option_value_id',
        'product_option_id',
        'product_id',
        'option_id',
        'option_value_id',
        'quantity',
        'subtract',
        'price',
        'price_prefix',
        'points',
        'points_prefix',
        'weight',
        'weight_prefix'
    ];

    //CRUD
    public static function createOcfaProductOptionValue()
    {

    }
    public static function selectOcfaProductOptionValue()
    {
        
    }
    public static function updateOcfaProductOptionValue()
    {
        
    }
    public static function deleteOcfaProductOptionValue()
    {
        
    }
}
