<?php

namespace App\Utils;

use App\Business;
use App\BusinessLocation;
use App\Discount;
use App\Media;
use App\Product;
use App\ProductRack; 
use App\ProductVariation;
use App\PurchaseLine;
use Carbon\Carbon;
use App\TaxRate;
use App\Transaction;
use App\TransactionSellLine;
use App\TransactionSellLinesPurchaseLines;
use App\Unit;
use App\Variation;
// use App\Business;
use App\VariationGroupPrice;
use App\Models\WarehouseInfo;
use App\Models\Warehouse;
use App\Models\RecievedPrevious;
use App\MovementWarehouse;
use App\Models\TransactionRecieved;
use App\VariationLocationDetails;
use App\VariationTemplate;
use App\VariationValueTemplate;
use Illuminate\Support\Facades\DB;

class ProductUtil extends Util
{
    /** 1* F
     * Create single type product variation
     *
     * @param (int or object) $product
     * @param $sku
     * @param $purchase_price
     * @param $dpp_inc_tax (default purchase pric including tax)
     * @param $profit_percent
     * @param $selling_price
     * @param $combo_variations = []
     *
     * @return boolean
     */
    public function createSingleProductVariation($product, $sku, $purchase_price, $dpp_inc_tax, $profit_percent, $selling_price, $selling_price_inc_tax, $combo_variations = [])
    {
        if (!is_object($product)) {
            $product = Product::find($product);
        }
        //create product variations
        $product_variation_data = [
                                    'name' => 'DUMMY',
                                    'is_dummy' => 1
                                ];
        $product_variation = $product->product_variations()->create($product_variation_data);
        //create variations
        $variation_data = [
                'name'                   => 'DUMMY',
                'product_id'             => $product->id,
                'sub_sku'                => $sku,
                'default_purchase_price' => $this->num_uf($purchase_price),
                'dpp_inc_tax'            => $this->num_uf($dpp_inc_tax),
                'profit_percent'         => $this->num_uf($profit_percent),
                'default_sell_price'     => $this->num_uf($selling_price),
                'sell_price_inc_tax'     => $this->num_uf($selling_price_inc_tax),
                'combo_variations'       => $combo_variations
            ];
        $variation = $product_variation->variations()->create($variation_data);
        Media::uploadMedia($product->business_id, $variation, request(), 'variation_images');
        return true;
    }
    //  2* F.........*****............... \\
    public function createSingleProductVariationPrices($product, $sku, $list_purchase_price, $list_dpp_inc_tax, $list_profit_percent, $list_selling_price, $list_selling_price_inc_tax, $combo_variations = [],$type=null,$list_unit)
    {
        if (!is_object($product)) {
            $product = Product::find($product);
        }
        foreach($list_purchase_price as $key => $i){
            switch ($key) {
                case 0:
                    $val = "Default Price";
                    break;
                case 1:
                    $val = "Whole Price";
                    break;
                case 2:
                    $val = "Retail Price";
                    break;
                case 3:
                    $val = "Minimum Price";
                    break;
                case 4:
                    $val = "Last Price";
                    break;
                case 5:
                    $val = "ECM Before Price";
                    break;
                case 6:
                    $val = "ECM After Price";
                    break;
                case 7:
                    $val = "Custom Price 1";
                    break;
                case 8:
                    $val = "Custom Price 2";
                    break;
                case 9:
                    $val = "Custom Price 3";
                    break;
                default:
                    $val = null;
            }
            if($type == 1){
                if($key == 0){
                    
                    //create product variations
                    $product_variation_data = [
                        'name' => 'DUMMY',
                        'is_dummy' => 1
                    ];
                     $product_variation = $product->product_variations()->create($product_variation_data);
                    //create variations
                    $variation_data = [
                        'name'                      => 'DUMMY',
                        'product_id'                => $product->id,
                        'sub_sku'                   => $sku,
                        'default_purchase_price'    => $this->num_uf($i),
                        'dpp_inc_tax'               => $this->num_uf($list_dpp_inc_tax[$key]),
                        'profit_percent'            => $this->num_uf($list_profit_percent[$key]),
                        'default_sell_price'        => $this->num_uf($list_selling_price[$key]),
                        'sell_price_inc_tax'        => $this->num_uf($list_selling_price_inc_tax[$key]),
                        'combo_variations'          => $combo_variations
                    ];
                    $variation = $product_variation->variations()->create($variation_data);
                    Media::uploadMedia($product->business_id, $variation, request(), 'variation_images');
                }
                $product_price                         = new \App\Models\ProductPrice();
                $product_price->name                   = $val;
                $product_price->product_id             = $product->id;
                $product_price->business_id            = $product->business_id;
                $product_price->default_purchase_price = $i;
                $product_price->dpp_inc_tax            = $list_dpp_inc_tax[$key];
                $product_price->profit_percent         = $list_profit_percent[$key];
                $product_price->default_sell_price     = $list_selling_price[$key];
                $product_price->sell_price_inc_tax     = $list_selling_price_inc_tax[$key];
                $product_price->unit_id                = $list_unit;
                $product_price->number_of_default      = $key;
                $product_price->date                   = \Carbon::now();
                $product_price->save();
            }else{
                $product_price                         = new \App\Models\ProductPrice();
                $product_price->name                   = $val;
                $product_price->product_id             = $product->id;
                $product_price->business_id            = $product->business_id;
                $product_price->default_purchase_price = $i;
                $product_price->dpp_inc_tax            = $list_dpp_inc_tax[$key];
                $product_price->profit_percent         = $list_profit_percent[$key];
                $product_price->default_sell_price     = $list_selling_price[$key];
                $product_price->sell_price_inc_tax     = $list_selling_price_inc_tax[$key];
                $product_price->unit_id                = $list_unit;
                $product_price->number_of_default      = $key;
                $product_price->date                   = \Carbon::now();
                $product_price->save();
            }
        }
         

        return true;
    }
    /** 3*
     * Create variable type product variation
     *
     * @param (int or object) $product
     * @param $input_variations
     *
     * @return boolean
     */
    public function createVariableProductVariations($product, $input_variations, $business_id = null)
    {
         
        if (!is_object($product)) {
            $product = Product::find($product);
        }
        //create product variations
        foreach ($input_variations as $key => $value) {
            
            $images                  = [];
            $variation_template_name = !empty($value['name']) ? $value['name'] : null;
            $variation_template_id   = !empty($value['variation_template_id']) ? $value['variation_template_id'] : null;
            if (empty($variation_template_id)) {
                if ($variation_template_name != 'DUMMY') {
                    $variation_template = VariationTemplate::where('business_id', $business_id)
                                                        ->whereRaw('LOWER(name)="' . strtolower($variation_template_name) . '"')
                                                        ->with(['values'])
                                                        ->first();
                    if (empty($variation_template)) {
                        $variation_template = VariationTemplate::create([
                            'name'          => $variation_template_name,
                            'business_id'   => $business_id
                        ]);
                    }
                    $variation_template_id = $variation_template->id;
                }
            } else {
                $variation_template      = VariationTemplate::with(['values'])->find($value['variation_template_id']);
                $variation_template_id   = $variation_template->id;
                $variation_template_name = $variation_template->name;
            }
          
            $product_variation_data = [
                                    'name'                  => $variation_template_name,
                                    'product_id'            => $product->id,
                                    'is_dummy'              => 0,
                                    'variation_template_id' => $variation_template_id
                                ];

            $product_variation = ProductVariation::create($product_variation_data);
           
            //create variations #2024-8-6
            if (!empty($value['variations'])) {
                $variation_data = [];

                $c = Variation::withTrashed()
                        ->where('product_id', $product->id)
                        ->count() + 1;
                       
                foreach ($value['variations'] as $k => $v) {
                    $sub_sku              = empty($v['sub_sku'])? $this->generateSubSku($product->sku, $c, $product->barcode_type) :$v['sub_sku'];
                    $variation_value_id   = !empty($v['variation_value_id']) ? $v['variation_value_id'] : null;
                    $variation_value_name = !empty($v['value']) ? $v['value'] : null;

                    if (!empty($variation_value_id)) {
                        $variation_value = $variation_template->values->filter(function ($item) use ($variation_value_id) {
                            return $item->id == $variation_value_id;
                        })->first();
                        $variation_value_name = $variation_value->name;
                    } else {
                        if (!empty($variation_template)) {
                            $variation_value =  VariationValueTemplate::where('variation_template_id', $variation_template->id)
                                ->whereRaw('LOWER(name)="' . $variation_value_name . '"')
                                ->first();
                            if (empty($variation_value)) {
                                $variation_value =  VariationValueTemplate::create([
                                    'name' => $variation_value_name,
                                    'variation_template_id' => $variation_template->id
                                ]);
                            }
                            $variation_value_id = $variation_value->id;
                            $variation_value_name = $variation_value->name;
                        } else {
                            $variation_value_id = null;
                            $variation_value_name = $variation_value_name;
                        }
                    }
                    $variation_data[] = [
                      'name'                   => $variation_value_name,
                      'variation_value_id'     => $variation_value_id,
                      'product_id'             => $product->id,
                      'sub_sku'                => $sub_sku,
                      'default_purchase_price' => $this->num_uf($v['default_purchase_price']),
                      'dpp_inc_tax'            => $this->num_uf($v['dpp_inc_tax']),
                      'profit_percent'         => $this->num_uf($v['profit_percent']),
                      'default_sell_price'     => $this->num_uf($v['default_sell_price']),
                      'sell_price_inc_tax'     => $this->num_uf($v['sell_price_inc_tax'])
                    ];
                    $c++;
                    $images[] = 'variation_images_' . $key . '_' . $k;   
                    
                    if(count($v['unit_D'])>0){
                        foreach($v['unit_D'] as $index => $value){
                            $in    = $index+1;
                            $units = $value;
                             #section for first line
                            $First_single_dpp           = $this->num_uf($v['default_purchase_price']);
                            $First_single_dpp_inc_tax   = $this->num_uf($v['dpp_inc_tax']);
                            $First_profit_percent       = $this->num_uf($v['profit_percent']);
                            $First_single_dsp           = $this->num_uf($v['default_sell_price']);
                            $First_single_dsp_inc_tax   = $this->num_uf($v['sell_price_inc_tax']);
                            $product_id                 = \App\Models\ProductPrice::where("product_id",$product->id)
                                                                        ->whereNull("default_name")
                                                                        ->where("number_of_default",0)
                                                                        ->where("unit_id",$units)
                                                                        ->where("variations_value_id",$variation_value_id)
                                                                        ->where("variations_template_id",$variation_template_id)
                                                                        ->first();
                           
                            if(empty($product_id)){
                                $product_id_price                         =  new \App\Models\ProductPrice();
                                $product_id_price->product_id             =  $product->id ;   
                                $product_id_price->business_id            =  $business_id ;   
                                $product_id_price->name                   =  "Default Price" ;   
                                $product_id_price->default_purchase_price =  $First_single_dpp;
                                $product_id_price->dpp_inc_tax            =  $First_single_dpp_inc_tax; 
                                $product_id_price->profit_percent         =  $First_profit_percent;
                                $product_id_price->default_sell_price     =  $First_single_dsp;
                                $product_id_price->sell_price_inc_tax     =  $First_single_dsp_inc_tax;
                                $product_id_price->number_of_default      =  0 ;     
                                $product_id_price->unit_id                =  $units ;
                                $product_id_price->variations_value_id    =  $variation_value_id ;
                                $product_id_price->variations_template_id =  $variation_template_id ;
                                $product_id_price->ks_line                =  $in ;
                                $product_id_price->save();
                            }else{
                                $product_id->name                         =  "Default Price";   
                                $product_id->default_purchase_price       =  $First_single_dpp;
                                $product_id->dpp_inc_tax                  =  $First_single_dpp_inc_tax;
                                $product_id->profit_percent               =  $First_profit_percent;
                                $product_id->default_sell_price           =  $First_single_dsp;
                                $product_id->sell_price_inc_tax           =  $First_single_dsp_inc_tax;
                                $product_id->unit_id                      =  $units;   
                                $product_id->variations_value_id          =  $variation_value_id;
                                $product_id->variations_template_id       =  $variation_template_id ;
                                $product_id->ks_line                      =  $in ;   
                                $product_id->update();
                            }
                            $sn_ddp     = 'single_dpp'.$in;
                            $sn_ddp_inc = 'single_dpp_inc_tax'.$in;
                            $pro_p      = 'profit_percent'.$in;
                            $sn_dsp     = 'single_dsp'.$in;
                            $sn_dsp_inc = 'single_dsp_inc_tax'.$in;
                            
                            #First for list prices
                            $request_single_dpp         = $v[$sn_ddp]     ;
                            $request_single_dpp_inc_tax = $v[$sn_ddp_inc] ;
                            $request_profit_percent     = $v[$pro_p]      ;
                            $request_single_dsp         = $v[$sn_dsp]     ;
                            $request_single_dsp_inc_tax = $v[$sn_dsp_inc] ;
                            foreach($request_single_dpp as $k => $values){
                                
                                $number_of_price_in_table   = ($index == 0)?$k+1:$k;
                                $product_id                 = \App\Models\ProductPrice::where("product_id",$product->id)
                                                                        ->whereNull("default_name")
                                                                        ->where("number_of_default",$number_of_price_in_table)
                                                                        ->where("unit_id",$units)
                                                                        ->where("variations_value_id",$variation_value_id)
                                                                        ->where("variations_template_id",$variation_template_id)
                                                                        ->first();
                                switch ($k) {
                                    case 0:
                                        $val = ($index==0)?"Whole Price":"Default Price";
                                        break;
                                    case 1:
                                        $val = ($index==0)?"Retail Price":"Whole Price";
                                        break;
                                    case 2:
                                        $val = ($index==0)?"Minimum Price":"Retail Price";
                                        break;
                                    case 3:
                                        $val = ($index==0)?"Last Price":"Minimum Price";
                                        break;
                                    case 4:
                                        $val = ($index==0)?"ECM Before Price":"Last Price";
                                        break;
                                    case 5:
                                        $val = ($index==0)?"ECM After Price":"ECM Before Price";
                                        break;
                                    case 6:
                                        $val = ($index==0)?"Custom Price 1":"ECM After Price";
                                        break;
                                    case 7:
                                        $val = ($index==0)?"Custom Price 2":"Custom Price 1";
                                        break;
                                    case 8:
                                        $val = ($index==0)?"Custom Price 3":"Custom Price 2";
                                        break;
                                    case 9:
                                        $val = "Custom Price 3";
                                        break;
                                    default:
                                        $val = null;
            
                                }
                                if(empty($product_id)){
                                    $product_id_price                         =  new \App\Models\ProductPrice();
                                    $product_id_price->product_id             =  $product->id ;   
                                    $product_id_price->business_id            =  $business_id ;   
                                    $product_id_price->name                   =  $val;   
                                    $product_id_price->default_purchase_price =  $this->num_uf($values);
                                    $product_id_price->dpp_inc_tax            =  $this->num_uf($request_single_dpp_inc_tax[$k]);
                                    $product_id_price->profit_percent         =  $this->num_uf($request_profit_percent[$k]);
                                    $product_id_price->default_sell_price     =  $this->num_uf($request_single_dsp[$k]);
                                    $product_id_price->sell_price_inc_tax     =  $this->num_uf($request_single_dsp_inc_tax[$k]);
                                    $product_id_price->number_of_default      =  $number_of_price_in_table ;     
                                    $product_id_price->unit_id                =  $units ;
                                    $product_id_price->variations_value_id    =  $variation_value_id ;
                                    $product_id_price->variations_template_id =  $variation_template_id ;
                                    $product_id_price->ks_line                =  $in ;
                                    $product_id_price->save();
                                }else{
                                    $product_id->name                         =  $val;   
                                    $product_id->default_purchase_price       =  $this->num_uf($values);
                                    $product_id->dpp_inc_tax                  =  $this->num_uf($request_single_dpp_inc_tax[$k]);
                                    $product_id->profit_percent               =  $this->num_uf($request_profit_percent[$k]);
                                    $product_id->default_sell_price           =  $this->num_uf($request_single_dsp[$k]);
                                    $product_id->sell_price_inc_tax           =  $this->num_uf($request_single_dsp_inc_tax[$k]);
                                    $product_id->unit_id                      =  $units;   
                                    $product_id->variations_value_id          =  $variation_value_id; 
                                    $product_id->variations_template_id       =  $variation_template_id ;
                                    $product_id->ks_line                      =  $in ;  
                                    $product_id->update();
                                }
                                
                            }
                        }
                    }
                }
                $variations = $product_variation->variations()->createMany($variation_data);

                $i = 0;
                
                foreach ($variations as $variation) {
                    Media::uploadMedia($product->business_id, $variation, request(), $images[$i]);
                    $i++;
                }
            }
        }
    }
    /** 4*
     * Update variable type product variation
     *
     * @param $product_id
     * @param $input_variations_edit
     *
     * @return boolean
     */
    public function updateVariableProductVariations($product_id, $input_variations_edit)
    {
        $product = Product::find($product_id);

        //Update product variations
        $product_variation_ids = [];
        $variations_ids        = [];
        $price_ids             = [];
        $all_units_ids         = [];
        $listGSN               = [];
        foreach ($input_variations_edit as $key => $value) {
            $product_variation_ids[] = $key;

            $product_variation = ProductVariation::find($key);
            $product_variation->name = $value['name'];
            $product_variation->save();
            $list_of_row_items = [];
            //Update existing variations
            if (!empty($value['variations_edit'])) {
                foreach ($value['variations_edit'] as $k => $v) {
                    $data = [
                        'name'                   => $v['value'],
                        'default_purchase_price' => $this->num_uf($v['default_purchase_price']),
                        'dpp_inc_tax'            => $this->num_uf($v['dpp_inc_tax']),
                        'profit_percent'         => $this->num_uf($v['profit_percent']),
                        'default_sell_price'     => $this->num_uf($v['default_sell_price']),
                        'sell_price_inc_tax'     => $this->num_uf($v['sell_price_inc_tax'])
                    ];
                    if (!empty($v['sub_sku'])) {
                        $data['sub_sku'] = $v['sub_sku'];
                    }
                    $variation = Variation::where('id', $k)
                            ->where('product_variation_id', $key)
                            ->first();
                    $list_of_row_items[] = $k;
                    if(count($v['unit_D'])>0){
                        foreach($v['unit_D'] as $index => $unit_value){
                            $in    = $index+1;
                            $units = $unit_value;
                            if(!in_array($unit_value,$all_units_ids)){
                                $all_units_ids[] = $unit_value;
                            }
                                #section for first line
                            $First_single_dpp           = $this->num_uf($v['default_purchase_price']);
                            $First_single_dpp_inc_tax   = $this->num_uf($v['dpp_inc_tax']);
                            $First_profit_percent       = $this->num_uf($v['profit_percent']);
                            $First_single_dsp           = $this->num_uf($v['default_sell_price']);
                            $First_single_dsp_inc_tax   = $this->num_uf($v['sell_price_inc_tax']);
                            if($index == 0){
                                $product_price_id                 = \App\Models\ProductPrice::find($v['line_id']);
                                $price_ids[]                = intVal($v['line_id']);
                                if(empty($product_price_id)){
                                    $product_id_price                         =  new \App\Models\ProductPrice();
                                    $product_id_price->product_id             =  $product->id ;   
                                    $product_id_price->business_id            =  request()->session()->get('user.business_id') ;   
                                    $product_id_price->name                   =  "Default Price" ;   
                                    $product_id_price->default_purchase_price =  $First_single_dpp;
                                    $product_id_price->dpp_inc_tax            =  $First_single_dpp_inc_tax; 
                                    $product_id_price->profit_percent         =  $First_profit_percent;
                                    $product_id_price->default_sell_price     =  $First_single_dsp;
                                    $product_id_price->sell_price_inc_tax     =  $First_single_dsp_inc_tax;
                                    $product_id_price->number_of_default      =  0 ;     
                                    $product_id_price->unit_id                =  $units ;
                                    $product_id_price->variations_value_id    =  $variation->variation_value_id ;
                                    $product_id_price->variations_template_id =  intVal($value['variation_template_id']) ;
                                    $product_id_price->ks_line                =  $in ;
                                    $product_id_price->save();
                                    $price_ids[]                              =  $product_id_price->id;
                                }else{
                                    $product_price_id->default_purchase_price       =  $First_single_dpp;
                                    $product_price_id->dpp_inc_tax                  =  $First_single_dpp_inc_tax;
                                    $product_price_id->profit_percent               =  $First_profit_percent;
                                    $product_price_id->default_sell_price           =  $First_single_dsp;
                                    $product_price_id->sell_price_inc_tax           =  $First_single_dsp_inc_tax;  
                                    $product_price_id->unit_id                      =  $units ;  
                                    $product_price_id->update();
                                }
                            }
                            $line_id    = 'line_id'.$in;
                            $sn_ddp     = 'single_dpp'.$in;
                            $sn_ddp_inc = 'single_dpp_inc_tax'.$in;
                            $pro_p      = 'profit_percent'.$in;
                            $sn_dsp     = 'single_dsp'.$in;
                            $sn_dsp_inc = 'single_dsp_inc_tax'.$in;
                            
                            #First for list prices
                            $request_line_id            = isset($v[$line_id])?$v[$line_id]:null     ;
                            $request_single_dpp         = $v[$sn_ddp]     ;
                            $request_single_dpp_inc_tax = $v[$sn_ddp_inc] ;
                            $request_profit_percent     = $v[$pro_p]      ;
                            $request_single_dsp         = $v[$sn_dsp]     ;
                            $request_single_dsp_inc_tax = $v[$sn_dsp_inc] ;
                            
                            foreach($request_single_dpp as $kn => $values){
                                if(!isset($request_line_id[$kn]) && $request_line_id != null){ 
                                    $product_price_id                 = \App\Models\ProductPrice::find($request_line_id[$kn]);
                                    $price_ids[]                      = $request_line_id[$kn];
                                }else{
                                    $product_price_id                 = null;
                                }
                                if(empty($product_price_id)){
                                    switch ($kn) {
                                        case 0:
                                            $val = ($index==0)?"Whole Price":"Default Price";
                                            break;
                                        case 1:
                                            $val = ($index==0)?"Retail Price":"Whole Price";
                                            break;
                                        case 2:
                                            $val = ($index==0)?"Minimum Price":"Retail Price";
                                            break;
                                        case 3:
                                            $val = ($index==0)?"Last Price":"Minimum Price";
                                            break;
                                        case 4:
                                            $val = ($index==0)?"ECM Before Price":"Last Price";
                                            break;
                                        case 5:
                                            $val = ($index==0)?"ECM After Price":"ECM Before Price";
                                            break;
                                        case 6:
                                            $val = ($index==0)?"Custom Price 1":"ECM After Price";
                                            break;
                                        case 7:
                                            $val = ($index==0)?"Custom Price 2":"Custom Price 1";
                                            break;
                                        case 8:
                                            $val = ($index==0)?"Custom Price 3":"Custom Price 2";
                                            break;
                                        case 9:
                                            $val = "Custom Price 3";
                                            break;
                                        default:
                                            $val = null;
                
                                    }
                                    $product_id_price                         =  new \App\Models\ProductPrice();
                                    $product_id_price->product_id             =  $product->id ;   
                                    $product_id_price->business_id            =  request()->session()->get('user.business_id') ;   
                                    $product_id_price->name                   =  $val;   
                                    $product_id_price->default_purchase_price =  $this->num_uf($values);
                                    $product_id_price->dpp_inc_tax            =  $this->num_uf($request_single_dpp_inc_tax[$kn]);
                                    $product_id_price->profit_percent         =  $this->num_uf($request_profit_percent[$kn]);
                                    $product_id_price->default_sell_price     =  $this->num_uf($request_single_dsp[$kn]);
                                    $product_id_price->sell_price_inc_tax     =  $this->num_uf($request_single_dsp_inc_tax[$kn]);
                                    $product_id_price->number_of_default      =  ($index==0)?$kn+1:$kn;     
                                    $product_id_price->unit_id                =  $units ;
                                    $product_id_price->variations_value_id    =  $variation->variation_value_id ;
                                    $product_id_price->variations_template_id =  intVal($value['variation_template_id']) ;
                                    $product_id_price->ks_line                =  $in ;
                                    $product_id_price->save();
                                    $price_ids[]                              = $product_id_price->id;
                                }else{
                                    $product_price_id->default_purchase_price       =  $this->num_uf($values);
                                    $product_price_id->dpp_inc_tax                  =  $this->num_uf($request_single_dpp_inc_tax[$kn]);
                                    $product_price_id->profit_percent               =  $this->num_uf($request_profit_percent[$kn]);
                                    $product_price_id->default_sell_price           =  $this->num_uf($request_single_dsp[$kn]);
                                    $product_price_id->sell_price_inc_tax           =  $this->num_uf($request_single_dsp_inc_tax[$kn]);
                                    $product_price_id->update();
                                }
                                
                            }
                        }
                    }
                    $variation->update($data);

                    Media::uploadMedia($product->business_id, $variation, request(), 'edit_variation_images_' . $key . '_' . $k);
                    
                    
                    $variations_ids[] = $k;
                   
                }
                $all_not_in_rows = Variation::whereNotIn('id', $list_of_row_items)->where('product_variation_id',$key)->where("product_id",$product->id)->get();
                if(count($all_not_in_rows)>0){
                    foreach($all_not_in_rows as $line_key => $line_value){
                        $line_value->delete();
                    }
                }
                 
            }
            
            
            
        
            $all_product_delete_id               = \App\Models\ProductPrice::where("product_id",$product->id)->whereNotIn("id",$price_ids)->get();     
            // dd($all_product_delete_id);
            foreach($all_product_delete_id as $line_index => $price_line){
                $price_line->delete();
                
            }
                
             //Add new variations
            if (!empty($value['variations'])) {
                $variation_data = [];
                $c = Variation::withTrashed()
                                ->where('product_id', $product->id)
                                ->count()+1;
                $media = [];
                foreach ($value['variations'] as $k => $v) {
                    $sub_sku = empty($v['sub_sku'])? $this->generateSubSku($product->sku, $c, $product->barcode_type) :$v['sub_sku'];

                    $variation_value_name = !empty($v['value'])? $v['value'] : null;
                    $variation_value_id = null;

                    if (!empty($product_variation->variation_template_id)) {
                        $variation_value =  VariationValueTemplate::where('variation_template_id', $product_variation->variation_template_id)
                                ->whereRaw('LOWER(name)="' . $v['value'] . '"')
                                ->first();
                        if (empty($variation_value)) {
                            $variation_value =  VariationValueTemplate::create([
                                'name' => $v['value'],
                                'variation_template_id' => $product_variation->variation_template_id
                            ]);
                        }

                        $variation_value_id = $variation_value->id;
                    }
                   
                    $variation_data[] = [
                      'name'                      => $variation_value_name,
                      'variation_value_id'        => $variation_value_id,
                      'product_id'                => $product->id,
                      'sub_sku'                   => $sub_sku,
                      'default_purchase_price'    => $this->num_uf($v['default_purchase_price']),
                      'dpp_inc_tax'               => $this->num_uf($v['dpp_inc_tax']),
                      'profit_percent'            => $this->num_uf($v['profit_percent']),
                      'default_sell_price'        => $this->num_uf($v['default_sell_price']),
                      'sell_price_inc_tax'        => $this->num_uf($v['sell_price_inc_tax'])
                    ];
                    $c++;
                    $media[] = 'variation_images_' . $key . '_' . $k;
                    if(count($v['unit_D'])>0){
                        foreach($v['unit_D'] as $index => $unit_value){
                            $in    = $index+1;
                            $units = $unit_value;
                             #section for first line
                            $First_single_dpp           = $this->num_uf($v['default_purchase_price']);
                            $First_single_dpp_inc_tax   = $this->num_uf($v['dpp_inc_tax']);
                            $First_profit_percent       = $this->num_uf($v['profit_percent']);
                            $First_single_dsp           = $this->num_uf($v['default_sell_price']);
                            $First_single_dsp_inc_tax   = $this->num_uf($v['sell_price_inc_tax']);
                            $product_price_id                 = \App\Models\ProductPrice::where("product_id",$product->id)
                                                                        ->whereNull("default_name")
                                                                        ->where("number_of_default",0)
                                                                        ->where("unit_id",$units)
                                                                        ->where("variations_value_id",$variation_value_id)
                                                                        ->where("variations_template_id",$product_variation->variation_template_id)
                                                                        ->first();
                           
                            if(empty($product_price_id)){
                                $product_id_price                         =  new \App\Models\ProductPrice();
                                $product_id_price->product_id             =  $product->id ;   
                                $product_id_price->business_id            =   request()->session()->get('user.business_id') ;  
                                $product_id_price->name                   =  "Default Price" ;   
                                $product_id_price->default_purchase_price =  $First_single_dpp;
                                $product_id_price->dpp_inc_tax            =  $First_single_dpp_inc_tax; 
                                $product_id_price->profit_percent         =  $First_profit_percent;
                                $product_id_price->default_sell_price     =  $First_single_dsp;
                                $product_id_price->sell_price_inc_tax     =  $First_single_dsp_inc_tax;
                                $product_id_price->number_of_default      =  0 ;     
                                $product_id_price->unit_id                =  $units ;
                                $product_id_price->variations_value_id    =  $variation_value_id ;
                                $product_id_price->variations_template_id =  $product_variation->variation_template_id ;
                                $product_id_price->ks_line                =  $in ;
                                $product_id_price->save();
                                $price_ids[]                              = intVal($product_id_price->id);
                            }else{
                                $product_price_id->name                         =  "Default Price";   
                                $product_price_id->default_purchase_price       =  $First_single_dpp;
                                $product_price_id->dpp_inc_tax                  =  $First_single_dpp_inc_tax;
                                $product_price_id->profit_percent               =  $First_profit_percent;
                                $product_price_id->default_sell_price           =  $First_single_dsp;
                                $product_price_id->sell_price_inc_tax           =  $First_single_dsp_inc_tax;
                                $product_price_id->unit_id                      =  $units;   
                                $product_price_id->variations_value_id          =  $variation_value_id;
                                $product_price_id->variations_template_id       =  $product_variation->variation_template_id ;
                                $product_price_id->ks_line                      =  $in ;   
                                $product_price_id->update();
                            }
                            $sn_ddp     = 'single_dpp'.$in;
                            $sn_ddp_inc = 'single_dpp_inc_tax'.$in;
                            $pro_p      = 'profit_percent'.$in;
                            $sn_dsp     = 'single_dsp'.$in;
                            $sn_dsp_inc = 'single_dsp_inc_tax'.$in;
                            
                            #First for list prices
                            $request_single_dpp         = $v[$sn_ddp]     ;
                            $request_single_dpp_inc_tax = $v[$sn_ddp_inc] ;
                            $request_profit_percent     = $v[$pro_p]      ;
                            $request_single_dsp         = $v[$sn_dsp]     ;
                            $request_single_dsp_inc_tax = $v[$sn_dsp_inc] ;

                            foreach($request_single_dpp as $k_index => $values){
                                $number_of_price_in_table         = ($index == 0)?$k_index+1:$k_index;
                                $product_price_id                 = \App\Models\ProductPrice::where("product_id",$product->id)
                                                                        ->whereNull("default_name")
                                                                        ->where("number_of_default",$number_of_price_in_table)
                                                                        ->where("unit_id",$units)
                                                                        ->where("variations_value_id",$variation_value_id)
                                                                        ->where("variations_template_id",$product_variation->variation_template_id)
                                                                        ->first();
                                switch ($k_index) {
                                    case 0:
                                        $val = ($index==0)?"Whole Price":"Default Price";
                                        break;
                                    case 1:
                                        $val = ($index==0)?"Retail Price":"Whole Price";
                                        break;
                                    case 2:
                                        $val = ($index==0)?"Minimum Price":"Retail Price";
                                        break;
                                    case 3:
                                        $val = ($index==0)?"Last Price":"Minimum Price";
                                        break;
                                    case 4:
                                        $val = ($index==0)?"ECM Before Price":"Last Price";
                                        break;
                                    case 5:
                                        $val = ($index==0)?"ECM After Price":"ECM Before Price";
                                        break;
                                    case 6:
                                        $val = ($index==0)?"Custom Price 1":"ECM After Price";
                                        break;
                                    case 7:
                                        $val = ($index==0)?"Custom Price 2":"Custom Price 1";
                                        break;
                                    case 8:
                                        $val = ($index==0)?"Custom Price 3":"Custom Price 2";
                                        break;
                                    case 9:
                                        $val = "Custom Price 3";
                                        break;
                                    default:
                                        $val = null;
                                }
                                if(empty($product_price_id)){
                                    $product_id_price                         =  new \App\Models\ProductPrice();
                                    $product_id_price->product_id             =  $product->id ;   
                                    $product_id_price->business_id            =  request()->session()->get('user.business_id') ;   
                                    $product_id_price->name                   =  $val;   
                                    $product_id_price->default_purchase_price =  $this->num_uf($values);
                                    $product_id_price->dpp_inc_tax            =  $this->num_uf($request_single_dpp_inc_tax[$k_index]);
                                    $product_id_price->profit_percent         =  $this->num_uf($request_profit_percent[$k_index]);
                                    $product_id_price->default_sell_price     =  $this->num_uf($request_single_dsp[$k_index]);
                                    $product_id_price->sell_price_inc_tax     =  $this->num_uf($request_single_dsp_inc_tax[$k_index]);
                                    $product_id_price->number_of_default      =  $number_of_price_in_table ;     
                                    $product_id_price->unit_id                =  $units ;
                                    $product_id_price->variations_value_id    =  $variation_value_id ;
                                    $product_id_price->variations_template_id =  $product_variation->variation_template_id ;
                                    $product_id_price->ks_line                =  $in ;
                                    $product_id_price->save();
                                    $price_ids[]                              = intVal($product_id_price->id);
                                }else{
                                    $product_price_id->name                         =  $val;   
                                    $product_price_id->default_purchase_price       =  $this->num_uf($values);
                                    $product_price_id->dpp_inc_tax                  =  $this->num_uf($request_single_dpp_inc_tax[$k_index]);
                                    $product_price_id->profit_percent               =  $this->num_uf($request_profit_percent[$k_index]);
                                    $product_price_id->default_sell_price           =  $this->num_uf($request_single_dsp[$k_index]);
                                    $product_price_id->sell_price_inc_tax           =  $this->num_uf($request_single_dsp_inc_tax[$k_index]);
                                    $product_price_id->unit_id                      =  $units;   
                                    $product_price_id->variations_value_id          =  $variation_value_id; 
                                    $product_price_id->variations_template_id       =  $product_variation->variation_template_id ;
                                    $product_price_id->ks_line                      =  $in ;  
                                    $product_price_id->update();
                                }
                                
                            }
                        }
                    }
                }
                $new_variations = $product_variation->variations()->createMany($variation_data);

                $i = 0;
                
                foreach ($new_variations as $new_variation) {
                    $variations_ids[] = $new_variation->id;
                    Media::uploadMedia($product->business_id, $new_variation, request(), $media[$i]);
                    $i++;
                }
            }
        }
        
        //Check if purchase or sell exist for the deletable variations
        $count_purchase = PurchaseLine::join(
            'transactions as T',
            'purchase_lines.transaction_id',
            '=',
            'T.id'
            )
              ->where('T.type', 'purchase')
              ->where('T.status', 'received')
              ->where('T.business_id', $product->business_id)
              ->where('purchase_lines.product_id', $product->id)
              ->whereNotIn('purchase_lines.variation_id', $variations_ids)
              ->count();

        $count_sell = TransactionSellLine::join(
            'transactions as T',
            'transaction_sell_lines.transaction_id',
            '=',
            'T.id'
            )
              ->where('T.type', 'sale')
              ->where('T.status', 'final')
              ->where('T.business_id', $product->business_id)
              ->where('transaction_sell_lines.product_id', $product->id)
              ->whereNotIn('transaction_sell_lines.variation_id', $variations_ids)
              ->count();

        $is_variation_delatable = $count_purchase > 0 || $count_sell > 0? false : true;
        
        if ($is_variation_delatable) {
            Variation::whereNotIn('id', $variations_ids)
                ->where('product_variation_id', $key)
                ->get();

        } else {
            throw new \Exception(__('lang_v1.purchase_already_exist'));
        }
        
        $for_delete = ProductVariation::where('product_id', $product_id)
                ->whereNotIn('id', $product_variation_ids)
                ->get();
        foreach($for_delete as $li_key => $li_value){
            $variation_for_delete =  Variation::where('product_variation_id',$li_value->id)->get();
            foreach($variation_for_delete as $l_key => $l_value){
                $l_value->delete() ;
            }
            $li_value->delete();
        }

    }
    /** 5*
     * Checks if products has manage stock enabled then Updates quantity for product and its
     * variations
     *
     * @param $location_id
     * @param $product_id
     * @param $variation_id
     * @param $new_quantity
     * @param $old_quantity = 0
     * @param $number_format = null
     * @param $uf_data = true, if false it will accept numbers in database format
     *
     * @return boolean
     */
    public function updateProductQuantity($location_id, $product_id, $variation_id, $new_quantity, $old_quantity = 0, $number_format = null, $uf_data = true)
    {
        // *1* FOR STYLE INPUT DATA 
        // *** AGT8422
            if ($uf_data) {
                $qty_difference = $this->num_uf($new_quantity, $number_format) - $this->num_uf($old_quantity, $number_format);
            } else {
                $qty_difference = $new_quantity - $old_quantity;
            }
        // ************
        // *2* FOR FIND PRODUCT
        // *** AGT8422
            $product = Product::find($product_id);
        // ************
        // *3* FOR CHECK IF STOCK IS ENABLED AND ADD QUANTITY IN VariationLocationDetails
        // *** AGT8422
            if ($product->enable_stock == 1 && $qty_difference != 0) {
                $variation = Variation::where('id',intval($variation_id))
                                        ->where('product_id', $product_id)
                                        ->first();
                //Add quantity in VariationLocationDetails
                $variation_location_d = VariationLocationDetails::where('variation_id', $variation->id)
                                                ->where('product_id', $product_id)
                                                ->where('product_variation_id', $variation->product_variation_id)
                                                ->where('location_id', $location_id)
                                                ->first();
                if (empty($variation_location_d)) {
                    $variation_location_d = new VariationLocationDetails();
                    $variation_location_d->variation_id = $variation->id;
                    $variation_location_d->product_id = $product_id;
                    $variation_location_d->location_id = $location_id;
                    $variation_location_d->product_variation_id = $variation->product_variation_id;
                    $variation_location_d->qty_available = 0;
                }
                $variation_location_d->qty_available += $qty_difference;
                $variation_location_d->save();

            }
        // ************
        return true;
    }
    /** 6*
     * Checks if products has manage stock enabled then Decrease quantity for product and its variations
     *
     * @param $product_id
     * @param $variation_id
     * @param $location_id
     * @param $new_quantity
     * @param $old_quantity = 0
     *
     * @return boolean
     */
    public  function decreaseProductQuantity($product_id, $variation_id, $location_id, $new_quantity, $old_quantity = 0)
    {
        $qty_difference = $new_quantity - $old_quantity;
        
        $product = Product::find($product_id);
        $variation = \App\Variation::where("product_id",$product_id)->first();
        $variation_id = $variation->id;
        //Check if stock is enabled or not.
        if ($product->enable_stock == 1) {
            //Decrement Quantity in variations location table
            $details = VariationLocationDetails::where('variation_id', $variation_id)
            ->where('product_id', $product_id)
            ->where('location_id', $location_id)
            ->first();
            

            //If location details not exists create new one
            if (empty($details)) {
                $variation = Variation::find($variation_id);
                $details = VariationLocationDetails::create([
                            'product_id' => $product_id,
                            'location_id' => $location_id,
                            'variation_id' => $variation_id,
                            'product_variation_id' => $variation->product_variation_id,
                            'qty_available' => 0
                          ]);
            }

            $details->decrement('qty_available', $qty_difference);
            
        }

        return true;
    }
    /** 7*
     * Decrease the product quantity of combo sub-products
     *
     * @param $combo_details
     * @param $location_id
     *
     * @return void
     */
    public function decreaseProductQuantityCombo($combo_details, $location_id)
    {
        //product_id = child product id
        //variation id is child product variation id
        foreach ($combo_details as $details) {
            $this->decreaseProductQuantity(
                $details['product_id'],
                $details['variation_id'],
                $location_id,
                $details['quantity']
            );
        }
    }
    /** 8*
     * Get all details for a product from its variation id
     *
     * @param int $variation_id
     * @param int $business_id
     * @param int $location_id
     * @param bool $check_qty (If false qty_available is not checked)
     *
     * @return array
     */
    public function getDetailsFromVariation($variation_id, $business_id, $location_id = null, $check_qty = true,$id_product=null)
    {
        // #2024-8-6
        if($id_product != null){
            $variation    = \App\Variation::where("product_id",$id_product)->first();
            $variation_id = $variation->id;
        }
        $query = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
                ->join('product_variations AS pv', 'variations.product_variation_id', '=', 'pv.id')
                ->leftjoin('variation_location_details AS vld', 'variations.id', '=', 'vld.variation_id')
                ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                ->leftjoin('brands', function ($join) {
                    $join->on('p.brand_id', '=', 'brands.id')
                    ->whereNull('brands.deleted_at');
                })
                ->where('p.business_id', $business_id)
                ->where('variations.id', $variation_id);
                //Add condition for check of quantity. (if stock is not enabled or qty_available > 0)
                if ($check_qty) {
                    $query->where(function ($query) use ($location_id) {
                        $query->where('p.enable_stock', '!=', 1)
                        ->orWhere('vld.qty_available', '>', 0);
                        
                    });
                }
                
                if (!empty($location_id) && $check_qty) {
                    //Check for enable stock, if enabled check for location id.
                    $query->where(function ($query) use ($location_id) {
                        $query->where('p.enable_stock', '!=', 1)
                        ->orWhere('vld.location_id', $location_id);
                    });
                }
                
                
        $product = $query->select(
            DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, 
            ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
            'p.id as product_id',
            'p.business_id as business_id',
            'p.fixed_amount',
            'p.max_in_invoice',
            'p.max_discount',
            'p.brand_id',
            'p.category_id',
            'p.tax as tax_id',
            'p.enable_stock',
            'p.enable_sr_no',
            'p.type as product_type',
            'p.name as product_actual_name',
            'p.warranty_id',
            'pv.name as product_variation_name',
            'pv.is_dummy as is_dummy',
            'variations.name as variation_name',
            'variations.sub_sku',
            'p.barcode_type',
            'vld.qty_available',
            'variations.default_sell_price',
            'variations.id as variation_id',
            'variations.default_purchase_price',
            'variations.sell_price_inc_tax',
            'variations.id as variation_id',
            'variations.combo_variations',  //Used in combo products
            'units.short_name as unit',
            'units.actual_name as actual_name',
            'units.id as unit_id',
            'units.base_unit_multiplier as base_unit_multiplier',
            'units.allow_decimal as unit_allow_decimal',
            'brands.name as brand',
            DB::raw("(SELECT purchase_price_inc_tax FROM purchase_lines WHERE 
            variation_id=variations.id ORDER BY id DESC LIMIT 1) as last_purchased_price")
        )
        ->first();
        $var                       = \App\Variation::find($variation_id);
        $qty                       = \App\Models\WarehouseInfo::where("product_id",$var->product_id)->sum("product_qty");
        if( $product == null && $qty > 0){
            $query = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
                                ->join('product_variations AS pv', 'variations.product_variation_id', '=', 'pv.id')
                                ->leftjoin('variation_location_details AS vld', 'variations.id', '=', 'vld.variation_id')
                                ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                                ->leftjoin('brands', function ($join) {
                                    $join->on('p.brand_id', '=', 'brands.id')
                                    ->whereNull('brands.deleted_at');
                                })
                                ->where('p.business_id', $business_id)
                                ->where('variations.id', $variation_id);
                                //Add condition for check of quantity. (if stock is not enabled or qty_available > 0)
                            
                                if (!empty($location_id) && $check_qty) {
                                    //Check for enable stock, if enabled check for location id.
                                    $query->where(function ($query) use ($location_id) {
                                        $query->where('p.enable_stock', '!=', 1)
                                        ->orWhere('vld.location_id', $location_id);
                                    });
                                }
                
                
        $product = $query->select(
                        DB::raw("IF(pv.is_dummy = 0, CONCAT(p.name, 
                        ' (', pv.name, ':',variations.name, ')'), p.name) AS product_name"),
                        'p.id as product_id',
                        'p.fixed_amount',
                        'p.max_in_invoice',
                        'p.max_discount',
                        'p.brand_id',
                        'p.category_id',
                        'p.tax as tax_id',
                        'p.enable_stock',
                        'p.enable_sr_no',
                        'p.type as product_type',
                        'p.name as product_actual_name',
                        'p.warranty_id',
                        'pv.name as product_variation_name',
                        'pv.is_dummy as is_dummy',
                        'variations.name as variation_name',
                        'variations.sub_sku',
                        'p.barcode_type',
                        'vld.qty_available',
                        'variations.default_sell_price',
                        'variations.id as variation_id',
                        'variations.default_purchase_price',
                        'variations.sell_price_inc_tax',
                        'variations.id as variation_id',
                        'variations.combo_variations',  //Used in combo products
                        'units.short_name as unit',
                        'units.id as unit_id',
                        'units.allow_decimal as unit_allow_decimal',
                        'brands.name as brand',
                        DB::raw("(SELECT purchase_price_inc_tax FROM purchase_lines WHERE 
                        variation_id=variations.id ORDER BY id DESC LIMIT 1) as last_purchased_price")
                    )
                    ->firstOrFail();
        }
        
        if ($product->product_type == 'combo') {
            if ($check_qty) {
                $product->qty_available = $this->calculateComboQuantity($location_id, $product->combo_variations);
            }
            
            $product->combo_products = $this->calculateComboDetails($location_id, $product->combo_variations);
        }
        
        return $product;
    }
        
    // 9* ...........\
    //  quotation   
    public function getDetailsFromVariationQ($variation_id, $business_id, $location_id = null, $check_qty = true,$id_product=null)
    {
        if($id_product != null){
            $variation    = \App\Variation::where("product_id",$id_product)->first();
            $variation_id = $variation->id;
            $pro          = Variation::where("product_id",$id_product)->first();
            $pro_name     = Product::where("id",$id_product)->first();
        }else{
            $pro          = Variation::find($variation_id);
            $pro_name     = Product::where("id",$pro->product_id)->first();
        
        }
      
        $query    = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
         ->join('product_variations AS pv', 'variations.product_variation_id', '=', 'pv.id')
                ->leftjoin('variation_location_details AS vld', 'variations.id', '=', 'vld.variation_id')
                ->leftjoin('units', 'p.unit_id', '=', 'units.id')
                ->leftjoin('brands', function ($join) {
                    $join->on('p.brand_id', '=', 'brands.id')
                    ->whereNull('brands.deleted_at');
                })
                ->where('p.business_id', $business_id)
                ->where('variations.id', $pro->id);
       
        $product  = $query->first();
        
        $product->product_id = $pro->product_id;
        $product->name = $pro_name->name;
        $product->product_variation_id = $pro->id;
          
          
        return $product;
    }
    /** 10*
     * Calculates the quantity of combo products based on
     * the quantity of variation items used.
     *
     * @param int $location_id
     * @param array $combo_variations
     *
     * @return int
     */
    public function calculateComboQuantity($location_id, $combo_variations)
    {
      //get stock of the items and calcuate accordingly.
        $combo_qty = 0;
        foreach ($combo_variations as $key => $value) {
            $variation = Variation::with(['product', 'variation_location_details' => function($q) use ($location_id){
                $q->where('location_id', $location_id);
            }])->findOrFail($value['variation_id']);

            $product = $variation->product;

            //Dont calculate stock if disabled
            if ($product->enable_stock != 1) {
                continue;
            }

            $vld = $variation->variation_location_details
                          ->first();

            $variation_qty = !empty($vld) ? $vld->qty_available : 0;
            $multiplier = $this->getMultiplierOf2Units($product->unit_id, $value['unit_id']);

            if ($combo_qty == 0) {
                $combo_qty = ($variation_qty/$multiplier) / $combo_variations[$key]['quantity'];
            } else {
                $combo_qty = min($combo_qty, ($variation_qty/$multiplier) / $combo_variations[$key]['quantity']);
            }
        }

        return floor($combo_qty);
    }
    /** 11*
     * Calculates the quantity of combo products based on
     * the quantity of variation items used.
     *
     * @param int $location_id
     * @param array $combo_variations
     *
     * @return int
     */
    public function calculateComboDetails($location_id, $combo_variations)
    {
        $details = [];

        foreach ($combo_variations as $key => $value) {
            $variation = Variation::with(['product', 'variation_location_details' => function($q) use ($location_id){
                $q->where('location_id', $location_id);
            }])->findOrFail($value['variation_id']);

            $vld = $variation->variation_location_details->first();

            $variation_qty = !empty($vld) ? $vld->qty_available : 0;
            $multiplier = $this->getMultiplierOf2Units($variation->product->unit_id, $value['unit_id']);

            $details[] = [
              'variation_id' => $value['variation_id'],
              'product_id' => $variation->product_id,
              'qty_required' => $this->num_uf($value['quantity']) * $multiplier,
              'enable_stock' => $variation->product->enable_stock
            ];
        }

        return $details;
    }
    /** 12*
     * Calculates the total amount of invoice
     *
     * @param array $products
     * @param int $tax_id
     * @param array $discount['discount_type', 'discount_amount']
     *
     * @return Mixed (false, array)
     */
    public function calculateInvoiceTotal($products, $tax_id, $discount = null, $uf_number = true)
    {
        if (empty($products)) {
            return false;
        }

        $output = ['total_before_tax' => 0, 'tax' => 0, 'discount' => 0, 'final_total' => 0];

        //Sub Total
         foreach ($products as $product) {
            $unit_price_inc_tax = $uf_number ? $this->num_uf($product['unit_price']) : $product['unit_price'];
            $quantity = $uf_number ? $this->num_uf($product['quantity']) : $product['quantity'];
            if (isset($product['line_discount_amount___'])) {
                $output['total_before_tax'] += $quantity * ($unit_price_inc_tax - $product['line_discount_amount___']);
            }else{
                $output['total_before_tax'] += $quantity * $unit_price_inc_tax;
            }
            

            //Add modifier price to total if exists
            if (!empty($product['modifier_price'])) {
                foreach ($product['modifier_price'] as $key => $modifier_price) {
                    $modifier_price = $uf_number ? $this->num_uf($modifier_price) : $modifier_price;
                    $uf_modifier_price = $this->num_uf($modifier_price);
                    $modifier_qty = isset($product['modifier_quantity'][$key]) ? $product['modifier_quantity'][$key] : 0;
                    $modifier_total = $uf_modifier_price * $modifier_qty;
                    $output['total_before_tax'] += $modifier_total;
                }
            }
        }

        //Calculate discount
        if (is_array($discount)) {
            $discount_amount = $uf_number ? $this->num_uf($discount['discount_amount']) : $discount['discount_amount'];
            if ($discount['discount_type'] == 'fixed') {
                $output['discount'] = $discount_amount;
            } else {
                $output['discount'] = ($discount_amount/100)*$output['total_before_tax'];
            }
        }

        //Tax
        $output['tax'] = 0;
        if (!empty($tax_id)) {
            $tax_details = TaxRate::find($tax_id);
            if (!empty($tax_details)) {
                $output['tax_id'] = $tax_id;
                $output['tax'] = ($tax_details->amount/100) * ($output['total_before_tax'] - $output['discount']);
            }
        }

        //Calculate total
        $output['final_total'] = $output['total_before_tax'] + $output['tax'] - $output['discount'];

        return $output;
    }
    /** 13*
     * Generates product sku
     *
     * @param string $string
     *
     * @return generated sku (string)
     */
    public function generateProductSku($string)
    {
        $business_id = request()->session()->get('user.business_id');
        $sku_prefix = Business::where('id', $business_id)->value('sku_prefix');

        return $sku_prefix . str_pad($string, 4, '0', STR_PAD_LEFT);
    }
    /** 14*
     * Gives list of trending products
     *
     * @param int $business_id
     * @param array $filters
     *
     * @return Obj
     */
    public function getTrendingProducts($business_id, $filters = [])
    {
        $query = Transaction::join(
            'transaction_sell_lines as tsl',
            'transactions.id',
            '=',
            'tsl.transaction_id'
        )
                    ->join('products as p', 'tsl.product_id', '=', 'p.id')
                    ->leftjoin('units as u', 'u.id', '=', 'p.unit_id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'sell')
                    ->where('transactions.status', 'final');

        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($filters['location_id'])) {
            $query->where('transactions.location_id', $filters['location_id']);
        }
        if (!empty($filters['category'])) {
            $query->where('p.category_id', $filters['category']);
        }
        if (!empty($filters['sub_category'])) {
            $query->where('p.sub_category_id', $filters['sub_category']);
        }
        if (!empty($filters['brand'])) {
            $query->where('p.brand_id', $filters['brand']);
        }
        if (!empty($filters['unit'])) {
            $query->where('p.unit_id', $filters['unit']);
        }
        if (!empty($filters['limit'])) {
            $query->limit($filters['limit']);
        } else {
            $query->limit(5);
        }

        if (!empty($filters['product_type'])) {
            $query->where('p.type', $filters['product_type']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$filters['start_date'],
                $filters['end_date']]);
        }

        // $sell_return_query = "(SELECT SUM(TPL.quantity) FROM transactions AS T JOIN purchase_lines AS TPL ON T.id=TPL.transaction_id WHERE TPL.product_id=tsl.product_id AND T.type='sell_return'";
        // if ($permitted_locations != 'all') {
        //     $sell_return_query .= ' AND T.location_id IN ('
        //      . implode(',', $permitted_locations) . ') ';
        // }
        // if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
        //     $sell_return_query .= ' AND date(T.transaction_date) BETWEEN \'' . $filters['start_date'] . '\' AND \'' . $filters['end_date'] . '\'';
        // }
        // $sell_return_query .= ')';

        $products = $query->select(
            DB::raw("(SUM(tsl.quantity) - COALESCE(SUM(tsl.quantity_returned), 0)) as total_unit_sold"),
            'p.name as product',
            'u.short_name as unit'
        )->whereNull('tsl.parent_sell_line_id')
                        ->groupBy('tsl.product_id')
                        ->orderBy('total_unit_sold', 'desc')
                        ->get();
        return $products;
    }
    /** 15*
     * Gives list of products based on products id and variation id
     *
     * @param int $business_id
     * @param int $product_id
     * @param int $variation_id = null
     *
     * @return Obj
     */
    public function getDetailsFromProduct($business_id, $product_id, $variation_id = null)
    {
        $product = Product::leftjoin('variations as v', 'products.id', '=', 'v.product_id')
                        ->whereNull('v.deleted_at')
                        ->where('products.business_id', $business_id);

        if (!is_null($variation_id) && $variation_id !== '0') {
            $product->where('v.id', $variation_id);
        }

        $product->where('products.id', $product_id);

        $products = $product->select(
            'products.id as product_id',
            'products.sku2 as sku2',
            'products.name as product_name',
            'v.id as variation_id',
            'v.name as variation_name',
            'v.sub_sku as barcode'
            ,'v.id as variation_id'
        )
                    ->get();

        return $products;
    }
    /** *F 16*
     * F => D (Previous product Increase)
     * D => F (All product decrease)
     * F => F (Newly added product drerease)
     *
     * @param  object  $transaction_before
     * @param  object  $transaction
     * @param  array   $input
     *
     * @return void
     */
    public function adjustProductStockForInvoice($status_before, $transaction, $input, $uf_data = true)
    {
        if ($status_before == 'final' && $transaction->status == 'Delivered') {
            foreach ($input['products'] as $product) {
                try{
                    $uf_quantity = $uf_data ? $this->num_uf($product['quantity']) : $product['quantity'];
                    $this->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $input['location_id'],
                        $uf_quantity
                    );
                    //Adjust quantity for combo items.
                    if (isset($product['product_type']) && $product['product_type'] == 'combo') {
                        // $this->decreaseProductQuantityCombo($product['combo'], $input['location_id']);
                        // $this->decreaseProductQuantityCombo($product['variation_id'], $input['location_id'], $uf_quantity);
                    }
                }catch(Exception $e){
                    // dd("خطأ"); 
                }
            }
        } elseif ($status_before == 'final' && $transaction->status == 'draft') {
            foreach ($input['products'] as $product) {
                if (!empty($product['transaction_sell_lines_id'])) {
                    try{
                        // $this->updateProductQuantity($input['location_id'], $product['product_id'], $product['variation_id'], $product['quantity'], 0, null, false);
                        //Adjust quantity for combo items.
                        if (isset($product['product_type']) && $product['product_type'] == 'combo') {
                            //Giving quantity in minus will increase the qty
                            foreach ($product['combo'] as $value) {
                                // $this->updateProductQuantity($input['location_id'], $value['product_id'], $value['variation_id'], $value['quantity'], 0, null, false);
                            }
                            // $this->updateEditedSellLineCombo($product['combo'], $input['location_id']);
                        }
                    }catch(Exception $e){
                        // dd("خطأ"); 
                    }
                }
            }
        } elseif ($status_before == 'draft' && $transaction->status == 'final') {
            foreach ($input['products'] as $product) {
                try{
                    $this->decreaseProductQuantity(
                        $product['product_id'],
                        $product['variation_id'],
                        $input['location_id'],
                        $uf_quantity
                    );
                    //Adjust quantity for combo items.
                    if (isset($product['product_type']) && $product['product_type'] == 'combo') {
                        // $this->decreaseProductQuantityCombo($product['combo'], $input['location_id']);
                        //$this->decreaseProductQuantityCombo($product['variation_id'], $input['location_id'], $uf_quantity);
                    }
                }catch(Exception $e){
                    // dd("خطأ"); 
                }
            }
        } elseif ($status_before == 'final' && $transaction->status == 'final') {
             foreach ($input['products'] as $product) {
                 try{
                    //Adjust quantity for combo items.
                    if (isset($product['product_type']) && $product['product_type'] == 'combo') {
                        // $this->decreaseProductQuantityCombo($product['combo'], $input['location_id']);
                        //$this->decreaseProductQuantityCombo($product['variation_id'], $input['location_id'], $uf_quantity);
                    }
                }catch(Exception $e){
                    // dd("خطأ"); 
                }
             }
        }
    }
    /** 17*
     * Updates variation from purchase screen
     *
     * @param array $variation_data
     *
     * @return void
     */
    public function updateProductFromPurchase($variation_data)
    {
        $variation_details = Variation::where('id', $variation_data['variation_id'])
                                        ->with(['product', 'product.product_tax'])
                                        ->first();
        $tax_rate = 0;
        if (!empty($variation_details->product->product_tax->amount)) {
            $tax_rate = $variation_details->product->product_tax->amount;
        }

        if (!isset($variation_data['sell_price_inc_tax'])) {
            $variation_data['sell_price_inc_tax'] = $variation_details->sell_price_inc_tax;
        }

        if (($variation_details->default_purchase_price != $variation_data['pp_without_discount']) ||
            ($variation_details->sell_price_inc_tax != $variation_data['sell_price_inc_tax'])) {
            //Set default purchase price exc. tax
            $variation_details->default_purchase_price = $variation_data['pp_without_discount'];

            //Set default purchase price inc. tax
            $variation_details->dpp_inc_tax = $this->calc_percentage($variation_details->default_purchase_price, $tax_rate, $variation_details->default_purchase_price);

            //Set default sell price inc. tax
            $variation_details->sell_price_inc_tax = $variation_data['sell_price_inc_tax'];

            //set sell price inc. tax
            $variation_details->default_sell_price = $this->calc_percentage_base($variation_details->sell_price_inc_tax, $tax_rate);

            //set profit margin
            $variation_details->profit_percent = $this->get_percent($variation_details->default_purchase_price, $variation_details->default_sell_price);

            $variation_details->save();
        }
    }
    /** 18*
     * Generated SKU based on the barcode type.
     *
     * @param string $sku
     * @param string $c
     * @param string $barcode_type
     *
     * @return void
     */
    public function generateSubSku($sku, $c, $barcode_type)
    {
        $sub_sku = $sku . $c;

        if (in_array($barcode_type, ['C128', 'C39'])) {
            $sub_sku = $sku . '-' . $c;
        }

        return $sub_sku;
    }
    /** 19*
     * Add rack details.
     *
     * @param int $business_id
     * @param int $product_id
     * @param array $product_racks
     * @param array $product_racks
     *
     * @return void
     */
    public function addRackDetails($business_id, $product_id, $product_racks)
    {
        if (!empty($product_racks)) {
            $data = [];
            foreach ($product_racks as $location_id => $detail) {
                $data[] = ['business_id' => $business_id,
                        'location_id' => $location_id,
                        'product_id' => $product_id,
                        'rack' => !empty($detail['rack']) ? $detail['rack'] : null,
                        'row' => !empty($detail['row']) ? $detail['row'] : null,
                        'position' => !empty($detail['position']) ? $detail['position'] : null,
                        'created_at' => \Carbon::now()->toDateTimeString(),
                        'updated_at' => \Carbon::now()->toDateTimeString()
                    ];
            }

            ProductRack::insert($data);
        }
    }
    /** 20*
     * Get rack details.
     *
     * @param int $business_id
     * @param int $product_id
     *
     * @return void
     */
    public function getRackDetails($business_id, $product_id, $get_location = false)
    {
        $query = ProductRack::where('product_racks.business_id', $business_id)
                    ->where('product_id', $product_id);

        if ($get_location) {
            $racks = $query->join('business_locations AS BL', 'product_racks.location_id', '=', 'BL.id')
                ->select(['product_racks.rack',
                        'product_racks.row',
                        'product_racks.position',
                        'BL.name'])
                ->get();
        } else {
            $racks = collect($query->select(['rack', 'row', 'position', 'location_id'])->get());

            $racks = $racks->mapWithKeys(function ($item, $key) {
                return [$item['location_id'] => $item->toArray()];
            })->toArray();
        }

        return $racks;
    }
    /** 21*
     * Update rack details.
     *
     * @param int $business_id
     * @param int $product_id
     * @param array $product_racks
     *
     * @return void
     */
    public function updateRackDetails($business_id, $product_id, $product_racks)
    {
        if (!empty($product_racks)) {
            foreach ($product_racks as $location_id => $details) {
                ProductRack::where('business_id', $business_id)
                    ->where('product_id', $product_id)
                    ->where('location_id', $location_id)
                    ->update(['rack' => !empty($details['rack']) ? $details['rack'] : null,
                            'row' => !empty($details['row']) ? $details['row'] : null,
                            'position' => !empty($details['position']) ? $details['position'] : null
                        ]);
            }
        }
    }
    /** 22*
     * Retrieves selling price group price for a product variation.
     *
     * @param int $variation_id
     * @param int $price_group_id
     * @param int $tax_id
     *
     * @return decimal
     */
    public function getVariationGroupPrice($variation_id, $price_group_id, $tax_id)
    {
        $price_inc_tax =
        VariationGroupPrice::where('variation_id', $variation_id)
                        ->where('price_group_id', $price_group_id)
                        ->value('price_inc_tax');

        $price_exc_tax = $price_inc_tax;
        if (!empty($price_inc_tax) && !empty($tax_id)) {
            $tax_amount = TaxRate::where('id', $tax_id)->value('amount');
            $price_exc_tax = $this->calc_percentage_base($price_inc_tax, $tax_amount);
        }
        return [
            'price_inc_tax' => $price_inc_tax,
            'price_exc_tax' => $price_exc_tax
        ];
    }
    /** 23*
     * Creates new variation if not exists.
     *
     * @param int $business_id
     * @param string $name
     *
     * @return obj
     */
    public function createOrNewVariation($business_id, $name)
    {
        $variation = VariationTemplate::where('business_id', $business_id)
                                    ->where('name', 'like', $name)
                                    ->with(['values'])
                                    ->first();

        if (empty($variation)) {
            $variation = VariationTemplate::create([
            'business_id' => $business_id,
            'name' => $name
            ]);
        }
        return $variation;
    }

    /**
     * Adds opening stock to a single product.
     *
     * @param int $business_id
     * @param obj $product
     * @param array $input
     * @param obj $transaction_date
     * @param int $user_id
     *
     * @return void
     */
    public function addSingleProductOpeningStock($business_id, $product, $input, $transaction_date, $user_id)
    {
        $locations = BusinessLocation::forDropdown($business_id)->toArray();

        $tax_percent = !empty($product->product_tax->amount) ? $product->product_tax->amount : 0;
        $tax_id = !empty($product->product_tax->id) ? $product->product_tax->id : null;

        foreach ($input as $key => $value) {
            $location_id = $key;
            $purchase_total = 0;
            //Check if valid location
            if (array_key_exists($location_id, $locations)) {
                $purchase_lines = [];

                $purchase_price = $this->num_uf(trim($value['purchase_price']));
                $item_tax = $this->calc_percentage($purchase_price, $tax_percent);
                $purchase_price_inc_tax = $purchase_price + $item_tax;
                $qty = $this->num_uf(trim($value['quantity']));

                $exp_date = null;
                if (!empty($value['exp_date'])) {
                    $exp_date = \Carbon::createFromFormat('d-m-Y', $value['exp_date'])->format('Y-m-d');
                }

                $lot_number = null;
                if (!empty($value['lot_number'])) {
                    $lot_number = $value['lot_number'];
                }

                if ($qty > 0) {
                    $qty_formated = $this->num_f($qty);
                    //Calculate transaction total
                    $purchase_total += ($purchase_price_inc_tax * $qty);
                    $variation_id = $product->variations->first()->id;

                    $purchase_line = new PurchaseLine();
                    $purchase_line->product_id = $product->id;
                    $purchase_line->store_id = 1;
                    $purchase_line->variation_id = $variation_id;
                    $purchase_line->item_tax = $item_tax;
                    $purchase_line->tax_id = $tax_id;
                    $purchase_line->quantity = $qty;
                    $purchase_line->pp_without_discount = $purchase_price;
                    $purchase_line->purchase_price = $purchase_price;
                    $purchase_line->purchase_price_inc_tax = $purchase_price_inc_tax;
                    $purchase_line->exp_date = $exp_date;
                    $purchase_line->lot_number = $lot_number;
                    $purchase_lines[] = $purchase_line;

                    $this->updateProductQuantity($location_id, $product->id, $variation_id, $qty_formated);
                }

                //create transaction & purchase lines
                if (!empty($purchase_lines)) {
                    $transaction = Transaction::create(
                        [
                  'type' => 'opening_stock',
                  'opening_stock_product_id' => $product->id,
                  'status' => 'received',
                  'business_id' => $business_id,
                //   'store' => $transaction_date,
                  'transaction_date' => $transaction_date,
                  'total_before_tax' => $purchase_total,
                  'location_id' => $location_id,
                  'final_total' => $purchase_total,
                  'payment_status' => 'paid',
                  'created_by' => $user_id
                ]
              );
                    $transaction->purchase_lines()->saveMany($purchase_lines);
                }
            }
        }
    }

    /** *F
     * Add/Edit transaction purchase lines
     *
     * @param object $transaction
     * @param array $input_data
     * @param array $currency_details
     * @param boolean $enable_product_editing
     * @param string $before_status = null
     *
     * @return array
     */ 
    // public function createOrUpdatePurchaseLines($transaction, $input_data, $currency_details, $enable_product_editing, $before_status = null ,$type_purchase=null)
    // {
    //     $updated_purchase_lines = [];
    //     $updated_purchase_line_ids = [0];
    //     $exchange_rate = !empty($transaction->exchange_rate) ? $transaction->exchange_rate : 1;
        

    //     foreach ($input_data as $data) {
    //         $multiplier = 1;
    //         if (isset($data['sub_unit_id']) && $data['sub_unit_id'] == $data['product_unit_id']) {
    //             unset($data['sub_unit_id']);
    //         }       
    //         if (!empty($data['sub_unit_id'])) {
    //             $unit = Unit::find($data['sub_unit_id']);
    //             $multiplier = !empty($unit->base_unit_multiplier) ? $unit->base_unit_multiplier : 1;
    //         }
    //         $new_quantity   =  $data['quantity']  * $multiplier;         
    //         $new_quantity_f =  $new_quantity ;    



    //         //update existing purchase line
    //         if (isset($data['purchase_line_id'])) {
              
    //             $purchase_line = PurchaseLine::findOrFail($data['purchase_line_id']);
    //             $updated_purchase_line_ids[] = $purchase_line->id;
    //             $old_qty =  $purchase_line->quantity ;
    //             $this->updateProductStock($before_status, $transaction, $data['product_id'], $data['variation_id'], $new_quantity, $purchase_line->quantity, $currency_details);
            
    //         } else {

    //             //create newly added purchase lines
    //             $purchase_line = new PurchaseLine();
    //             $purchase_line->store_id = $transaction->store;
    //             $purchase_line->product_id = $data['product_id'];            
    //             $purchase_line->variation_id = $data['variation_id'];
    //             $product_unit = Product::where("id",$purchase_line->product_id)->first();
    //             // Increase quantity only if status is received
    //             $total = "";
    //             $total = $new_quantity_f;
    //             $last_price = 0;
    //             $cost = 0; $without_contact = 0; 
                 
    //             $data_ship = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();

    //             if(!empty($data_ship)){
    //                 $ids       = $data_ship->items->pluck("id");
    //                 foreach($ids as $i){
    //                     $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
    //                     if($data_shippment->contact_id == $transaction->contact_id){ 
    //                         $cost += $data_shippment->total;
    //                     }else{
    //                         $without_contact += $data_shippment->total;
    //                     }
    //                  }
    //             }    

    //             $before = \App\Models\WarehouseInfo::qty_before($transaction);
                 
    //             $price  = PurchaseLine::orderby("id","desc")->where("product_id",$data['product_id'])->first();

    //             if ($transaction->status == 'received'  && $transaction->type != 'production_purchase') {
    //                     $this->updateProductQuantity($transaction->location_id, $data['product_id'], $data['variation_id'], $new_quantity_f, 0, $currency_details);
    //                     $date = Carbon::now();
    //                     $WarehouseInfo1 = WarehouseInfo::select()->get();
    //                     $state = "new";
    //                     $quantity = "";
    //                     $W_info = array();
    //                     $W_info["business_id"] =  $transaction->business_id;
    //                     $W_info["store_id"]    =  $transaction->store;
    //                     $W_info["product_id"]  =  $data['product_id'];
    //                     $W_info["product_qty"] =  $new_quantity_f;
    //                     $W_info["created_at"]  =  $date;
    //                     $W_info["updated_at"]  =  $date;

    //                     WarehouseInfo::update_stoct($data['product_id'],$transaction->store,$new_quantity_f,$transaction->business_id);

    //                     $prev                  =  new RecievedPrevious;
    //                     $prev->product_id      =  $data['product_id'];
    //                     $prev->store_id        =  $transaction->store;
    //                     $prev->business_id     =  $transaction->business_id ;
    //                     $prev->transaction_id  =  $transaction->id;
    //                     $prev->unit_id         =  $product_unit->unit_id;
    //                     $prev->total_qty       =  $new_quantity_f;
    //                     $prev->current_qty     =  $new_quantity_f;
    //                     $prev->remain_qty      =  0;
    //                     // $prev->transaction_deliveries_id   =  $tr_recieved->id;
    //                     $prev->product_name    =  $product_unit->name;  
    //                     $prev->line_id         =  $purchase_line->id;  
    //                     $prev->save();
    //                     //***  ........... eb
    //                     // WarehouseInfo::update_stoct($data['product_id'],$transaction->store,$new_quantity_f,$transaction->business_id);
    //                     MovementWarehouse::movemnet_warehouse($transaction,$product_unit,$total,$transaction->store,$price,'plus',$prev->id);
                        
    //                     //......................................................
                        
    //             }

                
    //         }   
            
            
    //         $purchase_line->quantity = $new_quantity;
    //         if(isset( $data['pp_without_discount_s'])){
    //             $purchase_line->pp_without_discount = ($this->num_uf($data['pp_without_discount_s'], $currency_details)*$exchange_rate) / $multiplier;
    //         }else{
    //             $purchase_line->pp_without_discount = ($this->num_uf($data['pp_without_discount'], $currency_details)*$exchange_rate) / $multiplier;
    //         }
          
    //         $purchase_line->discount_percent       = $this->num_uf($data["discount_percent"], $currency_details);
    //         $purchase_line->purchase_price         = ($this->num_uf($data['purchase_price'], $currency_details)*$exchange_rate) / $multiplier;
    //         $purchase_line->purchase_price_inc_tax = ($this->num_uf($data['unit_cost_after_tax'], $currency_details)*$exchange_rate) / $multiplier;
    //         $purchase_line->item_tax      = ($this->num_uf($data['item_tax'], $currency_details)*$exchange_rate) / $multiplier;
    //         $purchase_line->tax_id        = $transaction->tax_id;
    //         $purchase_line->lot_number    = !empty($data['lot_number']) ? $data['lot_number'] : null;
    //         $purchase_line->mfg_date      = !empty($data['mfg_date']) ? $this->uf_date($data['mfg_date']) : null;
    //         $purchase_line->exp_date      = !empty($data['exp_date']) ? $this->uf_date($data['exp_date']) : null;
    //         $purchase_line->sub_unit_id   = !empty($data['sub_unit_id']) ? $data['sub_unit_id'] : null;
    //         $purchase_line->purchase_note = !empty($data['purchase_note']) ? $data['purchase_note'] : null;
    //         $updated_purchase_lines[] = $purchase_line;
    //         //Edit product price
    //         if ($enable_product_editing == 1) {
    //             if (isset($data['default_sell_price'])) {
    //                 $variation_data['sell_price_inc_tax'] = ($this->num_uf($data['default_sell_price'], $currency_details)) / $multiplier;
    //             }
    //             $variation_data['pp_without_discount'] = ($this->num_uf($data['pp_without_discount'], $currency_details)*$exchange_rate) / $multiplier;
    //             $variation_data['variation_id'] = $purchase_line->variation_id;
    //             $variation_data['purchase_price'] = $purchase_line->purchase_price;
    //             $this->updateProductFromPurchase($variation_data);
    //         }

    //         if($transaction->status == 'received'  && $transaction->type != 'production_purchase'){
    //             //.....................................................................
    //             $tr_recieved      = \App\Models\TransactionRecieved::where("transaction_id",$transaction->id)->first();
    //             if(empty($tr_recieved)){
    //                 $type        = 'purchase_receive';
    //                 $ref_count   = $this->setAndGetReferenceCount($type);
    //                 $reciept_no  = $this->generateReferenceNumber($type, $ref_count);
    //                 $tr_recieved                  =  new TransactionRecieved;
    //                 $tr_recieved->store_id        =  $transaction->store;
    //                 $tr_recieved->transaction_id  =  $transaction->id;
    //                 $tr_recieved->business_id     =  $transaction->business_id ;
    //                 $tr_recieved->reciept_no      =  $reciept_no ;
    //                 $tr_recieved->ref_no          =  $transaction->ref_no;
    //                 $tr_recieved->status          = 'purchase';
    //                 $tr_recieved->save();
    //             }
    //             $prevss = \App\Models\RecievedPrevious::where("transaction_id",$transaction->id)->get();
    //              foreach($prevss as $pr){
    //                 $item_prv = \App\Models\RecievedPrevious::find($pr->id); 
    //                 $item_prv->transaction_deliveries_id = $tr_recieved->id;
    //                 $item_prv->update();
    //              }

    //         }
    //     }

    //     $total_ship = \App\Models\Purchase::supplier_shipp($transaction->id);
    //     if($type_purchase == null){
    //         if (($transaction->status == 'received' || $transaction->status == 'final') &&  $transaction->type != 'production_purchase') {
    //             \App\AccountTransaction::add_purchase($transaction,$total_ship);
    //         }
    //     }      
    //     //unset deleted purchase lines
    //     $delete_purchase_line_ids = [];
    //     $delete_purchase_lines = null; 
    //     if (!empty($updated_purchase_line_ids)) {
    //             $delete_purchase_lines = PurchaseLine::where('transaction_id', $transaction->id)
    //                                                     ->whereNotIn('id', $updated_purchase_line_ids)
    //                                                     ->get();                  
    //         if ($delete_purchase_lines->count()) {
    //                 foreach ($delete_purchase_lines as $delete_purchase_line) {
    //                     $delete_purchase_line_ids[] = $delete_purchase_line->id;
    //                     //decrease deleted only if previous status was received
    //                     if ($before_status == 'received') {
    //                         $this->decreaseProductQuantity(
    //                             $delete_purchase_line->product_id,
    //                             $delete_purchase_line->variation_id,
    //                             $transaction->location_id,
    //                             $delete_purchase_line->quantity
    //                         );
    //                     }
    //                 }
    //                 //Delete deleted purchase lines
    //                 PurchaseLine::where('transaction_id', $transaction->id)
    //                                   ->whereIn('id', $delete_purchase_line_ids)
    //                                   ->delete();
    //         }
    //     }
    //     //update purchase lines
    //     if (!empty($updated_purchase_lines)) {
    //         $transaction->purchase_lines()->saveMany($updated_purchase_lines);
    //     }

    //     return $delete_purchase_lines;
    // }
    public function createOrUpdatePurchaseLines($transaction, $input_data, $currency_details, $enable_product_editing, $before_status = null ,$type_purchase=null,$archive=null,$request=null,$info=null)
    {
        // *** FOR PURCHASE DATA
        // *** AGT8422 
            // *1* FOR INITIALIZE DATA
            // *** AGT8422
                $updated_purchase_lines      = [];
                $updated_purchase_line_ids   = [0];
                $exchange_rate               = !empty($transaction->exchange_rate) ? $transaction->exchange_rate : 1;
            // ************
            // *2* FOR UPDATE ROWS DATA IN PURCHASE LINE
            // *** AGT8422
                foreach ($input_data as $ky => $data) {
                    $multiplier = 1;
                    // *2/1* UNIT DATA
                        if (isset($data['sub_unit_id']) && $data['sub_unit_id'] == $data['product_unit_id']) {
                            unset($data['sub_unit_id']);
                        }       
                        if (!empty($data['sub_unit_id'])) {
                            $unit       = Unit::find($data['sub_unit_id']);
                            $multiplier = !empty($unit->base_unit_multiplier) ? $unit->base_unit_multiplier : 1;
                        }
                    // *2/2* QUANTITY DATA
                        $new_quantity   =  $data['quantity']  * $multiplier;         
                        $new_quantity_f =  $new_quantity ;    
                    if (isset($data['purchase_line_id'])) {
                    // *2/3* FOR UPDATE OLD ROWS ON PURCHASE ITEM
                            // ** UPDATE QUANTITY **/
                            $purchase_line               = PurchaseLine::findOrFail($data['purchase_line_id']);
                            $updated_purchase_line_ids[] = $purchase_line->id;
                            $purchase_line->store_id     = $transaction->store;
                            $purchase_line->list_price   = ($data['list_price']!=null || $data['list_price']!="")?$data['list_price']:$request->list_price;
                            $purchase_line->order_id     = ($request->line_sort)?(($info==null)?$request->line_sort[$ky]:$data['line_sort']):null;
                            $old_qty                     = $purchase_line->quantity ;
                            $this->updateProductStock($before_status, $transaction, $data['product_id'], $data['variation_id'], $new_quantity, $purchase_line->quantity, $currency_details);
                    } else {
                    // *2/4* FOR ADD NEW ROWS ON PURCHASE ITEM
                            // ** ADD STORE AND PRODUCT ID **/
                            $purchase_line               = new PurchaseLine();
                            $purchase_line->store_id     = $transaction->store;
                            $purchase_line->product_id   = $data['product_id'];
                            $purchase_line->list_price   = ($data['list_price']!=null || $data['list_price']!="")?$data['list_price']:$request->list_price;
                            $purchase_line->order_id     = ($request->line_sort)?(($info==null)?$request->line_sort[$ky]:$data['line_sort']):null;            
                            $purchase_line->variation_id = $data['variation_id'];
                    }   
                    // *2/5* FOR UPDATE QUANTITY
                        $purchase_line->quantity = $new_quantity;
                    // *2/6* CHECK FOR INCOMING DATA 
                        if(isset( $data['pp_without_discount_s'])){
                            $purchase_line->pp_without_discount = ($this->num_uf($data['pp_without_discount_s'], $currency_details)*$exchange_rate) / $multiplier;
                        }else{
                            $purchase_line->pp_without_discount = ($this->num_uf($data['pp_without_discount'], $currency_details)*$exchange_rate) / $multiplier;
                        }
                    // *2/7* UPDATE PURCHASE ITEM INFORMATION IF NEW OR OLD ITEM  
                        $purchase_line->discount_percent       = $this->num_uf($data["discount_percent"], $currency_details);
                        $purchase_line->purchase_price         = ($this->num_uf($data['purchase_price'], $currency_details)*$exchange_rate) / $multiplier;
                        $purchase_line->purchase_price_inc_tax = ($this->num_uf($data['unit_cost_after_tax'], $currency_details)*$exchange_rate) / $multiplier;
                        $purchase_line->item_tax               = ($this->num_uf($data['item_tax'], $currency_details)*$exchange_rate) / $multiplier;
                        $purchase_line->tax_id                 = $transaction->tax_id;
                        $purchase_line->lot_number             = !empty($data['lot_number']) ? $data['lot_number'] : null;
                        $purchase_line->mfg_date               = !empty($data['mfg_date']) ? (($info==null)?$this->uf_date($data['mfg_date']):$data['mfg_date']) : null;
                        $purchase_line->exp_date               = !empty($data['exp_date']) ? (($info==null)?$this->uf_date($data['exp_date']):$data['exp_date']) : null;
                        $purchase_line->sub_unit_id            = !empty($data['sub_unit_id']) ? $data['sub_unit_id'] : null;
                        $purchase_line->purchase_note          = !empty($data['purchase_note']) ? $data['purchase_note'] : null;
                        $updated_purchase_lines[]              = $purchase_line;
                    // *2/8* FOR UPDATE PRODUCT PRICE
                        if ($enable_product_editing == 1) {
                            if (isset($data['default_sell_price'])) {
                                $variation_data['sell_price_inc_tax'] = ($this->num_uf($data['default_sell_price'], $currency_details)) / $multiplier;
                            }
                            $variation_data['pp_without_discount'] = ($this->num_uf($data['pp_without_discount'], $currency_details)*$exchange_rate) / $multiplier;
                            $variation_data['variation_id']        = $purchase_line->variation_id;
                            $variation_data['purchase_price']      = $purchase_line->purchase_price;
                            // $this->updateProductFromPurchase($variation_data);
                        }
                }
            // ************
            // *3* FOR CALCULATE AMOUNT OF SHIPMENT THAT DEPENDING ON SUPPLIER
            // *** AGT8422
                $total_ship = \App\Models\Purchase::supplier_shipp($transaction->id);
            // ************
            // *4* FOR RESET DELETED PURCHASE LINES
            // *** AGT8422
                $delete_purchase_line_ids = [];
                $delete_purchase_lines    = null; 
            // ************
            // *5* FOR DELETE ROWS FROM PURCHASE
            // *** AGT8422
                if (!empty($updated_purchase_line_ids)) {
                    $delete_purchase_lines = PurchaseLine::where('transaction_id', $transaction->id)
                                                                ->whereNotIn('id', $updated_purchase_line_ids)
                                                                ->get();      
                    if ($delete_purchase_lines->count()) {
                            foreach ($delete_purchase_lines as $delete_purchase_line) {
                                $delete_purchase_line_ids[] = $delete_purchase_line->id;
                                if ($before_status == 'received') {
                                    $this->decreaseProductQuantity(
                                        $delete_purchase_line->product_id,
                                        $delete_purchase_line->variation_id,
                                        $transaction->location_id,
                                        $delete_purchase_line->quantity
                                    );
                                }
                            }
                            //Delete deleted purchase lines
                            PurchaseLine::where('transaction_id', $transaction->id)
                                            ->whereIn('id', $delete_purchase_line_ids)
                                            ->delete();
                    }
                }
            // ************
            // *6* FOR SAVE UPDATED ROWS OF PURCHASE
            // *** AGT8422
                if (!empty($updated_purchase_lines)) {
                    $transaction->purchase_lines()->saveMany($updated_purchase_lines);
                }
            // ************
            // *7* FOR INCREASE QUANTITY 
            // *** AGT8422
                if($type_purchase == null){
                    $purchase_line_i = \App\PurchaseLine::where("transaction_id",$transaction->id)->get();
                    foreach($purchase_line_i as $pr_line){
                        $product_unit    = Product::where("id",$pr_line->product_id)->first();
                        $total           = "";
                        $total           = $pr_line->quantity;
                        // $total           = $new_quantity_f;
                        $last_price      = 0;
                        $cost            = 0;
                        $without_contact = 0; 
                        $data_ship       = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->first();
                        if(!empty($data_ship)){
                            $ids       = $data_ship->items->pluck("id");
                            foreach($ids as $i){
                                $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                                if($data_shippment->contact_id == $transaction->contact_id){ 
                                    $cost += $data_shippment->total;
                                }else{
                                    $without_contact += $data_shippment->total;
                                }
                            }
                        }    
                        $before        = \App\Models\WarehouseInfo::qty_before($transaction);
                        $price_i       = PurchaseLine::orderby("id","desc")->where("transaction_id",$transaction->id)->where("product_id",$pr_line->product_id)->first();
                        if ($transaction->status == 'received'  && $transaction->type != 'production_purchase') {
                            if((isset($pr_line->product->variations)) && (count($pr_line->product->variations)>1)){
                                foreach($pr_line->product->variations as $var){
                                    $this->updateProductQuantity($transaction->location_id, $pr_line->product_id, $var->id, $pr_line->quantity, 0, $currency_details);
                                }
                            } 
                            WarehouseInfo::update_stoct($pr_line->product_id,$transaction->store,$pr_line->quantity,$transaction->business_id);
                            $prev                  =  new RecievedPrevious;
                            $prev->product_id      =  $pr_line->product_id;
                            $prev->store_id        =  $transaction->store;
                            $prev->business_id     =  $transaction->business_id ;
                            $prev->transaction_id  =  $transaction->id;
                            $prev->unit_id         =  $product_unit->unit_id;
                            $prev->total_qty       =  $pr_line->quantity;
                            $prev->current_qty     =  $pr_line->quantity;
                            $prev->remain_qty      =  0;
                            $prev->product_name    =  $product_unit->name;  
                            $prev->line_id         =  $pr_line->id;  
                            $prev->save();
                            $list_id[]             =  $pr_line->id;
                            //***  ........... eb
                            $price_i = $pr_line;
                            MovementWarehouse::movemnet_warehouse($transaction,$product_unit,$total,$transaction->store,$price_i,'plus',$prev->id,NULL);/***#$% */
                            //......................................................
                        }
                    }
                    // ** FOR MAKE ENTRIES FOR PURCHASE
                    // *** AGT8422
                        if (($transaction->status == 'received' || $transaction->status == 'final') &&  $transaction->type != 'production_purchase') {
                            \App\AccountTransaction::add_purchase($transaction,$total_ship);
                        }
                    // ************
                    // ** FOR MAKE RECEIVED RECEIPT
                    // *** AGT8422
                        if($transaction->status == 'received'  && $transaction->type != 'production_purchase'){
                            //.....................................................................
                            $tr_received                      = \App\Models\TransactionRecieved::where("transaction_id",$transaction->id)->first();
                            if(empty($tr_received)){
                                $type                         = 'purchase_receive';
                                $ref_count                    =  $this->setAndGetReferenceCount($type,$transaction->business_id);
                                $receipt_no                   =  $this->generateReferenceNumber($type, $ref_count,$transaction->business_id);
                                $tr_received                  =  new TransactionRecieved;
                                $tr_received->store_id        =  $transaction->store;
                                $tr_received->transaction_id  =  $transaction->id;
                                $tr_received->business_id     =  $transaction->business_id ;
                                $tr_received->reciept_no      =  $receipt_no ;
                                $tr_received->ref_no          =  $transaction->ref_no;
                                $tr_received->status          = 'purchase';
                                $tr_received->save();
                            }
                            $prs = \App\Models\RecievedPrevious::where("transaction_id",$transaction->id) ->update(["transaction_deliveries_id" => $tr_received->id]);
                        }
                    // ************
                }
            // ************
            // *8* FOR ARCHIVE DATA
            // *** AGT8422 
                if($archive != null){
                    $purchase_lines = \App\PurchaseLine::where("transaction_id",$transaction->id)->get();
                    foreach($purchase_lines as $it){
                        \App\Models\ArchivePurchaseLine::save_purchases($archive , $it);
                    }
                }
            // ************
        // ***********
        
        return $delete_purchase_lines;
    }
     
    /** 26*
     * Updates product stock after adding or updating purchase
     *
     * @param string $status_before
     * @param obj $transaction
     * @param integer $product_id
     * @param integer $variation_id
     * @param decimal $new_quantity in database format
     * @param decimal $old_quantity in database format
     * @param array $currency_details
     *
     */
    public function updateProductStock($status_before, $transaction, $product_id, $variation_id, $new_quantity, $old_quantity, $currency_details)
    {
        $new_quantity_f     =   $new_quantity ;
        $old_qty            =   $old_quantity ;
        // ** FOR UPDATING STORE QUANTITY      
        if ($status_before == 'received' && $transaction->status == 'received') { // ** STAY RECEIVED
            try{
                //if status received update existing quantity
                $this->updateProductQuantity($transaction->location_id, $product_id, $variation_id, $new_quantity_f, $old_qty, $currency_details);
            }catch(Exception $e){
                dd("خطأ"); 
            }
        } elseif ($status_before == 'received' && $transaction->status != 'received') {
            try{
                $this->decreaseProductQuantity(
                    $product_id,
                    $variation_id,
                    $transaction->location_id,
                    $old_quantity
                );
            }catch(Exception $e){
            }
        } elseif ($status_before != 'received' && $transaction->status == 'received') {
            try{
                $this->updateProductQuantity($transaction->location_id, $product_id, $variation_id, $new_quantity_f, 0, $currency_details);
            }catch(Exception $e){
            }
        }
       
    }
    /** 27*
     * Recalculates purchase line data according to subunit data
     *
     * @param integer $purchase_line
     * @param integer $business_id
     *
     * @return array
     */
    public function changePurchaseLineUnit($purchase_line, $business_id)
    {
        $base_unit   = $purchase_line->product->unit;
        $sub_units   = $base_unit->sub_units;
        $sub_unit_id = $purchase_line->sub_unit_id;
        $sub_unit    = $sub_units->filter(function ($item) use ($sub_unit_id) {
                            return $item->id == $sub_unit_id;
                        })->first();

        if (!empty($sub_unit)) {
            $multiplier                            = $sub_unit->base_unit_multiplier;
            $purchase_line->quantity               = $purchase_line->quantity / $multiplier;
            $purchase_line->pp_without_discount    = $purchase_line->pp_without_discount * $multiplier;
            $purchase_line->purchase_price         = $purchase_line->purchase_price * $multiplier;
            $purchase_line->purchase_price_inc_tax = $purchase_line->purchase_price_inc_tax * $multiplier;
            $purchase_line->item_tax               = $purchase_line->item_tax * $multiplier;
            $purchase_line->quantity_returned      = $purchase_line->quantity_returned / $multiplier;
            $purchase_line->quantity_sold          = $purchase_line->quantity_sold / $multiplier;
            $purchase_line->quantity_adjusted      = $purchase_line->quantity_adjusted / $multiplier;
        }

        //SubUnits
        $purchase_line->sub_units_options = $this->getSubUnits($business_id, $base_unit->id, false, $purchase_line->product_id);

        return $purchase_line;
    }
    /** 28*
     * Recalculates sell line data according to subunit data
     *
     * @param integer $unit_id
     *
     * @return array
     */
    public function changeSellLineUnit($business_id, $sell_line)
    {
        $unit_details = $this->getSubUnits($business_id, $sell_line->unit_id, false, $sell_line->product_id);

        $sub_unit = null;
        $sub_unit_id = $sell_line->sub_unit_id;
        foreach ($unit_details as $key => $value) {
            if ($key == $sub_unit_id) {
                $sub_unit = $value;
            }
        }

        if (!empty($sub_unit)) {
            $multiplier = $sub_unit['multiplier'];
            $sell_line->quantity_ordered = $sell_line->quantity_ordered / $multiplier;
            $sell_line->item_tax = $sell_line->item_tax * $multiplier;
            $sell_line->default_sell_price = $sell_line->default_sell_price * $multiplier;
            $sell_line->unit_price_before_discount = $sell_line->unit_price_before_discount * $multiplier;
            $sell_line->sell_price_inc_tax = $sell_line->sell_price_inc_tax * $multiplier;
            $sell_line->sub_unit_multiplier = $multiplier;

            $sell_line->unit_details = $unit_details;
        }

        return $sell_line;
    }
    /** 29*
     * Retrieves current stock of a variation for the given location
     *
     * @param int $variation_id, int location_id
     *
     * @return float
     */
    public function getCurrentStock($variation_id, $location_id)
    {
        $current_stock = VariationLocationDetails::where('variation_id', $variation_id)
                                              ->where('location_id', $location_id)
                                              ->value('qty_available');

        if (null == $current_stock) {
            $current_stock = 0;
        }

        return $current_stock;
    }
    /** 30*
     * Adjusts stock over selling with purchases, opening stocks andstock transfers
     * Also maps with respective sells
     *
     * @param obj $transaction
     *
     * @return void
     */
    public function adjustStockOverSelling($transaction)
    {
        if ($transaction->status != 'received') {
            return false;
        }
        foreach ($transaction->purchase_lines as $purchase_line) {
            if ($purchase_line->product->enable_stock == 1) {

                //Available quantity in the purchase line
                $purchase_line_qty_avlBl                   = $purchase_line->quantity_remaining;
               
                if ($purchase_line_qty_avlBl <= 0) {
                    continue;
                }
                //update sell line purchase line mapping
                $sell_line_purchase_lines = TransactionSellLinesPurchaseLines::where('purchase_line_id', 0)
                                                ->join('transaction_sell_lines as tsl', 'tsl.id', '=', 'transaction_sell_lines_purchase_lines.sell_line_id')
                                                ->join('transactions as t', 'tsl.transaction_id', '=', 't.id')
                                                ->where('t.location_id', $transaction->location_id)
                                                ->where('tsl.variation_id', $purchase_line->variation_id)
                                                ->where('tsl.product_id', $purchase_line->product_id)
                                                ->select('transaction_sell_lines_purchase_lines.*')
                                                ->get();
                foreach ($sell_line_purchase_lines as $slPl) {
                    if ($purchase_line_qty_avlBl > 0) {
                        if ($slPl->quantity <= $purchase_line_qty_avlBl) {
                            $purchase_line_qty_avlBl      -= $slPl->quantity;
                            $slPl->purchase_line_id        = $purchase_line->id;
                            $slPl->save();
                            //update purchase line quantity sold
                            $purchase_line->quantity_sold += $slPl->quantity;
                            $purchase_line->save();
                        } else {
                            $diff                          = $slPl->quantity - $purchase_line_qty_avlBl;
                            $slPl->purchase_line_id        = $purchase_line->id;
                            $slPl->quantity                = $purchase_line_qty_avlBl;
                            $slPl->save();

                            //update purchase line quantity sold
                            $purchase_line->quantity_sold  += $slPl->quantity;
                            $purchase_line->save();

                            TransactionSellLinesPurchaseLines::create([
                                                                'quantity'          => $diff,
                                                                'sell_line_id'      => $slPl->sell_line_id,
                                                                'purchase_line_id'  => 0
                                                            ]);
                            break;
                        }
                    }
                }
            }
        }
    }
    /** 31*
     * Finds out most relevant descount for the product
     *
     * @param obj $product, int $business_id, int $location_id, bool $is_cg,
     * bool $is_spg
     *
     * @return obj discount
     */
    public function getProductDiscount($product, $business_id, $location_id, $is_cg = false, $is_spg = false, $variation_id = null)
    {
        $now = \Carbon::now()->toDateTimeString();

        //Search if both category and brand matches
        $query1 = Discount::where('business_id', $business_id)
                    ->where('location_id', $location_id)
                    ->where('is_active', 1)
                    ->where('starts_at', '<=', $now)
                    ->where('ends_at', '>=', $now)
                    ->where('brand_id', $product->brand_id)
                    ->where('category_id', $product->category_id)
                    ->orderBy('priority', 'desc')
                    ->latest();
        if ($is_cg) {
            $query1->where('applicable_in_cg', 1);
        }
        if ($is_spg) {
            $query1->where('applicable_in_spg', 1);
        }

        $discount = $query1->first();

        //Search if either category or brand matches
        if (empty($discount)) {
            $query2 = Discount::where('business_id', $business_id)
                    ->where('location_id', $location_id)
                    ->where('is_active', 1)
                    ->where('starts_at', '<=', $now)
                    ->where('ends_at', '>=', $now)
                    ->where(function ($q) use ($product) {
                        $q->whereRaw('(brand_id="' . $product->brand_id .'" AND category_id IS NULL)')
                        ->orWhereRaw('(category_id="' . $product->category_id .'" AND brand_id IS NULL)');
                    })
                    ->orderBy('priority', 'desc');
            if ($is_cg) {
                $query2->where('applicable_in_cg', 1);
            }
            if ($is_spg) {
                $query2->where('applicable_in_spg', 1);
            }
            $discount = $query2->first();
        }

        //Search if variation has discount
        if (!empty($variation_id)) {
          $query3 = Discount::where('business_id', $business_id)
                      ->where('location_id', $location_id)
                      ->where('is_active', 1)
                      ->where('starts_at', '<=', $now)
                      ->where('ends_at', '>=', $now)
                      ->whereHas('variations', function($q) use ($variation_id){
                        $q->where('variation_id', $variation_id);
                      })
                      ->orderBy('priority', 'desc')
                      ->latest();
          if ($is_cg) {
              $query3->where('applicable_in_cg', 1);
          }
          if ($is_spg) {
              $query3->where('applicable_in_spg', 1);
          }
          $discount_by_variation = $query3->first();
          if (!empty($discount_by_variation) && !empty($discount)) {
            $discount = $discount_by_variation->priority >= $discount->priority ? $discount_by_variation : $discount;
          } else if (empty($discount)) {
              $discount = $discount_by_variation;
          }

        }

        if (!empty($discount)) {
            $discount->formated_starts_at = $this->format_date($discount->starts_at->toDateTimeString(), true);
            $discount->formated_ends_at = $this->format_date($discount->ends_at->toDateTimeString(), true);
        }

        return $discount;
    }
    /** 32*
     * Filters product as per the given inputs and return the details.
     *
     * @param string $search_type (like or exact)
     *
     * @return object
     */
    public function filterProduct($business_id, $search_term, $location_id = null, $not_for_selling = null, $price_group_id = null, $product_types = [], $search_fields = [], $check_qty = false,$hide_pos = false, $search_type = 'like',$return_check=false){

        $query = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->active()
                ->whereNull('variations.deleted_at')
                ->leftjoin('product_barcode as barcode','variations.id','=','barcode.variation_id')
                ->leftjoin('units as U', 'products.unit_id', '=', 'U.id')
                ->leftjoin(
                    'variation_location_details AS VLD',
                    function ($join) use ($location_id) {
                        $join->on('variations.id', '=', 'VLD.variation_id');

                        //Include Location
                        if (!empty($location_id)) {
                            $join->where(function ($query) use ($location_id) {
                                $query->where('VLD.location_id', '=', $location_id);
                                //Check null to show products even if no quantity is available in a location.
                                //TODO: Maybe add a settings to show product not available at a location or not.
                                $query->orWhereNull('VLD.location_id');
                            });
                            ;
                        }
                    }
                );

        if (!is_null($not_for_selling)) {
            $query->where('products.not_for_selling', $not_for_selling);
        }

        //Note
        if (!empty($price_group_id)) {
            $query->leftjoin(
                'variation_group_prices AS VGP',
                function ($join) use ($price_group_id) {
                    $join->on('variations.id', '=', 'VGP.variation_id')
                        ->where('VGP.price_group_id', '=', $price_group_id);
                }
            );
        }

        $query->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

        if (!empty($product_types)) {
            $query->whereIn('products.type', $product_types);
        }

        if (in_array('lot', $search_fields)) {
            $query->leftjoin('purchase_lines as pl', 'variations.id', '=', 'pl.variation_id');
        }
        /*if($hide_pos=="true"){
           // return "test";
             $query->whereNull('products.hide_pos');
        }*/

        //Include search
        if (!empty($search_term)) {

            //Search with like condition
            if($search_type == 'like'){
                $query->where(function ($query) use ($search_term, $search_fields) {

                    if (in_array('name', $search_fields)) {
                        $query->where('products.name', 'like', '%' . $search_term .'%');
                    }

                    if (in_array('sku', $search_fields)) {
                        $query->orWhere('sku', 'like', '%' . $search_term .'%')
                              ->orWhere('sku2', 'like', '%' . $search_term .'%')
                              ->orWhere('barcode.barcode', 'like', '%' . $search_term .'%');
                    }

                    if (in_array('sub_sku', $search_fields)) {
                        $query->orWhere('sub_sku', 'like', '%' . $search_term .'%');
                    }

                    if (in_array('lot', $search_fields)) {
                        $query->orWhere('pl.lot_number', 'like', '%' . $search_term .'%');
                    }
                });
            }

            //Search with exact condition
            if($search_type == 'exact'){
                $query->where(function ($query) use ($search_term, $search_fields) {

                    if (in_array('name', $search_fields)) {
                        $query->where('products.name', $search_term);
                    }

                    if (in_array('sku', $search_fields)) {
                        $query->orWhere('sku', $search_term);
                    }

                    if (in_array('sub_sku', $search_fields)) {
                        $query->orWhere('sub_sku', $search_term);
                    }

                    if (in_array('lot', $search_fields)) {
                        $query->orWhere('pl.lot_number', $search_term);
                    }
                });
            }
        }

        if($return_check){
            $query->where('products.enable_stock', '!=', 0);
        }
        //Include check for quantity
        if ($check_qty) {
            $query->where('VLD.qty_available', '>', 0);
        }

        if (!empty($location_id)) {
            $query->ForLocation($location_id);
        }

        $query->select(
                'products.id as product_id',
               /* 'products.hide_pos',*/
                'products.name',
                'products.type',
                'variations.default_purchase_price',
                'products.enable_stock',
                'variations.id as variation_id',
                'variations.name as variation',
                'VLD.qty_available',
                'variations.sell_price_inc_tax as selling_price',
                'variations.sub_sku',
                'U.short_name as unit'
            );

            $query1 = $query->get();  
            // dd($query1);

        if (!empty($price_group_id)) {
            $query->addSelect('VGP.price_inc_tax as variation_group_price');
        }

        if (in_array('lot', $search_fields)) {
            $query->addSelect('pl.id as purchase_line_id', 'pl.lot_number');
        }

        $query->groupBy('variations.id');
       $allData =  $query->orderBy('VLD.qty_available', 'desc')
                        ->get();
        if(app('request')->input('status') == 'quotation'){
            foreach($allData as $data){
                $data->qty_available = 10000;
            }
            
        }
        foreach($allData as $qy){
           
           if(app("request")->input("store")){
           
            $warahouse_info_qty = WarehouseInfo::where("business_id",$business_id)->where("product_id",$qy->product_id)->where("store_id",app("request")->input("store"))->sum("product_qty");
            }else{
                 $warahouse_info_qty = WarehouseInfo::where("business_id",$business_id)->where("product_id",$qy->product_id)->sum("product_qty");
           }
           
           if( $warahouse_info_qty > 0 ){
                
               $qy->qty_available = $warahouse_info_qty;
           }
           
            
            
        } 
        return $allData;
    }
     // 32*
    public function getProductStockDetails($business_id, $filters, $for,$alert_qty=false)
    {
        $query = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
                  ->join('units', 'p.unit_id', '=', 'units.id')
                  ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')
                  ->leftjoin('business_locations as l', 'vld.location_id', '=', 'l.id')
                  ->join('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                  ->where('p.business_id', $business_id)
                  ->whereIn('p.type', ['single', 'variable']);
        if($alert_qty){
          $query->whereRaw('vld.qty_available <= p.alert_quantity');
        }
        $permitted_locations = auth()->user()->permitted_locations();
        $location_filter = '';

        if ($permitted_locations != 'all') {
            $query->whereIn('vld.location_id', $permitted_locations);

            $locations_imploded = implode(', ', $permitted_locations);
            $location_filter .= "AND transactions.location_id IN ($locations_imploded) ";
        }

        if (!empty($filters['location_id'])) {
            $location_id = $filters['location_id'];

            $query->where('vld.location_id', $location_id);

            $location_filter .= "AND transactions.location_id=$location_id";

            //If filter by location then hide products not available in that location
            $query->join('product_locations as pl', 'pl.product_id', '=', 'p.id')
                  ->where(function ($q) use ($location_id) {
                      $q->where('pl.location_id', $location_id);
                  });
        }

        if (!empty($filters['category_id'])) {
            $query->where('p.category_id', $filters['category_id']);
        }
        if (!empty($filters['sub_category_id'])) {
            $query->where('p.sub_category_id', $filters['sub_category_id']);
        }
        if (!empty($filters['brand_id'])) {
            $query->where('p.brand_id', $filters['brand_id']);
        }
        if (!empty($filters['unit_id'])) {
            $query->where('p.unit_id', $filters['unit_id']);
        }

        if (!empty($filters['tax_id'])) {
            $query->where('p.tax', $filters['tax_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('p.type', $filters['type']);
        }

        if (isset($filters['only_mfg_products']) && $filters['only_mfg_products'] == 1) {
            $query->join('mfg_recipes as mr', 'mr.variation_id', '=', 'variations.id');
        }

        if (isset($filters['active_state']) && $filters['active_state'] == 'active') {
            $query->where('p.is_inactive', 0);
        }
        if (isset($filters['active_state']) && $filters['active_state'] == 'inactive') {
            $query->where('p.is_inactive', 1);
        }
        if (isset($filters['not_for_selling']) && $filters['not_for_selling'] == 1) {
            $query->where('p.not_for_selling', 1);
        }

        if (!empty($filters['repair_model_id'])) {
            $query->where('p.repair_model_id', request()->get('repair_model_id'));
        }

        //TODO::Check if result is correct after changing LEFT JOIN to INNER JOIN
        // $string = pl.quantity_sold + pl.quantity_adjusted + pl.quantity_returned + pl.mfg_quantity_used;

        $pl_query_string = $this->get_pl_quantity_sum_string('pl');

        if ($for == 'view_product' && !empty(request()->input('product_id'))) {
            $location_filter = 'AND transactions.location_id=l.id';
        }

        $products = $query->select(
            // DB::raw("(SELECT SUM(quantity) FROM transaction_sell_lines LEFT JOIN transactions ON transaction_sell_lines.transaction_id=transactions.id WHERE transactions.status='final' $location_filter AND
            //     transaction_sell_lines.product_id=products.id) as total_sold"),
               /* total_sold */
            DB::raw("(SELECT SUM(TSL.quantity - TSL.quantity_returned) FROM transactions 
                  JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                  WHERE transactions.status='final' AND transactions.type='sale' AND transactions.location_id=vld.location_id
                  AND TSL.variation_id=variations.id) as total_sold"),
            
                  /* total_delivered */
            DB::raw("(SELECT SUM(DP.current_qty) FROM transactions 
                  JOIN  delivered_previouses AS DP ON transactions.id=DP.transaction_id
                  WHERE transactions.type='sale' AND transactions.location_id=vld.location_id
                  AND DP.product_id=variations.product_id) as total_delivered"),
                  /* total_wrong_delivered */
            DB::raw("(SELECT SUM(WDP.current_qty) FROM transactions 
                  JOIN  delivered_wrongs AS WDP ON transactions.id=WDP.transaction_id
                  WHERE transactions.type='sale' AND transactions.location_id=vld.location_id
                  AND WDP.product_id=variations.product_id) as total_wrong_delivered"),

            /* total_transfered */
            DB::raw("(SELECT SUM(IF(transactions.type='Stock_Out', TSL.quantity, 0) ) FROM transactions 
                  JOIN transaction_sell_lines AS TSL ON transactions.id=TSL.transaction_id
                  WHERE transactions.status='final' AND transactions.type='Stock_Out' AND transactions.location_id=vld.location_id AND (TSL.variation_id=variations.id)) as total_transfered"),

            DB::raw("(SELECT SUM(IF(transactions.type='stock_adjustment', SAL.quantity, 0) ) FROM transactions 
                  JOIN stock_adjustment_lines AS SAL ON transactions.id=SAL.transaction_id
                  WHERE transactions.type='stock_adjustment' AND transactions.location_id=vld.location_id 
                    AND (SAL.variation_id=variations.id)) as total_adjusted"),

            /* stock_price */
            /* pl.quantity_sold + pl.quantity_adjusted + pl.quantity_returned + pl.mfg_quantity_used;*/
           DB::raw("(SELECT SUM( COALESCE(pl.quantity - ($pl_query_string), 0) * purchase_price_inc_tax) FROM transactions
                  JOIN purchase_lines AS pl ON transactions.id=pl.transaction_id
                  WHERE transactions.status='received' AND transactions.location_id=vld.location_id 
                  AND (pl.variation_id=variations.id)) as stock_price"),

            /* stock */
            DB::raw("SUM(vld.qty_available) as stock"),
            /* max_purchase_price */
            DB::raw('MAX(variations.dpp_inc_tax) as max_purchase_price'),
            'variations.sub_sku as sku',
            'p.name as product',
            'p.type',
            'p.alert_quantity',
            'p.id as product_id',
            'units.short_name as unit',
            'p.enable_stock as enable_stock',
            'variations.sell_price_inc_tax as unit_price',
            'pv.name as product_variation',
            'variations.name as variation_name',
            'l.name as location_name',
            'l.id as location_id',
            'variations.id as variation_id',
            'variations.default_purchase_price'

        )->groupBy('variations.id', 'vld.location_id');

        if (isset($filters['show_manufacturing_data']) && $filters['show_manufacturing_data']) {
            $pl_query_string = $this->get_pl_quantity_sum_string('PL');
            $products->addSelect(
                DB::raw("(SELECT COALESCE(SUM(PL.quantity - ($pl_query_string)), 0) FROM transactions 
                    JOIN purchase_lines AS PL ON transactions.id=PL.transaction_id
                    WHERE transactions.status='received' AND transactions.type='production_purchase' AND transactions.location_id=vld.location_id  
                    AND (PL.variation_id=variations.id)) as total_mfg_stock")
            );
        }
        $products->whereNull('p.deleted_at');
        if (!empty($filters['product_id'])) {
            $products->where('p.id', $filters['product_id'])
                    ->groupBy('l.id');
        }

        if ($for == 'view_product') {
           
            return $products->get();
        } else if ($for == 'api') {
          
            return $products->paginate();
        } else {
          
            return $products;
        }
    }
    /** 33*
     * Gives the details of combo product
     *
     * @param array $combo_variations
     * @param int $business_id
     *
     * @return array
     */
    public function __getComboProductDetails($combo_variations, $business_id)
    {
         
        
        foreach ($combo_variations as $key => $value) {
            if($value['variation_id'] != 0){
                $combo_variations[$key]['variation'] = Variation::with(['product'])->find($value['variation_id']);
                $product        = Product::where('id', $combo_variations[$key]['variation']->product->id)
                                            ->with(['unit'])
                                            ->first();
                $var            = $product->variations->first();
                $var            = ($var)?$var->default_purchase_price:0;
                $business       = \App\Business::find($combo_variations[$key]['variation']->product->business_id);
                $allUnits       = [];
                $allUnits[$product->unit_id] = [
                    'name' => $product->unit->actual_name,
                    'multiplier' => $product->unit->base_unit_multiplier,
                    'allow_decimal' => $product->unit->allow_decimal,
                    'price' => $var,
                    'check_price' => $business->default_price_unit,
                    ];
              
            
                if($product->sub_unit_ids != null){
                    foreach($product->sub_unit_ids  as $i){
                            $row_price    =  0;
                            $un           = \App\Unit::find($i);
                            $row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$product->id)->where("number_of_default",0)->first();
                            $row_price    = ($row_price)?$row_price->default_purchase_price:0;
                            $allUnits[$i] = [
                                'name'          => $un->actual_name,
                                'multiplier'    => $un->base_unit_multiplier,
                                'allow_decimal' => $un->allow_decimal,
                                'price'         => $row_price,
                                'check_price'   => $business->default_price_unit,
                            ] ;
                        }
                } 
                
                $sub_units        = $allUnits  ;
                
                // $combo_variations[$key]['sub_units'] = $this->getSubUnits($business_id, $combo_variations[$key]['variation']['product']->unit_id, true);
                $combo_variations[$key]['sub_units'] = $allUnits;
    
                $combo_variations[$key]['multiplier'] = 1;
                
                if (!empty($combo_variations[$key]['sub_units'])) {
                    if (isset($combo_variations[$key]['sub_units'][$combo_variations[$key]['unit_id']])) {
                        $combo_variations[$key]['multiplier'] = $combo_variations[$key]['sub_units'][$combo_variations[$key]['unit_id']]['multiplier'];
                        $combo_variations[$key]['unit_name']  = $combo_variations[$key]['sub_units'][$combo_variations[$key]['unit_id']]['name'];
                        
                    }
                }
             
            }
        }
        return $combo_variations;
    }
    //    34*
    public function getVariationStockDetails($business_id, $variation_id, $location_id)
    {
          $purchase_details = Variation::
                     join('products as p', 'p.id', '=', 'variations.product_id')
                    ->join('units', 'p.unit_id', '=', 'units.id')
                    ->leftjoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')
                    ->leftjoin('purchase_lines as pl', 'pl.variation_id', '=', 'variations.id')
                    ->leftjoin('transactions as t', 'pl.transaction_id', '=', 't.id')
                    ->leftjoin('recieved_previouses as rvp', 't.id', '=', 'rvp.transaction_id')
                    ->where('t.location_id', $location_id)
                    ->where('t.status', "received")
                    ->where('p.business_id', $business_id)
                    ->where('variations.id', $variation_id)
                    ->select(
                        DB::raw("SUM(IF(t.type='purchase', pl.quantity, 0)) as total_purchase"),
                        DB::raw("SUM(IF(t.type='pusrchase', pl.quantity_returned, 0)) as total_purchase_return"),
                        DB::raw("SUM(pl.quantity_adjusted) as total_adjusted"),
                        DB::raw("SUM(IF(t.type='opening_stock', pl.quantity, 0)) as total_opening_stock"),
                        DB::raw("SUM(IF(t.type='Stock_In', pl.quantity, 0)) as total_purchase_transfer"),
                        DB::raw("SUM(IF(t.type='production_purchase', pl.quantity, 0)) as total_production_purchase"),
                        DB::raw("SUM(IF(rvp.current_qty>'0', rvp.current_qty, 0)) as total_receive"),
                        DB::raw("SUM(mfg_quantity_used) as mfg_quantity_used"),
                        'variations.sub_sku as sub_sku',
                        'p.name as product',
                        'p.type',
                        'p.sku',
                        'p.id as product_id',
                        'units.short_name as unit',
                        'pv.name as product_variation',
                        'variations.name as variation_name',
                        'variations.id as variation_id'
                    )->first() ;
         
           
            // total_production_purchase  quantity comes from production
            // mfg_quantity_used  quantity used for production

             $sell_details = Variation::join('products as p', 'p.id', '=', 'variations.product_id')
                    ->leftjoin('transaction_sell_lines as sl', 'sl.variation_id', '=', 'variations.id')
                    ->join('transactions as t', 'sl.transaction_id', '=', 't.id')
                    ->where('t.location_id', $location_id)
                    ->whereIn('t.status', ['delivered','Delivered'])
                    ->where('p.business_id', $business_id)
                    ->where('variations.id', $variation_id)
                    ->select(
                        DB::raw("SUM(IF(t.type='sale', sl.quantity, 0)) as total_sold"),
                        DB::raw("SUM(IF(t.type='sale', sl.quantity_returned, 0)) as total_sell_return"),
                        DB::raw("SUM(IF(t.type='Stock_Out', sl.quantity, 0)) as total_sell_transfer"),
                        DB::raw("SUM(IF(t.type='production_sell', sl.quantity, 0)) as production_sell")
                    )
                  ->get()->first();


        $current_stock = VariationLocationDetails::where('variation_id',
                                            $variation_id)
                                        ->where('location_id', $location_id)
                                        ->first();

        if ($purchase_details->type == 'variable') {
            $product_name = $purchase_details->product . ' - ' . $purchase_details->product_variation . ' - ' . $purchase_details->variation_name . ' (' . $purchase_details->sub_sku . ')';
        } else {
            $product_name = $purchase_details->product . '(' . $purchase_details->sku . ')';
        }

        $total_in=$purchase_details->total_purchase
                  +$purchase_details->total_opening_stock
                  +$purchase_details->total_purchase_transfer
                  +$purchase_details->total_production_purchase
                  +$sell_details->total_sell_return;

        $total_out=$purchase_details->total_purchase_return
                   +$sell_details->total_sold
                   +$sell_details->total_sell_transfer
                   +$sell_details->production_sell;

       
        $output = [
            'variation' => $product_name,
            'unit' => $purchase_details->unit,
            'total_purchase' => $purchase_details->total_purchase,
            'total_purchase_return' => $purchase_details->total_purchase_return,
            'total_adjusted' => $purchase_details->total_adjusted,
            'total_opening_stock' => $purchase_details->total_opening_stock,
            'total_purchase_transfer' => $purchase_details->total_purchase_transfer,


            'total_production_purchase' => $purchase_details->total_production_purchase,
            'mfg_quantity_used' => $purchase_details->mfg_quantity_used,


            'total_sold' => $sell_details->total_sold,
            'total_sell_return' => $sell_details->total_sell_return,
            'total_sell_transfer' => $sell_details->total_sell_transfer,
            'current_stock' => $current_stock->qty_available ?? 0,

            'production_sell'=>$sell_details->production_sell,
            'total_in'=> $total_in,
            'total_out'=>$total_out
        ];

        return $output;
    }
    //   35*
    public function getVariationStockHistory($business_id, $variation_id, $location_id)
    {
        $stock_history = Transaction::
        leftjoin('transaction_sell_lines as sl', 'sl.transaction_id', '=', 'transactions.id')
        ->leftjoin('purchase_lines as pl', 'pl.transaction_id', '=', 'transactions.id')
        ->leftjoin('stock_adjustment_lines as al', 'al.transaction_id', '=', 'transactions.id')
        ->leftjoin('transactions as return', 'transactions.return_parent_id', '=', 'return.id')
        ->leftjoin('purchase_lines as rpl','rpl.transaction_id', '=', 'return.id')
        ->leftjoin('transaction_sell_lines as rsl','rsl.transaction_id', '=', 'return.id')
        ->where('transactions.location_id', $location_id)
        ->where( function($q) use ($variation_id){
                        $q->where('sl.variation_id', $variation_id)
                            ->orWhere('pl.variation_id', $variation_id)
                            ->orWhere('al.variation_id', $variation_id)
                            ->orWhere('rpl.variation_id', $variation_id)
                            ->orWhere('rsl.variation_id', $variation_id);
                    })
        ->whereIn('transactions.type', ['sale', 'purchase', 'stock_adjustment', 'opening_stock', 'Stock_Out', 'Stock_In', 'production_purchase','production_sell', 'purchase_return', 'sell_return'])
        ->select(
            'transactions.id as transaction_id',
            'transactions.type as transaction_type',
            'sl.quantity as sell_line_quantity',
            'sl.unit_price as sales_price',
            'pl.quantity as purchase_line_quantity',
            'pl.purchase_price as price',
            'rsl.quantity_returned as sell_return',
            'rpl.quantity_returned as purchase_return',
            'al.quantity as stock_adjusted',
            'transactions.return_parent_id',
            'transactions.transaction_date',
            'transactions.status',
            'transactions.invoice_no',
            'transactions.ref_no'
        )
        ->orderBy('transactions.transaction_date', 'asc')
        ->get();

        $stock_history_array = [];
        $stock = 0;
        foreach ($stock_history as $stock_line) {
            
            if ($stock_line->transaction_type == 'sale') {
                
                if ($stock_line->status != 'delivered'   ) {
                    
                        continue;
                     
                }
                $quantity_change =  -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = [
                    'date' => $stock_line->transaction_date,
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'sale',
                    'type_label' => __('sale.sale'),
                    'ref_no' => $stock_line->invoice_no,
                    'transaction_id' => $stock_line->transaction_id,
                    'price' => $stock_line->sales_price

                ];
            } else if ($stock_line->transaction_type == 'purchase') {
               
                if ($stock_line->status != 'received'   ) {
                    continue;
                }

                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = [
                    'date' => $stock_line->transaction_date,
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase',
                    'type_label' => __('lang_v1.purchase'),
                    'ref_no' => $stock_line->ref_no,
                    'transaction_id' => $stock_line->transaction_id,
                    'price' => $stock_line->price
                ];
            }   else if ($stock_line->transaction_type == 'stock_adjustment') {
                $quantity_change = -1 * $stock_line->stock_adjusted;
                $stock += $quantity_change;
                $stock_history_array[] = [
                    'date' => $stock_line->transaction_date,
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'stock_adjustment',
                    'type_label' => __('stock_adjustment.stock_adjustment'),
                    'ref_no' => $stock_line->ref_no,
                    'transaction_id' => $stock_line->transaction_id,
                    'price' => null

                ];
            } else if ($stock_line->transaction_type == 'opening_stock') {
          
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = [
                    'date' => $stock_line->transaction_date,
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'opening_stock',
                    'type_label' => __('report.opening_stock'),
                    'ref_no' => $stock_line->ref_no ?? '',
                    'transaction_id' => $stock_line->transaction_id,
                    'price' => $stock_line->price
                ];
            } else if ($stock_line->transaction_type == 'Stock_Out') {
                
                $quantity_change = -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = [
                    'date' => $stock_line->transaction_date,
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'Stock_Out',
                    'type_label' => __('lang_v1.stock_transfers') . ' (' . __('lang_v1.out') . ')',
                    'ref_no' => $stock_line->ref_no,
                    'transaction_id' => $stock_line->transaction_id,
                    'price' => null
                ];
            } else if ($stock_line->transaction_type == 'Stock_In') {
                
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = [
                    'date' => $stock_line->transaction_date,
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'Stock_In',
                    'type_label' => __('lang_v1.stock_transfers') . ' (' . __('lang_v1.in') . ')',
                    'ref_no' => $stock_line->ref_no,
                    'transaction_id' => $stock_line->transaction_id,
                    'price' => null
                ];
            } else if ($stock_line->transaction_type == 'production_purchase') {
                $quantity_change = $stock_line->purchase_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = [
                    'date' => $stock_line->transaction_date,
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'production_purchase',
                    'type_label' => __('manufacturing::lang.manufactured'),
                    'ref_no' => $stock_line->ref_no,
                    'transaction_id' => $stock_line->transaction_id,
                    'price' => $stock_line->price
                ];
            } else if ($stock_line->transaction_type == 'production_sell') {
                $quantity_change = -1 * $stock_line->sell_line_quantity;
                $stock += $quantity_change;
                $stock_history_array[] = [
                    'date' => $stock_line->transaction_date,
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'production_sell',
                    'type_label' => __('manufacturing::lang.manufacturing').'('.__('lang_v1.out').')',
                    'ref_no' => $stock_line->ref_no,
                    'transaction_id' => $stock_line->transaction_id,
                    'price' => $stock_line->sales_price
                ];

            } else if ($stock_line->transaction_type == 'purchase_return') {
                $quantity_change =  -1 * $stock_line->purchase_return;
                $stock += $quantity_change;
                $stock_history_array[] = [
                    'date' => $stock_line->transaction_date,
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'purchase_return',
                    'type_label' => __('lang_v1.purchase_return'),
                    'ref_no' => $stock_line->ref_no,
                    'transaction_id' => $stock_line->transaction_id,
                    'price' => $stock_line->price
                ];
            } else if ($stock_line->transaction_type == 'sell_return') {
                $quantity_change = $stock_line->sell_return;
                $stock += $quantity_change;
                $stock_history_array[] = [
                    'date' => $stock_line->transaction_date,
                    'quantity_change' => $quantity_change,
                    'stock' => $this->roundQuantity($stock),
                    'type' => 'Stock_In',
                    'type_label' => __('lang_v1.sell_return'),
                    'ref_no' => $stock_line->invoice_no,
                    'transaction_id' => $stock_line->transaction_id,
                    'price' => $stock_line->sales_price
                ];
            }
        }

        return array_reverse($stock_history_array);
    }
}
