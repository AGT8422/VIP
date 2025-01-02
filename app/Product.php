<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use App\Utils\Util;
class Product extends Model
{
    use SoftDeletes;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected $appends = ['image_url'];
    // protected $connection = 'mysql2';


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'sub_unit_ids' => 'array',
    ];
   
    /**
     * Get the products image.
     *
     * @return string
     */
    public function getImageUrlAttribute()
    {
        $image_url ='';
        $company_name = request()->session()->get("user_main.domain");
        if (!empty($this->image)) {
            $image_url = asset('uploads/companies/'.$company_name.'/img/' . rawurlencode($this->image));
        } 
        return $image_url;
    }

    /**
    * Get the products image path.
    *
    * @return string
    */
    public function getImagePathAttribute()
    {
        if (!empty($this->image)) {
            $company_name = request()->session()->get("user_main.domain");
            $image_path = public_path('uploads') . '/companies/'.$company_name ."/". config('constants.product_img_path') . '/' . $this->image;
        } else {
            $image_path = null;
        }
        return $image_path;
    }
    /**
    * Get the products image path.
    *
    * @return string
    */
    public function getImagePathSecondAttribute()
    {
        if (!empty($this->image)) {
            $company_name = request()->session()->get("user_main.domain");
            $image_path = public_path('/uploads') . '/companies/' .$company_name ."/". config('constants.product_img_path') . '/' . $this->image;
        } else {
            $image_path = null;
        }
        return $image_path;
    }
    /**
    * Get the products image path.
    *
    * @return string
    */
    public function getImagePathBaseAttribute()
    {
        if (!empty($this->image)) {
            $company_name = request()->session()->get("user_main.domain");
            $image_path = base_path('uploads') . '/companies/' .$company_name ."/". config('constants.product_img_path') . '/' . $this->image;
        } else {
            $image_path = null;
        }
        return $image_path;
    }

    public function product_variations()
    {
        return $this->hasMany(\App\ProductVariation::class);
    }
    
    /**
     * Get the brand associated with the product.
     */
    public function brand()
    {
        return $this->belongsTo(\App\Brands::class);
    }
    
    /**
    * Get the unit associated with the product.
    */
    public function unit()
    {
        return $this->belongsTo(\App\Unit::class);
    }
    /**
     * Get category associated with the product.
     */
    public function category()
    {
        return $this->belongsTo(\App\Category::class);
    }
    /**
     * Get sub-category associated with the product.
     */
    public function sub_category()
    {
        return $this->belongsTo(\App\Category::class, 'sub_category_id', 'id');
    } 
    /**
     * Get the brand associated with the product.
     */
    public function product_tax()
    {
        return $this->belongsTo(\App\TaxRate::class, 'tax', 'id');
    }
    /**
     * Get the variations associated with the product.
     */
    public function variations()
    {
        return $this->hasMany(\App\Variation::class);
    }
    /**
     * If product type is modifier get products associated with it.
     */
    public function modifier_products()
    {
        return $this->belongsToMany(\App\Product::class, 'res_product_modifier_sets', 'modifier_set_id', 'product_id');
    }
    /**
     * If product type is modifier get products associated with it.
     */
    public function modifier_sets()
    {
        return $this->belongsToMany(\App\Product::class, 'res_product_modifier_sets', 'product_id', 'modifier_set_id');
    }
    /**
     * Get the purchases associated with the product.
     */
    public function purchase_lines()
    {
        return $this->hasMany(\App\PurchaseLine::class);
    }
    /**
     * Scope a query to only include active products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('products.is_inactive', 0);
    }
    /**
     * Scope a query to only include inactive products.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('products.is_inactive', 1);
    }
    /**
     * Scope a query to only include products for sales.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProductForSales($query)
    {
        return $query->where('not_for_selling', 0);
    }
    /**
     * Scope a query to only include products not for sales.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProductNotForSales($query)
    {
        return $query->where('not_for_selling', 1);
    }
    public function product_locations()
    {
        return $this->belongsToMany(\App\BusinessLocation::class, 'product_locations', 'product_id', 'location_id');
    }

    /**
     * Scope a query to only include products available for a location.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForLocation($query, $location_id)
    {
        return $query->where(function ($q) use ($location_id) {
            $q->whereHas('product_locations', function ($query) use ($location_id) {
                $query->where('product_locations.location_id', $location_id);
            });
        });
    }

    /**
     * Get warranty associated with the product.
     */
    public function warranty()
    {
        return $this->belongsTo(\App\Warranty::class);
    }
    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }
    public function opening_quantites()
    {
        return $this->hasMany('\App\Models\OpeningQuantity','product_id');
    }
    public function recived()
    {
        return $this->hasMany('\App\Models\RecievedPrevious','product_id');
    }
    public static function product_cost($id,$variation_id=null)
    {
        // $row =  Product::find($id);
        // $purchaseline = PurchaseLine::where("product_id",$row->id)->whereHas("transaction",function($query){
        //                                     $query->whereIn('type',['opening_stock','production_purchase']);
        //                                     $query->where('status', 'received' );
        //                                 })->select(\DB::raw('SUM(purchase_price*quantity) as price'),
        //                                  \DB::raw('SUM(quantity) as count'))->first();
        // $line_amount = 0;
        
        // $html = number_format(0,2);
        // $sum =  $row->recived->sum('current_qty');
        
        // $amount  =  0;
        // $div = 0; 
        // foreach ($row->recived as $recive) {
        //     $Qty  = 0;$div = 0; 
        //     $line =  \App\PurchaseLine::where('transaction_id',$recive->transaction_id)
        //                                     ->where('product_id',$recive->product_id)
        //                                     ->select([DB::raw("SUM(quantity*purchase_price) as purchase_total")
        //                                             ,DB::raw("SUM(quantity) as qty")],"*")
        //                                     ->first();
            
        //     $purchase =  \App\PurchaseLine::where('transaction_id',$recive->transaction_id)
        //                                         ->where('product_id',$recive->product_id)
        //                                         ->first();

        //     $total_final = $line->purchase_total/$line->qty;
        //     //... 30,000 / 20  = 1500 
            
        //     $add             = \App\Models\AdditionalShippingItem::whereHas('additional_shipping',function($query) use($purchase){
        //                                                             $query->where("type",0);
        //                                                             $query->where('transaction_id',$purchase->transaction_id);
        //                                                     })->sum('amount') ;
            
        //     $recive_shipp    = \App\Models\AdditionalShippingItem::whereHas('additional_shipping',function($query) use($purchase){
        //                                                         $query->where("type",1);
        //                                                         $query->where('transaction_id',$purchase->transaction_id);
        //                                                     })->sum('amount') ;
                                                        
        //     $shipp           = \App\Models\AdditionalShipping::where("type",1)->where('transaction_id',$purchase->transaction_id)->first() ;
            
            
        //     if(!empty($shipp)){
        //         $childs          = \App\Models\TransactionRecieved::childs($shipp->t_recieved);           
        //         foreach($childs as $item){
        //             $ch   = \App\Models\RecievedPrevious::find($item) ;
                    
                    
        //             if(!empty($ch)){
        //                 $Qty += $ch->current_qty;  
        //             }
        //         }
        //         if($Qty == 0){
        //             $Qty  = 1;
        //         }
        //         $price  = Product::product_cost_purchase($id,$recive->transaction_id);
        //         $final  = ($price * $Qty) + $recive_shipp;
        //         $div    = $final/$Qty;
               
        //     } 


        //     if ($purchase->transaction->discount_type == "fixed_before_vat"){
        //         $dis = $purchase->transaction->discount_amount;
        //     }else if ($purchase->transaction->discount_type == "fixed_after_vat"){
        //         $tax = \App\TaxRate::find($purchase->transaction->tax_id);
        //         $dis = ($purchase->transaction->discount_amount*100)/(100+$tax->amount) ;
        //     }else if ($purchase->transaction->discount_type == "percentage"){
        //         $dis = ($purchase->transaction->total_before_tax *  $purchase->transaction->discount_amount)/100;
        //     }else{
        //         $dis = 0;
        //     }
             




        //     $final   = ($purchase->transaction->total_before_tax + $add - $dis   );

        //     $per     = ($final/$purchase->transaction->total_before_tax) * $total_final ;
            
        //     $amount += ($recive->current_qty - $purchase->quantity_returned)*$per ;
        //     $row      = \App\Models\ItemMove::subtotal_recieve_correct($recive->transaction_id);
        //     $sub_total_in_recieve = 0;


        //     foreach($row as $it){
        //         $qty   = $it[2];
        //         $price = $it[1];
        //         $total_ = $it[2] * $it[1];
        //         $sub_total_in_recieve +=$total_; 
        //     }
        //     $subtotal_recieve = $sub_total_in_recieve;
            
        //     $percent          =  $amount  / $subtotal_recieve;
          
        //     $expense_per      =  $percent * $recive_shipp;
        //     $line_amount      =  $amount  +  $expense_per  ;

        //     $sum    -= $purchase->quantity_returned;
           
        // }
        
    
        // if (($purchaseline->count+$sum) > 0) {
        //     $html =     ($purchaseline->price + $line_amount)/($purchaseline->count+$sum)      ;

        // }
       
        // $html_s =  $html;
        $item = \App\Models\ItemMove::orderby("date","desc")
                                    ->orderBy("id","desc")
                                    ->where("product_id",$id);
        if($variation_id != null){
            $item->where("variation_id",$variation_id);
        }
        $item = $item->first();
        // $item = \App\Models\ItemMove::orderby("id","desc")->where("product_id",$id)->first();
        if(!empty($item)){
            $costs_ = $item->unit_cost;
        }else{
            $costs_ = 0;
        }
        return $costs_ ;
        
    } 
    public static function product_cost_expense($id,$transaction_id,$trans_recieve)
    {
   
        $total         =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($transaction_id) {
                                                             $query->where("type",1);
                                                             $query->where("transaction_id",$transaction_id);
                                                            })->sum("amount");

        $total_purchase   =  \App\Models\AdditionalShippingItem::whereHas("additional_shipping",function($query) use($transaction_id) {
                                                             $query->where("type",0);
                                                             $query->where("transaction_id",$transaction_id);
                                                            })->sum("amount");
       
        $row_purchase_price_total = \App\PurchaseLine::where("transaction_id",$transaction_id)
                                                            ->select(
                                                                DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();

        $row_purchase_price = \App\PurchaseLine::where("transaction_id",$transaction_id)
                                                            ->where("product_id",$id)
                                                            ->select(
                                                                DB::raw("SUM(quantity*purchase_price) as total_row_price"),
                                                                DB::raw("SUM(quantity) as qty")
                                                            )->first();
        $transaction_basic  = \App\Transaction::find($transaction_id); 
        
        if ($transaction_basic->discount_type == "fixed_before_vat"){
            $dis = $transaction_basic->discount_amount;
        }else if ($transaction_basic->discount_type == "fixed_after_vat"){
            $tax = \App\TaxRate::find($transaction_basic->tax_id);
            $dis = ($transaction_basic->discount_amount*100)/(100+$tax->amount) ;
        }else if ($transaction_basic->discount_type == "percentage"){
            $dis = ($transaction_basic->total_before_tax *  $transaction_basic->discount_amount)/100;
        }else{
            $dis = 0;
        }
        
        if( !empty($row_purchase_price) ){
            ///... choice the cost 
            $final_prices      =  $row_purchase_price_total->total_row_price - $dis + $total_purchase;
            $pecent_prices     =  ($row_purchase_price_total->total_row_price!=0)?($final_prices / $row_purchase_price_total->total_row_price):0;

            $additional_prices =  $pecent_prices * ($row_purchase_price->qty != 0)?(($row_purchase_price->total_row_price / $row_purchase_price->qty)):0;
             
            $row_cost          =  $additional_prices    ;
            
        }else{
            $row_cost = \App\Product::product_cost($product_id);
        }


        $row  = \App\Models\ItemMove::subtotal_recieve_correct($transaction_id);
        $sub_total_in_recieve = 0;
        foreach($row as $it){
            $qty   = $it[2];
            $price = $it[1];
            $total_ = $it[2] * $it[1];
            $sub_total_in_recieve +=$total_; 
        }
         $total_receive =  $sub_total_in_recieve;
        if($total_receive != 0){
            $percent       = $total / $total_receive ;
        }else{
            $percent       = 0;
        }
        $additional    = $percent * $row_cost;
        $final_total   = $additional + $row_cost;
        $html  =  round($final_total,2);
        return $html;
    }  
    public static function product_cost_purchase($id,$transaction_id,$return=null)
    {
                $cost=0;$without_contact=0;
                $transaction = \App\Transaction::find($transaction_id);
                if($return == null){
                    $data_ship   = \App\Models\AdditionalShipping::where("transaction_id",$transaction->id)->where("type",0)->first();
                    if(!empty($data_ship)){
                        $ids = $data_ship->items->pluck("id");
                        foreach($ids as $i){
                            $data_shippment   = \App\Models\AdditionalShippingItem::find($i);
                            if($data_shippment->contact_id == $transaction->contact_id){ 
                                $cost += $data_shippment->amount;
                            }else{
                                $without_contact += $data_shippment->amount;
                            }
                        }
                    }
                }
                $total_expense = $cost + $without_contact;

                if ($transaction->discount_type == "fixed_before_vat"){
                    $dis = $transaction->discount_amount;
                }else if ($transaction->discount_type == "fixed_after_vat"){
                    $tax = \App\TaxRate::find($transaction->tax_id);
                    $dis = ($transaction->discount_amount*100)/(100+$tax->amount) ;
                }else if ($transaction->discount_type == "percentage"){
                    $dis = ($transaction->total_before_tax *  $transaction->discount_amount)/100;
                }else{
                    $dis = 0;
                }

                $purchase       = \App\PurchaseLine::where("transaction_id",$transaction->id)->get();
                if($return != null){
                    $total_purchase = \App\PurchaseLine::where("transaction_id",$transaction->id)->select(DB::raw("SUM(quantity_returned*bill_return_price) as total_price"),DB::raw("SUM(quantity_returned) as qty"))->first();
                    $total_purchase_product = \App\PurchaseLine::where("transaction_id",$transaction->id)->where("product_id",$id)->select(DB::raw("SUM(quantity_returned*bill_return_price) as total_price"),DB::raw("SUM(quantity_returned) as qty"))->first();
                
                }else{
                    $total_purchase = \App\PurchaseLine::where("transaction_id",$transaction->id)->select(DB::raw("SUM(quantity*purchase_price) as total_price"),DB::raw("SUM(quantity) as qty"))->first();
                    $total_purchase_product = \App\PurchaseLine::where("transaction_id",$transaction->id)->where("product_id",$id)->select(DB::raw("SUM(quantity*purchase_price) as total_price"),DB::raw("SUM(quantity) as qty"))->first();
                }
                $cost_inc_exp   = 0;
                
                 if(count($purchase)>0){
                    foreach($purchase as $pli){
                        if($pli->product_id == $id){
                            //...... for second price in item movement
                            if($total_purchase->total_price != 0){
                                $percent       = ($total_expense - $dis) / $total_purchase->total_price;
                                $additional    = $pli->purchase_price * $percent ;
                                $qty_multy     = $pli->quantity * ($pli->purchase_price + $additional);
                                $cost_inc_exp += $qty_multy ;
                                

                            }else{
                                $cost_inc_exp += $pli->quantity * $pli->purchase_price;
                            }
                        }
                    }
                }

                if($total_purchase_product->qty != 0){
                    $final  =  $cost_inc_exp / $total_purchase_product->qty;
                }else{
                    $final  = 0 ;
                }
             
                $html  =  round($final ,2);
                return $html;
    }
    public static function closing_stock($business_id)
    {
        $closing_cost = 0;
        $listed       =  [] ;
        $products     = Product::where("products.business_id",$business_id)->join('warehouse_infos as wf','wf.product_id','products.id')->get();
        foreach($products as $it){
            $cost          = Product::product_cost($it->id);
            $listed[$it->id.$it->name]      = $cost ;
            $it_product    = \App\Models\WarehouseInfo::where("product_id",$it->id)->sum("product_qty"); 
            if($it_product > 0){
                $closing_cost += round($cost,2) * round($it_product,2);
            }
        }   
        return $closing_cost;
    }
    public static function closing_stock_filter($business_id,$start_date,$end_date)
    {
        $closing_cost = 0;
        $listed       =  [] ;
        $products     = Product::where("business_id",$business_id)->get();
        foreach($products as $it){
            $cost                           =  Product::product_cost_filter($it->id,$start_date,$end_date);
            $listed[$it->id.$it->name]      =  $cost ;
            $it_product                     =  \App\Models\WarehouseInfo::where("product_id",$it->id)->sum("product_qty"); 
            $closing_cost                  +=  ($cost * $it_product);
        }   
        return $closing_cost;
    }
// ..............1
    public static function product_cost_filter($id,$start_date,$end_date){
        $item = \App\Models\ItemMove::whereDate('date','>=',$start_date)->whereDate('date','<=',$end_date)->orderby("date","desc")->orderBy("id","desc")->where("product_id",$id)->first();
        if(!empty($item)){
            $costs_ = $item->unit_cost;
            $qty_   = $item->current_qty;
        }else{
            $costs_ = 0;
            $qty_   = 0;
        }
        return  $outPut = [
            "cost" => $costs_ ,
            "qty"  => $qty_
        ];
    } 
// ..............2
    public static function between_purchase_recieve($id)
    {
        $lines_recived  =  \App\PurchaseLine::OrderBy('id','desc')->where('product_id',$id)->whereHas('transaction',function($query){
                                                                                $query->where('type','purchase');
                                                                                $query->where('status','!=','recieved');
                                                                            })->sum("quantity");
        $fin_rev        =  \App\Models\RecievedPrevious::where("product_id",$id)->sum("current_qty");
        $r_margin       =  $lines_recived - $fin_rev;
        return $r_margin;
    }  
    public static function between_sell_deliver($id)
    {
        $lines_delivery =  \App\TransactionSellLine::OrderBy('id','desc')->whereHas('product',function($query) use($id){
                                                                                $query->where('id',$id);
                                                                                $query->where('enable_stock',1);
                                                                            })->whereHas('transaction',function($query){
                                                                                $query->whereIn('type',['sell',"sale"]);
                                                                                $query->where('status','!=','delivered');
                                                                                $query->whereIn('status', ['ApprovedQuotation' ,"draft","final" ]);
                                                                                $query->whereIn('sub_status', ["proforma","f"]);
                                                                            })->sum("quantity");
        $fin_del        =  \App\Models\DeliveredPrevious::whereHas('transaction',function($query){
                                                        $query->whereIn('type',['sell',"sale"]);
                                                        $query->where('status','!=','delivered');
                                                        $query->whereIn('status', ['ApprovedQuotation' ,"draft","final" ]);
                                                        $query->whereIn('sub_status', ["proforma","f"]);
                                                    })->whereHas('product',function($query) use($id){
                                                        $query->where('id',$id);
                                                        $query->where('enable_stock',1);
                                                    })->sum("current_qty");
        $d_margin       =  $lines_delivery - $fin_del;
        return  $d_margin;
    }    
    public static function purchase_cost_before_global_dis($id,$transaction_id)
    {
        $transaction = \App\Transaction::find($transaction_id);
        $total_purchase_product = \App\PurchaseLine::where("transaction_id",$transaction->id)->where("product_id",$id)->select(DB::raw("SUM(quantity*purchase_price) as total_price"),DB::raw("SUM(quantity) as qty"))->first();
        if($total_purchase_product->qty != 0){
            $final = $total_purchase_product->total_price / $total_purchase_product->qty ;
        }else{
            $final = 0;
        }
        return $final;
    }
    public static function discount_for_one_row($id,$transction_id)
    {
        $transaction = \App\Transaction::find($transction_id);
        $total_purchase = \App\PurchaseLine::where("transaction_id",$transaction->id)->select(DB::raw("SUM(quantity*purchase_price) as total_price"),DB::raw("SUM(quantity) as qty"))->first();
        $total_purchase_product = \App\PurchaseLine::where("transaction_id",$transaction->id)->where("product_id",$id)->select(DB::raw("SUM(quantity*purchase_price) as total_price"),DB::raw("SUM(quantity) as qty"))->first();
        if($total_purchase_product->qty != 0){
            $final = $total_purchase_product->total_price / $total_purchase_product->qty ;
        }else{
            $final = 0;
        }
        if ($transaction->discount_type == "fixed_before_vat"){
            $dis = $transaction->discount_amount;
        }else if ($transaction->discount_type == "fixed_after_vat"){
            $tax = \App\TaxRate::find($transaction->tax_id);
            $dis = ($transaction->discount_amount*100)/(100+$tax->amount) ;
        }else if ($transaction->discount_type == "percentage"){
            $dis = ($transaction->total_before_tax *  $transaction->discount_amount)/100;
        }else{
            $dis = 0;
        }
        if($total_purchase->total_price != 0){
            $discount_percentage =  $dis/$total_purchase->total_price;
        }else{
            $discount_percentage =  0;
        }
        $total_discount = $discount_percentage  ;
         return  $total_discount;
    }
    public static function moreUnit($data,$product){ 
        foreach($data["actual_name"] as $key => $value){
            $input = [];
            $input['business_id']          = $product->business_id;
            $input['created_by']           = $product->created_by;
            $input['actual_name']          = $data["actual_name"][$key];
            $input['price_unit']           = $data["price_unit"][$key];
            $input['short_name']           = $data["short_name"][$key];
            $input['base_unit_id']         = $data["base_unit_id"][$key];
            $input['base_unit_multiplier'] = $data["base_unit_multiplier"][$key];
            $input['allow_decimal']        = $data["allow_decimal"][$key];
            $input['product_id']           = $product->id;
            
            if (!empty($input['base_unit_id']) && !empty($input['base_unit_multiplier'])) {
                $base_unit_multiplier      = $data["base_unit_multiplier"][$key];
                if ($base_unit_multiplier != 0) {
                    $input['base_unit_multiplier'] = $base_unit_multiplier;
                }
            }
            $unit = Unit::create($input);
        }
    }
    public static function moreUpdateUnit($data,$product){ 
        //...... get all unit for product 
        $unit      = Unit::where("product_id",$product->id)->get();
        //...... initialize for old and new and delete  rows
        $old_id    = [];
        $new_id    = [];
        //...... keys
        $keys      = [];
       
        //...... 
        if(isset($data["line_ids"])){
            foreach($data["line_ids"] as $keyn => $ii){
                $old_id[] = $ii;
                $keys  [] = $keyn;
            }
        }else{
            $old_id = [];
            $keys   = null;
             
        }
       
        if(isset($data["actual_name"])){
            foreach($data["actual_name"] as $keyv => $i){
                if($keys != null ){
                    if(!in_array($keyv,$keys)){
                        $new_id[] = $keyv;
                        array_push($keys,$keyv);
                    }
                }else{
                    $new_id[] = $keyv;
                }
            }
        }
        $delete = Unit::where("product_id",$product->id)->whereNotIn("id",$old_id)->get();
        
        foreach($delete as $i){
                $i->delete();
        }
        $oldnew    = Unit::where("product_id",$product->id)->whereIn("id",$old_id)->get();
        
        foreach($oldnew as $key => $i){
            $input = [];
            $i->business_id           = $product->business_id;
            $i->created_by            = $product->created_by;
            $i->actual_name           = $data["actual_name"][$key];
            $i->price_unit            = $data["price_unit"][$key];
            $i->short_name            = $data["short_name"][$key];
            $i->base_unit_id          = $data["base_unit_id"][$key];
            $i->base_unit_multiplier  = $data["base_unit_multiplier"][$key];
            $i->allow_decimal         = $data["allow_decimal"][$key];
            $i->product_id            = $product->id;
            
            if (!empty($input['base_unit_id']) && !empty($input['base_unit_multiplier'])) {
                $base_unit_multiplier      = $data["base_unit_multiplier"][$key];
                if ($base_unit_multiplier != 0) {
                    $input['base_unit_multiplier'] = $base_unit_multiplier;
                }
            }
            $i->update();
        }
        foreach($data["actual_name"] as $key => $value){
          
            if(in_array($key,$new_id)){
                 
                $input = [];
                $input['business_id']          = $product->business_id;
                $input['created_by']           = $product->created_by;
                $input['actual_name']          = $data["actual_name"][$key];
                $input['price_unit']           = $data["price_unit"][$key];
                $input['short_name']           = $data["short_name"][$key];
                $input['base_unit_id']         = $data["base_unit_id"][$key];
                $input['base_unit_multiplier'] = $data["base_unit_multiplier"][$key];
                $input['allow_decimal']        = $data["allow_decimal"][$key];
                $input['product_id']           = $product->id;
                
                if (!empty($input['base_unit_id']) && !empty($input['base_unit_multiplier'])) {
                    $base_unit_multiplier      = $data["base_unit_multiplier"][$key];
                    if ($base_unit_multiplier != 0) {
                        $input['base_unit_multiplier'] = $base_unit_multiplier;
                    }
                }
                $unit = Unit::create($input);
                array_push($new_id,$key);
            }
        }
    } 

    // ************ api e-commerce **** \\
        public static function video($i){
            if($i->video != null || $i->video != ""){
                $vedio="";
                $name_vedio = json_decode($i->video);
                foreach($name_vedio as $it){
                    $vedio = \URL::to("storage/app/public/".$it);
                }
                if($vedio != "" && $vedio != null){
                    return $vedio;
                }else{
                    return null;
                }
            }
        }
        public static function images($i){
                $price                   = 0; 
                $allData                 = [];
                $more_image              = [];
                $more_image_items        = [];
                $product_details         = \App\Product::getDetails($i);
                foreach($product_details->variations as $variation){
                    if($i->type == "combo"){
                        $items  = $variation->combo_variations ;
                        foreach($items as $it){
                            $v_arn               = \App\Product::variations_id($it["variation_id"]);
                            $more_image_items[]  = $v_arn["product"];
                            $price += $v_arn["product"]["price"];
                            foreach($v_arn["variation"]->media as $media){
                                $more_image[]   = $media->getDisplayUrlAttribute();
                            }
                        }
                    }else{
                        $price                   =  null;
                        foreach($variation->media as $media){
                            $more_image[]        = $media->getDisplayUrlAttribute();
                        }
                    } 
                }
                $allData["more_image"]=$more_image;
                $allData["more_image_items"]=$more_image_items;
                $allData["price"]=$price;
                return $allData;
        }
        public static function getData(){
            $discount_pro    = [];
            $combo           = [];
            $products        = \App\Product::SelectAll();
            $cat        = \App\Models\Ecommerce\ShopCategory::where("view",1)->get();
            $category   = [];
            foreach($cat as $ei){
                $category[] = [
                    "id"          => $ei->id,  
                    "name"        => $ei->name,  
                    "business_id" => $ei->business_id,  
                    "short_code"  => $ei->short_code,  
                    "parent_id"   => $ei->parent_id,  
                    "created_by"  => $ei->created_by,  
                    "description" => $ei->description,  
                    "image"       => $ei->image,  
                ];
            }
            $feature         = [];
            foreach($products as $i){
                $prs                     = json_decode($i);
                $prs->sale_price         = round($prs->sale_price ,2);
                if($i->product_type == "single"){
                    $productPrice                     = \App\Product::productPrice($i); 
                    $prs->price_before                = round($productPrice["before_price"],2);
                    $prs->price_after                 = round($productPrice["after_price"] ,2);
                    // if($productPrice["before_price"] != $productPrice["after_price"]){
                    //     $discount_pro[]      = $prs ;
                    // }
                }else if($i->product_type == "combo"){
                    $prs->sale_price   = ($alters["price"]!=null)?round($alters["price"],2):round($prs->sale_price,2);
                }
                $vedio                   = \App\Product::video($i);
                $alters                  = \App\Product::images($i);
                if($vedio != null){
                    $prs->vedios              = $vedio;
                }
                //..  product details for image and vedio
                if(count($alters["more_image"])>0){
                    $prs->alter_images            =   $alters["more_image"];
                }
                if(count($alters["more_image_items"])>0){
                    $prs->children                =   $alters["more_image_items"];
                }
                if($i->is_feature != 0){
                    $feature[] = $prs;
                }
                if($i->ecm_collection == 1){
                    $combo[]           = $prs ;
                }
                if($i->ecm_discount == 1){
                    $discount_pro[]           = $prs ;
                }
                $product_last[]    = $prs;
            }
            $sections    = \App\Models\Ecommerce::where("view",1)->where("topSection",0)->get();
            $top_section = \App\Models\Ecommerce::where("view",1)->where("topSection",1)->first();
            
            $arrays      = [];
            if(!empty($top_section)){
                $title_list["top_section"] = [
                        "id"         => $top_section->id,
                        "Name"       => $top_section->name,
                        "Image"      => $top_section->image_url ,
                        "Title"      => $top_section->title,
                        "Description"=> $top_section->desc ,
                        "Button"     => $top_section->button
                ];
            }
            foreach($sections as $key => $i){
                if($i->about_us == 0){
                    $arrays[] = [
                                    "index" => $it->index_item,
                                    "Name"  => $i->name,
                                    "Image" => $i->image_url ,
                                    "Title" => $i->title,
                                    "Description"=> $i->desc ,
                                    "Button"=>  $i->button
                                ];     
                }
            }
            $title_list["others"] = $arrays;
            $title                = $title_list;  
            $array["item"]        = $product_last;          
            $array["Category"]    = $category;          
            $array["Feature"]     = $feature;          
            $array["Discount"]    = $discount_pro;          
            $array["Collection"]  = $combo;          
            $array["Title"]       = $title;        
            return $array;
        }
        public static function getAbout($id = null){
            
            $topSection           = \App\Models\Ecommerce::where("view",1)->where("about_us",1)->where("topSection",1)->first();
            
            
            if($id == null){
                $about                = \App\Models\Ecommerce::where("view",1)->where("about_us",1)->where("topSection",0)->get();
            }else{ 
                $about                = \App\Models\Ecommerce::where("view",1)->where("about_us",1)->where("id",$id)->get();
            }
            if(!empty($topSection)){
                $list["top_section"]  = [
                    "id"          => $topSection->id ,
                    "Name"        => $topSection->name ,
                    "Image"       => $topSection->image_url ,
                    "Title"       => $topSection->title,
                    "Description" => $topSection->desc ,
                    "view"        => $topSection->view ,
                    // "Button"=>  $i->button
                ];
            }
            $about_us    = [];
            foreach($about as $key => $it){
                    $about_us[] = [
                        "index"       => $it->index_item ,
                        "id"          => $it->id ,
                        "Name"        => $it->name ,
                        "Image"       => $it->image_url ,
                        "Title"       => $it->title,
                        "Description" => $it->desc ,
                        "view"        => $it->view ,
                        // "Button"=>  $i->button
                    ];
            }
            $list["others"]       = $about_us;
            $array["About-us"]    = ($id==null)?$list:$about_us;          
            return $array;
        }
        public static function getSocial($id = null) {
            $business_location  = \App\BusinessLocation::first();
            if($id == null) { 
                $social_media       = \App\Models\Ecommerce\SocialMedia::where("business_id",$business_location->business_id)->select(["id","title","link","icon"])->get();
            }else{
                $social_media       = \App\Models\Ecommerce\SocialMedia::where("business_id",$business_location->business_id)->select(["id","title","link","icon"])->where("id",$id)->get();
            } 
            
            $list_              =  [];
            foreach($social_media as $i){
                $list_ [] = [
                        "id"    => $i->id,
                        "title" => $i->title,
                        "link"  => $i->link,
                        "icon"  => $i->icon_url,
                ];
            }
            return $list_;
        }
        public static function getSubscribe(){
            $subscribe           = \App\Models\Ecommerce::where("subscribe",1)->get();
            $subscribeList       = [];
            foreach($subscribe as $it){
                    $subscribeList[] = [
                        "Name"        => $it->name ,
                        "id"          => $it->id ,
                        "Image"       => $it->image_url ,
                        "Title"       => $it->title,
                        "Description" => $it->desc ,
                        "Button"      => $it->button
                    ];
            
            }
            $array["Subscribe"]    = $subscribeList;          
            return $array;
        }
        public static function getContact($id = null){
            
            if($id == null){
                $contact = \App\Models\ContactUs::where("view",1)->get();
            }else{ 
                $contact = \App\Models\ContactUs::where("view",1)->where("id",$id)->get();
            }
            $arrayContacts    = [];
            foreach($contact as $key => $i){
                $arrayContacts[] = [
                                    "index"  => $i->index_item,
                                    "id"     => $i->id,
                                    "Title"  => $i->title,
                                    "Mobile" => $i->mobile ,
                                    "icon"   => $i->icon_url ,
                                    "view"   => $i->view ,
                                    ];
                            
            }
            $array["Contact-us"]  = $arrayContacts;          
            return $array;
        }
        public static function Feature(){
            // \DB::raw("IF(products.type = 'variable' OR products.type = 'combo', products.category_id, NULL) as cat"),
            $product = \App\Product::join('variations as vr','products.id','vr.product_id')->select("products.id","products.name" ,"products.sub_category_id","products.sku as code","vr.sell_price_inc_tax as sale_price","products.warranty_id","products.product_vedio","products.type","products.feature","products.image")->where("products.ecommerce",1)->where("products.feature","=",1)->get();
            return $product;
        }
        public static function Variable(){
            $product = \App\Product::where("type","!=","single")->get();
            return $product;
        }
        public static function SelectAll(){
            // \DB::raw("IF(products.type = 'variable' OR products.type = 'combo', products.category_id, NULL) as cat"),
            $products        = \App\Product::join("product_variations as pv","pv.product_id","products.id")
                                ->join("variations as  vr","vr.product_id","products.id")
                                // ->join("variation_templates as  vt","pv.variation_template_id","vt.id")
                                // ->join("media as  md","md.model_id","vr.id")
                                ->select(   'products.id',
                                            'products.name as name_product' ,
                                            'products.sub_category_id',
                                            'products.sku as code',
                                            // 'pv.id as pv_id',
                                            // 'vr.id as vr_id',
                                            'vr.sell_price_inc_tax as sale_price',
                                            'products.warranty_id',
                                            'products.product_vedio as video',
                                            'products.type as product_type',
                                            'products.feature as is_feature',
                                            'products.ecm_discount as ecm_discount',
                                            'products.ecm_collection as ecm_collection',
                                            'products.image',
                                            'pv.variation_template_id',
                                            'pv.name as child_name',
                                            'pv.is_dummy'
                                )
                                ->where("products.ecommerce",1)
                                ->where("products.feature",1)
                                ->orWhere("products.ecm_discount",1)
                                ->orWhere("products.ecm_collection",1)
                                ->groupBy("products.id")
                                ->get();
            return $products;
        }
        public static function getDetails($i){
            $product = \App\ProductVariation::where('product_id', $i->id)
                                ->with(['variations', 'variations.media'])
                                ->first();
            return $product;
        }
        public static function variations_id($i){
            $product              = \App\Variation::find($i);
            $array["product"]     = [
                                        "id"        => $product->product["id"],
                                        "name"      => $product->product["name"],
                                        "code"      => $product->product["sku"],
                                        "price"     => $product->default_sell_price,
                                        "image"     => $product->product["image"],
                                        "image_url" => $product->product["image_url"],
                                    ];                   
            $array["variation"]   = $product         ;            
            return $array;
        }
        public static function productPrice($i){
            $product              = \App\Models\ProductPrice::where("product_id",$i->id)->whereHas("unit",function($query){
                                                                    $query->where("default",1);
                                                            })->whereIn("name",["ECM After Price","ECM Before Price"])
                                                            ->get();
            
            if(count($product)>0){
        
                foreach($product as $i){
                    if($i->name == "ECM After Price"){
                        $array["after_price"]  = $i->sell_price_inc_tax;            
                    }else{
                        $array["before_price"] = $i->sell_price_inc_tax;                   
                    }
                }
                return $array;
            }else{
                $array["after_price"]      = 0;
                $array["before_price"]     = 0;   
                return $array;
            }
        } 
        public static function StorePage($data,$client,$request) {
            try{
                $list         = [];
                $search       = 1;
                $filter       = 0;

                // ******
                $page         = $request->query('page', 1);
                $skip         = $request->query('skip', 0);
                $limit        = $request->query('limit', 25);
                $Skp          = ($page-1)*$limit;
                // ******
                
                $product      = \App\Product::join("variations as vr","products.id","vr.product_id")
                                            ->select("products.id","products.product_vedio as vedio_name","products.category_id as main_category","products.brand_id as brand","products.sub_category_id as sub_category","products.unit_id","products.sku as code","products.name","products.product_description as description","vr.sell_price_inc_tax as sale_price","products.image")
                                            
                                            ;
                // // .... 1 product type in_array(single,variable,combo)
                // if(isset($data["product_type"]) && $data["product_type"] != "" && $data["product_type"] != null){
                //     switch($data["product_type"]){
                //         case 1: 
                //             $product->where("products.type","single");    
                //             break;
                //         case 2: 
                //             $product->where("products.type","variable");    
                //             break;
                //         case 3: 
                //             $product->where("products.type","combo");    
                //             break;
                //         default: break;
                //     }    
                // }
                // .... 1 product type in_array(single,variable,combo)
                if(isset($data["product_type"]) && $data["product_type"] != "" && $data["product_type"] != null){
                        $array_filter = [];
                        if(in_array(1,$data["product_type"])){
                            $array_filter[] = "single";
                            
                        }
                        if(in_array(2,$data["product_type"])){
                            $array_filter[] = "variable";
                        }
                        if(in_array(3,$data["product_type"])){
                            $array_filter[] = "combo";
                            
                
                        }
                            $product->whereIn("products.type",$array_filter); 
                }
                    
                // .... 2 product unit
                if(isset($data["product_unit"]) && $data["product_unit"] != "" && $data["product_unit"] != null){
                        $product->whereIn("products.unit_id",$data["product_unit"]);     
                }
                
                // .... 3 product category
                if(isset($data["product_category"]) && $data["product_category"] != "" && $data["product_category"] != null){
                        $product->whereIn("products.category_id",$data["product_category"]);
                }
                
                // .... 4 product sub category
                if(isset($data["product_sub_category"]) && $data["product_sub_category"] != "" && $data["product_sub_category"] != null){
                        $product->whereIn("products.sub_category_id",$data["product_sub_category"]);
                }
                
                // .... 5 product brand
                if(isset($data["product_brand"]) && $data["product_brand"] != "" && $data["product_brand"] != null){
                        $product->whereIn("products.brand_id",$data["product_brand"]);
                }
                
                // // .... 6 product price range
                // if(isset($data["product_price_range"]) && $data["product_price_range"] != "" && $data["product_price_range"] != null){
                //     switch($data["product_price_range"]){
                //         case 1: 
                //             $product->where("vr.sell_price_inc_tax",">=",100);    
                //             $product->where("vr.sell_price_inc_tax","<=",500);    
                //             break;
                //         case 2: 
                //             $product->where("vr.sell_price_inc_tax",">=",500);    
                //             $product->where("vr.sell_price_inc_tax","<=",1000);    
                //             break;
                //         case 3: 
                //             $product->where("vr.sell_price_inc_tax",">=",1000);    
                //             $product->where("vr.sell_price_inc_tax","<=",5000);    
                //             break;
                //         case 4: 
                //             $product->where("vr.sell_price_inc_tax",">=",5000);    
                //             $product->where("vr.sell_price_inc_tax","<=",10000);    
                //             break;
                //         case 5: 
                //             $product->where("vr.sell_price_inc_tax",">=",10000);    
                //             $product->where("vr.sell_price_inc_tax","<=",50000);    
                //             break;
                //         default: break;
                //     }    
                // }

                // .... 6 product price range M
                if(isset($data["product_price_range_max"]) && $data["product_price_range_max"] != "" && $data["product_price_range_max"] != null){
                        $product->where("vr.sell_price_inc_tax","<=",$data["product_price_range_max"]);    
                }
                if(isset($data["product_price_range_min"]) && $data["product_price_range_min"] != "" && $data["product_price_range_min"] != null){
                        $product->where("vr.sell_price_inc_tax",">=",$data["product_price_range_min"]);    
                }
                
                // .... 7 search
                if(isset($data["Search"]) && $data["Search"] != "" && $data["Search"] != null){
                        $product->where("products.name","like","%".trim($data["Search"])."%");
                        $product->orWhere("products.sku","like","%".trim($data["Search"])."%");
                        $product->orWhere("products.sku2","like","%".trim($data["Search"])."%");
                }
                
                // .... 8 product sort date
                if(isset($data["sort"]) && $data["sort"] != "" && $data["sort"] != null){
                    if($data["sort"] == 0 || $data["sort"] == 1){
                        $product->orderBy("products.id","asc");
                    }
                    if($data["sort"] == 2){
                        $product->orderBy("products.id","desc");
                    }
                    if($data["sort"] == 3){
                        $product->orderBy("vr.sell_price_inc_tax","asc");
                    }
                    if($data["sort"] == 4){
                        $product->orderBy("vr.sell_price_inc_tax","desc");
                    }
                    if($data["sort"] == 5){
                        $product->orderBy("products.created_at","asc");
                    }
                    if($data["sort"] == 6){
                        $product->orderBy("products.created_at","desc");
                    }
                }
                

                $totalProduct = $product->where("products.ecommerce",1)->get()->count();

                $totalPages   = ceil($totalProduct / $limit);
                // ******
                $prevPage     = $page > 1 ? $page - 1 : null;
                $nextPage     = $page < $totalPages ? $page + 1 : null;
                $product_last = [];
                // ******
                foreach($product->skip($Skp)->take($limit)->get() as $it){
                    $prs                     = json_decode($it);
                    $prs->sale_price         = round($prs->sale_price,2);

                    $contacts_price = TransactionSellLine::orderby("id","desc")->where("product_id",$it->id)->select()->first();
                    if($contacts_price != null){
                        if($it->tax!=null){$tax_rates = \App\TaxRate::where('id', $it->tax)->select(['amount'])->first();$tax_ = $tax_rates->amount ;}else{$tax_ = 0;}
                        $price = ($contacts_price->unit_price*$tax_/100)+($contacts_price->unit_price);                
                    }
                    $product_deatails = \App\ProductVariation::where('product_id', $it->id)
                                            ->with(['variations', 'variations.media'])
                                            ->first();
                    if($it->vedio_name != null || $it->vedio_name != ""){
                            $vedio="";
                            $name_vedio = json_decode($it->vedio_name);
                            foreach($name_vedio as $i){
                                $vedio = \URL::to("storage/app/public/".$i);
                            }
                            if($vedio != "" && $vedio != null){
                                $prs->vedio            =   $vedio;
                            }
                    }
                    $more_image = [];
                    foreach($product_deatails->variations as $variation){
                            foreach($variation->media as $media){
                                $more_image[] = $media->getDisplayUrlAttribute();
                            }
                    }        
                    if(count($more_image)>0){
                        $prs->alter_images            =   $more_image;
                    }
                    $prs->image_url            =   ($prs->image_url != "")? $prs->image_url:"https://t4.ftcdn.net/jpg/04/99/93/31/360_F_499933117_ZAUBfv3P1HEOsZDrnkbNCt4jc3AodArl.jpg";
                    // $prs->image            =   ($prs->image_url != "")? $prs->image_url:"https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1665px-No-Image-Placeholder.svg.png";
                    $wishlist = \App\Models\WishList::where("product_id",$prs->id)->first();
                    if(!empty($wishlist)){
                        $prs->wishlist = true;
                    }else{
                        $prs->wishlist = false;
                    }
                    $product_last[] = $prs;
                }
                $count                 = count($product_last);
                // ******
                $store                 = \App\Models\Ecommerce::storeSection();
                $type                  = \App\Models\Ecommerce::typeProduct();
                $brand                 = \App\Models\Ecommerce::brandProduct();
                $unit                  = \App\Models\Ecommerce::unitProduct();
                $category              = \App\Models\Ecommerce::categoryProduct();
                $iid                   = isset($data["product_category"])?$data["product_category"]:null;
                $subCategory           = \App\Models\Ecommerce::subCategoryProduct($iid);
                $collection            = \App\Models\Ecommerce::collectionProduct();
                $list["first_section"] = $store;
                $list["collection"]    = $collection;
                $sort_list             = [];
                $sort_list[]              = [
                        "id"    => 1,
                        "value" => "Ascending"
                ];
                $sort_list[]              = [
                        "id"    => 2,
                        "value" => "Descending"
                ];
                $sort_list[]              = [
                        "id"    => 3,
                        "value" => "Price Ascending"
                ];
                $sort_list[]              = [
                        "id"    => 4,
                        "value" => "Price DESC"
                ];
                $sort_list[]              = [
                        "id"    => 5,
                        "value" => "Date Descending"
                ];
                $sort_list[]              = [
                        "id"    => 6,
                        "value" => "Date Descending"
                ];
                $ranges[]              = [
                        "id"    => 1,
                        "value" => "( 100 - 500 ) AED"
                ];
                $ranges[]              = [
                        "id"    => 2,
                        "value" => "( 500 - 1000 ) AED"
                ];
                $ranges[]              = [
                        "id"    => 3,
                        "value" => "( 1000 - 5000 ) AED"
                ];
                $ranges[]              = [
                        "id"    => 4,
                        "value" => "( 5000 - 10000 ) AED"
                ];
                $ranges[]              = [
                        "id"    => 5,
                        "value" => "( 10000 - 50000 ) AED",
                ];
                $allData  = [
                    "title"    => "Price Range",
                    "name"     => "product_price_range",
                    "options"  => $ranges
                ];
                $allDataSort  = [
                    "title"    => "Sort Section",
                    "name"     => "Ascending",
                    "options"  => $sort_list
                ];
                $list["product"]       = [
                        "items"         => $product_last ,
                        "totalRows"     => $totalProduct,
                        'current_page'  => $page,
                        'last_page'     => $totalPages,
                        'limit'         => 25,
                        'prev_page_url' => $prevPage ? "/api/Ecom/Store-page?page=$prevPage" : null,
                        'next_page_url' => $nextPage ? "/api/Ecom/Store-page?page=$nextPage" : null,
                ]; 
                $list["filter"]   = [ 
                    $type,
                    $unit,
                    $category,
                    $subCategory,
                    $brand,
                    $allDataSort,
                    ["title"    => "Search By Text",
                    "name"     => "Search",
                    "options"  => ["id"=> 0 ,"value"=>""]
                    ]
                ];

                return $list;
            }catch(Exception $e){
                return false;
            }
        } 
        public static function Software($data,$request) 
        {
            try{
                $list              = [] ; $allData = []; $otherSection = [];
                $software          = \App\Models\Ecommerce\Software::where("view",1)->get();
                foreach($software as $k => $ies){
                    if($ies->topSection == 1){
                        $allDataTop[] = [
                            "id"               => $ies->id,
                            "name"             => $ies->name,
                            "title"            => $ies->title,
                            "button"           => $ies->button,
                            "description"      => $ies->description,
                            "image_url"        => $ies->image_url,
                            // "image_url"        => "https://pactsoft.com/india/assets/images/module-page/Property-Management-Software-in-India.jpg",
                            "image_url_1"      => "https://rockwellautomation.scene7.com/is/image/rockwellautomation/DesignConfigurationSoftware_563430904-1.1280.jpg",

                        ];
                        $allData["info"]    = $allDataTop;
                        $d_vedio="";
                        if(($ies->alter_image != null || $ies->alter_image != "" ) && $k == 0){
                            $allData["slider"]  = $ies->alter_image_url;
                        }
                        if($ies->video != null || $ies->video != ""){
                            $vedio="";
                                $name_vedio = json_decode($ies->video);
                                foreach($name_vedio as $i){
                                    $vedio = \URL::to("/public/uploads/img/vedios/".$i);
                                }
                                if($vedio != "" && $vedio != null){
                                    $d_vedio            =   $vedio;
                                }
                            $allData["vedio"]   = $d_vedio;
                        }
                    }else{
                        $otherSection[] = [
                            "id"               => $ies->id,
                            "name"             => $ies->name,
                            "title"            => $ies->title,
                            "description"      => $ies->description,
                            "image_url"        => $ies->image_url,
                            "button"           => $ies->button,
                        ];
                    }
                }
                $list["ImageSlider"]    = $allData["slider"];
                $list["VedioSection"]   = $allData["vedio"];
                $list["TopSection"]     = $allData["info"];
                $list["OtherSection"]   = $otherSection;
                return $list;
            }catch(Exception $e){
                return false;
            }
        }
    // ************* end api e-commerce ****  \\

    /**
     * Product Dropdown
     *
     * @param int $business_id
     * @param string $type Product type
     * @return array
     */
    public static function forDropdown($business_id)
    {
        $products = Product::where('business_id', $business_id)
                                        ->select(DB::raw('IF(sku IS NOT NULL, CONCAT(name, " || ", sku), name) as name'), 'id')
                                        ->orderBy('name', 'asc')
                                        ->get();

        $dropdown =  $products->pluck('name', 'id');

        return $dropdown;
    }


    // ** related
    public static function related($id) {
        $product     = Product::find($id);
        $Related_s   = Product::where("category_id",$product->category_id)->limit(6)->get();
        $list        = [];
        foreach($Related_s as $i){
            $list [] = [
                "id"    => $i->id,
                "name"  => $i->name,
                "price" => round($i->variations[0]->sell_price_inc_tax,2),
                "image" => $i->image_url
            ];
        }
        return $list; 
    }

    #2024-8-6
    public static function getListPrices($row = null) {
       if($row != null){
            $list = [
                0 => "Default Price"    ,
                1 => "Whole Price"      ,
                2 => "Retail Price"     ,
                3 => "Minimum Price"    ,
                4 => "Last Price"       ,
                5 => "ECM Before Price" ,
                6 => "ECM After Price"  ,
                7 => "Custom Price 1"   ,
                8 => "Custom Price 2"   ,
                9 => "Custom Price 3"   ,
            ];
        }else{
            $list = [
                0 => "Default Price"    ,
                1 => "Whole Price"      ,
                2 => "Retail Price"     ,
                3 => "Minimum Price"    ,
                4 => "Last Price"       ,
                5 => "ECM Before Price" ,
                6 => "ECM After Price"  ,
                7 => "Custom Price 1"   ,
                8 => "Custom Price 2"   ,
                9 => "Custom Price 3"   ,
            ];
        }
        
        return $list;
    }
    public static function getProductPrices($product_id,$type = null) {
         
     
        $list_of_prices_in_unit = \App\Models\ProductPrice::where("product_id",$product_id)->whereNotNull("unit_id")->orderBy("id","asc");
        if($type != null){
            $list_of_prices_in_unit->where("variations_value_id",$type['variation_value_id'])->where("variations_template_id",$type['variation_template_id']);
        }                                                    
        $list_of_prices_in_unit = $list_of_prices_in_unit->get();
        
   
        $list_unit     = [];$list_line     = [];
        foreach($list_of_prices_in_unit as $k => $line){
            if(!in_array($line->unit_id,$list_unit)){
                array_push($list_unit,$line->unit_id);
            }
            $line_prices[] = 
                [
                  "line_id" =>  $line->number_of_default,
                  "unit"    =>  $line->unit_id,
                  "price"   =>  ($line->default_purchase_price!=null)?$line->default_purchase_price:0,
                  "name"    =>  $line->name,
                ];
                
        }
        foreach($list_unit as $line_unit){
            $unit = [];
            foreach($line_prices as $package){
                if($package["unit"] == $line_unit){
                    $unit[]                = $package;
                }
            }
            $list_line[$line_unit] = $unit;
        }
        return $list_line;
    }
    public static function checkIfIsMove($product_id) {
        $check          = false;
        $purchase_lines = \App\PurchaseLine::where("product_id",$product_id)->first();
        $sales_lines    = \App\TransactionSellLine::where("product_id",$product_id)->first(); 
        if(!empty($purchase_lines) || !empty($sales_lines)){
            $check = true ;
        }
        return $check;
    }
}

