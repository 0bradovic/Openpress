<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaProductImage extends Model
{
    //
    protected $table = 'ocfa_product_image';

    //protected $primaryKey = 'product_image_id';

    protected $fillable = [
        'product_image_id', 'product_id', 'image', 'sort_order'
    ];

    //CRUD
    public static function createOcfaProductImage()
    {

    }
    public static function selectOcfaProductImage()
    {

    }
    public static function updateOcfaProductImage()
    {

    }
    public static function deleteOcfaProductImage()
    {

    }

}
