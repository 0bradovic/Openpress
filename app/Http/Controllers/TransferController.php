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

class TransferController extends Controller
{
    //
    public static function doThings()
    {
        ini_set('max_execution_time', 9999999);
        $startingId = 1000;
        $startIdPostmeta = 10000;
        $startIdTerms = 200;
        $startIdCategory = 4000;
        $startIdImages = 90000;
        $startIdTermsBrand = 9000;
        $startIdMETA=1000000;
        $startIdVariationZ = 555555555555555;
        $startIdTermMETA = 9999;

        //OC Categories to WP Categories
        foreach(OcfaCategoryDescription::all() as $ocCategory)
        {
            $categoryExist = false;
            foreach(WpTerm::all() as $wpCategory)
            {
                
                if($ocCategory->name == $wpCategory->name)
                {
                    $categoryExist = true;
                    break;
                }
            }

            if($categoryExist == false)
            {
                
                $parentId_array = OcfaCategory::where('category_id', '=', $ocCategory->category_id)->select('parent_id')->first()->toArray();
                $parentId = $parentId_array['parent_id'];
                //dd($parentId);
                if($parentId!=0)
                {
                    $parentId*=100;
                }
                
                $newWpCategory = WpTerm::create([
                    'term_id' => $startIdCategory++,
                    'name' => $ocCategory->name,
                    'slug' => TransferController::slugify($ocCategory->name),
                    'term_group' => 0
                ]);

                $newWpTermTaxonomyCategory = WpTermTaxonomy::create([
                    'term_taxonomy_id' => $newWpCategory->term_id,
                    'term_id' => $newWpCategory->term_id,
                    'taxonomy' => 'product_cat',
                    'description' => '',
                    'parent' => $parentId,
                    'count' => 0
                ]);
            }
        }



        foreach(OcfaProduct::all() as $ocProduct)
        {

            //WP_POSTS
            $newWpPost_id = $startingId++;
            $DES = OcfaProductDescription::select('description')->where('product_id', '=', $ocProduct->product_id)->first()->toArray();
            $DES = $DES['description'];
            
            $HTML_CONTENT = htmlspecialchars_decode($DES);
            
            //dd($HTML_CONTENT);
            
            $newWpPost = WpPost::create([
                'ID' => $newWpPost_id,
                'post_author' => '1',
                'post_date' => Carbon::now(),
                'post_date_gmt' =>  Carbon::now(),
                'post_content' => $HTML_CONTENT,
                'post_title' => $ocProduct->model,
                'post_excerpt' => '',
                'post_status' => 'publish',
                'comment_status' => 'open',
                'ping_status' => 'open',
                'post_name' => TransferController::slugify($ocProduct->model),
                'post_modified' =>  Carbon::now(),
                'post_modified_gmt' =>  Carbon::now(),
                'post_parent' => '0',
                'guid' => "http://sfonlineshop.antiqueandarts.com/?post_type=product&#038;p=".$newWpPost_id,
                'menu_order' => '0',
                'post_type' => 'product',
                'comment_count' => '0',
                'to_ping' => '',
                'pinged' => '',
                'post_content_filtered' => '',
                'post_mime_type' => ''
            ]);

            //WP_POSTMETA [_stock_status]
            WpPostMeta::create([
                'meta_id' => $startIdPostmeta++,
                'post_id' => $newWpPost->ID,
                'meta_key' => '_stock_status',
                'meta_value' => 'instock'
            ]);

            //WP_POSTMETA [_stock]
            WpPostMeta::create([
                'meta_id' => $startIdPostmeta++,
                'post_id' => $newWpPost->ID,
                'meta_key' => '_stock',
                'meta_value' => null
            ]);

            
            //WP_POSTMETA [_price]
            $newWpPostMeta_price = WpPostMeta::create([
                'meta_id' => $startIdPostmeta++,
                'post_id' => $newWpPost_id,
                'meta_key' => '_price',
                'meta_value' => (int)$ocProduct->price
            ]);
            //WP_POSTMETA [_product_attributes]
            $newWpPostMetaATTRIBUTE = WpPostMeta::create([
                'meta_id' => $startIdPostmeta++,
                'post_id' =>  $newWpPost_id,
                'meta_key' => '_product_attributes',
                'meta_value' => 'a:2:{s:8:"pa_brand";a:6:{s:4:"name";s:8:"pa_brand";s:5:"value";s:0:"";s:8:"position";i:0;s:10:"is_visible";i:1;s:12:"is_variation";i:0;s:11:"is_taxonomy";i:1;}s:11:"pa_velicina";a:6:{s:4:"name";s:11:"pa_velicina";s:5:"value";s:0:"";s:8:"position";i:2;s:10:"is_visible";i:1;s:12:"is_variation";i:1;s:11:"is_taxonomy";i:1;}}'
            ]);

            //WP_POSTMETA [_regular_price]
            $newWpPostMeta_regular_price = WpPostMeta::create([
                'meta_id' => $startIdPostmeta++,
                'post_id' => $newWpPost_id,
                'meta_key' => '_regular_price',
                'meta_value' => (int)$ocProduct->price
            ]);


            //WP_POSTMETA [_visibility]
            $newWpPostMeta_visibility = WpPostMeta::create([
                'meta_id' => $startIdPostmeta++,
                'post_id' => $newWpPost_id,
                'meta_key' => '_visibility',
                'meta_value' => 'visible'
            ]);


            




            //WP_TERM
            //dd(OcfaProductOption::where('product_option_id','=', $ocProduct->product_id)->get());
            if(OcfaProductOption::where('product_id','=', $ocProduct->product_id)->first()!=null)
            {
                WpTermRelationship::create([
                    'object_id' => $newWpPost_id,
                    'term_taxonomy_id' => '4',
                    'term_order' => '0'
                ]);

                $wpTermTaxonomy = WpTermTaxonomy::where('term_id', '=', 4)->first();
                $wpTermTaxonomy->count++;
                $wpTermTaxonomy->save();

            }
            else
            {
                WpTermRelationship::create([
                    'object_id' => $newWpPost_id,
                    'term_taxonomy_id' => '2',
                    'term_order' => '0'
                ]);

                $wpTermTaxonomy = WpTermTaxonomy::where('term_id', '=', 2)->first();
                $wpTermTaxonomy->count++;
                $wpTermTaxonomy->save();

            }

            //PRODUCT SIZES
            //dd(OcfaProductOptionValue::where('product_id', '=', $ocProduct->product_id)->where('option_id', '=', 13)->select('option_value_id')->get()->toArray());
            foreach(OcfaProductOptionValue::where('product_id', '=', $ocProduct->product_id)->where('option_id', '=', 13)->select('option_value_id')->get()->toArray() as $optionValue)
            {
                $optionValueId = $optionValue['option_value_id'];
                if($optionValueId!=0)
                {
                    $sizeNameArray = OcfaOptionValueDescription::select('name')->where('option_value_id', '=', $optionValueId)->first();
                    //dd($sizeNameArray);
                    $sizeName = $sizeNameArray['name'];
                    //dd($sizeName);
                    $ifExist = false;
                    $existingSizeTermId = 0;
                    foreach(WpTerm::all() as $wpTerms)
                    {
                        if($wpTerms->name==$sizeName)
                        {
                            $ifExist=true;

                            $existingSizeTermId = $wpTerms->term_id;
                            
                            break;

                        }
                    }
                    if($ifExist==true)
                    {
                        
                        $existingSizeTermTAXONOMY_Id = WpTermTaxonomy::select('term_taxonomy_id')->where('term_id', '=', $existingSizeTermId)->first();

                        $existingSizeTermTAXONOMY_Id = $existingSizeTermTAXONOMY_Id['term_taxonomy_id'];

                        
                        $existingSizeTermTAXONOMY_object = WpTermTaxonomy::where('term_taxonomy_id', '=', $existingSizeTermTAXONOMY_Id)->first();

                        //dd($existingSizeTermTAXONOMY_object);


                        $existingSizeTermTAXONOMY_object->count++;

                        $existingSizeTermTAXONOMY_object->save();


                        WpTermRelationship::create([
                            'object_id' => $newWpPost_id,
                            'term_taxonomy_id' => $existingSizeTermTAXONOMY_Id,
                            'term_order' => 0
                        ]);

                        

                        $dakleName = $newWpPost->post_title.' - '.$sizeName;
                        $dakleNameSLUG = TransferController::slugify($dakleName);

                        $AJDI = $startIdVariationZ;
                        $velicinaPost = WpPost::create([
                            'ID' => $AJDI,
                            'post_author' => '1',
                            'post_date' => Carbon::now(),
                            'post_date_gmt' =>  Carbon::now(),
                            'post_content' => '',
                            'post_title' => $dakleName,
                            'post_excerpt' => '',
                            'post_status' => 'publish',
                            'comment_status' => 'closed',
                            'ping_status' => 'closed',
                            'post_name' => $dakleNameSLUG,
                            'post_modified' =>  Carbon::now(),
                            'post_modified_gmt' =>  Carbon::now(),
                            'post_parent' => $newWpPost->ID,
                            'guid' => "http://sfonlineshop.antiqueandarts.com/?product_variation=product-".$newWpPost_id.'-variation-'.$AJDI,
                            'menu_order' => '0',
                            'post_type' => 'product_variation',
                            'comment_count' => '0',
                            'to_ping' => '',
                            'pinged' => '',
                            'post_content_filtered' => '',
                            'post_mime_type' => ''

                        ]);
                        $startIdVariationZ++;

                        
                        
                        WpPostmeta::create([
                            'meta_id' => $startIdMETA++,
                            'post_id' => $velicinaPost->ID,
                            'meta_key' => 'attribute_pa_velicina',
                            'meta_value' => TransferController::slugify($sizeName)
                        ]);

                        
                    //WP_POSTMETA [_tax_status]
                    $newWpPostMeta_tax_status = WpPostMeta::create([
                        'meta_id' => $startIdPostmeta++,
                        'post_id' => $velicinaPost->ID,
                        'meta_key' => '_tax_status',
                        'meta_value' => 'none'
                    ]);
                    //WP_POSTMETA [_tax_class]
                    $newWpPostMeta_tax_class = WpPostMeta::create([
                        'meta_id' => $startIdPostmeta++,
                        'post_id' => $velicinaPost->ID,
                        'meta_key' => '_tax_class',
                        'meta_value' => 'none'
                    ]);
                    //WP_POSTMETA [_regular_price]
                    $newWpPostMeta_regular_price = WpPostMeta::create([
                        'meta_id' => $startIdPostmeta++,
                        'post_id' => $velicinaPost->ID,
                        'meta_key' => '_regular_price',
                        'meta_value' => (int)$ocProduct->price
                    ]);

                    //WP_POSTMETA [_stock]
                    $newWpPostMeta_stock = WpPostMeta::create([
                        'meta_id' => $startIdPostmeta++,
                        'post_id' => $velicinaPost->ID,
                        'meta_key' => '_stock',
                        'meta_value' => 100
                    ]);

                    //WP_POSTMETA [_manage_stock]
                    $newWpPostMeta_manage_stock = WpPostMeta::create([
                        'meta_id' => $startIdPostmeta++,
                        'post_id' => $velicinaPost->ID,
                        'meta_key' => '_manage_stock',
                        'meta_value' => 'no'
                    ]);

                    //WP_POSTMETA [_stock_status]
                    $newWpPostMeta_stock_status = WpPostMeta::create([
                        'meta_id' => $startIdPostmeta++,
                        'post_id' => $velicinaPost->ID,
                        'meta_key' => '_stock_status',
                        'meta_value' => 'instock'
                    ]);

                    //WP_POSTMETA [_price]
                    $newWpPostMeta_price = WpPostMeta::create([
                        'meta_id' => $startIdPostmeta++,
                        'post_id' => $velicinaPost->ID,
                        'meta_key' => '_price',
                        'meta_value' => (int)$ocProduct->price
                    ]);

                    }
                    else
                    {


                        $x = WpTerm::create([
                            'term_id' => $startIdTerms++,
                            'name' => $sizeName,
                            'slug' => TransferController::slugify($sizeName),
                            'team_group' => '0'
                        ]);

                        $y = WpTermTaxonomy::create([
                            'term_taxonomy_id' => $x->term_id,
                            'term_id' => $x->term_id,
                            'taxonomy' => 'pa_velicina',
                            'parent' => 0,
                            'count' => 1,
                            'description' => ''
                        ]);

                        $existingSizeTermId = $x->term_id;
                        //$existingSizeTermTAXONOMY_Id = WpTermTaxonomy::select('term_taxonomy_id')->where('term_id', '=', $wpTerms->term_id)->first();
                        $existingSizeTermTAXONOMY_Id = $x->term_id;

                        WpTermRelationship::create([
                            'object_id' => $newWpPost_id,
                            'term_taxonomy_id' => $existingSizeTermTAXONOMY_Id,
                            'term_order' => 0
                        ]);

                        //WP_TERMMETA[not_dropdown]
                        WpTermMeta::create([
                            'meta_id' => $startIdTermMETA++,
                            'term_id' => $x->term_id,
                            'meta_key' => 'not_dropdown',
                            'meta_value' => 'on'
                        ]);
                        //WP_TERMMETA[color]
                        WpTermMeta::create([
                            'meta_id' => $startIdTermMETA++,
                            'term_id' => $x->term_id,
                            'meta_key' => 'color',
                            'meta_value' => ''
                        ]);
                        //WP_TERMMETA[image]
                        WpTermMeta::create([
                            'meta_id' => $startIdTermMETA++,
                            'term_id' => $x->term_id,
                            'meta_key' => 'image',
                            'meta_value' => ''
                        ]);




                        $dakleName = $newWpPost->post_title.' - '.$sizeName;
                        $dakleNameSLUG = TransferController::slugify($dakleName);


                        $AJDI = $startIdVariationZ;
                        $velicinaPost = WpPost::create([
                            'ID' => $AJDI,
                            'post_author' => '1',
                            'post_date' => Carbon::now(),
                            'post_date_gmt' =>  Carbon::now(),
                            'post_content' => '',
                            'post_title' => $dakleName,
                            'post_excerpt' => '',
                            'post_status' => 'publish',
                            'comment_status' => 'closed',
                            'ping_status' => 'closed',
                            'post_name' => $dakleNameSLUG,
                            'post_modified' =>  Carbon::now(),
                            'post_modified_gmt' =>  Carbon::now(),
                            'post_parent' => $newWpPost->ID,
                            'guid' => "http://sfonlineshop.antiqueandarts.com/?product_variation=product-".$newWpPost_id.'-variation-'.$AJDI,
                            'menu_order' => '0',
                            'post_type' => 'product_variation',
                            'comment_count' => '0',
                            'to_ping' => '',
                            'pinged' => '',
                            'post_content_filtered' => '',
                            'post_mime_type' => ''

                        ]);
                        $startIdVariationZ++;

                        WpPostmeta::create([
                            'meta_id' => $startIdMETA++,
                            'post_id' => $velicinaPost->ID,
                            'meta_key' => 'attribute_pa_velicina',
                            'meta_value' => TransferController::slugify($sizeName)
                        ]);

                         //WP_POSTMETA [_tax_status]
                        $newWpPostMeta_tax_status = WpPostMeta::create([
                            'meta_id' => $startIdPostmeta++,
                            'post_id' => $velicinaPost->ID,
                            'meta_key' => '_tax_status',
                            'meta_value' => 'none'
                        ]);
                        //WP_POSTMETA [_tax_class]
                        $newWpPostMeta_tax_class = WpPostMeta::create([
                            'meta_id' => $startIdPostmeta++,
                            'post_id' => $velicinaPost->ID,
                            'meta_key' => '_tax_class',
                            'meta_value' => 'none'
                        ]);
                        //WP_POSTMETA [_regular_price]
                        $newWpPostMeta_regular_price = WpPostMeta::create([
                            'meta_id' => $startIdPostmeta++,
                            'post_id' => $velicinaPost->ID,
                            'meta_key' => '_regular_price',
                            'meta_value' => (int)$ocProduct->price
                        ]);

                        //WP_POSTMETA [_stock]
                        $newWpPostMeta_stock = WpPostMeta::create([
                            'meta_id' => $startIdPostmeta++,
                            'post_id' => $velicinaPost->ID,
                            'meta_key' => '_stock',
                            'meta_value' => 100
                        ]);

                        //WP_POSTMETA [_manage_stock]
                        $newWpPostMeta_manage_stock = WpPostMeta::create([
                            'meta_id' => $startIdPostmeta++,
                            'post_id' => $velicinaPost->ID,
                            'meta_key' => '_manage_stock',
                            'meta_value' => 'no'
                        ]);

                        //WP_POSTMETA [_stock_status]
                        $newWpPostMeta_stock_status = WpPostMeta::create([
                            'meta_id' => $startIdPostmeta++,
                            'post_id' => $velicinaPost->ID,
                            'meta_key' => '_stock_status',
                            'meta_value' => 'instock'
                        ]);

                        //WP_POSTMETA [_price]
                        $newWpPostMeta_price = WpPostMeta::create([
                            'meta_id' => $startIdPostmeta++,
                            'post_id' => $velicinaPost->ID,
                            'meta_key' => '_price',
                            'meta_value' => (int)$ocProduct->price
                        ]);
                    }                        
                }
            }




            //PRODUCT BRANDS
            if($ocProduct->manufacturer_id!=0)
            {
                $brandName = OcfaManufacturer::where('manufacturer_id', '=', $ocProduct->manufacturer_id)->select('name')->first()->toArray();
                $brandName = $brandName['name'];

                $ifExist = false;
                $existingBrandTermId = 0;
                foreach(WpTerm::all() as $wpTerms)
                {
                    if($wpTerms->name==$brandName)
                    {
                        $ifExist=true;

                        $existingBrandTermId = $wpTerms->term_id;

                        break;

                    }
                }
                if($ifExist==true)
                {
                    $existingBrandTermTAXONOMY_Id = WpTermTaxonomy::select('term_taxonomy_id')->where('term_id', '=', $existingBrandTermId)->first();

                    $existingBrandTermTAXONOMY_Id = $existingBrandTermTAXONOMY_Id['term_taxonomy_id'];

                    $existingBrandTermTAXONOMY_object = WpTermTaxonomy::where('term_taxonomy_id', '=', $existingBrandTermTAXONOMY_Id)->first();

                    $existingBrandTermTAXONOMY_object->count++;

                    $existingBrandTermTAXONOMY_object->save();


                    WpTermRelationship::create([
                        'object_id' => $newWpPost_id,
                        'term_taxonomy_id' => $existingBrandTermTAXONOMY_Id,
                        'term_order' => 0
                    ]);

                }
                else
                {

                    $x = WpTerm::create([
                        'term_id' => $startIdTermsBrand++,
                        'name' => $brandName,
                        'slug' => TransferController::slugify($brandName),
                        'team_group' => '0'
                    ]);

                    $y = WpTermTaxonomy::create([
                        'term_taxonomy_id' => $x->term_id,
                        'term_id' => $x->term_id,
                        'taxonomy' => 'pa_brand',
                        'parent' => 0,
                        'count' => 1,
                        'description' => ''
                    ]);

                    $existingBrandTermId = $x->term_id;
                    //$existingBrandTermTAXONOMY_Id = WpTermTaxonomy::select('term_taxonomy_id')->where('term_id', '=', $wpTerms->term_id)->first();
                    $existingBrandTermTAXONOMY_Id = $x->term_id;

                    WpTermRelationship::create([
                        'object_id' => $newWpPost_id,
                        'term_taxonomy_id' => $existingBrandTermTAXONOMY_Id,
                        'term_order' => 0
                    ]);
                }
            }


            //PRODUCT CATEGORIES
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //If Product is in Outlet category too
            $outletCategoryEXIST = false;
            $outletCategoryNAME = '';
            foreach(OcfaProductToCategory::where('product_id', '=', $ocProduct->product_id)->select('category_id')->get()->toArray() as $ocProductCategory1)
            {
                $br=0;
                $x = OcfaCategoryDescription::where('category_id', '=', $ocProductCategory1['category_id'])->select('name')->first();
                $x['name'];
                foreach(OcfaProductToCategory::where('product_id', '=', $ocProduct->product_id)->select('category_id')->get()->toArray() as $ocProductCategory2)
                {
                    $y = OcfaCategoryDescription::where('category_id', '=', $ocProductCategory2['category_id'])->select('name')->first();
                    $y['name'];
                    if($x['name']==$y['name'])
                    {
                        $br++;
                        
                    }
                    if($br==2)
                    {
                        $outletCategoryEXIST = true;
                        $outletCategoryNAME = $y['name'];
                    }
                }
                
                
            }
            $outletCategoryDoneOnce = false; //if current cat is outlet but first time
            //Categories of the Product
            foreach(OcfaProductToCategory::where('product_id', '=', $ocProduct->product_id)->select('category_id')->get()->toArray() as $ocProductCategory)
            {
                
                if($outletCategoryEXIST==true) //If the product is in outlet category and in additional category with same name as in outlet one
                {
                    
                    $x = $ocCategoryOfCurrentProduct_Name = OcfaCategoryDescription::where('category_id', '=', $ocProductCategory['category_id'])->select('name')->first();
                    $x_Name = $x['name'];

                    if($x_Name == $outletCategoryNAME && $outletCategoryDoneOnce==false)    //do outlet and then continue with other categories
                    {

                        $ocCategoryOfCurrentProduct_Name = OcfaCategoryDescription::where('category_id', '=', $ocProductCategory['category_id'])->select('name')->first();

                        $ocCategoryOfCurrentProduct_Name = $ocCategoryOfCurrentProduct_Name['name'];

                        $WHEREISFUCKINGSLUG = array_values(WpTerm::where('name','=',$outletCategoryNAME)->get()->toArray());

                        
                        $wpTermId_Category_Id = $WHEREISFUCKINGSLUG[1]['term_id'];
                        
                        $wpTermTaxonomyId_Category = WpTermTaxonomy::where('term_id', '=', $wpTermId_Category_Id)->select('term_taxonomy_id')->first();

                        $wpTermTaxonomyId_Category_ZBOG_COUNTER = WpTermTaxonomy::where('term_id', '=', $wpTermId_Category_Id)->first();

                        $wpTermTaxonomyId_Category_ZBOG_COUNTER->count++;
                        $wpTermTaxonomyId_Category_ZBOG_COUNTER->save();

                        if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent!=0)//15 18 31
                        {
                            if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent==15)
                            {
                                $PARENTOV_COUNTER = WpTermTaxonomy::where('term_id', '=', 15)->first();
                                $PARENTOV_COUNTER->count++;
                                $PARENTOV_COUNTER->save();
                            }
                            if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent==18)
                            {
                                $PARENTOV_COUNTER = WpTermTaxonomy::where('term_id', '=', 18)->first();
                                $PARENTOV_COUNTER->count++;
                                $PARENTOV_COUNTER->save();
                            }
                            if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent==31)
                            {
                                $PARENTOV_COUNTER = WpTermTaxonomy::where('term_id', '=', 31)->first();
                                $PARENTOV_COUNTER->count++;
                                $PARENTOV_COUNTER->save();
                            }
                        }

                        $TTID = $wpTermTaxonomyId_Category['term_taxonomy_id'];

                        WpTermRelationship::create([
                            'object_id' => $newWpPost->ID,
                            'term_taxonomy_id' => $TTID,
                            'term_order' => 0
                        ]);

                        $outletCategoryDoneOnce = true;

                    }
                    else
                    {
                        $ocCategoryOfCurrentProduct_Name = OcfaCategoryDescription::where('category_id', '=', $ocProductCategory['category_id'])->select('name')->first();
                        $ocCategoryOfCurrentProduct_Name = $ocCategoryOfCurrentProduct_Name['name'];
    
                        $wpTermId_Category = WpTerm::where('name', '=', $ocCategoryOfCurrentProduct_Name)->select('term_id')->first();
                        $wpTermId_Category_Id = $wpTermId_Category['term_id'];
    
                        $wpTermTaxonomyId_Category = WpTermTaxonomy::where('term_id', '=', $wpTermId_Category_Id)->select('term_taxonomy_id')->first();

                        $wpTermTaxonomyId_Category_ZBOG_COUNTER = WpTermTaxonomy::where('term_id', '=', $wpTermId_Category_Id)->first();
                        $wpTermTaxonomyId_Category_ZBOG_COUNTER->count++;
                        $wpTermTaxonomyId_Category_ZBOG_COUNTER->save();

                        if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent!=0)//15 18 31
                        {
                            if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent==15)
                            {
                                $PARENTOV_COUNTER = WpTermTaxonomy::where('term_id', '=', 15)->first();
                                $PARENTOV_COUNTER->count++;
                                $PARENTOV_COUNTER->save();
                            }
                            if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent==18)
                            {
                                $PARENTOV_COUNTER = WpTermTaxonomy::where('term_id', '=', 18)->first();
                                $PARENTOV_COUNTER->count++;
                                $PARENTOV_COUNTER->save();
                            }
                            if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent==31)
                            {
                                $PARENTOV_COUNTER = WpTermTaxonomy::where('term_id', '=', 31)->first();
                                $PARENTOV_COUNTER->count++;
                                $PARENTOV_COUNTER->save();
                            }
                        }

                        $TTID = $wpTermTaxonomyId_Category['term_taxonomy_id'];
    
                        WpTermRelationship::create([
                            'object_id' => $newWpPost->ID,
                            'term_taxonomy_id' => $TTID,
                            'term_order' => 0
                        ]);
                    }
                }
                else //If this product doesn't exist in outlet AND non outlet same cat
                {
                    $ocCategoryOfCurrentProduct_Name = OcfaCategoryDescription::where('category_id', '=', $ocProductCategory['category_id'])->select('name')->first();
                    $ocCategoryOfCurrentProduct_Name = $ocCategoryOfCurrentProduct_Name['name'];

                    $wpTermId_Category = WpTerm::where('name', '=', $ocCategoryOfCurrentProduct_Name)->select('term_id')->first();
                    $wpTermId_Category_Id = $wpTermId_Category['term_id'];

                    $wpTermTaxonomyId_Category = WpTermTaxonomy::where('term_id', '=', $wpTermId_Category_Id)->select('term_taxonomy_id')->first();

                    $wpTermTaxonomyId_Category_ZBOG_COUNTER = WpTermTaxonomy::where('term_id', '=', $wpTermId_Category_Id)->first();
                    $wpTermTaxonomyId_Category_ZBOG_COUNTER->count++;
                    $wpTermTaxonomyId_Category_ZBOG_COUNTER->save();

                    if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent!=0)//15 18 31
                        {
                            if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent==15)
                            {
                                $PARENTOV_COUNTER = WpTermTaxonomy::where('term_id', '=', 15)->first();
                                $PARENTOV_COUNTER->count++;
                                $PARENTOV_COUNTER->save();
                            }
                            if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent==18)
                            {
                                $PARENTOV_COUNTER = WpTermTaxonomy::where('term_id', '=', 18)->first();
                                $PARENTOV_COUNTER->count++;
                                $PARENTOV_COUNTER->save();
                            }
                            if($wpTermTaxonomyId_Category_ZBOG_COUNTER->parent==31)
                            {
                                $PARENTOV_COUNTER = WpTermTaxonomy::where('term_id', '=', 31)->first();
                                $PARENTOV_COUNTER->count++;
                                $PARENTOV_COUNTER->save();
                            }
                        }

                    $TTID = $wpTermTaxonomyId_Category['term_taxonomy_id'];

                    WpTermRelationship::create([
                        'object_id' => $newWpPost->ID,
                        'term_taxonomy_id' => $TTID,
                        'term_order' => 0
                    ]);
                }
            }
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



            ////////////////////
            $str = trim(substr($ocProduct->image, strrpos($ocProduct->image, '/') + 1));
            $ZNACISLIKA_PRVA = WpPost::create([
                'ID' => $startIdImages++,
                'post_author' => 1,
                'post_date' => Carbon::now(),
                'post_date_gmt' => Carbon::now(),
                'post_title' => $str,
                'post_name' => $str,
                'post_status' => 'inherit',
                'post_modified' => Carbon::now(),
                'post_modified_gmt' => Carbon::now(),
                'post_parent' => $newWpPost->ID,
                'guid' => "http://sfonlineshop.antiqueandarts.com//wp-content/uploads/".$ocProduct->image,
                'menu_order' => '0',
                'post_type' => 'attachment',
                'post_mime_type' => 'image/jpeg',
                'comment_count' => 0,
                'post_content' => '',
                'post_excerpt' => '',
                'post_password' => '',
                'to_ping' => '',
                'pinged' => '',
                'post_content_filtered' => ''
            ]);
            

            //Wp_postmeta for images [_wp_attached_file]
            WpPostmeta::create([
                'meta_id' => $startIdMETA++,
                'post_id' => $ZNACISLIKA_PRVA->ID,
                'meta_key' => '_wp_attachment_metadata',
                'meta_value' => 'a:6:{s:5:"width";i:2164;s:6:"height";i:1443;s:14:"hwstring_small";s:22:"height=1443 width=2164";s:4:"file";s:30:"'.$ocProduct->image.'";s:5:"sizes";a:5:{s:9:"thumbnail";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:150;s:6:"height";i:150;}s:6:"medium";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:300;s:6:"height";i:214;}s:14:"shop_thumbnail";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:90;s:6:"height";i:67;}s:12:"shop_catalog";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:150;s:6:"height";i:111;}s:11:"shop_single";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:300;s:6:"height";i:250;}}s:10:"image_meta";a:10:{s:8:"aperture";i:0;s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";i:0;s:9:"copyright";s:0:"";s:12:"focal_length";i:0;s:3:"iso";i:0;s:13:"shutter_speed";i:0;s:5:"title";s:0:"";}}'
            ]);

            
            
            //Wp_postmeta for images [_wp_attachment_metadata]
            WpPostmeta::create([
                'meta_id' => $startIdMETA++,
                'post_id' => $ZNACISLIKA_PRVA->ID,
                'meta_key' => '_wp_attached_file',
                'meta_value' => $ocProduct->image,

            ]);


            //Wp_postmeta for images [_thumbnail_id]
            WpPostmeta::create([
                'meta_id' => $startIdMETA++,
                'post_id' => $newWpPost->ID,
                'meta_key' => '_thumbnail_id',
                'meta_value' => $ZNACISLIKA_PRVA->ID
            ]);

            



            //////////////////
            
        
            $first=true;
            foreach(OcfaProductImage::all() as $ocImage)
            {
                

                if($ocImage->product_id==$ocProduct->product_id)
                {
                    $str = trim(substr($ocImage->image, strrpos($ocImage->image, '/') + 1));
                    $ZNACISLIKA = WpPost::create([
                        'ID' => $startIdImages++,
                        'post_author' => 1,
                        'post_date' => Carbon::now(),
                        'post_date_gmt' => Carbon::now(),
                        'post_title' => $str,
                        'post_name' => $str,
                        'post_status' => 'inherit',
                        'post_modified' => Carbon::now(),
                        'post_modified_gmt' => Carbon::now(),
                        'post_parent' => $newWpPost->ID,
                        'guid' => "http://sfonlineshop.antiqueandarts.com//wp-content/uploads/".$ocImage->image,
                        'menu_order' => '0',
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image/jpeg',
                        'comment_count' => 0,
                        'post_content' => '',
                        'post_excerpt' => '',
                        'post_password' => '',
                        'to_ping' => '',
                        'pinged' => '',
                        'post_content_filtered' => ''
                    ]);

                    if($first==true)
                    {
                    //Wp_postmeta for images [_product_image_gallery]
                    $LUDILO = WpPostmeta::create([
                        'meta_id' => $startIdMETA++,
                        'post_id' => $newWpPost->ID,
                        'meta_key' => '_product_image_gallery',
                        'meta_value' => $ZNACISLIKA->ID.',',
                    ]);
                    $first=false;
                    }
                    
                    //$str = trim(substr($ocImage->image, strrpos($ocImage->image, '/') + 1));

                    /*
                    if($first==true)
                    {
                        //Wp_postmeta for images [_wp_attached_file]
                        WpPostmeta::create([
                            'meta_id' => $startIdMETA++,
                            'post_id' => $ZNACISLIKA->ID,
                            'meta_key' => '_wp_attachment_metadata',
                            'meta_value' => 'a:6:{s:5:"width";i:2164;s:6:"height";i:1443;s:14:"hwstring_small";s:22:"height=1443 width=2164";s:4:"file";s:30:"'.$ocImage->image.'";s:5:"sizes";a:5:{s:9:"thumbnail";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:150;s:6:"height";i:150;}s:6:"medium";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:300;s:6:"height";i:214;}s:14:"shop_thumbnail";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:90;s:6:"height";i:67;}s:12:"shop_catalog";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:150;s:6:"height";i:111;}s:11:"shop_single";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:300;s:6:"height";i:250;}}s:10:"image_meta";a:10:{s:8:"aperture";i:0;s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";i:0;s:9:"copyright";s:0:"";s:12:"focal_length";i:0;s:3:"iso";i:0;s:13:"shutter_speed";i:0;s:5:"title";s:0:"";}}'
                        ]);

                        
                        
                        //Wp_postmeta for images [_wp_attachment_metadata]
                        WpPostmeta::create([
                            'meta_id' => $startIdMETA++,
                            'post_id' => $ZNACISLIKA->ID,
                            'meta_key' => '_wp_attached_file',
                            'meta_value' => $ocImage->image,

                        ]);


                        //Wp_postmeta for images [_thumbnail_id]
                        WpPostmeta::create([
                            'meta_id' => $startIdMETA++,
                            'post_id' => $newWpPost->ID,
                            'meta_key' => '_thumbnail_id',
                            'meta_value' => $ZNACISLIKA->ID
                        ]);

                        //Wp_postmeta for images [_product_image_gallery]
                        WpPostmeta::create([
                            'meta_id' => $startIdMETA++,
                            'post_id' => $newWpPost->ID,
                            'meta_key' => '_product_image_gallery',
                            'meta_value' => $ZNACISLIKA->ID
                        ]);

                        $first=false;

                    }
                    else
                    {
                        */
                        //Wp_postmeta for images [_wp_attached_file]
                        WpPostmeta::create([
                            'meta_id' => $startIdMETA++,
                            'post_id' => $ZNACISLIKA->ID,
                            'meta_key' => '_wp_attachment_metadata',
                            'meta_value' => 'a:6:{s:5:"width";i:2164;s:6:"height";i:1443;s:14:"hwstring_small";s:22:"height=1443 width=2164";s:4:"file";s:30:"'.$ocImage->image.'";s:5:"sizes";a:5:{s:9:"thumbnail";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:150;s:6:"height";i:150;}s:6:"medium";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:300;s:6:"height";i:214;}s:14:"shop_thumbnail";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:90;s:6:"height";i:67;}s:12:"shop_catalog";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:150;s:6:"height";i:111;}s:11:"shop_single";a:3:{s:4:"file";s:22:"'.$str.'";s:5:"width";i:300;s:6:"height";i:250;}}s:10:"image_meta";a:10:{s:8:"aperture";i:0;s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";i:0;s:9:"copyright";s:0:"";s:12:"focal_length";i:0;s:3:"iso";i:0;s:13:"shutter_speed";i:0;s:5:"title";s:0:"";}}'
                        ]);

                        
                        
                        //Wp_postmeta for images [_wp_attachment_metadata]
                        WpPostmeta::create([
                            'meta_id' => $startIdMETA++,
                            'post_id' => $ZNACISLIKA->ID,
                            'meta_key' => '_wp_attached_file',
                            'meta_value' => $ocImage->image,

                        ]);

                        /*
                        //Wp_postmeta for images [_product_image_gallery]
                        WpPostmeta::create([
                            'meta_id' => $startIdMETA++,
                            'post_id' => $newWpPost->ID,
                            'meta_key' => '_product_image_gallery',
                            'meta_value' => $ZNACISLIKA->ID
                        ]);
                        */

                    //}
                    $LUDILO->meta_value = $LUDILO->meta_value.$ZNACISLIKA->ID.',';
                    $LUDILO->save();

                    
                }

            }
            $ASD=substr($LUDILO->meta_value, 0, -1);
            $LUDILO->meta_value=$ASD;

            
            
            $startingId++;
            $startIdPostmeta++;
            $startIdTerms++;
            $startIdCategory++;
            $startIdImages++;
            $startIdTermsBrand++;
            $startIdMETA++;
            $startIdVariationZ++;
            $startIdTermMETA++;
            
        
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
