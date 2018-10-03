<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaProductDescription extends Model
{
    //
    protected $table = 'ocfa_product_description';

    protected $fillable = [
        'product_id', 
        'language_id', 
        'name', 
        'description', 
        'tag', 
        'meta_title',
        'meta_description',
        'meta_keyword'
    ];

    //CRUD
    public static function createOcfaProductDescription()
    {

    }
    public static function selectOcfaProductDescription()
    {
        
    }
    public static function updateOcfaProductDescription()
    {
        
    }
    public static function deleteOcfaProductDescription()
    {
        
    }
 }
