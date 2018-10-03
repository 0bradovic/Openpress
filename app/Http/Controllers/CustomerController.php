<?php

namespace App\Http\Controllers;

use App\OcfaCategory;
use App\OcfaCategoryDescription;
use App\OcfaOptionValueDescription;
use App\OcfaProduct;
use App\OcfaProductDescription;
use App\OcfaProductImage;
use App\OcfaProductOption;
use App\OcfaProductOptionValue;
use App\OcfaProductToCategory;
use App\WpPost;
use App\WpPostMeta;
use App\WpTerm;
use App\WpTermRelationship;
use App\WpTermTaxonomy;
use App\OcfaManufacturer;
use App\WpTermMeta;
use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Str;


use App\OcfaCustomer;
use App\WpUser;
use App\WpUserMeta;
use App\OcfaAddress;
use App\OcfaOrder;
use App\WpWoocommerceOrderItem;
use App\WpWoocommerceOrderItemMETA;
use App\OcfaOrderProduct;
use App\OcfaOrderOption;
//WpPost
//WpPostMeta





class CustomerController extends Controller
{
    //

    public static function justDoIt()
    {
        $startID = 1000;
        $startIDmeta = 9999;
        $startIDpost = 666666666;
        $startIDpostMETA = $startIDpost;
        $orderItemId = 5555;

        foreach(OcfaOrder::all() as $ocCusORDER)
        {

            
            $ocCusORDER_ID = $ocCusORDER->order_id;
            $ocCus_ID = $ocCusORDER->customer_id;
            $ocCus_EMAIL = $ocCusORDER->email;

            $wpCustomer = WpUser::where('user_email', '=', $ocCus_EMAIL)->select('ID')->first();
            $wpCus_ID = $wpCustomer['ID'];

            //dd($ocCusORDER_ID);

            $POSTOJI_LI = OcfaOrderProduct::where('order_id','=', $ocCusORDER_ID)->select('name')->first();
            $POSTOJI_LI = $POSTOJI_LI['name'];

            $x = WpPost::where('post_title','=', $POSTOJI_LI)->first();
            
            if($x==null)
            {
                continue;
            }
            //if($POSTOJI_LI!=null)
            //{
            //    dd($POSTOJI_LI);
            //}


            foreach(OcfaCustomer::where('email', '=', $ocCus_EMAIL)->get() as $ocCustomer)
            {

                $post_name = CustomerController::slugify('Order $ndash;'.Carbon::now());
                $new_WpPost_Order_ID = $startIDpost++;
                $new_WpPost_Order = WpPost::create([
                    'ID' => $new_WpPost_Order_ID,
                    'post_author' => '1',
                    'post_date' => Carbon::now(),
                    'post_date_gmt' => Carbon::now(),
                    'post_content' => '',
                    'post_title' => 'Order $ndash;'.Carbon::now(),
                    'post_excerpt' => '',
                    'post_status' => 'wp-completed',
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_password' => 'order_'.Str::random(13) ,
                    'post_name' => $post_name,
                    'to_ping' => '',
                    'pinged' => '',
                    'post_modified' => Carbon::now(),
                    'post_modified_gmt' => Carbon::now(),
                    'post_content_filtered' => '',
                    'post_parent' => 0,
                    'guid' => "http://sfonlineshop.antiqueandarts.com/?post_type=shop_order&#038;p=".$new_WpPost_Order_ID,
                    'post_type' => 'shop_order',
                    'post_mime_type' => '',
                    'comment_count' => 0
                ]);
        
                
                $quantity = OcfaOrderProduct::where('order_id','=', $ocCusORDER_ID)->select('quantity')->first();
                $quantity = $quantity['quantity'];


                $productName = OcfaOrderProduct::where('order_id','=', $ocCusORDER_ID)->select('name')->first();
                $productName = $productName['name'];


                $velicina = OcfaOrderOption::where('order_id', '=', $ocCusORDER_ID)->select('value')->first();
                $velicina = $velicina['value'];


                $_product_id = WpPost::where('post_title', '=', $productName)->select('ID')->first();
                $_product_id = $_product_id['ID']; //PARENT

                $child_order_name = $productName.' - '.$velicina;
                $child_product_id = WpPost::where('post_parent', '=', $_product_id)->where('post_title','=', $child_order_name)->select('ID')->first();
                $child_product_id = $child_product_id['ID'];


                #region WpWoocommerceOrderItem

                //WpWoocommerceOrderItem [line_item] 
                $line_item = WpWoocommerceOrderItem::create([
                    'order_item_id' => $orderItemId++,
                    'order_item_name' => $child_order_name,
                    'order_item_type' => 'line_item',
                    'order_id' => $new_WpPost_Order_ID
                ]);
                //WpWoocommerceOrderItem [shipping]
                $shipping = WpWoocommerceOrderItem::create([
                    'order_item_id' => $orderItemId++,
                    'order_item_name' => 'Free shipping',
                    'order_item_type' => 'shipping',
                    'order_id' => $new_WpPost_Order_ID
                ]);

                
                #endregion



                #region WpWoocommerceOrderItemMETA [line_item]

                //WpWoocommerceOrderItemMETA [_product_id]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $line_item->order_item_id,
                    'meta_key' => '_product_id',
                    'meta_value' => $_product_id
                ]);

                //WpWoocommerceOrderItemMETA [_variation_id]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $line_item->order_item_id,
                    'meta_key' => '_variation_id',
                    'meta_value' => $child_product_id
                ]);

                //WpWoocommerceOrderItemMETA [_qty]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $line_item->order_item_id,
                    'meta_key' => '_qty',
                    'meta_value' => $quantity
                ]);

                //WpWoocommerceOrderItemMETA [_line_subtotal]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $line_item->order_item_id,
                    'meta_key' => '_line_subtotal',
                    'meta_value' => $ocCusORDER->total
                ]);

                //WpWoocommerceOrderItemMETA [_line_total]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $line_item->order_item_id,
                    'meta_key' => '_line_total',
                    'meta_value' => $ocCusORDER->total
                ]);

                //WpWoocommerceOrderItemMETA [_line_tax_data]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $line_item->order_item_id,
                    'meta_key' => '_line_tax_data',
                    'meta_value' => 'a:2:{s:5:"total";a:0:{}s:8:"subtotal";a:0:{}}'
                ]);

                //WpWoocommerceOrderItemMETA [pa_velicina]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $line_item->order_item_id,
                    'meta_key' => 'pa_velicina',
                    'meta_value' => $velicina
                ]);

                //WpWoocommerceOrderItemMETA [_tax_class]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $line_item->order_item_id,
                    'meta_key' => '_tax_class',
                    'meta_value' => ''
                ]);

                //WpWoocommerceOrderItemMETA [_line_tax]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $line_item->order_item_id,
                    'meta_key' => '_line_tax',
                    'meta_value' => '0'
                ]);

                //WpWoocommerceOrderItemMETA [_line_subtotal_tax]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $line_item->order_item_id,
                    'meta_key' => '_line_subtotal_tax',
                    'meta_value' => '0'
                ]);




                #endregion
                
                #region WpWoocommerceOrderItemMETA [shipping]


                //WpWoocommerceOrderItemMETA [method_id]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $shipping->order_item_id,
                    'meta_key' => 'method_id',
                    'meta_value' => 'free_shipping:1'
                ]);

                //WpWoocommerceOrderItemMETA [cost]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $shipping->order_item_id,
                    'meta_key' => 'cost',
                    'meta_value' => '0.00'
                ]);

                //WpWoocommerceOrderItemMETA [total_tax]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $shipping->order_item_id,
                    'meta_key' => 'total_tax',
                    'meta_value' => '0'
                ]);

                //WpWoocommerceOrderItemMETA [taxes]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $shipping->order_item_id,
                    'meta_key' => 'taxes',
                    'meta_value' => 'a:1:{s:5:"total";a:0:{}}'
                ]);

                $Items = $productName.' - '.$velicina.' &times; '.$quantity;
                //WpWoocommerceOrderItemMETA [Items]
                WpWoocommerceOrderItemMETA::create([
                    'meta_id' => $startIDmeta++,
                    'order_item_id' => $shipping->order_item_id,
                    'meta_key' => 'Items',
                    'meta_value' => $Items
                ]);


                #endregion


                #region WP_POSTMETA

                if($ocCusORDER->customer_id!=0)
                {   
                    //WP_POSTMETA [_customer_user]
                    WpPostMeta::create([
                        'ID' => $startIDpostMETA++,
                        'post_id' => $new_WpPost_Order_ID,
                        'meta_key' => '_customer_user',
                        'meta_value' => $wpCus_ID
                    ]);
                }
                else if($ocCusORDER->customer_id==0)
                {
                    //WP_POSTMETA [_customer_user]
                    WpPostMeta::create([
                        'ID' => $startIDpostMETA++,
                        'post_id' => $new_WpPost_Order_ID,
                        'meta_key' => '_customer_user',
                        'meta_value' => 0
                    ]);
                }

                //WP_POSTMETA [_payment_method]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_payment_method',
                    'meta_value' => 'cod'
                ]);
                //WP_POSTMETA [_payment_method_title]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_payment_method_title',
                    'meta_value' => 'Plaćanje pouzećem'
                ]);
                 //WP_POSTMETA [_billing_first_name]
                 WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_first_name',
                    'meta_value' => $ocCusORDER->firstname
                ]);
                //WP_POSTMETA [_billing_last_name]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_last_name',
                    'meta_value' => $ocCusORDER->lastname
                ]);
                $aTresa = OcfaAddress::where('customer_id', '=', $ocCus_ID)->select('address_1')->first();
                $aTresa=$aTresa['address_1'];
                //WP_POSTMETA [_billing_address_1]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_address_1',
                    'meta_value' => $aTresa
                ]);
                //WP_POSTMETA [_billing_email]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_email',
                    'meta_value' => $ocCus_EMAIL
                ]);

                //WP_POSTMETA [_order_key]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_order_key',
                    'meta_value' =>  ''
                ]);
                
                //WP_POSTMETA [_transaction_id]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_transaction_id',
                    'meta_value' => ''
                ]);
                
                //WP_POSTMETA [_customer_ip_address]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_customer_ip_address',
                    'meta_value' => ''
                ]);
                
                //WP_POSTMETA [_customer_user_agent]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_customer_user_agent',
                    'meta_value' => 'mozilla/5.0 (windows nt 10.0; win64; x64) applewebkit/537.36 (khtml, like gecko) chrome/68.0.3440.106 safari/537.36'
                ]);
                
                //WP_POSTMETA [_created_via]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_created_via',
                    'meta_value' => 'checkout'
                ]);
                
                //WP_POSTMETA [_date_completed]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_date_completed',
                    'meta_value' => ''
                ]);
                
                //WP_POSTMETA [_completed_date]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_completed_date',
                    'meta_value' => ''
                ]);
                
                //WP_POSTMETA [_date_paid]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_date_paid',
                    'meta_value' => ''
                ]);
                
                //WP_POSTMETA [_paid_date]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_paid_date',
                    'meta_value' => ''
                ]);
                
                //WP_POSTMETA [_cart_hash]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_cart_hash',
                    'meta_value' => ''
                ]);
                
                //WP_POSTMETA [_billing_company]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_company',
                    'meta_value' => ''
                ]);
                $graditj = OcfaOrder::where('customer_id', '=', $ocCus_ID)->select('payment_city')->first();
                $graditj=$graditj['payment_city'];
                //WP_POSTMETA [_billing_city]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_city',
                    'meta_value' => $graditj
                ]);
                
                $drzavitza = OcfaOrder::where('customer_id', '=', $ocCus_ID)->select('payment_country')->first();
                $drzavitza=$drzavitza['payment_city'];
                //WP_POSTMETA [_billing_state]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_state',
                    'meta_value' => $drzavitza
                ]);
                
                $postcode = OcfaOrder::where('customer_id', '=', $ocCus_ID)->select('shipping_postcode')->first();
                $postcode=$postcode['payment_city'];
                //WP_POSTMETA [_billing_postcode]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_postcode',
                    'meta_value' => $postcode
                ]);
                
                //WP_POSTMETA [_billing_country]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_country',
                    'meta_value' => 'RS'
                ]);


                $teljefon = OcfaOrder::where('customer_id', '=', $ocCus_ID)->select('telephone')->first();
                $teljefon=$teljefon['telephone'];
                //WP_POSTMETA [_billing_phone]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_phone',
                    'meta_value' => $teljefon
                ]);
                
                //WP_POSTMETA [_shipping_first_name]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_shipping_first_name',
                    'meta_value' => $ocCusORDER->firstname
                ]);
                
                //WP_POSTMETA [_shipping_last_name]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_shipping_last_name',
                    'meta_value' => $ocCusORDER->lastname
                ]);
                
                //WP_POSTMETA [_shipping_company]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_shipping_company',
                    'meta_value' => ''
                ]);
                
                //WP_POSTMETA [_shipping_address_1]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_shipping_address_1',
                    'meta_value' => $aTresa
                ]);
                
                $aTresa2 = OcfaAddress::where('customer_id', '=', $ocCus_ID)->select('address_2')->first();
                $aTresa2=$aTresa2['address_2'];
                //WP_POSTMETA [_shipping_address_2]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_shipping_address_2',
                    'meta_value' => $aTresa2
                ]);
                
                //WP_POSTMETA [_shipping_city]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_shipping_city',
                    'meta_value' => $graditj
                ]);
                
                //WP_POSTMETA [_shipping_state]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_shipping_state',
                    'meta_value' => $drzavitza
                ]);
                
                //WP_POSTMETA [_shipping_postcode]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_shipping_postcode',
                    'meta_value' => $postcode
                ]);
                
                //WP_POSTMETA [_shipping_country]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_shipping_country',
                    'meta_value' => 'RS'
                ]);
                
                //WP_POSTMETA [_order_currency]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_order_currency',
                    'meta_value' => 'RSD'
                ]);
                
                //WP_POSTMETA [_cart_discount]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_cart_discount',
                    'meta_value' => 0
                ]);
                
                //WP_POSTMETA [_cart_discount_tax]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_cart_discount_tax',
                    'meta_value' => 0
                ]);
                
                //WP_POSTMETA [_order_shipping]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_order_shipping',
                    'meta_value' => 0
                ]);
                
                //WP_POSTMETA [_order_shipping_tax]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_order_shipping_tax',
                    'meta_value' => 0
                ]);
                
                //WP_POSTMETA [_order_tax]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_order_tax',
                    'meta_value' => 0
                ]);
                
                $cena = OcfaOrder::where('customer_id', '=', $ocCus_ID)->select('total')->first();
                $cena=$cena['total'];
                //WP_POSTMETA [_order_total]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_order_total',
                    'meta_value' => $cena
                ]);
                
                //WP_POSTMETA [_order_version]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_order_version',
                    'meta_value' => '3.3.5'
                ]);
                
                //WP_POSTMETA [_prices_include_tax]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_prices_include_tax',
                    'meta_value' => 'no'
                ]);
                
                $ggg = $ocCusORDER->firstname.' '.$ocCusORDER->lastname.'  '.$aTresa.' '.$aTresa2.' '.$graditj.' '.$drzavitza.' '.$postcode.' RS '.$ocCus_EMAIL.' '.$teljefon;

                //WP_POSTMETA [_billing_address_index]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_billing_address_index',
                    'meta_value' => $ggg
                ]);
                
                //WP_POSTMETA [_shipping_address_index]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_shipping_address_index',
                    'meta_value' => $ggg
                ]);
                
                //WP_POSTMETA [_download_permissions_granted]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_download_permissions_granted',
                    'meta_value' => 'yes'
                ]);
                
                //WP_POSTMETA [_recorded_sales]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_recorded_sales',
                    'meta_value' => 'yes'
                ]);
                
                //WP_POSTMETA [_recorded_coupon_usage_counts]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_recorded_coupon_usage_counts',
                    'meta_value' => 'no'
                ]);
                
                //WP_POSTMETA [_order_stock_reduced]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_order_stock_reduced',
                    'meta_value' => 'no'
                ]);
                
                //WP_POSTMETA [_edit_lock]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_edit_lock',
                    'meta_value' => '1537299523:6'
                ]);
                
                //WP_POSTMETA [_edit_last]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_edit_last',
                    'meta_value' => '2'
                ]);
                
                //WP_POSTMETA [slide_template]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => 'slide_template',
                    'meta_value' => 'default'
                ]);
                
                //WP_POSTMETA [_vc_post_settings]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => '_vc_post_settings',
                    'meta_value' => 'a:1:{s:10:"vc_grid_id";a:0:{}}'
                ]);
                
                //WP_POSTMETA [br_labels]
                WpPostMeta::create([
                    'ID' => $startIDpostMETA++,
                    'post_id' => $new_WpPost_Order_ID,
                    'meta_key' => 'br_labels',
                    'meta_value' => 'a:34:{s:15:"label_from_post";s:0:"";s:12:"content_type";s:4:"text";s:4:"text";s:5:"Label";s:11:"text_before";s:0:"";s:14:"text_before_nl";s:0:"";s:10:"text_after";s:0:"";s:13:"text_after_nl";s:0:"";s:5:"image";s:0:"";s:4:"type";s:5:"label";s:11:"padding_top";s:3:"-10";s:18:"padding_horizontal";s:1:"0";s:13:"border_radius";s:1:"3";s:12:"border_width";s:1:"0";s:12:"border_color";s:6:"ffffff";s:12:"image_height";s:2:"30";s:11:"image_width";s:2:"50";s:9:"color_use";s:1:"1";s:5:"color";s:6:"f16543";s:10:"font_color";s:6:"ffffff";s:9:"font_size";s:2:"14";s:11:"line_height";s:2:"30";s:8:"position";s:4:"left";s:6:"rotate";s:4:"0deg";s:6:"zindex";s:3:"500";s:4:"data";a:0:{}s:15:"tooltip_content";s:0:"";s:13:"tooltip_theme";s:4:"dark";s:16:"tooltip_position";s:3:"top";s:18:"tooltip_open_delay";s:1:"0";s:19:"tooltip_close_delay";s:1:"0";s:15:"tooltip_open_on";s:5:"click";s:22:"tooltip_close_on_click";s:1:"0";s:17:"tooltip_use_arrow";s:1:"0";s:17:"tooltip_max_width";s:3:"300";}'
                ]);
                
                


                #endregion


            }


        }

    }

    



    //Custom function: convert String to Slug
    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }
}
