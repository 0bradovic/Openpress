<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaAddress extends Model
{
    //
    protected $table = 'ocfa_address';

    protected $primaryKey = 'address_id';

    protected $fillable = [
        'address_id', 'customer_id', 'firstname', 'lastname', 
        'company', 'address_1', 'address_2', 'city', 'postcode',
        'country_id', 'zone_id', 'customer_field'
    ];

    
}
