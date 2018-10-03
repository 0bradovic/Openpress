<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OcfaCustomer extends Model
{
    //

    protected $table = 'ocfa_customer';

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'customer_id', 'customer_group_id', 'store_id', 'language_id', 'firstname', 'lastname',
        'email', 'telephone', 'fax', 'password', 'salt', 'cart', 'wishlist', 'newsletter',
        'address_id', 'customer_field', 'ip', 'status', 'approved', 'safe', 'token', 'code', 'date_added'
    ];

    //public $timestamps = false;
}
