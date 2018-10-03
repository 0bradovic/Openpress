<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaProductToCategory extends Model
{
    //

    protected $table = 'ocfa_product_to_category';

    protected $fillable = [
        'product_id', 'category_id'
    ];

    //CRUD
    public static function createOcfaProductToCategory()
    {

    }
    public static function selectOcfaProductToCategory()
    {
        
    }
    public static function updateOcfaProductToCategory()
    {
        
    }
    public static function deleteOcfaProductToCategory()
    {
        
    }
}
