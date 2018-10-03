<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaManufacturer extends Model
{
    //
    protected $table = 'ocfa_manufacturer';

    protected $primaryKey = 'manufacturer_id';

    protected $fillable = [
        'manufacturer_id', 'name', 'image', 'sort_order'
    ];

    
    //CRUD
    public static function createOcfaManufacturer()
    {

    }
    public static function selectOcfaManufacturer()
    {
        
    }
    public static function updateOcfaManufacturer()
    {
        
    }
    public static function deleteOcfaManufacturer()
    {
        
    }
}
