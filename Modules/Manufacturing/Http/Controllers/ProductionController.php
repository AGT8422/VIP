<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\BusinessLocation;
use App\Transaction;
use App\AccountTransaction;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Variation;
use App\PurchaseLine;
use App\TransactionSellLine;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Manufacturing\Entities\MfgRecipe;
use Modules\Manufacturing\Utils\ManufacturingUtil;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Warehouse;

class ProductionController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $mfgUtil;
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, ManufacturingUtil $mfgUtil, BusinessUtil $businessUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->mfgUtil = $mfgUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_production')) {
            abort(403, 'Unauthorized action.');
        }
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        if (request()->ajax()) {
            $productions = Transaction::join(
                'business_locations AS bl',
                'transactions.location_id',
                '=',
                'bl.id'
                )->join('purchase_lines as pl', 'pl.transaction_id', '=', 'transactions.id')
                ->leftJoin('units as su', 'pl.sub_unit_id', '=', 'su.id')
                ->join('variations as v', 'v.id', '=', 'pl.variation_id')
                ->join('product_variations as pv', 'pv.id', '=', 'v.product_variation_id')
                ->join('products as p', 'p.id', '=', 'v.product_id')
                ->join('units as u', 'p.unit_id', '=', 'u.id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'production_purchase')
                ->select(
                    'transactions.id',
                    'transaction_date',
                    'ref_no',
                    'p.id as p_id',
                    'bl.name as location_name',
                    DB::raw('IF(p.type="variable", 
                            CONCAT(p.name, " - ", pv.name, " - ", v.name, " (", v.sub_sku, ")"), 
                            CONCAT(p.name, " (", v.sub_sku, ")") 
                            ) as product_name'),
                    'pl.quantity',
                    'final_total',
                    'su.short_name as sub_unit_name',
                    'su.base_unit_multiplier',
                    'u.short_name as unit_name',
                    'mfg_is_final'
                )->groupBy('transactions.id');

            return Datatables::of($productions)
                ->addColumn('action', function ($row) {
                    $html = '<button data-href="' .  action('\Modules\Manufacturing\Http\Controllers\ProductionController@show', [$row->id]) . '" class="btn btn-info btn-xs btn-modal" data-container=".view_modal"><i class="fa fa-eye"></i> ' . __('messages.view') . '</button>';
                    $html .= ' <a href="' .  action('\Modules\Manufacturing\Http\Controllers\ProductionController@edit', [$row->id]) . '" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> ' . __('messages.edit') . '</a>';
                    if(request()->session()->get("user.id") == 1 || request()->session()->get("user.id") == 7 || request()->session()->get("user.id") == 8){
                        $html .= ' <button data-href="' . action('\Modules\Manufacturing\Http\Controllers\ProductionController@destroy', [$row->id]) . '" class="delete-production btn btn-xs btn-danger"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</button>';
                    }
                    
                    $html .= ' <button data-href="' . action('\Modules\Manufacturing\Http\Controllers\ProductionController@entry', [$row->id]) . '" class="btn btn-xs btn-modal"  data-container=".view_modal"><i class="fa fa-eye"></i> ' . __("home.Entry") . '</button>';

                    return $html;
                })
                ->editColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{$final_total}}">{{$final_total}}</span>'
                )
                ->editColumn(
                    'quantity',
                    function ($row) {
                        $qty = empty($row->base_unit_multiplier) ? $row->quantity : $row->quantity / $row->base_unit_multiplier ;
                        $unit = empty($row->sub_unit_name) ? $row->unit_name : $row->sub_unit_name;
                        return "<span class='display_currency' data-currency_symbol='false' data-orig-value='$qty'>$qty</span> $unit";
                    }
                )
                ->editColumn(
                    'product_name',
                    function ($row) {
                        $html  = $row->product_name;
                        $html .='<br>
                            <div class="btn-group"><button type="button" class="btn btn-info dropdown-toggle btn-xs no-print" data-toggle="dropdown" aria-expanded="false">'.__("messages.actions").'<span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu dropdown-menu-left" role="menu">' .
                            '<li><a data-href="'. action('ProductController@view', [ $row->p_id] ).' " class="btn-modal" data-container=".view_modal"><i class="fa fa-eye"></i>'.__("messages.view").'</a></li>'.
                            '<li><a href="'.action('ProductController@edit', [ $row->p_id]).'"><i class="glyphicon glyphicon-edit"></i>'.__("messages.edit").'</a></li>'.
                            '<li><a href="'.action('ItemMoveController@index', [$row->p_id]).'"><i class="fas fa-history"></i>'.__("lang_v1.product_stock_history").'</a></li>'.
                            '</ul>'.
                            '<button type="button" style="margin-left:10px" class="btn btn-primary btn-xs btn-modal no-print"   data-container=".view_modal" data-href="'.action('ProductController@viewStock', [$row->p_id]) .'">'.__('lang_v1.view_Stock').'</button>'. 
                            '<button type="button" style="margin-left:10px" class="btn bg-yellow btn-xs btn-modal no-print" data-container=".view_modal" data-href="'.action('ProductController@viewUnrecieved', [$row->p_id]) .'">'.__('recieved.should_recieved').'</button>'. 
                            '</div>';
                        return  $html;
                    }
                )
                ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                ->rawColumns(['final_total', 'action','product_name', 'quantity'])
                ->filterColumn('product_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(p.name, ' - ', pv.name, ' - ', v.name, ' (', v.sub_sku, ')') like ?", ["%{$keyword}%"]);
                })
                ->make(true);
        }

        return view('manufacturing::production.index')->with(compact("currency_details"));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_production')) {
            abort(403, 'Unauthorized action.');
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        $recipe_dropdown    = MfgRecipe::forDropdown($business_id);
        $stores             = Warehouse::childs($business_id);
        $out                = \App\Business::find($business_id);
        if(!empty($out)){
            $store_out  = $out->store_mfg ;
        }else{
            $store_out  = null ;
        }
        $row                  = 1;$line_prices  = [];#2024-8-6
        $list_of_prices       = \App\Product::getListPrices($row);
        return view('manufacturing::production.create')
                ->with(compact('business_locations', 'store_out' ,'list_of_prices' ,'recipe_dropdown','stores'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_production')) {
            abort(403, 'Unauthorized action.');
        }
          
        try {
             
            $request->validate([
                'transaction_date' => 'required',
                'location_id'      => 'required',
                'final_total'      => 'required'
            ]);

            //Create Production purchase
            $manufacturing_settings               = $this->mfgUtil->getSettings($business_id);
            $user_id                              = $request->session()->get('user.id');
            $transaction_data                     = $request->only([ 'ref_no', 'transaction_date', 'location_id','list_price', 'final_total','final_before']);

            $is_final                             = !empty($request->input('finalize')) ? 1 : 0;
            $transaction_data['business_id']      = $business_id;
            $transaction_data['created_by']       = $user_id;
            $transaction_data['store']            = $request->store_in ;
            $transaction_data['store_in']         = $request->store_in;
            $transaction_data['type']             = 'production_purchase';
            $transaction_data['status']           = $is_final ? 'received' : 'pending';
            $transaction_data['payment_status']   = 'due';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], true);
            $transaction_data['final_total']      = $this->productUtil->num_uf($transaction_data['final_total']);

            # Update reference count
            # Generate reference number
            $ref_count = $this->productUtil->setAndGetReferenceCount($transaction_data['type']);
            if (empty($transaction_data['ref_no'])) {
                $prefix                     = !empty($manufacturing_settings['ref_no_prefix']) ? $manufacturing_settings['ref_no_prefix'] : null;
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber($transaction_data['type'], $ref_count, null, $prefix);
            }

            $variation_id   = $request->input('variation_id');
            $variation      = Variation::where('id', $variation_id)->with(['product'])->first();
            $final_total    = $request->input('final_total');
            $quantity       = $request->input('quantity');
            $waste_units    = $this->productUtil->num_uf($request->input('mfg_wasted_units'));
            $uf_qty         = $this->productUtil->num_uf($quantity);
            
            if (!empty($waste_units)) {
                $new_qty  = $uf_qty - $waste_units;
                $uf_qty   = $new_qty;
                $quantity = $this->productUtil->num_f($new_qty);
            }

            $final_total_uf                          = $this->productUtil->num_uf($final_total);
            $unit_purchase_line_total                = $final_total_uf / $uf_qty;
            $unit_purchase_line_total_f              = $this->productUtil->num_f($unit_purchase_line_total);

            $transaction_data['mfg_wasted_units']    = $waste_units;
            $transaction_data['mfg_production_cost'] = $this->productUtil->num_uf($request->input('production_cost'));
            $transaction_data['mfg_is_final']        = $is_final;
      
            $purchase_line_data = [
                'variation_id'           => $variation_id,
                'quantity'               => $quantity,
                'list_price'             => $request->list_price,
                'product_id'             => $variation->product_id,
                'product_unit_id'        => $variation->product->unit_id,
                'pp_without_discount'    => $unit_purchase_line_total_f,
                'discount_percent'       => 0,
                'purchase_price'         => $unit_purchase_line_total_f,
                'purchase_price_inc_tax' => $unit_purchase_line_total_f,
                'unit_cost_after_tax'    => $unit_purchase_line_total_f,
                'item_tax'               => 0,
                'purchase_line_tax_id'   => null,
                'mfg_date'               => $this->transactionUtil->format_date($transaction_data['transaction_date'])
            ];
            if (request()->session()->get('business.enable_lot_number') == 1) {
                $purchase_line_data['lot_number'] = $request->input('lot_number');
            }
            if (request()->session()->get('business.enable_product_expiry') == 1) {
                $purchase_line_data['exp_date']   = $request->input('exp_date');
            }
            if (!empty($request->input('sub_unit_id'))) {
                $purchase_line_data['sub_unit_id'] = $request->input('sub_unit_id');
            }
            DB::beginTransaction();
            $transaction          = Transaction::create($transaction_data);
            $currency_details     = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $update_product_price = !empty($manufacturing_settings['enable_updating_product_price']) && $is_final ? true : false;
            $this->productUtil->createOrUpdatePurchaseLines($transaction, [$purchase_line_data], $currency_details, $update_product_price,null,null,null,$request);

            //Adjust stock over selling if found 
            $this->productUtil->adjustStockOverSelling($transaction);

            # Create production sell
            $transaction_sell_data = [
                'business_id'                       => $business_id,
                'location_id'                       => $transaction->location_id,
                'store'                             => $request->store_id,
                'store_in'                          => $request->store_in,
                'transaction_date'                  => $transaction->transaction_date,
                'created_by'                        => $transaction->created_by,
                'status'                            => $is_final ? 'final' : 'draft',
                'type'                              => 'production_sell',
                'mfg_parent_production_purchase_id' => $transaction->id,
                'payment_status'                    => 'due',
                'final_total'                       => $transaction->final_total
            ];

            $sell_lines            = [];
            $ingredient_quantities = !empty($request->input('ingredients')) ? $request->input('ingredients') : [];

            # Get ingredient details to create sell lines
            $recipe = MfgRecipe::where('variation_id', $variation_id)->first();

            $all_variation_details = $this->mfgUtil->getIngredientDetails($recipe, $business_id);

            foreach ($all_variation_details as $variation_details) {
                $variation         = $variation_details['variation'];
                $line_sub_unit_id  = !empty($ingredient_quantities[$variation_details['id']]['sub_unit_id']) ? $ingredient_quantities[$variation_details['id']]['sub_unit_id'] : null;
                $line_multiplier   = !empty($line_sub_unit_id) ? $variation_details['sub_units'][$line_sub_unit_id]['multiplier'] : 1;
                $mfg_waste_percent = !empty($ingredient_quantities[$variation_details['id']]['mfg_waste_percent']) ? $this->productUtil->num_uf($ingredient_quantities[$variation_details['id']]['mfg_waste_percent']) : 0;
                $cost              = \App\Product::product_cost($variation->product_id);
                $sell_lines[]      = [
                        'product_id'           => $variation->product_id,
                        'variation_id'         => $variation->id,
                        'quantity'             => $this->productUtil->num_uf($ingredient_quantities[$variation_details['id']]['quantity']),
                        'item_tax'             => 0,
                        'tax_id'               => null,
                        'unit_price'           => $cost * $line_multiplier,
                        'unit_price_inc_tax'   => $cost * $line_multiplier,
                        'enable_stock'         => $variation_details['enable_stock'],
                        'product_unit_id'      => $variation->product->unit_id,
                        'sub_unit_id'          => $line_sub_unit_id,
                        'base_unit_multiplier' => $line_multiplier,
                        'mfg_waste_percent'    => $mfg_waste_percent
                    ];
            }

            # Create Sell Transfer transaction
            $production_sell = Transaction::create($transaction_sell_data);
            if (!empty($sell_lines)) {
                $this->transactionUtil->createOrUpdateSellLines($production_sell, $sell_lines, $transaction_sell_data['location_id'], null, null, ['mfg_waste_percent' => 'mfg_waste_percent']);
            }
            if ($production_sell->status == 'final') {
                foreach ($sell_lines as $sell_line) {
                    if ($sell_line['enable_stock']) {
                        $line_qty = $sell_line['quantity'] * $sell_line['base_unit_multiplier'];
                        $this->productUtil->decreaseProductQuantity(
                            $sell_line['product_id'],
                            $sell_line['variation_id'],
                            $production_sell->location_id,
                            $line_qty
                        );
                    }
                }
                # Map sell lines with purchase lines  $this->transactionUtil->mapPurchaseSell($business, $production_sell->sell_lines, 'production_purchase');
                $business = [
                            'id'                => $business_id,
                            'accounting_method' => $request->session()->get('business.accounting_method'),
                            'location_id'       => $production_sell->location_id
                        ];
               
            }

            if ($production_sell->status == 'final') {
                foreach ($request->ingredients as $ing) {
                    $variation  = Variation::where('id', $ing['variation_id'])->with(['product'])->first() ;
                    $quantity   = $ing['quantity'];
                    $store_id   = $request->store_id; 
                    $sell_line  = \App\TransactionSellLine::where('transaction_id',$production_sell->id)->where('product_id',$variation->product_id)->first();
                    \App\Models\WarehouseInfo::update_stoct($sell_line->product_id,$request->store_id,($quantity*-1),$business_id);
                    \App\MovementWarehouse::movemnet_warehouse($production_sell,$sell_line->product,$quantity,$request->store_id,$sell_line,$type="decrease");
                }
            }

            if ($production_sell->status == 'final'){
                $variation  =  Variation::find($request->variation_id);
                \App\Models\WarehouseInfo::update_stoct($variation->product_id,$request->store_in,$request->quantity,$business_id);
                $price      =  (str_replace(',','',$request->final_total)/$request->quantity);
                \App\MovementWarehouse::production($production_sell,$variation->product,$request->quantity,$request->store_in,$price);
                $type_cost  = null;
                 if($request->input('production_cost')){ $type_cost = $request->input('production_cost');}
                \App\AccountTransaction::production_entry($production_sell,$price,$request->input("final_before"),$type_cost);
                if( !empty($production_sell)  && !empty($transaction) ){
                    $COSTs = ($type_cost!=NULL)?$type_cost:0; 
                    \App\Models\ItemMove::production($production_sell->id,$transaction->id,$COSTs);
                }
            }
            DB::commit();
            $output = [
                    'success' => 1,
                    'msg'     => __('lang_v1.added_success')
                ];
        } catch (Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg'     => __('messages.something_went_wrong')
            ];
        }

        return redirect()->action('\Modules\Manufacturing\Http\Controllers\ProductionController@index')->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_production')) {
            abort(403, 'Unauthorized action.');
        }

        $production_purchase = Transaction::where('business_id', $business_id)
                                    ->where('type', 'production_purchase')
                                    ->with(['purchase_lines', 'purchase_lines.variations', 'purchase_lines.variations.product_variation', 'purchase_lines.variations.product',
                                        'purchase_lines.sub_unit', 'purchase_lines.variations.product.unit'])
                                    ->findOrFail($id);

        $production_sell     = Transaction::where('business_id', $business_id)
                                    ->where('type', 'production_sell')
                                    ->where('mfg_parent_production_purchase_id', $production_purchase->id)
                                    ->with(['sell_lines', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'sell_lines.variations.product', 'sell_lines.sub_unit'])
                                    ->first();

        $purchase_line         = $production_purchase->purchase_lines[0];
        $base_unit_multiplier  = !empty($purchase_line->sub_unit) ? $purchase_line->sub_unit->base_unit_multiplier : 1;
        $quantity              = $purchase_line->quantity / $base_unit_multiplier;
        $quantity_wasted       = 0;
        $unit_name             = !empty($purchase_line->sub_unit) ?  $purchase_line->sub_unit->short_name : $purchase_line->variations->product->unit->short_name;
        if (!empty($production_purchase->mfg_wasted_units)) {
            $quantity_wasted   = $production_purchase->mfg_wasted_units;
            $quantity         += $quantity_wasted;
        }
        $actual_quantity       = $quantity * $base_unit_multiplier;

        $ingredients             = [];
        $total_ingredients_price = 0;
        # Format sell lines
        foreach ($production_sell->sell_lines as $sell_line) {
            $variation                = $sell_line->variations;
            $sell_line_qty            = empty($sell_line->sub_unit) ? $sell_line->quantity : $sell_line->quantity / $sell_line->sub_unit->base_unit_multiplier;
            $unit                     = empty($sell_line->sub_unit) ? $variation->product->unit->short_name : $sell_line->sub_unit->short_name;
            $cost                     = \App\Product::product_cost($variation->product->id);
            $line_total_price         = $sell_line->unit_price * $sell_line->quantity;
            $total_ingredients_price += $line_total_price;

            $waste_percent            = !empty($sell_line->mfg_waste_percent) ? $sell_line->mfg_waste_percent : 0;
            $wasted_qty               = $this->moduleUtil->calc_percentage($sell_line_qty, $waste_percent);
            $final_quantity           = $sell_line_qty - $wasted_qty;

            $ingredients[] = [
                'dpp_inc_tax'     => $variation->dpp_inc_tax,
                'quantity'        => $sell_line_qty,
                'full_name'       => $variation->full_name,
                'id'              => $variation->id,
                'unit'            => $unit,
                'allow_decimal'   => $variation->product->unit->allow_decimal,
                'variation'       => $variation,
                'enable_stock'    => $variation->product->enable_stock,
                'total_price'     => $line_total_price,
                'waste_percent'   => $waste_percent,
                'final_quantity'  => $final_quantity
           ];
        }

        $total_production_cost = 0;
        if (!empty($production_purchase->mfg_production_cost)) {
            $total_production_cost = $this->transactionUtil->calc_percentage($total_ingredients_price, $production_purchase->mfg_production_cost);
        }

        return view('manufacturing::production.show')->with(compact('production_purchase', 'production_sell', 'purchase_line', 'ingredients', 'unit_name', 'quantity', 'quantity_wasted', 'actual_quantity', 'total_production_cost'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_production')) {
            abort(403, 'Unauthorized action.');
        }

        $production_purchase = Transaction::where('business_id', $business_id)
                                    ->where('type', 'production_purchase')
                                    ->with(['purchase_lines', 'purchase_lines.variations', 'purchase_lines.variations.product_variation', 'purchase_lines.variations.product'])
                                    ->findOrFail($id);

        // //Finalized production should not be editable
        // if ($production_purchase->mfg_is_final == 1) {
        //     $output = ['success' => 0,
        //                 'msg' => __('messages.something_went_wrong')
        //             ];
        //     return redirect()->action('\Modules\Manufacturing\Http\Controllers\ProductionController@index')->with('status', $output);
        // }
        $out                = \App\Business::find($business_id);
        if(!empty($out)){
            $store_out  = $out->store_mfg ;
        }else{
            $store_out  = null ;
        }

        $production_sell = Transaction::where('business_id', $business_id)
                                    ->where('type', 'production_sell')
                                    ->where('mfg_parent_production_purchase_id', $production_purchase->id)
                                    ->with(['sell_lines', 'sell_lines.variations', 'sell_lines.variations.product_variation', 'sell_lines.variations.product', 'sell_lines.variations.product.unit'])
                                    ->first();
                        
        $purchase_line        = $production_purchase->purchase_lines[0];
        $recipe               = MfgRecipe::where('variation_id', $purchase_line->variation_id)->first();
        $store                = $production_purchase->store;
        $base_unit_multiplier = !empty($purchase_line->sub_unit) ? $purchase_line->sub_unit->base_unit_multiplier : 1;
        $quantity             = $purchase_line->quantity / $base_unit_multiplier;
        $quantity_wasted      = 0;
        
        if (!empty($production_purchase->mfg_wasted_units)) {
            $quantity_wasted  = $production_purchase->mfg_wasted_units;
            $quantity        += $quantity_wasted;
        }
        $actual_quantity      = $quantity * $base_unit_multiplier;

        $sub_units            = $this->moduleUtil->getSubUnits($business_id, $purchase_line->variations->product->unit->id);
        $unit_name            = $purchase_line->variations->product->unit->short_name;
        $sub_unit_id          = $purchase_line->sub_unit_id;

        $ingredients             = [];
        $total_ingredients_price = 0;
        foreach ($production_sell->sell_lines as $sell_line) {
            $variation = $sell_line->variations;

            # $line_sub_units = $this->moduleUtil->getSubUnits($business_id, $variation->product->unit->id);
            $pro        = \App\Product::find($variation->product->id); 
            $var        = ($pro->variations)?$pro->variations->first()->default_purchase_price:0;
        
            $allUnits   = [];
            $business   = \App\Business::find($pro->business_id);
            $allUnits[$pro->unit_id] = [
                'name'           => $pro->unit->actual_name,
                'multiplier'     => $pro->unit->base_unit_multiplier,
                'allow_decimal'  => $pro->unit->allow_decimal,
                'price'          => $var,
                'check_price'    => 0,
                ];
            if($pro->sub_unit_ids != null){
                foreach($pro->sub_unit_ids  as $i){
                        $row_price    =  0;
                        $un           = \App\Unit::find($i);
                        $row_price    = \App\Models\ProductPrice::where("unit_id",$i)->where("product_id",$pro->id)->where("number_of_default",0)->first();
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
            $line_sub_units = $allUnits  ;

            $is_line_sub_unit = false;
            $line_sub_unit_id = null;
            $multiplier       = 1;
            $line_unit_name   = $variation->product->unit->short_name;
            $allow_decimal    = $variation->product->unit->allow_decimal;
            if (!empty($line_sub_units)) {
                foreach ($line_sub_units as $key => $value) {
                    if (!empty($sell_line->sub_unit_id) && $sell_line->sub_unit_id == $key) {
                        $line_sub_unit_id = $sell_line->sub_unit_id;
                        $multiplier       = $value['multiplier'];
                        $allow_decimal    = $value['allow_decimal'];
                        $line_unit_name   = $value['name'];
                    }
                }
                $is_line_sub_unit = true;
            }

            $unit_quantity            = $sell_line->quantity / $actual_quantity;
            $line_total_price         = $variation->dpp_inc_tax * $sell_line->quantity;
            $total_ingredients_price += $line_total_price;

            $waste_percent            = !empty($sell_line->mfg_waste_percent) ? $sell_line->mfg_waste_percent : 0;
            $wasted_qty               = $this->moduleUtil->calc_percentage($sell_line->quantity, $waste_percent);
            $final_quantity           = ($sell_line->quantity - $wasted_qty) / $multiplier;
            $list_of_prices_in_unit   = \App\Product::getProductPrices($variation->product->id);
            $ingredients[] = [
                'dpp_inc_tax'            => $variation->dpp_inc_tax,
                'quantity'               => $sell_line->quantity / $multiplier,
                'full_name'              => $variation->full_name,
                'variation_id'           => $variation->id,
                'unit'                   => $line_unit_name,
                'allow_decimal'          => $allow_decimal,
                'variation'              => $variation,
                'enable_stock'           => $variation->product->enable_stock,
                'is_sub_unit'            => $is_line_sub_unit,
                'sub_units'              => $line_sub_units,
                'sub_unit_id'            => $line_sub_unit_id,
                'list_of_prices_in_unit' => $list_of_prices_in_unit,
                'multiplier'             => $multiplier,
                'unit_quantity'          => $unit_quantity,
                'total_price'            => $line_total_price,
                'waste_percent'          => $waste_percent,
                'final_quantity'         => $final_quantity,
                'id'                     => $sell_line->id
           ];
        }

        $total_production_cost = 0;
        if (!empty($recipe->extra_cost)) {
            $total_production_cost = $this->transactionUtil->calc_percentage($total_ingredients_price, $recipe->extra_cost);
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        $variation_name     = $purchase_line->variations->product->name;
        if ($purchase_line->variations->product->type == 'variable') {
            $variation_name .= ' - ' .
            $purchase_line->variations->product_variation->name .
            ' - ' . $purchase_line->variations->name;
        }
        $variation_name  .= ' (' . $purchase_line->variations->sub_sku . ')';
        $recipe_dropdown  = [$purchase_line->variation_id => $variation_name];

        $business_details = $this->businessUtil->getDetails($business_id);
        $pos_settings     = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);

        $manufacturing_settings = $this->mfgUtil->getSettings($business_id);
        $stores                 = Warehouse::childs($business_id);
        $typees = "edit";
        $row              = 1;$line_prices  = [];#2024-8-6
        $list_of_prices   = \App\Product::getListPrices($row);
        return view('manufacturing::production.edit')->with(compact('production_purchase','list_of_prices','store_out','typees','store','stores','production_sell', 'business_locations', 'recipe_dropdown', 'ingredients', 'business_details', 'pos_settings', 'sub_units', 'quantity', 'quantity_wasted', 'actual_quantity', 'recipe', 'unit_name', 'sub_unit_id', 'total_production_cost', 'manufacturing_settings'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
 
        $business_id = $request->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_production')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {

            $request->validate([
                'transaction_date' => 'required',
                'location_id'      => 'required',
                'final_total'      => 'required'
            ]);

            # Create Production purchase
            $transaction_data       = $request->only([ 'ref_no', 'transaction_date', 'location_id', 'final_total','final_before']);
            $is_final               = !empty($request->input('finalize')) ? 1 : 0;
            $manufacturing_settings = $this->mfgUtil->getSettings($business_id);

            $transaction_data['status']           = $is_final ? 'received' : 'pending';
            $transaction_data['payment_status']   = 'due';
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['transaction_date'], true);
            $transaction_data['final_total']      = $this->productUtil->num_uf($transaction_data['final_total']);

            $variation_id      = $request->input('variation_id');
            $variation         = Variation::where('id', $variation_id)->with(['product'])->first();
            $final_total       = $request->input('final_total');
            $quantity          = $request->input('quantity');
            $waste_units       = $this->productUtil->num_uf($request->input('mfg_wasted_units'));
            $uf_qty            = $this->productUtil->num_uf($quantity);
            if (!empty($waste_units)) {
                $new_qty       = $uf_qty - $waste_units;
                $uf_qty        = $new_qty;
                $quantity      = $this->productUtil->num_f($new_qty);
            }
            $final_total_uf    = $this->productUtil->num_uf($final_total);

            $unit_purchase_line_total   = $final_total_uf / $uf_qty;
            $unit_purchase_line_total_f = $this->productUtil->num_f($unit_purchase_line_total);

            $transaction_data['mfg_wasted_units']    = $waste_units;
            $transaction_data['store']               = $request->store_id;
            $transaction_data['store_in']            = $request->store_in;
            $transaction_data['mfg_production_cost'] = $this->productUtil->num_uf($request->input('production_cost'));
            $transaction_data['mfg_is_final']        = $is_final;
            $purchase_line_data = [
                'variation_id'           => $variation_id,
                'quantity'               => $quantity,
                'product_id'             => $variation->product_id,
                'product_unit_id'        => $variation->product->unit_id,
                'pp_without_discount'    => $unit_purchase_line_total_f,
                'discount_percent'       => 0,
                'purchase_price'         => $unit_purchase_line_total_f,
                'purchase_price_inc_tax' => $unit_purchase_line_total_f,
                'unit_cost_after_tax'    => $unit_purchase_line_total_f,
                'item_tax'               => 0,
                'purchase_line_tax_id'   => null,
                'mfg_date'               => $this->transactionUtil->format_date($transaction_data['transaction_date'])
            ];

            if (request()->session()->get('business.enable_lot_number') == 1) {
                $purchase_line_data['lot_number'] = $request->input('lot_number');
            }

            if (request()->session()->get('business.enable_product_expiry') == 1) {
                $purchase_line_data['exp_date'] = $request->input('exp_date');
            }

            if (!empty($request->input('sub_unit_id'))) {
                $purchase_line_data['sub_unit_id'] = $request->input('sub_unit_id');
            }

            $transaction = Transaction::where('business_id', $business_id)->where('type', 'production_purchase')->findOrFail($id);
            # Finalized production should not be editable
            if ($transaction->mfg_is_final == 1) {
                
                DB::beginTransaction();
                    try{
                        $tran = Transaction::find($id);
                        
                        if(!empty($tran)){
                            $store_id_old    = $tran->store_in;
                            $purchase        = \App\PurchaseLine::where("transaction_id",$tran->id)->get();
                            foreach($purchase as $it){
                                $purchase_new    =  $purchase_line_data["quantity"];
                                $old_quntity     = $it->quantity;
                                if($it->quantity != $purchase_new){
                                    $it->quantity  = $purchase_new;
                                    $it->update();
                                }
                            }
                            $transaction->update($transaction_data);
                            
                        }
                        $transaction_sell_data = [
                            'mfg_parent_production_purchase_id'=>$transaction->id,
                            'transaction_date' => $transaction->transaction_date, 
                            'status' => $is_final ? 'final' : 'draft',
                            'payment_status' => 'due',
                            'final_total' => $transaction->final_total
                        ];
                         # Create Sell Transfer transaction
                         $production_sell = Transaction::where('business_id', $business_id)
                                                    ->where('type', 'production_sell')
                                                    ->with('sell_lines', 'sell_lines.product', 'sell_lines.variations')
                                                    ->where('mfg_parent_production_purchase_id', $transaction->id)
                                                    ->first();

                        $production_sell->update($transaction_sell_data);
                        $sell_lines = [];
                        $ingredient_quantities = $request->input('ingredients');

                        foreach ($production_sell->sell_lines as $key=>$sell_line) {
                            $variation = $sell_line->variations;
                            $line_key = array_keys($ingredient_quantities)[$key];
                            $line_sub_unit_id = !empty($ingredient_quantities[$line_key]['sub_unit_id']) ?
                                            $ingredient_quantities[$line_key]['sub_unit_id'] : null;
                            $line_multiplier = 1;
                            if (!empty($line_sub_unit_id)) {
                                $sub_units = $this->productUtil->getSubUnits($business_id, $sell_line->product->unit_id);
                                $line_multiplier = !empty($sub_units[$line_sub_unit_id]['multiplier']) ? $sub_units[$line_sub_unit_id]['multiplier'] : 1;
                            }

                            $mfg_waste_percent = !empty($ingredient_quantities[$line_key]['mfg_waste_percent']) ? $this->productUtil->num_uf($ingredient_quantities[$line_key]['mfg_waste_percent']) : 0;
                            $cost         = \App\Product::product_cost($variation->product_id);
            
                            $sell_lines[] = [
                                'product_id'           => $variation->product_id,
                                'variation_id'         => $variation->id,
                                'quantity'             => $this->productUtil->num_uf($ingredient_quantities[$line_key]['quantity']),
                                'item_tax'             => 0,
                                'tax_id'               => null,
                                'unit_price'           => $cost * $line_multiplier,
                                'unit_price_inc_tax'   => $cost * $line_multiplier,
                                'enable_stock'         => $sell_line->product->enable_stock,
                                'product_unit_id'      => $variation->product->unit_id,
                                'sub_unit_id'          => $line_sub_unit_id,
                                'base_unit_multiplier' => $line_multiplier,
                                'mfg_waste_percent'    => $mfg_waste_percent
                            ];
                            

                            if ($transaction_sell_data['status'] == 'final') {
                                    $margin     =  $sell_line->quantity - $this->productUtil->num_uf($ingredient_quantities[$line_key]['quantity']);
                                    if($margin > 0){
                                        \App\Models\WarehouseInfo::update_stoct($sell_line->product_id,$request->store_id,($this->productUtil->num_uf($ingredient_quantities[$line_key]['quantity'])*-1),$business_id);
                                    }elseif($margin < 0){
                                        \App\Models\WarehouseInfo::update_stoct($sell_line->product_id,$request->store_id,($margin),$business_id);
                                    } 
                                    \App\MovementWarehouse::movemnet_warehouse($production_sell,$sell_line->product,$sell_line->quantity,$request->store_id,$sell_line,$type="decrease");
                            }
                            \App\TransactionSellLine::where('id',$sell_line->id)->update([
                                'quantity'           => $ingredient_quantities[$line_key]['quantity'],
                                'mfg_waste_percent'  => $mfg_waste_percent,
                                'unit_price'         => $cost * $line_multiplier,
                                'unit_price_inc_tax' => $cost * $line_multiplier,
                            ]);
                        }

                        $final_before = str_replace(',','',$request->input("final_before"));
                        $price        = (str_replace(',','',$request->final_total)/$request->quantity);
                         
                        if( $is_final == 1){
                            $type_cost   = null;
                            if($request->input('production_cost')){ $type_cost = str_replace(',','',$request->input('production_cost'));}
                            $old_account = \App\AccountTransaction::where("transaction_id",$production_sell->id)->get();
                            if(count($old_account)>0){
                                $old = $old_account;
                            } else{
                                $old = null;
                            }
                            \App\AccountTransaction::production_entry($production_sell,$price,$final_before,$type_cost,$old);
                            $tr = \App\Transaction::where("mfg_parent_production_purchase_id",$transaction->id)->first();
                            if(!empty($tr)){
                                $COSTs = ( $type_cost != NULL )?$type_cost:0;
                                \App\Models\ItemMove::production($tr->id,$transaction->id,$COSTs);
                            }
                            $output = [
                                    'success' => 1,
                                    'msg'     => __('messages.updated_successfull')
                            ];
                        }   

                        if ($transaction_sell_data['status'] == 'final'){
                            $variation      =  Variation::find($request->variation_id);
                            $trans          =  \App\Transaction::find($transaction_sell_data['mfg_parent_production_purchase_id']);
                            $purchase_line  =  \App\PurchaseLine::where('transaction_id',$trans->id)->where('product_id',$variation->product_id)->first();
                            $margin         =  $old_quntity - $request->quantity;
                            if($margin > 0){
                                if($store_id_old != $request->store_in){
                                    \App\Models\WarehouseInfo::update_stoct($variation->product_id,$store_id_old,($old_quntity*-1),$business_id);
                                    \App\Models\WarehouseInfo::update_stoct($variation->product_id,$request->store_in,($request->quantity),$business_id);
                                }else{
                                    \App\Models\WarehouseInfo::update_stoct($variation->product_id,$request->store_in,($request->quantity*-1),$business_id);
                                }
                            }elseif($margin < 0){
                                if($store_id_old != $request->store_in){
                                    \App\Models\WarehouseInfo::update_stoct($variation->product_id,$store_id_old,($old_quntity*-1),$business_id);
                                    \App\Models\WarehouseInfo::update_stoct($variation->product_id,$request->store_in,$request->quantity,$business_id);
                                }else{
                                    \App\Models\WarehouseInfo::update_stoct($variation->product_id,$request->store_in,$margin*-1,$business_id);
                                }
                            }else{
                                if($store_id_old != $request->store_in){
                                    \App\Models\WarehouseInfo::update_stoct($variation->product_id,$store_id_old,($old_quntity*-1),$business_id);
                                    \App\Models\WarehouseInfo::update_stoct($variation->product_id,$request->store_in,($old_quntity),$business_id);
                                }
                            } 
                           
                            $price  =  (str_replace(',','',$request->final_total)/$request->quantity);
                            \App\MovementWarehouse::production($production_sell,$variation->product,$request->quantity,$request->store_in,$price);
            
                            // $type_cost = null;
                            // if($request->input('production_cost')){ $type_cost = $request->input('production_cost');}
                            // \App\AccountTransaction::production_entry($production_sell,$price,$request->input("final_before"),$type_cost);
                            // if( !empty($production_sell)  && !empty($transaction)){
                            //     //...... itemMove .
                            //     \App\Models\ItemMove::production($production_sell->id,$transaction->id);
                            // }
                           
                        }

                        $output = [
                            'success' => 1,
                            'msg'     => __('messages.successfull')
                        ];
                    }catch(Exception $e){
                        $output = [
                            'success' => 0,
                            'msg'     => __('messages.something_went_wrong')
                        ];
                    }
                DB::commit();
                return redirect()->action('\Modules\Manufacturing\Http\Controllers\ProductionController@index')->with('status', $output);
            } 
                
            DB::beginTransaction();

            $transaction->update($transaction_data);

            $currency_details     = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            $update_product_price = !empty($manufacturing_settings['enable_updating_product_price']) && $is_final ? true : false;

            $this->productUtil->createOrUpdatePurchaseLines($transaction, [$purchase_line_data], $currency_details, $update_product_price);

            # Adjust stock over selling if found
            $this->productUtil->adjustStockOverSelling($transaction);

            $transaction_sell_data = [
                'transaction_date' => $transaction->transaction_date,
                'status'           => $is_final ? 'final' : 'draft',
                'payment_status'   => 'due',
                'final_total'      => $transaction->final_total
            ];

            # Create Sell Transfer transaction
            $production_sell = Transaction::where('business_id', $business_id)
                                    ->where('type', 'production_sell')
                                    ->with('sell_lines', 'sell_lines.product', 'sell_lines.variations')
                                    ->where('mfg_parent_production_purchase_id', $transaction->id)
                                    ->first();

            $production_sell->update($transaction_sell_data);

            $sell_lines            = [];
            $ingredient_quantities = $request->input('ingredients');

            foreach ($production_sell->sell_lines as $key=>$sell_line) {
                $variation        = $sell_line->variations;
                $line_key         = array_keys($ingredient_quantities)[$key];
                $line_sub_unit_id = !empty($ingredient_quantities[$line_key]['sub_unit_id']) ?
                                $ingredient_quantities[$line_key]['sub_unit_id'] : null;
                $line_multiplier  = 1;
                if (!empty($line_sub_unit_id)) {
                    $sub_units    = $this->productUtil->getSubUnits($business_id, $sell_line->product->unit_id);
                    $line_multiplier = !empty($sub_units[$line_sub_unit_id]['multiplier']) ? $sub_units[$line_sub_unit_id]['multiplier'] : 1;
                }

                $mfg_waste_percent = !empty($ingredient_quantities[$line_key]['mfg_waste_percent']) ? $this->productUtil->num_uf($ingredient_quantities[$line_key]['mfg_waste_percent']) : 0;
                $cost         = \App\Product::product_cost($variation->product_id);
 
                $sell_lines[] = [
                    'product_id'           => $variation->product_id,
                    'variation_id'         => $variation->id,
                    'quantity'             => $this->productUtil->num_uf($ingredient_quantities[$line_key]['quantity']),
                    'item_tax'             => 0,
                    'tax_id'               => null,
                    'unit_price'           => $cost * $line_multiplier,
                    'unit_price_inc_tax'   => $cost * $line_multiplier,
                    'enable_stock'         => $sell_line->product->enable_stock,
                    'product_unit_id'      => $variation->product->unit_id,
                    'sub_unit_id'          => $line_sub_unit_id,
                    'base_unit_multiplier' => $line_multiplier,
                    'mfg_waste_percent'    => $mfg_waste_percent
                ];
                \App\TransactionSellLine::where('id',$sell_line->id)->update([
                    'quantity'             => $ingredient_quantities[$line_key]['quantity'],
                    'mfg_waste_percent'    => $mfg_waste_percent,
                    'unit_price'           => $cost * $line_multiplier,
                    'unit_price_inc_tax'   => $cost * $line_multiplier,
                 ]);
            }

            if ($transaction_sell_data['status'] == 'final') {
                foreach ($sell_lines as $sell_line) {
                    if ($sell_line['enable_stock']) {
                        $line_qty = $sell_line['quantity'] * $sell_line['base_unit_multiplier'];
                        $this->productUtil->decreaseProductQuantity(
                            $sell_line['product_id'],
                            $sell_line['variation_id'],
                            $production_sell->location_id,
                            $line_qty
                        );
                    }
                }

                # Map sell lines with purchase lines
                $business = [
                    'id'                => $business_id,
                    'accounting_method' => $request->session()->get('business.accounting_method'),
                    'location_id'       => $production_sell->location_id
                ];
                # $this->transactionUtil->mapPurchaseSell($business, $production_sell->sell_lines, 'production_purchase');
            }

            if ($transaction_sell_data['status'] == 'final') {
                foreach ($request->ingredients as $ing) {
                    $variation  = Variation::where('id', $ing['variation_id'])->with(['product'])->first() ;
                    $quantity   = $ing['quantity'];
                    $store_id   = $request->store_id; 
                    $sell_line  = \App\TransactionSellLine::where('transaction_id',$production_sell->id)->where('product_id',$variation->product_id)->first();
                    \App\Models\WarehouseInfo::update_stoct($sell_line->product_id,$request->store_id,($quantity*-1),$business_id);
                    \App\MovementWarehouse::movemnet_warehouse($production_sell,$sell_line->product,$quantity,$request->store_id,$sell_line,$type="decrease");
                }
            }

            if ($transaction_sell_data['status'] == 'final'){
                $variation  =  Variation::find($request->variation_id);
                \App\Models\WarehouseInfo::update_stoct($variation->product_id,$request->store_in,$request->quantity,$business_id);
                $price      =  (str_replace(',','',$request->final_total)/$request->quantity);
                \App\MovementWarehouse::production($production_sell,$variation->product,$request->quantity, $request->store_in,$price);
                $type_cost = null;
                if($request->input('production_cost')){ $type_cost = $request->input('production_cost');}
                \App\AccountTransaction::production_entry($production_sell,$price,$request->input("final_before"),$type_cost);
                if( !empty($production_sell)  && !empty($transaction)){
                    $COSTs = ($type_cost!=NULL)?$type_cost:0;
                    \App\Models\ItemMove::production($production_sell->id,$transaction->id,$COSTs);
                }
                

            }
            DB::commit();
            
            $output = [
                    'success' => 1,
                    'msg'     => __('lang_v1.updated_success')
            ];
        } catch (Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = [
                    'success' => 0,
                    'msg'     => __('messages.something_went_wrong')
            ];
        }

        return redirect()->action('\Modules\Manufacturing\Http\Controllers\ProductionController@index')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         
        $business_id = request()->session()->get('user.business_id');
        if (!(auth()->user()->can('superadmin') || $this->moduleUtil->hasThePermissionInSubscription($business_id, 'manufacturing_module')) || !auth()->user()->can('manufacturing.access_production')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            DB::beginTransaction();
            try {
                //1//....... bill 
                $sell_tr      = \App\Transaction::find($id);
                $purchase_tr  = \App\Transaction::where("mfg_parent_production_purchase_id",$id)->first();
                
                //2//....... item
                $ids_pr      =  \App\PurchaseLine::where("transaction_id",$id)->get();
                $ids_sl      =  \App\TransactionSellLine::where("transaction_id",$purchase_tr->id)->get();

                //3//...... entries
                $account_transaction = \App\AccountTransaction::where("transaction_id",$purchase_tr->id)->get();
                $entry_s    = \App\Models\Entry::where("account_transaction",$sell_tr->id)->get();
                // $entry_p    = \App\Models\Entry::where("account_transaction",$purchase_tr->id)->get();

                //4//...... map
                $map  = \App\Models\StatusLive::where("transaction_id",$sell_tr->id)->get();


                
                //5//....... item movement
                $itemMove_sel = \App\Models\ItemMove::where("transaction_id",$sell_tr->id)->get();
                $itemMove_pur = \App\Models\ItemMove::where("transaction_id",$purchase_tr->id)->get();
                
                //6// ..... warehouse movement 
                $warehouseMovement = \App\MovementWarehouse::where("transaction_id",$purchase_tr->id)->get();
                
                
                // .....  return purchase  -> minus --
                foreach($ids_pr as $it){
                    //... quantity
                    $qty     =  $it->quantity;
                    $product =  $it->product_id;
                    $store   =  $it->transaction->store_in;
                    $sum     =  \App\Models\WarehouseInfo::store_qty($business_id,$store,$product);
                    \App\Models\WarehouseInfo::store_decrement($business_id,$store,$product,$qty);
                    $it->delete();               
                }

                //.....  return sell   ->  plus ++
                foreach($ids_sl as $it){
                    //... quantity
                    $qty     =  $it->quantity;
                    $product =  $it->product_id;
                    $store   =  $it->store_id;
                    $sum     =  \App\Models\WarehouseInfo::store_qty($business_id,$store,$product);
                    \App\Models\WarehouseInfo::store_increment($business_id,$store,$product,$qty);
                    $it->delete();
                }

                //.....  entries 
                foreach($account_transaction as $it){
                    $account_tr  = $it->account_id;
                    $action_date = $it->operation_date;
                    //... quantity
                    $it->delete();
                    $account     = \App\Account::find($account_tr); 
                    if($account->cost_center!=1){ 
                        \App\AccountTransaction::nextRecords($account->id,$account->business_id,$action_date);
                    }
                }

                //.....  warehouse move
                foreach($warehouseMovement as $it){
                    //... move
                    $it->delete();
                }
                
                //.....  entries sale
                foreach($entry_s as $it){
                    //... quantity
                    $it->delete();
                } 

                //.....  map sale
                foreach($map as $it){
                    //... quantity
                    $it->delete();
                }

                //.....  itemMove  sale
                foreach($itemMove_sel as $it){
                    //... quantity
                    $it->delete();
                }

                //.....  itemMove  purchase
                foreach($itemMove_pur as $it){
                    //... quantity
                    $it->delete();
                }

                $sell_tr->delete();
                $purchase_tr->delete();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.deleted_success')
                ];
                DB::commit();
                return  $output;
                
                
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
    
                $output['success'] = false;
                // $output['msg']     =  __("messages.something_went_wrong");
                $output['msg']     =  "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage();
                return  $output ;
            }
        }
    }

    /**
     * Retrives data for manufacturing report.
     * @return Response
     */
    public function getManufacturingReport()
    {
        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            $location_id = request()->get('location_id');

            $production_totals = $this->mfgUtil->getProductionTotals($business_id, $location_id, $start_date, $end_date);

            $total_sold = $this->mfgUtil->getTotalSold($business_id, $location_id, $start_date, $end_date);
            

            $output['total_production'] = $production_totals['total_production'];
            $output['total_production_cost'] = $production_totals['total_production_cost'];
            $output['total_sold'] = $total_sold;

            return $output;
        }

        $business_locations = BusinessLocation::forDropdown($business_id, true);
        return view('manufacturing::production.report')->with(compact('business_locations'));
    }

    public function entry($id)
    {
        $transaction  = Transaction::where("mfg_parent_production_purchase_id",$id)->first();
        $allData   =  \App\AccountTransaction::where('transaction_id',$transaction->id)
                                                   ->where("amount",">",0)
                                                   ->orderBy("entry_id")
                                                   ->get();
                                                
        $data    =  \App\Transaction::find($id);
        $entry   =  \App\Models\Entry::get(); 
        return view('manufacturing::production.entry')
               ->with('allData',$allData)
               ->with('entry',$entry)
               ->with('data',$data);
    }
}
