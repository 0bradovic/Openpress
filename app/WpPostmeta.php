<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WpPostmeta extends Model
{
    //

    protected $table = 'wp_postmeta';

    protected $primaryKey = 'meta_id';

    protected $fillable = [
        'meta_id', 'post_id', 'meta_key', 'meta_value'
    ];

    public $timestamps = false;


    //CRUD
    public static function createWpPostmeta()
    {

    }
    public static function selectWpPostmeta()
    {
        
    }
    public static function updateWpPostmeta()
    {
        
    }
    public static function deleteWpPostmeta()
    {
        
    }
}
