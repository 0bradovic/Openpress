<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaCategoryDescription extends Model
{
    //
    protected $table = 'ocfa_category_description';

    protected $fillable = [
        'category_id',
        'language_id',
        'name',
        'description',
        'meta_title',
        'meta_description',
        'meta_keyword'
    ];

    //CRUD
    public static function createOcfaCategoryDescription()
    {

    }
    public static function selectOcfaCategoryDescription()
    {
        
    }
    public static function updateOcfaCategoryDescription()
    {
        
    }
    public static function deleteOcfaCategoryDescription()
    {
        
    }
}
