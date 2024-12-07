<?php

namespace  App\Http\Controllers;

use App\Brands;
use App\busines_slug;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Currency;
use App\Media;
use App\price_currencies;
use App\Product;
use App\product_barcode;
use App\ProductVariation;
use App\PurchaseLine;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Unit;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Variation;
use App\VariationGroupPrice;
use App\VariationLocationDetails;
use App\VariationTemplate;
use App\Warranty;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Excel;
use Stripe\Checkout\Session;
use App\Utils\TransactionUtil;

use Stripe\File;
use Yajra\DataTables\Facades\DataTables;


class ProductGallery extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil,TransactionUtil $transactionUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function gallery()
    {

        if (!auth()->user()->can('product.view') && !auth()->user()->can('product.create')&& !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views')  && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);
        $Products_ = Product::where("business_id",$business_id)->get();



      
         

        if (request()->ajax()) {
            $product_array = [];    
            foreach($Products_ as $Pro_){
                $warehouse_ = \App\Models\WarehouseInfo::where("product_id",$Pro_->id)->select(DB::raw("SUM(product_qty) as available"))->first();
                if(!in_array($Pro_->id,$product_array) && $warehouse_->available != 0 ){
                    array_push($product_array,$Pro_->id);
                }
            }
            // ->join('warehouse_infos as wi', 'products.id', '=', 'wi.product_id')
            $query = Product::with(['media'])
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                
                ->join('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
                ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id')
                ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
                ->join('variations as v', 'v.product_id', '=', 'products.id')
                ->leftJoin('variation_location_details as vld', 'vld.variation_id', '=', 'v.id')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

            //Filter by location
            $location_id = request()->get('location_id', null);
            $permitted_locations = auth()->user()->permitted_locations();
 
            if (!empty($location_id) && $location_id != 'none') {
                if ($permitted_locations == 'all' || in_array($location_id, $permitted_locations)) {
                    $query->whereHas('product_locations', function ($query) use ($location_id) {
                        $query->where('product_locations.location_id', '=', $location_id);
                    });
                }
            } elseif ($location_id == 'none') {
                $query->doesntHave('product_locations');
            } else {
                if ($permitted_locations != 'all') {
                    $query->whereHas('product_locations', function ($query) use ($permitted_locations) {
                        $query->whereIn('product_locations.location_id', $permitted_locations);
                    });
                } else {
                    $query->with('product_locations');
                }
            }

            $offset = request()->get('offset', 0);
            $products = $query->select(
                'products.id',
                'products.name as product',
                'products.type',
                'c1.name as category',
                'c2.name as sub_category',
                'units.actual_name as unit',
                'brands.name as brand',
                'tax_rates.name as tax',
                'products.sku',
                'products.image',
                'products.enable_stock',
                'products.is_inactive',
                'products.not_for_selling',
                'products.product_custom_field1',
                'products.product_custom_field2',
                'products.product_custom_field3',
                'products.product_custom_field4',
                'v.sell_price_inc_tax',
                DB::raw('SUM(vld.qty_available) as current_stock'),
                DB::raw('MIN(v.sell_price_inc_tax) as min_price'),
                DB::raw('MAX(v.dpp_inc_tax) as max_purchase_price'),
                // DB::raw('SUM(wi.product_qty) as available'),
                DB::raw('MIN(v.dpp_inc_tax) as min_purchase_price')

            )->orderBy('products.name')->groupBy('products.id')->offset( $offset)
                ->limit(12);

            $productname = request()->get('productname', null);
            $products->where('products.name','like','%'.$productname.'%');


            $type = request()->get('type', null);
            if (!empty($type)) {
                $products->where('products.type', $type);
            }
            $image_type = request()->get('image_type', null);
            if ($image_type=='default') {
                $products->whereNull('products.image');
            }
            if ($image_type=='image') {
                $products->where('products.image','!=', '');
            }
            $product_available = request()->get('product_available', null);
            
            if ($product_available != null) {
                if($product_available == 1){
                    $products->whereIn("products.id",$product_array);
                }else if($product_available == 0){
                    $products->whereNotIn("products.id",$product_array);
                }
            }

            $current_stock = request()->get('current_stock', null);
            //dd($products);
            if ($current_stock=='zero') {
                $products->having('current_stock', '0');
            }
            if ($current_stock=='gtzero') {
                $products->having('current_stock','>', 0);
            }
            if ($current_stock=='lszero') {
                $products->having('current_stock','<', 0);
            }

            $category_id = request()->get('category_id', null);
            if (!empty($category_id)) {
                $products->where('products.category_id', $category_id);
            }
            $sub_category_id = request()->get('sub_category_id', null);
            if (!empty($sub_category_id)) {
                $products->where('products.sub_category_id', $sub_category_id);
            }

            $brand_id = request()->get('brand_id', null);
            if (!empty($brand_id)) {
                $products->where('products.brand_id', $brand_id);
            }

            $unit_id = request()->get('unit_id', null);
            if (!empty($unit_id)) {
                $products->where('products.unit_id', $unit_id);
            }

            $tax_id = request()->get('tax_id', null);
            if (!empty($tax_id)) {
                $products->where('products.tax', $tax_id);
            }

            $active_state = request()->get('active_state', null);
            if ($active_state == 'active') {
                $products->Active();
            }
            if ($active_state == 'inactive') {
                $products->Inactive();
            }
            $not_for_selling = request()->get('not_for_selling', null);
            if ($not_for_selling == 'true') {
                $products->ProductNotForSales();
            }

            $woocommerce_enabled = request()->get('woocommerce_enabled', 0);
            if ($woocommerce_enabled == 1) {
                $products->where('products.woocommerce_disable_sync', 0);
            }

            if (!empty(request()->get('repair_model_id'))) {
                $products->where('products.repair_model_id', request()->get('repair_model_id'));
            }

            $products=$products->get();
            //added just for limte number of product
            $output['product'] = view('product_gallery.product', ['products'=>$products,'from'=>'gallery'])->render();
            $output['count']   =  $products->count();

           return $output;

        }

        $rack_enabled = (request()->session()->get('business.enable_racks') || request()->session()->get('business.enable_row') || request()->session()->get('business.enable_position'));

        $categories = Category::forDropdown($business_id, 'product');

        $brands = Brands::forDropdown($business_id);

        $units = Unit::forDropdown($business_id);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, false);
        $taxes = $tax_dropdown['tax_rates'];

        $business_locations = BusinessLocation::forDropdown($business_id);
        $business_locations->prepend(__('lang_v1.none'), 'none');

        if ($this->moduleUtil->isModuleInstalled('Manufacturing') && (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module'))) {
            $show_manufacturing_data = true;
        } else {
            $show_manufacturing_data = false;
        }

        //list product screen filter from module
        $pos_module_data = $this->moduleUtil->getModuleData('get_filters_for_list_product_screen');

        $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');

        $sub_cat = []; 
        $sub_categories_ = Category::where('business_id', $business_id)
        ->where('parent_id',"!=",0)->get();
        foreach($sub_categories_ as $key => $value){
            $sub_cat[$value->id] = $value->name;
        }   

        return view('product_gallery.index')
            ->with(compact(
                'rack_enabled',
                'categories',
                'brands',
                'sub_cat',
                'units',
                'Products_',
                'taxes',
                'business_locations',
                'show_manufacturing_data',
                'pos_module_data',
                'is_woocommerce'
            ));


        }


    public function inventory()
    {
       $business_id =busines_slug::business(request()->slug);


      /* $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);*/

        if (request()->ajax()) {
          $query = Product::with(['media'])
                ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                ->join('units', 'products.unit_id', '=', 'units.id')
                ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
                ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id')
                ->leftJoin('tax_rates', 'products.tax', '=', 'tax_rates.id')
                ->join('variations as v', 'v.product_id', '=', 'products.id')
                ->leftJoin('variation_location_details as vld', 'vld.variation_id', '=', 'v.id')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

                $query->with('product_locations');


            $offset = request()->get('offset', 0);
            $products = $query->select(
                'products.id',
                'products.name as product',
                'products.type',
                'c1.name as category',
                'c2.name as sub_category',
                'units.actual_name as unit',
                'brands.name as brand',
                'tax_rates.name as tax',
                'products.sku',
                'products.image',
                'products.enable_stock',
                'products.is_inactive',
                'products.not_for_selling',
                'products.product_custom_field1',
                'products.product_custom_field2',
                'products.product_custom_field3',
                'products.product_custom_field4',
                DB::raw('MAX(v.sell_price_inc_tax) as max_price'),
                DB::raw('MIN(v.sell_price_inc_tax) as min_price'),
                DB::raw('MAX(v.dpp_inc_tax) as max_purchase_price'),
                DB::raw('MIN(v.dpp_inc_tax) as min_purchase_price')

            )->orderBy('products.name') ->groupBy('products.id')->offset( $offset)
                ->limit(12);

            $productname = request()->get('productname', null);
            $products->where('products.name','like','%'.$productname.'%');


            $type = request()->get('type', null);
            if (!empty($type)) {
                $products->where('products.type', $type);
            }

            $category_id = request()->get('category_id', null);
            if (!empty($category_id)) {
                $products->where('products.category_id', $category_id);
            }

            $brand_id = request()->get('brand_id', null);
            if (!empty($brand_id)) {
                $products->where('products.brand_id', $brand_id);
            }

            $unit_id = request()->get('unit_id', null);
            if (!empty($unit_id)) {
                $products->where('products.unit_id', $unit_id);
            }
            $location_id = request()->get('location_id', null);
            if (!empty($location_id)) {
                $products->where('vld.location_id', $location_id);
            }


            $products=$products->get();
            //added just for limte number of product
            $output['product'] = view('product_gallery.product', ['products'=>$products,'from'=>'inventory'])->render();
            $output['count']=$products->count();

            return $output;

        }


        $categories = Category::forDropdown($business_id, 'product');
        $brands     = Brands::forDropdown($business_id);
        $units      = Unit::forDropdown($business_id);

        $business_locations  = BusinessLocation::where('business_id', $business_id)->Active()->pluck('name', 'id');
        $business_locations->prepend(__('report.all_locations'), '');
        /*total products */


        return view('product_gallery.inventory')
            ->with(compact(
                  'categories',
                'brands',
                'units',
                'business_locations'

            ));


    }


    public function setting(){

        return view('product_gallery.setting');
    }


    public function update(Request $request)
    {
        if (!auth()->user()->can('product.gallary')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $request->validate([
          'slug'=>['required', 'string', 'max:5','min:3', 'unique:busines_slugs']
       ],
       [
       'slug.unique'=>'عفوا هذا المسار موجود برجاء تغييرة!!!',
       'slug.min'=>'يجب أن يحتوي النص على الأقل 3 أحرف!!!',
       'slug.max'=>'لا يمكن أن يحتوي النص  على أكثر من 5 حرف(أحرف).!!!',
       ]);

        $data=busines_slug::updateOrCreate(['business_id'=>$business_id],
            [
            'business_id'=>$business_id,
            'slug'=>$request->slug,
        ]);
        $data->save();
        return redirect('/gallery/gallery');

    }


    public function singlproduct(Request $request){

        $product_id=$request->id;
        $product=Product::findorfail($product_id);




        $output=$request->id;
        return view('product_gallery.singlproduct',['product'=>$product]);
    }

    public function stock_report(Request $request){
            if (!auth()->user()->can('product.view') && !auth()->user()->can('product.create')&& !auth()->user()->can('SalesMan.views')&& !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('admin_supervisor.views')  && !auth()->user()->can('warehouse.views') && !auth()->user()->can('manufuctoring.views')) {
                abort(403, 'Unauthorized action.');
            }
            $business_id               = request()->session()->get('user.business_id');
            $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);
            $main_store                = []; 
            $store                     = []; 
            $store_                    = \App\Models\Warehouse::where("business_id",$business_id)->where("status",1)->get() ;
            $main_store_               = \App\Models\Warehouse::where("business_id",$business_id)->where("status",0)->get() ;
            foreach( $main_store_ as $it ){
                $main_store[$it->id]=$it->name;
            }   
            foreach( $store_ as $it ){
                $store[$it->id]=$it->name;
            }

            if (request()->ajax()) {
               
                $query = Product::with(['media'])
                    ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
                    ->leftJoin('categories as c1', 'products.category_id', '=', 'c1.id')
                    ->leftJoin('categories as c2', 'products.sub_category_id', '=', 'c2.id')
                    ->join('variations as v', 'v.product_id', '=', 'products.id')
                    ->where('products.business_id', $business_id)
                    ->where('products.type', '!=', 'modifier');
                if($request->pricegroup>0){
                    $query->leftJoin('variation_group_prices','v.id','=','variation_group_prices.variation_id')
                        ->leftJoin('selling_price_groups','selling_price_groups.id','=','variation_group_prices.price_group_id');
                      }

                //Filter by location
                $location_id = request()->get('location_id', null);
                $permitted_locations = auth()->user()->permitted_locations();

                if (!empty($location_id) && $location_id != 'none') {
                    if ($permitted_locations == 'all' || in_array($location_id, $permitted_locations)) {
                        $query->whereHas('product_locations', function ($query) use ($location_id) {
                            $query->where('product_locations.location_id', '=', $location_id);
                        });
                    }
                } elseif ($location_id == 'none') {
                    $query->doesntHave('product_locations');
                } else {
                    if ($permitted_locations != 'all') {
                        $query->whereHas('product_locations', function ($query) use ($permitted_locations) {
                            $query->whereIn('product_locations.location_id', $permitted_locations);
                        });
                    } else {
                        $query->with('product_locations');
                    }
                }
                

                $offset = request()->get('offset', 0);
                $products = $query->select(
                    'products.id',
                    'products.sku as code',
                    'products.name as product',
                    'products.type',
                    'c1.name as category',
                    'c2.name as sub_category',
                    'brands.name as brand',
                    'products.sku',
                    'products.image',
                    'v.name as variationname',
                    'v.sell_price_inc_tax as max_price',
                    'v.sell_price_inc_tax  as min_price' ,
                    'v.dpp_inc_tax  as max_purchase_price' ,
                    'v.dpp_inc_tax  as min_purchase_price',
    
                )->orderBy('products.name');


                if($request->pricegroup>0) {
                    $products->addSelect(
                        'variation_group_prices.price_inc_tax as groupprice',
                        'selling_price_groups.name as groupname'
                    );

                    $products->orderBy('groupname');
                }
                $productname = request()->get('productname', null);


                $products->where('products.name','like','%'.$productname.'%');

            if( $request->pricegroup>0)
                $products->where('variation_group_prices.price_group_id', $request->pricegroup);
                $type = request()->get('type', null);
                if (!empty($type)) {
                    $products->where('products.type', $type);
                }
                $image_type = request()->get('image_type', null);
                if ($image_type=='default') {
                    $products->whereNull('products.image');
                }
                if ($image_type=='image') {
                    $products->where('products.image','!=', '');
                }


                $current_stock = request()->get('current_stock', null);
             
                if ($current_stock=='zero') {
                    $products->having('current_stock', '0');
                }
                if ($current_stock=='gtzero') {
                    $products->having('current_stock','>', 0);
                }
                if ($current_stock=='lszero') {
                    $products->having('current_stock','<', 0);
                }

                $category_id = request()->get('category_id', null);
                if (!empty($category_id)) {
                    $products->where('products.category_id', $category_id);
                }

                $brand_id = request()->get('brand_id', null);
                if (!empty($brand_id)) {
                    $products->where('products.brand_id', $brand_id);
                }

                $unit_id = request()->get('unit_id', null);
                if (!empty($unit_id)) {
                    $products->where('products.unit_id', $unit_id);
                }

                $tax_id = request()->get('tax_id', null);
                if (!empty($tax_id)) {
                    $products->where('products.tax', $tax_id);
                }
                $until_date = request()->get('until_date', null);
                $active_state = request()->get('active_state', null);
                if ($active_state == 'active') {
                    $products->Active();
                }
                if ($active_state == 'inactive') {
                    $products->Inactive();
                }
                $not_for_selling = request()->get('not_for_selling', null);
                if ($not_for_selling == 'true') {
                    $products->ProductNotForSales();
                }

                $woocommerce_enabled = request()->get('woocommerce_enabled', 0);
                if ($woocommerce_enabled == 1) {
                    $products->where('products.woocommerce_disable_sync', 0);
                }

                if (!empty(request()->get('repair_model_id'))) {
                    $products->where('products.repair_model_id', request()->get('repair_model_id'));
                }

                $products=$products->get();
                //added just for limte number of product
                $output['product'] = view('product_gallery.partials.stock_report', ['store_id'=>request()->store_id,'main_store'=>request()->main_store,'products'=>$products,"until_date"=>$until_date??null,"product_available"=>(request()->product_available)??null,"price"=>(request()->price)??null,'from'=>'gallery'])->render();
                $output['count']=$products->count();

                return $output;

            }

            $rack_enabled = (request()->session()->get('business.enable_racks') || request()->session()->get('business.enable_row') || request()->session()->get('business.enable_position'));

            $categories = Category::forDropdown($business_id, 'product');

            $brands = Brands::forDropdown($business_id);

            $units  = Unit::forDropdown($business_id);

            $tax_dropdown = TaxRate::forBusinessDropdown($business_id, false);
            $taxes = $tax_dropdown['tax_rates'];

            $business_locations = BusinessLocation::forDropdown($business_id);
            $business_locations->prepend(__('lang_v1.none'), 'none');

            if ($this->moduleUtil->isModuleInstalled('Manufacturing') && (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module'))) {
                $show_manufacturing_data = true;
            } else {
                $show_manufacturing_data = false;
            }

            //list product screen filter from module
            $pos_module_data = $this->moduleUtil->getModuleData('get_filters_for_list_product_screen');

            $is_woocommerce = $this->moduleUtil->isModuleInstalled('Woocommerce');


           $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');
           $price_groups->prepend(trans("home.default_price"),0);
           $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);


        return view('product_gallery.stock_report')
                            ->with(compact(
                                'rack_enabled',
                                'categories',
                                'brands',
                                'main_store',
                                'store',
                                'units',
                                'currency_details',
                                'taxes',
                                'business_locations',
                                'show_manufacturing_data',
                                'pos_module_data',
                                'is_woocommerce','price_groups'
                            ));


        }

    public function stock_report_table()
   {

        if(request()->ajax()){
            
            $business_id = request()->session()->get("user.id");
            $product     = \App\Product::select(); 
          
                        
            if(!empty(request()->type)){
                $type_ = request()->type;
                $product->where("type",$type_);
            }
            if(!empty(request()->category_id)){
                $type_ = request()->category_id;
                $product->where("category_id",$type_);
            }
            if(!empty(request()->brand_id)){
                $type_ = request()->brand_id;
                $product->where("brand_id",$type_);
            }
          
            
        
            //......................... price_type
            if(!empty(request()->price)){
                $type = request()->price;
            }else{
                $type = null;
            }

            //.......................... until_date
            if(!empty(request()->until_date)){
                $until_date = request()->until_date;
            }else{
                $until_date = null;
            }

            //........................... main_Store
            if(!empty(request()->main_store)){
                $main_store = request()->main_store;
                $st = [];
                $warehouse_id = \App\Models\Warehouse::find($main_store);
                $warehouse    = \App\Models\Warehouse::where("parent_id",$warehouse_id->id)->get();
                foreach($warehouse as $it){
                    $st[] = $it->id; 
                }
                $product_ = [] ;
                $pr      = \App\Product::select()->get();
                foreach($pr as $it){
                    $total = \App\Models\WarehouseInfo::where("product_id",$it->id)->whereIn("store_id",$st)->sum("product_qty");
                    if($total > 0){
                        $product_[] =  $it->id ;
                    }
                }
                $product->whereIn("id",$product_);

            }

            //........................... Store
            if(!empty(request()->store_id)){
                $store = request()->store_id;
                $product_ = [] ;
                $pr      = \App\Product::select()->get();
                foreach( $pr as $it){
                    $total = \App\Models\WarehouseInfo::where("product_id",$it->id)->where("store_id",$store)->first();
                    if(!empty($total)){
                        if($total->product_qty > 0){
                            $product_[] =  $it->id ;
                        }
                    }
                }
                $product->whereIn("id",$product_);
            }
            
             //........... 
            $product_  = [] ;$total =0;
            $pr        = \App\Product::select()->get();
            if(request()->product_available != 0){
               $available = request()->product_available;
                foreach( $pr as $it){
                    if($available == 1){
                       $total   = \App\Models\WarehouseInfo::where("product_id",$it->id)->sum("product_qty");
                    }elseif($available == 2){
                       $total   = \App\Product::between_sell_deliver($it->id);
                    }elseif($available == 3){
                       $total   = \App\Product::between_purchase_recieve($it->id);
                    }
                    if($total > 0){
                       $product_[] =  $it->id ;
                    }
                }
            }else{
                foreach( $pr as $it){
                    $total = \App\Models\WarehouseInfo::where("product_id",$it->id)->sum("product_qty");
                    if($total <= 0){
                        $product_[] =  $it->id ;
                    }
                }
            }
            $product->whereIn("id",$product_);
            
            if(!empty(request()->productname)){
                $type_ = request()->productname;
                $product->where("name",'like','%'.$type_.'%');
            }

            $stores_filter        = request()->store_id ;
            $stores_filter_main   = request()->main_store ;
            $product->select()->get();

            return Datatables::of($product)
                            ->addColumn("image",function($row){
                                if($row->image_url)
                                    $html = '<a href="'. \URL::to($row->image_url)  .'" target="_blank">
                                            <img src="'. $row->image_url .'" alt="Product image" style="width: 100px;height: 100px">
                                            </a>';
                                else {
                                   $html =  __("home.without_image") ;  
                                }
                                return $html ;
                            })
                            ->addColumn("name",function($row){
                                $html = '<button class="btn btn-modal btn-link" data-container=".view_modal" data-href="'.action("ProductController@view",[$row->id]).'">'. $row->name .'</button>';
                                return $html ;
                            })
                            ->addColumn("qty",function($row) use($stores_filter,$stores_filter_main){
                                if($stores_filter != null ){
                                    if($stores_filter_main !=null){
                                        $st = [];
                                        $warehouse_id = \App\Models\Warehouse::find($stores_filter_main);
                                        $warehouse    = \App\Models\Warehouse::where("parent_id",$warehouse_id->id)->get();
                                        foreach($warehouse as $it){
                                            $st[] = $it->id; 
                                        }
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->whereIn("store_id",$st)->where("store_id",$stores_filter)->sum("product_qty");
                                    }else{
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->where("store_id",$stores_filter)->sum("product_qty");
                                    }
                                }else{
                                    if($stores_filter_main !=null){
                                        $st = [];
                                        $warehouse_id = \App\Models\Warehouse::find($stores_filter_main);
                                        $warehouse    = \App\Models\Warehouse::where("parent_id",$warehouse_id->id)->get();
                                        foreach($warehouse as $it){
                                            $st[] = $it->id; 
                                        }
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->whereIn("store_id",$st)->sum("product_qty");
                                    }else{
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->sum("product_qty");
                                    }
                                }
                                return round($total,config("constants.currency_precision")) ;
                            })
                            ->addColumn("actual_qty",function($row) use($stores_filter,$stores_filter_main){
                                if($stores_filter != null ){
                                    if($stores_filter_main !=null){
                                        $st = [];
                                        $warehouse_id = \App\Models\Warehouse::find($stores_filter_main);
                                        $warehouse    = \App\Models\Warehouse::where("parent_id",$warehouse_id->id)->get();
                                        foreach($warehouse as $it){
                                            $st[] = $it->id; 
                                        }
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->whereIn("store_id",$st)->where("store_id",$stores_filter)->sum("product_qty");
                                        $old   = \App\Models\RecievedPrevious::where("product_id",$row->id)->whereIn("store_id",$st)
                                                                            ->whereHas('transaction',function($query){
                                                                                $query->whereIn("status",['pending','ordered']);
                                                                            })->where("store_id",$stores_filter)
                                                                            ->sum('current_qty');
                                    }else{
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->where("store_id",$stores_filter)->sum("product_qty");
                                        $old   = \App\Models\RecievedPrevious::where("product_id",$row->id)
                                                                            ->whereHas('transaction',function($query){
                                                                                $query->whereIn("status",['pending','ordered']);
                                                                            })->where("store_id",$stores_filter)
                                                                            ->sum('current_qty');
                                         
                                    }
                                }else{
                                    if($stores_filter_main !=null){
                                        $st = [];
                                        $warehouse_id = \App\Models\Warehouse::find($stores_filter_main);
                                        $warehouse    = \App\Models\Warehouse::where("parent_id",$warehouse_id->id)->get();
                                        foreach($warehouse as $it){
                                            $st[] = $it->id; 
                                        }
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->whereIn("store_id",$st)->sum("product_qty");
                                        $old   = \App\Models\RecievedPrevious::where("product_id",$row->id)->whereIn("store_id",$st)
                                                                            ->whereHas('transaction',function($query){
                                                                                $query->whereIn("status",['pending','ordered']);
                                                                            })
                                                                            ->sum('current_qty');
                                    }else{
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->sum("product_qty");
                                        $old   = \App\Models\RecievedPrevious::where("product_id",$row->id)
                                                                            ->whereHas('transaction',function($query){
                                                                                $query->whereIn("status",['pending','ordered']);
                                                                            })
                                                                            ->sum('current_qty');
                                    }
                                }
                                $actual =  $total - $old;
                                $actual =  ($actual>0)?$actual:0;
                                return round($actual,config("constants.currency_precision")) ;
                            })
                            ->addColumn("over_qty",function($row) use($stores_filter,$stores_filter_main){
                                if($stores_filter != null ){
                                    if($stores_filter_main !=null){
                                        $st = [];
                                        $warehouse_id = \App\Models\Warehouse::find($stores_filter_main);
                                        $warehouse    = \App\Models\Warehouse::where("parent_id",$warehouse_id->id)->get();
                                        foreach($warehouse as $it){
                                            $st[] = $it->id; 
                                        }
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->whereIn("store_id",$st)->where("store_id",$stores_filter)->sum("product_qty");
                                        $old   = \App\Models\RecievedPrevious::where("product_id",$row->id)->whereIn("store_id",$st)
                                                                            ->whereHas('transaction',function($query){
                                                                                $query->whereIn("status",['pending','ordered']);
                                                                            })->where("store_id",$stores_filter)
                                                                            ->sum('current_qty');
                                    }else{
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->where("store_id",$stores_filter)->sum("product_qty");
                                        $old   = \App\Models\RecievedPrevious::where("product_id",$row->id)
                                                                            ->whereHas('transaction',function($query){
                                                                                $query->whereIn("status",['pending','ordered']);
                                                                            })->where("store_id",$stores_filter)
                                                                            ->sum('current_qty');
                                         
                                    }
                                }else{
                                    if($stores_filter_main !=null){
                                        $st = [];
                                        $warehouse_id = \App\Models\Warehouse::find($stores_filter_main);
                                        $warehouse    = \App\Models\Warehouse::where("parent_id",$warehouse_id->id)->get();
                                        foreach($warehouse as $it){
                                            $st[] = $it->id; 
                                        }
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->whereIn("store_id",$st)->sum("product_qty");
                                        $old   = \App\Models\RecievedPrevious::where("product_id",$row->id)->whereIn("store_id",$st)
                                                                            ->whereHas('transaction',function($query){
                                                                                $query->whereIn("status",['pending','ordered']);
                                                                            })
                                                                            ->sum('current_qty');
                                    }else{
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->sum("product_qty");
                                        $old   = \App\Models\RecievedPrevious::where("product_id",$row->id)
                                                                            ->whereHas('transaction',function($query){
                                                                                $query->whereIn("status",['pending','ordered']);
                                                                            })
                                                                            ->sum('current_qty');
                                    }
                                }
                                return round($old ,config("constants.currency_precision")) ;
                            })
                            ->addColumn("code",function($row){
                                return $row->sku ;
                            })
                            ->addColumn("unit_price",function($row) use($type,$until_date){
                                if($type == 1){
                                    if($until_date != null){
                                        $pro   = \App\Models\ItemMove::orderBy("date","desc")->orderBy("id","desc")->where("date","<=",$until_date)->where("product_id",$row->id)->first();
                                    }else{
                                        $pro   = \App\Models\ItemMove::orderBy("date","desc")->orderBy("id","desc")->where("product_id",$row->id)->first();
                                    }
                                    if(!empty($pro)){
                                            $po = round($pro->unit_cost,config("constants.currency_precision"));
                                    }else{
                                            $po =  0;
                                    } 
                                    $price = $po;
                                }else{
                                    $rod = \App\Variation::where("product_variation_id",$row->id)->first();
                                    if(!empty($rod)){
                                        $po = round($rod->default_sell_price,config("constants.currency_precision"));
                                    }else{
                                        $po =  0;
                                    } 
                                    $price = $po;
                                }
                               
                                return round($price,config("constants.currency_precision")) ;
                            })
                            ->addColumn("total_price",function($row) use($type,$until_date,$stores_filter,$stores_filter_main){
                                if($stores_filter != null ){
                                    if($stores_filter_main !=null){
                                        $st = [];
                                        $warehouse_id = \App\Models\Warehouse::find($stores_filter_main);
                                        $warehouse    = \App\Models\Warehouse::where("parent_id",$warehouse_id->id)->get();
                                        foreach($warehouse as $it){
                                            $st[] = $it->id; 
                                        }
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->whereIn("store_id",$st)->sum("product_qty");
                                        
                                    }else{
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->where("store_id",$stores_filter)->sum("product_qty");
                                        
                                    }
                                }else{
                                    if($stores_filter_main !=null){
                                        $st = [];
                                        $warehouse_id = \App\Models\Warehouse::find($stores_filter_main);
                                        $warehouse    = \App\Models\Warehouse::where("parent_id",$warehouse_id->id)->get();
                                        foreach($warehouse as $it){
                                            $st[] = $it->id; 
                                        }
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->whereIn("store_id",$st)->sum("product_qty");
                                        
                                    }else{
                                         
                                        $total = \App\Models\WarehouseInfo::where("product_id",$row->id)->sum("product_qty");
                                    }
                                }
                                if($type == 1){
                                    if($until_date != null){
                                        $pro   = \App\Models\ItemMove::orderBy("date","desc")->orderBy("id","desc")->where("date","<=",$until_date)->where("product_id",$row->id)->first();
                                    }else{
                                        $pro   = \App\Models\ItemMove::orderBy("date","desc")->orderBy("id","desc")->where("product_id",$row->id)->first();
                                    }
                                    if(!empty($pro)){
                                            $po = round($pro->unit_cost,config("constants.currency_precision"));
                                    }else{
                                            $po =  0;
                                    } 
                                    $price = $po;
                                }else{
                                    $rod = \App\Variation::where("product_variation_id",$row->id)->first();
                                    if(!empty($rod)){
                                        $po = round($rod->default_sell_price,config("constants.currency_precision"));
                                    }else{
                                        $po =  0;
                                    } 
                                    $price = $po;
                                }
                                $total_final =  $price * $total;
                                return round($total_final,config("constants.currency_precision")); 
                            })
                            ->addColumn("should_received",function($row){
                                $rec_v =  \App\Product::between_purchase_recieve($row->id);
                                return round($rec_v,config("constants.currency_precision")) ;
                            })
                            ->addColumn("should_delivered",function($row){
                                $del_v =  \App\Product::between_sell_deliver($row->id);
                                return round($del_v,config("constants.currency_precision")) ;
                            })
                            ->rawColumns(["image","code","name","qty","actual_qty","over_qty","unit_price","total_price","should_received","should_delivered"])
                            ->make(true);

        }


   }
   
        public function export(Request $request){

        $data=Product::get();


        return \Maatwebsite\Excel\Facades\Excel::download($data,'data.xlsx');




     }

 }
