<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaProductOption extends Model
{
    //
    protected $table = 'ocfa_product_option';

    //protected $primaryKey = 'product_option_id';

    protected $fillable = [
        'product_option_id', 'product_id', 'option_id', 'value', 'required'
    ];

    //CRUD
    public static function createOcfaProductOption()
    {

    }
    public static function selectOcfaProductOption()
    {

    }
    public static function updateOcfaProductOption()
    {

    }
    public static function deleteOcfaProductOption()
    {

    }
}
