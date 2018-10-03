<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaProduct extends Model
{
    //
    protected $table = 'ocfa_product';

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_id',
        'model',
        'sku',
        'upc',
        'ean',
        'jan',
        'isbn',
        'mpn',
        'location',
        'quantity',
        'stock_status_id',
        'image',
        'manufacturer_id',
        'shipping',
        'price',
        'points',
        'tax_class_id',
        'date_available',
        'weight',
        'weight_class_id',
        'length',
        'width',
        'height',
        'length_class_id',
        'subtract',
        'minimum',
        'sort_order',
        'status',
        'viewed',
        'date_added',
        'date_modified'
    ];

    //CRUD
    public static function createOcfaProduct()
    {

    }
    public static function selectOcfaProduct()
    {
        
    }
    public static function updateOcfaProduct()
    {
        
    }
    public static function deleteOcfaProduct()
    {
        
    }

}
