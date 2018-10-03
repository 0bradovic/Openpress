<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WpTermRelationship extends Model
{
    //
    protected $table = 'wp_term_relationships';

    protected $fillable = [
        'object_id', 'term_taxonomy_id' , 'term_order'
    ];

    public $timestamps = false;


    //CRUD
    public static function createWpTermRelationship()
    {

    }
    public static function selectWpTermRelationship()
    {
        
    }
    public static function updateWpTermRelationship()
    {
        
    }
    public static function deleteWpTermRelationship()
    {
        
    }
}
