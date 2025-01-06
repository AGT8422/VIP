<?php

namespace App\Http\Controllers;

use App\Imports\ProductImage;

use Maatwebsite\Excel\Facades\Excel;
use App\Brands;
use App\Business;
use App\BusinessLocation;
use App\Category;
use App\Currency;
use App\Media;
use App\Contact;
use App\price_currencies;
use App\Product;
use App\Models\ProductPrice;
use App\product_barcode;
use App\CustomerGroup;
use App\ProductVariation;
use App\PurchaseLine;
use App\SellingPriceGroup;
use App\TaxRate;
use App\Unit;
use App\Models\Warehouse;     
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\ProductUtil;
use App\Utils\BusinessUtil;
use App\Variation;
use App\Models\TransactionDelivery;
use App\TransactionSellLine;
use App\Models\WarehouseInfo;
use App\Models\TransactionRecieved;
use App\Models\RecievedPrevious;
use App\Models\DeliveredPrevious;
use App\Models\RecievedWrong;
use App\Models\OpeningQuantity;
use App\Transaction;
use App\VariationGroupPrice;
use App\VariationLocationDetails;
use App\VariationTemplate;
use App\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Stripe\Checkout\Session;
use Stripe\File;
use Yajra\DataTables\Facades\DataTables;
use App\MovementWarehouse;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExceptionOccured;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Config;


class ProductController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $businessUtil;

    private $barcode_types;

    /**
     * Constructor
     * 
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil, BusinessUtil $businessUtil , TransactionUtil $transactionUtil)
    {
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->dummyPaymentLine = ['method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'bank_account_number' => '',
        'is_return' => 0, 'transaction_no' => ''];
        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ((!auth()->user()->can('product.view') && !auth()->user()->can('product.create') )) {
            abort(403, 'Unauthorized action.');
        }
        $is_admin    = auth()->user()->hasRole('Admin#' . session('business.id')) ? true : false;
        $business_id = request()->session()->get('user.business_id');
        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        
        $list_product = [];
        $sub_cat = []; 

        $product_l = Product::where("business_id",$business_id)->get();
        
        $sub_categories_ = Category::where('business_id', $business_id)
                ->where('parent_id',"!=",0)->get();
                
        if (request()->ajax()) {
            $query = Product::with(['media'])->where(function($query){
                    if (app('request')->input('sub_category_id') > 0) {
                        $query->where('sub_category_id',app('request')->input('sub_category_id'));
                    }
                })
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
            $product_name = request()->get('product_name', null);

            $permitted_locations = auth()->user()->permitted_locations();

            $default_selling_price=request()->get('default_selling_price')?request()->get('default_selling_price'):0;
            if( $default_selling_price>0)
                $query->where('v.sell_price_inc_tax', '=', $default_selling_price);


            if (!empty($location_id) && $location_id != 'none') {
                if ($permitted_locations == 'all' || in_array($location_id, $permitted_locations)) {
                    $query->whereHas('product_locations', function ($query) use ($location_id) {
                        $query->where('product_locations.location_id', '=', $location_id);
                    });
                }
            } elseif ($location_id == 'none') {
                $query->doesntHave('product_locations');
            }  else {

                if ($permitted_locations != 'all') {
                    $query->whereHas('product_locations', function ($query) use ($permitted_locations) {
                        $query->whereIn('product_locations.location_id', $permitted_locations);
                    });
                } else {
                    $query->with('product_locations');
                }
            }  
            
           
           

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
                'products.feature',
                'products.product_description',
                'products.ecommerce',
                'products.not_for_selling',
                'products.product_custom_field1',
                'products.product_custom_field2',
                'products.product_custom_field3',
                'products.product_custom_field4',
                DB::raw('SUM(vld.qty_available) as current_stock'),
                DB::raw('MAX(v.sell_price_inc_tax) as max_price'),
                DB::raw('MIN(v.sell_price_inc_tax) as min_price'),
                DB::raw('MAX(v.default_sell_price) as max_price_Exc'),
                DB::raw('MIN(v.default_sell_price) as min_price_Exc'),
                DB::raw('MAX(v.default_purchase_price) as max_purchase_price'),
                DB::raw('MIN(v.default_purchase_price) as min_purchase_price')

                )->groupBy('products.id');

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
            if ($product_name != null) {
                $products->where('products.id' , $product_name);
            }
            $sku_product = request()->get('product_sku', null);
            if ($sku_product != null) {
                $products->where('products.sku' , $sku_product);
            }

           $current_stock = request()->get('current_stock', null);
   
            if ($current_stock=='zero') {
                $all                 = [];
                $id_pro              = [];
                $id_product          = [];
                $id_morethan_product = [];
                $id_lessthan_product = [];
                $all_zero            = \App\Models\WarehouseInfo::select(DB::raw('SUM(product_qty) as all_qty'),'product_id')->having('all_qty',"0")->groupBy("product_id")->get();
                $all_morethan_zero   = \App\Models\WarehouseInfo::select(DB::raw('SUM(product_qty) as all_qty'),'product_id')->having('all_qty',">","0")->groupBy("product_id")->get();
                $all_lessthan_zero   = \App\Models\WarehouseInfo::select(DB::raw('SUM(product_qty) as all_qty'),'product_id')->having('all_qty',"<","0")->groupBy("product_id")->get();
                foreach($all_zero as $z){
                    $id_product[]        = $z->product_id; 
                }
                foreach($all_morethan_zero as $z){ 
                    $id_morethan_product[] = $z->product_id;
                }
                foreach($all_lessthan_zero as $z){ 
                    $id_lessthan_product[] = $z->product_id;
                }
                $product_not_in      = \App\Product::whereNotIn("id",$id_product)->whereNotIn("id",$id_morethan_product)->whereNotIn("id",$id_lessthan_product)->get();
                foreach($product_not_in as $z){ 
                    $id_pro[] = $z->id;
                }
                foreach($all_zero as $z){
                    $id_pro[]        = $z->product_id; 
                }
                $products->whereIn('products.id', $id_pro);
                 
            }
             if ($current_stock=='gtzero') {
                $products->leftJoin('warehouse_infos as wi', 'wi.product_id', '=', 'products.id');
                $products->addSelect( DB::raw('SUM(wi.product_qty) as qty_all'));
                $products->having('qty_all','>', 0);
            }
             if ($current_stock=='lszero') { 
                $products->leftJoin('warehouse_infos as wi', 'wi.product_id', '=', 'products.id');
                $products->addSelect( DB::raw('SUM(wi.product_qty) as qty_all'));
                $products->having('qty_all','<', 0);
            }

            $category_id = request()->get('category_id', null);
            if (!empty($category_id)) {
                $products->where('products.category_id', $category_id);
            }
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
            //** eb >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> if you nead read from main stock
            // '@if($enable_stock == 1) {{@number_format($current_stock)}} @else -- @endif {{$unit}}' 
            //........................................
            $products->whereNull("products.deleted_at");
            
            return Datatables::of($products)
                ->addColumn(
                    'product_locations',
                    function ($row) {
                        return $row->product_locations->implode('name', ', ');
                    }
                )
                ->editColumn('category', '{{$category}} @if(!empty($sub_category))<br/> -- {{$sub_category}}@endif')
                ->addColumn(
                    'action',
                    function ($row) use ($selling_price_group_count,$is_admin) {
                        $id = $row->id;
                        $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                         
                        // if (auth()->user()->can('product.view')   ) {
                        //     $html .=
                        //     '<li><a href="' . action('LabelsController@show') . '?product_id=' . $row->id . '" data-toggle="tooltip" title="' . __('lang_v1.label_help') . '"><i class="fa fa-barcode"></i> ' . __('barcode.labels') . '</a></li>';
                        // }
                        if (auth()->user()->can('product.view')) {
                            $html .=
                            '<li><a href="' . action('ProductController@view', [$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        }

                        if (auth()->user()->can('product.update')) {
                            $html .=
                            '<li><a href="' . action('ProductController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        
                        if ( $is_admin || auth()->user()->can('product.delete')) {
                            $html .=
                            '<li><a href="' . action('ProductController@destroy', [$row->id]) . '" class="delete-product"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        if ( $is_admin || auth()->user()->can('delete_product_image')  ) {
                            $html .=
                            '<li><a data-id="'. $row->id .'"  class="delete_product_image" ><i class="fa fas fa-trash" style="color: red;cursor:pointer"></i> Delete Image </a></li>';
                        }
                        if (auth()->user()->can('product.update') ) {
                            $html .=
                                '<li><a href="' . action('ProductController@addbarcode', [$row->id]) . '" ><i class="fa fa-plus-circle"></i> ' . __("messages.morebarcode") . '</a></li>';
                        }
                        // if (auth()->user()->can('product.productMovement')) {
                        //     $html .=
                        //     '<li><a href="' . action('ProductController@movement', [$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("messages.movement") . '</a></li>';
                        // }

                        if ($row->is_inactive == 1) {
                            $html .=
                            '<li><a href="' . action('ProductController@activate', [$row->id]) . '" class="activate-product"><i class="fas fa-check-circle"></i> ' . __("lang_v1.reactivate") . '</a></li>';
                        }

                        $html .= '<li class="divider"></li>';

                        if ($row->enable_stock == 1 && auth()->user()->can('product.opening_stock') ) {
                            $html .=
                            '<li><a href="#" data-href="' . action('OpeningStockController@add', ['product_id' => $row->id]) . '" class="add-opening-stock"><i class="fa fa-database"></i> ' . __("lang_v1.add_edit_opening_stock") . '</a></li>';
                        }
                        if (auth()->user()->can('product.avarage_cost')  ) {
                            // $html .=
                            // '<li><a href="' . action('ProductController@productStockHistory', [$row->id]) . '"><i class="fas fa-history"></i> ' . __("lang_v1.product_stock_history") . '</a></li>';
                            $html .=
                            '<li><a href="' . action('ItemMoveController@index', [$row->id]) . '"><i class="fas fa-history"></i> ' . __("lang_v1.product_stock_history") . '</a></li>';
                        }
                        
                        if ($is_admin) {
                            if ($selling_price_group_count > 0) {
                                $html .=
                                '<li><a href="' . action('ProductController@addSellingPrices', [$row->id]) . '"><i class="fas fa-money-bill-alt"></i> ' . __("lang_v1.add_selling_price_group_prices") . '</a></li>';
                            }
                        }
                        if (auth()->user()->can('product.create')) {

                            $html .=
                                '<li><a href="' . action('ProductController@create', ["d" => $row->id]) . '"><i class="fa fa-copy"></i> ' . __("lang_v1.duplicate_product") . '</a></li>';
                        }
                        
                        if (!empty($row->media->first())) {

                            $html .=
                                '<li><a href="' . $row->media->first()->display_url . '" download="'.$row->media->first()->display_name.'"><i class="fas fa-download"></i> ' . __("lang_v1.product_brochure") . '</a></li>';
                        }

                            // $html .=
                            //     '<li><a data-href="' . \URL::to("/products/changeFeature?id=$id") . '" class="btn-modal"><i class="fa fa-eye"></i> ' . __("View In Ecommerce") . '</a></li>';
                            // $html .=
                            //     '<li><a data-href="' . \URL::to("/products/unChangeFeature?id=$id") . '" class="btn-modal"><i class="fa fa-trash"></i> ' . __("Remove From Ecommerce") . '</a></li>';
                        $html .= '</ul></div>';
                        
                        return $html;
                    }
                )
                ->editColumn('product', function ($row) {
                    $product = $row->is_inactive     == 1 ? $row->product . ' <span class="label bg-gray">' . __("lang_v1.inactive") .'</span>' : $row->product;
                    $product = $row->not_for_selling == 1 ? $product . ' <span class="label bg-gray">' . __("lang_v1.not_for_selling") .
                        '</span>' : $product;
                        
                    return $product;
                })
                ->editColumn('image', function ($row) {
                     
                    
                    if($row->image_url){
                            $image = $row->image_url;
                    }else {
                            $image = "";
                    
                        
                    }
                    return '<div style="display: flex;"><img src="' .  $image . '" alt="Product image"  width="150px" height="150px"></div>';
                })
                ->editColumn('type', '@lang("lang_v1." . $type)')
                ->addColumn('mass_delete', function ($row) {
                    return  '<input type="checkbox" class="row-select"  value="' . $row->id .'">' ;
                })
                ->addColumn('mass_deletes', function ($row) {
                                $rr = ( $row->feature != 0)? "checked='checked'" : '1' ;
                    return  '<input type="checkbox"  class="e-commerce" ' . $rr . ' value="' . $row->id .'">' ;
                })
                ->editColumn('current_stock', 
                    function($row){ 
                        if ($row->enable_stock) {
                            $warehouse = WarehouseInfo::where("product_id",$row->id)
                                                        ->select(DB::raw("SUM(product_qty) as stock"))->first();
                            $stock = $row->stock ? $row->stock : 0 ;

                            $html = '<span style="white-space: nowrap;" data-is_quantity="true" class="current_stock display_currency" data-orig-value="' . (float)$warehouse->stock . '" data-unit="' . $row->unit . '" data-currency_symbol=false > ' . (float)$warehouse->stock . '</span>' . ' ' . $row->unit ;
                            if(  $warehouse->stock != null ){
                                $html .= ' <button type="button" class="btn btn-primary btn-xs btn-modal no-print" id="view_s" data-container=".view_modal" data-href="' . action('ProductController@viewStock', [$row->id]) .'">' . __('lang_v1.view_Stock') . '</button>';
                            }
                            return  $html;
                        } else {
                            return '--';
                        }
                    }

               
                )
                ->addColumn('purchase_price', function($row){
                        $html = \App\Product::product_cost($row->id);
                        if(auth()->user()->can('product.avarage_cost')){
                              return   number_format($html,2)  ;
                        }else{
                            return  '---------' ;
                        }
                        
                    }
                 )
                ->addColumn(
                    'selling_price_Exc',
                    '<div style="white-space: nowrap;">@format_currency($min_price_Exc) @if($max_price != $min_price_Exc && $type == "variable") -  @format_currency($max_price_Exc)@endif </div>'
                )
                ->editColumn(
                    'product_description',
                    '<div style="white-space: nowrap;">{!! $product_description !!}</div>')
                ->addColumn('product',
                    function($row){
                        $check =( $row->ecommerce != 0)? "**" : '' ;
                     return $row->product  ;
                    }
                )
                ->addColumn(
                    'selling_price',
                    '<div style="white-space: nowrap;">@format_currency($min_price) @if($max_price != $min_price && $type == "variable") -  @format_currency($max_price)@endif </div>'
                )
                ->filterColumn('products.sku', function ($query, $keyword) {
                    $query->whereHas('variations', function($q) use($keyword){
                            $q->where('sub_sku', 'like', "%{$keyword}%");
                        })
                    ->orWhere('products.sku', 'like', "%{$keyword}%");
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("product.view")) {
                            return  action('ProductController@view', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }])
                ->rawColumns(['action',"mass_deletes",'image', 'product_description' , 'mass_delete','current_stock' , "selling_price_Exc", 'product', 'selling_price', 'purchase_price', 'category'])
                ->make(true);

        }
        

        $rack_enabled   = (request()->session()->get('business.enable_racks') || request()->session()->get('business.enable_row') || request()->session()->get('business.enable_position'));
        $categories     = Category::forDropdown($business_id, 'product');
        $brands         = Brands::forDropdown($business_id);
        $units          = Unit::forDropdown($business_id);
        $tax_dropdown   = TaxRate::forBusinessDropdown($business_id, false);
        $taxes          = $tax_dropdown['tax_rates'];
        $products_code  = [];
        $products_codes = Product::select('id','sku')->get();

        $business_locations = BusinessLocation::forDropdown($business_id);
        $business_locations->prepend(__('lang_v1.none'), 'none');

        if ($this->moduleUtil->isModuleInstalled('Manufacturing') && (auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module'))) {
            $show_manufacturing_data = true;
        } else {
            $show_manufacturing_data = false;
        }

        foreach($products_codes as $key => $value){
            $products_code[$value->sku] = $value->sku; 
        }
        foreach($product_l as $key => $value){
            $list_product[$value->id] = $value->name . " || " . $value->sku; 
        }
        foreach($sub_categories_ as $key => $value){
            $sub_cat[$value->id] = $value->name;
        }

        //list product screen filter from module
        $pos_module_data = $this->moduleUtil->getModuleData('get_filters_for_list_product_screen');
        $is_woocommerce  = $this->moduleUtil->isModuleInstalled('Woocommerce');
        $o_sub           = Category::find(app('request')->input('sub_category_id'));
        $o_title         =  ($o_sub)?$o_sub->name:trans('home.All Products');
        
        return view('product.index')
            ->with(compact(
                'rack_enabled',
                'list_product',
                'categories',
                'sub_cat',
                'products_code',
                'currency_details',
                'brands',
                'units',
                'taxes',
                'business_locations',
                'show_manufacturing_data',
                'pos_module_data',
                'is_woocommerce',
                'o_title'
            ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
 
        if (!auth()->user()->can('product.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for products quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse();
            }elseif (!$this->moduleUtil->isQuotaAvailable('products', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action('ProductController@index'));
            }
        } elseif (!$this->moduleUtil->isQuotaAvailable('products', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action('ProductController@index'));
        }
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $categories    = Category::forDropdown($business_id, 'product');
        $currency      = \App\Currency::select("*")->get(); 
        $exchange_rate = \App\Models\ExchangeRate::select("*")->where("source",0)->get(); 
        $currencies    = [];
        foreach($currency as $it){
            foreach($exchange_rate as $i){
                if($i->currency_id == $it->id){
                    $currencies[$it->id] =  $it->currency . " - " . $it->code . " - " . $it->symbol;
                }
            }
        }
        $brands  = Brands::forDropdown($business_id);
        $units   = Unit::forDropdown($business_id, true);
        $unitsm  = Unit::forDropdown($business_id, false);
        $unitm = \App\Unit::where("default",1)->first();
        $tax_dropdown   = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes          = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];

        $barcode_types   =  $this->barcode_types;
        $barcode_default =  $this->productUtil->barcode_default();

        $default_profit_percent = request()->session()->get('business.default_profit_percent');;

        //Get all business locations
        $business_locations = BusinessLocation::forDropdown($business_id);

        //Duplicate product
        $duplicate_product = null;
        $rack_details      = null;
        
        $sub_categories = [];
        if (!empty(request()->input('d'))) {
            $duplicate_product = Product::where('business_id', $business_id)->find(request()->input('d'));
            $duplicate_product->name .= ' (copy)';

            if (!empty($duplicate_product->category_id)) {
                $sub_categories = Category::where('business_id', $business_id)
                        ->where('parent_id', $duplicate_product->category_id)
                        ->pluck('name', 'id')
                        ->toArray();
            }

            //Rack details
            if (!empty($duplicate_product->id)) {
                $rack_details = $this->productUtil->getRackDetails($business_id, $duplicate_product->id);
            }
        }

        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);

        $module_form_parts = $this->moduleUtil->getModuleData('product_form_part');
        $product_types     = $this->product_types();

        $common_settings   = session()->get('business.common_settings');

        $warranties        = Warranty::forDropdown($business_id);
        $product_price     = ProductPrice::where("business_id",$business_id)->get();

        //product screen view from module
        $pos_module_data   = $this->moduleUtil->getModuleData('get_product_screen_top_view');
        $counter           = 0;
        $array             = [];
      
         
        $array_unit    = [];
        $product_price = ProductPrice::where("business_id",$business_id)->get();
        $units_main    = Unit::forDropdown($business_id, false);
        $units         = Unit::forDropdown($business_id, true);
        $unitsP        = Unit::forDropdownInPrice($business_id);
        foreach($unitsP as $key => $value){ $array_unit[]   = [$key => $value]; }

         $array_unit = $units_main;
        //product screen view from module
        $pos_module_data = $this->moduleUtil->getModuleData('get_product_screen_top_view');
         return view('product.create')
            ->with(compact('categories','unitm','currencies','currency','currency_details','units_main','array_unit','product_price',  'brands', 'units', 'taxes', 'barcode_types', 'default_profit_percent', 'tax_attributes', 'barcode_default', 'business_locations', 'duplicate_product', 'sub_categories', 'rack_details', 'selling_price_group_count', 'module_form_parts', 'product_types', 'common_settings', 'warranties', 'pos_module_data'));
    }

    private function product_types()
    {
        //Product types also includes modifier.
        return ['single' => __('lang_v1.single'),
                'variable' => __('lang_v1.variable'),
                'combo' => __('lang_v1.combo')
            ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('product.create')  ) {
            abort(403, 'Unauthorized action.');
        }
        try {
          
            $business_id = $request->session()->get('user.business_id');
            $form_fields = ['name', 'brand_id', 'unit_id', 'category_id','full_description', 'tax', 'type', 'barcode_type', 'sku','sku2', 'alert_quantity', 'tax_type', 'weight', 'product_custom_field1', 'product_custom_field2', 'product_custom_field3', 'product_custom_field4', 'product_description', 'sub_unit_ids'];

            $module_form_fields = $this->moduleUtil->getModuleFormField('product_form_fields');
            if (!empty($module_form_fields)) {
                $form_fields = array_merge($form_fields, $module_form_fields);
            }

            $product_details = $request->only($form_fields);
            $product_details['full_description'] = (isset($request->full_description))?$request->full_description:null;
            $product_details['business_id'] = $business_id;
            $product_details['created_by']  = $request->session()->get('user.id');

            $product_details['enable_stock'] = (!empty($request->input('enable_stock')) &&  $request->input('enable_stock') == 1) ? 1 : 0;
            $product_details['not_for_selling'] = (!empty($request->input('not_for_selling')) &&  $request->input('not_for_selling') == 1) ? 1 : 0;

            if (!empty($request->input('sub_category_id'))) {
                $product_details['sub_category_id'] = $request->input('sub_category_id') ;
            }

            if (empty($product_details['sku'])) {
                $product_details['sku'] = ' ';
            }
            $expiry_enabled = $request->session()->get('business.enable_product_expiry');
            if (!empty($request->input('expiry_period_type')) && !empty($request->input('expiry_period')) && !empty($expiry_enabled) && ($product_details['enable_stock'] == 1)) {
                $product_details['expiry_period_type'] = $request->input('expiry_period_type');
                $product_details['expiry_period'] = $this->productUtil->num_uf($request->input('expiry_period'));
            }

            if (!empty($request->input('enable_sr_no')) &&  $request->input('enable_sr_no') == 1) {
                $product_details['enable_sr_no'] = 1 ;
            }

            //upload document
            $product_details['image'] = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image');

            $common_settings = session()->get('business.common_settings');

            $product_details['warranty_id'] = !empty($request->input('warranty_id')) ? $request->input('warranty_id') : null;

            DB::beginTransaction();
            $company_name = request()->session()->get("user_main.domain");
            if($request->hasFile('vedio')){
                // save step up
                $request->validate([
                    'vedio'  => 'required|mimes:mp4,mov,avi|max:25480', // Max 25MB
                ]);
                $array_vedio = [];
                $path_name   =  \time() . $request->file('vedio')->getClientOriginalName();
                if ($request->hasFile('vedio')) {
                    $video         = $request->file('vedio');
                    $path          = $video->store('/../public/uploads/companies/'.$company_name.'/video','public');
                    $array_vedio[] = $path;
                    // For example, you can store the path and other details in the 'videos' table.
                    // You can also store the video information in your database if needed.
                }
                $product_details["product_vedio"] =  json_encode($array_vedio) ;
            }
                 
            $product = Product::create($product_details);
                
            // ... Save Additional Price's
            // foreach($request->name_add as $key => $it){
          
            //     if($it != null){
            //         $list_currency   = [];
            //         $product_price                  = new ProductPrice;
            //         $product_price->business_id     = $business_id;
            //         $product_price->product_id      = $product->id;
            //         $product_price->name            = $it;
            //         $product_price->price           = $request->price[$key];
            //         foreach($request->currency_price as $k => $if){
            //             foreach($if as $ky => $i){
            //                 $one_currency    = [];
            //                 if($key == $ky){
            //                     $one_currency[$request->currency_amount_price[$k][$ky]]=$i;
            //                     $list_currency[]  = $one_currency;
            //                 }
            //             }
            //         }
            //         $product_price->list_of_price   = json_encode($list_currency)  ;
            //         $product_price->default_name    = ($key<4)?1:null;
            //         switch ($key) {
            //             case 0:
            //                 $val = 1;
            //                 break;
            //             case 1:
            //                 $val = 2;
            //                 break;
            //             case 2:
            //                 $val = 3;
            //                 break;
            //             case 3:
            //                 $val = 4;
            //                 break;
            //             default:
            //                 $val = null;
            //         }
            //         $product_price->number_of_default    = $val;
            //         $product_price->date   = \Carbon::now()->format("Y-m-d");
            //         $product_price->save();
            //     }
            // }
            if($product->type == 'single'){
                if(isset($request->unit_D)){
                    foreach($request->unit_D as $kd => $value){
                        if($kd == 0){
                            $request_single_dpp         = $request->single_dpp1;
                            $request_single_dsp_inc_tax = $request->single_dsp_inc_tax1 ;
                            $request_single_dpp_inc_tax = $request->single_dpp_inc_tax1;
                            $request_profit_percent     = $request->profit_percent1;
                            $request_single_dsp         = $request->single_dsp1 ;
                        }elseif($kd == 1){
                            $request_single_dpp         = $request->single_dpp2;
                            $request_single_dsp_inc_tax = $request->single_dsp_inc_tax2 ;
                            $request_single_dpp_inc_tax = $request->single_dpp_inc_tax2;
                            $request_profit_percent     = $request->profit_percent2;
                            $request_single_dsp         = $request->single_dsp2 ;
                        }else{
                            $request_single_dpp         = $request->single_dpp3;
                            $request_single_dsp_inc_tax = $request->single_dsp_inc_tax3 ;
                            $request_single_dpp_inc_tax = $request->single_dpp_inc_tax3;
                            $request_profit_percent     = $request->profit_percent3;
                            $request_single_dsp         = $request->single_dsp3 ;
                        }
                        foreach($request_single_dpp as $k => $values){
                            $product_id  =  ProductPrice::where("product_id",$product->id)
                                                                    ->whereNull("default_name")
                                                                    ->where("number_of_default",$k)
                                                                    ->where("unit_id",$value)
                                                                    ->first();
                            switch ($k) {
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
                            if(empty($product_id)){
                                $product_id_ptice                      =  new ProductPrice();
                                $product_id_ptice->product_id          =  $product->id ;   
                                $product_id_ptice->business_id         =  $business_id ;   
                                $product_id_ptice->name                =  $val ;   
                                $product_id_ptice->default_purchase_price =    $this->productUtil->num_uf($values)  ;   
                                $product_id_ptice->dpp_inc_tax         =  $this->productUtil->num_uf($request_single_dpp_inc_tax[$k])    ;   
                                $product_id_ptice->profit_percent      =  $this->productUtil->num_uf($request_profit_percent[$k])   ;   
                                $product_id_ptice->default_sell_price  =  $this->productUtil->num_uf($request_single_dsp[$k])   ;   
                                $product_id_ptice->sell_price_inc_tax  =  $this->productUtil->num_uf($request_single_dsp_inc_tax[$k])   ;   
                                $product_id_ptice->number_of_default   =  $k ;     
                                $product_id_ptice->unit_id             =  $value ;
                                $product_id_ptice->save();
                            }else{
                                $product_id->name                      =  $val      ;   
                                $product_id->default_purchase_price    =  $this->productUtil->num_uf($values)   ;   
                                $product_id->dpp_inc_tax               =  $this->productUtil->num_uf($request_single_dpp_inc_tax[$k])      ;  
                                $product_id->profit_percent            =  $this->productUtil->num_uf($request_profit_percent[$k])      ;    
                                $product_id->default_sell_price        =  $this->productUtil->num_uf($request_single_dsp[$k])      ;   
                                $product_id->sell_price_inc_tax        =  $this->productUtil->num_uf($request_single_dsp_inc_tax[$k])     ;   
                                $product_id->unit_id                   =  $value    ;   
                                $product_id->update();
                            }          
                        }
                    }
                }       
            }
                 
            if (empty(trim($request->input('sku')))) {
                $sku = $this->productUtil->generateProductSku($product->id);
                $product->sku = $sku;
                $product->save();
            }

            //Add product locations
            $product_locations = $request->input('product_locations');
            if (!empty($product_locations)) {
                $product->product_locations()->sync($product_locations);
            }
            
            if ($product->type == 'single') {
                if($request->input('single_dpp1')!= null){
                    $this->productUtil->createSingleProductVariationPrices($product->id, $product->sku, $request->input('single_dpp1'), $request->input('single_dpp_inc_tax1'), $request->input('profit_percent1'), $request->input('single_dsp1'), $request->input('single_dsp_inc_tax1'),$product->sku2,1,$request->input('unit1'));
                }
                if($request->input('single_dpp2') != null){
                    $this->productUtil->createSingleProductVariationPrices($product->id, $product->sku, $request->input('single_dpp2'), $request->input('single_dpp_inc_tax2'), $request->input('profit_percent2'), $request->input('single_dsp2'), $request->input('single_dsp_inc_tax2'),$product->sku2,null,$request->input('unit2'));
                }
                if($request->input('single_dpp3')!= null){
                    $this->productUtil->createSingleProductVariationPrices($product->id, $product->sku, $request->input('single_dpp3'), $request->input('single_dpp_inc_tax3'), $request->input('profit_percent3'), $request->input('single_dsp3'), $request->input('single_dsp_inc_tax3'),$product->sku2,null,$request->input('unit3'));
                }
            } elseif ($product->type == 'variable') {
                if (!empty($request->input('product_variation'))) {
                    $input_variations = $request->input('product_variation');
                    $this->productUtil->createVariableProductVariations($product->id, $input_variations);
                }
            } elseif ($product->type == 'combo') {
                //Create combo_variations array by combining variation_id and quantity.
                $combo_variations = [];
                if (!empty($request->input('composition_variation_id'))) {
                    $composition_variation_id = $request->input('composition_variation_id');
                    $quantity = $request->input('quantity');
                    $unit = $request->input('unit');

                    foreach ($composition_variation_id as $key => $value) {
                        $combo_variations[] = [
                                'variation_id'  => $value,
                                'quantity'      => $this->productUtil->num_uf($quantity[$key]),
                                'unit_id'       => $unit[$key]
                            ];
                    }
                 
                }

                 $this->productUtil->createSingleProductVariation($product->id, $product->sku, $request->input('item_level_purchase_price_total'), $request->input('purchase_price_inc_tax'), $request->input('profit_percent'), $request->input('selling_price'), $request->input('selling_price_inc_tax'), $combo_variations) ;
                
                
            }

            //Add product racks details.
            $product_racks = $request->get('product_racks', null);
            if (!empty($product_racks)) {
                $this->productUtil->addRackDetails($business_id, $product->id, $product_racks);
            }

            //Set Module fields
            if (!empty($request->input('has_module_data'))) {
                $this->moduleUtil->getModuleData('after_product_saved', ['product' => $product, 'request' => $request]);
            }

            Media::uploadMedia($product->business_id, $product, $request, 'product_brochure', true);

             
            /*
             * add more barcode edit eng mohmaed ali
             */
             /*$barcode=product_barcode::create([
                'product_id'=>$product->id,
                'business_id'=>$business_id,
                'barcode'=>$product->sku,
                'type'=>$product->barcode_type,
                'ismain'=>1,
             ]);*/

            foreach($request->input('product_locations') as $key => $first){
                            $location_id  = $first;
            }
                      
            // if($request->prices){
            //         \App\Models\ProductPrice::savePrices($request->prices);
            // }
            
            $id_product   = Product::orderby("id","desc")->where("business_id",$business_id)->first();
            $id_variation = Variation::orderby("id","desc")->first();
            if($id_variation->id !== null){
                $variation =  $id_variation->id ;
                $pid =  $id_product->id ;
            }else{
                $pid = 1 ;
                $variation = 1 ;
            }

            
            $variation_location_d = new VariationLocationDetails();
            $variation_location_d->variation_id = $variation;
            $variation_location_d->product_id = $pid;
            $variation_location_d->location_id = $location_id;
            $variation_location_d->product_variation_id = $variation;
            $variation_location_d->qty_available = 0;
            $variation_location_d->save();

            
            // .......... more than unit ...
            if(isset($request->actual_name)){
                $data_unit = $request->only(["actual_name","price_unit","short_name","allow_decimal","base_unit_id","base_unit_multiplier"]);
                Product::moreUnit($data_unit,$product);
            }


            DB::commit();
            $output = ['success' => 1,
                            'msg' => __('product.product_added_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            // 'msg' => __("messages.something_went_wrong")
                            'msg' => $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
            return redirect('products')->with('status', $output);
        }

        if ($request->input('submit_type') == 'submit_n_add_opening_stock') {
            return redirect()->action('ProductController@AddOpeningProduct'
            );
        } elseif ($request->input('submit_type') == 'submit_n_add_selling_prices') {
            return redirect()->action('ProductController@addSellingPrices',[$product->id]
            );
        } elseif ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action('ProductController@create')->with('status', $output);
        }

        return redirect('products')->with('status', $output);
    }
     // .................
    public function add_category(Request $request)
    {
        $category_type = request()->get('type');
        // if ($category_type == 'product' && !auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
        //     abort(403, 'Unauthorized action.');
        // }
        // $business_id = request()->session()->get('user.business_id');

        // $module_category_data = $this->moduleUtil->getTaxonomyData($category_type);

        // $categories = Category::where('business_id', $business_id)
        //                 ->where('parent_id', 0)
        //                 ->where('category_type', $category_type)
        //                 ->select(['name', 'short_code', 'id'])
        //                 ->get();

        // $parent_categories = [];
        // if (!empty($categories)) {
        //     foreach ($categories as $category) {
        //         $parent_categories[$category->id] = $category->name;
        //     }
        // }

        return view('taxonomy.create');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         
        if (!auth()->user()->can('product.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $details = $this->productUtil->getRackDetails($business_id, $id, true);

        return view('product.show')->with(compact('details'));
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show_Global($id)
    {
        if (!auth()->user()->can('product.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $details = $this->productUtil->getRackDetails($business_id, $id, true);

        return view('product.show_Global')->with(compact('details'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id   = request()->session()->get('user.business_id');
        $categories    = Category::forDropdown($business_id, 'product');
        $brands        = Brands::forDropdown($business_id);
        $currency      = \App\Currency::select("*")->get(); 
        $exchange_rate = \App\Models\ExchangeRate::select("*")->where("source",0)->get(); 
        $currencies    = [];
        foreach($currency as $it){
            foreach($exchange_rate as $i){
                if($i->currency_id == $it->id){
                    $currencies[$it->id] =  $it->currency . " - " . $it->code . " - " . $it->symbol;
                }
            }
        }
        $product_deatails_parent = ProductVariation::where('product_id', $id)
                                                    ->with(['variations', 'variations.media'])
                                                    ->first();
        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];

        $barcode_types = $this->barcode_types;

        $product = Product::where('business_id', $business_id)
                            ->with(['product_locations'])
                            ->where('id', $id)
                            ->firstOrFail();

        //Sub-category
        $sub_categories = [];
        $sub_categories = Category::where('business_id', $business_id)
                        ->where('parent_id', $product->category_id)
                        ->pluck('name', 'id')
                        ->toArray();
        $sub_categories = [ "" => "None"] + $sub_categories;

        $default_profit_percent = request()->session()->get('business.default_profit_percent');

        //Get units.
        $units   = Unit::forDropdown($business_id, true);
        $unitsP  = Unit::forDropdownInPrice($business_id);
        $unitall = Unit::where("business_id",$business_id)->get();

        $sub_units = $this->productUtil->getSubUnits($business_id, $product->unit_id, true);
        
        //Get all business locations
        $business_locations = BusinessLocation::forDropdown($business_id);
        //Rack details
        $rack_details = $this->productUtil->getRackDetails($business_id, $id);

        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);

        $module_form_parts = $this->moduleUtil->getModuleData('product_form_part');
        $product_types = $this->product_types();
        $common_settings = session()->get('business.common_settings');
        $warranties = Warranty::forDropdown($business_id);
        $product_price = \App\Models\ProductPrice::where("business_id",$business_id)->where("product_id",$id)->get();
    
        // dd( $product_price);
        //product screen view from module
        $pos_module_data = $this->moduleUtil->getModuleData('get_product_screen_top_view');
        $counter = 0;
        $array   = [];
         
        return view('product.edit')->with(compact('categories','product_deatails_parent' ,'unitsP','counter','unitall','currencies','currency','product_price',   'brands', 'units', 'sub_units', 'taxes', 'tax_attributes', 'barcode_types', 'product', 'sub_categories', 'default_profit_percent', 'business_locations', 'rack_details', 'selling_price_group_count', 'module_form_parts', 'product_types', 'common_settings', 'warranties', 'pos_module_data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }
     
        try {
            $business_id     = $request->session()->get('user.business_id');
            $product_details = $request->only(['name', 'brand_id', 'unit_id', 'category_id', 'tax', 'barcode_type', 'sku','sku2','alert_quantity', 'tax_type', 'weight', 'product_custom_field1', 'product_custom_field2', 'product_custom_field3', 'product_custom_field4', 'product_description', 'sub_unit_ids']);

            DB::beginTransaction();

            $product         = Product::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(['product_variations'])
                                ->first();

            $module_form_fields = $this->moduleUtil->getModuleFormField('product_form_fields');
            if (!empty($module_form_fields)) {
                foreach ($module_form_fields as $column) {
                    $product->$column = $request->input($column);
                }
            }

            $product->name                  = $product_details['name'];
            $product->brand_id              = $product_details['brand_id'];
            $product->unit_id               = $product_details['unit_id'];
            $product->category_id           = $product_details['category_id'];
            $product->tax                   = $product_details['tax'];
            $product->barcode_type          = $product_details['barcode_type'];
            $product->sku                   = $product_details['sku'];
            $product->sku2                  = $product_details['sku2'];
            $product->alert_quantity        = $product_details['alert_quantity'];
            $product->tax_type              = $product_details['tax_type'];
            $product->weight                = $product_details['weight'];
            $product->product_custom_field1 = $product_details['product_custom_field1'];
            $product->product_custom_field2 = $product_details['product_custom_field2'];
            $product->product_custom_field3 = $product_details['product_custom_field3'];
            $product->product_custom_field4 = $product_details['product_custom_field4'];
            $product->product_description   = $product_details['product_description'];
            $product->sub_unit_ids          = !empty($product_details['sub_unit_ids']) ? $product_details['sub_unit_ids'] : null;
            $product->warranty_id           = !empty($request->input('warranty_id')) ? $request->input('warranty_id') : null;

            if (!empty($request->input('enable_stock')) &&  $request->input('enable_stock') == 1) {
                $product->enable_stock = 1;
            } else {
                $product->enable_stock = 0;
            }

            $product->not_for_selling = (!empty($request->input('not_for_selling')) &&  $request->input('not_for_selling') == 1) ? 1 : 0;

            if (!empty($request->input('sub_category_id'))) {
                $product->sub_category_id = $request->input('sub_category_id');
            } else {
                $product->sub_category_id = null;
            }

            $expiry_enabled = $request->session()->get('business.enable_product_expiry');
            if (!empty($expiry_enabled)) {
                if (!empty($request->input('expiry_period_type')) && !empty($request->input('expiry_period')) && ($product->enable_stock == 1)) {
                    $product->expiry_period_type = $request->input('expiry_period_type');
                    $product->expiry_period      = $this->productUtil->num_uf($request->input('expiry_period'));
                } else {
                    $product->expiry_period_type = null;
                    $product->expiry_period      = null;
                }
            }

            if (!empty($request->input('enable_sr_no')) &&  $request->input('enable_sr_no') == 1) {
                $product->enable_sr_no = 1;
            } else {
                $product->enable_sr_no = 0;
            }

            //upload document
            $file_name = $this->productUtil->uploadFile($request, 'image', config('constants.product_img_path'), 'image');
            if (!empty($file_name)) {

                //If previous image found then remove
                if (!empty($product->image_path) && file_exists($product->image_path)) {
                    unlink($product->image_path);
                }

                $product->image = $file_name;
                //If product image is updated update woocommerce media id
                if (!empty($product->woocommerce_media_id)) {
                    $product->woocommerce_media_id = null;
                }
            }
            if($request->hasFile('vedio')){
                // save step up
                $request->validate([
                    'vedio' => 'required|mimes:mp4,mov,avi|max:25480', // Max 25MB
                ]);
                $array_vedio = [];
                $path_name   =  \time() . $request->file('vedio')->getClientOriginalName();
                if ($request->hasFile('vedio')) {
                    $video = $request->file('vedio');
                    $path  = $video->store('vedios','public');
                    $array_vedio[] = $path;
                    // You can also store the video information in your database if needed.
                    // For example, you can store the path and other details in the 'videos' table.
                }
                $product->product_vedio =  json_encode($array_vedio) ;
            }
            $product->save();
            $product->touch();

            //Add product locations
            $product_locations = !empty($request->input('product_locations')) ?
                                $request->input('product_locations') : [];
            $product->product_locations()->sync($product_locations);

            if ($product->type == 'single') {
                $single_data                       = $request->only(['single_variation_id1', 'single_dpp1', 'single_dpp_inc_tax1', 'single_dsp_inc_tax1', 'profit_percent1', 'single_dsp1']);
                if(isset($single_data['single_variation_id1'])){
                    $variation                         = Variation::find($single_data['single_variation_id1']);
                    $variation->sub_sku                = $product->sku;
                    $variation->default_purchase_price = $this->productUtil->num_uf($single_data['single_dpp1'][0]);
                    $variation->dpp_inc_tax            = $this->productUtil->num_uf($single_data['single_dpp_inc_tax1'][0]);
                    $variation->profit_percent         = $this->productUtil->num_uf($single_data['profit_percent1'][0]);
                    $variation->default_sell_price     = $this->productUtil->num_uf($single_data['single_dsp1'][0]);
                    $variation->sell_price_inc_tax     = $this->productUtil->num_uf($single_data['single_dsp_inc_tax1'][0]);
                    $variation->save();
                    Media::uploadMedia($product->business_id, $variation, $request, 'variation_images');
                }
            } elseif ($product->type == 'variable') {
             
                
                //Update existing variations
                $input_variations_edit = $request->get('product_variation_edit');
                if (!empty($input_variations_edit)) {
                    $this->productUtil->updateVariableProductVariations($product->id, $input_variations_edit);
                }
                 
                //Add new variations created.
                $input_variations = $request->input('product_variation');
                if (!empty($input_variations)) {
                    $this->productUtil->createVariableProductVariations($product->id, $input_variations);
                }
            } elseif ($product->type == 'combo') {

                //Create combo_variations array by combining variation_id and quantity.
                $combo_variations = [];
                if (!empty($request->input('composition_variation_id'))) {
                    $composition_variation_id = $request->input('composition_variation_id');
                    $quantity = $request->input('quantity');
                    $unit = $request->input('unit');
                    $list_price = $request->input('list_price');

                    foreach ($composition_variation_id as $key => $value) {
                        $combo_variations[] = [
                                'variation_id' => $value,
                                'quantity' => $quantity[$key],
                                'unit_id' => $unit[$key],
                                'list_price' => $list_price[$key]
                            ];
                    }
                }

                $variation = Variation::find($request->input('combo_variation_id'));
                $variation->sub_sku = $product->sku;
                $variation->default_purchase_price = $this->productUtil->num_uf($request->input('item_level_purchase_price_total'));
                $variation->dpp_inc_tax = $this->productUtil->num_uf($request->input('purchase_price_inc_tax'));
                $variation->profit_percent = $this->productUtil->num_uf($request->input('profit_percent'));
                $variation->default_sell_price = $this->productUtil->num_uf($request->input('selling_price'));
                $variation->sell_price_inc_tax = $this->productUtil->num_uf($request->input('selling_price_inc_tax'));
                $variation->combo_variations = $combo_variations;
                $variation->save();
            }

            //Add product racks details.
            $product_racks = $request->get('product_racks', null);
            if (!empty($product_racks)) {
                $this->productUtil->addRackDetails($business_id, $product->id, $product_racks);
            }

            $product_racks_update = $request->get('product_racks_update', null);
            if (!empty($product_racks_update)) {
                $this->productUtil->updateRackDetails($business_id, $product->id, $product_racks_update);
            }

            //Set Module fields
            if (!empty($request->input('has_module_data'))) {
                $this->moduleUtil->getModuleData('after_product_saved', ['product' => $product, 'request' => $request]);
            }
            // if(isset($request->name_add)){
            //     // ... Update Additional Price's
            //     foreach($request->name_add as $key => $it){
            //         // ..... 0 1 2 3 4 5
                    
            //         if($it != null){
            //             $findProductLine = \App\Models\ProductPrice::find($request->line_id[$key]);
            //             if(!empty($findProductLine)){
            //                 $list_currency   = [];
            //                 $findProductLine->business_id     = $business_id;
            //                 $findProductLine->product_id      = $id;
            //                 $findProductLine->name            = $it;
            //                 $findProductLine->price           = $request->price[$key];
            //                 foreach($request->currency_price as $k => $if){
            //                     foreach($if as $ky => $i){
            //                         $one_currency    = [];
            //                         if($key == $ky){
            //                             // dd($request);
            //                             $one_currency [$request->currency_amount_price[$k][$ky]]=$i;
            //                             $list_currency[]  = $one_currency;
            //                         }
            //                     }
            //                 }
            //                 $findProductLine->list_of_price   = json_encode($list_currency)  ;
            //                 $findProductLine->date            = \Carbon::now()->format("Y-m-d");
            //                 $findProductLine->update();
            //             }else{
            //                 $list_currency   = [];
            //                 $product_price                  = new \App\Models\ProductPrice;
            //                 $product_price->business_id     = $business_id;
            //                 $product_price->product_id      = $product->id;
            //                 $product_price->name            = $it;
            //                 $product_price->price           = $request->price[$key];
            //                 foreach($request->currency_price as $k => $if){
            //                     foreach($if as $ky => $i){
            //                         $one_currency    = [];
            //                         if($key == $ky){
            //                             $one_currency[$request->currency_amount_price[$k][$ky]]=$i;
            //                             $list_currency[]  = $one_currency;
            //                         }
            //                     }
            //                 }
            //                 switch ($it) {
            //                     case "Whole Price":
            //                         $val = 1;
            //                         $def = 1;
            //                         break;
            //                     case "Retail Price":
            //                         $val = 2;
            //                         $def = 1;
            //                         break;
            //                     case "Last Price":
            //                         $val = 4;
            //                         $def = 1;
            //                         break;
            //                     case "Minimum Price":
            //                         $val = 3;
            //                         $def = 1;
            //                     break;
            //                     default:

            //                         $def = null;
            //                         $val = null;
            //                 }
            //                 $product_price->default_name         = $def;
            //                 $product_price->number_of_default    = $val;
            //                 $product_price->list_of_price   = json_encode($list_currency)  ;
            //                 $product_price->date   = \Carbon::now()->format("Y-m-d");
            //                 $product_price->save();
            //             }
            //         }
            //     }
            // }
        
            if(isset($request->unit_D)){
                $units_new    = [];
                foreach($request->unit_D as $kd => $value){
                    if($value != null){
                        if($kd == 0){
                            $request_single_dpp         = isset($request->single_dpp1)?$request->single_dpp1:[];
                            $request_single_dsp_inc_tax = isset($request->single_dsp_inc_tax1)?$request->single_dsp_inc_tax1:[] ;
                            $request_single_dpp_inc_tax = isset($request->single_dpp_inc_tax1)?$request->single_dpp_inc_tax1:[];
                            $request_profit_percent     = isset($request->profit_percent1)?$request->profit_percent1:[];
                            $request_single_dsp         = isset($request->single_dsp1)?$request->single_dsp1:[] ;
                        }elseif($kd == 1){
                            $request_single_dpp         = isset($request->single_dpp2)?$request->single_dpp2:[];
                            $request_single_dsp_inc_tax = isset($request->single_dsp_inc_tax2)?$request->single_dsp_inc_tax2:[] ;
                            $request_single_dpp_inc_tax = isset($request->single_dpp_inc_tax2)?$request->single_dpp_inc_tax2:[];
                            $request_profit_percent     = isset($request->profit_percent2)?$request->profit_percent2:[];
                            $request_single_dsp         = isset($request->single_dsp2)?$request->single_dsp2:[] ;
                        }else{
                            $request_single_dpp         = isset($request->single_dpp3)?$request->single_dpp3:[];
                            $request_single_dsp_inc_tax = isset($request->single_dsp_inc_tax3)?$request->single_dsp_inc_tax3:[] ;
                            $request_single_dpp_inc_tax = isset($request->single_dpp_inc_tax3)?$request->single_dpp_inc_tax3:[];
                            $request_profit_percent     = isset($request->profit_percent3)?$request->profit_percent3:[];
                            $request_single_dsp         = isset($request->single_dsp3)?$request->single_dsp3:[] ;
                        }
                        
                        if(!in_array($value,$units_new)){
                            $units_new[] = $value;
                        }
                        foreach($request_single_dpp as $k => $values){
                            $product_id  =  ProductPrice::where("product_id",$product->id)
                                                                    ->whereNull("default_name")
                                                                    ->where("number_of_default",$k)
                                                                    ->where("unit_id",$value)
                                                                    ->first();
                        
                            switch ($k) {
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
                            
                            if(empty($product_id) && $value != null){
                                $product_id_ptice                      =  new ProductPrice();
                                $product_id_ptice->product_id          =  $product->id ;   
                                $product_id_ptice->business_id         =  $business_id ;   
                                $product_id_ptice->name                =  $val ;   
                                $product_id_ptice->default_purchase_price =    $this->productUtil->num_uf($values)  ;   
                                $product_id_ptice->dpp_inc_tax         =  $this->productUtil->num_uf($request_single_dpp_inc_tax[$k])    ;   
                                $product_id_ptice->profit_percent      =  $this->productUtil->num_uf($request_profit_percent[$k])   ;   
                                $product_id_ptice->default_sell_price  =  $this->productUtil->num_uf($request_single_dsp[$k])   ;   
                                $product_id_ptice->sell_price_inc_tax  =  $this->productUtil->num_uf($request_single_dsp_inc_tax[$k])   ;   
                                $product_id_ptice->number_of_default   =  $k ;     
                                $product_id_ptice->unit_id             =  intVal($value) ;
                                $product_id_ptice->save();
                            
                            }else{
                                $product_id->name                      =  $val      ;   
                                $product_id->default_purchase_price    =  $this->productUtil->num_uf($values)   ;   
                                $product_id->dpp_inc_tax               =  $this->productUtil->num_uf($request_single_dpp_inc_tax[$k])      ;  
                                $product_id->profit_percent            =  $this->productUtil->num_uf($request_profit_percent[$k])      ;    
                                $product_id->default_sell_price        =  $this->productUtil->num_uf($request_single_dsp[$k])      ;   
                                $product_id->sell_price_inc_tax        =  $this->productUtil->num_uf($request_single_dsp_inc_tax[$k])     ;   
                                $product_id->unit_id                   =  intVal($value)    ;
                                if($value != null){
                                    $product_id->update();
                                }else{
                                    $product_id->delete();
                                }
                                
                            }
                                
                        }
                    }
                }
                

                $product_old_id  =  ProductPrice::where("product_id",$product->id)
                                                            ->whereNotIn("unit_id",$units_new)
                                                            ->get();
                foreach($product_old_id as $priceOne){
                    $priceOne->delete();
                    
                }
            }     

            Media::uploadMedia($product->business_id, $product, $request, 'product_brochure', true);
            // .......... more than unit ...
            if(isset($request->actual_name)){
                $data_unit = $request->only(["line_ids","actual_name","price_unit","short_name","allow_decimal","base_unit_id","base_unit_multiplier"]);
                Product::moreUpdateUnit($data_unit,$product);
            }
            DB::commit();
            $output = ['success' => 1,
                            'msg' => __('product.product_updated_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => $e->getMessage()
                        ];
        }

        if ($request->input('submit_type') == 'update_n_edit_opening_stock') {
            return redirect("/products/add-Opening-Product");
           
        } elseif ($request->input('submit_type') == 'submit_n_add_selling_prices') {
            return redirect()->action(
                'ProductController@addSellingPrices',
                [$product->id]
            );
        } elseif ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                'ProductController@create'
            )->with('status', $output);
        }

        return redirect('products')->with('status', $output);
    }

    /* 
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id 
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request,$name=null)
    {
        $result = $name;
        $business_id = $request->session()->get('user.business_id');
        $products = Product::where("business_id",$business_id)->where("name",$name)->first();
        if( isset($products) ){$status = true;}else{$status = false;}
        $output = ["success" => 1,"msg" => "successfull","status" => $status];
        return  $output;
    }
    /* 
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id 
     * @return \Illuminate\Http\Response
     */
    public function subOfMain(Request $request,$id=null)
    {
     
        $result = $id;
        $business_id = $request->session()->get('user.business_id');
        $products = Category::where("business_id",$business_id)->where("parent_id",$id)->get();
        $array = [];
        foreach($products as $it){
                $array[$it->id] = $it->name;
        }
        $output = ["success" => true,"msg" => "successfull","array" => $array];
        return  $output;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('product.delete') ) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
               $business_id = request()->session()->get('user.business_id');

               $can_be_deleted = true;
               $error_msg = '';

               //Check if any purchase or transfer exists
               $count = PurchaseLine::join(
                   'transactions as T',
                   'purchase_lines.transaction_id',
                   '=',
                   'T.id'
               )
                                   ->whereIn('T.type', ['purchase'])
                                   ->where('T.business_id', $business_id)
                                   ->where('purchase_lines.product_id', $id)
                                   ->count();
               if ($count > 0) {
                   $can_be_deleted = false;
                   $error_msg = __('lang_v1.purchase_already_exist');
               } else {
                   //Check if any opening stock sold
                   $count = PurchaseLine::join(
                       'transactions as T',
                       'purchase_lines.transaction_id',
                       '=',
                       'T.id'
                    )
                        ->where('T.type', 'opening_stock')
                        ->where('T.business_id', $business_id)
                        ->where('purchase_lines.product_id', $id)
                        ->where('purchase_lines.quantity_sold', '>', 0)
                        ->count();
                    $count2 = \App\TransactionSellLine::where("product_id",$id)->count();
                    if ($count > 0) {
                        $can_be_deleted = false;
                        $error_msg = __('lang_v1.purchase_already_exist');
                    } else {
                        if($count2>0){
                            $can_be_deleted = false;
                            $error_msg = __('lang_v1.purchase_already_exist');
                        }else{
                            //Check if any opening stock sold
                            $count = PurchaseLine::join(
                                'transactions as T',
                                'purchase_lines.transaction_id',
                                '=',
                                'T.id'
                            )
                                            ->where('T.type', 'opening_stock')
                                            ->where('T.business_id', $business_id)
                                            ->where('purchase_lines.product_id', $id)
                                            ->where('purchase_lines.quantity_sold', '>', 0)
                                            ->count();
                            if ($count > 0) {
                                $can_be_deleted = false;
                                $error_msg = __('lang_v1.opening_stock_sold');
                            } else {
                                //Check if any stock is adjusted
                                $count = PurchaseLine::join(
                                    'transactions as T',
                                    'purchase_lines.transaction_id',
                                    '=',
                                    'T.id'
                                )
                                            ->where('T.business_id', $business_id)
                                            ->where('purchase_lines.product_id', $id)
                                            ->where('purchase_lines.quantity_adjusted', '>', 0)
                                            ->count();
                                if ($count > 0) {
                                    $can_be_deleted = false;
                                    $error_msg = __('lang_v1.stock_adjusted');
                                }
                            }
                        }
                        
                    }
               }

               $product = Product::where('id', $id)
                               ->where('business_id', $business_id)
                               ->with('variations')
                               ->first();

               //Check if product is added as an ingredient of any recipe
               if ($this->moduleUtil->isModuleInstalled('Manufacturing')) {
                   $variation_ids = $product->variations->pluck('id');

                   $exists_as_ingredient = \Modules\Manufacturing\Entities\MfgRecipeIngredient::whereIn('variation_id', $variation_ids)
                       ->exists();
                       if ($exists_as_ingredient) {
                           $can_be_deleted = false;
                           $error_msg = __('manufacturing::lang.added_as_ingredient');
                       }
               }

               if ($can_be_deleted) {
                   if (!empty($product)) {
                       DB::beginTransaction();
                       //Delete variation location details
                       VariationLocationDetails::where('product_id', $id)
                                               ->delete();
                       $product->delete();

                       DB::commit();
                   }

                   $output = ['success' => true,
                               'msg' => __("lang_v1.product_delete_success")
                           ];
               } else {
                   $output = ['success' => false,
                               'msg' => $error_msg
                           ];
               }
           } catch (\Exception $e) {
               DB::rollBack();
               \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

               $output = ['success' => false,
                               'msg' => __("messages.something_went_wrong")
                           ];
           }

           return $output;
       }
    }

    /**
     * Get subcategories list for a category.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getSubCategories(Request $request)
    {
        if (!empty($request->input('cat_id'))) {
            $category_id = $request->input('cat_id');
            $business_id = $request->session()->get('user.business_id');
            $sub_categories = Category::where('business_id', $business_id)
                        ->where('parent_id', $category_id)
                        ->select(['name', 'id'])
                        ->get();
            $html = '<option value="">None</option>';
            if (!empty($sub_categories)) {
                foreach ($sub_categories as $sub_category) {
                    $html .= '<option value="' . $sub_category->id .'">' .$sub_category->name . '</option>';
                }
            }
            echo $html;
            exit;
        }
    }
    /**
     * Get stock list .
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewStock($id)
    {
         
        if (!auth()->user()->can('product.view_sStock')  ) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
        
            $business_id = request()->session()->get('user.business_id');
            $location = BusinessLocation::where('business_id', $business_id)->first();            
            
 
            //***........ last price  purchase
            $purchaseline_last = PurchaseLine::orderBy("id" , "desc" )->where("product_id",$id)->first();
            //***........ last price  openQuantity
            $OpenQuantity__last = OpeningQuantity::orderBy("id" , "desc" )->where("business_location_id",$location->id)->where("product_id",$id)->first();
            //***........ last price  sales
            $sells_last = TransactionSellLine::orderBy("id" , "desc" )->where("product_id",$id)
                                                                        ->whereHas("transaction",function($query){
                                                                             $query->whereNotIn("type" , ["production_sell","Stock_Out"]);
                                                                        })->first();
            //***........ get product
            $product = Product::where( "id", $id)->with(['variations', 'variations.product_variation', 'variations.group_prices'])->first();
            //***........ get warehouse info     
            $warehouse_info = WarehouseInfo::where('business_id', $business_id)->get();
            //***........ open  max min 
            $OpeningQuantity = OpeningQuantity::where("business_location_id",$location->id)->where("product_id",$id)->whereHas("transaction",function($query){
                                                    $query->whereIn("status",["opening_stock"]);
                                                })->select( DB::raw('SUM(quantity * price) as price'),DB::raw('SUM(price) as price_one'),
                                                            DB::raw('MAX(price) as MAX'),
                                                            DB::raw('MIN(price) as MIN'),
                                                            DB::raw('COUNT(id) as count')
                                                )->first();
            //***........ purchase  max min
            $purchaseline = PurchaseLine::where("product_id",$id)->whereHas("transaction",function($query){
                                                $query->whereIn("status",["received"]);
                                        })->select( DB::raw('SUM(quantity * purchase_price) as price'),
                                                    DB::raw('SUM(purchase_price) as price_one'),
                                                    DB::raw('MAX(purchase_price) as MAX'),
                                                    DB::raw('MIN(purchase_price) as MIN'), 
                                                    DB::raw('COUNT(id) as count')
                                        )->first();
           //***........ sales  max min
            $sells = TransactionSellLine::where("product_id",$id)->whereHas("transaction",function($query){
                                            $query->whereIn("status",["final","ApprovedQuotation","delivered"]);
                                            $query->whereNotIn("type" , ["sale","production_sell","Stock_Out"]);
                                        })->select( DB::raw('SUM(unit_price) as price'),
                                                    DB::raw('MAX(unit_price) as MAX'),
                                                    DB::raw('MIN(unit_price) as MIN'),
                                                    DB::raw('COUNT(id) as count')
                                         )->first();

            $Purchase_prices = [];

            $LAST_FINAL      = round(($purchaseline_last)?$purchaseline_last->purchase_price:0,3);
            $MAX_FINAL       = round(($purchaseline->MAX)?$purchaseline->MAX:0,3);
            $MIN_FINAL       = round(($purchaseline->MIN)?$purchaseline->MIN:0,3);
            $LAST_FINAL_open = round(($OpenQuantity__last)?$OpenQuantity__last->price:0,3);
            $MAX_FINAL_open  = round(($OpeningQuantity->MAX)?$OpeningQuantity->MAX:0,3);
            $MIN_FINAL_open  = round(($OpeningQuantity->MIN)?$OpeningQuantity->MIN:0,3);
            
            if( $MAX_FINAL_open > $MAX_FINAL ){ $MAX_FINAL = $MAX_FINAL_open; }
            if( $MIN_FINAL_open > $MIN_FINAL ){ $MIN_FINAL = $MIN_FINAL_open; }
            
            if(isset($OpenQuantity__last)){
                if(isset($purchaseline_last)){
                    if( $OpenQuantity__last->created_at > $purchaseline_last->created_at){
                        $LAST_FINAL =  $LAST_FINAL_open ;
                    }
                }else{
                     $LAST_FINAL =  $LAST_FINAL_open ;
                    
                }
            }
            
            
            // $COST_FINAL = ($html)?$html:0;
            $COST_FINAL = \App\Product::product_cost($id);
            // $COST_FINAL = round(($html_one)?$html_one:0,3);
            $Purchase_prices["middle"]  = $COST_FINAL;
            $Purchase_prices["minimum"] = $MIN_FINAL;
            $Purchase_prices["maximum"] = $MAX_FINAL;
            $Purchase_prices["final"]   = $LAST_FINAL;
            
            $Sells_prices = [];
           
            $LAST_FINAL_sells = round(($sells_last)?$sells_last->unit_price:0,3);
            $MAX_FINAL_sells  = round(($sells->MAX)?$sells->MAX:0,3);
            $MIN_FINAL_sells  = round(($sells->MIN)?$sells->MIN:0,3);
            $COST_FINAL_sells = round( (($sells->count)!=0) ? ($sells->price)/($sells->count):0,3);

            $Sells_prices["middle"]  = $COST_FINAL_sells;
            $Sells_prices["minimum"] = $MIN_FINAL_sells;
            $Sells_prices["maximum"] = $MAX_FINAL_sells;
            $Sells_prices["final"]   = $LAST_FINAL_sells;
            
            
        
        }

        return view("product.view_sStock")
                ->with(compact('warehouse_info',
                                'COST_FINAL' ,
                                'product',
                                "Purchase_prices",
                                "Sells_prices"
                                 ));
       
    }
    /**
     * Get stock list .P
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewStatusReport($id)
    {

        if (!auth()->user()->can('product.view-modal_status')  ) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
        
            $business_id = request()->session()->get('user.business_id');
            $product = Product::where('business_id', $business_id)
                                ->where('id', $id)
                                ->with(['variations', 'variations.product_variation', 'variations.group_prices'])
                                ->first();
                                
            $PurchaseLine = PurchaseLine::select()->get();
            
            $Warehouse = Warehouse::where('business_id', $business_id)
            ->get();
            
            $unit = Unit::where('business_id', $business_id)
            ->get();
            $products = Product::where('business_id', $business_id)
            ->get();

            $Transaction = Transaction::where('business_id', $business_id)->get();
            
            $transaction_id_list = [];
            $transactionN = [];
            
            foreach($Transaction as $trans){
                if( $trans->type == "purchase" ){                 
                        
                        foreach($PurchaseLine as $PL){
                            if($PL->transaction_id == $trans->id){
                                if($PL->product_id == $id){
                                    $transaction_id_list[] = $PL->transaction_id;               
                                }
                            }
                        }
                    }
            }
            // dd($transaction_id_list);
            
            $product_list = [];
            foreach($products as $prd){
                $product_list[$prd->id] = $prd->name;
                
            }
            $Warehouse_list = [];
            
            foreach($Warehouse as $Ware){
                $Warehouse_list[$Ware->id] = $Ware->name;
                
            }
            
            $recived_list = [];
            foreach($transaction_id_list as  $tr_list){
                $RecievedPrevious = RecievedPrevious::where('transaction_id', $tr_list)->get();
                $recived_list[] = $RecievedPrevious;
            }


            $RecievedWrong = RecievedWrong::select()->get();
            // dd($recived_list_1);
        
        }

        return view("product.view-modal_status")->with(compact('product','product_list','recived_list','RecievedWrong','unit' , 'Warehouse_list'));
       
    }

    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProductVariationFormPart(Request $request)
    {
        $business_id    = $request->session()->get('user.business_id');
        $action         = $request->input('action');
        $business       = Business::findOrFail($business_id);
        $profit_percent = $business->default_profit_percent;
        if ($request->input('action') == "add") {
            $list_sub_unit  = $request->input('list');
            $change         = $request->input('change');
            $main_unit      = $request->input('main_unit');
            $main_id        = $request->input('main_id');
            if ($request->input('type') == 'single') {
                $array_unit    = [];$other_unit=[];
                $product_price  = ProductPrice::where("business_id",$business_id)->get();
                $units_all      = Unit::forDropdown($business_id, false);
                $units          = Unit::forDropdown($business_id, true);
                $unitsP         = Unit::forDropdownInPrice($business_id);
                foreach($unitsP as $key => $value){ $array_unit[]   = [$key => $value]; }
                if($change == "change"){
                    $other_unit[]        = $main_id;
                    foreach(json_decode($list_sub_unit)  as $i){
                        $other_unit[] = $i;
                    }
                }else{
                    $other_unit[]        = $product->unit_id;
                    if($product->sub_unit_ids != null){
                        foreach($product->sub_unit_ids  as $i){
                            $other_unit[] = $i;
                        }
                    }
                }
                $units_main  = [] ;
                foreach($units_all as $ky => $li){
                    if(in_array($ky,$other_unit)){
                        $units_main[$ky] = $li ;
                    }
                }
                return view('product.partials.single_product_form_part')
                        ->with([
                            'profit_percent' => $profit_percent,
                            'product_price'  => $product_price , 
                            'array_unit'     => $array_unit,
                            'units'          => $units,
                            'other_unit'     => $other_unit,
                            'units_main'     => $units_main
                        ]);
            } elseif ($request->input('type') == 'variable') {
                $array_unit    = [];$other_unit=[];
                $product_price  = ProductPrice::where("business_id",$business_id)->get();
                $units_all      = Unit::forDropdown($business_id, false);
                $units          = Unit::forDropdown($business_id, true);
                $unitsP         = Unit::forDropdownInPrice($business_id);
                foreach($unitsP as $key => $value){ $array_unit[]   = [$key => $value]; }
                if($change == "change"){
                    $other_unit[]        = $main_id;
                    foreach(json_decode($list_sub_unit)  as $i){
                        $other_unit[] = $i;
                    }
                }else{
                    $other_unit[]        = $product->unit_id;
                    if($product->sub_unit_ids != null){
                        foreach($product->sub_unit_ids  as $i){
                            $other_unit[] = $i;
                        }
                    }
                }
                $units_main  = [] ;
                foreach($units_all as $ky => $li){
                    if(in_array($ky,$other_unit)){
                        $units_main[$ky] = $li ;
                    }
                }
                $variation_templates = VariationTemplate::where('business_id', $business_id)->pluck('name', 'id')->toArray();
                $variation_templates = [ "" => __('messages.please_select')] + $variation_templates;
                return view('product.partials.variable_product_form_part')
                        ->with(compact([
                            'variation_templates',
                            'profit_percent',
                            'action',
                            'product_price', 
                            'array_unit',   
                            'units',        
                            'other_unit',   
                            'units_main'   
                        ]));
            } elseif ($request->input('type') == 'combo') {
                $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
                return view('product.partials.combo_product_form_part')
                        ->with(compact([
                            'profit_percent',
                            'currency_details',
                            'action'
                        ]));
            }
        } elseif ($request->input('action') == "edit" || $request->input('action') == "duplicate") {
            $action         = $request->input('action');
            $list_sub_unit  = $request->input('list');
            $change         = $request->input('change');
            $main_unit      = $request->input('main_unit');
            $main_id        = $request->input('main_id');
            $product_id     = $request->input('product_id');
            $product        = Product::find($product_id);
            if ($request->input('type') == 'single') {
                $empty               = 0;
                $list_unit           = [];
                $other_unit          = [];
                $array_unit          = [];
                $product_details     = ProductVariation::where('product_id', $product_id)
                                                    ->with([
                                                        'variations',
                                                        'variations.media'
                                                    ])
                                                    ->first();
                $units_all          = Unit::forDropdown($business_id, false);
             
                if($change == "change"){
                    if($main_unit == "main"){
                        $other_unit[]        = $main_id;
                    }else{
                        $other_unit[]        = $product->unit_id;
                    }
                    foreach(json_decode($list_sub_unit)  as $i){
                        $other_unit[] = $i;
                    }
                }else{
                    $other_unit[]        = $product->unit_id;
                    if($product->sub_unit_ids != null){
                        foreach($product->sub_unit_ids  as $i){
                                $other_unit[] = $i;
                            }
                    }
                    
                }
                $units_main  = [] ;
                foreach($units_all as $ky => $li){
                    if(in_array($ky,$other_unit)){
                        $units_main[$ky] = $li ;
                    }
                }
              
                $query               = ProductPrice::where("business_id",$business_id)->where("product_id",$product_id);
                $product_price       = $query->get();
                $Product_price_units = $query->groupBy('unit_id')->get();
                foreach($Product_price_units as $en){$list_unit[$en->unit_id] = $en->unit_id;}
                if(count($list_unit)==0){  $empty = 1; }
                
                return view('product.partials.edit_single_product_form_part')
                            ->with(compact([
                                'product_details',
                                'product_price',
                                'other_unit',
                                'empty',
                                'action',
                                'product_price',
                                "units_main",
                                "list_unit"
                            ]));
            } elseif ($request->input('type') == 'variable') { 
               
                $list_unit = [];  $empty = 0;
                $product_variations = ProductVariation::where('product_id', $product_id)
                                                        ->with([
                                                            'variations',
                                                            'variations.media'
                                                            ])
                                                            ->get();
                $product_details     = ProductVariation::where('product_id', $product_id)
                                                        ->with([
                                                            'variations',
                                                            'variations.media'
                                                            ])
                                                            ->first();

                $product_price  = ProductPrice::where("business_id",$business_id)->where("product_id",$product_id)->get();
                $array_unit     = [];$other_unit=[];
                $units_all      = Unit::forDropdown($business_id, false);
                $units          = Unit::forDropdown($business_id, true);
                $unitsP         = Unit::forDropdownInPrice($business_id);
                foreach($unitsP as $key => $value){ $array_unit[]   = [$key => $value]; }
                
                
                if($change == "change"){
                    if($main_unit == "main"){
                        $other_unit[]        = $main_id;
                    }else{
                        $other_unit[]        = $product->unit_id;
                    }
                    foreach(json_decode($list_sub_unit)  as $i){
                        $other_unit[] = $i;
                    }
                }else{
                    $other_unit[]        = $product->unit_id;
                    if($product->sub_unit_ids != null){
                        foreach($product->sub_unit_ids  as $i){
                                $other_unit[] = $i;
                            }
                    }
                    
                }
                $units_main  = [] ;
                foreach($units_all as $ky => $li){
                    if(in_array($ky,$other_unit)){
                        $units_main[$ky] = $li ;
                    }
                }
                $query               = ProductPrice::where("business_id",$business_id)->where("product_id",$product_id);
                $amazing_list        = [];
                $product_price       = $query->get();
                $Product_price_units = $query->groupBy('unit_id')->get();
                // $Product_price_variations = $query->groupBy('unit_id')->groupBy('variations_value_id')->get();
                // foreach($Product_price_variations as $i => $item){
                //     $amazing_list[] = [
                //             "id"   => $item->variations_value_id,
                //             "unit" => $item->unit_id,
                //     ];

                // }
                // dd($amazing_list);
                foreach($Product_price_units as $en){$list_unit[$en->unit_id] = $en->unit_id;}
                if(count($list_unit)==0){  $empty = 1; }
                return view('product.partials.variable_product_form_part')
                        ->with(compact([ 
                                        'product_variations',
                                        'profit_percent',
                                        'action',
                                        'product_price', 
                                        'array_unit',   
                                        'units',        
                                        'other_unit',   
                                        'units_main',
                                        'empty',
                                        'product_details',
                                        // 'Product_price_variations',
                                        'list_unit'   
                                    ]));
            } elseif ($request->input('type') == 'combo') {
                $product_details  = ProductVariation::where('product_id', $product_id)
                                                    ->with([ 'variations','variations.media'])
                                                    ->first();
                $combo_variations = $this->productUtil->__getComboProductDetails($product_details['variations'][0]->combo_variations, $business_id);
                $variation_id     = $product_details['variations'][0]->id;
                $profit_percent   = $product_details['variations'][0]->profit_percent;
                $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
                $product_price    = ProductPrice::where("business_id",$business_id)->where("product_id",$product_id)->get();
                return view('product.partials.combo_product_form_part')
                                        ->with(compact([
                                                        'combo_variations', 
                                                        'profit_percent', 
                                                        'action', 
                                                        'variation_id',
                                                        'currency_details',
                                                        'product_id',
                                                        'product_price',
                                                    ]));
            
            }
        }
    }
    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getVariationValueRow(Request $request)
    {
        $business_id     = $request->session()->get('user.business_id');
        $business        = Business::findOrFail($business_id);
        $profit_percent  = $business->default_profit_percent;
        $ks              = $request->input('ks');//#2024-8-6
        $variation_index = $request->input('variation_row_index');
        $value_index     = $request->input('value_index') + 1;
        $product_price   = ProductPrice::where("business_id",$business_id)->get();
        $row_type        = $request->input('row_type', 'add');
        $array_unit     = [];$other_unit=[];
        $list_sub_unit  = $request->input('list');
        $change         = $request->input('change');
        $main_unit      = $request->input('main_unit');
        $main_id        = $request->input('main_id');
        $product_price  = ProductPrice::where("business_id",$business_id)->get();
        $units_all      = Unit::forDropdown($business_id, false);
        $units          = Unit::forDropdown($business_id, true);
        $unitsP         = Unit::forDropdownInPrice($business_id);
        foreach($unitsP as $key => $value){ $array_unit[]   = [$key => $value]; }
         
        $other_unit[]        = $main_id;
        foreach(json_decode($list_sub_unit)  as $i){
            $other_unit[] = $i;
        }
       
        $units_main  = [] ;
        foreach($units_all as $ky => $li){
            if(in_array($ky,$other_unit)){
                $units_main[$ky] = $li ;
            }
        }
         
        return view('product.partials.variation_value_row')
                ->with(compact('profit_percent','units_main','other_unit','product_price','ks', 'variation_index', 'value_index', 'row_type'));
    }
//......................................................................................................###########
    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getProductVariationRow(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findorfail($business_id);
        $profit_percent = $business->default_profit_percent;

        $variation_templates = VariationTemplate::where('business_id', $business_id)
                                                ->pluck('name', 'id')->toArray();
        $variation_templates = [ "" => __('messages.please_select')] + $variation_templates;
        $product_price = ProductPrice::where("business_id",$business_id)->get();
        $row_index      = $request->input('row_index', 0);
        $action         = $request->input('action');
        $ks             = $request->input('ks');//#2024-8-6
        $array_unit     = [];$other_unit=[];
        $list_sub_unit  = $request->input('list');
        $change         = $request->input('change');
        $main_unit      = $request->input('main_unit');
        $main_id        = $request->input('main_id');
        $product_price  = ProductPrice::where("business_id",$business_id)->get();
        $units_all      = Unit::forDropdown($business_id, false);
        $units          = Unit::forDropdown($business_id, true);
        $unitsP         = Unit::forDropdownInPrice($business_id);
        foreach($unitsP as $key => $value){ $array_unit[]   = [$key => $value]; }
         
        $other_unit[]        = $main_id;
        foreach(json_decode($list_sub_unit)  as $i){
            $other_unit[] = $i;
        }
       
        $units_main  = [] ;
        foreach($units_all as $ky => $li){
            if(in_array($ky,$other_unit)){
                $units_main[$ky] = $li ;
            }
        }
      
        return view('product.partials.product_variation_row')
                    ->with(compact('variation_templates','product_price','units_main','other_unit', 'ks','row_index', 'action', 'profit_percent'));
    }

    /**
     * Get product form parts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getVariationTemplate(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $business = Business::findorfail($business_id);
        $profit_percent = $business->default_profit_percent;
        $product_price = ProductPrice::where("business_id",$business_id)->get();
        $template = VariationTemplate::where('id', $request->input('template_id'))
                                                ->with(['values'])
                                                ->first();
        $row_index = $request->input('row_index');
        $ks        = $request->input('ks');
        $array_unit     = [];$other_unit=[];
        $list_sub_unit  = $request->input('list');
        $change         = $request->input('change');
        $main_unit      = $request->input('main_unit');
        $main_id        = $request->input('main_id');
        $product_price  = ProductPrice::where("business_id",$business_id)->get();
        $units_all      = Unit::forDropdown($business_id, false);
        $units          = Unit::forDropdown($business_id, true);
        $unitsP         = Unit::forDropdownInPrice($business_id);
        foreach($unitsP as $key => $value){ $array_unit[]   = [$key => $value]; }
         
        $other_unit[]        = $main_id;
        foreach(json_decode($list_sub_unit)  as $i){
            $other_unit[] = $i;
        }
       
        $units_main  = [] ;
        foreach($units_all as $ky => $li){
            if(in_array($ky,$other_unit)){
                $units_main[$ky] = $li ;
            }
        }
        return view('product.partials.product_variation_template')
                    ->with(compact('template','units_main','other_unit', 'ks','product_price','row_index', 'profit_percent'));
    }

    /**
     * Return the view for combo product row
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getComboProductEntryRow(Request $request)
    {
        if (request()->ajax()) {
        
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $business_id = $request->session()->get('user.business_id');

            if (!empty($product_id)) {
                $product = Product::where('id', $product_id)
                        ->with(['unit'])
                        ->first();

                $query = Variation::where('product_id', $product_id)
                        ->with(['product_variation']);

                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }
                $variations =  $query->get();
                $var         = $variations->first();
                $var         = ($var)? $var->default_purchase_price:0;
                $allUnits    = [];
                $business    = \App\Business::find($business_id);
                $allUnits[$product->unit_id] = [
                    'name'          => $product->unit->actual_name,
                    'multiplier'    => ($product->unit->base_unit_multiplier !=null)?$product->unit->base_unit_multiplier:1,
                    'allow_decimal' => $product->unit->allow_decimal,
                    'price'         => $var,
                    'check_price'   => $business->default_price_unit,
                    ];
                 
                if($product->sub_unit_ids != null){
                    foreach($product->sub_unit_ids  as $i){
                            $row_price    =  0;
                            $un           = \App\Unit::find($i);
                            $row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$product->id)->where("number_of_default",0)->first();
                            $row_price    = ($row_price)?$row_price->default_purchase_price:0;
                            $allUnits[$i] = [
                                'name'          => $un->actual_name,
                                'multiplier'    => ($un->base_unit_multiplier != null)?$un->base_unit_multiplier:1,
                                'allow_decimal' => $un->allow_decimal,
                                'price'         => $row_price,
                                'check_price'   => $business->default_price_unit,
                            ] ;
                        }
                } 
                $sub_units              = $allUnits  ;
                $list_of_prices_in_unit = \App\Product::getProductPrices($product_id);
                // $sub_units = $this->productUtil->getSubUnits($business_id, $product['unit']->id);

                return view('product.partials.combo_product_entry_row')
                ->with(compact('product', 'variations','list_of_prices_in_unit','sub_units'));
            }
        }
    }

    /**
     * Retrieves products list.
     *
     * @param  string  $q
     * @param  boolean  $check_qty
     *
     * @return JSON
     */
    public function getProducts(Request $request)
    {
        // return $request;
        if (request()->ajax()) {
            $search_term     = request()->input('term', '');
            $location_id     = request()->input('location_id', null);
            $check_qty       = request()->input('check_qty', false);
            $return_check    = request()->input('return_check', false);
            $price_group_id  = request()->input('price_group', null);
            $price_group_id  = request()->input('price_group', '');
            $business_id     = request()->session()->get('user.business_id');
            $not_for_selling = request()->get('not_for_selling', null);
            $product_types   = request()->get('product_types', []);
            $search_fields   = request()->get('search_fields', ['name', 'sku']);
            if (in_array('sku', $search_fields)) {
                $search_fields[] = 'sub_sku';
            }
            
            
        $result = $this->productUtil->filterProduct($business_id, $search_term, $location_id, $not_for_selling, $price_group_id, $product_types, $search_fields, $check_qty,false,"like",1);
            
            $warahouse_info_qty = WarehouseInfo::where("business_id",$business_id)->where("product_id",$result[0]->product_id)->sum("product_qty");
         
            
             
            $result[0]->qty_available = $warahouse_info_qty;
           
    
 

            return json_encode($result);
        }
    }

    /**
     * Retrieves products list without variation list
     *
     * @param  string  $q
     * @param  boolean  $check_qty
     *
     * @return JSON
     */
    public function getProductsWithoutVariations()
    {
        if (request()->ajax()) {
            $term = request()->input('term', '');
            //$location_id = request()->input('location_id', '');

            //$check_qty = request()->input('check_qty', false);

            $business_id = request()->session()->get('user.business_id');

            $products = Product::join('variations', 'products.id', '=', 'variations.product_id')
                ->where('products.business_id', $business_id)
                ->where('products.type', '!=', 'modifier');

            //Include search
            if (!empty($term)) {
                $products->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term .'%');
                    $query->orWhere('sku', 'like', '%' . $term .'%');
                    $query->orWhere('sub_sku', 'like', '%' . $term .'%');
                });
            }

            //Include check for quantity
            // if($check_qty){
            //     $products->where('VLD.qty_available', '>', 0);
            // }

            $products = $products->groupBy('products.id')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    'products.enable_stock',
                    'products.sku'
                )
                    ->orderBy('products.name')
                    ->get();
            return json_encode($products);
        }
    }

    /**
     * Checks if product sku already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkProductSku(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $sku = $request->input('sku');
        $product_id = $request->input('product_id');

        //check in products table
        $query = Product::where('business_id', $business_id)
                        ->where('sku', $sku);
        if (!empty($product_id)) {
            $query->where('id', '!=', $product_id);
        }
        $count = $query->count();

        //check in variation table if $count = 0
        if ($count == 0) {
            $count = Variation::where('sub_sku', $sku)
                            ->join('products', 'variations.product_id', '=', 'products.id')
                            ->where('product_id', '!=', $product_id)
                            ->where('business_id', $business_id)
                            ->count();
        }
        if ($count == 0) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }

    /**
     * Loads quick add product modal.
     *
     * @return \Illuminate\Http\Response
     */
    public function quickAdd()
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $product_name = !empty(request()->input('product_name'))? request()->input('product_name') : '';

        $product_for = !empty(request()->input('product_for'))? request()->input('product_for') : null;

        $business_id = request()->session()->get('user.business_id');
        $categories = Category::forDropdown($business_id, 'product');
        $brands = Brands::forDropdown($business_id);
        $units = Unit::forDropdown($business_id, true);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];

        $barcode_types = $this->barcode_types;
        $product_price = \App\Models\ProductPrice::where("business_id",$business_id)->get();
        $default_profit_percent = Business::where('id', $business_id)->value('default_profit_percent');

        $locations = BusinessLocation::forDropdown($business_id);

        $enable_expiry = request()->session()->get('business.enable_product_expiry');
        $enable_lot = request()->session()->get('business.enable_lot_number');

        $module_form_parts = $this->moduleUtil->getModuleData('product_form_part');

        //Get all business locations
        $business_locations = BusinessLocation::forDropdown($business_id);

        $common_settings = session()->get('business.common_settings');
        $warranties = Warranty::forDropdown($business_id);
        $units_ = [];  
                 
        $unitsm  = Unit::forDropdown($business_id, false);
        $units   = Unit::forDropdown($business_id, true);
        $unitsP  = Unit::forDropdownInPrice($business_id);
        $arraa   = [] ;
        foreach($unitsP as $key => $value){
                $arraa[] = [$key => $value];
        }
        return view('product.partials.quick_add_product')
                ->with(compact('categories', 'brands', 'units','product_price','unitsP','arraa','unitsm', 'taxes', 'barcode_types', 'default_profit_percent', 'tax_attributes', 'product_name', 'locations', 'product_for', 'enable_expiry', 'enable_lot', 'module_form_parts', 'business_locations', 'common_settings', 'warranties'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveQuickProduct(Request $request)
    {
        if (!auth()->user()->can('product.create') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = $request->session()->get('user.business_id');
            $form_fields = ['name', 'brand_id', 'unit_id', 'category_id', 'tax', 'barcode_type','tax_type', 'sku',
                'alert_quantity', 'type', 'sub_unit_ids', 'sub_category_id', 'weight', 'product_custom_field1', 'product_custom_field2', 'product_custom_field3', 'product_custom_field4', 'product_description'];

            $module_form_fields = $this->moduleUtil->getModuleData('product_form_fields');
            if (!empty($module_form_fields)) {
                foreach ($module_form_fields as $key => $value) {
                    if (!empty($value) && is_array($value)) {
                        $form_fields = array_merge($form_fields, $value);
                    }
                }
            }
            $product_details = $request->only($form_fields);

            $product_details['type'] = empty($product_details['type']) ? 'single' : $product_details['type'];
            $product_details['business_id'] = $business_id;
            $product_details['created_by'] = $request->session()->get('user.id');
            if (!empty($request->input('enable_stock')) &&  $request->input('enable_stock') == 1) {
                $product_details['enable_stock'] = 1 ;
                //TODO: Save total qty
                //$product_details['total_qty_available'] = 0;
            }
            if (!empty($request->input('not_for_selling')) &&  $request->input('not_for_selling') == 1) {
                $product_details['not_for_selling'] = 1 ;
            }
            if (empty($product_details['sku'])) {
                $product_details['sku'] = ' ';
            }

            $expiry_enabled = $request->session()->get('business.enable_product_expiry');
            if (!empty($request->input('expiry_period_type')) && !empty($request->input('expiry_period')) && !empty($expiry_enabled)) {
                $product_details['expiry_period_type'] = $request->input('expiry_period_type');
                $product_details['expiry_period'] = $this->productUtil->num_uf($request->input('expiry_period'));
            }

            if (!empty($request->input('enable_sr_no')) &&  $request->input('enable_sr_no') == 1) {
                $product_details['enable_sr_no'] = 1 ;
            }

            $product_details['warranty_id'] = !empty($request->input('warranty_id')) ? $request->input('warranty_id') : null;

            DB::beginTransaction();

            $product = Product::create($product_details);

            if (empty(trim($request->input('sku')))) {
                $sku = $this->productUtil->generateProductSku($product->id);
                $product->sku = $sku;
                $product->save();
            }
            (!empty($request->input('single_dpp1')))?$this->productUtil->createSingleProductVariationPrices($product->id, $product->sku, $request->input('single_dpp1'), $request->input('single_dpp_inc_tax1'), $request->input('profit_percent1'), $request->input('single_dsp1'), $request->input('single_dsp_inc_tax1'),$product->sku2,1,$request->input('unit1')):null;
            (!empty($request->input('single_dpp2')))?$this->productUtil->createSingleProductVariationPrices($product->id, $product->sku, $request->input('single_dpp2'), $request->input('single_dpp_inc_tax2'), $request->input('profit_percent2'), $request->input('single_dsp2'), $request->input('single_dsp_inc_tax2'),$product->sku2,null,$request->input('unit2')):null;
            (!empty($request->input('single_dpp3')))?$this->productUtil->createSingleProductVariationPrices($product->id, $product->sku, $request->input('single_dpp3'), $request->input('single_dpp_inc_tax3'), $request->input('profit_percent3'), $request->input('single_dsp3'), $request->input('single_dsp_inc_tax3'),$product->sku2,null,$request->input('unit3')):null;
            // $this->productUtil->createSingleProductVariation(
            //     $product->id,
            //     $product->sku,
            //     $request->input('single_dpp'),
            //     $request->input('single_dpp_inc_tax'),
            //     $request->input('profit_percent'),
            //     $request->input('single_dsp'),
            //     $request->input('single_dsp_inc_tax')
            // );

            if ($product->enable_stock == 1 && !empty($request->input('opening_stock'))) {
                $user_id = $request->session()->get('user.id');

                $transaction_date = $request->session()->get("financial_year.start");
                $transaction_date = \Carbon::createFromFormat('Y-m-d', $transaction_date)->toDateTimeString();

                $this->productUtil->addSingleProductOpeningStock($business_id, $product, $request->input('opening_stock'), $transaction_date, $user_id);
            }

            //Add product locations
            $product_locations = $request->input('product_locations');
            if (!empty($product_locations)) {
                $product->product_locations()->sync($product_locations);
            }

            DB::commit();

            $output = ['success' => 1,
                            'msg'       => __('product.product_added_success'),
                            'product'   => $product,
                            'variation' => $product->variations->first(),
                            'locations' => $product_locations
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function viewStockDetails($id)
    {
         
        if (!auth()->user()->can('product.view') && !auth()->user()->can('warehouse.views')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $product = Product::where('business_id', $business_id)
                        ->with(['brand', 'unit', 'category', 'sub_category', 'product_tax', 'variations', 'variations.product_variation', 'variations.group_prices', 'variations.media', 'product_locations', 'warranty', 'media'])
                        ->findOrFail($id);

            $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');

            $trans = TransactionRecieved::where("business_id",$business_id)->get();
            $trans_d = TransactionDelivery::where("business_id",$business_id)->get();

            $RecievedPrevious_sum = RecievedPrevious::where("product_id",$id)->select(DB::raw("SUM(current_qty) as quantity"))->first();
            $RecievedPrevious = RecievedPrevious::where("product_id",$id)->get();
            
            $RecievedPrevious_id = RecievedPrevious::where("product_id",$id)->first();
            
            $DeliveredPrevious = DeliveredPrevious::where("product_id",$id)
                                    ->get();
            
            $transaction_id = $RecievedPrevious_id->transaction_id;
            
            $PurchaseLine = PurchaseLine::where("product_id",$id)
                                        ->select(DB::raw("SUM(quantity) as total"))                        
                                        ->first();
            $sell = TransactionSellLine::where("product_id",$id)
                                        ->select(DB::raw("SUM(quantity) as total"))                        
                                        ->first();

            $supplier_remain = $PurchaseLine->total - $RecievedPrevious_sum->quantity;
            

            $contact = Transaction::where("id",$transaction_id)->first();
          
        
            $supplier = $contact->contact;

  
            $allowed_group_prices = [];

            foreach ($price_groups as $key => $value) {
                if (auth()->user()->can('selling_price_group.' . $key)) {
                    $allowed_group_prices[$key] = $value;
                }
            }

            $warehouseInfo_all = WarehouseInfo::where("product_id",$id)->get();
            
            $list = [];

            foreach($warehouseInfo_all as $ware){
                $warehouse = Warehouse::find($ware->store_id);
                $list[$ware->store_id] = $warehouse->name; 
            }
            
            $list_trans = [];
            $list_purchase = [];
            
            $list_trans_d = [];
            $list_sell = [];
            
            foreach($trans as $tran){
                $list_trans[$tran->id] = $tran->reciept_no; 
                $list_purchase[$tran->id] = $tran->ref_no; 
            }
            foreach($trans_d as $tran_d){
                $list_trans_d[$tran_d->id] = $tran_d->reciept_no; 
                $list_sell[$tran_d->id] = $tran_d->invoice_no; 
            }

            $transactionRecieved = WarehouseInfo::where("product_id",$id)->get();

            $group_price_details = [];

            foreach ($product->variations as $variation) {
                foreach ($variation->group_prices as $group_price) {
                    $group_price_details[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
                }
            }

            $rack_details = $this->productUtil->getRackDetails($business_id, $id, true);

            $combo_variations = [];
            if ($product->type == 'combo') {
                $combo_variations = $this->productUtil->__getComboProductDetails($product['variations'][0]->combo_variations, $business_id);
            }

            return view('product.view-modal_stock')->with(compact(
                'product',
                'rack_details',
                'list_trans_d',
                'list_sell',
                'RecievedPrevious',
                'DeliveredPrevious',
                'supplier',
                'supplier_remain',
                'warehouseInfo_all',
                'list_trans',
                'list_purchase',
                'list',
                'allowed_group_prices',
                'group_price_details',
                'combo_variations'
            ));
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        if (!auth()->user()->can('product.view')  ) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');

            $product     = Product::where('business_id', $business_id)
                                    ->with(['brand', 'unit', 'category', 'sub_category', 'product_tax', 'variations', 'variations.product_variation', 'variations.group_prices', 'variations.media', 'product_locations', 'warranty', 'media'])
                                    ->findOrFail($id);

            $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');

           
            $price                = $product->variations[0]->default_sell_price;
            $allowed_group_prices = [];

            foreach ($price_groups as $key => $value) {
                if (auth()->user()->can('selling_price_group.' . $key)) {
                    $allowed_group_prices[$key] = $value;
                }
            }

            $group_price_details = [];

            foreach ($product->variations as $variation) {
                foreach ($variation->group_prices as $group_price) {
                    $group_price_details[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
                }
            }

            $rack_details = $this->productUtil->getRackDetails($business_id, $id, true);

            $combo_variations = [];
            if ($product->type == 'combo') {
                $combo_variations = $this->productUtil->__getComboProductDetails($product['variations'][0]->combo_variations, $business_id);
            }


            $location = BusinessLocation::where('business_id', $business_id)->first();            
            
 
            //***........ last price  purchase
            $purchaseline_last  = PurchaseLine::orderBy("id" , "desc" )->where("product_id",$id)->first();
            //***........ last price  openQuantity
            $OpenQuantity__last = OpeningQuantity::orderBy("id" , "desc" )->where("business_location_id",$location->id)->where("product_id",$id)->first();
            //***........ last price  sales
            $sells_last         = TransactionSellLine::orderBy("id" , "desc" )->where("product_id",$id)
                                                                        ->whereHas("transaction",function($query){
                                                                             $query->whereNotIn("type" , ["production_sell","Stock_Out"]);
                                                                        })->first();
            //***........ get product
            $productz        = Product::where( "id", $id)->with(['variations', 'variations.product_variation', 'variations.group_prices'])->first();
            //***........ get warehouse info     
            $warehouse_info  = WarehouseInfo::where('business_id', $business_id)->get();
            //***........ open  max min 
            $OpeningQuantity = OpeningQuantity::where("business_location_id",$location->id)->where("product_id",$id)->whereHas("transaction",function($query){
                                                    $query->whereIn("status",["opening_stock"]);
                                                })->select( DB::raw('SUM(quantity * price) as price'),DB::raw('SUM(price) as price_one'),
                                                            DB::raw('MAX(price) as MAX'),
                                                            DB::raw('MIN(price) as MIN'),
                                                            DB::raw('COUNT(id) as count')
                                                )->first();
            //***........ purchase  max min
            $purchaseline = PurchaseLine::where("product_id",$id)->whereHas("transaction",function($query){
                                                $query->whereIn("status",["received"]);
                                        })->select( DB::raw('SUM(quantity * purchase_price) as price'),
                                                    DB::raw('SUM(purchase_price) as price_one'),
                                                    DB::raw('MAX(purchase_price) as MAX'),
                                                    DB::raw('MIN(purchase_price) as MIN'), 
                                                    DB::raw('COUNT(id) as count')
                                        )->first();
           //***........ sales  max min
            $sells        = TransactionSellLine::where("product_id",$id)->whereHas("transaction",function($query){
                                            $query->whereIn("status",["final","ApprovedQuotation","delivered"]);
                                            $query->whereNotIn("type" , ["sale","production_sell","Stock_Out"]);
                                        })->select( DB::raw('SUM(unit_price) as price'),
                                                    DB::raw('MAX(unit_price) as MAX'),
                                                    DB::raw('MIN(unit_price) as MIN'),
                                                    DB::raw('COUNT(id) as count')
                                         )->first();

            $Purchase_prices = [];

            $LAST_FINAL      = round(($purchaseline_last)?$purchaseline_last->purchase_price:0,3);
            $MAX_FINAL       = round(($purchaseline->MAX)?$purchaseline->MAX:0,3);
            $MIN_FINAL       = round(($purchaseline->MIN)?$purchaseline->MIN:0,3);
            $LAST_FINAL_open = round(($OpenQuantity__last)?$OpenQuantity__last->price:0,3);
            $MAX_FINAL_open  = round(($OpeningQuantity->MAX)?$OpeningQuantity->MAX:0,3);
            $MIN_FINAL_open  = round(($OpeningQuantity->MIN)?$OpeningQuantity->MIN:0,3);
            
            if( $MAX_FINAL_open > $MAX_FINAL ){ $MAX_FINAL = $MAX_FINAL_open; }
            if( $MIN_FINAL_open > $MIN_FINAL ){ $MIN_FINAL = $MIN_FINAL_open; }
            
            if(isset($OpenQuantity__last)){
                if(isset($purchaseline_last)){
                    if( $OpenQuantity__last->created_at > $purchaseline_last->created_at){
                        $LAST_FINAL =  $LAST_FINAL_open ;
                    }
                }else{
                     $LAST_FINAL =  $LAST_FINAL_open ;
                    
                }
            }
            
            
            // $COST_FINAL = ($html)?$html:0;
            $COST_FINAL                 = \App\Product::product_cost($id);
            // $COST_FINAL = round(($html_one)?$html_one:0,3);
            $Purchase_prices["middle"]  = $COST_FINAL;
            $Purchase_prices["minimum"] = $MIN_FINAL;
            $Purchase_prices["maximum"] = $MAX_FINAL;
            $Purchase_prices["final"]   = $LAST_FINAL;
            
            $Sells_prices = [];
           
            $LAST_FINAL_sells = round(($sells_last)?$sells_last->unit_price:0,3);
            $MAX_FINAL_sells  = round(($sells->MAX)?$sells->MAX:0,3);
            $MIN_FINAL_sells  = round(($sells->MIN)?$sells->MIN:0,3);
            $COST_FINAL_sells = round( (($sells->count)!=0) ? ($sells->price)/($sells->count):0,3);

            $Sells_prices["middle"]  = $COST_FINAL_sells;
            $Sells_prices["minimum"] = $MIN_FINAL_sells;
            $Sells_prices["maximum"] = $MAX_FINAL_sells;
            $Sells_prices["final"]   = $LAST_FINAL_sells;
            

            ///..... should recieve 
            $data_re   =  Product::find($id);
              
            $lines_de =  \App\TransactionSellLine::OrderBy('id','desc')->where('product_id',$product->id)->whereHas('transaction',function($query){
                                                                        $query->whereIn('type',['sell',"sale"]);
                                                                        $query->where('status','!=','delivered');
                                                                        $query->whereIn('status', ['ApprovedQuotation' ,"draft","final" ]);
                                                                        $query->whereIn('sub_status', ["proforma","f"]);
                                                                    })->get( );
            $lines_re  =  \App\PurchaseLine::OrderBy('id','desc')->where('product_id',$product->id)->whereHas('transaction',function($query){
                                                                                    $query->where('type','purchase');
                                                                                    $query->where('status','!=','recieved');
                                                                                })->get( );
            return view('product.view-modal')->with(compact(
                        'product',
                        'productz',
                        'lines_re',
                        'price',
                        'lines_de',
                        'data_re',
                        'Sells_prices',
                        'warehouse_info',
                        'COST_FINAL',
                        'Purchase_prices',
                        'rack_details',
                        'allowed_group_prices',
                        'group_price_details',
                        'combo_variations'
            ));
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }
    }

    /**
     * Mass deletes products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDestroy(Request $request)
    {
        if (!auth()->user()->can('product.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $purchase_exist = false;

            if (!empty($request->input('selected_rows'))) {
                $business_id = $request->session()->get('user.business_id');

                $selected_rows = explode(',', $request->input('selected_rows'));

                $products = Product::where('business_id', $business_id)
                                    ->whereIn('id', $selected_rows)
                                    ->with(['purchase_lines', 'variations'])
                                    ->get();
                $deletable_products = [];

                $is_mfg_installed = $this->moduleUtil->isModuleInstalled('Manufacturing');

                DB::beginTransaction();

                foreach ($products as $product) {
                    $can_be_deleted = true;
                    //Check if product is added as an ingredient of any recipe
                    if ($is_mfg_installed) {
                        $variation_ids = $product->variations->pluck('id');

                        $exists_as_ingredient = \Modules\Manufacturing\Entities\MfgRecipeIngredient::whereIn('variation_id', $variation_ids)
                            ->exists();
                        $can_be_deleted = !$exists_as_ingredient;
                    }

                    //Delete if no purchase found
                    if (empty($product->purchase_lines->toArray()) && $can_be_deleted) {
                        //Delete variation location details
                        VariationLocationDetails::where('product_id', $product->id)
                                                    ->delete();
                        $product->delete();
                    } else {
                        $purchase_exist = true;
                    }
                }

                DB::commit();
            }

            if (!$purchase_exist) {
                $output = ['success' => 1,
                            'msg' => __('lang_v1.deleted_success')
                        ];
            } else {
                $output = ['success' => 0,
                            'msg' => __('lang_v1.products_could_not_be_deleted')
                        ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Shows form to add selling price group prices for a product.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addSellingPrices($id)
    {
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $product = Product::where('business_id', $business_id)
            ->with(['variations', 'variations.group_prices', 'variations.product_variation'])
            ->findOrFail($id);

        $price_groups = SellingPriceGroup::where('business_id', $business_id)
            ->active()
            ->get();
        $variation_prices = [];
        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $variation_prices[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
            }
        }
        return view('product.add-selling-prices')->with(compact('product', 'price_groups', 'variation_prices'));
    }
    
    public function ViewOpeningProduct($id)
    {
        
        $business_id    = request()->session()->get('user.business_id');
        $location       = BusinessLocation::where("business_id",$business_id)->first();
        $OpenQuantity   = OpeningQuantity::where("business_location_id",$location->id)->where("id",$id)->first();
        $OpenQuantity_  = OpeningQuantity::join("purchase_lines as pl","pl.id","opening_quantities.purchase_line_id")
                                          ->where("business_location_id",$location->id)
                                          ->where("opening_quantities.transaction_id",$OpenQuantity->transaction_id)
                                          ->select([
                                                "pl.id as pur_id",
                                                "pl.order_id as order_id",
                                                "opening_quantities.id",
                                                "opening_quantities.quantity",
                                                "opening_quantities.business_location_id",
                                                "opening_quantities.warehouse_id",
                                                "opening_quantities.price",
                                                "opening_quantities.date",
                                                "opening_quantities.product_id",
                                                "opening_quantities.variation_id",
                                                "opening_quantities.transaction_id",
                                                "opening_quantities.purchase_line_id"
                                          ])->orderBy("order_id" ,"asc")->get();
        
        $Transaction = Transaction::where("business_id",$business_id)->where("id",$OpenQuantity->transaction_id)->first();
    
        return view("product.viewProduct")->with(compact(["OpenQuantity","OpenQuantity_","Transaction","id"]));
    }

    /**
     * Saves selling price group prices for a product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveSellingPrices(Request $request)
    {
        // dd($request);
        if (!auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }

        //try {
            $business_id = $request->session()->get('user.business_id');
            $product = Product::where('business_id', $business_id)
                ->with(['variations'])
                ->findOrFail($request->input('product_id'));
            DB::beginTransaction();


            foreach ($product->variations as $variation) {
                $variation_group_prices = [];
                foreach ($request->input('group_prices') as $key => $value) {
                    if (isset($value[$variation->id])) {
                        $variation_group_price =
                            VariationGroupPrice::where('variation_id', $variation->id)
                                ->where('price_group_id', $key)
                                ->first();
                        if (empty($variation_group_price)) {
                            $variation_group_price = new VariationGroupPrice([
                                'variation_id' => $variation->id,
                                'price_group_id' => $key
                            ]);
                        }

                        $variation_group_price->price_inc_tax = $this->productUtil->num_uf($value[$variation->id]);
                        $variation_group_prices[] = $variation_group_price;
                    }
                }


                if (!empty($variation_group_prices)) {
                    $variation->group_prices()->saveMany($variation_group_prices);
                }
            }
            //Update product updated_at timestamp
            $product->touch();

            DB::commit();
            $output = ['success' => 1,
                'msg' => __("lang_v1.updated_success")
            ];
        /*} catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }*/

        if ($request->input('submit_type') == 'submit_n_add_opening_stock') {
            return redirect()->action(
                'OpeningStockController@add',
                ['product_id' => $product->id]
            );
        } elseif ($request->input('submit_type') == 'save_n_add_another') {
            return redirect()->action(
                'ProductController@create'
            )->with('status', $output);
        }

        return redirect('products')->with('status', $output);
    }

    public function viewGroupPrice($id)
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $product = Product::where('business_id', $business_id)
                            ->where('id', $id)
                            ->with(['variations', 'variations.product_variation', 'variations.group_prices'])
                            ->first();

        $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');

        $allowed_group_prices = [];
        foreach ($price_groups as $key => $value) {
            if (auth()->user()->can('selling_price_group.' . $key)) {
                $allowed_group_prices[$key] = $value;
            }
        }

        $group_price_details = [];

        foreach ($product->variations as $variation) {
            foreach ($variation->group_prices as $group_price) {
                $group_price_details[$variation->id][$group_price->price_group_id] = $group_price->price_inc_tax;
            }
        }

        return view('product.view-product-group-prices')->with(compact('product', 'allowed_group_prices', 'group_price_details'));
    }

    /**
     * Mass deactivates products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDeactivate(Request $request)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if (!empty($request->input('selected_products'))) {
                $business_id = $request->session()->get('user.business_id');

                $selected_products = explode(',', $request->input('selected_products'));

                DB::beginTransaction();

                $products = Product::where('business_id', $business_id)
                                    ->whereIn('id', $selected_products)
                                    ->update(['is_inactive' => 1]);

                DB::commit();
            }

            $output = ['success' => 1,
                            'msg' => __('lang_v1.products_deactivated_success')
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    /**
     * Activates the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $product = Product::where('id', $id)
                                ->where('business_id', $business_id)
                                ->update(['is_inactive' => 0]);

                $output = ['success' => true,
                                'msg' => __("lang_v1.updated_success")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = ['success' => false,
                                'msg' => __("messages.something_went_wrong")
                            ];
            }

            return $output;
        }
    }

    /**
     * Deletes a media file from storage and database.
     *
     * @param  int  $media_id
     * @return json
     */
    public function deleteMedia($media_id)
    {
        if (!auth()->user()->can('product.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                Media::deleteMedia($business_id, $media_id);

                $output = ['success' => true,
                                'msg' => __("lang_v1.file_deleted_successfully")
                            ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = ['success' => false,
                                'msg' => __("messages.something_went_wrong")
                            ];
            }

            return $output;
        }
    }

    public function getProductsApi($id = null)
    {
        try {
            $api_token = request()->header('API-TOKEN');
            $filter_string = request()->header('FILTERS');
            $order_by = request()->header('ORDER-BY');

            parse_str($filter_string, $filters);

            $api_settings = $this->moduleUtil->getApiSettings($api_token);

            $limit = !empty(request()->input('limit')) ? request()->input('limit') : 10;

            $location_id = $api_settings->location_id;

            $query = Product::where('business_id', $api_settings->business_id)
                            ->active()
                            ->with(['brand', 'unit', 'category', 'sub_category',
                                'product_variations', 'product_variations.variations', 'product_variations.variations.media',
                                'product_variations.variations.variation_location_details' => function ($q) use ($location_id) {
                                    $q->where('location_id', $location_id);
                                }]);

            if (!empty($filters['categories'])) {
                $query->whereIn('category_id', $filters['categories']);
            }

            if (!empty($filters['brands'])) {
                $query->whereIn('brand_id', $filters['brands']);
            }

            if (!empty($filters['category'])) {
                $query->where('category_id', $filters['category']);
            }

            if (!empty($filters['sub_category'])) {
                $query->where('sub_category_id', $filters['sub_category']);
            }

            if ($order_by == 'name') {
                $query->orderBy('name', 'asc');
            } elseif ($order_by == 'date') {
                $query->orderBy('created_at', 'desc');
            }

            if (empty($id)) {
                $products = $query->paginate($limit);
            } else {
                $products = $query->find($id);
            }
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($products);
    }

    public function getVariationsApi()
    {
        try {
            $api_token = request()->header('API-TOKEN');
            $variations_string = request()->header('VARIATIONS');

            if (is_numeric($variations_string)) {
                $variation_ids = intval($variations_string);
            } else {
                parse_str($variations_string, $variation_ids);
            }

            $api_settings = $this->moduleUtil->getApiSettings($api_token);
            $location_id = $api_settings->location_id;
            $business_id = $api_settings->business_id;

            $query = Variation::with([
                                'product_variation',
                                'product' => function ($q) use ($business_id) {
                                    $q->where('business_id', $business_id);
                                },
                                'product.unit',
                                'variation_location_details' => function ($q) use ($location_id) {
                                    $q->where('location_id', $location_id);
                                }
                            ]);

            $variations = is_array($variation_ids) ? $query->whereIn('id', $variation_ids)->get() : $query->where('id', $variation_ids)->first();
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return $this->respondWentWrong($e);
        }

        return $this->respond($variations);
    }

    /**
     * Shows form to edit multiple products at once.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkEdit(Request $request)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        $selected_products_string = $request->input('selected_products');
        if (!empty($selected_products_string)) {
            $selected_products = explode(',', $selected_products_string);
            $business_id = $request->session()->get('user.business_id');

            $products = Product::where('business_id', $business_id)
                                ->whereIn('id', $selected_products)
                                ->with(['variations', 'variations.product_variation', 'variations.group_prices', 'product_locations'])
                                ->get();

            $all_categories = Category::catAndSubCategories($business_id);

            $categories = [];
            $sub_categories = [];
            foreach ($all_categories as $category) {
                $categories[$category['id']] = $category['name'];

                if (!empty($category['sub_categories'])) {
                    foreach ($category['sub_categories'] as $sub_category) {
                        $sub_categories[$category['id']][$sub_category['id']] = $sub_category['name'];
                    }
                }
            }

            $brands = Brands::forDropdown($business_id);

            $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
            $taxes = $tax_dropdown['tax_rates'];
            $tax_attributes = $tax_dropdown['attributes'];

            $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');
            $business_locations = BusinessLocation::forDropdown($business_id);

            return view('product.bulk-edit')->with(compact(
                'products',
                'categories',
                'brands',
                'taxes',
                'tax_attributes',
                'sub_categories',
                'price_groups',
                'business_locations'
            ));
        }
    }

    /**
     * Updates multiple products at once.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdate(Request $request)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $products = $request->input('products');
            $business_id = $request->session()->get('user.business_id');

            DB::beginTransaction();
            foreach ($products as $id => $product_data) {
                $update_data = [
                    'category_id' => $product_data['category_id'],
                    'sub_category_id' => $product_data['sub_category_id'],
                    'brand_id' => $product_data['brand_id'],
                    'tax' => $product_data['tax'],
                ];

                //Update product
                $product = Product::where('business_id', $business_id)
                                ->findOrFail($id);

                $product->update($update_data);

                //Add product locations
                $product_locations = !empty($product_data['product_locations']) ?
                                    $product_data['product_locations'] : [];
                $product->product_locations()->sync($product_locations);

                $variations_data = [];

                //Format variations data
                foreach ($product_data['variations'] as $key => $value) {
                    $variation = Variation::where('product_id', $product->id)->findOrFail($key);
                    $variation->default_purchase_price = $this->productUtil->num_uf($value['default_purchase_price']);
                    $variation->dpp_inc_tax = $this->productUtil->num_uf($value['dpp_inc_tax']);
                    $variation->profit_percent = $this->productUtil->num_uf($value['profit_percent']);
                    $variation->default_sell_price = $this->productUtil->num_uf($value['default_sell_price']);
                    $variation->sell_price_inc_tax = $this->productUtil->num_uf($value['sell_price_inc_tax']);
                    $variations_data[] = $variation;

                    //Update price groups
                    if (!empty($value['group_prices'])) {
                        foreach ($value['group_prices'] as $k => $v) {
                            VariationGroupPrice::updateOrCreate(
                                ['price_group_id' => $k, 'variation_id' => $variation->id],
                                ['price_inc_tax' => $this->productUtil->num_uf($v)]
                            );
                        }
                    }
                }
                $product->variations()->saveMany($variations_data);
            }
            DB::commit();

            $output = ['success' => 1,
                            'msg' => __("lang_v1.updated_success")
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect('products')->with('status', $output);
    }

    /**
     * Adds product row to edit in bulk edit product form
     *
     * @param  int  $product_id
     * @return \Illuminate\Http\Response
     */
    public function getProductToEdit($product_id)
    {
        if (!auth()->user()->can('product.update') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');

        $product = Product::where('business_id', $business_id)
                            ->with(['variations', 'variations.product_variation', 'variations.group_prices'])
                            ->findOrFail($product_id);
        $all_categories = Category::catAndSubCategories($business_id);

        $categories = [];
        $sub_categories = [];
        foreach ($all_categories as $category) {
            $categories[$category['id']] = $category['name'];

            if (!empty($category['sub_categories'])) {
                foreach ($category['sub_categories'] as $sub_category) {
                    $sub_categories[$category['id']][$sub_category['id']] = $sub_category['name'];
                }
            }
        }

        $brands = Brands::forDropdown($business_id);

        $tax_dropdown = TaxRate::forBusinessDropdown($business_id, true, true);
        $taxes = $tax_dropdown['tax_rates'];
        $tax_attributes = $tax_dropdown['attributes'];

        $price_groups = SellingPriceGroup::where('business_id', $business_id)->active()->pluck('name', 'id');

        return view('product.partials.bulk_edit_product_row')->with(compact(
            'product',
            'categories',
            'brands',
            'taxes',
            'tax_attributes',
            'sub_categories',
            'price_groups'
        ));
    }

    /**
     * Gets the sub units for the given unit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $unit_id
     * @return \Illuminate\Http\Response
     */
    public function getSubUnits(Request $request)
    {
        if (!empty($request->input('unit_id'))) {
            $unit_id = $request->input('unit_id');
            $business_id = $request->session()->get('user.business_id');
            $sub_units = $this->productUtil->getSubUnits($business_id, $unit_id, true);

            //$html = '<option value="">' . __('lang_v1.all') . '</option>';
            $html = '';
            if (!empty($sub_units)) {
                foreach ($sub_units as $id => $sub_unit) {
                    if($id != $unit_id){
                        $html .= '<option value="' . $id .'">' .$sub_unit['name'] . '</option>';
                    }
                }
            }

            return $html;
        }
    }

    public function updateProductLocation(Request $request)
    {
        if (!auth()->user()->can('product.update')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $selected_products = $request->input('products');
            $update_type = $request->input('update_type');
            $location_ids = $request->input('product_location');

            $business_id = $request->session()->get('user.business_id');

            $product_ids = explode(',', $selected_products);

            $products = Product::where('business_id', $business_id)
                                ->whereIn('id', $product_ids)
                                ->with(['product_locations'])
                                ->get();
            DB::beginTransaction();
            foreach ($products as $product) {
                $product_locations = $product->product_locations->pluck('id')->toArray();

                if ($update_type == 'add') {
                    $product_locations = array_unique(array_merge($location_ids, $product_locations));
                    $product->product_locations()->sync($product_locations);
                } elseif ($update_type == 'remove') {
                    foreach ($product_locations as $key => $value) {
                        if (in_array($value, $location_ids)) {
                            unset($product_locations[$key]);
                        }
                    }
                    $product->product_locations()->sync($product_locations);
                }
            }
            DB::commit();
            $output = ['success' => 1,
                            'msg' => __("lang_v1.updated_success")
                        ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return $output;
    }

    public function productStockHistory($id)
    {
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {

            $stock_details = $this->productUtil->getVariationStockDetails($business_id, $id, request()->input('location_id'));
            $stock_history = $this->productUtil->getVariationStockHistory($business_id, $id, request()->input('location_id'));
    
            return view('product.stock_history_details')
                ->with(compact('stock_details', 'stock_history'));
        }

        $product = Product::where('business_id', $business_id)
                            ->with(['variations', 'variations.product_variation']) 
                            ->findOrFail($id);

        //Get all business locations
        $business_locations = BusinessLocation::forDropdown($business_id);


        return view('product.stock_history')
                ->with(compact('product', 'business_locations'));
    }

    /* show view for add and edit barcode 6 function*/
    public function addbarcode($id){
        if (!auth()->user()->can('product.view')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $product = Product::where('business_id', $business_id)->findOrFail($id);
        $barcodes=Product::where('products.id','=',$id)
                  ->join('variations','products.id','=','variations.product_id')
                  ->get();

        if($product->type=='single'){
            $variation=Variation::where('product_id','=',$id)->value('id');
        }else{
            $variation=Variation::where('product_id','=',$id)->pluck('name','id');
            $variation->prepend('إختر',0);
        }


        $barcode_default =  $this->productUtil->barcode_default();
        $barcode_types = $this->barcode_types;


        return view('product.barcode',['product'=>$product,'barcodes'=>$barcodes,'barcode_types'=>$barcode_types,'barcode_default'=>$barcode_default,'variation'=>$variation]);
    }

    public function getproductbarcode(Request $request){
        $business_id = request()->session()->get('user.business_id');
        //Barcode from variations
        $output='';
        $data=Variation::where('product_id','=',$request->productid)->get();
        $name='';

        /* Barcode form variation main barcode */
        foreach ($data as $row){

            $name=$row->name=='DUMMY'?'':$row->name;

            $output .='<tr id="'.$row->id.'">
                           <td>
                            <input type="text"  value="'.$name.'" class="form-control" readonly style="max-width: 100px">
                           </td>
                         <td>
                             <input type="text"  id="barcode_'.$row->id.'" value="'.$row->sub_sku.'" class="form-control" style="max-width: 200px">
                          </td>
                         
                           <td>
                                <button onclick="updatebarcode('.$row->id.')"  class="btn  btn-primary btn-modal"> <i class="glyphicon glyphicon-save"></i> '. __("messages.save").'</button> ';

            $output .='</td></tr>';
        }


        $data=Variation::where('variations.product_id',$request->productid)
            ->join('product_barcode','variations.id','=','product_barcode.variation_id')
            ->select('variations.name','variations.id as variation_id','product_barcode.barcode','product_barcode.ismain','product_barcode.type','product_barcode.id as barcode_id')
            ->orderby('variation_id')
            ->get();

       /* Barcode From product_barcode  */
        foreach ($data as $row){
            $output .='<tr id="'.$row->barcode_id.'">
                           <td>
                            <input type="text"  value="'.$row->name.'" class="form-control" readonly style="max-width: 100px">
                           </td>
                         <td>
                             <input type="text"  id="barcode_'.$row->barcode_id.'" value="'.$row->barcode.'" class="form-control" style="max-width: 200px">
                          </td>
                         
                           <td>
                                <button onclick="updatebarcode2('.$row->barcode_id.')"  class="btn  btn-primary btn-modal"> <i class="glyphicon glyphicon-save"></i> '. __("messages.save").'</button> ';

            if($row->ismain==0)
                $output .=' <button onclick="deletdata('.$row->barcode_id.')" class="btn  btn-danger delete_asset_button"> <i class="glyphicon glyphicon-trash"></i> '. __("messages.delete").'</button> ';
            $output .='</td></tr>';
        }


        return $output;

    }


    /* Add new Barcode to product_barcode */
    public function savebarcode(Request $request){

        $barcode=$request->barcode ;
        $id=$request->id;
        $business_id = request()->session()->get('user.business_id');
        $checkbarcode=Variation::where('sub_sku',$barcode)
                                 ->where('business_id','=',$business_id)
                                 ->join('products','variations.product_id','=','products.id')
                                 ->count();

        $checkbarcode2=product_barcode::where('barcode','=',$barcode)
                                        ->where('business_id','=',$business_id)
                                        ->count();
        if($checkbarcode>0 || $checkbarcode2>0){
            $output = ['success' => 0,
                'msg' =>'عفوا الباركود موجود قبل ذلك'
            ];
            return $output;
        }

        if($id==0){
            //add new barcode for this product
            $barcode=product_barcode::create([
                'variation_id'=>$request->variation,
                'business_id'=>$business_id,
                'barcode'=>$request->barcode,
                'type'=>$request->barcode_type,
                'ismain'=>0,
            ]);
            $output = ['success' => 1,
                'msg' =>'تم إضافة الباركود بنجاح'
            ];

        }

         if($id>0){
            $barcode=product_barcode::where('variation_id',$id)->get;
            $barcode->barcode=$request->barcode;
            $barcode->save();


            $output = ['success' => 1,
                'msg' =>'تم إضافة الباركود بنجاح'
            ];
        }



        return $output;
    }

    public function updatebarcode(Request $request){

        $barcode=$request->barcode ;
        $id=$request->id;
        $variation_id=$request->variation_id;
        $business_id = request()->session()->get('user.business_id');

        $checkbarcode=Variation::where('sub_sku',$barcode)
            ->where('business_id','=',$business_id)
            ->where('variations.id','!=',$variation_id)
            ->join('products','variations.product_id','=','products.id')
            ->count();

        $checkbarcode2=product_barcode::where('barcode',$barcode)
            ->where('business_id','=',$business_id)
            ->where('product_barcode.id','!=',$id)
            ->join('variations','product_barcode.variation_id','=','variations.id')
            ->count();


        if($checkbarcode>0 || $checkbarcode2>0){
            $output = ['success' => 0,
                'msg' =>'عفوا الباركود موجود قبل ذلك'
            ];
            return $output;
        }


//$id is variation id

            $barcode=Variation::where('id','=',$id)->first();
            $barcode->sub_sku=$request->barcode;
            $barcode->save();
            $output = ['success' => 1,
                'msg' =>'تم تعديل الباركود بنجاح'
            ];

        return $output;
    }
    public function updatebarcode2(Request $request){

        $barcode=$request->barcode ;
        $id=$request->id;
        $variation_id=$request->variation_id;
        $business_id = request()->session()->get('user.business_id');

        $checkbarcode=Variation::where('sub_sku',$barcode)
            ->where('business_id','=',$business_id)
            ->where('variations.id','!=',$variation_id)
            ->join('products','variations.product_id','=','products.id')
            ->count();

        $checkbarcode2=product_barcode::where('barcode',$barcode)
            ->where('business_id','=',$business_id)
            ->where('product_barcode.id','!=',$id)
            ->join('variations','product_barcode.variation_id','=','variations.id')
            ->count();


        if($checkbarcode>0 || $checkbarcode2>0){
            $output = ['success' => 0,
                'msg' =>'عفوا الباركود موجود قبل ذلك'
            ];
            return $output;
        }
        //$id is variation id
        $barcode=product_barcode::where('id','=',$id)->first();
        $barcode->barcode=$request->barcode;
        $barcode->save();
        $output = ['success' => 1,
            'msg' =>'تم تعديل الباركود بنجاح'
        ];

        return $output;
    }

    public function deletebarcode(Request $request){

        $barcode=product_barcode::where('id',$request->id)->delete();
        $output = ['success' => 1,
            'msg' =>'تم حذف الباركود بنجاح'.$request->id
        ];
        return   $output;
    }

    
    public function cancelFView($id) {
        return "";
    }
    
    public function addFView($id) {
        return "";
        
    }
    

    public function products()
    {
        if (!auth()->user()->can('product.view') && !auth()->user()->can('product.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $selling_price_group_count = SellingPriceGroup::countSellingPriceGroups($business_id);

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
                DB::raw('SUM(vld.qty_available) as current_stock'),
                DB::raw('MAX(v.sell_price_inc_tax) as max_price'),
                DB::raw('MIN(v.sell_price_inc_tax) as min_price'),
                DB::raw('MAX(v.dpp_inc_tax) as max_purchase_price'),
                DB::raw('MIN(v.dpp_inc_tax) as min_purchase_price')

            )->groupBy('products.id');

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

            return Datatables::of($products)
                ->addColumn(
                    'product_locations',
                    function ($row) {
                        return $row->product_locations->implode('name', ', ');
                    }
                )
                ->editColumn('category', '{{$category}} @if(!empty($sub_category))<br/> -- {{$sub_category}}@endif')
                ->addColumn(
                    'action',
                    function ($row) use ($selling_price_group_count) {
                        $html =
                            '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu"><li><a href="' . action('LabelsController@show') . '?product_id=' . $row->id . '" data-toggle="tooltip" title="' . __('lang_v1.label_help') . '"><i class="fa fa-barcode"></i> ' . __('barcode.labels') . '</a></li>';

                        if (auth()->user()->can('product.view')) {
                            $html .=
                                '<li><a href="' . action('ProductController@view', [$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        }

                        if (auth()->user()->can('product.update')) {
                            $html .=
                                '<li><a href="' . action('ProductController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }

                        if (auth()->user()->can('product.delete')) {
                            $html .=
                                '<li><a href="' . action('ProductController@destroy', [$row->id]) . '" class="delete-product"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }


                        if (auth()->user()->can('product.update')) {
                            $html .=
                                '<li><a href="' . action('ProductController@addbarcode', [$row->id]) . '" ><i class="fa fa-plus-circle"></i> ' . __("messages.morebarcode") . '</a></li>';
                        }


                        if ($row->is_inactive == 1) {
                            $html .=
                                '<li><a href="' . action('ProductController@activate', [$row->id]) . '" class="activate-product"><i class="fas fa-check-circle"></i> ' . __("lang_v1.reactivate") . '</a></li>';
                        }

                        $html .= '<li class="divider"></li>';

                        if ($row->enable_stock == 1 && auth()->user()->can('product.opening_stock')) {
                            $html .=
                                '<li><a href="#" data-href="' . action('OpeningStockController@add', ['product_id' => $row->id]) . '" class="add-opening-stock"><i class="fa fa-database"></i> ' . __("lang_v1.add_edit_opening_stock") . '</a></li>';
                        }

                        if (auth()->user()->can('product.view')) {
                            $html .=
                                '<li><a href="' . action('ProductController@productStockHistory', [$row->id]) . '"><i class="fas fa-history"></i> ' . __("lang_v1.product_stock_history") . '</a></li>';
                        }

                        if (auth()->user()->can('product.create')) {

                            if ($selling_price_group_count > 0) {
                                $html .=
                                    '<li><a href="' . action('ProductController@addSellingPrices', [$row->id]) . '"><i class="fas fa-money-bill-alt"></i> ' . __("lang_v1.add_selling_price_group_prices") . '</a></li>';
                            }

                            $html .=
                                '<li><a href="' . action('ProductController@create', ["d" => $row->id]) . '"><i class="fa fa-copy"></i> ' . __("lang_v1.duplicate_product") . '</a></li>';
                        }

                        if (!empty($row->media->first())) {

                            $html .=
                                '<li><a href="' . $row->media->first()->display_url . '" download="'.$row->media->first()->display_name.'"><i class="fas fa-download"></i> ' . __("lang_v1.product_brochure") . '</a></li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->editColumn('product', function ($row) {
                    $product = $row->is_inactive == 1 ? $row->product . ' <span class="label bg-gray">' . __("lang_v1.inactive") .'</span>' : $row->product;

                    $product = $row->not_for_selling == 1 ? $product . ' <span class="label bg-gray">' . __("lang_v1.not_for_selling") .
                        '</span>' : $product;

                    return $product;
                })
                ->editColumn('image', function ($row) {
                      
                    if($row->image){
                            $image = $row->image;
                    }else {
                            $image = "";
                    
                        
                    }
                    return '<div style="display: flex;"><img src="' . $image . '" alt="Product image" class="product-thumbnail-small"></div>';
                })
                ->editColumn('type', '@lang("lang_v1." . $type)')
                ->addColumn('mass_delete', function ($row) {
                    return  '<input type="checkbox" class="row-select" value="' . $row->id .'">' ;
                })
                ->editColumn('current_stock', '@if($enable_stock == 1) {{@number_format($current_stock)}} @else -- @endif {{$unit}}')
                ->addColumn(
                    'purchase_price',
                    '<div style="white-space: nowrap;">@format_currency($min_purchase_price) @if($max_purchase_price != $min_purchase_price && $type == "variable") -  @format_currency($max_purchase_price)@endif </div>'
                )
                ->addColumn(
                    'selling_price',
                    '<div style="white-space: nowrap;">@format_currency($min_price) @if($max_price != $min_price && $type == "variable") -  @format_currency($max_price)@endif </div>'
                )
                ->filterColumn('products.sku', function ($query, $keyword) {
                    $query->whereHas('variations', function($q) use($keyword){
                        $q->where('sub_sku', 'like', "%{$keyword}%");
                    })
                        ->orWhere('products.sku', 'like', "%{$keyword}%");
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("product.view")) {
                            return  action('ProductController@view', [$row->id]) ;
                        } else {
                            return '';
                        }
                    }])
                ->rawColumns(['action', 'image', 'mass_delete', 'product', 'selling_price', 'purchase_price', 'category'])
                ->make(true);
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

        return view('product.products')
            ->with(compact(
                'rack_enabled',
                'categories',
                'brands',
                'units',
                'taxes',
                'business_locations',
                'show_manufacturing_data',
                'pos_module_data',
                'is_woocommerce'
            ));
    }
        /**
     * add the opening resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function AddOpeningProduct(Request $request)
    {
        if (!auth()->user()->can('purchase.create') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        \App\Models\ItemMove::delete_all_purchase_not_connect($business_id);
        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse();
            }elseif (!$this->moduleUtil->isQuotaAvailable('products', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action('ProductController@index'));
            }
        } elseif (!$this->moduleUtil->isQuotaAvailable('products', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('products', $business_id, action('ProductController@index'));
        }

        $taxes = TaxRate::where('business_id', $business_id)
                        ->ExcludeForTaxGroup()
                        ->get();


        $orderStatuses = $this->productUtil->orderStatuses();

        $business_locations = BusinessLocation::forDropdown($business_id, false, true);
       
        $mainstore = Warehouse::where('business_id', $business_id)->select(['name','id','status','mainStore','description'])->get();

        $mainstore_categories = [];

        if (!empty($mainstore)) {
            foreach ($mainstore as $mainstor) {
            
                if($mainstor->status != 0){
                    $mainstore_categories[$mainstor->id] = $mainstor->name;

                }
            }
                   
        }
        $bl_attributes = $business_locations['attributes'];

        $business_locations = $business_locations['locations'];

        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;

        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }

        $customer_groups = CustomerGroup::forDropdown($business_id);

        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;

        $payment_types = $this->productUtil->payment_types(null, true);

        $childs = \App\Models\Warehouse::childs($business_id);
        #2024-8-6
        $list_of_prices = \App\Product::getListPrices();
        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);
        
        return view('product.AddOpening')
            ->with('yes','tets')
            ->with(compact('taxes', 'list_of_prices' ,'orderStatuses', 'childs' ,'business_locations','mainstore_categories', 'currency_details', 'default_purchase_status', 'customer_groups', 'types', 'shortcuts', 'payment_line', 'payment_types', 'accounts', 'bl_attributes'));
        
    }
    /**
     * add the opening resource.
     * 
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function  OpeningProduct(Request $request)
    { 
         
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create') && !auth()->user()->can('view_own_purchase') ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $childs = Warehouse::childs($business_id);
        $allData  =  \App\Models\OpeningQuantity::OrderBy('id','desc')->where(function($query) use($request){
            if ($request->store_id) {
                $query->where('warehouse_id',$request->store_id);
            }
            if ($request->location_id) {
                $query->where('business_location_id',$request->location_id);
            }

        })->get();
        return view('product.OpeningProduct')
            ->with(compact('business_locations', 'childs','allData'));
    }
    public function viewUnrecieved($id)
    {
        $data   =  Product::find($id);
        $lines  =  PurchaseLine::OrderBy('id','desc')->where('product_id',$id)->whereHas('transaction',function($query){
                                $query->where('type','purchase');
                                $query->where('status','!=','recieved');
                            })->get();
        return view('product.un_recieved_details')
                ->with('product',$data)
                ->with('items',$lines);
    }

    public function movement($id)
    {
        if (!auth()->user()->can('product.view')   ) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
      
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
 
        $categories = Category::forDropdown($business_id, 'product');

       

        $units = Unit::forDropdown($business_id);

         

        $business_locations = BusinessLocation::forDropdown($business_id);
        $business_locations->prepend(__('lang_v1.none'), 'none');
         $allData =  Movementwarehouse::OrderBy('id','desc')->where('product_id',$id)->paginate(2);
         $data =  Product::find($id);
         return view("product.productMovment")->with(compact("id" , "categories" ,  "currency_details" , "business_locations"))
                    ->with('data',$data)
                    ->with('allData',$allData)
                    ;
    }

    public function viewDelivered($id)
    {
         
        $data   =  Product::find($id);
        $lines  =  \App\TransactionSellLine::OrderBy('id','desc')->where('product_id',$id)->whereHas('transaction',function($query){
                        $query->whereIn('type',["sale",'sell']);
                        $query->whereIn('status',['ApprovedQuotation','final','delivered','draft']);
                        $query->whereIn('sub_status',['final','f','proforma']);
                     })->get()->where('margin','>',0);
        return view('product.un_deliverd_details')
        ->with('product',$data)
        ->with('items',$lines);
    }
        //........eb
    public function viewDeliveredf($id,$f)
    {
        $data  =  Product::find($id);
        $lines  =  \App\TransactionSellLine::OrderBy('id','desc')->where('product_id',$id)->whereHas('transaction',function($query){
                        $query->whereIn('type',['sell',"sale"]);
                        $query->where('status','!=','delivered');
                        $query->whereIn('status', ['ApprovedQuotation' ,"draft"]);
                        $query->whereIn('sub_status', ["proforma"]);
                        // $query->whereIn('status', ['ApprovedQuotation' ,"draft","final" ]);
                        // $query->whereIn('sub_status', ["proforma","f"]);
                    })->get()->where('margin','>',0);
        return view('product.un_deliverd_details')
        ->with('product',$data)
        ->with('items',$lines);
    }
    public function getTrans(Request $request,$id){
        $data       =  Product::find($id); 
        $list       =  []; 
        $stock_outs =  \App\Transaction::where("type","Stock_Out")->whereHas("sell_lines",function($q) use($id){
                                    $q->where("product_id",$id);
            
        })->get();
        foreach($stock_outs as $item){
            $stock_ins  =  \App\Transaction::where("id",$item->id+1)->first();
            $sum        =  $stock_ins->purchase_lines->where("product_id",$id)->sum("quantity");
            $line       =  $stock_ins->purchase_lines->where("product_id",$id)->first();
            if($line != null){
                if($line->product_id == $id){
                    $list[$item->id] = [
                        "ref_no"    => $item->ref_no,  
                        "qty"       => $sum,  
                        "store_out" => $item->store_from->name,  
                        "store_in"  => $line->transaction->store_to->name,
                        "date"      => $item->transaction_date
                    ];
                }
            }
        }
       
        return view('product.transfered_details')
                    ->with('product',$data)
                    ->with('list',$list);
    }
    //................eb

    public function changeFeature() {
        
            
        $id = request()->input("id");
        $style = request()->input("Style");
         
        if($style != null){
             $product = \App\Product::find($id);
            if(!empty($product)){
                if($style == "Feature"){
                    
                    $product->feature = 1;
                }elseif($style == "Discount"){
                    
                    $product->ecm_discount = 1;
                }elseif( $style == "Collection"){
                    
                    $product->ecm_collection = 1;
                }else{
                    $product->ecommerce = 1;
                }
                $product->save();
                $output = [
                        "success"=>1,
                        "msg"=>"Added Successfully",
                    ];
                return redirect()->back()->with("status",$output);
            }else{
                $output = [
                        "success"=>0,
                        "msg"=>"Failed",
                    ];
                return redirect()->back()->with("status",$output);
            }
        }else{
            $product = \App\Product::find($id);
            if(!empty($product)){
                $product->ecommerce = 1;
                $product->save();
                return "success";
            }else{
                return null;
            }
        }
   
    }
    public function unChangeFeature() {
        
         
        $id    = request()->input("id");
        $style = request()->input("Style");
        if($style != null){
            $product = \App\Product::find($id);
            if(!empty($product)){
                if($style == "Feature"){
                        
                    $product->feature = 0;
                }elseif($style == "Discount"){
                    
                    $product->ecm_discount = 0;
                }elseif( $style == "Collection"){
                    
                    $product->ecm_collection = 0;
                }else{
                    $product->ecommerce = 0;
                }
                $product->save();
                $output = [
                        "success"=>1,
                        "msg"=>"Removed Successfully",
                    ];
                return redirect()->back()->with("status",$output);
            }else{
                $output = [
                        "success"=>0,
                        "msg"=>"Failed",
                    ];
                return redirect()->back()->with("status",$output);
            }
        }else{
            $product = \App\Product::find($id);
            if(!empty($product)){
                $product->ecommerce = 0;
                $product->save();
                return "success";
            }else{
                return null;
            }
        }
    }
     
     
    
    public function refreshAll(){
        \App\Models\ItemMove::refreshAll();
        return redirect()->back();
    }
    public function removeImage() {
        $product_id = request()->input("id");
        $product = \App\Product::find($product_id); 
        if (!empty($product->image_path_second) && file_exists($product->image_path_second)) {
            unlink($product->image_path_second);
            $product->image = null;
            $product->update();
        }
    }
    public function import(Request $request){
          
         
        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        $import        = new ProductImage;
        $File          = $request->file('file');
        Excel::import($import,$File);
        $data          = $import->data;
        $index         = 0;
        $indexItem     = 0;
        $list_of_items = [];
        $list_of_name  = [];
        $list          = [];
        foreach($data as $key => $row){
            if($key == 0){
                foreach($row as $k => $i){
                    if($i == 'الصورة'){
                        $index = $k;
                    }
                    if($i == 'المادة'){
                        $indexItem = $k;
                    }
                }
            }else{
                if($row[$index] != null){
                    $list_of_items[] = [
                        "key"       => $key,
                        "itemKey"   => $index,
                        "value"     => $row[$index]
                    ];
                }
                if($row[$indexItem] != null){
                    $list_of_name[]  = [
                        "key"       => $key,
                        "itemKey"   => $indexItem,
                        "value"     => $row[$indexItem]
                    ];
                }
            }
        }
        foreach($list_of_items as $firstName){
            foreach($list_of_name as $secondName){
                if(strpos($firstName['value'],str_replace(',','',preg_replace('/\s+/','',$secondName['value']))) !== false){
                    $list[] = [
                        // "1"=>$firstName['value'],
                        // "2"=>str_replace(',','',preg_replace('/\s+/','',$secondName['value'])),
                        // "3"=>strpos($firstName['value'],str_replace(',','',preg_replace('/\s+/','',$secondName['value']))),
                        "0" => $firstName['value'],
                        "1" => $secondName['value'],
                    ];
                }
            }
        }

        # .....................................
        
        $file_name     = $File->getClientOriginalName();
        $File->storeAs('/xls',$file_name);
        $final_file    = public_path("/uploads/xls"."/".$file_name) ;
        // 
        $spreadsheet         = IOFactory::load($final_file);
        $sheet               = $spreadsheet->getActiveSheet();
        // 
        $columnToInsertAfter = 'A';
        $newColumnIndex      = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnToInsertAfter) + 1;
        $newColumnLetter     = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($newColumnIndex);
        // 
        $sheet->insertNewColumnBefore($newColumnLetter, 1);
        // 
        $columnToInsertAfterThird = 'C';
        $newColumnIndexThird      = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnToInsertAfterThird);
        $newColumnLetterThird     = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($newColumnIndexThird);
        $highestRow = $sheet->getHighestRow();
        for ($row = 2; $row <= $highestRow; $row++) {
            foreach($list as $object){ 
                if($sheet->getCell($newColumnLetterThird.$row)->getValue() == $object[1]){
                     $sheet->setCellValue($newColumnLetter . $row, $object[0]);
                 }
            }
        }
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $newFilePath = public_path('/uploads/xls/modified_excel_file.xlsx');
        $writer->save($newFilePath); 
        return redirect("/products");
    }
    public function changeDescription($id) {
        $text       =  request()->input('text');
        $text       =  Crypt::decryptString($text); 
        $row        =  request()->input('line');
        $return     =  request()->input('return');
        $product    =  \App\Product::find($id);
        if($text != null){
            return view('alerts.change_description')->with(compact(["text","product","row","return"]));
        } 
    }
    public function urlDescriptionEncrypt() {
        
        if(request()->ajax()){
            $url          =  request()->input('text');
            $encryptedUrl =  Crypt::encryptString($url);  
            return response()->json([
                "success" => true,
                "text"    => $encryptedUrl
            ]);
        }
        
    }
}
