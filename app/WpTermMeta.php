<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WpTermMeta extends Model
{
    //
    protected $table = 'wp_termmeta';

    protected $primaryKey = 'meta_id';

    protected $fillable = [
        'meta_id', 'term_id', 'meta_key', 'meta_value'
    ];

    public $timestamps = false;

}
