<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WpTerm extends Model
{
    //
    protected $table = 'wp_terms';

    protected $primaryKey = 'term_id';

    protected $fillable = [
        'term_id', 'name', 'slug', 'term_group'
    ];

    public $timestamps = false;


    //CRUD
    public static function createWpTerm()
    {

    }
    public static function selectWpTerm()
    {

    }
    public static function updateWpTerm()
    {

    }
    public static function deleteWpTerm()
    {

    }

}
