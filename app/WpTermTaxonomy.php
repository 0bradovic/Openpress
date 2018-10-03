<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WpTermTaxonomy extends Model
{
    //
    protected $table = 'wp_term_taxonomy';

    protected $primaryKey = 'term_taxonomy_id';

    protected $fillable = [
        'term_taxonomy_id', 'term_id', 'taxonomy', 'description', 'parent', 'count'
    ];

    public $timestamps = false;


    //CRUD
    public static function createWpTermTaxonomy()
    {

    }
    public static function selectWpTermTaxonomy()
    {

    }
    public static function updateWpTermTaxonomy()
    {

    }
    public static function deleteWpTermTaxonomy()
    {

    }



}
