<?php

namespace App\Models\FrontEnd\Products;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\FrontEnd\Utils\GlobalUtil;
use App\Media;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Excel;
class Product extends Model
{
    use HasFactory,SoftDeletes;
    // *** REACT FRONT-END PRODUCT *** // 
    // **1** ALL PRODUCT
    public static function getProduct($user,$request) {
        try{
            $page         = $request->query('page', 1);
            $skip         = $request->query('skip', 0);
            $limit        = $request->query('limit', 25);
            $skpP         = ($page-1)*$limit;
            $list         = [];

            $business_id  = $user->business_id;
            $count        = \App\Product::count();
            $totalPages   = ceil($count / $limit);
            $prevPage     = $page > 1 ? $page - 1 : null;
            $nextPage     = $page < $totalPages ? $page + 1 : null;
            $product      = \App\Product::where("business_id",$business_id)->skip($skpP)->take($limit)->orderBy("id","desc")->get();
            if(count($product)==0) { return false; }
            foreach($product as $i){
                $location                 = \App\BusinessLocation::where("business_id",$i->business_id)->first(); 
                $vedio = "";
                if($i->vedio_name != null || $i->vedio_name != ""){
                    $name_vedio = json_decode($i->vedio_name);
                    foreach($name_vedio as $ie){
                        $vedio = \URL::to("storage/app/public/".$ie);
                    }
                }
                
                $cost                               = \App\Product::product_cost($i->id);
                $variation_product                  = \App\Variation::where("product_id",$i->id)->first();
                $list[] =[
                        "id"                        =>  $i->id, 
                        "name"                      =>  $i->name, 
                        "location"                  =>  $location->name, 
                        "brand"                     =>  ($i->brand != null)?($i->brand->name):"", 
                        "category"                  =>  ($i->category != null)?($i->category->name):"", 
                        "sub_category"              =>  ($i->sub_category != null)?($i->sub_category->name):"", 
                        "tax"                       =>  ($i->product_tax != null)?($i->product_tax->name):"", 
                        "tax_type"                  =>  $i->tax_type, 
                        "enable_stock"              =>  $i->enable_stock,
                        "unit_cost_price"           =>  $cost, 
                        "unit_sale_price_exc_vat"   =>  FloatVal($variation_product->default_sell_price), 
                        "unit_sale_price"           =>  FloatVal($variation_product->sell_price_inc_tax), 
                        "code"                      =>  $i->sku,
                        "additional_code"           =>  $i->sku2,
                        "video"                     =>  ($vedio != "" && $vedio != null)?$vedio:null,
                        "barcode_type"              =>  $i->barcode_type,
                        "image"                     =>  $i->image_url,
                        "warranty"                  =>  ($i->warranty != null)?$i->warranty->name:"",
                        "created_by"                =>  (\App\Models\User::find($i->created_by))?\App\Models\User::find($i->created_by)->first_name:"",
                        "created_at"                =>  $i->created_at->format("Y-m-d"),
                ]; 
            }
            $total["items"] = $list;
            $total["info"]  = [
                    "totalRows"     => $count,
                    'current_page'  => $page,
                    'last_page'     => $totalPages,
                    'limit'         => 25,
                    'prev_page_url' => $prevPage ? "/app/react/products/all?page=$prevPage" : null,
                    'next_page_url' => $nextPage ? "/app/react/products/all?page=$nextPage" : null,
            ];
            return $total;
        }catch(Exception $e){
            return false;
        }
    }
    // **2** CREATE PRODUCT
    public static function createProduct($user) {
        try{
            $list   = Product::requirement($user);
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // **3** VIEW PRODUCT
    public static function viewProduct($user,$id) {
        try{
            
            $productUtil                   = new ProductUtil();
            $list                          = Product::stock($user,$id);
            $product                       = \App\Product::find($id);
            $vedio = "";
            if($product->product_vedio != null || $product->product_vedio != ""){
                $name_vedio = json_decode($product->product_vedio);
                foreach($name_vedio as $ie){
                    $vedio = \URL::to("storage/app/public/".$ie);
                }
            }
            $rack_details                  = $productUtil->getRackDetails($user->business_id, $id);

            $product_details               = \App\ProductVariation::where('product_id', $id)
                                                ->with(['variations', 'variations.media'])
                                                ->first();
            $more_image = [];
            foreach($product_details->variations as $variation){
                    foreach($variation->media as $media){
                        $more_image[] = $media->getDisplayUrlAttribute();
                    }
            }        
            $cost                            = \App\Product::product_cost($id);
            $allData                         = [];
            $allData["name"]                 = $product->name ;
            $allData["image"]                = $product->image_url ;
            if(count($more_image)>0){ $allData["alter_images"]  =   $more_image; }
            if($vedio!=""){$allData["video"] = $vedio;}
            $allData["description"]          = $product->product_description ;
            $allData["full_description"]     = $product->full_description ;
            $allData["product_type"]         = $product->type ;
            $allData["code"]                 = $product->sku ;
            $allData["second_code"]          = $product->sku2 ;
            $allData["category"]             = ($product->category)?$product->category->name:"Wrong!!!" ;
            $allData["sub_category"]         = ($product->sub_category)?$product->sub_category->name:"Wrong!!!" ;
            $allData["tax"]                  = ($product->product_tax)?$product->product_tax->name :"Wrong!!!" ; 
            $allData["manage_stock"]         = ($product->enable_stock == 1)?"Yes":"No" ;
            if($product->enable_stock == 1){$allData["alert_qty"] = $product->alert_quantity;}
            $allData["total_quantity"]       = $list["total_qty"];
            $allData["cost"]                 = $cost ;
            $allData["stock"]                = $list["list_store"];
            $allData["barcode_type"]         = $product->barcode_type ;
            $allData["brand"]                = ($product->brand)?$product->brand->name:"" ;
            $allData["unit"]                 = ($product->unit)?$product->unit->actual_name:"" ;
            $allData["custom_field_1"]       = $product->custom_field_1 ;
            $allData["custom_field_2"]       = $product->custom_field_2 ;
            $allData["custom_field_3"]       = $product->custom_field_3 ;
            $allData["custom_field_4"]       = $product->custom_field_4 ;
            $allData["weight"]               = $product->weight ;
            if(count($product->product_locations) > 0){
                $allData["product_locations"] = implode(', ', $product->product_locations->pluck('name')->toArray()); 
		    }else{
                $allData["product_locations"] = "";
            }
            $allData["warranty"]                 = ($product->warranty)?$product->warranty->display_name:"" ;
            $rack = [];
            $rackLine = [];
          
            foreach($rack_details as $rd){
                $location             = \App\BusinessLocation::find($rd["location_id"]);
                $rackLine["name"]     = ($location != null)?$location->name:"" ;
                $rackLine["rack"]     = $rd["rack"] ;
                $rackLine["row"]      = $rd["row"] ;
                $rackLine["position"] = $rd["position"] ;
                $rack[] = $rackLine;
            }
            $allData["rack"]               = $rack ;
             
            if($product->type == "variable"){
                $rows = [];
                foreach($product->variations as $V_ari){
                   
                    $rowsLine = [];
                    $rowsLine["name"]                   =  $V_ari->product_variation->name ."-".  $V_ari->name;
                    $rowsLine["sub_sku"]                =  $V_ari->sub_sku;
                    $rowsLine["default_purchase_price"] =  $V_ari->default_purchase_price;
                    $rowsLine["dpp_inc_tax"]            =  $V_ari->dpp_inc_tax;
                    $rowsLine["profit_percent"]         =  $V_ari->profit_percent;
                    $rowsLine["default_sell_price"]     =  $V_ari->default_sell_price;
                    $rowsLine["sell_price_inc_tax"]     =  $V_ari->sell_price_inc_tax;
                    foreach($V_ari->media as $media){
                        $rowsLine["image"] = $media->display_url ;  
                    }
                    $rows[] = $rowsLine;
                }
                $allData["variable_rows"] = $rows;
            }
            
            if($product->type == "combo"){
                $rows = [];
                $product_details  = \App\ProductVariation::where('product_id', $id)->with(['variations', 'variations.media'])->first();
                $combo_variations = $productUtil->__getComboProductDetails($product_details['variations'][0]->combo_variations, $product->business_id);
                $variation_id     = $product_details['variations'][0]->id;
                $profit_percent   = $product_details['variations'][0]->profit_percent;

                foreach($combo_variations as $V_ari){
                   if($V_ari['variation']){
                        $rowsLine = [];
                        $rowsLine["name"]                   =  $V_ari['variation']['product']->name ."-".  $V_ari['variation']->sub_sku;
                        $rowsLine["sub_sku"]                =  $V_ari['variation']->sub_sku;
                        $rowsLine["default_purchase_price"] =  $V_ari['variation']->default_purchase_price;
                        $rowsLine["dpp_inc_tax"]            =  $V_ari['variation']->dpp_inc_tax;
                        $rowsLine["profit_percent"]         =  $V_ari['variation']->profit_percent;
                        $rowsLine["default_sell_price"]     =  $V_ari['variation']->default_sell_price;
                        $rowsLine["sell_price_inc_tax"]     =  $V_ari['variation']->sell_price_inc_tax . isset($V_ari['unit_name'])??"";
                        $rowsLine["quantity"]               =  $V_ari['quantity'];
                        $rowsLine["net_amount"]             =  $V_ari['variation']->default_purchase_price * $V_ari['quantity'] * $V_ari['multiplier'];
                        foreach($V_ari['variation']->media as $media){
                            $rowsLine["image"] = $media->display_url ;  
                        }
                        $rows[] = $rowsLine;
                    }
                }
                $allData["combo_rows"] = $rows;
                $allData["total_default_sell_price"] = $product->variations->first()->sell_price_inc_tax;

            }
            
             
             return $allData;
        }catch(Exception $e){
            return false;
        }
    }
    // **4** EDIT PRODUCT
    public static function editProduct($user,$id) {
        try{
            $productUtil             = new ProductUtil();
            $business_id             = $user->business_id;
            // .**Requirement**.......
            $require                 = Product::requirement($user);
            // .......................
            $product                 = \App\Product::where("id",$id)
                                                    ->select([
                                                        "id","name","business_id","type","unit_id","full_description",    
                                                        "sub_unit_ids","brand_id","category_id","sub_category_id","tax",    
                                                        "tax_type","enable_stock","alert_quantity","sku","sku2","not_for_selling",    
                                                        "barcode_type","expiry_period","expiry_period_type","enable_sr_no","weight",    
                                                        "product_custom_field1","product_custom_field2","product_custom_field3","product_custom_field4",    
                                                        "product_description","created_by","warranty_id","product_vedio","image" 
                                                    ])
                                                    ->first();
            if(!$product){ return false; }
            $variation               =  \App\Variation::where("product_id",$id)->first();                                        
            $line                    = [];$listM = [];
            $linePrices              = [];$vedS  = "";
            // .**vedio**..........................................
                if($product->product_vedio != null || $product->product_vedio != ""){
                    $video="";
                    $name_video = json_decode($product->product_vedio);
                    foreach($name_video as $i){
                        $video = \URL::to("storage/app/public/".$i);
                    }
                    if($video != "" && $video != null){
                        $vedS            =   $video;
                    }
                }
            // ....................................................
            // .**MoreImage**......................................
                foreach($product->variations as $key => $variation){
                    foreach($variation->media as $media){
                        $listM[] = ["id"=>$media->id,"value"=>$media->display_url];
                    }
                }
            // ....................................................
            // .**variable**.......................................
                $ProductVariation = [];$ProductCombo = [];$total_default_sell_price = 0;
                if($product->type == "variable"){
                        $listOfVar=[];$finalVariable=[];
                        foreach($product->variations as $v){
                            if(!in_array($v->product_variation->variation_template->id,$listOfVar) && $v->product_variation->variation_template->id !=null){
                                $listOfVar[] = $v->product_variation->variation_template->id;
                                $listOfTable_id[] = $v->product_variation->id ;
                                $listOfTable_name[] = $v->product_variation->name ;
                            }
                        }
                        foreach($listOfVar as $keys =>  $var){
                             $rows = [];
                            foreach($product->variations as $V_ari){
                                if($V_ari->product_variation->variation_template->id == $var){
                                    $rowsLine                           =  [];
                                    $rowsLine["rows_id"]                =  $V_ari->id;
                                    $rowsLine["type"]                   =  "old";
                                    $rowsLine["variation_value_id"]     =  $V_ari->variation_value_id;
                                    $rowsLine["value"]                  =  $V_ari->name;
                                    $rowsLine["sub_sku"]                =  $V_ari->sub_sku;
                                    $rowsLine["default_purchase_price"] =  round($V_ari->default_purchase_price,2);
                                    $rowsLine["dpp_inc_tax"]            =  round($V_ari->dpp_inc_tax,2);
                                    $rowsLine["profit_percent"]         =  round($V_ari->profit_percent,2);
                                    $rowsLine["default_sell_price"]     =  round($V_ari->default_sell_price,2);
                                    $rowsLine["sell_price_inc_tax"]     =  round($V_ari->sell_price_inc_tax,2);
                                    foreach($V_ari->media as $media){
                                        $rowsLine["image"] = $media->display_url ;  
                                    }
                                    if(count($V_ari->media)==0){$rowsLine["image"] = "" ;}
                                    $rows[] = $rowsLine;
                                }
                            }
                            $finalVariable[] = [
                                "table_id"              => $listOfTable_id[$keys],
                                "type"                  => "old",
                                "name"                  => $listOfTable_name[$keys],
                                "variation_template_id" => $var,
                                "variations"            => $rows
                                ] ;
                        }
                        
                    $ProductVariation = $finalVariable;
                }
            // ....................................................
            // .**combo**..........................................
                if($product->type == "combo"){

                    // ..............
                    // ..............
                    $rowCs = [];
                    $product_details                 = \App\ProductVariation::where('product_id', $id)->with(['variations', 'variations.media'])->first();
                    $combo_variations                = $productUtil->__getComboProductDetails($product_details['variations'][0]->combo_variations,$product->business_id);
                    $variation_id                    = $product_details['variations'][0]->id;
                    $profit_percent                  = $product_details['variations'][0]->profit_percent;
                    $tax_amount                         =  ($product->product_tax)?$product->product_tax->amount:0;
                    $rowsLine["name"]                     =  "";
                    $rowsLine["composition_variation_id"] =  "";
                    $rowsLine["quantity"]                 =  0;
                    $rowsLine["unit"]                     =  "";
                    $rowsLine["purchase_price_exc"]       =  "";
                    $rowsLine["total_amount"]             =  0;
                    $rowsLine["all_unit"]                 =  [];
                    $rowsLine["initial"]                  =  true;
                    $rowsLine["unit_quantity"]            =  1;
                    $rowCs[] = $rowsLine;
                    foreach($combo_variations as  $V_ari){
                        if(isset($V_ari['variation'])){
                            
                            $subUnit                              =  Product::sub_units_product($V_ari['variation']['product']);
                            $rowsLine                             =  [];
                            $rowsLine["name"]                     =  $V_ari['variation']['product']->name ."-".  $V_ari['variation']->sub_sku;
                            // $rowsLine["sub_sku"]                =  $V_ari['variation']->sub_sku;
                            $rowsLine["composition_variation_id"] =  \App\Variation::where("product_id",$V_ari['variation']['product']->id)->first()->id;
                            $rowsLine["quantity"]                 =  $V_ari['quantity'];
                            $rowsLine["unit"]                     =  $V_ari['variation']['product']->unit_id;
                            $rowsLine["purchase_price_exc"]       =  round($V_ari['variation']->default_purchase_price,2);
                            $rowsLine["total_amount"]             =  round(($V_ari['variation']->default_purchase_price * $V_ari['quantity'] * $V_ari['multiplier']),2);
                            $rowsLine["all_unit"]                 =  $subUnit;
                            $rowsLine["initial"]                  =  false;
                            $rowsLine["unit_quantity"]            =  ($V_ari['variation']['product']->unit->base_unit_multiplier == null)?1:$V_ari['variation']['product']->unit->base_unit_multiplier;
                              foreach($V_ari['variation']->media as $media){
                                // $rowsLine["image"] = $media->display_url ;  
                            }
                            $rowCs[] = $rowsLine;
                        }
                    }
                    $item_level_purchase_price_total  = round($product->variations->first()->default_purchase_price,2);
                    $purchase_price_inc_tax           = round(((($product->variations->first()->default_purchase_price * $tax_amount)/100) + $product->variations->first()->default_purchase_price),2);
                    $profit_percent                   = round($product->variations->first()->profit_percent,2);
                    $selling_price                    = round((($product->variations->first()->sell_price_inc_tax * 100)/(100+$tax_amount)),2); 
                    $selling_price_inc_tax            = round($product->variations->first()->sell_price_inc_tax,2);
                    $combo_variation_id               = $product->variations->first()->id;
                    $ProductCombo[]                   = [
                            "item_level_purchase_price_total" => $item_level_purchase_price_total,
                            "purchase_price_inc_tax"          => $purchase_price_inc_tax ,
                            "profit_percent"                  => $profit_percent,
                            "selling_price"                   => $selling_price,
                            "selling_price_inc_tax"           => $selling_price_inc_tax,
                            "p_id"                            => $product->id,
                            "combo_variation_id"              => $combo_variation_id,
                            "rows"                            => $rowCs
                    ];
                        
                    
                }
            // ....................................................
            // .**location**.......................................
                if(count($product->product_locations) > 0){
                    $product_locations =  $product->product_locations->pluck('id')->toArray() ; 
                }else{
                    $product_locations = [];
                }
            // ....................................................
            // .**RACKS**..........................................
                $rack_details             = $productUtil->getRackDetails($user->business_id, $id);
                $rack                     = [];
                $rackLine                 = [];
                $compare                  = [];
                $compareFinal             = [];
                $Alllocation              = \App\BusinessLocation::get();
                foreach($rack_details as $rd){
                    $rackLine1             = [];
                    $location              = \App\BusinessLocation::find($rd["location_id"]);
                    $rackLine["id"]        = ($location != null)?$location->id:"" ;
                    $compare[]             = $location->id;
                    $rackLine["name"]      = ($location != null)?$location->name:"" ;
                    $rackLine1[]["rack"]     = $rd["rack"] ;
                    $rackLine1[]["row"]      = $rd["row"] ;
                    $rackLine1[]["position"] = $rd["position"] ;
                    $rackLine["value"]     = $rackLine1;
                    $rackLine["type"]      = "old";
                    $rack[] = $rackLine;
                }
                foreach($Alllocation as $oneLocation){
                    if(!in_array($oneLocation->id,$compare)){
                        $rackLine1             = [];
                        $rackLine["id"]          = $oneLocation->id;
                        $rackLine["name"]        = $oneLocation->name ;
                        $rackLine1[]["rack"]     = "" ;
                        $rackLine1[]["row"]      = "" ;
                        $rackLine1[]["position"] = "" ;
                        $rackLine["value"]     = $rackLine1;
                        $rackLine["type"]      = "new";
                        $rack[] = $rackLine;
                    }
                }
                $rackList                 = $rack ;
            // ....................................................
            // .**TablePrice**.....................................
                $units                   = \App\Models\ProductPrice::where("product_id",$product->id)->whereNotNull("unit_id")->select("unit_id")->groupBy("unit_id")->get();   
                foreach($units as $key => $value){
                $lineUnits[]         = $value;
                $product_price       = \App\Models\ProductPrice::where("product_id",$product->id)->where("unit_id",$value->unit_id)->whereNotNull("unit_id")->get();   
                    foreach($product_price as $ky => $val){
                        $lineP                  = [];
                        $lineP[$val->unit_id]   = [
                            "id"                        => $ky+1,
                            "unit_id"                   => $val->unit_id,
                            "value"                     => $val->name,
                            "single_dpp"                => round($val->default_purchase_price,2),
                            "single_dpp_in_tax"         => round($val->dpp_inc_tax,2),
                            "profit_percent"            => round($val->profit_percent,2),
                            "single_dsp"                => round($val->default_sell_price,2),
                            "single_dsp_inc_tax"        => round($val->sell_price_inc_tax,2),
                        ];
                        $linePrices[] = $lineP;
                    }
                }
               
                $list_of_units     = [];$list_of_units_sub=[];
                $list_of_units[]   =  $product->unit_id;
                $listOfSub         =  ( $product->sub_unit_ids != null )?((count($product->sub_unit_ids)>0)?$product->sub_unit_ids:[]):[] ;
                foreach($listOfSub as $item){
                    $list_of_units_sub[] = intVal($item);
                    $list_of_units[]     = intVal($item);
                }
                $prices_names      = \App\Models\ProductPrice::whereNull("product_id")->get();
                $emptyTable        =  [];
                $listPricesNames   = [];
                foreach($prices_names as $i){
                    $listPricesNames[] = $i->name  ;
                }
                 
                // $listPricesNames   =  ["Default Price","Whole Price","Retail Price","Minimum Price","Last Price","ECM Before Price","ECM After Price","Custom Price 1","Custom Price 2","Custom Price 3"]; 
                $emptyTable[] = [
                            "id"                        => 1,
                            "unit_id"                   => null,
                            "value"                     => "Default Price",
                            "single_dpp"                => 0,
                            "single_dpp_in_tax"         => 0,
                            "profit_percent"            => 0,
                            "single_dsp"                => 0,
                            "single_dsp_inc_tax"        => 0,  
                    ];
                foreach($listPricesNames as $k => $valuses){
                    $emptyTable[] = [
                            "id"                        => $k+2,
                            "unit_id"                   => null,
                            "value"                     => $valuses,
                            "single_dpp"                => 0,
                            "single_dpp_in_tax"         => 0,
                            "profit_percent"            => 0,
                            "single_dsp"                => 0,
                            "single_dsp_inc_tax"        => 0,  
                    ];
                }
                $FinalListOFArray  =  [];
                foreach($list_of_units as $key => $idItem){
                    $listOFArray  =  [];
                    foreach($linePrices as $ke => $ii){
                        foreach($ii as $e => $i){
                            if($e == $idItem){
                                $listOFArray[]=$i;
                            }
                        }
                    }
                    if($key == 0){
                        $FinalListOFArray["tableData"] = $listOFArray;
                    }elseif($key == 1){
                        $FinalListOFArray["tableDataChildOne"] = $listOFArray;
                    }else{
                        $FinalListOFArray["tableDataChildTwo"] = $listOFArray;
                    }
                }
                $object =json_decode(json_encode($FinalListOFArray));
               $imagesOld= [];
               $imagesOld[]=["oldImages"=>$listM];
               
            // ....................................................
            // .Final Data.......-->...
           
                $line = [
                    "id"                        => $product->id,
                    "name"                      => $product->name,
                    "code"                      => $product->sku,
                    "code2"                     => $product->sku2,
                    "barcode_type"              => $product->barcode_type,
                    "unit_id"                   => $product->unit_id,
                    "sub_unit_id"               => $list_of_units_sub,
                    "brand_id"                  => $product->brand_id,
                    "category_id"               => $product->category_id,
                    "sub_category_id"           => $product->sub_category_id,
                    "enable_stock"              => $product->enable_stock,
                    "alert_quantity"            => $product->alert_quantity,
                    "warranty_id"               => $product->warranty_id,
                    "show_more_price"           => isset($object->tableDataChildOne)?1:0,
                    "short_description"         => $product->product_description,
                    "long_description"          => $product->full_description,
                    "location"                  => $product_locations, 
                    "productImage"              => $product->image_url, 
                    "productmultipleimages"     => $imagesOld, 
                    "productbrochure"           => ($product->media)?(($product->media->first())?$product->media->first()->display_url:""):"",
                    "productvideo"              => $vedS,
                    "expiry_period_type"        => $product->expiry_period_type,
                    "not_for_sale"              => boolVal($product->not_for_selling)   ,
                    "expiry_period"             => $product->expiry_period,
                    "weight"                    => $product->weight,
                    "custom_field_1"            => $product->product_custom_field1,
                    "custom_field_2"            => $product->product_custom_field2,
                    "custom_field_3"            => $product->product_custom_field3,
                    "custom_field_4"            => $product->product_custom_field4,
                    "product_type"              => $product->type,
                    "positionDetailsValue"      => $rackList,
                    "tax"                       => ($product->product_tax)?($product->product_tax->amount/100):0,
                    "tax_id"                    => $product->tax,
                    "tax_type"                  => $product->tax_type,
                    "created_by"                => $product->created_by,
                    "enable_sr_no"              => $product->enable_sr_no,
                    "product_variation"         => $ProductVariation,
                    "product_compo"             => $ProductCombo,
                    // ................................................ \\
                    "business_id"               => $product->business_id,
                    
                    "tableData"                 => isset($object->tableData)?$object->tableData:$emptyTable, 
                    "tableDataChildOne"         => isset($object->tableDataChildOne)?$object->tableDataChildOne:$emptyTable ,
                    "tableDataChildTwo"         => isset($object->tableDataChildTwo)?$object->tableDataChildTwo:$emptyTable ,
                ];
                $list["require"]         = $require;
                $list["info"]            = $line;
                // ........................
            return $list;
        }catch(Exception $e){
            return false;
        } 
    }
    // **5** STORE PRODUCT
    public static function storeProduct($user,$data,$request) {
        try{
            \DB::beginTransaction();
            $data["business_id"] = $user->business_id;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Product::where("name",$data["name"])->where("business_id",$data["business_id"])->first();
                if($old)   {   return "Can't Add This Product ,Because This Name is Already Exist !.";  }
            }
            if(!empty($data["code"]) && $data["code"] != ""){
                $old             = \App\Product::where("sku",$data["code"])->where("business_id",$data["business_id"])->first();
                if($old)   {   return "Can't Add This Product ,Because This Code is Already Exist !.";  }
            }
            if(!empty($data["code2"]) && $data["code2"] != ""){
                $old             = \App\Product::where("sku2",$data["code2"])->where("business_id",$data["business_id"])->first();
                if($old)   {  return "Can't Add This Product ,Because This The Second Code  is Already Exist !.";  }
            }
           
            $output              = Product::createNewProduct($user,$data,$request);
            if($output == false){ return "Failed"; }else{ $output = "Success" ;} 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **6** UPDATE PRODUCT
    public static function updateProduct($user,$data,$request,$id) {
        try{
           
            \DB::beginTransaction();
            $data["business_id"] = $user->business_id;
            if(!empty($data["name"]) && $data["name"] != ""){
                $old             = \App\Product::where("name",$data["name"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old)   {   return "Can't Add This Product ,Because This Name is Already Exist !.";  }
            }
            if(!empty($data["code"]) && $data["code"] != ""){
                $old             = \App\Product::where("sku",$data["code"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old)   {   return "Can't Add This Product ,Because This Code is Already Exist !.";  }
            }
            if(!empty($data["code2"]) && $data["code2"] != ""){
                $old             = \App\Product::where("sku2",$data["code2"])->where("id","!=",$id)->where("business_id",$data["business_id"])->first();
                if($old)   {  return "Can't Add This Product ,Because This The Second Code  is Already Exist !.";  }
            }
         
            $output              = Product::updateOldProduct($user,$data,$request,$id);
            if($output == false){ return "Failed"; }else{ $output = "Success" ;} 
            \DB::commit();
            return $output;
        }catch(Exception $e){
            return false;
        }
    }
    // **7** DELETE PRODUCT
    public static function deleteProduct($user,$id) {
        try{
            \DB::beginTransaction();
            $business_id     = $user->business_id;
            $product         = \App\Product::find($id);
            if(!$product) { return false; }
            $check           = GlobalUtil::check("product",$id);
            if( $check != null) {
                return "related";
            }else {
                $variation          = \App\Variation::where("product_id",$id)->get();
                $VariationL         = \App\VariationLocationDetails::where("product_id",$id)->first();
                $product_variation  = \App\ProductVariation::where("product_id",$id)->first();
                $prices             = \App\Models\ProductPrice::where("product_id",$id)->get();
                foreach($variation as $i){
                    $i->delete();
                }
                foreach($prices as $ie){
                    $ie->delete();
                }
                if($product_variation){
                    $product_variation->delete();
                }
                if($VariationL){
                    $VariationL->delete();
                }
                if($product){
                    $product->delete();
                }
                \DB::commit();
            }
            return "saved";
        }catch(Exception $e){
            return "notFound";
        }
    }
    // **8** GALLERY
    public static function ProductGallery($user,$request) {
        try{
            $page         = $request->query('page', 1);
            $skip         = $request->query('skip', 0);
            $limit        = $request->query('limit', 25);
            $skpP         = ($page-1)*$limit;
            $list         = [];
            $list_all     = [];
            $count        = \App\Product::count();
            $totalPages   = ($limit != 0)?ceil($count / $limit):$count;
            $prevPage     = $page > 1 ? $page - 1 : null;
            $nextPage     = $page < $totalPages ? $page + 1 : null;
            if($limit == -1){
                $product = \App\Product::where("business_id",$user->business_id)->get();
            }else{
                $product = \App\Product::where("business_id",$user->business_id)->skip($skpP)->take($limit)->get();
            }
            if(count($product)==0) { return false; }
            foreach($product as $i){
                $current_qty = \App\Models\WarehouseInfo::where("product_id",$i->id)->sum("product_qty");
                $itemMove    = \App\Models\ItemMove::where("product_id",$i->id)->orderBy("date","desc")->orderBy("id","desc")->first(); 
                $list[]    = [
                    "id"          => $i->id,
                    "name"        => $i->name,
                    "image"       => ($i->image_url!="")?$i->image_url:"No Image",
                    "description" => $i->product_description,
                    "stock"       => $current_qty,
                    "cost"        => (!empty($itemMove))?round($itemMove->unit_cost,2):0 ,
                ];
            }
            $list_all["items"] = $list;
            $list_all["info"]  = [
                "totalRows"     => $count,
                'current_page'  => ($limit != -1)?intVal($page):"",
                'last_page'     => ($limit != -1)?intVal($totalPages):"",
                'limit'         => ($limit != -1)?intVal($limit):"all",
                'prev_page_url' => ($limit != -1)?($prevPage ? "/api/app/react/product-gallery/all?page=$prevPage" : null):"",
                'next_page_url' => ($limit != -1)?($nextPage ? "/api/app/react/product-gallery/all?page=$nextPage" : null):"",
        ];
            return $list_all;
        }catch(Exception $e){
            return false;
        }
    }
    // **9** INVENTORY
    public static function InventoryReport($user,$request) {
        try{
            $page                       = $request->query('page', 1);
            $skip                       = $request->query('skip', 0);
            $limit                      = $request->query('limit', 25);
            $skpP                       = ($page-1)*$limit;
            $list                       = [];
            $list_all                   = [];
            // ....FILTER SECTION.............................................................................................
            $product_type               = $request->query('product_type', null);//*
            $product_category           = $request->query('product_category', null);//*
            $product_sub_category       = $request->query('product_sub_category', null);//*
            $product_price              = $request->query('product_price', null);//*
            $product_available          = $request->query('product_available', null);//*
            $product_store              = $request->query('product_store', null);//*
            $product_main_store         = $request->query('product_main_store', null);//*
            $product_brand              = $request->query('product_brand', null);//*
            $product_until_date         = $request->query('product_until_date', null); //*
            // .......................................................................................................END....
            $product  = \App\Product::where("business_id",$user->business_id);
            // ..EXECUTE.FILTER......................................................................................START....
            if($product_type != null){
                $product->where("type",$product_type);
            }
            if($product_category != null){
                $product->where("category_id",$product_category);
            }
            if($product_sub_category != null){
                $product->where("sub_category_id",$product_sub_category);
            }
            if($product_brand != null){
                $product->where("brand_id",$product_brand);
            }
            // .......................................................................................................END....
            $count = count($product->get());
            if($limit != -1){
                $product->skip($skpP)->take($limit);
            }
            $product->get();
            $totalPages                      = ($limit != 0)?ceil($count / $limit):$count;
            $prevPage                        = $page > 1 ? $page - 1 : null;
            $nextPage                        = $page < $totalPages ? $page + 1 : null;
            if(count($product->get())==0) { return false; }
            $total_stock                     = 0 ; $total_cost                      = 0 ;
            $total_stock_available           = 0 ; $total_cost_available            = 0 ;
            $total_stock_not_available       = 0 ; $total_cost_not_available        = 0 ;
            $count_not_available             = 0 ; $count_available                 = 0 ;
            $total_stock_should_deliver      = 0 ; $total_stock_should_receive      = 0 ;
            $total_cost_should_deliver       = 0 ; $total_cost_should_receive       = 0 ;
            $count_should_deliver            = 0 ; $count_should_receive            = 0 ;
            $array_product_id                = []; $array_product_id_should_receive = [];
            $array_product_id_not_available  = []; $array_product_id_should_deliver = [];
            // ..............................................................................
            foreach($product->get() as $i){
                if($product_main_store != null){
                    $stores_child  = \App\Models\Warehouse::where("parent_id",$product_main_store)->pluck("id");
                    if(count($stores_child)==0) { return false; }
                }else{
                    $stores_child  = [];
                }
                if($product_store == null){
                    if(count($stores_child)>0){
                        $current_qty         = \App\Models\WarehouseInfo::where("product_id",$i->id)->whereIn("store_id",$stores_child)->sum("product_qty");  
                    }else{
                        $current_qty         = \App\Models\WarehouseInfo::where("product_id",$i->id)->sum("product_qty");  
                    }
                }else{
                    if(count($stores_child)>0){
                        $current_qty         = \App\Models\WarehouseInfo::where("product_id",$i->id)
                                                                ->whereHas("store",function($query) use($stores_child,$product_store){
                                                                    $query->whereIn("id",$stores_child);
                                                                    $query->where("id",$product_store);
                                                                })
                                                                ->sum("product_qty");  
                    }else{
                        $current_qty         = \App\Models\WarehouseInfo::where("product_id",$i->id)->where("store_id",$product_store)->sum("product_qty");  
                    }
                }
                
                if($product_until_date == null){
                    $itemMove                = \App\Models\ItemMove::where("product_id",$i->id)->orderBy("date","desc")->orderBy("id","desc")->first(); 
                }else{
                    $itemMove                = \App\Models\ItemMove::where("product_id",$i->id)->where("date","<=",$product_until_date)->whereOr("created_at","<=",$product_until_date)->orderBy("date","desc")->orderBy("id","desc")->first(); 
                }
                $card_product                = \App\Variation::where("product_id",$i->id)->first();
                (!empty($card_product))? $po = $card_product->default_sell_price : $po = 0 ;
                $price     = $po;
                $list[]    = [
                    "id"              => $i->id,
                    "name"            => $i->name,
                    "image"           => ($i->image_url!="")?$i->image_url:"No Image",
                    "description"     => $i->product_description,
                    "stock"           => $current_qty,
                    "cost"            => ( $product_price != null)?( ($product_price == "price")?(round($price,2)):((!empty($itemMove))?round($itemMove->unit_cost,2):0) ):0 ,
                    "total"           => ( $product_price != null)?( ($product_price == "price")?(round($price,2)):((!empty($itemMove))?round($itemMove->unit_cost * $current_qty,2):0) ):0 ,
                    "should_received" => \App\Product::between_purchase_recieve($i->id),
                    "should_delivery" => \App\Product::between_sell_deliver($i->id),
                ];
                $total_stock                           += $current_qty;
                $total_cost                            += ($current_qty *( (!empty($itemMove))?$itemMove->unit_cost:0 ));
                if( $current_qty > 0 ){
                    $array_product_id[]                 = [
                        "id"              => $i->id,
                        "name"            => $i->name,
                        "image"           => ($i->image_url!="")?$i->image_url:"No Image",
                        "description"     => $i->product_description,
                        "stock"           => $current_qty,
                        "cost"            => ( $product_price != null)?( ($product_price == "price")?(round($price,2)):((!empty($itemMove))?round($itemMove->unit_cost,2):0) ):0 ,
                        "total"           => ( $product_price != null)?( ($product_price == "price")?(round($price,2)):((!empty($itemMove))?round($itemMove->unit_cost * $current_qty,2):0) ):0 ,
                        "should_received" => \App\Product::between_purchase_recieve($i->id),
                        "should_delivery" => \App\Product::between_sell_deliver($i->id),
                    ];;
                    $total_stock_available  += $current_qty;
                    $total_cost_available   += ($current_qty *( (!empty($itemMove))?$itemMove->unit_cost:0 ));
                    $count_available++;
                }else{
                    
                    $array_product_id_not_available[]   = [
                        "id"              => $i->id,
                        "name"            => $i->name,
                        "image"           => ($i->image_url!="")?$i->image_url:"No Image",
                        "description"     => $i->product_description,
                        "stock"           => $current_qty,
                        "cost"            => ( $product_price != null)?( ($product_price == "price")?(round($price,2)):((!empty($itemMove))?round($itemMove->unit_cost,2):0) ):0 ,
                        "total"           => ( $product_price != null)?( ($product_price == "price")?(round($price,2)):((!empty($itemMove))?round($itemMove->unit_cost * $current_qty,2):0) ):0 ,
                        "should_received" => \App\Product::between_purchase_recieve($i->id),
                        "should_delivery" => \App\Product::between_sell_deliver($i->id),
                    ];
                    $total_stock_not_available   += $current_qty;
                    $total_cost_not_available    += ($current_qty *( (!empty($itemMove))?$itemMove->unit_cost:0 ));
                    $count_not_available++;
                }
                if(\App\Product::between_purchase_recieve($i->id) != 0){
                    $array_product_id_should_receive[]                 = [
                        "id"              => $i->id,
                        "name"            => $i->name,
                        "image"           => ($i->image_url!="")?$i->image_url:"No Image",
                        "description"     => $i->product_description,
                        "stock"           => $current_qty,
                        "cost"            => ( $product_price != null)?( ($product_price == "price")?(round($price,2)):((!empty($itemMove))?round($itemMove->unit_cost,2):0) ):0 ,
                        "total"           => ( $product_price != null)?( ($product_price == "price")?(round($price,2)):((!empty($itemMove))?round($itemMove->unit_cost * $current_qty,2):0) ):0 ,
                        "should_received" => \App\Product::between_purchase_recieve($i->id),
                        "should_delivery" => \App\Product::between_sell_deliver($i->id),
                    ];
                    $total_stock_should_receive  += $current_qty;
                    $total_cost_should_receive   += ($current_qty *( (!empty($itemMove))?$itemMove->unit_cost:0 ));
                    $count_should_receive++;
                    
                }
                if(\App\Product::between_sell_deliver($i->id) != 0){
                        $array_product_id_should_deliver[]   = [
                            "id"              => $i->id,
                            "name"            => $i->name,
                            "image"           => ($i->image_url!="")?$i->image_url:"No Image",
                            "description"     => $i->product_description,
                            "stock"           => $current_qty,
                            "cost"            => ( $product_price != null)?( ($product_price == "price")?(round($price,2)):((!empty($itemMove))?round($itemMove->unit_cost,2):0) ):0 ,
                            "total"           => ( $product_price != null)?( ($product_price == "price")?(round($price,2)):((!empty($itemMove))?round($itemMove->unit_cost * $current_qty,2):0) ):0 ,
                            "should_received" => \App\Product::between_purchase_recieve($i->id),
                            "should_delivery" => \App\Product::between_sell_deliver($i->id),
                        ];;
                        $total_stock_should_deliver  += $current_qty;
                        $total_cost_should_deliver   += ($current_qty *( (!empty($itemMove))?$itemMove->unit_cost:0 ));
                        $count_should_deliver++;
                }
            }
            // ..............................................................................
            if($product_available != null && $product_available == "available"){
                $count_available                 = count($array_product_id);
                $available_totalPages            = ($limit != 0)?ceil($count_available / $limit):$count_available;
                $available_prevPage              = $page > 1 ? $page - 1 : null;
                $available_nextPage              = $page < $available_totalPages ? $page + 1 : null;
            }elseif($product_available != null && $product_available == "not_available"){
                $count_not_available             = count($array_product_id_not_available);
                $not_available_totalPages        = ($limit != 0)?ceil($count_not_available / $limit):$count_not_available;
                $not_available_prevPage          = $page > 1 ? $page - 1 : null;
                $not_available_nextPage          = $page < $not_available_totalPages ? $page + 1 : null;
            }elseif($product_available != null && $product_available == "should_receive"){
                $count_should_receive            = count($array_product_id_should_receive);
                $should_receive_totalPages       = ($limit != 0)?ceil($count_should_receive / $limit):$count_should_receive;
                $should_receive_prevPage         = $page > 1 ? $page - 1 : null;
                $should_receive_nextPage         = $page < $should_receive_totalPages ? $page + 1 : null;
            }elseif($product_available != null && $product_available == "should_deliver"){
                $count_should_deliver            = count($array_product_id_should_deliver);
                $should_deliver_totalPages       = ($limit != 0)?ceil($count_should_deliver / $limit):$count_should_deliver;
                $should_deliver_prevPage         = $page > 1 ? $page - 1 : null;
                $should_deliver_nextPage         = $page < $should_deliver_totalPages ? $page + 1 : null;
            }
            // ..............................................................................
            if($product_available != null){
                if($product_available == "available"){
                    $RESULT            = $array_product_id;
                    $COST_RESULT       = round($total_cost_available,2);
                    $STOCK_RESULT      = $total_stock_available;
                    $COUNT_RESULT      = $count_available;
                    $PREV_PAGE_RESULT  = $available_prevPage;
                    $NEXT_PAGE_RESULT  = $available_nextPage;
                    $TOTAL_PAGE_RESULT = $available_totalPages;
                }elseif($product_available == "not_available"){
                    $RESULT            = $array_product_id_not_available;
                    $COST_RESULT       = round($total_cost_not_available,2);
                    $STOCK_RESULT      = $total_stock_not_available;
                    $COUNT_RESULT      = $count_not_available;
                    $PREV_PAGE_RESULT  = $not_available_prevPage;
                    $NEXT_PAGE_RESULT  = $not_available_nextPage;
                    $TOTAL_PAGE_RESULT = $not_available_totalPages;
                }elseif($product_available == "should_receive"){
                    $RESULT            = $array_product_id_should_receive;
                    $COST_RESULT       = round($total_stock_should_receive,2);
                    $STOCK_RESULT      = $total_cost_should_receive;
                    $COUNT_RESULT      = $count_should_receive;
                    $PREV_PAGE_RESULT  = $should_receive_prevPage;
                    $NEXT_PAGE_RESULT  = $should_receive_nextPage;
                    $TOTAL_PAGE_RESULT = $should_receive_totalPages;
                }elseif($product_available == "should_deliver"){
                    $RESULT            = $array_product_id_should_deliver;
                    $COST_RESULT       = round($total_stock_should_deliver,2);
                    $STOCK_RESULT      = $total_cost_should_deliver;
                    $COUNT_RESULT      = $count_should_deliver;
                    $PREV_PAGE_RESULT  = $should_deliver_prevPage;
                    $NEXT_PAGE_RESULT  = $should_deliver_nextPage;
                    $TOTAL_PAGE_RESULT = $should_deliver_totalPages;
                }else{
                    $RESULT            = $list;
                    $COST_RESULT       = round($total_cost,2);
                    $STOCK_RESULT      = $total_stock;
                    $COUNT_RESULT      = $count;
                    $PREV_PAGE_RESULT  = $prevPage;
                    $NEXT_PAGE_RESULT  = $nextPage;
                    $TOTAL_PAGE_RESULT = $totalPages;
                }
            }else{
                $RESULT            = $list;
                $COST_RESULT       = round($total_cost,2);
                $STOCK_RESULT      = $total_stock;
                $COUNT_RESULT      = $count;
                $PREV_PAGE_RESULT  = $prevPage;
                $NEXT_PAGE_RESULT  = $nextPage;
                $TOTAL_PAGE_RESULT = $totalPages;
            }
            // .............................................................................
            $list_all["items"]      =  $RESULT;
            $list_all["info"]       =  [
                    "total_stock"   => $STOCK_RESULT,
                    "total_cost"    => $COST_RESULT,
                    "totalRows"     => $COUNT_RESULT,
                    'current_page'  => ($limit != -1)?intVal($page):"",
                    'last_page'     => ($limit != -1)?intVal($TOTAL_PAGE_RESULT):"",
                    'limit'         => ($limit != -1)?intVal($limit):"all",
                    'prev_page_url' => ($limit != -1)?($PREV_PAGE_RESULT ? "/api/app/react/product-gallery/all?page=$PREV_PAGE_RESULT" : null):"",
                    'next_page_url' => ($limit != -1)?($NEXT_PAGE_RESULT ? "/api/app/react/product-gallery/all?page=$NEXT_PAGE_RESULT" : null):"", 
            ];
            // ...............................................................................
            return $list_all;
        }catch(Exception $e){
            return false;
        }  
    }

    // ****** MAIN FUNCTIONS 
    // **1** CREATE PRODUCT
    public static function createNewProduct($user,$data,$request) {
        try{
             
            $productUtil = new ProductUtil();
            // ....VALIDATION......................................................
                if(isset($request->vedio) && !is_string($request->video)){
                    $request->validate([
                        'video'  => 'required|mimes:mp4,mov,avi|max:25480', // Max 25MB
                    ]);
                }
            // ....SKU......................................................
                if(isset($data['code'])){
                    if (empty(trim($data["code"]))) {
                        $sku_prefix  = \App\Business::where('id', $user->business_id)->value('sku_prefix');
                        $product_sku = $sku_prefix . str_pad($data["code"], 4, '0', STR_PAD_LEFT);
                    }else{
                        $product_sku = trim($data["code"]);
                    }
                }else{
                    $sku_prefix  = \App\Business::where('id', $user->business_id)->value('sku_prefix');
                    $product_sku = $sku_prefix . str_pad(null, 4, '0', STR_PAD_LEFT);
                }
                 
            // ....SAVE PRODUCT........................................................START..
                $product                         = new \App\Product();
                $product->name                   = isset($data["name"])?$data["name"]:"";
                $product->business_id            = isset($data["business_id"])?$data["business_id"]:1;
                $product->type                   = isset($data["product_type"])?$data["product_type"]:"single";
                $product->unit_id                = isset($data["unit_id"])?$data["unit_id"]:null;
                $product->sub_unit_ids           = isset($request->sub_unit_id)?$request->sub_unit_id:null;
                $product->brand_id               = isset($data["brand_id"])?$data["brand_id"]:null;
                $product->category_id            = isset($data["category_id"])?$data["category_id"]:null;
                $product->sub_category_id        = isset($data["sub_category_id"])?$data["sub_category_id"]:null;
                $product->tax                    = isset($data["tax_id"])?$data["tax_id"]:null;
                $product->tax_type               = "exclusive";
                $product->enable_stock           = isset($data["enable_stock"])?(($data["enable_stock"] == true)?1:0):0;
                if(isset($data["enable_stock"]) && ($data["enable_stock"] == 1 || $data["enable_stock"] == true)){
                    $product->alert_quantity         = isset($data["alert_quantity"])?$data["alert_quantity"]:null;
                }
                $product->sku                    = $product_sku;
                $product->sku2                   = isset($data["code2"])?$data["code2"]:null;
                $product->barcode_type           = isset($data["barcode_type"])?$data["barcode_type"]:"C39";
                $product->expiry_period          = isset($data["expiry_period"])?$data["expiry_period"]:null;
                $product->expiry_period_type     = isset($data["expiry_period_type"])?$data["expiry_period_type"]:null;
                $product->enable_sr_no           = isset($data["enable_sr_no"])?$data["enable_sr_no"]:0;
                $product->weight                 = isset($data["weight"])?$data["weight"]:null;
                $product->product_custom_field1  = isset($data["custom_field_1"])?$data["custom_field_1"]:null;
                $product->product_custom_field2  = isset($data["custom_field_2"])?$data["custom_field_2"]:null;
                $product->product_custom_field3  = isset($data["custom_field_3"])?$data["custom_field_3"]:null;
                $product->product_custom_field4  = isset($data["custom_field_4"])?$data["custom_field_4"]:null;
                $product->product_description    = isset($data["description"])?$data["description"]:null;
                $product->created_by             = $user->id;
                $product->warranty_id            = isset($data["warranty_id"])?$data["warranty_id"]:null;
                $product->not_for_selling        = isset($data["not_for_sale"])?$data["not_for_sale"]:0;
                $product->full_description       = isset($data["full_description"])?$data["full_description"]:0;
             
            
            // ...............................................................................
                if($request->hasFile("image") != null || $request->hasFile("image") != false){
                    $dir_name =  config('constants.product_img_path');
                    if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                        $data_a        = getimagesize($request->file("image"));
                        $width       = $data_a[0];
                        $height      = $data_a[1];
                        $half_width  = $width/2;
                        $half_height = $height/2;
                        $imgs = \Image::make($request->file("image"))->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        if ($imgs->save(base_path("public/uploads/img/$new_file_name"),20)) {
                            $uploaded_file_name        = $new_file_name;
                            $product->image            = $uploaded_file_name;
                        }
                    }
                }
                

            // ...............................................................................
                if ($request->hasFile("video") != null || $request->hasFile("video") != false) {
                    $array_video            = [];
                    $path_name              = \time() . $request->file('video')->getClientOriginalName();
                    $video                  = $request->file('video');
                    $path                   = $video->store('vedios','public');
                    $array_video[]          = $path;
                    $product->product_vedio = json_encode($array_video) ;
                }
                $product->save();
            // ...........................................................................END.
 
            // ....LOCATIONS....................................................
              
                $product_locations      = $request->product_locations;
                if (!empty($product_locations)) {
                    $product->product_locations()->sync($product_locations);
                }

            // ....MEDIA........................................................
                if($request->brochure != null && $request->brochure != "" ){
                    Media::uploadMedia($product->business_id, $product, $request, 'brochure', true);
                }
                      $location_id = [];

            // ....VARIATION.....................................................
            if($request->product_locations != null && $request->product_locations != "" ){
                foreach($request->product_locations as $key => $first){
                        $location_id  = $first;
                }
            }
                
                
                
            //Add product racks details.
                $product_racks = $request->get('product_racks', null);
                if (!empty($product_racks)) {
                     $racks_list  = [];
                     $list_row    = [];
                   
                    foreach(json_decode($request->product_racks) as $key => $value) {
                        
                        $list_row[$value->id] = [];
                        foreach($value->value as $k => $val){
                            if($k==0){
                                
                                $list_row[$value->id]["rack"]=$val->rack;
                            }
                            if($k==1){
                                $list_row[$value->id]["row"]=$val->row;
                                
                            }
                            if($k==2){
                                $list_row[$value->id]["position"]=$val->position;
                                
                            }
                        }
                        $racks_list[$value->id] = $list_row[$value->id];
                    }
                    $product_racks =  $racks_list ;
                        
                    $productUtil->addRackDetails($user->business_id, $product->id, $product_racks);
                }
            // .......................................
                if ($product->type == 'single') {
                    // ....PRICES..................
                    // ........1. *
                             
                        if(isset($data['table_price_1'])){
                            foreach(json_decode($data['table_price_1']) as $data1){
                             
                                GlobalUtil::oneRowPrice($product,$user,$data1);
                                if($data1->value == "default_price"){
                                     GlobalUtil::createSingleProductVariationPrices($product->id, $product->sku, $data1->single_dpp, $data1->single_dpp_inc_tax ,$data1->profit_percent  , $data1->single_dsp , $data1->single_dsp_inc_tax  ,$product->sku2,null,$data1->unit_id);
                                }
                            }
                        }
                    // .......... *
                    // ........2. *
                        if(isset($data['table_price_2'])){
                            foreach(json_decode($data['table_price_2']) as $data2){
                                GlobalUtil::oneRowPrice($product,$user,$data2);
                            }
                        }
                    // .......... *
                    // ........3. *
                        if(isset($data['table_price_3'])){
                            foreach(json_decode($data['table_price_3']) as $data3){
                                GlobalUtil::oneRowPrice($product,$user,$data3);
                            }                    
                        }
                    // .......... *
                    $variation                                  = \App\Variation::where("product_id",$product->id)->first();
                    $variation_location_d                       = new \App\VariationLocationDetails();
                    $variation_location_d->variation_id         = $variation->id;
                    $variation_location_d->product_id           = $product->id;
                    $variation_location_d->location_id          = $location_id;
                    $variation_location_d->product_variation_id = $variation->id;
                    $variation_location_d->qty_available        = 0;
                    $variation_location_d->save();
                   if($request->hasFile("more_image") != null || $request->hasFile("more_image") != false ){
                        
                        Media::uploadMedia($product->business_id, $variation, $request, 'more_image');
                    }
                } elseif ($product->type == 'variable') {
                    if (!empty($request->input('product_variation'))) {
                        $input_variations          = $request->input('product_variation');
                        GlobalUtil::createVariableProductVariations($product->id, $input_variations);
                    }
                } elseif ($product->type == 'combo') {
                    //Create combo_variations array by combining variation_id and quantity.
                    $combo_variations              = [];
                    
                    if (!empty($request->product_compo)) {
                        $dataCombo                 = json_decode($request->product_compo);
                        if(count($dataCombo)>0){
                            if($dataCombo[0]){
                                foreach ($dataCombo[0]->rows as $key => $value) {
                                    if($key != 0){
                                            $combo_variations[]    = [
                                                'variation_id' => $value->composition_variation_id,
                                                'quantity'     => $value->quantity,
                                                'unit_id'      => $value->unit
                                            ];
                                         
                                    }
                                }
                            GlobalUtil::createSingleProductVariation($product->id, $product->sku, $dataCombo[0]->item_level_purchase_price_total, $dataCombo[0]->purchase_price_inc_tax, $dataCombo[0]->profit_percent, $dataCombo[0]->selling_price, $dataCombo[0]->selling_price_inc_tax, $combo_variations);
                            }
                        }
                    }
                }
            // .................................................................. 
             
            return true; 
        }catch(Exception $e){
            return false;
        }
    }
    // **2** UPDATE PRODUCT
    public static function updateOldProduct($user,$data,$request,$id) {
        try{
           
            $productUtil = new ProductUtil();
            // ....VALIDATION...................................................................
                if(isset($request->video) && !is_string($request->video) ){
                    $request->validate([
                        'video'  => 'required|mimes:mp4,mov,avi|max:25480', // Max 25MB
                    ]);
                }
            // ....SKU..........................................................................
                if (empty(trim($data["code"]))) {
                    $sku_prefix  = \App\Business::where('id', $user->business_id)->value('sku_prefix');
                    $product_sku = $sku_prefix . str_pad($data["code"], 4, '0', STR_PAD_LEFT);
                }else{
                    $product_sku = trim($data["code"]);
                }
             
            // ....UPDATE PRODUCT........................................................START..
                $product                         = \App\Product::find($id);
                $units                   = \App\Models\ProductPrice::where("product_id",$product->id)->whereNotNull("unit_id")->select("unit_id")->groupBy("unit_id")->get();   
                $linePrices = [];
                foreach($units as $key => $value){
                    $lineUnits[]             = $value;
                    $product_price           = \App\Models\ProductPrice::where("product_id",$product->id)->where("unit_id",$value->unit_id)->whereNotNull("unit_id")->get();   
                    foreach($product_price as $ky => $val){
                        $lineP                  = [];
                        $lineP[$val->unit_id]   = [
                            "id"                        => $ky+1,
                            "unit_id"                   => $val->unit_id,
                            "value"                     => $val->name,
                            "single_dpp"                => round($val->default_purchase_price,2),
                            "single_dpp_in_tax"         => round($val->dpp_inc_tax,2),
                            "profit_percent"            => round($val->profit_percent,2),
                            "single_dsp"                => round($val->default_sell_price,2),
                            "single_dsp_inc_tax"        => round($val->sell_price_inc_tax,2),
                        ];
                        $linePrices[] = $lineP;
                    }
                }
                $list_of_units                   = [];$list_of_units_sub=[];
                $list_of_units[]                 =  $product->unit_id;
                $listOfSub                       =  ( $product->sub_unit_ids != null )?((count($product->sub_unit_ids)>0)?$product->sub_unit_ids:[]):[] ;
                foreach($listOfSub as $item){
                    $list_of_units_sub[]         = intVal($item);
                    $list_of_units[]             = intVal($item);
                }
                $FinalListOFArray  =  [];
                foreach($list_of_units as $key => $idItem){
                    $listOFArray  =  [];
                    foreach($linePrices as $ke => $ii){
                        foreach($ii as $e => $i){
                            if($e == $idItem){
                                $listOFArray = $i["unit_id"];
                            }
                        }
                    }
                    if($key == 0){
                        $FinalListOFArray["tableData"] = $listOFArray;
                    }elseif($key == 1){
                        $FinalListOFArray["tableDataChildOne"] = $listOFArray;
                    }else{
                        $FinalListOFArray["tableDataChildTwo"] = $listOFArray;
                    }
                }
              
                $object                          = json_decode(json_encode($FinalListOFArray)); 
                $product->name                   = isset($data["name"])?$data["name"]:"";
                $product->business_id            = isset($data["business_id"])?$data["business_id"]:1;
                $product->type                   = isset($data["product_type"])?$data["product_type"]:"single";
                $product->unit_id                = isset($data["unit_id"])?$data["unit_id"]:null;
                $product->sub_unit_ids           = isset($request->sub_unit_id)? $request->sub_unit_id :null;
                $product->brand_id               = isset($data["brand_id"])?$data["brand_id"]:null;
                $product->category_id            = isset($data["category_id"])?$data["category_id"]:null;
                $product->sub_category_id        = isset($data["sub_category_id"])?$data["sub_category_id"]:null;
                $product->tax                    = isset($data["tax_id"])?$data["tax_id"]:null;
                $product->tax_type               = "exclusive";
                $product->enable_stock           = isset($data["enable_stock"])?$data["enable_stock"]:0;
                $product->alert_quantity         = isset($data["alert_quantity"])?$data["alert_quantity"]:null;
                $product->sku                    = $product_sku;
                $product->sku2                   = isset($data["code2"])?$data["code2"]:null;
                $product->barcode_type           = isset($data["barcode_type"])?$data["barcode_type"]:"C39";
                $product->expiry_period          = isset($data["expiry_period"])?$data["expiry_period"]:null;
                $product->expiry_period_type     = isset($data["expiry_period_type"])?$data["expiry_period_type"]:null;
                $product->enable_sr_no           = isset($data["enable_sr_no"])?$data["enable_sr_no"]:0;
                $product->weight                 = isset($data["weight"])?$data["weight"]:null;
                $product->product_custom_field1  = isset($data["custom_field_1"])?$data["custom_field_1"]:null;
                $product->product_custom_field2  = isset($data["custom_field_2"])?$data["custom_field_2"]:null;
                $product->product_custom_field3  = isset($data["custom_field_3"])?$data["custom_field_3"]:null;
                $product->product_custom_field4  = isset($data["custom_field_4"])?$data["custom_field_4"]:null;
                $product->product_description    = isset($data["description"])?$data["description"]:null;
                $product->created_by             = $user->id;
                $product->warranty_id            = isset($data["warranty_id"])?$data["warranty_id"]:null;
                $product->not_for_selling        = (isset($data["not_for_sale"]) && $data["not_for_sale"] == "true") ?1:0;
                $product->full_description       = isset($data["full_description"])?$data["full_description"]:0;
            
            // .................................................................................
                if($request->hasFile("image") != null || $request->hasFile("image") != false){
                    $dir_name =  config('constants.product_img_path');
                    if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                        $data_I          = getimagesize($request->file("image"));
                        $width         = $data_I[0];
                        $height        = $data_I[1];
                        $half_width    = $width/2;
                        $half_height   = $height/2;
                        $imgs = \Image::make($request->file("image"))->resize($half_width,$half_height); //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        if ($imgs->save(base_path("public/uploads/img/$new_file_name"),20)) {
                            $uploaded_file_name        = $new_file_name;
                            $product->image            = $uploaded_file_name;
                        }
                    }
                }

            // .................................................................................
                if ($request->hasFile("video") != null || $request->hasFile("video") != false) {
                    $array_video            = [];
                    $path_name              = \time() . $request->file('video')->getClientOriginalName();
                    $video                  = $request->file('video');
                    $path                   = $video->store('vedios','public');
                    $array_video[]          = $path;
                    $product->product_vedio = json_encode($array_video) ;
                }
                $product->update();

            // .............................................................................END.
            // ....LOCATIONS....................................................................
            
                $product_locations      = !empty($request->product_locations) ? $request->product_locations : [];
                $product->product_locations()->sync($product_locations);

            // ....MEDIA........................................................................
                if($request->brochure != null && $request->brochure != "" ){
                    Media::uploadMedia($product->business_id, $product, $request, 'brochure', true);
                }
            // ....VARIATION....................................................................
                foreach($request->product_locations as $key => $first){
                        $location_id  = $first;
                }
            // ....PRICES.......................................................................
            
                //Add product racks details.
                $product_racks = $request->get('product_racks', null);
                if (!empty($product_racks)) {
                     $racks_list1  = [];
                     $list_row1    = [];
                   
                    foreach(json_decode($request->product_racks) as $key => $value) {
                        
                        $list_row1[$value->id] = [];
                        foreach($value->value as $k => $val){
                            if($k==0){
                                
                                $list_row1[$value->id]["rack"]=$val->rack;
                            }
                            if($k==1){
                                $list_row1[$value->id]["row"]=$val->row;
                                
                            }
                            if($k==2){
                                $list_row1[$value->id]["position"]=$val->position;
                                
                            }
                        }
                        $racks_list1[$value->id] = $list_row1[$value->id];
                    }
                    $product_racks =  $racks_list1 ;
                    $productUtil->addRackDetails($user->business_id, $product->id, $product_racks);
                }
                $product_racks_update = $request->get('product_racks_update', null);
                if (!empty($product_racks_update)) {
                     $racks_list2  = [];
                     $list_row2    = [];
                   
                    foreach(json_decode($request->product_racks_update) as $key => $value) {
                        
                        $list_row2[$value->id] = [];
                        foreach($value->value as $k => $val){
                            if($k==0){
                                
                                $list_row2[$value->id]["rack"]=$val->rack;
                            }
                            if($k==1){
                                $list_row2[$value->id]["row"]=$val->row;
                                
                            }
                            if($k==2){
                                $list_row2[$value->id]["position"]=$val->position;
                                
                            }
                        }
                        $racks_list2[$value->id] = $list_row2[$value->id];
                    }
                    $product_racks_update =  $racks_list2 ;
                    $productUtil->updateRackDetails($user->business_id, $product->id, $product_racks_update);
                }
            // ....VARIATION.....................................................
                if ($product->type == 'single') {
                    // ........1. *
                        if(isset($data['table_price_1'])){
                            foreach(json_decode($data['table_price_1']) as $data1){
                                if(isset($data1->unit_id)){
                                    GlobalUtil::updateOneRowPrice($product,$user,$data1);
                                    if($data1->value == "Default Price"){
                                        // GlobalUtil::createSingleProductVariationPrices($product->id, $product->sku, $data1->single_dpp , $data1->single_dpp_inc_tax  ,$data1->profit_percent   , $data1->single_dsp  , $data1->single_dsp_inc_tax   ,$product->sku2,null,$data1->unit_id );
                                        $variation                         = \App\Variation::where("product_id",$product->id)->first();
                                        $variation->sub_sku                = $product->sku;       
                                        $variation->default_purchase_price = $data1->single_dpp ;
                                        $variation->dpp_inc_tax            = $data1->single_dpp_inc_tax ; 
                                        $variation->profit_percent         = $data1->profit_percent   ;
                                        $variation->default_sell_price     = $data1->single_dsp  ;
                                        $variation->sell_price_inc_tax     = $data1->single_dsp_inc_tax;
                                        $variation->update();
                                    }
                                    if(isset($object->tableData)){
                                        if($data1->unit_id != $object->tableData){
                                            $product_price           = \App\Models\ProductPrice::where("product_id",$product->id)->where("unit_id",$object->tableData)->whereNotNull("unit_id")->get();   
                                            foreach($product_price as $items_delete){ $items_delete->delete(); }  
                                        }
                                    }
                                }else{ 
                                   $product_price           = \App\Models\ProductPrice::where("product_id",$product->id)->where("unit_id",$object->tableData)->whereNotNull("unit_id")->get();   
                                   foreach($product_price as $items_delete){ $items_delete->delete(); }
                                   $variation                         = \App\Variation::where("product_id",$product->id)->first();
                                   $variation->sub_sku                = $product->sku;       
                                   $variation->default_purchase_price = 0 ;
                                   $variation->dpp_inc_tax            = 0 ; 
                                   $variation->profit_percent         = 0 ;
                                   $variation->default_sell_price     = 0 ;
                                   $variation->sell_price_inc_tax     = 0 ;
                                   $variation->update();
                                }
                            }
                        }
                    // .......... *
                    // ........2. *
                        if(isset($data['table_price_2'])){
                            foreach(json_decode($data['table_price_2']) as $data2){
                                if(isset($data2->unit_id)){
                                    GlobalUtil::updateOneRowPrice($product,$user,$data2);
                                    if(isset($object->tableDataChildOne)){
                                        if($data2->unit_id != $object->tableDataChildOne){
                                            $product_price           = \App\Models\ProductPrice::where("product_id",$product->id)->where("unit_id",$object->tableDataChildOne)->whereNotNull("unit_id")->get();   
                                            foreach($product_price as $items_delete){ $items_delete->delete(); }  
                                        }
                                    }
                                }else{
                                    $product_price           = \App\Models\ProductPrice::where("product_id",$product->id)->where("unit_id",$object->tableDataChildOne)->whereNotNull("unit_id")->get();   
                                    foreach($product_price as $items_delete){ $items_delete->delete(); }
                                }
                            }
                        }
                    // .......... *
                    // ........3. *
                        if(isset($data['table_price_3'])){
                            foreach(json_decode($data['table_price_3']) as $data3){
                                if(isset($data3->unit_id)){
                                    GlobalUtil::updateOneRowPrice($product,$user,$data3);
                                    if(isset($object->tableDataChildTwo)){
                                        if($data3->unit_id != $object->tableDataChildTwo){
                                            $product_price           = \App\Models\ProductPrice::where("product_id",$product->id)->where("unit_id",$object->tableDataChildTwo)->whereNotNull("unit_id")->get();   
                                            foreach($product_price as $items_delete){ $items_delete->delete(); }  
                                        }
                                    }
                                }else{
                                    $product_price           = \App\Models\ProductPrice::where("product_id",$product->id)->where("unit_id",$object->tableDataChildTwo)->whereNotNull("unit_id")->get();   
                                    foreach($product_price as $items_delete){ $items_delete->delete(); }
                                }
                            }                    
                        }
                    // .......... *
                    $variation                                  = \App\Variation::where("product_id",$product->id)->first();
                    Media::uploadMedia($product->business_id, $variation, $request, 'more_image');
                } elseif ($product->type == 'variable') {
                    //Update existing variations
                    $input_variations_edit = $request->get('product_variation_edit');
                        $finalArray  = [];
                     
                       foreach(json_decode($request->product_variation_edit) as $ii){
                           $object_index = $ii->table_id;
                           $arrayGlobal  = []; $array          = []; $arrayLine_old        = [];
                           $line         = []; $arrayLine_new  = []; $count=1;
                           foreach($ii->variations as $child){
                                    $line["sub_sku"]                = $child->sub_sku;
                                    $line["value"]                  = $child->value;
                                    $line["variation_value_id"]     = $child->variation_value_id;
                                    $line["default_purchase_price"] = $child->default_purchase_price;
                                    $line["dpp_inc_tax"]            = $child->dpp_inc_tax;
                                    $line["profit_percent"]         = $child->profit_percent;
                                    $line["default_sell_price"]     = $child->default_sell_price;
                                    $line["sell_price_inc_tax"]     = $child->sell_price_inc_tax;
                               if($child->type == "old"){
                                    $arrayLine_old[$child->rows_id]     = $line;
                               }else{
                                    $arrayLine_new[$count]     = $line;
                                    $count++;
                               }
                               
                           }
                           $array["name"]                  = $ii->name;
                           $array["variation_template_id"] = $ii->variation_template_id;
                           $array["variations_edit"]       = $arrayLine_old;
                           if(count($arrayLine_new)>0){
                                $array["variations"]       = $arrayLine_new;
                           }
                           if($ii->type == "old"){
                                
                               $finalArray["product_variation_edit"][$object_index] = $array;
                           }else{
                                
                               $finalArray["product_variation"][] = $array;
                               
                           }
                       }
                   $lengthOfTables =0;
                    if (isset($finalArray["product_variation_edit"])) {
                        
                        $lengthOfTables = GlobalUtil::updateVariableProductVariations($product->id, $finalArray["product_variation_edit"],"update");
                    }

                    //Add new variations created.
                    $input_variations = $request->input('product_variation');
                    if (!empty($input_variations)) {
                        
                        GlobalUtil::createVariableProductVariations($product->id, $input_variations,null,$lengthOfTables);
                    }
                } elseif ($product->type == 'combo') {
                    $combo_variations = [];
                    //Create combo_variations array by combining variation_id and quantity.
                    $dataCombo = json_decode($request->product_compo);
                    if(count($dataCombo)>0){
                            if($dataCombo[0]){
                                foreach ($dataCombo[0]->rows as $key => $value) {
                                    if($key != 0){
                                        $combo_variations[]    = [
                                                'variation_id' => $value->composition_variation_id,
                                                'quantity'     => $value->quantity,
                                                'unit_id'      => $value->unit
                                            ];
                                    }
                                }
                                $variation                         = \App\Variation::find($dataCombo[0]->combo_variation_id);
                                 
                                $variation->sub_sku                = $product->sku;
                                $variation->default_purchase_price = $dataCombo[0]->item_level_purchase_price_total  ;
                                $taxes_product                     = ($product->product_tax)?$product->product_tax->amount:0;
                                $variation->dpp_inc_tax            = $dataCombo[0]->item_level_purchase_price_total + (($dataCombo[0]->item_level_purchase_price_total * $taxes_product)/(100))  ;
                                $variation->profit_percent         = $dataCombo[0]->profit_percent  ;
                                $variation->default_sell_price     = ($dataCombo[0]->selling_price_inc_tax * 100 )/(100+$taxes_product)  ;
                                $variation->sell_price_inc_tax     = $dataCombo[0]->selling_price_inc_tax   ;
                                $variation->combo_variations       = $combo_variations;
                                $variation->update();
                            }
                    }
                    

                }
            // .................................................................. 
            $product->update();
            return true; 
        }catch(Exception $e){
            return false;
        }
    }

    // **3** Delete Product Image
    public static function deleteMediaProduct($user,$id){
        try{
            $business_id = $user->business_id;
            Media::deleteMedia($business_id, $id);
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // **4** Product Item Move
    public static function productItemMove($user,$data,$id){
        try{
            $business_id = $user->business_id;
            $move_id  = [];
            $itemMove = \App\Models\ItemMove::orderBy("date","asc")->orderBy("order_id","desc")->orderBy("id","asc")->where("product_id",$id)->first();
            if(!empty($itemMove)){
                $move_id [] = $itemMove->id;
                $date       = $itemMove->date;
                \App\Models\ItemMove::updateRefresh($itemMove,$itemMove,$move_id,$date);
            }
            $list        = \App\Models\ItemMove::orderByRaw('ISNULL(date), date desc, created_at desc')
                                                ->orderBy("id","desc")
                                                ->orderBy("order_id","desc")
                                                ->where("product_id",$id)
                                                ->where("business_id",$business_id)
                                                ->select()
                                                ->get();
             
            return $list;
        }catch(Exception $e){
            return false;
        }
    }

    // ******** REQUIREMENT FUNCTION
    // **1** CREATE 0R UPDATE
    public static function requirement($user) {
        $business_id                = $user->business_id;
        $warranties                 = \App\Warranty::forDropdown($business_id);
        $brands                     = \App\Brands::where("business_id",$business_id)->orderBy("id","asc")->get();
        $units                      = \App\Unit::forDropdown($business_id, true);
        $unitsM                     = \App\Unit::forDropdown($business_id, false);
        $m_units                    = \App\Unit::where("business_id",$business_id)->whereNull("base_unit_id")->get();
        $s_units                    = \App\Unit::where("business_id",$business_id)->whereNotNull("base_unit_id")->get();
        $m_listed                   = [];
        $b_listed                   = [];
        $listed                     = [];
        foreach($m_units as $ies){
            $m_listed[] = [
                "id"        => $ies->id,
                "value"     => $ies->actual_name,
            ];
        }
        foreach($s_units as $ie){
            $listed[] = [
                "id"        => $ie->id,
                "name"      => $ie->actual_name,
                "parent_id" => $ie->base_unit_id,
            ];
        }
        foreach($brands as $ies){
            $b_listed[] = [
                "id"        => $ies->id,
                "value"     => $ies->name,
            ];
        }
        $categories                 = \App\Category::forDropdown($business_id, 'product');
        $sub_categories             = \App\Category::where('business_id', $business_id)
                                                    ->where('parent_id',"!=",0)
                                                    ->select('name','id','parent_id')
                                                    ->get();$array=[];$variation_v=[];
        $variation_templates        = \App\VariationTemplate::where('business_id', $business_id)->pluck('name', 'id')->toArray();
        $variation_value_templates  = \App\VariationValueTemplate::select(['variation_template_id','name' ,'id'])->get();
        foreach($variation_value_templates as $key => $i){
            $variation_v[]              = [
                "id"                     =>$i->id,
                "name"                   =>$i->name,
                "variation_templates_id" =>$i->variation_template_id,
            ]; 
        }
        $variation_templates        = [ "" => __('messages.please_select')] + $variation_templates;
        foreach($sub_categories as $ie){
            $array[$ie->id] = ["parent"=>$ie->parent_id,"name"=>$ie->name];
        }
        $currency                   = \App\Currency::select("*")->get(); 
        $exchange_rate              = \App\Models\ExchangeRate::select("*")->where("source",0)->get(); 
        $product_price_listed              = \App\Models\ProductPrice::where("business_id",$business_id)
                                                                ->whereNull("product_id")
                                                                ->select([
                                                                    "id","default_name","number_of_default",
                                                                    "name","price","default_purchase_price",
                                                                    "dpp_inc_tax","profit_percent","default_sell_price",
                                                                    "sell_price_inc_tax","date"
                                                                ])->get();
        $product_price = [];
        $product_price[0]      =  "Default Price" ;
        foreach ($product_price_listed as $key => $value) {
             
            $product_price[$value->id] = $value->name ;

        }             
        $currencies                 = [];
        foreach($currency as $it){
            foreach($exchange_rate as $i){
                if($i->currency_id == $it->id){
                    $currencies[$it->id] =  $it->currency . " - " . $it->code . " - " . $it->symbol;
                }
            }
        }
        $product_type                     = ['single' => __('lang_v1.single'), 'variable' => __('lang_v1.variable'), 'combo' => __('lang_v1.combo') ];
        $barcode_type                     = [ 'C128' => 'Code 128 (C128)', 'C39' => 'Code 39 (C39)', 'EAN13' => 'EAN-13', 'EAN8' => 'EAN-8', 'UPCA' => 'UPC-A', 'UPCE' => 'UPC-E'];
        $businessLocation                 = \App\BusinessLocation::get();
        $product_racks                    = [];
        foreach($businessLocation as $ies){
            $product_racks[$ies->id]      = [
                "name"  => $ies->name,
                "value" => ["rack","row","position"],
                "type"  => "",
            ];
        }
        $listed_taxed = [];
        $tax_dropdown                     = \App\TaxRate::forBusinessDropdown($business_id, true, true);
        $tax_                             = \App\TaxRate::get();
        foreach($tax_ as $ii){
            $listed_taxed[] = [
                "id"        => $ii->id,    
                "name"      => $ii->name,    
                "value"     => round(($ii->amount/100),2),
            ]; 
        }
        $taxes                            = $tax_dropdown['tax_rates'];
        $tax_attributes                   = $tax_dropdown['attributes'];
        $business_locations               = GlobalUtil::businessLocation($business_id,false,false,true,true,$user);
        $list["warranties"]               = GlobalUtil::arrayToObject($warranties);
        $list["brands"]                   = $b_listed;
        $list["units"]                    = $m_listed;
        $list["sub_units"]                = $listed ;
        $list["categories"]               = GlobalUtil::arrayToObject($categories);
        $list["sub_categories"]           = GlobalUtil::arrayToObject($array);
        $list["currencies"]               = GlobalUtil::arrayToObject($currencies); 
        $list["business_locations"]       = GlobalUtil::arrayToObject($business_locations);
        $list["product_type"]             = GlobalUtil::arrayToObject($product_type);
        $list["barcode_type"]             = GlobalUtil::arrayToObject($barcode_type);
        $list["units_for_price"]          = GlobalUtil::arrayToObject($unitsM);
        $list["tax_attributes"]           = GlobalUtil::arrayToObject($tax_attributes) ;
        $list["taxes"]                    = $listed_taxed ;
        $list["product_price"]            = GlobalUtil::arrayToObject($product_price) ;
        $list["product_racks"]            = GlobalUtil::arrayToObject($product_racks) ;
        $list["variation_templates"]      = GlobalUtil::arrayToObject($variation_templates) ;
        $list["variation_value_templates"]= $variation_v;
        return $list;
    }
    // **2** STOCK
    public static function stock($user,$id) {
       $warehouse   =  \App\Models\WarehouseInfo::where("product_id",$id)->get();
       $list        =  [];$total = 0;$allData      =  [];
       foreach($warehouse as $ie){
            $list[]  =  [
                "store" => ($ie->store)?$ie->store->name:"Wrong!!!",
                "qty"   => $ie->product_qty,
            ];
            $total  += $ie->product_qty;
       }
       $allData["list_store"]  = $list;
       $allData["total_qty"]   = $total;
       return $allData ; 
    }
    // **3** EXPORT
    public static function productExport($user,$data,$request) {
        return asset('files/import_products_csv_template.xls');
    }
    // **4** EXPORT
    public static function productImport($user,$data,$request) {
            try {
                $productUtil     = new ProductUtil();
                $transactionUtil = new TransactionUtil();
                $notAllowed      = $productUtil->notAllowedInDemo();
                if (!empty($notAllowed)) {
                    return $notAllowed;
                }
                
                //Set maximum php execution time
                ini_set('max_execution_time', 0);
                ini_set('memory_limit', -1);
    
                if ($request->hasFile('products_csv')) {
                    $file = $request->file('products_csv');
    
                    $parsed_array = Excel::toArray([], $file);
    
                    //Remove header row 0
                    $imported_data = array_splice($parsed_array[0], 1);
    
                    $business_id            = $user->business_id;
                    $user_id                = $user->id;
                    $default_profit_percent = ($user->business)?$user->business->default_profit_percent:0;
    
                    $formated_data = [];
                    $is_valid      = true;
                    $error_msg     = '';
                    $total_rows    = count($imported_data);
    
                    //Check if subscribed or not, then check for products quota
                    // if (!$this->moduleUtil->isSubscribed($business_id)) {
                    //     return $this->moduleUtil->expiredResponse();
                    // } elseif (!$this->moduleUtil->isQuotaAvailable('products', $business_id, $total_rows)) {
                    //     return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action('ImportProductsController@index'));
                    // }
    
                    $business_locations = \App\BusinessLocation::where('business_id', $business_id)->get();
                    \DB::beginTransaction();
                    foreach ($imported_data as $key => $value) {
    
                        //Check if any column is missing
                        if (count($value) < 36) {
                            $is_valid =  false;
                            $error_msg = "Some of the columns are missing. Please, use latest CSV file template.";
                            $output = ['success' => 0,
                                            'msg' => $error_msg
                                        ];
                            return response([
                                    "status"  => 403 ,
                                    "message" => $output["msg"]
                                ],403);
                            break;
                        }
    
                        $row_no = $key + 1;
                        $product_array = [];
                        $product_array['business_id'] = $business_id;
                        $product_array['created_by']  = $user_id;
                        
                        //Add name
                        $product_name = trim($value[0]);
                        if (!empty($product_name)) {
                            $product_array['name'] = $product_name;
                        } else {
                            $is_valid  =  false;
                            $error_msg = "Product name is required in row no. $row_no   at coulm : 1 ";
                            $output = ['success' => 0,
                                        'msg' => $error_msg
                                        ];
                            return response([
                                    "status"  => 403 ,
                                    "message" => $output["msg"]
                                ],403);
                            break;
                        }
                        
                        //image name
                        $image_name = trim($value[28]);
                        if (!empty($image_name)) {
                            $product_array['image'] = $image_name;
                        } else {
                            $product_array['image'] = '';
                        }
    
                        $product_array['product_description'] = isset($value[29]) ? $value[29] : null;
    
                        //Custom fields
                        if (isset($value[30])) {
                            $product_array['product_custom_field1'] = trim($value[30]);
                        } else {
                            $product_array['product_custom_field1'] = '';
                        }
                        if (isset($value[31])) {
                            $product_array['product_custom_field2'] = trim($value[31]);
                        } else {
                            $product_array['product_custom_field2'] = '';
                        }
                        if (isset($value[32])) {
                            $product_array['product_custom_field3'] = trim($value[32]);
                        } else {
                            $product_array['product_custom_field3'] = '';
                        }
                        if (isset($value[33])) {
                            $product_array['product_custom_field4'] = trim($value[33]);
                        } else {
                            $product_array['product_custom_field4'] = '';
                        }
    
                        //Add not for selling
                        $product_array['not_for_selling'] = !empty($value[34]) && $value[34] == 1 ? 1 : 0;
    
                        //Add enable stock
                        $enable_stock = trim($value[7]);
                        if (in_array($enable_stock, [0,1])) {
                            $product_array['enable_stock'] = $enable_stock;
                        } else {
                            $product_array['enable_stock'] = 1;
                           /* $is_valid =  false;
                            $error_msg = "Invalid value for MANAGE STOCK in row no. $row_no";
                            break;*/
                        }
    
                        //Add product type
                        $product_type = strtolower(trim($value[13]));
                        if (in_array($product_type, ['single','variable'])) {
                            $product_array['type'] = $product_type;
                        } else {
                           /* $is_valid =  false;
                            $error_msg = "Invalid value for PRODUCT TYPE in row no. $row_no";
                            break;*/
                            $product_array['type'] ='single';
                        }
    
                        //Add unit
                        $unit_name = trim($value[2]);
                        if (!empty($unit_name)) {
                            $unit  = \App\Unit::where('business_id', $business_id)
                                        ->where(function ($query) use ($unit_name) {
                                            $query->where('short_name', $unit_name)
                                                  ->orWhere('actual_name', $unit_name);
                                        })->first();
                            if (!empty($unit)) {
                                $product_array['unit_id'] = $unit->id;
                            } else {
                                $is_valid  = false;
                                $error_msg = "Unit with name $unit_name not found in row no. $row_no. You can add unit from Products > Units";
                                $output = ['success' => 0,
                                            'msg' => $error_msg
                                            ];
                                return response([
                                        "status"  => 403 ,
                                        "message" => $output["msg"]
                                    ],403);
                                break;
                            }
                        } else {
                            $unit                     = \App\Unit::where('business_id', $business_id)->first();
                            $product_array['unit_id'] = $unit->id;
                        }
    
                        //Add barcode type
                        $barcode_type = strtoupper(trim($value[6]));
                        if (empty($barcode_type)) {
                            $product_array['barcode_type'] = 'C128';
                        } elseif (array_key_exists($barcode_type, $productUtil->barcode_types())) {
                            $product_array['barcode_type'] = $barcode_type;
                        } else {
                            $is_valid  = false;
                            $error_msg = "$barcode_type barcode type is not valid in row no. $row_no. Please, check for allowed barcode types in the instructions";
                            $output = ['success' => 0,
                                        'msg' => $error_msg
                                        ];
                            return response([
                                    "status"  => 403 ,
                                    "message" => $output["msg"]
                                ],403);
                            break;
                        }
    
                        //Add Tax
                        $tax_name   = trim($value[11]);
                        $tax_amount = 0;
                        if (!empty($tax_name)) {
                            $tax = \App\TaxRate::where('business_id', $business_id)
                                            ->where('name', $tax_name)
                                            ->first();
                            if (!empty($tax)) {
                                $product_array['tax'] = $tax->id;
                                $tax_amount = $tax->amount;
                            } else {
                                $is_valid   = false;
                                $error_msg  = "Tax with name $tax_name in row no. $row_no not found. You can add tax from Settings > Tax Rates";
                                $output = ['success' => 0,
                                            'msg' => $error_msg
                                            ];
                                return response([
                                        "status"  => 403 ,
                                        "message" => $output["msg"]
                                    ],403);
                                break;
                            }
                        }
                        //Add tax type
                        $tax_type = strtolower(trim($value[12]));
                        if (in_array($tax_type, ['inclusive', 'exclusive'])) {
                            $product_array['tax_type'] = $tax_type;
                        } else {
                            $tax_type                  = 'exclusive';
                            $product_array['tax_type'] = 'exclusive';
                        }
    
                        //Add alert quantity
                        if ($product_array['enable_stock'] == 1) {
                            $product_array['alert_quantity'] = trim($value[8]);
                        }
                        
    
                        //Add brand
                        //Check if brand exists else create new
                        $brand_name = trim($value[1]);
                        if (!empty($brand_name)) {
                            $brand = \App\Brands::firstOrCreate(
                                ['business_id' => $business_id, 'name' => $brand_name],
                                ['created_by'  => $user_id]
                            );
                            $product_array['brand_id'] = $brand->id;
                        }
    
                        //Add Category
                        //Check if category exists else create new
                        $category_name = trim($value[3]);
                        if (!empty($category_name)) {
                            $category = \App\Category::firstOrCreate(
                                ['business_id' => $business_id, 'name' => $category_name, 'category_type' => 'product'],
                                ['created_by'  => $user_id, 'parent_id' => 0]
                            );
                            $product_array['category_id'] = $category->id;
                        }
    
                        //Add Sub-Category
                        $sub_category_name = trim($value[4]);
                        if (!empty($sub_category_name)) {
                            $sub_category  = \App\Category::firstOrCreate(
                                ['business_id' => $business_id, 'name' => $sub_category_name, 'category_type' => 'product'],
                                ['created_by' => $user_id, 'parent_id' => $category->id]
                            );
                            $product_array['sub_category_id'] = $sub_category->id;
                        }
    
                        //Add SKU
                        $sku = trim($value[5]);
                        if (!empty($sku)) {
                            $product_array['sku'] = $sku;
                            //Check if product with same SKU already exist
                            $is_exist = \App\Product::where('business_id', $business_id)
                                                       ->where(function ($query) use ($product_array) {
                                                         $query->where('sku', $product_array['sku'])
                                                                ->orwhere('sku2',$product_array['sku']);
                                                      })
                                                     ->exists();
                            if ($is_exist) {
                                $is_valid  = false;
                                $error_msg = "$sku SKU already exist in row no. $row_no col: 6 value ";
                                $output = ['success' => 0,
                                            'msg' => $error_msg
                                            ];
                                return response([
                                        "status"  => 403 ,
                                        "message" => $output["msg"]
                                    ],403);
                                break;
                            }
                        } else {
                            $product_array['sku'] = ' ';
                        }
    
    
    
                        //Add product expiry
                        $expiry_period = trim($value[9]);
                        $expiry_period_type = strtolower(trim($value[10]));
                        if (!empty($expiry_period) && in_array($expiry_period_type, ['months', 'days'])) {
                            $product_array['expiry_period'] = $expiry_period;
                            $product_array['expiry_period_type'] = $expiry_period_type;
                        } else {
                            //If Expiry Date is set then make expiry_period 12 months.
                            if (!empty($value[22])) {
                                $product_array['expiry_period'] = 12;
                                $product_array['expiry_period_type'] = 'months';
                            }
                        }
    
                        //Enable IMEI or Serial Number
                        $enable_sr_no = trim($value[23]);
                        if (in_array($enable_sr_no, [0,1])) {
                            $product_array['enable_sr_no'] = $enable_sr_no;
                        } elseif (empty($enable_sr_no)) {
                            $product_array['enable_sr_no'] = 0;
                        } else {
                            $is_valid =  false;
                            $error_msg = "Invalid value for ENABLE IMEI OR SERIAL NUMBER  in row no. $row_no";
                            $output = ['success' => 0,
                                        'msg' => $error_msg
                                        ];
                            return response([
                                    "status"  => 403 ,
                                    "message" => $output["msg"]
                                ],403);
                            break;
                        }
    
                        //Weight
                        if (isset($value[24])) {
                            $product_array['weight'] = trim($value[24]);
                        } else {
                            $product_array['weight'] = '';
                        }
    
                        if ($product_array['type'] == 'single') {
                            //Calculate profit margin
                            $profit_margin = trim($value[18]);
                            if (empty($profit_margin)) {
                                $profit_margin = $default_profit_percent;
                            } else {
                                $profit_margin = trim($value[18]);
                            }
                            $product_array['variation']['profit_percent'] = $profit_margin;
    
                            //Calculate purchase price
                            $dpp_inc_tax = trim($value[16]);
                            $dpp_exc_tax = trim($value[17]);
                            if ($dpp_inc_tax == '' && $dpp_exc_tax == '') {
                                $is_valid = false;
                                $error_msg = "PURCHASE PRICE is required in row no. $row_no colm 16";
                                $output = ['success' => 0,
                                            'msg' => $error_msg
                                            ];
                                return response([
                                        "status"  => 403 ,
                                        "message" => $output["msg"]
                                    ],403);
                                break;
                            } else {
                                $dpp_inc_tax = ($dpp_inc_tax != '') ? $dpp_inc_tax : 0;
                                $dpp_exc_tax = ($dpp_exc_tax != '') ? $dpp_exc_tax : 0;
                            }
    
                            //Calculate Selling price
                            $selling_price   = !empty(trim($value[19])) ? trim($value[19]) : 0 ;
    
                            //Calculate product prices
                            $product_prices  = GlobalUtil::calculateVariationPrices($dpp_exc_tax, $dpp_inc_tax, $selling_price, $tax_amount, $tax_type, $profit_margin,$productUtil);
    
                            //Assign Values
                            $product_array['variation']['dpp_inc_tax'] = $product_prices['dpp_inc_tax'];
                            $product_array['variation']['dpp_exc_tax'] = $product_prices['dpp_exc_tax'];
                            $product_array['variation']['dsp_inc_tax'] = $product_prices['dsp_inc_tax'];
                            $product_array['variation']['dsp_exc_tax'] = $product_prices['dsp_exc_tax'];
                            
                            //Opening stock
                            if (!empty($value[20]) && $enable_stock == 1) {
                                $product_array['opening_stock_details']['quantity'] = trim($value[20]);
    
                                if (!empty(trim($value[21]))) {
                                    $location_name = trim($value[21]);
                                    $location = \App\BusinessLocation::where('name', $location_name)
                                                                ->where('business_id', $business_id)
                                                                ->first();
                                    if (!empty($location)) {
                                        $product_array['opening_stock_details']['location_id'] = $location->id;
                                    } else {
                                        $is_valid = false;
                                        $error_msg = "No location with name '$location_name' found in row no. $row_no";
                                        $output = ['success' => 0,
                                                    'msg' => $error_msg
                                                    ];
                                        return response([
                                                "status"  => 403 ,
                                                "message" => $output["msg"]
                                            ],403);
                                        break;
                                    }
                                } else {
                                    $location = \App\BusinessLocation::where('business_id', $business_id)->first();
                                    $product_array['opening_stock_details']['location_id'] = $location->id;
                                }
    
                                $product_array['opening_stock_details']['expiry_date'] = null;
    
                                //Stock expiry date
                                if (!empty($value[22])) {
                                    $product_array['opening_stock_details']['exp_date'] = \Carbon::createFromFormat('m-d-Y', trim($value[22]))->format('Y-m-d');
                                } else {
                                    $product_array['opening_stock_details']['exp_date'] = null;
                                }
                            }
                        } elseif ($product_array['type'] == 'variable') {
                            $variation_name = trim($value[14]);
                            if (empty($variation_name)) {
                                $is_valid = false;
                                $error_msg = "VARIATION NAME is required in row no. $row_no";
                                $output = ['success' => 0,
                                            'msg' => $error_msg
                                            ];
                                return response([
                                        "status"  => 403 ,
                                        "message" => $output["msg"]
                                    ],403);
                                break;
                            }
                            $variation_values_string = trim($value[15]);
                            if (empty($variation_values_string)) {
                                $is_valid = false;
                                $error_msg = "VARIATION VALUES are required in row no. $row_no";
                                $output = ['success' => 0,
                                            'msg' => $error_msg
                                            ];
                                return response([
                                        "status"  => 403 ,
                                        "message" => $output["msg"]
                                    ],403);
                                break;
                            }
    
                            $dpp_inc_tax_string   = trim($value[16]);
                            $dpp_exc_tax_string   = trim($value[17]);
                            $selling_price_string = trim($value[19]);
                            $profit_margin_string = trim($value[18]);
    
                            if (empty($dpp_inc_tax_string) && empty($dpp_exc_tax_string)) {
                                $is_valid = false;
                                $error_msg = "PURCHASE PRICE is required in row no. $row_no";
                                $output = ['success' => 0,
                                            'msg' => $error_msg
                                            ];
                                return response([
                                        "status"  => 403 ,
                                        "message" => $output["msg"]
                                    ],403);
                                break;
                            }
    
                            //Variation values
                            $variation_values = array_map('trim', explode(
                                '|',
                                $variation_values_string
                            ));
    
                            //Map Purchase price with variation values
                            $dpp_inc_tax = [];
                            if (!empty($dpp_inc_tax_string)) {
                                $dpp_inc_tax = array_map('trim', explode(
                                    '|',
                                    $dpp_inc_tax_string
                                ));
                            } else {
                                foreach ($variation_values as $k => $v) {
                                    $dpp_inc_tax[$k] = 0;
                                }
                            }
                            
                            $dpp_exc_tax = [];
                            if (!empty($dpp_exc_tax_string)) {
                                $dpp_exc_tax = array_map('trim', explode(
                                    '|',
                                    $dpp_exc_tax_string
                                ));
                            } else {
                                foreach ($variation_values as $k => $v) {
                                    $dpp_exc_tax[$k] = 0;
                                }
                            }
    
                            //Map Selling price with variation values
                            $selling_price = [];
                            if (!empty($selling_price_string)) {
                                $selling_price = array_map('trim', explode(
                                    '|',
                                    $selling_price_string
                                    ));
                            } else {
                                foreach ($variation_values as $k => $v) {
                                    $selling_price[$k] = 0;
                                }
                            }
    
                            //Map profit margin with variation values
                            $profit_margin = [];
                            if (!empty($profit_margin_string)) {
                                $profit_margin = array_map('trim', explode(
                                    '|',
                                    $profit_margin_string
                                    ));
                            } else {
                                foreach ($variation_values as $k => $v) {
                                    $profit_margin[$k] = $default_profit_percent;
                                }
                            }
    
                            //Check if length of prices array is equal to variation values array length
                            $array_lengths_count = [count($variation_values), count($dpp_inc_tax), count($dpp_exc_tax), count($selling_price), count($profit_margin)];
                            $same = array_count_values($array_lengths_count);
    
                            if (count($same) != 1) {
                                $is_valid = false;
                                $error_msg = "Prices mismatched with VARIATION VALUES in row no. $row_no";
                                $output = ['success' => 0,
                                            'msg' => $error_msg
                                            ];
                                return response([
                                        "status"  => 403 ,
                                        "message" => $output["msg"]
                                    ],403);
                                break;
                            }
                            $product_array['variation']['name'] = $variation_name;
    
                            //Check if variation exists or create new
                            $variation = $productUtil->createOrNewVariation($business_id, $variation_name);
                            $product_array['variation']['variation_template_id'] = $variation->id;
    
                            foreach ($variation_values as $k => $v) {
                                $variation_prices = GlobalUtil::calculateVariationPrices($dpp_exc_tax[$k], $dpp_inc_tax[$k], $selling_price[$k], $tax_amount, $tax_type, $profit_margin[$k],$productUtil);
    
                                //get variation value
                                $variation_value = $variation->values->filter(function ($item) use ($v) {
                                    return strtolower($item->name) == strtolower($v);
                                })->first();
    
                                if (empty($variation_value)) {
                                    $variation_value = \App\VariationValueTemplate::create([
                                      'name' => $v,
                                      'variation_template_id' => $variation->id
                                    ]);
                                }
                                
                                //Assign Values
                                $product_array['variation']['variations'][] = [
                                    'value'                  => $v,
                                    'variation_value_id'     => $variation_value->id,
                                    'default_purchase_price' => $variation_prices['dpp_exc_tax'],
                                    'dpp_inc_tax'            => $variation_prices['dpp_inc_tax'],
                                    'profit_percent'         => $productUtil->num_f($profit_margin[$k]),
                                    'default_sell_price'     => $variation_prices['dsp_exc_tax'],
                                    'sell_price_inc_tax'     => $variation_prices['dsp_inc_tax']
                                ];
                            }
    
                            //Opening stock
                            if (!empty($value[20]) && $enable_stock == 1) {
                                $variation_os = array_map('trim', explode('|', $value[20]));
    
                                //$product_array['opening_stock_details']['quantity'] = $variation_os;
    
                                //Check if count of variation and opening stock is matching or not.
                                if (count($product_array['variation']['variations']) != count($variation_os)) {
                                    $is_valid = false;
                                    $error_msg = "Opening Stock mismatched with VARIATION VALUES in row no. $row_no";
                                    $output = ['success' => 0,
                                                'msg' => $error_msg
                                                ];
                                    return response([
                                            "status"  => 403 ,
                                            "message" => $output["msg"]
                                        ],403);
                                    break;
                                }
    
                                if (!empty(trim($value[21]))) {
                                    $location_name = trim($value[21]);
                                    $location = \App\BusinessLocation::where('name', $location_name)
                                                                ->where('business_id', $business_id)
                                                                ->first();
                                    if (empty($location)) {
                                        $is_valid = false;
                                        $error_msg = "No location with name '$location_name' found in row no. $row_no";
                                        $output = ['success' => 0,
                                                    'msg' => $error_msg
                                                    ];
                                        return response([
                                                "status"  => 403 ,
                                                "message" => $output["msg"]
                                            ],403);
                                        break;
                                    }
                                } else {
                                    $location = \App\BusinessLocation::where('business_id', $business_id)->first();
                                }
                                $product_array['variation']['opening_stock_location'] = $location->id;
    
                                foreach ($variation_os as $k => $v) {
                                    $product_array['variation']['variations'][$k]['opening_stock'] = $v;
                                    $product_array['variation']['variations'][$k]['opening_stock_exp_date'] = null;
                                    
                                    if (!empty($value[22])) {
                                        $product_array['variation']['variations'][$k]['opening_stock_exp_date'] = \Carbon::createFromFormat('m-d-Y', trim($value[22]))->format('Y-m-d');
                                    } else {
                                        $product_array['variation']['variations'][$k]['opening_stock_exp_date'] = null;
                                    }
                                }
                            }
                        }
                        //Assign to formated array
                        $formated_data[] = $product_array;
                    }
    
                    if (!$is_valid) {
                        $output = ['success' => 0,
                                    'msg' => $error_msg
                                    ];
                        return response([
                                "status"  => 403 ,
                                "message" => $output["msg"]
                            ],403);
                    }
    
                    if (!empty($formated_data)) {
                        foreach ($formated_data as $index => $product_data) {
                            $variation_data = $product_data['variation'];
                            unset($product_data['variation']);
    
                            $opening_stock = null;
                            if (!empty($product_data['opening_stock_details'])) {
                                $opening_stock = $product_data['opening_stock_details'];
                            }
                            if (isset($product_data['opening_stock_details'])) {
                                unset($product_data['opening_stock_details']);
                            }
    
    
                            // $product_data['sku2']=trim($value[37]);
    
                            //Create new product
                            $product = \App\Product::create($product_data);
                            //If auto generate sku generate new sku
                            if ($product->sku == ' ') {
                                $sku = $productUtil->generateProductSku($product->id);
                                $product->sku = $sku;
                                $product->save();
                            }
                           
                            //Rack, Row & Position.
                            GlobalUtil::rackDetails(
                                $imported_data[$index][25],
                                $imported_data[$index][26],
                                $imported_data[$index][27],
                                $business_id,
                                $product->id,
                                $index+1,
                                $productUtil
                            );
    
                            //Product locations
                            if (!empty($imported_data[$index][35])) {
                                $locations_array = explode(',', $imported_data[$index][35]);
                                $location_ids = [];
                                foreach ($locations_array as $business_location) {
                                    foreach ($business_locations as $loc) {
                                        if (strtolower($loc->name) == strtolower(trim($business_location))) {
                                           $location_ids[] = $loc->id;
                                        }
                                    }
                                }
                                if (!empty($location_ids)) {
                                    $product->product_locations()->sync($location_ids);
                                }
                            }
    
                            //Create single product variation
                            if ($product->type == 'single') {
                                $productUtil->createSingleProductVariation(
                                    $product,
                                    $product->sku,
                                    $variation_data['dpp_exc_tax'],
                                    $variation_data['dpp_inc_tax'],
                                    $variation_data['profit_percent'],
                                    $variation_data['dsp_exc_tax'],
                                    $variation_data['dsp_inc_tax']
                                );
                                if (!empty($opening_stock)) {
                                    GlobalUtil::addOpeningStock($opening_stock, $product, $business_id,$user->id,$productUtil);
                                }
                            } elseif ($product->type == 'variable') {
                                //Create variable product variations
                                $productUtil->createVariableProductVariations(
                                    $product,
                                    [$variation_data],
                                    $business_id
                                );
    
                                if (!empty($value[20]) && $enable_stock == 1) {
                                  
                                    GlobalUtil::addOpeningStockForVariable($variation_data, $product, $business_id,$user->id,$productUtil);
                                }
                            }
                        }
                    }
                }
                
            } catch (\Exception $e) {
                 return false;
            }
    }
    // **5** SUBUNIT
    public static function sub_units_product($product){
        
        if($product->sub_unit != null){
            $list_unit = [];
            $all = json_decode( $product->sub_unit );
            foreach($all as $i){
                $unit            = \App\Unit::find($i);
                $list_unit[]     = ["id" => $i ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier];
            }
            $unit2               = \App\Unit::find($product->unit_id);
            $list_unit[]         = ["id" => $unit2->id ,"value" => $unit2->actual_name ,"unit_quantity"=>($unit2->base_unit_multiplier == null)?1:$unit2->base_unit_multiplier];
            return $list_unit ;
            
        }else{
            $unit                = \App\Unit::find($product->unit_id);
            $list_unit[]         = ["id" => $product->unit_id ,"value" => $unit->actual_name ,"unit_quantity"=>($unit->base_unit_multiplier == null)?1:$unit->base_unit_multiplier];
            $list_unit ;
            return $list_unit ;
        }
    }
}
