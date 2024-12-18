<?php

namespace Modules\Woocommerce\Http\Controllers;

use DB;
use App\Media;
use App\System;
use App\Product;
use App\TaxRate;
use App\Category;
use App\Business;
use App\Variation;
use App\Utils\ModuleUtil;
use App\BusinessLocation;
use App\SellingPriceGroup;
use App\VariationTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\Woocommerce\Utils\WoocommerceUtil;
use Modules\Woocommerce\Entities\WoocommerceSyncLog;


class WoocommerceController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $woocommerceUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param WoocommerceUtil $woocommerceUtil
     * @return void
     */
    public function __construct(WoocommerceUtil $woocommerceUtil, ModuleUtil $moduleUtil)
    {
        $this->woocommerceUtil = $woocommerceUtil;
        $this->moduleUtil      = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        try {
            $business_id = request()->session()->get('business.id');

            if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
                abort(403, 'Unauthorized action.');
            }
                
            $tax_rates = TaxRate::where('business_id', $business_id)
                            ->get();
                            
            $woocommerce_tax_rates = ['' => __('messages.please_select')];

            $alerts = [];

            $not_synced_cat_count = Category::where('business_id', $business_id)
                                        ->whereNull('woocommerce_cat_id')
                                        ->count();

            if (!empty($not_synced_cat_count)) {
                $alerts['not_synced_cat'] = $not_synced_cat_count == 1 ? __('woocommerce::lang.one_cat_not_synced_alert') : __('woocommerce::lang.cat_not_sync_alert', ['count' => $not_synced_cat_count]);
            }

            $cat_last_sync = $this->woocommerceUtil->getLastSync($business_id, 'categories', false);
            if (!empty($cat_last_sync)) {
                $updated_cat_count = Category::where('business_id', $business_id)
                                        ->whereNotNull('woocommerce_cat_id')
                                        ->where('updated_at', '>', $cat_last_sync)
                                        ->count();
            }
            
            if (!empty($updated_cat_count)) {
                $alerts['updated_cat'] = $updated_cat_count == 1 ? __('woocommerce::lang.one_cat_updated_alert') : __('woocommerce::lang.cat_updated_alert', ['count' => $updated_cat_count]);
            }

            $products_last_synced = $this->woocommerceUtil->getLastSync($business_id, 'all_products', false);
            $not_synced_product_count = Product::where('business_id', $business_id)
                                        ->whereIn('type', ['single', 'variable'])
                                        ->whereNull('woocommerce_product_id')
                                        ->where('woocommerce_disable_sync', 0)
                                        ->count();

            if (!empty($not_synced_product_count)) {
                $alerts['not_synced_product'] = $not_synced_product_count == 1 ? __('woocommerce::lang.one_product_not_sync_alert') : __('woocommerce::lang.product_not_sync_alert', ['count' => $not_synced_product_count]);
            }
            if (!empty($products_last_synced)) {
                $updated_product_count = Product::where('business_id', $business_id)
                                        ->whereNotNull('woocommerce_product_id')
                                        ->whereIn('type', ['single', 'variable'])
                                        ->where('updated_at', '>', $products_last_synced)
                                        ->count();
            }

            if (!empty($updated_product_count)) {
                $alerts['not_updated_product'] = $updated_product_count == 1 ? __('woocommerce::lang.one_product_updated_alert') : __('woocommerce::lang.product_updated_alert', ['count' => $updated_product_count]);
            }

            $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
            if (empty($notAllowed)) {
                $response = $this->woocommerceUtil->getTaxRates($business_id);
                if (!empty($response)) {
                    foreach ($response as $r) {
                        $woocommerce_tax_rates[$r->id] = $r->name;
                    }
                }
            }
        } catch (\Exception $e) {
            $alerts['connection_failed'] = 'Unable to connect with WooCommerce, Check API settings';
        }
        

        return view('woocommerce::woocommerce.index')
                ->with(compact('tax_rates', 'woocommerce_tax_rates', 'alerts'));
    }
    /**
     * Displays form to update woocommerce api settings.
     * @return Response
     */
    public function apiSettings()
    {
        $business_id = request()->session()->get('business.id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.access_woocommerce_api_settings')))) {
            abort(403, 'Unauthorized action.');
        }

        $default_settings = [
            'woocommerce_app_url' => '',
            'woocommerce_consumer_key' => '',
            'woocommerce_consumer_secret' => '',
            'location_id' => null,
            'default_tax_class' => '',
            'product_tax_type' => 'inc',
            'default_selling_price_group' => '',
            'product_fields_for_create' => ['category', 'quantity'],
            'product_fields_for_update' => ['name', 'price', 'category', 'quantity'],
        ];

        $price_groups = SellingPriceGroup::where('business_id', $business_id)
                        ->pluck('name', 'id')->prepend(__('lang_v1.default'), '');

        $business = Business::find($business_id);

        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            $business = null;
        }

        if (!empty($business->woocommerce_api_settings)) {
            $default_settings = json_decode($business->woocommerce_api_settings, true);
            if (empty($default_settings['product_fields_for_create'])) {
                $default_settings['product_fields_for_create'] = [];
            }

            if (empty($default_settings['product_fields_for_update'])) {
                $default_settings['product_fields_for_update'] = [];
            }
        }

        $locations = BusinessLocation::forDropdown($business_id);
        $module_version = System::getProperty('woocommerce_version');

        $cron_job_command = $this->moduleUtil->getCronJobCommand();

        return view('woocommerce::woocommerce.api_settings')
                ->with(compact('default_settings', 'locations', 'price_groups', 'module_version', 'cron_job_command', 'business'));
    }
    /**
     * Updates woocommerce api settings.
     * @return Response
     */
    public function updateSettings(Request $request)
    {
        $business_id = request()->session()->get('business.id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.access_woocommerce_api_settings')))) {
            abort(403, 'Unauthorized action.');
        }

        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $input = $request->except('_token');

            $input['product_fields_for_create'] = !empty($input['product_fields_for_create']) ? $input['product_fields_for_create'] : [];
            $input['product_fields_for_update'] = !empty($input['product_fields_for_update']) ? $input['product_fields_for_update'] : [];

            $business = Business::find($business_id);
            $business->woocommerce_api_settings = json_encode($input);
            $business->woocommerce_wh_oc_secret = $input['woocommerce_wh_oc_secret'];
            $business->woocommerce_wh_ou_secret = $input['woocommerce_wh_ou_secret'];
            $business->woocommerce_wh_od_secret = $input['woocommerce_wh_od_secret'];
            $business->woocommerce_wh_or_secret = $input['woocommerce_wh_or_secret'];
            $business->save();

            $output = ['success' => 1,
                            'msg' => trans("lang_v1.updated_succesfully")
                        ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0,
                            'msg' => trans("messages.something_went_wrong")
                        ];
        }

        return redirect()->back()->with(['status' => $output]);
    }
    /**
     * Updates woocommerce api settings.
     * @return Response
     */
    public function updateContactUsSettings(Request $request)
    {
        $business_id = request()->session()->get('business.id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.access_woocommerce_api_settings')))) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $input        = $request->except('_token');
            $title        = $request->title;
            $array        = (isset($request->line_id))?$request->line_id:[];
            \DB::beginTransaction();
            if(count($title)>0){
                foreach($title as $key => $it){
                    $row = \App\Models\ContactUs::saveData($request,$key,$array);
                }
            }
            \DB::commit();
            $output = ['success' => 1, 'msg' => trans("lang_v1.updated_succesfully") ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            $output = ['success' => 0, 'msg' => trans("messages.something_went_wrong") ];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Synchronizes pos categories with Woocommerce categories
     * @return Response
     */
    public function syncCategories()
    {
        $business_id = request()->session()->get('business.id');

        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.syc_categories')))) {
            abort(403, 'Unauthorized action.');
        }

        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            DB::beginTransaction();
            $user_id = request()->session()->get('user.id');
            $this->woocommerceUtil->syncCategories($business_id, $user_id);
            DB::commit();
            $output = ['success' => 1,'msg' => __("woocommerce::lang.synced_successfully")];
        } catch (\Exception $e) {
            DB::rollBack();
            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,'msg' => $e->getMessage()];
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => 0,'msg' => __("messages.something_went_wrong")];
            }
        }

        return $output;
    }

    /**
     * Synchronizes pos products with Woocommerce products
     * @return Response
     */
    public function syncProducts()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.sync_products')))) {
            abort(403, 'Unauthorized action.');
        }

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        try {
            $user_id   = request()->session()->get('user.id');
            $sync_type = request()->input('type');
            DB::beginTransaction();
            $this->woocommerceUtil->syncProducts($business_id, $user_id, $sync_type);
            DB::commit();
            $output = ['success' => 1,'msg' => __("woocommerce::lang.synced_successfully")];
        } catch (\Exception $e) {
            DB::rollBack();
            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,'msg' => $e->getMessage()];
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => 0,'msg' => __("messages.something_went_wrong")];
            }
        }
        
        return $output;
    }

    /**
     * Synchronizes Woocommers Orders with POS sales
     * @return Response
     */
    public function syncOrders()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.sync_orders')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            DB::beginTransaction();
            $user_id = request()->session()->get('user.id');
           
            $this->woocommerceUtil->syncOrders($business_id, $user_id);

            DB::commit();

            $output = ['success' => 1,
                            'msg' => __("woocommerce::lang.synced_successfully")
                        ];
        } catch (\Exception $e) {
            DB::rollBack();

            if (get_class($e) == 'Modules\Woocommerce\Exceptions\WooCommerceError') {
                $output = ['success' => 0,
                            'msg' => $e->getMessage(),
                        ];
            } else {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

                $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong"),
                        ];
            }
        }

        return $output;
    }

    /**
     * Retrives sync log
     * @return Response
     */
    public function getSyncLog()
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }
        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $last_sync = [
                'categories'   => $this->woocommerceUtil->getLastSync($business_id, 'categories'),
                'new_products' => $this->woocommerceUtil->getLastSync($business_id, 'new_products'),
                'all_products' => $this->woocommerceUtil->getLastSync($business_id, 'all_products'),
                'orders'       => $this->woocommerceUtil->getLastSync($business_id, 'orders')

            ];
            return $last_sync;
        }
    }

    /**
     * Maps POS tax_rates with Woocommerce tax rates.
     * @return Response
     */
    public function mapTaxRates(Request $request)
    {
        $notAllowed = $this->woocommerceUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module') && auth()->user()->can('woocommerce.map_tax_rates')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->except('_token');
            foreach ($input['taxes'] as $key => $value) {
                $value = !empty($value) ? $value : null;
                TaxRate::where('business_id', $business_id)
                        ->where('id', $key)
                        ->update(['woocommerce_tax_rate_id' => $value]);
            }

            $output = ['success' => 1,'msg' => __("lang_v1.updated_succesfully") ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => 0, 'msg' => __("messages.something_went_wrong")];
        }

        return redirect()->back()->with(['status' => $output]);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function viewSyncLog()
    {
        $business_id         = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $logs = WoocommerceSyncLog::where('woocommerce_sync_logs.business_id', $business_id)
                    ->leftjoin('users as U', 'U.id', '=', 'woocommerce_sync_logs.created_by')
                    ->select([
                        'woocommerce_sync_logs.created_at',
                        'sync_type', 'operation_type',
                        DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"),
                        'woocommerce_sync_logs.data',
                        'woocommerce_sync_logs.details as log_details',
                        'woocommerce_sync_logs.id as DT_RowId'
                    ]);
            $sync_type = [];
            if (auth()->user()->can('woocommerce.syc_categories')) {
                $sync_type[] = 'categories';
            }
            if (auth()->user()->can('woocommerce.sync_products')) {
                $sync_type[] = 'all_products';
                $sync_type[] = 'new_products';
            }

            if (auth()->user()->can('woocommerce.sync_orders')) {
                $sync_type[] = 'orders';
            }
            if (!auth()->user()->can('superadmin')) {
                $logs->whereIn('sync_type', $sync_type);
            }

            return Datatables::of($logs)
                ->editColumn('created_at', function ($row) {
                    $created_at = $this->woocommerceUtil->format_date($row->created_at, true);
                    $for_humans = \Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->diffForHumans();
                    return $created_at . '<br><small>' . $for_humans . '</small>';
                })
                ->editColumn('sync_type', function ($row) {
                    $array = [
                        'categories' => __('category.categories'),
                        'all_products' => __('sale.products'),
                        'new_products' => __('sale.products'),
                        'orders' => __('woocommerce::lang.orders'),
                    ];
                    return $array[$row->sync_type];
                })
                ->editColumn('operation_type', function ($row) {
                    $array = [
                        'created' => __('woocommerce::lang.created'),
                        'updated' => __('woocommerce::lang.updated'),
                        'reset'   => __('woocommerce::lang.reset'),
                    ];
                    return array_key_exists($row->operation_type, $array) ? $array[$row->operation_type] : '';
                })
                ->editColumn('data', function ($row) {
                    if (!empty($row->data)) {
                        $data = json_decode($row->data, true);
                        return implode(', ', $data) . '<br><small>' . count($data) . ' ' . __('woocommerce::lang.records') . '</small>';
                    } else {
                        return '';
                    }
                })
                ->editColumn('log_details', function ($row) {
                    $details = '';
                    if (!empty($row->log_details)) {
                        $details = $row->log_details;
                    }
                    return $details;
                })
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->rawColumns(['created_at', 'data'])
                ->make(true);
        }

        return view('woocommerce::woocommerce.sync_log');
    }

    /**
     * Retrives details of a sync log.
     * @param int $id
     * @return Response
     */
    public function getLogDetails($id)
    {
        $business_id = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $log         = WoocommerceSyncLog::where('business_id', $business_id)->find($id);
            $log_details = json_decode($log->details);
            return view('woocommerce::woocommerce.partials.log_details')->with(compact('log_details'));
        }
    }

    /**
     * Resets synced categories
     * @return json
     */
    public function resetCategories()
    {
        $business_id        = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $user_id = request()->session()->get('user.id');
                Category::where('business_id', $business_id)->update(['woocommerce_cat_id' => null]);
                $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'categories', 'reset', null);
                $output = ['success' => 1, 'msg' => __("woocommerce::lang.cat_reset_success")];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => 0, 'msg' => __("messages.something_went_wrong")];
            }

            return $output;
        }
    }
    /**
     * Resets synced products
     * @return json
     */
    public function resetProducts()
    {
        $business_id     = request()->session()->get('business.id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'woocommerce_module'))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $user_id     = request()->session()->get('user.id');
                //Update products table
                Product::where('business_id', $business_id)->update(['woocommerce_product_id' => null, 'woocommerce_media_id' => null]);
                $product_ids = Product::where('business_id', $business_id)->pluck('id');
                $product_ids = !empty($product_ids) ? $product_ids : [];
                //Update variations table
                Variation::whereIn('product_id', $product_ids)->update([ 'woocommerce_variation_id' => null ]);
                //Update variation templates
                VariationTemplate::where('business_id', $business_id)->update([ 'woocommerce_attr_id' => null ]);
                Media::where('business_id', $business_id)->update(['woocommerce_media_id' => null]);
                $this->woocommerceUtil->createSyncLog($business_id, $user_id, 'all_products', 'reset', null);
                $output = ['success' => 1, 'msg' => __("woocommerce::lang.prod_reset_success")];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => 0, 'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()];
            }

            return $output;
        }
    }



    // **new*********************** \\
    // *** ACTIONS E-COMMERCE
    // *1* view in e_commerce
    // ..........................................................
    public function addECommerce(Request $request,$id)  
    {
        $style     = request()->input("Style");
        if($style != null && $style == "contact"){
            $ContactUs = \App\Models\ContactUs::find($id);
            if(!empty($ContactUs)){
                $ContactUs->view = 1;
                $ContactUs->save();
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
        }elseif($style != null && $style == "section"){
            $sections = \App\Models\Ecommerce::find($id);
            if(!empty($sections)){
                $sections->view = 1;
                $sections->save();
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
        }elseif($style != null && $style == "social"){
            $sections = \App\Models\Ecommerce\SocialMedia::find($id);
            if(!empty($sections)){
                $sections->view = 1;
                $sections->save();
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
        }elseif($style != null && $style == "shop"){
            $sections = \App\Models\Ecommerce\ShopCategory::find($id);
            if(!empty($sections)){
                $sections->view = 1;
                $sections->save();
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
        }elseif($style != null && $style == "float"){
            $sections = \App\Models\Ecommerce\FloatingBar::find($id);
            if(!empty($sections)){
                $sections->view = 1;
                $sections->save();
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
            $output = [
                "success"=>0,
                "msg"=>"Failed !!",
            ];
            return redirect()->back()->with("status",$output);
        }
    }
    // *2* remove from e_commerce
    // ..........................................................
    public function removeECommerce(Request $request,$id)  
    {
        $style     = request()->input("Style");
        if($style != null && $style == "contact"){
            $ContactUs = \App\Models\ContactUs::find($id);
            if(!empty($ContactUs)){
                $ContactUs->view = 0;
                $ContactUs->save();
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
        }elseif($style != null && $style == "section"){
            $sections = \App\Models\Ecommerce::find($id);
            if(!empty($sections)){
                $sections->view = 0;
                $sections->save();
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
        }elseif($style != null && $style == "social"){
            $sections = \App\Models\Ecommerce\SocialMedia::find($id);
            if(!empty($sections)){
                $sections->view = 0;
                $sections->save();
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
        }elseif($style != null && $style == "shop"){
            $sections = \App\Models\Ecommerce\ShopCategory::find($id);
            if(!empty($sections)){
                $sections->view = 0;
                $sections->save();
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
        }elseif($style != null && $style == "float"){
            $sections = \App\Models\Ecommerce\FloatingBar::find($id);
            if(!empty($sections)){
                $sections->view = 0;
                $sections->save();
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
            $output = [
                        "success"=>0,
                        "msg"=>"Failed !! ",
                    ];
                return redirect()->back()->with("status",$output);
        }
    }
    // *3* save as  topSection e_commerce
    // ..........................................................
    public function topSection(Request $request,$id)  
    {
        $style     = request()->input("Style");
        if($style != null && $style == "about"){
            $sections     = \App\Models\Ecommerce::find($id);
            $sections_all = \App\Models\Ecommerce::where("id","!=",$id)
                                                    ->where("about_us",1)
                                                    ->get();
            if(!empty($sections)){
                foreach($sections_all as $ie){
                    $ie->topSection   = 0;
                    $ie->update();
                }
                $sections->topSection = 1;
                $sections->update();
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
        }elseif($style != null && $style == "store"){
            $sections     = \App\Models\Ecommerce::find($id);
            $sections_all = \App\Models\Ecommerce::where("id","!=",$id)
                                                    ->where("store_page",1)
                                                    ->get();
            if(!empty($sections)){
                foreach($sections_all as $ie){
                    $ie->topSection   = 0;
                    $ie->update();
                }
                $sections->topSection = 1;
                $sections->update();
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
            $sections     = \App\Models\Ecommerce::find($id);
            $sections_all = \App\Models\Ecommerce::where("id","!=",$id)
                                                    ->where("about_us",0)
                                                    ->where("store_page",0)
                                                    ->where("subscribe",0)
                                                    ->get();
            if(!empty($sections)){
                foreach($sections_all as $ie){
                    $ie->topSection   = 0;
                    $ie->update();
                }
                $sections->topSection = 1;
                $sections->update();
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
        }
    }
    // *4* remove from e_commerce  
    // ..........................................................
    public function dontViewInEcm() {
        if(request()->ajax()){
            $id = request()->input("id");
            $check = request()->input("Style");
            if($check != null){
                $Ecommerce = \App\Models\Ecommerce::find($id);
                    if(!empty($Ecommerce)){
                    if($check == "About_us"){
                        $Ecommerce->about_us = 0;
                    }
                    $Ecommerce->update();
                    $response  = [
                        "success"=> true,
                        "msg"=>__("This Section Will Not Appear in About Us ")            
                    ];
                }else{
                    $response  = [
                        "success"=> false,
                        "msg"=>__("There is no section with this name")            
                    ];
                }
                return $response;
            }else{
                    $Ecommerce = \App\Models\Ecommerce::find($id);
                    if(!empty($Ecommerce)){
                    $Ecommerce->view = 0;
                    $Ecommerce->update();
                    $response  = [
                        "success"=> true,
                        "msg"=>__("This Section Will Not Appear in About Us ")            
                    ];
                }else{
                    $response  = [
                        "success"=> false,
                        "msg"=>__("There is no section with this name")            
                    ];
                }
                return $response;
            }
        }  
    }
    // *5* show in e_commerce         
    // ..........................................................
    public function viewInEcm() {
        if(request()->ajax()){
            $id = request()->input("id");
            $check = request()->input("Style");
            if($check != null){
                $Ecommerce = \App\Models\Ecommerce::find($id);
                    if(!empty($Ecommerce)){
                    if($check == "About_us"){
                        $Ecommerce->about_us = 1;
                    }
                    $Ecommerce->update();
                    $response  = [
                        "success"=> true,
                        "msg"=>__("This Section Will  Appear in About Us ")            
                    ];
                }else{
                    $response  = [
                        "success"=> false,
                        "msg"=>__("There is no section with this name")            
                    ];
                }
                return $response;
            }else{
                $Ecommerce = \App\Models\Ecommerce::find($id);
                if(!empty($Ecommerce)){
                    $Ecommerce->view = 1;
                    $Ecommerce->update();
                    $response  = [
                        "success"=> true,
                        "msg"=>__("This Section Will Appear in About Us ")            
                    ];
                }else{
                    $response  = [
                        "success"=> false,
                        "msg"=>__("There is no section with this name")            
                    ];
                }
                return $response;
            } 
        }
    }
    // ..........................................................
    // *** CONTACT US
    // *0* list Contact e_commerce
    // ..........................................................  
    public function contactSettings() {
        $allData   = \App\Models\ContactUs::allData();
        $allSocial = \App\Models\Ecommerce\SocialMedia::allData();
        $count     = count($allData); 
        return view("woocommerce::woocommerce.contactus_settings")->with(compact("allData","allSocial","count"));
    }
    // *1* create Contact e_commerce
    // ..........................................................  
    public function createContact(Request $request){
        $style    = $request->input("style");
        return view("woocommerce::woocommerce.contactus_create")->with("style",$style);
    }
    // *2* edit Contact e_commerce
    // ..........................................................
    public function editContact($id){
        $contact   = \App\Models\ContactUs::find($id); 
        return view("woocommerce::woocommerce.contactus_update")->with("id",$id)->with("contact",$contact);
    }
    // *3* save Contact e_commerce
    // ..........................................................
    public function saveContact(Request $request){
        try{     
            $data               = $request->only(["title","mobile","links","address","additional_info"]);
            \DB::beginTransaction();
            $item                   = new \App\Models\ContactUs(); 
            $item->title            = $data["title"]; 
            $item->mobile           = $data["mobile"]; 
            $item->links            = $data["links"]; 
            $item->address          = $data["address"];
            $item->additional_info  = $data["additional_info"];
            $item->view             = 1;
            if($request->hasFile("icon")  != null || $request->hasFile("icon") != false ){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name = $new_file_name;
                        $item->icon         = $uploaded_file_name; 
                    }
                }
            } 
            $item->save();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.added_successfull") ,
            ];
            return redirect("/woocommerce/contacts")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/contacts")->with("status",$output);
        }
    }
    // *4* update Contact e_commerce
    // ..........................................................
    public function updateContact(Request $request,$id){
        try{    
            $data               = $request->only(["title","mobile","links","address","additional_info"]);
            \DB::beginTransaction();
            $item                   = \App\Models\ContactUs::find($id); 
            $item->title            = $data["title"]; 
            $item->mobile           = $data["mobile"]; 
            $item->links            = $data["links"]; 
            $item->address          = $data["address"];
            $item->additional_info  = $data["additional_info"];
            $item->view             = 1;
            if($request->hasFile("icon")  != null || $request->hasFile("icon") != false ){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name = $new_file_name;
                        $item->icon         = $uploaded_file_name; 
                    }
                }
            } 
            $item->update();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.updated_successfull") ,
            ];
            return redirect("/woocommerce/contacts")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/contacts")->with("status",$output);
        }
    }
    // ***...
    public function getEContact(){
        $business_id = request()->session()->get("user.business_id");
        if(request()->ajax()){
            $check      = request()->input("check");
            $contact_us = \App\Models\ContactUs::whereNotNull("created_at");
                
            // if(request()->get("brand")){
            //     $data = request()->get("brand");
            //     $products->where("brand_id",$data) ;
            // }
            $contact_us->get() ;
            return Datatables::of($contact_us)
                    ->addColumn("action",function($row) {
                        $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@editContact', ["id"=>$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Edit") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@addECommerce', ["id"=>$row->id,"Style" => "contact"]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add To E-commerce") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@removeECommerce', ["id"=>$row->id,"Style" => "contact"]) . '" class="view-product"><i class="fa fa-trash"></i> ' . __("Remove From E-commerce") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    })
                    ->addColumn("icon",function($row){
                        if($row->icon_url == null || $row->icon_url == ""){
                            return "No Image" ;
                        }else{
                            return "<img src='".$row->icon_url."' width=100% height=100 style='border-radius:10px;border:2px solid black;padding: 2px' />" ;
                        }
                    })
                    ->addColumn("mobile",function($row){
                        return $row->mobile ;
                    })
                    ->addColumn("links",function($row){
                        return $row->links ;
                    })
                    ->addColumn("title",function($row){
                        return $row->title;
                    })
                    ->addColumn("address",function($row){
                        return "<span class='btn-second' style='background-color:#f1f1f1;border-radius:10px;padding:10px;margin:10px;cursor:pointer;'>".$row->address."</span>" ;
                    })
                    ->addColumn("additional_note",function($row){
                        return $row->additional_info;
                    })
                    ->addColumn("type",function($row){
                        $view               = "";
                        if($row->view       == 1){ 
                            $view           = "<i class='fa fas fa-check'></i>";
                        }
                        return  "View In E-Commerce " . $view  ;
                    })
                    ->rawColumns(["action","type","links","title","mobile","icon","additional_note","address","type"])
                    ->make(true);;
        }
            
    }
    // ***...SOCIAL
    public function getSocial(){
        $business_id = request()->session()->get("user.business_id");
        if(request()->ajax()){
            $check      = request()->input("check");
            $social     = \App\Models\Ecommerce\SocialMedia::whereNotNull("created_at");
                
            // if(request()->get("brand")){
            //     $data = request()->get("brand");
            //     $products->where("brand_id",$data) ;
            // }
            $social->get() ;
            return Datatables::of($social)
                    ->addColumn("action",function($row) {
                        $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@editSocial', ["id"=>$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Edit") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@deleteSocial', ["id"=>$row->id]) . '" class="view-product"><i class="fa fa-trash"></i> ' . __("Delete") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@addECommerce', ["id"=>$row->id,"Style" => "social"]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add To E-commerce") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@removeECommerce', ["id"=>$row->id,"Style" => "social"]) . '" class="view-product"><i class="fa fa-trash"></i> ' . __("Remove From E-commerce") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    })
                    ->addColumn("icon",function($row){
                        if($row->icon_url == null || $row->icon_url == ""){
                            return "No Image" ;
                        }else{
                            return "<img src='".$row->icon_url."' width=100% height=100 style='border-radius:10px;border:2px solid black;padding: 2px' />" ;
                        }
                    })
                    ->addColumn("link",function($row){
                        return $row->link ;
                    })
                    ->addColumn("title",function($row){
                        return $row->title;
                    })
                    ->addColumn("type",function($row){
                        $view               = "";
                        if($row->view       == 1){ 
                            $view           = "<i class='fa fas fa-check'></i>";
                        }
                        return  "View In E-Commerce " . $view  ;
                    })
                    ->rawColumns(["action","type","links","title","mobile","icon","additional_note","address","type"])
                    ->make(true);;
        }
            
    }
    // ***...SOCIAL
    public function createSocial(Request $request){
        $style    =  $request->input("style");
        return view("woocommerce::woocommerce.social_create")->with(compact("style"));
    }
    // ***...SOCIAL
    public function editSocial(Request $request,$id){
        $social    =  \App\Models\Ecommerce\SocialMedia::find($id);
        return view("woocommerce::woocommerce.edit_social_create")->with(compact("social","id"));
    }
    // ***...SOCIAL
    public function saveSocial(Request $request){
        try{     
            $data              = $request->only(["title","link"]);
            \DB::beginTransaction();
            $item              = new \App\Models\Ecommerce\SocialMedia(); 
            $item->business_id = session()->get("user.business_id"); 
            $item->title       = $data["title"]; 
            $item->link        = $data["link"];  
            $item->client_id   = 1;  
            if($request->hasFile("icon")  != null || $request->hasFile("icon") != false ){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name = $new_file_name;
                        $item->icon         = $uploaded_file_name; 
                    }
                }
            } 
            $item->save();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.added_successfull") ,
            ];
            return redirect("/woocommerce/contacts")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/contacts")->with("status",$output);
        }
    }
    // ***...SOCIAL
    public function updateSocial(Request $request,$id){
        try{     
            $data              = $request->only(["title","link"]);
            \DB::beginTransaction();
            $item              = \App\Models\Ecommerce\SocialMedia::find($id); 
            $item->title       = $data["title"]; 
            $item->link        = $data["link"];  
            $item->client_id   = 1;  
            if($request->hasFile("icon")  != null || $request->hasFile("icon") != false ){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name = $new_file_name;
                        $item->icon         = $uploaded_file_name; 
                    }
                }
            } 
            $item->save();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.updated_successfull") ,
            ];
            return redirect("/woocommerce/contacts")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/contacts")->with("status",$output);
        }
    }
    // ***...SOCIAL
    public function deleteSocial(Request $request,$id){
        try{     
             
            \DB::beginTransaction();
            $item              = \App\Models\Ecommerce\SocialMedia::find($id); 
            if(empty($item)){
                $output = [
                    "success" => 0 ,
                    "msg"     => __("messages.something_went_wrong") ,
                ];
            }else{
                $item->delete();
                \DB::commit();
                $output = [
                    "success" => 1 ,
                    "msg"     => __("messages.deleted_successfull") ,
                ];
            }
            return redirect("/woocommerce/contacts")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/contacts")->with("status",$output);
        }
    }
    // ..............................
    // *** SECTIONS
    // *0* list sections e_commerce
    // ..............................
    public function sectionsSettings() {
        $allData = \App\Models\Ecommerce::allData();
        $count   = count($allData); 
        return view("woocommerce::woocommerce.sections_settings")->with(compact("allData","count"));
    }
    // *1* create sections e_commerce
    // ..............................
    public function createSection(Request $request){
        $style    = $request->input("style");
        return view("woocommerce::woocommerce.sections_create")->with("style",$style);
    }
    // *2* edit sections e_commerce
    // ..............................
    public function editSection($id){
        $section   = \App\Models\Ecommerce::find($id); 
        return view("woocommerce::woocommerce.sections_update")->with("id",$id)->with("section",$section);
    }
    // *3* save sections e_commerce
    // ..............................
    public function saveSections(Request $request){
        try{     
            $data              = $request->only(["name","description","title","button","style"]);
            $check             = $data["style"];
            \DB::beginTransaction();
            $item              = new \App\Models\Ecommerce(); 
            $item->name        = $data["name"]; 
            $item->title       = $data["title"]; 
            $item->desc        = $data["description"]; 
            $item->button      = $data["button"];
            $item->view        = 1;
            if($check != null && $check == "about"){
                $item->about_us      = 1;
            }
            if($check != null && $check == "store"){
                $item->store_page      = 1;
            }
            if($request->hasFile("image")  != null || $request->hasFile("image") != false ){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    if ($request->file("image")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name = $new_file_name;
                        $item->image        = $uploaded_file_name; 
                    }
                }
            } 
            $item->save();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.added_successfull") ,
            ];
            return redirect("/woocommerce/sections")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/sections")->with("status",$output);
        }
    }
    // *4* update sections e_commerce 
    // ..............................
    public function updateSections(Request $request,$id){
        try{    
            $data              = $request->only(["name","description","title","button"]);
            \DB::beginTransaction();
            $item              = \App\Models\Ecommerce::find($id); 
            $item->name        = $data["name"]; 
            $item->title       = $data["title"]; 
            $item->desc        = $data["description"]; 
            $item->button      = $data["button"];
            if($request->hasFile("image")  != null || $request->hasFile("image") != false ){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    if ($request->file("image")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name = $new_file_name;
                        $item->image        = $uploaded_file_name; 
                    }
                }
            } 
            $item->update();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.added_successfull") ,
            ];
            return redirect("/woocommerce/sections")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/sections")->with("status",$output);
        }
    }
    // ..***.. 
    public function updateSectionSettings(Request $request) {

        try{
            $name        = $request->name;
            $array       = (isset($request->line_id))?$request->line_id:[];
            \DB::beginTransaction();
            if(count($name)>0){
                foreach($name as $key => $it){
                    $row = \App\Models\Ecommerce::saveRow($request,$key,$array);
                }
            }
            \DB::commit();
            $alerts['update'] = "success";
        }catch(Exception $e){
            $alerts['connection_failed'] = 'Unable to connect with WooCommerce, Check API settings';
            
        }
        return back();
    }
    // ***...
    public function getESection(){
        $business_id = request()->session()->get("user.business_id");
        if(request()->ajax()){
            $check    = request()->input("check");
            $sections = \App\Models\Ecommerce::whereNotNull("created_at");
            if($check == "about"){
                $sections->where("about_us",1) ;
            }elseif($check == "store"){
                $sections->where("store_page",1) ;
            }elseif($check == "login"){
                $sections->where("login",1) ;
            }elseif($check == "signup"){
                $sections->where("signup",1) ;
            }elseif($check == "Top"){
                $sections->where("store_page",0) ;
                $sections->where("about_us",0) ;
                $sections->where("subscribe",0) ;
            } 
            // if(request()->get("brand")){
            //     $data = request()->get("brand");
            //     $products->where("brand_id",$data) ;
            // }
            $sections->get() ;
            return Datatables::of($sections)
                    ->addColumn("action",function($row) use($check){
                        $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@editSection', ["id"=>$row->id,]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Edit") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@delSection', ["id"=>$row->id,]) . '" class="view-product delete-section"><i class="fa fa-trash"></i> ' . __("Delete") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@addECommerce', ["id"=>$row->id,"Style" => "section"]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add To E-commerce") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@removeECommerce', ["id"=>$row->id,"Style" => "section"]) . '" class="view-product"><i class="fa fa-trash"></i> ' . __("Remove From E-commerce") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@topSection', ["id"=>$row->id,"Style" => $check]) . '" class="view-product"><i class="fa fa-star"></i> ' . __("Set As Top Section") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    })
                    ->addColumn("image",function($row){
                        if($row->image_url == null || $row->image_url == ""){
                            return "No Image" ;
                        }else{
                            return "<img src='".$row->image_url."' width=100% height=100 style='border-radius:10px;border:2px solid black;padding: 2px' />" ;
                        }
                    })
                    ->addColumn("name",function($row){
                        return $row->name ;
                    })
                    ->addColumn("title",function($row){
                        return $row->title;
                    })
                    ->addColumn("button",function($row){
                        return "<span class='btn-second' style='background-color:#f1f1f1;border-radius:10px;padding:10px;margin:10px;cursor:pointer;'>".$row->button."</span>" ;
                    })
                    ->addColumn("description",function($row){
                        return $row->desc;
                    })
                    ->addColumn("type",function($row){
                        $view               = "";
                        $about_us           = "";
                        $subscribe          = "";
                        $store_page         = ""; 
                        $main_section       = "";
                        $topSection         = "";
                        if($row->view       == 1){ 
                            $view           = "<i class='fa fas fa-check'></i>";
                        }
                        if($row->subscrib   == 1){ 
                            $subscrib       = "<i class='fa fas fa-check'></i>";
                        }
                        if($row->about_us   == 1){ 
                            $about_us       = "<i class='fa fas fa-check'></i>";
                        }
                        if($row->about_us   == 0 && $row->about_us == 0 && $row->about_us == 0){ 
                            $main_section   = "<i class='fa fas fa-check'></i>";
                        }
                        if($row->topSection == 1){ 
                            $topSection     = "<i class='fa fas fa-check'></i>";
                        }
                        if($row->store_page == 1){ 
                            $store_page     = "<i class='fa fas fa-check'></i>";
                        }
                        
                        return  "View In E-Commerce " . $view . " <br> Main Section " . $main_section ." <br> About Us " . $about_us . " <br> Subscribe " . $subscribe .  " <br> Top Section " . $topSection. " <br> Store Page " . $store_page;
                    })
                    ->rawColumns(['action',"type","button","title","name","image",'description'])
                    ->make(true);;
        }
            
    }
    // *5* delete  
    // ..............................
    public function delSection(Request $request,$id){
        try{     
             
            \DB::beginTransaction();
            $item              = \App\Models\Ecommerce::find($id); 
            if(!empty($item)){
                $item->delete();
                \DB::commit();
                $output = [
                    "success" => 1 ,
                    "msg"     => __("messages.deleted_successfull") ,
                ];
            }else{
                $output = [
                    "success" => 0 ,
                    "msg"     => __("messages.something_went_wrong") ,
                ];
            }
            return redirect("/woocommerce/sections/all")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/sections/all")->with("status",$output);
        }
    }
    // ***...
    public function getStripeApi(){
        $business_id = request()->session()->get("user.business_id");
        $allData     = \App\Models\Ecommerce\StripeSetting::getInfo();
        
        
        if(request()->ajax()){
            $check    = request()->input("check");
            $stripe   = \App\Models\Ecommerce\StripeSetting::select();
             
            // if(request()->get("brand")){
            //     $data = request()->get("brand");
            //     $products->where("brand_id",$data) ;
            // }
            return Datatables::of($stripe)
                    ->addColumn("action",function($row) use($check){
                        $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@editStripeApi', ["id"=>$row->id,]) . '" class="view-product"><i class="fa fa-edit"></i> ' . __("Edit") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    }) 
                    ->rawColumns(['action'])
                    ->make(true);;
        }
        return view("woocommerce::woocommerce.stripe_settings")->with(compact("allData"));  
    }
    // *1* edit Stripe e_commerce
    // ..............................
    public function editStripeApi($id){
        try{ 
            $stripe    = \App\Models\Ecommerce\StripeSetting::find($id);
            if(empty($stripe)){
                $output = [
                    "success" => 0 ,
                    "msg"     => __("messages.something_went_wrong") ,
                ];
                return redirect("/woocommerce/stripe/all")->with("status",$output);
            } 
            return view("woocommerce::woocommerce.stripe_update")->with(compact("stripe" ,"id"));
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/stripe/all")->with("status",$output);
        }
    }
    // *2* update Stripe e_commerce
    // ..............................
    public function updateStripeApi(Request $request,$id){
        try{     
            $data              = $request->only(["api_public","api_private","product_key","url_website"]);
            \DB::beginTransaction();
            $item              = \App\Models\Ecommerce\StripeSetting::find($id); 
            if(!empty($item)){
                $item->api_public   = isset($data["api_public"])?$data["api_public"]:null;
                $item->api_private  = isset($data["api_private"])?$data["api_private"]:null;
                $item->product_key  = isset($data["product_key"])?$data["product_key"]:null;
                $item->url_website  = isset($data["url_website"])?$data["url_website"]:null;
                $item->update();
                \DB::commit();
                $output = [
                    "success" => 1 ,
                    "msg"     => __("messages.updated_successfull") ,
                ];
            }else{
                $output = [
                    "success" => 0 ,
                    "msg"     => __("messages.something_went_wrong") ,
                ];
            }
            return redirect("/woocommerce/stripe/all")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/stripe/all")->with("status",$output);
        }
    }
    // .........................................................
    // ***...
    public function getFloat(){
        $business_id = request()->session()->get("user.business_id");
        $allData     = \App\Models\Ecommerce\FloatingBar::get();
        
        if(request()->ajax()){
            $check    = request()->input("check");
            $floating = \App\Models\Ecommerce\FloatingBar::whereNotNull("created_at");
             
            // if(request()->get("brand")){
            //     $data = request()->get("brand");
            //     $products->where("brand_id",$data) ;
            // }
           
            return Datatables::of($floating)
                    ->addColumn("action",function($row) use($check){
                        $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@editFloat', ["id"=>$row->id,]) . '" class="view-product"><i class="fa fa-edit"></i> ' . __("Edit") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@delFloat', ["id"=>$row->id,]) . '" class="view-product delete-float"><i class="fa fa-trash"></i> ' . __("Delete") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@addECommerce', ["id"=>$row->id,"Style" => "float"]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add To E-commerce") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@removeECommerce', ["id"=>$row->id,"Style" => "float"]) . '" class="view-product"><i class="fa fa-trash"></i> ' . __("Remove From E-commerce") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    })
                    ->addColumn("category",function($row){
                        $category = \App\Category::find($row->category_id);
                        $name     = (!empty($category))?$category->name:"";
                        return  $name ;
                    })
                    ->editColumn("created_at",function($row){
                        return $row->created_at->format("Y-m-d") ;
                    })
                    ->editColumn("icon",function($row){
                        return  '<img src="'.$row->icon_url.'" width=100px hight=100px>' ;
                    })
                    ->addColumn("type",function($row){
                        $view               = "";
                        if($row->view       == 1){ 
                            $view           = "<i class='fa fas fa-check'></i>";
                        }
                        return  "View In E-Commerce " . $view  ;
                    })
                    ->rawColumns(['action','category','type','icon'])
                    ->make(true);;
        }
        return view("woocommerce::woocommerce.floating_settings")->with(compact("allData"));  
    }
    // *1* create float e_commerce
    // ..............................
    public function createFloat(Request $request){
        $style    = $request->input("style");
        $allCategory = \App\Category::get();$list_category=[];
        foreach($allCategory as $item){
            $list_category[$item->id] = $item->name; 
        }
        return view("woocommerce::woocommerce.floating_create")->with(compact("style","list_category"));
    }
    // *2* edit float e_commerce
    // ..............................
    public function editFloat($id){
        $floating    = \App\Models\Ecommerce\FloatingBar::find($id); 
        $allCategory = \App\Category::get();$list_category=[];
        foreach($allCategory as $item){
            $list_category[$item->id] = $item->name; 
        }
        return view("woocommerce::woocommerce.floating_update")->with(compact("floating","list_category","id"));
    }
    // *3* save float e_commerce
    // ..............................
    public function saveFloat(Request $request){
        try{     
             
            $data              = $request->only(["title","category_id"]);
            \DB::beginTransaction();
            $item              = new \App\Models\Ecommerce\FloatingBar(); 
            $item->title       = isset($data["title"])?$data["title"]:null; 
            $item->business_id = session()->get("user.business_id"); 
            $item->category_id = isset($data["category_id"])?$data["category_id"]:null; 
            // if($request->hasFile("icon")  != null || $request->hasFile("icon") != false ){
            //     $dir_name =  config('constants.product_img_path');
            //     if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
            //         $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
            //         if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
            //             $uploaded_file_name = $new_file_name;
            //             $item->icon         = $uploaded_file_name; 
            //         }
            //     }
            // } 
            $item->save();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.added_successfull") ,
            ];
            return redirect("/woocommerce/float/all")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/float/all")->with("status",$output);
        }
    }
    // *4* update float e_commerce
    // ..............................
    public function updateFloat(Request $request,$id){
        try{     
            $data              = $request->only(["title","category_id"]);
            \DB::beginTransaction();
            $item              = \App\Models\Ecommerce\FloatingBar::find($id); 
            $item->title       = isset($data["title"])?$data["title"]:null; 
            $item->category_id = isset($data["category_id"])?$data["category_id"]:null; 
            // if($request->hasFile("icon")  != null || $request->hasFile("icon") != false ){
            //     $dir_name =  config('constants.product_img_path');
            //     if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
            //         $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
            //         if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
            //             $uploaded_file_name = $new_file_name;
            //             $item->icon         = $uploaded_file_name; 
            //         }
            //     }
            // } 
            $item->update();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.updated_successfull") ,
            ];
            return redirect("/woocommerce/float/all")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/float/all")->with("status",$output);
        }
    }
    // *4* delete float e_commerce
    // ..............................
    public function delFloat(Request $request,$id){
        try{     
             
            \DB::beginTransaction();
            $item              = \App\Models\Ecommerce\FloatingBar::find($id); 
            if(!empty($item)){
                $item->delete();
                \DB::commit();
                $output = [
                    "success" => 1 ,
                    "msg"     => __("messages.deleted_successfull") ,
                ];
            }else{
                $output = [
                    "success" => 0 ,
                    "msg"     => __("messages.something_went_wrong") ,
                ];
            }
            return redirect("/woocommerce/float/all")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/float/all")->with("status",$output);
        }
    }
    // .........................................................
    // ***...
    public function getShop(){
        $business_id = request()->session()->get("user.business_id");
        $allData     = \App\Models\Ecommerce\ShopCategory::get();
        
        if(request()->ajax()){
            $check    = request()->input("check");
            $shop     = \App\Models\Ecommerce\ShopCategory::select();
            // if(request()->get("brand")){
            //     $data = request()->get("brand");
            //     $products->where("brand_id",$data) ;
            // }
 
                
           return Datatables::of($shop)
                    ->addColumn("action",function($row) use($check){
                        $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@editShop', ["id"=>$row->id,]) . '" class="view-product"><i class="fa fa-edit"></i> ' . __("Edit") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@delShop', ["id"=>$row->id,]) . '" class="view-product delete-shop"><i class="fa fa-trash"></i> ' . __("Delete") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@addECommerce', ["id"=>$row->id,"Style" => "shop"]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add To E-commerce") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@removeECommerce', ["id"=>$row->id,"Style" => "shop"]) . '" class="view-product"><i class="fa fa-trash"></i> ' . __("Remove From E-commerce") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    })
                    ->addColumn("category",function($row){
                        $category = \App\Category::find($row->category_id);
                        $name     = (!empty($category))?$category->name:"";
                        return  $name ;
                    })
                    ->editColumn("created_at",function($row){
                        return $row->created_at->format("Y-m-d") ;
                    })
                    ->editColumn("icon",function($row){
                        return  '<img src="'.$row->icon_url.'" width=100px hight=100px>' ;
                    })
                    ->addColumn("type",function($row){
                        $view               = "";
                        if($row->view       == 1){ 
                            $view           = "<i class='fa fas fa-check'></i>";
                        }
                        return  "View In E-Commerce " . $view  ;
                    })
                    ->rawColumns(['action','category','type','icon'])
                    ->make(true);;
        }
        return view("woocommerce::woocommerce.shop_settings")->with(compact("allData"));  
    }
    // *1* create Shop e_commerce
    // ..............................
    public function createShop(Request $request){
        $style    = $request->input("style");
        $allCategory = \App\Category::get();$list_category=[];
        foreach($allCategory as $item){
            $list_category[$item->id] = $item->name; 
        }
        return view("woocommerce::woocommerce.shop_create")->with(compact("style","list_category"));
    }
    // *2* edit Shop e_commerce
    // ..............................
    public function editShop($id){
        $shop    = \App\Models\Ecommerce\ShopCategory::find($id); 
        $allCategory = \App\Category::get();$list_category=[];
        foreach($allCategory as $item){
            $list_category[$item->id] = $item->name; 
        }
        return view("woocommerce::woocommerce.shop_update")->with(compact("shop","list_category","id"));
    }
    // *3* save Shop e_commerce
    // ..............................
    public function saveShop(Request $request){
        try{     
            $data              = $request->only(["name","category_id"]);
            \DB::beginTransaction();
            $item              = new \App\Models\Ecommerce\ShopCategory();
            $cate              = \App\Category::find($data["category_id"]); 
            $item->business_id = session()->get("user.business_id"); 
            $item->name        = isset($data["name"])?$data["name"]:null; 
            $item->category_id = isset($data["category_id"])?$data["category_id"]:null; 
            $item->parent_id   =  $cate->parent_id; 
            $item->short_code  =  $cate->short_code; 
            $item->view        =  1; 
            $item->description =  $cate->description; 
            $item->created_by  = session()->get("user.id"); 
            if($request->hasFile("icon")  != null || $request->hasFile("icon") != false ){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name = $new_file_name;
                        $item->icon         = $uploaded_file_name; 
                    }
                }
            } 
            $item->save();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.added_successfull") ,
            ];
            return redirect("/woocommerce/shop/all")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/shop/all")->with("status",$output);
        }
    }
    // *4* update Shop e_commerce
    // ..............................
    public function updateShop(Request $request,$id){
        try{     
            $data              = $request->only(["name","category_id"]);
            \DB::beginTransaction();
            $item              = \App\Models\Ecommerce\ShopCategory::find($id);
            if(empty($item)){
                $output = [
                    "success" => 0 ,
                    "msg"     => __("messages.something_went_wrong") ,
                ];
                return redirect("/woocommerce/shop/all")->with("status",$output);
            }
            $cate              =  \App\Category::find($data["category_id"]); 
            $item->name        =  isset($data["name"])?$data["name"]:null; 
            $item->category_id =  isset($data["category_id"])?$data["category_id"]:null; 
            $item->parent_id   =  $cate->parent_id; 
            $item->short_code  =  $cate->short_code; 
            $item->description =  $cate->description; 
            if($request->hasFile("icon")  != null || $request->hasFile("icon") != false ){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("icon")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("icon")->getClientOriginalName();
                    if ($request->file("icon")->storeAs($dir_name, $new_file_name)) {
                        $uploaded_file_name = $new_file_name;
                        $item->icon         = $uploaded_file_name; 
                    }
                }
            } 
            $item->update();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.updated_successfull") ,
            ];
            return redirect("/woocommerce/shop/all")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/shop/all")->with("status",$output);
        }
    }
    // *4* update  SoftwareTop e_commerce
    // ..............................
    public function updateSoftwareTop(Request $request,$id){
        try{     
            $data              = $request->only(["top_name","top_description"]);
            \DB::beginTransaction();
            $item              = \App\Models\Ecommerce\Software::find($id);
            if(empty($item)){
                $output = [
                    "success" => 0 ,
                    "msg"     => __("messages.something_went_wrong") ,
                ];
                return redirect("/woocommerce/software")->with("status",$output);
            }
            
            $item->title        =  isset($data["top_name"])?$data["top_name"]:null; 
            $item->description  =  isset($data["top_description"])?$data["top_description"]:null; 
            $old_image          =  $item->image;
            $old_videos                = json_decode($item->video); 

            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                $dir_name =  config('constants.product_img_path');
                if($old_image != null && $old_image != ""){
                     
                        unlink(public_path('uploads/img/'.$old_image));
                    
                }
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    $data_a        = getimagesize($request->file("image"));
                    $width         = $data_a[0];
                    $height        = $data_a[1];
                    $half_width    = $width/1;
                    $half_height   = $height/1;
                    // $imgs          = \Image::make($request->file("image"))->resize($half_width,$half_height); 
                    //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                    // if ($imgs->save(base_path("public/public/uploads/img/$new_file_name"),20)) {
                    //     $uploaded_file_name        = $new_file_name;
                    //     $item->image               = $uploaded_file_name;
                    // }
                    if ($request->file("image")->move("public/uploads/img", $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $item->image             = $uploaded_file_name; 
                    }
                }
            }
            if($request->hasFile('vedio')){
                $array_video = [];
                $path_name   =  \time() . $request->file('vedio')->getClientOriginalName();
                if ($request->hasFile('vedio')) {
                    foreach($old_videos as $first){
                         $new_name =   $first ;
                        
                         if($new_name != null && $new_name != ""){
                            unlink(public_path("uploads/img/vedios/$new_name"));
                            
                        }
                    }
                    $video         = $request->file('vedio');
                    
                    $path          = $video->move("public/uploads/img/vedios", $path_name);
                    
                    // $path          = $video->store('vedios','public');
                    $array_video[] = $path_name;
                    // You can also store the video information in your database if needed.
                    // For example, you can store the path and other details in the 'videos' table.
                }
                 
                $item->video =  json_encode($array_video) ;
            }
            
            $more_image = json_decode($item->alter_image);
            
            if($more_image == null || $more_image == []){
                $more_image = [];
            }
            if ($request->hasFile('image_more')) {
                $count_doc1 = 1;
                foreach ($request->file('image_more') as $file) {
                    $file_name = time().'.'.$count_doc1++.'.'.$file->getClientOriginalName();
                    $file->move('public/uploads/img',$file_name);
                    array_push($more_image,$file_name);
                }
            }
            
            $item->alter_image = json_encode($more_image);
            $item->update();
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.updated_successfull") ,
            ];
            return redirect("/woocommerce/software")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/software")->with("status",$output);
        }
    }
    // *5* update Auth e_commerce
    // ..............................
    public function updateAuthImage(Request $request){
        try{     
            $data                    = $request->only(["login","signup"]);
            \DB::beginTransaction();
            $itemLogin               = \App\Models\Ecommerce::where("login",1)->where("view",1)->first();
            $itemSignUp              = \App\Models\Ecommerce::where("signup",1)->where("view",1)->first();
            if(empty($itemSignUp)){
                $output = [
                    "success" => 0 ,
                    "msg"     => __("messages.something_went_wrong") ,
                ];
                return redirect("/woocommerce/auth-info")->with("status",$output);
            }
            if(empty($itemLogin)){
                $output = [
                    "success" => 0 ,
                    "msg"     => __("messages.something_went_wrong") ,
                ];
                return redirect("/woocommerce/auth-info")->with("status",$output);
            }
            
             
            if(!empty($itemLogin)){
                $old_image_login                 = $itemLogin->image;
                if($request->hasFile("login") != null || $request->hasFile("login") != false){
                    $dir_name =  config('constants.product_img_path');
                    if ($request->file("login")->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time() . '_' . $request->file("login")->getClientOriginalName();
                        $data_a        = getimagesize($request->file("login"));
                        $width         = $data_a[0];
                        $height        = $data_a[1];
                        $half_width    = $width/1;
                        $half_height   = $height/1;
                        // $imgs          = \Image::make($request->file("login")); 
                        //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        
                        if ($request->file("login")->storeAs($dir_name, $new_file_name)) {
                            $uploaded_file_name      = $new_file_name;
                            $itemLogin->image        = $uploaded_file_name; 
                        }
                    }
                }
                $itemLogin->update();
                if($old_image_login != null && $old_image_login != ""){
                    unlink(public_path("/uploads/img/$old_image_login")); 
                }
            }

            if(!empty($itemSignUp)){
                $old_image_signUp                 = $itemSignUp->image;
                if($request->hasFile("signup") != null || $request->hasFile("signup") != false){
                    $dir_name =  config('constants.product_img_path');
                    if ($request->file("signup")->getSize() <= config('constants.document_size_limit')) {
                        $new_file_name = time() . '_' . $request->file("signup")->getClientOriginalName();
                        $data_a        = getimagesize($request->file("signup"));
                        $width         = $data_a[0];
                        $height        = $data_a[1];
                        $half_width    = $width/1;
                        $half_height   = $height/1;
                        // $imgs          = \Image::make($request->file("signup")); 
                        //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                        // if ($imgs->save(base_path("public/public/uploads/img/$new_file_name"),20)) {
                        //     $uploaded_file_name        = $new_file_name;
                        //     $itemSignUp->image          = $uploaded_file_name;
                        // }
                        if ($request->file("signup")->storeAs($dir_name, $new_file_name)) {
                            $uploaded_file_name      = $new_file_name;
                            $itemSignUp->image       = $uploaded_file_name; 
                        }
                    }
                }
                $itemSignUp->update();
                if($old_image_signUp != null && $old_image_signUp != ""){
                    unlink(public_path("/uploads/img/$old_image_signUp")); 
                }
            }
            
            \DB::commit();
            $output = [
                "success" => 1 ,
                "msg"     => __("messages.updated_successfull") ,
            ];
            return redirect("/woocommerce/auth-info")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/auth-info")->with("status",$output);
        }
    }
    // *6* update Auth e_commerce
    // ..............................
    public function AuthInfo(Request $request){
        try{     

            $itemLogin               = \App\Models\Ecommerce::where("login",1)->where("view",1)->first();
            $itemSignUp              = \App\Models\Ecommerce::where("signup",1)->where("view",1)->first();
             
            return view("woocommerce::woocommerce.auth_page")->with("login",$itemLogin)->with("signup",$itemSignUp);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/auth-info")->with("status",$output);
        }
    }
    // *7* delete Shop e_commerce
    // ..............................
    public function delShop(Request $request,$id){
        try{     
             
            \DB::beginTransaction();
            $item              = \App\Models\Ecommerce\ShopCategory::find($id); 
            if(!empty($item)){
                $item->delete();
                \DB::commit();
                $output = [
                    "success" => 1 ,
                    "msg"     => __("messages.deleted_successfull") ,
                ];
            }else{
                $output = [
                    "success" => 0 ,
                    "msg"     => __("messages.something_went_wrong") ,
                ];
            }
            return redirect("/woocommerce/shop/all")->with("status",$output);
        }catch(Exception $e) {
            $output = [
                "success" => 0 ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return redirect("/woocommerce/shop/all")->with("status",$output);
        }
    }
    // ..........................................................
    // *** ACCOUNT E-COMMERCE
    // *0* list account e_commerce
    // ..........................................................
    public function accountsSettings() {
        $tax         = [];
        $business_id = session()->get("user.business_id");
        $allData     = \App\Models\AccountSetting::allData();
        $patterns    = \App\Models\Pattern::forDropdown();
        $accounts    = \App\Account::items();
        $taxes       = \App\TaxRate::all();
        foreach($taxes as $i){$tax[$i->id]=$i->name;}
        $stores      = \App\Models\Warehouse::childs($business_id);
        return view("woocommerce::woocommerce.accounts_settings")->with(compact("allData","taxes","patterns","accounts","stores"));
    }
    // *1* update account e_commerce
    // ..........................................................
    public function updateAccountsSettings(Request $request) {

        try{
            $accountSetting = \App\Models\AccountSetting::first();
            $data           = $request->only([
                                    "pattern_id",
                                    "sale",
                                    "sale_tax",
                                    "sale_return",
                                    "sale_discount",
                                    "purchase",
                                    "purchase_tax",
                                    "purchase_return",
                                    "purchase_discount",
                                    "client_account_id",
                                    "client_visa_account_id",
                                    "client_store_id",
                                    "tax_id",
                                ]);
            if(empty($accountSetting)){
                \App\Models\AccountSetting::SaveSetting($data);
            }else{
                \App\Models\AccountSetting::UpdateSetting($data);
            }
            $alerts =  [
                        'success' => 1,
                        'msg'     => __("messages.updated_successfull")
                        ] ;
        }catch(Exception $e){
            $alerts =  [
                'success' => 0,
                'msg'     => __("messages.something_wrong")
                ] ;
            
        }
        return back()->with("status",$alerts);
    }
    // ..........................................................
    // *** PRODUCT E-COMMERCE
    // *1* LIST OF PRODUCT
    public function getEProduct(){
        $business_id = request()->session()->get("user.business_id");
        if(request()->ajax()){

            $check = request()->get("check");
            $products = \App\Product::where("business_id",$business_id);
            if(request()->get("name")){
                $data = request()->get("name");
                $products->where("id",$data) ;
            }
            if(request()->get("category")){
                $data = request()->get("category");
                $products->where("category_id",$data) ;
            }
            if(request()->get("sub_category")){
                $data = request()->get("sub_category");
                $products->where("sub_category_id",$data) ;
            }
            if(request()->get("brand")){
                $data = request()->get("brand");
                $products->where("brand_id",$data) ;
            }
            $products->get() ;
            return Datatables::of($products)
                    ->addColumn("action",function($row) use($check){
                        $all        = "All" ;
                        $Feature    = "Feature";
                        $Discount   = "Discount";
                        $Collection = "Collection";
                        $html =
                        '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="' . action('ProductController@changeFeature', ["id"=>$row->id,"Style" => $all]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add To E-commerce") . '</a></li>';
                        $html .= '<li><a href="' . action('ProductController@changeFeature', ["id"=>$row->id,"Style" => $Feature]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add To Feature List") . '</a></li>';
                        $html .= '<li><a href="' . action('ProductController@changeFeature', ["id"=>$row->id,"Style" => $Discount]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add To Discount List") . '</a></li>';
                        $html .= '<li><a href="' . action('ProductController@changeFeature', ["id"=>$row->id,"Style" => $Collection]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add To Collection List") . '</a></li>';
                        $html .= '<li><a href="' . action('ProductController@unChangeFeature', ["id"=>$row->id,"Style" => $all]) . '" class="view-product"><i class="fa fa-trash"></i> ' . __("Remove From E-commerce") . '</a></li>';
                        $html .= '<li><a href="' . action('ProductController@unChangeFeature', ["id"=>$row->id,"Style" => $Feature]) . '" class="view-product"><i class="fa fa-trash"></i> ' . __("Remove From Feature List") . '</a></li>';
                        $html .= '<li><a href="' . action('ProductController@unChangeFeature', ["id"=>$row->id,"Style" => $Discount]) . '" class="view-product"><i class="fa fa-trash"></i> ' . __("Remove From Discount List") . '</a></li>';
                        $html .= '<li><a href="' . action('ProductController@unChangeFeature', ["id"=>$row->id,"Style" => $Collection]) . '" class="view-product"><i class="fa fa-trash"></i> ' . __("Remove From Collection List") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    })
                    ->addColumn("name",function($row) use($check){
                        if($check == "Feature"){ 
                            $feature = $row->feature;
                        }elseif($check == "Discount"){ 
                            $feature = $row->ecm_discount;
                        }else{ 
                            $feature = $row->ecm_collection;
                        }
                        if($feature == 0){
                            $feature = "";
                        }else{
                            $feature = "<i class='fa fas fa-check'></i>";
                        }
                        return $row->name . "<br>"  ;
                    })
                    ->addColumn("code",function($row){
                        return $row->sku;
                    })
                    ->addColumn("description",function($row){
                        return $row->product_description;
                    })
                    ->addColumn("type",function($row){
                        $feature    = "";
                        $discount   = "";
                        $collection = "";
                        $ecommerce = "";
                        if($row->feature == 1){ 
                            $feature    = "<i class='fa fas fa-check'></i>";
                        }
                        if($row->ecm_discount == 1){ 
                            $discount   = "<i class='fa fas fa-check'></i>";
                        }
                        if($row->ecm_collection == 1){ 
                            $collection = "<i class='fa fas fa-check'></i>";
                        }
                        if($row->ecommerce == 1){ 
                            $ecommerce = "<i class='fa fas fa-check'></i>";
                        }
                        
                        return  "E-commerce " . $ecommerce ." <br> Feature " . $feature . " <br> Discount " . $discount . "<br> Collection " . $collection ;
                    })
                    ->rawColumns(['action',"type","name",'code', 'description'])
                    ->make(true);;
        }
    }
    // *2* LIST OF PRODUCT
    public function productSettings() {
        $business_id  = request()->session()->get('user.business_id');
        $products     = \App\Product::forDropdown($business_id); 
        $category     = \App\Category::forDropdown($business_id, 'product');
        $sub_category = \App\Category::forDropdownSub($business_id, 'product');
        $brands       = \App\Brands::forDropdown($business_id);
        return view("woocommerce::woocommerce.product_settings")->with(compact("products","category","sub_category","brands"));
    }
    // *3* UPDATE OF PRODUCT
    public function updateProductSettings() {
        
        try{
            
            $alerts['update'] = "success";
        }catch(Exception $e){
            $alerts['connection_failed'] = 'Unable to connect with WooCommerce, Check API settings';
            
        }
        return back();
    }
    // *4* SOFTWARE SETTING
    public function softwarePage() {
        $business_id  = request()->session()->get('user.business_id');
        $software     = \App\Models\Ecommerce\Software::where("topSection",1)->where("view",1)->first(); 
        $otherSoftware= \App\Models\Ecommerce\Software::where("view",1)->get();
        $products     = \App\Product::forDropdown($business_id); 
        $category     = \App\Category::forDropdown($business_id, 'product');
        $sub_category = \App\Category::forDropdownSub($business_id, 'product');
        $brands       = \App\Brands::forDropdown($business_id);
        return view("woocommerce::woocommerce.software_page")->with(compact("products","category","sub_category","brands","software","otherSoftware"));
    }
    // *5* SOFTWARE CREATE SETTING
    public function softwareCreate() {
      
        $business_id  = request()->session()->get('user.business_id');
        return view("woocommerce::woocommerce.software_create") ;
    }
    // *6* SOFTWARE Save SETTING
    public function softwareSave(Request $request) {
        $business_id  = request()->session()->get('user.business_id');
        try{
            $software                = new \App\Models\Ecommerce\Software();
            $software->title         = $request->title;
            $software->name          = $request->name;
            $software->description   = $request->description;
            $software->button        = $request->button;
            $software->view          = 1;
            if($request->topSection){
                $software->topSection          = 1;
            }
            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                $dir_name =  config('constants.product_img_path');
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    $data_a        = getimagesize($request->file("image"));
                    $width         = $data_a[0];
                    $height        = $data_a[1];
                    $half_width    = $width/1;
                    $half_height   = $height/1;
                    // $imgs          = \Image::make($request->file("image"))->resize($half_width,$half_height); 
                    //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                    // if ($imgs->save(base_path("public/public/uploads/img/$new_file_name"),20)) {
                    //     $uploaded_file_name        = $new_file_name;
                    //     $item->image               = $uploaded_file_name;
                    // }
                    if ($request->file("image")->move("public/uploads/img", $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $software->image         = $uploaded_file_name; 
                    }
                }
            }
            $software->save()   ;
             
             
            $alerts =  [
                        'success' => 1,
                        'msg'     => __("messages.added_successfull")
                        ] ;
        }catch(Exception $e){
            $alerts =  [
                'success' => 0,
                'msg'     => __("messages.something_wrong")
                ] ;
            
        }
        return redirect("/woocommerce/software")->with("status",$alerts);
         
    }
    // *5* SOFTWARE Edit SETTING
    public function softwareEdit(Request $request,$id) {
        $business_id  = request()->session()->get('user.business_id');
        $software     = \App\Models\Ecommerce\Software::find($id);
        return view("woocommerce::woocommerce.software_edit")->with("software",$software) ;
    }
    // *6* SOFTWARE Update SETTING
    public function softwareUpdate(Request $request,$id) {
        $business_id  = request()->session()->get('user.business_id');
        try{
            $software                = \App\Models\Ecommerce\Software::find($id);
            if(empty($software)){
                $alerts =  [
                'success' => 0,
                'msg'     => __("messages.something_wrong")
                ] ;
                return back()->with("status",$alerts);
            }
            $software->title         = $request->title;
            $software->name          = $request->name;
            $software->description   = $request->description;
            $software->button        = $request->button;
            if($request->topSection){
                $software->topSection          = 1;
            }else{
                $software->topSection          = 0;
                
            }
            $old_image               =  $software->image;
            if($request->hasFile("image") != null || $request->hasFile("image") != false){
                $dir_name =  config('constants.product_img_path');
                if($old_image != null && $old_image != ""){
                     
                        unlink(public_path('uploads/img/'.$old_image));
                    
                }
                if ($request->file("image")->getSize() <= config('constants.document_size_limit')) {
                    $new_file_name = time() . '_' . $request->file("image")->getClientOriginalName();
                    $data_a        = getimagesize($request->file("image"));
                    $width         = $data_a[0];
                    $height        = $data_a[1];
                    $half_width    = $width/1;
                    $half_height   = $height/1;
                    // $imgs          = \Image::make($request->file("image"))->resize($half_width,$half_height); 
                    //$request->$file_name->storeAs($dir_name, $new_file_name)  ||\public_path($new_file_name)
                    // if ($imgs->save(base_path("public/public/uploads/img/$new_file_name"),20)) {
                    //     $uploaded_file_name        = $new_file_name;
                    //     $item->image               = $uploaded_file_name;
                    // }
                    if ($request->file("image")->move("public/uploads/img", $new_file_name)) {
                        $uploaded_file_name      = $new_file_name;
                        $software->image         = $uploaded_file_name; 
                    }
                }
            }
            $software->update()   ;
             
             
            $alerts =  [
                        'success' => 1,
                        'msg'     => __("messages.updated_successfull")
                        ] ;
        }catch(Exception $e){
            $alerts =  [
                'success' => 0,
                'msg'     => __("messages.something_wrong")
                ] ;
            
        }
        return redirect("/woocommerce/software")->with("status",$alerts);
         
    }
    // *6* SOFTWARE Delete SETTING
    public function softwareDelete(Request $request,$id) {
        $business_id  = request()->session()->get('user.business_id');
        try{
            if(request()->ajax()){
                $software                = \App\Models\Ecommerce\Software::find($id);
                if(empty($software)){
                    $alerts =  [
                    'success' => 0,
                    'msg'     => __("messages.something_wrong")
                    ] ;
                    return back()->with("status",$alerts);
                }
                 
                $old_image               =  $software->image;
                if($request->hasFile("image") != null || $request->hasFile("image") != false){
                    $dir_name =  config('constants.product_img_path');
                    if($old_image != null && $old_image != ""){
                         
                            unlink(public_path('uploads/img/'.$old_image));
                        
                    }
                    
                }
                $software->delete();
                $alerts =  [
                            'success' => true,
                            'msg'     => __("messages.deleted_successfull")
                            ] ;
            }else{
                $alerts =  [
                    'success' => false,
                    'msg'     => __("messages.something_wrong")
                ] ;
            }
        }catch(Exception $e){
            $alerts =  [
                'success' => false,
                'msg'     => __("messages.something_wrong")
                ] ;
            
        }
        return $alerts ;
         
    }
    // *7* SOFTWARE delete images
    public function deleteImage() {
        try{     
            $business_id  = request()->session()->get('user.business_id');
            if(request()->ajax()){
                 $this_image = request()->input("name");
                
                \DB::beginTransaction();
                $item              = \App\Models\Ecommerce\Software::where("topSection",1)->where("view",1)->first(); 
                if(!empty($item)){
                    $list     = json_decode($item->alter_image);
                    $new_list = [];
                    foreach($list as $img){
                        if($img != $this_image){
                            $new_list[] = $img;
                        }else{
                            if(filter_var(public_path('uploads/img/'.$img), FILTER_VALIDATE_URL)){
                            unlink(public_path('uploads/img/'.$img));
                            }
                        }
                    }
                    $new_list          = json_encode($new_list);
                    
                    $item->alter_image = $new_list;
                    $item->update();
                    \DB::commit();
                    $output = [
                        "success" => true ,
                        "msg"     => __("messages.deleted_successfull") ,
                    ];
                }else{
                    $output = [
                        "success" => false ,
                        "msg"     => __("messages.something_went_wrong") ,
                    ];
                }
                return  $output ;
            }
        }catch(Exception $e) {
            $output = [
                "success" => false ,
                "msg"     => __("messages.something_went_wrong") ,
            ];
            return  $output ;
        }
        
    }
    // ..........................................................
    
    // *** CONNECTION WEBSITES
    // *1* LIST OF WEBSITES
    public function connectWebsite() {
        $business_id      = request()->session()->get('user.business_id');
        $list_of_company  = \App\Models\Ecommerce\ConnectionWebsite::select("company_name","e_commerce_url","erp_url","username","id")->get();
   
        $username  = [];
        $e_url     = [];
        $erp_url   = [];
        $companies = [];
        if(count($list_of_company) > 0){    
            foreach($list_of_company as $value){
                $companies[$value->company_name] = $value->company_name ;
                $username[$value->username]      = $value->username;
                $e_url[$value->e_commerce_url]   = $value->e_commerce_url;
                $erp_url[$value->erp_url]        = $value->erp_url;
            }
        }
      
        return view("woocommerce::woocommerce.connection_website")->with(compact("companies","erp_url","e_url","username"));
    }
    // *2* TABLE WEBSITES
    public function Websites(){
        $business_id  = request()->session()->get("user.business_id");
        
        if(request()->ajax()){

            $check    = request()->get("check");
            $websites = \App\Models\Ecommerce\ConnectionWebsite::select();
            if(request()->get("company_name")){
                $data     = request()->get("company_name");
                $websites->where("company_name",$data) ;
            }
            if(request()->get("e_commerce_url")){
                $data = request()->get("e_commerce_url");
                $websites->where("e_commerce_url",$data) ;
            }
            if(request()->get("erp_url")){
                $data = request()->get("erp_url");
                $websites->where("erp_url",$data) ;
            }
            if(request()->get("username")){
                $data = request()->get("username");
                $websites->where("username",$data) ;
            }
             
            $websites->get() ;
            return Datatables::of($websites)
                    ->addColumn("action",function($row) use($check){
                        $html  ='<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '</ul></div>';
                        return $html;
                    })->rawColumns(['action'])
                    ->make(true);
        }

    }
    // ..........................................................

    // *** BILL'S 
    // *1* LIST OF BILL
    public function getBill(){
        $business_id  = request()->session()->get("user.business_id");
        if(request()->ajax()){
             $bills    = \App\Transaction::where("business_id",$business_id)->where("ecommerce",1)->select()->get();
            // if(request()->get("")){
            //     $data = request()->get("");
            //     $bills->where("",$data) ;
            // }
            // if(request()->get("")){
            //     $data = request()->get("");
            //     $bills->where("",$data) ;
            // }
            return Datatables::of($bills)
                    ->addColumn("action",function($row){
                        $html  = '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        // $html .= '<li><a href="' . action('ProductController@changeFeature', ["id"=>$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add E-commerce") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    })
                    ->addColumn("invoice_no",function($row){
                        $invoice_no = $row->invoice_no;
                        return $invoice_no  ;
                    })
                    ->addColumn("final_total",function($row){
                        $final_total = $row->final_total;
                        return $final_total;
                    })
                    ->addColumn("transaction_date",function($row){
                        $transaction_date = $row->transaction_date;
                        return $transaction_date;
                    })
                    ->addColumn("status",function($row){
                        $status = ($row->status)?$row->status:"";
                        return $status;
                    })
                    ->addColumn( 'payment_status',
                    function ($row) {
                        $payment_status = \App\Transaction::getPaymentStatus($row);
                        if($payment_status == null){
                            $payment_status = "due" ; 
                        }
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
                     
                        
                    })->addColumn(
                        'delivery_status', function ($row)  {
                        
                            $product_list = [];
                            $sell = \App\TransactionSellLine::where("transaction_id",$row->id)->get();
                            foreach($sell as $it){
                                $product_list[] = $it->product_id;
                            }
                            $TransactionSellLine = \App\TransactionSellLine::where("transaction_id",$row->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                            $DeliveredPrevious   = \App\Models\DeliveredPrevious::where("transaction_id",$row->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                            $wrong               = \App\Models\DeliveredWrong::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                                
                            
                            if($DeliveredPrevious == null){
                                $payment_status = "not_delivereds";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong ,  "type" => "normal"  ,"approved"=> false]);
                            }else if($TransactionSellLine <= $DeliveredPrevious){
                                $payment_status = "delivereds";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id , "wrong" => $wrong , "type" => "normal"  ,"approved"=> false]);
                            
                            } else if( $DeliveredPrevious < $TransactionSellLine ){
        
                                $payment_status = "separates";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong ,  "type" => "normal"  ,"approved"=> false]);
        
        
                            }
                    }) 
                    ->addColumn("contact_id",function($row){
                        $contact_id = ($row->contact)?$row->contact->first_name:"";
                        return $contact_id;
                    })
                    ->rawColumns(["action","invoice_no","final_total","transaction_date","status","payment_status","delivery_status","contact_id"])
                    ->make(true);
        }
        return view("woocommerce::woocommerce.bills");
    }
    // .................................................. 
    // *** Cart'S 
    // *1* LIST OF Cart
    public function getCart(){
        $business_id  = request()->session()->get("user.business_id");
        if(request()->ajax()){
             $bills    = \App\Models\EcomTransaction::where("business_id",$business_id)->where("not_finished",1)->select()->get();
            // if(request()->get("")){
            //     $data = request()->get("");
            //     $bills->where("",$data) ;
            // }
            // if(request()->get("")){
            //     $data = request()->get("");
            //     $bills->where("",$data) ;
            // }
            return Datatables::of($bills)
                    ->addColumn("action",function($row){
                        $html  = '<div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">'. __("messages.actions") . '<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        // $html .= '<li><a href="' . action('ProductController@changeFeature', ["id"=>$row->id]) . '" class="view-product"><i class="fa fa-eye"></i> ' . __("Add E-commerce") . '</a></li>';
                        $html .= '</ul></div>';
                        return $html;
                    })
                    ->addColumn("invoice_no",function($row){
                        $invoice_no = $row->invoice_no;
                        return $invoice_no  ;
                    })
                    ->addColumn("final_total",function($row){
                        $final_total = $row->final_total;
                        return $final_total;
                    })
                    ->addColumn("transaction_date",function($row){
                        $transaction_date = $row->transaction_date;
                        return $transaction_date;
                    })
                    ->addColumn("status",function($row){
                        $status = ($row->status)?$row->status:"";
                        return $status;
                    })
                    ->addColumn( 'payment_status',
                    function ($row) {
                        $payment_status = \App\Transaction::getPaymentStatus($row);
                        if($payment_status == null){
                            $payment_status = "due" ; 
                        }
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
                     
                        
                    })->addColumn(
                        'delivery_status', function ($row)  {
                        
                            $product_list = [];
                            $sell = \App\TransactionSellLine::where("transaction_id",$row->id)->get();
                            foreach($sell as $it){
                                $product_list[] = $it->product_id;
                            }
                            $TransactionSellLine = \App\TransactionSellLine::where("transaction_id",$row->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(quantity) as total"))->first()->total;
                            $DeliveredPrevious   = \App\Models\DeliveredPrevious::where("transaction_id",$row->id)->whereIn("product_id",$product_list)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                            $wrong               = \App\Models\DeliveredWrong::where("transaction_id",$row->id)->select(DB::raw("SUM(current_qty) as total"))->first()->total;
                                
                            
                            if($DeliveredPrevious == null){
                                $payment_status = "not_delivereds";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong ,  "type" => "normal"  ,"approved"=> false]);
                            }else if($TransactionSellLine <= $DeliveredPrevious){
                                $payment_status = "delivereds";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id , "wrong" => $wrong , "type" => "normal"  ,"approved"=> false]);
                            
                            } else if( $DeliveredPrevious < $TransactionSellLine ){
        
                                $payment_status = "separates";
                                return (string) view('sell.partials.deleivery_status', ['payment_status' => $payment_status, 'id' => $row->id, "wrong" => $wrong ,  "type" => "normal"  ,"approved"=> false]);
        
        
                            }
                    }) 
                    ->addColumn("contact_id",function($row){
                        $contact_id = ($row->contact)?$row->contact->first_name:"";
                        return $contact_id;
                    })
                    ->addColumn("mobile",function($row){
                        $mobile = ($row->contact)?$row->contact->mobile:"";
                        return $mobile;
                    })
                    ->rawColumns(["action","invoice_no","final_total","transaction_date","status","payment_status","delivery_status","contact_id","mobile"])
                    ->make(true);
        }
        return view("woocommerce::woocommerce.carts");
    }
    // .................................................. 
    // *** Logo'S 
    // *1* Page OF Logo && Color
    public function getLogo(){
        $business_id  = request()->session()->get("user.business_id");
        $data         =  [ ] ;
        $logo         =  \App\Business::getLogo($data);
        $business     =  \App\Business::find($business_id);
        return view("woocommerce::woocommerce.logo")->with(compact(["logo","business"]));
    }
    // *2* SAVE SETTING
    public function imageCropPost(Request $request)
    {
        $data = $request->image;
        $business_id = $request->business_id;
        \DB::beginTransaction();
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data              = base64_decode($data);
        $image_name        = time() . '.png';
        $path              = public_path() . "/uploads/logo/" . $image_name;
        $fil = "public/uploads/logo/" . $image_name;
        file_put_contents($path, $data);
        $business             =  \App\Business::find($business_id);
        $business->web_logo   =  $fil;
        $business->update();
        \DB::commit();
        return response()->json(['status' => 1, 'message' => "Image uploaded successfully"]);
    }
    // *2* SAVE SETTING
    public function imageFloatCropPost(Request $request)
    {
        $data = $request->image;
        $id   = $request->id;
        $business_id = $request->business_id;
        \DB::beginTransaction();
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data              = base64_decode($data);
        $image_name        = time() . '.png';
        $path              = public_path() . "/uploads/img/" . $image_name;
        $fil =   $image_name;
        file_put_contents($path, $data);
        $item              =  \App\Models\Ecommerce\FloatingBar::find($id);
        $item->icon        =  $fil;
        $item->update();
        \DB::commit();
        return response()->json(['status' => 1, 'message' => "Image uploaded successfully"]);
    }
    // *2* Change Color SETTING
    public function changeColor(Request $request)
    {
        $business_id     =  session()->get("user.business_id");
        $business        =  \App\Business::find($business_id);
        \DB::beginTransaction();
        
        if(isset($request->web_color)){
            $business->web_color         =  $request->web_color       ;
        }
        if(isset($request->web_second_color)){
            $business->web_second_color  =  $request->web_second_color;
        }
        if(isset($request->web_font_color)){
            $business->web_font_color    =  $request->web_font_color  ;
        }
        $business->update();
        \DB::commit();
        return  redirect()->back();
    }
    // .................................................. 


}
