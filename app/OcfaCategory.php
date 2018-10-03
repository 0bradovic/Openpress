<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaCategory extends Model
{
    //
    protected $table = 'ocfa_category';

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_id',
        'image',
        'parent_id',
        'top',
        'column',
        'sort_order',
        'status',
        'date_added',
        'date_modified'
    ];

    //CRUD
    public static function createOcfaCategory()
    {

    }
    public static function selectOcfaCategory()
    {
        
    }
    public static function updateOcfaCategory()
    {
        
    }
    public static function deleteOcfaCategory()
    {
        
    }
}
