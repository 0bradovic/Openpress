<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaOptionValueDescription extends Model
{
    //
    protected $table = 'ocfa_option_value_description';

    protected $fillable = [
        'option_value_id', 'language_id', 'option_id', 'name'
    ];

    //CRUD
    public static function createOcfaValueDescription()
    {

    }
    public static function selectOcfaValueDescription()
    {
        
    }
    public static function updateOcfaValueDescription()
    {
        
    }
    public static function deleteOcfaValueDescription()
    {
        
    }
}
