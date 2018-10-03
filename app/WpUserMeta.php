<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WpUserMeta extends Model
{
    //
    protected $table = 'wp_usermeta';

    protected $primaryKey = 'umeta_id';

    protected $fillable = [
        'umeta_id', 'user_id', 'meta_key', 'meta_value'
    ];

    public $timestamps = false;


}
