<?php

namespace App\Http\Controllers;

use App\BusinessLocation;

use Carbon\Carbon;
use App\PurchaseLine;
use App\Transaction;
use App\TransactionSellLinesPurchaseLines;
use App\Utils\ModuleUtil;
use App\Models\Warehouse;
use App\Models\WarehouseInfo;
use App\MovementWarehouse;

use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Datatables;

use DB;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class StockTransferController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
        $this->status_colors = [
            'in_transit' => 'bg-yellow',
            'completed' => 'bg-green',
            'pending' => 'bg-red',
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('purchase.create') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('admin_without.views')) {
            abort(403, 'Unauthorized action.');
        }

        $statuses = $this->stockTransferStatuses();

        $business_id = request()->session()->get('user.business_id');
        $edit_days = request()->session()->get('business.transaction_edit_days');
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $statuses = $this->stockTransferStatuses();
        $business_id = request()->session()->get('user.business_id');

        $allData =  Transaction::OrderBy('id','desc')->where('business_id',$business_id)->where('type','Stock_In')
                        ->get();

            if (request()->ajax()) {
                 
                return Datatables::of($allData)
                    ->addColumn('action', function ($row) use ($edit_days) {
                        $html = '<button type="button" title="' . __("stock_adjustment.view_details") . '" class="btn btn-primary btn-xs btn-modal" data-container=".views_modal" data-href="' . action('StockTransferController@show', [$row->id]) . '"><i class="fa fa-eye" aria-hidden="true"></i> ' . __('messages.view') . '</button>';
    
                        $html .= ' <a href="#" class="print-invoice btn btn-info btn-xs" data-href="' . action('StockTransferController@printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i> '. __("messages.print") .'</a>';
    
                        $date = \Carbon::parse($row->transaction_date)
                            ->addDays($edit_days);
                        $today = today();
    
                         if(request()->session()->get("user.id") == 1){
                            $html .= '&nbsp;
                            <button type="button" data-href="' . action("StockTransferController@destroy", [$row->id]) . '" class="btn btn-danger btn-xs delete_stock_transfer"><i class="fa fa-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</button>';
                        }
                         
    
                        if ($row->status != 'final') {
                            $html .= '&nbsp;
                            <a href="' . action("StockTransferController@edit", [$row->id]) . '" class="btn btn-primary btn-xs"><i class="fa fa-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a>';
                        }
    
                        return $html;
                    })->editColumn('location_from',
                        function ($row){
                            $warehouse = Warehouse::find($row->store);
                            return  $warehouse->name ;
                        }
                    )->editColumn('location_to',
                        function($row){
                            $warehouse = Warehouse::find($row->transfer_parent_id);
                            if($warehouse){
                                $data = $warehouse->name;
                            
                            }else{
                                $data = "defult";
                            }
                            
                            return   $data;

                        })
                     ->editColumn(
                        'final_total',
                        '<span class="display_currency" data-currency_symbol="true">{{$final_total}}</span>'
                    )
                    ->editColumn(
                        'shipping_charges',
                        '<span class="display_currency" data-currency_symbol="true">{{$shipping_charges}}</span>'
                    )
                    ->editColumn('status', function($row) use($statuses) {
                        $row->status = $row->status == 'final' ? 'completed' : $row->status;
                        $status =  $statuses[$row->status];

                        $status_color = !empty($this->status_colors[$row->status]) ? $this->status_colors[$row->status] : 'bg-gray';
                        $status = $row->status != 'completed' ? '<a href="#" class="stock_transfer_status" data-status="' . $row->status . '" data-href="' . action("StockTransferController@updateStatus", [$row->id]) . '"><span class="label ' . $status_color .'">' . $statuses[$row->status] . '</span></a>' : '<span class="label ' . $status_color .'">' . $statuses[$row->status] . '</span>';
                            
                        return $status;
                    })
                    ->editColumn('final_total', function($row)  {
                        
                        return $row->purchase_lines->sum('quantity');
                    })
                    ->editColumn('transaction_date', '{{@format_datetime($transaction_date)}}')
                    ->rawColumns(['final_total', 'action', 'shipping_charges', 'status'])
                    ->setRowAttr([
                    'data-href' => function ($row) {
                        return  action('StockTransferController@show', [$row->id]);
                    }])
                    ->make(true);
            }
    
        return view('stock_transfer.index')->with(compact(['statuses','currency_details','allData']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (!auth()->user()->can('purchase.create') &&   !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('admin_without.views')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
		$mainstore = Warehouse::where('business_id', $business_id)->select()->get();
 
        $mainstore_categories = [];
        if (!empty($mainstore)) {
            foreach ($mainstore as $mainstor) {
                if($mainstor->status == 1){
                    $mainstore_categories[$mainstor->id] = $mainstor->name;
                }
            }
        }
        //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse(action('StockTransferController@index'));
            } 
        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        $statuses = $this->stockTransferStatuses();

        return view('stock_transfer.create')
                ->with(compact('business_locations','mainstore_categories', 'statuses'));
    }

    private function stockTransferStatuses()
    {
        return [
            'pending' => __('lang_v1.pending'),
            'in_transit' => __('lang_v1.in_transit'),
            'completed' => __('restaurant.completed')
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
        
        if (!auth()->user()->can('purchase.create')&& !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('admin_without.views')) {
            abort(403, 'Unauthorized action.');
        }
        

        try {
 
            $business_id = $request->session()->get('user.business_id');
            $trans = BusinessLocation::where("business_id",$business_id)->first();
            
            //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse(action('StockTransferController@index'));
            } 
        }
 
            DB::beginTransaction();
            
            $input_data = $request->only([ 'location_id', 'ref_no', 'transaction_date', 'additional_notes', 'shipping_charges', 'final_total']);
            $status  = $request->input('status');
            $user_id = $request->session()->get('user.id');

            $input_data['final_total']      = $this->productUtil->num_uf($input_data['final_total']);
            $input_data['total_before_tax'] = $input_data['final_total'];

            $input_data['type']             = 'Stock_Out';
            $input_data['business_id']      = $business_id;
            $input_data['created_by']       = $user_id;
            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['shipping_charges'] = 0;
            $input_data['payment_status']   = 'paid';
            $input_data['status']           = $status == 'completed' ? 'final' : $status;
            $input_data['store']            = $request->location_id; 
            $input_data["location_id"]      = $trans->id;
            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('stock_transfer');
            //Generate reference number
            if (empty($input_data['ref_no'])) {
                $input_data['ref_no'] = $this->productUtil->generateReferenceNumber('stock_transfer', $ref_count);
            }

            $products = $request->input('products');
          
            $sell_lines = [];
            $purchase_lines = [];

            if (!empty($products)) {
                foreach ($products as $product) {
                    $sell_line_arr = [
                                'product_id'   => $product['product_id'],
                                'variation_id' => $product['variation_id'],
                                'quantity'     => $this->productUtil->num_uf($product['quantity']),
                                'store_id'     => $request->transfer_location_id,
                                'item_tax'     => 0,
                                'tax_id'       => null];

                    $purchase_line_arr = $sell_line_arr;
                    $pr =  \App\Variation::find($product['variation_id']);
                   // $sell_line_arr['unit_price'] = $this->productUtil->num_uf($product['unit_price']);
                    $sell_line_arr['unit_price']         = isset($pr->default_purchase_price)?$pr->default_purchase_price:1 ;
                    $sell_line_arr['unit_price_inc_tax'] = isset($pr->dpp_inc_tax)?$pr->dpp_inc_tax:1 ;

                    $purchase_line_arr['purchase_price'] = $sell_line_arr['unit_price'];
                    $purchase_line_arr['purchase_price_inc_tax'] = $sell_line_arr['unit_price'];

                    if (!empty($product['lot_no_line_id'])) {
                        //Add lot_no_line_id to sell line
                        $sell_line_arr['lot_no_line_id'] = $product['lot_no_line_id'];

                        //Copy lot number and expiry date to purchase line
                        $lot_details                     = PurchaseLine::find($product['lot_no_line_id']);
                        $purchase_line_arr['lot_number'] = $lot_details->lot_number;
                        $purchase_line_arr['mfg_date']   = $lot_details->mfg_date;
                        $purchase_line_arr['exp_date']   = $lot_details->exp_date;
                    }

                    $sell_lines[] = $sell_line_arr;
                    $purchase_lines[] = $purchase_line_arr;
                }
            }

            
             
            //Create Sell Transfer transaction
            $sell_transfer = Transaction::create($input_data);

            //Create Purchase Transfer at transfer location
            $input_data['type'] = 'Stock_In';
            $input_data['location_id'] =  $trans->id;
            $input_data['transfer_parent_id'] = $request->transfer_location_id;
            $input_data['status'] = $status == 'completed' ? 'completed' : $status;

            $purchase_transfer = Transaction::create($input_data);
            //Sell Product from first location
            if (!empty($sell_lines)) {
                $this->transactionUtil->createOrUpdateSellLines($sell_transfer, $sell_lines, $input_data['location_id'],null,null,[],null,null,null,$request);
            }
            


            //Purchase product in second location
            if (!empty($purchase_lines)) {
                  $purchase_transfer->purchase_lines()->createMany($purchase_lines);
            }
            
            //Decrease product stock from sell location
            //And increase product stock at purchase location
            if ($status == 'completed') {
                foreach ($products as $product) {
                    if ($product['enable_stock']) {
                        
                        //**.............. EB
                       
                        WarehouseInfo::transferfromTo($product["product_id"],$request->location_id,$request->transfer_location_id,$product["quantity"],$business_id);
                       
                        $this->productUtil->decreaseProductQuantity(
                            $product['product_id'],
                            $product['variation_id'],
                            $trans->id,
                            $product['quantity'] 
                        );

                        $this->productUtil->updateProductQuantity(
                            $trans->id,
                            $product['product_id'],
                            $product['variation_id'],
                            $product['quantity']
                        );
                        MovementWarehouse::store_moves($request->location_id,$request->transfer_location_id,$product["product_id"],$product["quantity"],$sell_transfer,$purchase_transfer);

                    }
                }
                \App\Models\ItemMove::transfer($purchase_transfer,$sell_transfer,0,0);

                //Adjust stock over selling if found
                $this->productUtil->adjustStockOverSelling($purchase_transfer);

                //Map sell lines with purchase lines
                $business = ['id' => $business_id,
                            'accounting_method' => $request->session()->get('business.accounting_method'),
                            'location_id' => $sell_transfer->location_id
                        ];
                // $this->transactionUtil->mapPurchaseSell($business, $sell_transfer->sell_lines, 'purchase');
            }

            $this->transactionUtil->activityLog($sell_transfer, 'added');

            $output = ['success' => 1,
                            'msg' => __('lang_v1.stock_transfer_added_successfully')
                        ];
           DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => $e->getMessage()
                        ];
        }

        return redirect('stock-transfers')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('purchase.view') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('admin_without.views')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
            
        $sell_transfer = Transaction::where('business_id', $business_id)
                            ->where('id', $id-1)
                            ->where('type', 'Stock_Out')
                            ->with(
                                'contact',
                                'sell_lines',
                                'sell_lines.product',
                                'sell_lines.variations',
                                'sell_lines.variations.product_variation',
                                'sell_lines.lot_details',
                                'location',
                                'warehouse',
                                'sell_lines.product.unit'
                            )
                            ->first();
                           

        $purchase_transfer = Transaction::where('business_id', $business_id)
                    ->where('id', $id)
                    ->where('type', 'Stock_In')
                    ->with('location',"warehouse_to")
                    ->first();
     
        $location_details = ['sell' => $sell_transfer, 'purchase' => $purchase_transfer];

        $lot_n_exp_enabled = false;
        if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
            $lot_n_exp_enabled = true;
        }

        $statuses = $this->stockTransferStatuses();

        $statuses['final'] = __('restaurant.completed');
        
        $activities = Activity::forSubject($sell_transfer)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();
        return view('stock_transfer.show')
                ->with(compact('sell_transfer','purchase_transfer', 'location_details', 'lot_n_exp_enabled', 'statuses', 'activities'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        if (!auth()->user()->can('purchase.delete') && !auth()->user()->can('warehouse.Delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            if (request()->ajax()) {
                $edit_days = request()->session()->get('business.transaction_edit_days');
                if (!$this->transactionUtil->canBeEdited($id, $edit_days)) {
                    return ['success' => 0,
                        'msg' => __('messages.transaction_edit_not_allowed', ['days' => $edit_days])];
                }

                //Get purchase transfer transaction
                $purchase_transfer = Transaction::where('id', $id)
                                                    ->where('type', 'Stock_In')
                                                    ->with(['purchase_lines'])
                                                    ->first();

                //Get sell transfer transaction
                $sell_transfer     = Transaction::where('ref_no', $purchase_transfer->ref_no)
                                                    ->where('type', 'Stock_Out')
                                                    ->with(['sell_lines'])
                                                    ->first();

                $move_purchase  = \App\MovementWarehouse::where("business_id",$purchase_transfer->business_id)->where("transaction_id",$purchase_transfer->id)->get();
                $move_sell      = \App\MovementWarehouse::where("business_id",$purchase_transfer->business_id)->where("transaction_id",$sell_transfer->id)->get();
                foreach($move_purchase as $it){
                     $it->delete();
                }
                foreach($move_sell as $it){
                     $it->delete();
                }
                

                \App\Models\ItemMove::delete_transafer($sell_transfer,$purchase_transfer);


                $ids_pur = $purchase_transfer->purchase_lines->pluck("id"); 
                $ids_sel = $sell_transfer->sell_lines->pluck("id"); 

                foreach($ids_pur as $it){
                    $purchase = \App\PurchaseLine::find($it);
                    if(!empty($purchase)){
                         //.... decrement from purchase store 
                         \App\Models\WarehouseInfo::update_stoct($purchase->product_id,$purchase->store_id,($purchase->quantity*-1),$purchase_transfer->business_id);
                    }
                }
                foreach($ids_sel as $it){
                    $sell = \App\TransactionSellLine::find($it);
                    if(!empty($sell)){
                        //.... increment from Sale store 
                        \App\Models\WarehouseInfo::update_stoct($sell->product_id,$sell->store_id,($sell->quantity),$purchase_transfer->business_id);
                    }
                }


                //Check if any transfer stock is deleted and delete purchase lines
                $purchase_lines = $purchase_transfer->purchase_lines;
                foreach ($purchase_lines as $purchase_line) {
                    if ($purchase_line->quantity_sold > 0) {
                        return [ 'success' => 0,
                                        'msg' => __('lang_v1.stock_transfer_cannot_be_deleted')
                            ];
                    }
                }

                DB::beginTransaction();
                //Get purchase lines from transaction_sell_lines_purchase_lines and decrease quantity_sold
                $sell_lines = $sell_transfer->sell_lines;
                $deleted_sell_purchase_ids = [];
                $products = []; //variation_id as array

                foreach ($sell_lines as $sell_line) {
                    $purchase_sell_line = TransactionSellLinesPurchaseLines::where('sell_line_id', $sell_line->id)->first();
                    if (!empty($purchase_sell_line)) {
                        //Decrease quntity sold from purchase line
                        PurchaseLine::where('id', $purchase_sell_line->purchase_line_id)
                                ->decrement('quantity_sold', $sell_line->quantity);
                        $deleted_sell_purchase_ids[] = $purchase_sell_line->id;

                        //variation details
                        if (isset($products[$sell_line->variation_id])) {
                            $products[$sell_line->variation_id]['quantity'] += $sell_line->quantity;
                            $products[$sell_line->variation_id]['product_id'] = $sell_line->product_id;
                        } else {
                            $products[$sell_line->variation_id]['quantity'] = $sell_line->quantity;
                            $products[$sell_line->variation_id]['product_id'] = $sell_line->product_id;
                        }
                    }
                }

                //Update quantity available in both location
                if (!empty($products)) {
                    foreach ($products as $key => $value) {

                        //Decrease from location 2
                        $this->productUtil->decreaseProductQuantity(
                            $products[$key]['product_id'],
                            $key,
                            $purchase_transfer->location_id,
                            $products[$key]['quantity']
                        );

                      
                        //Increase in location 1
                        $this->productUtil->updateProductQuantity(
                            $sell_transfer->location_id,
                            $products[$key]['product_id'],
                            $key,
                            $products[$key]['quantity']
                        );

                     
                    }
                }

                //Delete sale line purchase line
                if (!empty($deleted_sell_purchase_ids)) {
                    TransactionSellLinesPurchaseLines::whereIn('id', $deleted_sell_purchase_ids)
                        ->delete();
                }

                //Delete both transactions
                $sell_transfer->delete();
                $purchase_transfer->delete();

                $output = ['success' => 1,
                        'msg' => __('lang_v1.stock_transfer_delete_success')
                    ];
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            // 'msg' => __('messages.something_went_wrong')
                            'msg' => $e->getMessage()
                        ];
        }
        return $output;
    }

    /**
     * Checks if ref_number and supplier combination already exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            
            $sell_transfer = Transaction::where('business_id', $business_id)
                                ->where('id', $id-1)
                                ->where('type', 'Stock_Out')
                                ->with(
                                    'contact',
                                    'sell_lines',
                                    'sell_lines.product',
                                    'sell_lines.variations',
                                    'sell_lines.variations.product_variation',
                                    'sell_lines.lot_details',
                                    'location',
                                    'warehouse',
                                    'sell_lines.product.unit'
                                )
                                ->first();

            $purchase_transfer = Transaction::where('business_id', $business_id)
                        ->where('id', $id)
                        ->where('type', 'Stock_In')
                        ->with("warehouse_to")
                        ->first();

            $location_details = ['sell' => $sell_transfer, 'purchase' => $purchase_transfer];

            $lot_n_exp_enabled = false;
            if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                $lot_n_exp_enabled = true;
            }


            $output = ['success' => 1, 'receipt' => []];
            $output['receipt']['html_content'] = view('stock_transfer.print', compact('sell_transfer', 'purchase_transfer' ,  'location_details', 'lot_n_exp_enabled'))->render();
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            // 'msg' => __('messages.something_went_wrong')
                            'msg' => $e->getMessage()
                        ];
        }

        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
      
        if (!auth()->user()->can('purchase.edit') && !auth()->user()->can('warehouse.Edit')&& !auth()->user()->can('manufuctoring.Edit') && !auth()->user()->can('admin_supervisor.Edit') && !auth()->user()->can('admin_without.Edit')) {
            abort(403, 'Unauthorized action.');
        }
        $s_id = $id - 1;
        $business_id = request()->session()->get('user.business_id');

        $business_locations = BusinessLocation::forDropdown($business_id);

        $statuses = $this->stockTransferStatuses();
        $WarehousInfo = WarehouseInfo::where("business_id",$business_id)->get();
        // ->where('status', '!=', 'final')
        $sell_transfer = Transaction::where('business_id', $business_id)
                ->where('type', 'Stock_Out')
                ->with(['sell_lines',"warehouse","warehouse_to"])
                ->findOrFail($s_id);
                // ->where('status', '!=', 'Completed')
        $counts = [];
        $purchase_transfer = Transaction::where('business_id', 
                $business_id)
                ->where('id', $id)
                ->where('type', 'Stock_In')
                ->with(["warehouse_to"])
                ->first();
        foreach($purchase_transfer->purchase_lines as  $it){
            $counts[]   = $it->id;
        }


        $quantity =  0;
        foreach($WarehousInfo as $Winfo){
            if($Winfo->store_id == $sell_transfer->warehouse->id  ){
                $quantity =  $Winfo->product_qty; 
            }
        }
        $products = [];
        foreach ($sell_transfer->sell_lines as $sell_line) {

            $product = $this->productUtil->getDetailsFromVariationQ($sell_line->variation_id, $business_id, $sell_transfer->location_id);
            $product->formatted_qty_available = $this->productUtil->num_f($product->qty_available);
            $product->quantity_ordered = $sell_line->quantity;
            $product->transaction_sell_lines_id = $sell_line->id;
            $product->lot_no_line_id = $sell_line->lot_no_line_id;
            
            
            //Get lot number dropdown if enabled
            $lot_numbers = [];
            if (request()->session()->get('business.enable_lot_number') == 1 || request()->session()->get('business.enable_product_expiry') == 1) {
                $lot_number_obj = $this->transactionUtil->getLotNumbersFromVariation($sell_line->variation_id, $business_id, $sell_transfer->location_id, true);
                foreach ($lot_number_obj as $lot_number) {
                    $lot_number->qty_formated = $this->productUtil->num_f($lot_number->qty_available);
                    $lot_numbers[] = $lot_number;
                }
            }
            $product->lot_numbers = $lot_numbers;

            $products[] = $product;
            
        }
        // dd("stop1");
        
        $mainstore = Warehouse::where('business_id', $business_id)->select()->get();
        $mainstore_categories = [];
        if (!empty($mainstore)) {
            foreach ($mainstore as $mainstor) {
                if($mainstor->status == 1){
                    $mainstore_categories[$mainstor->id] = $mainstor->name;
                }
            }
        }
        return view('stock_transfer.edit')
                ->with(compact('sell_transfer' ,'counts', 'quantity' , 'purchase_transfer', 'business_locations','mainstore_categories', 'statuses', 'products'));
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
        if (!auth()->user()->can('purchase.create') && !auth()->user()->can('warehouse.Edit')&& !auth()->user()->can('manufuctoring.Edit') && !auth()->user()->can('admin_supervisor.Edit') && !auth()->user()->can('admin_without.Edit')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = $request->session()->get('user.business_id');

            //Check if subscribed or not
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            if(!$this->moduleUtil->isSubscribedPermitted($business_id)){
                return $this->moduleUtil->expiredResponse(action('StockTransferController@index'));
            } 
        }


            $business_id   = request()->session()->get('user.business_id');
            $location_id   = \App\BusinessLocation::where('business_id',$business_id)->first()->id;
            $sell_transfer = Transaction::where('business_id', $business_id)
                                                ->where('type', 'Stock_Out')
                                                ->findOrFail($id);

            $sell_transfer_before = $sell_transfer->replicate();
            $purchase_transfer = Transaction::where('business_id', 
                                                    $business_id)
                                                ->where('ref_no', $sell_transfer->ref_no)
                                                ->where('type', 'Stock_In')
                                                ->with(['purchase_lines'])
                                                ->first();

            $status = $request->input('status');

            DB::beginTransaction();
            
            $input_data = $request->only(['transaction_date', 'additional_notes', 'shipping_charges', 'final_total']);
            $status = ($request->input('status') == "")? "completed" : $request->input('status');

            $input_data['final_total']      = $this->productUtil->num_uf($input_data['final_total']);
            $input_data['total_before_tax'] = $input_data['final_total'];

            $input_data['transaction_date'] = $this->productUtil->uf_date($input_data['transaction_date'], true);
            $input_data['shipping_charges'] = NULL;
            $input_data['shipping_charges'] = NULL;
            $input_data['status'] = $status == 'completed' ? 'final' : $status;

            $products = $request->input('products');
            $sell_lines = [];
            $purchase_lines = [];
            $edited_purchase_lines = [];

     
            if (!empty($products)) {
                $ids = [] ;
                foreach($products as $it){
                    if(isset($it["transaction_sell_lines_id"])){
                        $ids[] = $it["transaction_sell_lines_id"];
                    }
                }
                $idp = [] ;
                foreach($products as $it){
                    if(isset($it["number"])){
                        $idp[] = $it["number"];
                    }
                }
                $tr_selline    = \App\TransactionSellLine::where("transaction_id",$id)->whereNotIn("id",$ids)->get();
                $ids_delete    = $tr_selline->pluck("id");
                $tr_purchase   = \App\PurchaseLine::where("transaction_id",$purchase_transfer->id)->whereNotIn("id",$idp)->get();
                $id_pur_delete = $tr_purchase->pluck("id");
              
                if(count($ids_delete)>0){
                    foreach($ids_delete as $key => $it){
                        $purchase = \App\PurchaseLine::find($id_pur_delete[$key]);
                        $line     = \App\TransactionSellLine::find($it);
                        WarehouseInfo::update_stoct($line->product_id,$purchase->store_id,$line->quantity*-1,$business_id);
                        WarehouseInfo::update_stoct($line->product_id,$line->store_id,$line->quantity,$business_id);

                        $delete_move_pur = \App\MovementWarehouse::where("purchase_line_id",$purchase->id)->first(); 
                        $delete_move_sal = \App\MovementWarehouse::where("transaction_sell_line_id",$line->id)->first();
                        
                        $delete_item_move_pur = \App\Models\ItemMove::where("purchase_line_id",$purchase->id)->first(); 
                        $delete_item_move_sal = \App\Models\ItemMove::where("sells_line_id",$line->id)->first();
                        
                        if(!empty($delete_item_move_pur)){
                            $delete_item_move_pur->delete();
                        }
                        if(!empty($delete_item_move_sal)){
                            $delete_item_move_sal->delete();
                        }
                        if(!empty($delete_move_pur)){
                            $delete_move_pur->delete();
                        }
                        if(!empty($delete_move_sal)){
                            $delete_move_sal->delete();
                        }
                     }
                }
                //Decrease product stock from sell location
                //And increase product stock at purchase location
                if ($status == 'completed' && $purchase_transfer->status != "completed") {  
                    foreach ($products as $product) {   
                        if ($product['enable_stock']) {
                            //**.............. EB
                            WarehouseInfo::transferfromTo($product["product_id"],$request->location_id,$request->transfer_location_id,$product["quantity"],$business_id);
                            $this->productUtil->decreaseProductQuantity(
                                $product['product_id'],
                                $product['variation_id'],
                                $location_id,
                                $product['quantity'] 
                            );
                            $this->productUtil->updateProductQuantity(
                                $location_id,
                                $product['product_id'],
                                $product['variation_id'],
                                $product['quantity']
                            );
                            MovementWarehouse::store_moves($request->location_id,$request->transfer_location_id,$product["product_id"],$product["quantity"],$sell_transfer,$purchase_transfer);
                        }
                    }
                
                }else if($status != 'completed' && $purchase_transfer->status == "completed") {
                    foreach ($products as $product) {
                        if ($product['enable_stock']) {
                            //**.............. EB
                            WarehouseInfo::transferfromTo($product["product_id"],$request->transfer_location_id,$request->location_id,$product["quantity"],$business_id);
                            $this->productUtil->updateProductQuantity(
                                $location_id,
                                $product['product_id'],
                                $product['variation_id'],
                                $product['quantity']
                            );
                            $this->productUtil->decreaseProductQuantity(
                                $product['product_id'],
                                $product['variation_id'],
                                $location_id,
                                $product['quantity'] 
                            );
                            MovementWarehouse::store_moves($request->transfer_location_id,$request->location_id,$product["product_id"],$product["quantity"],$sell_transfer,$purchase_transfer);
                        }
                    }
                }else if(($status == 'completed' && $purchase_transfer->status == "completed")){
                    foreach ($products as $product) {
                        if($request->transfer_location_id != $purchase_transfer->transfer_parent_id){
                            $purchase = \App\PurchaseLine::where("transaction_id",$purchase_transfer->id)->where("product_id",$product['product_id'])->first();
                            $margin   = $product["quantity"];
                            if($request->transfer_location_id != $sell_transfer->store){
                                //..... here 
                                if(!empty($purchase)){
                                    $margin      = $purchase->quantity - $product["quantity"];
                                    if($margin > 0 ){
                                        //... here 
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($purchase->quantity*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$request->transfer_location_id,($product["quantity"]),$business_id);
                                    }elseif($margin < 0 ){
                                        ///....here 
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($purchase->quantity*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);                                   
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$request->transfer_location_id,($product["quantity"]),$business_id);
                                    }else{
                                        // ..here 
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($purchase->quantity*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$request->transfer_location_id,($product["quantity"]),$business_id);
                                    } 
                                } 
                            }else{
                                //.. here 
                                if(!empty($purchase)){
                                    $margin      = $purchase->quantity - $product["quantity"];
                                    if($margin > 0 ){
                                        //.....here
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($purchase->quantity*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);                                   
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$request->transfer_location_id,($product["quantity"]),$business_id);
                                    }elseif($margin < 0 ){
                                        //.... here
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($purchase->quantity*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);                                   
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$request->transfer_location_id,($product["quantity"]),$business_id);
                                    }else{
                                        ///....here 
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($product["quantity"]*-1),$business_id);
                                        \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($product["quantity"]),$business_id);
                                    } 
                                } 
                            }
                        }else{
                            //.... here 
                            $purchase = \App\PurchaseLine::where("transaction_id",$purchase_transfer->id)->where("product_id",$product['product_id'])->first();
                             
                            $margin   = $product["quantity"];
                            if(!empty($purchase)){
                                $margin      = $purchase->quantity - $product["quantity"];
                                if($margin > 0 ){
                                    //..... here 
                                    \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($margin*-1),$business_id);
                                    \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);
                                    
                                }elseif($margin < 0 ){
                                    //.... here
                                    \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,($margin*-1),$business_id);                                   
                                    \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,($margin),$business_id);
                                }
                            }else{
                                $margin      = $product["quantity"];
                                //.... here
                                \App\Models\WarehouseInfo::update_stoct($product['product_id'],$purchase_transfer->transfer_parent_id,$margin,$business_id);                                   
                                \App\Models\WarehouseInfo::update_stoct($product['product_id'],$sell_transfer->store,$margin*-1,$business_id);
                            }
                                
                        }
                    }
                } 
                foreach ($products as $product) {
                    $sell_line_arr = [
                                'product_id' => $product['product_id'],
                                'variation_id' => $product['variation_id'],
                                'quantity' => $this->productUtil->num_uf($product['quantity']),
                                'item_tax' => 0,
                                'tax_id' => null];

                    $purchase_line_arr = $sell_line_arr;

                    $sell_line_arr['unit_price'] = (isset($product['unit_price']))?$this->productUtil->num_uf($product['unit_price']):0;
                    $sell_line_arr['unit_price_inc_tax'] = $sell_line_arr['unit_price'];

                    $purchase_line_arr['purchase_price'] = $sell_line_arr['unit_price'];
                    $purchase_line_arr['store_id'] = $request->transfer_location_id;
                    $purchase_line_arr['purchase_price_inc_tax'] = $sell_line_arr['unit_price'];
                    if (isset($product['transaction_sell_lines_id'])) {
                        $sell_line_arr['transaction_sell_lines_id'] = $product['transaction_sell_lines_id'];
                    }

                    if (!empty($product['lot_no_line_id'])) {
                        //Add lot_no_line_id to sell line
                        $sell_line_arr['lot_no_line_id'] = $product['lot_no_line_id'];

                        //Copy lot number and expiry date to purchase line
                        $lot_details = PurchaseLine::find($product['lot_no_line_id']);
                        $purchase_line_arr['lot_number'] = $lot_details->lot_number;
                        $purchase_line_arr['mfg_date'] = $lot_details->mfg_date;
                        $purchase_line_arr['exp_date'] = $lot_details->exp_date;
                    }

                    $sell_lines[] = $sell_line_arr;

                    $purchase_line = [];
                    //check if purchase_line for the variation exists else create new 
                    foreach ($purchase_transfer->purchase_lines as $pl) {
                        if ($pl->variation_id == $purchase_line_arr['variation_id']) {
                            $pl->update($purchase_line_arr);
                            $edited_purchase_lines[] = $pl->id;
                            $purchase_line = $pl;
                            break;
                        }
                    }
                    if (empty($purchase_line)) {
                        $purchase_line = new PurchaseLine($purchase_line_arr);
                    }

                    $purchase_lines[] = $purchase_line;
                }
            }

            //Create Sell Transfer transaction
            $sell_transfer->update($input_data);
            $sell_transfer->save();
             

            //Create Purchase Transfer at transfer location
            $input_data['status'] = $status == 'completed' ? 'completed' : $status;
            $input_data['transfer_parent_id'] = $request->transfer_location_id;


            $purchase_transfer->update($input_data);
            $purchase_transfer->save();

            //Sell Product from first location
            if (!empty($sell_lines)) {
                $this->transactionUtil->createOrUpdateSellLines($sell_transfer, $sell_lines, $sell_transfer->location_id, false, 'draft',[],null,null,null,$request);
            }
 
            //Purchase product in second location
            if (!empty($purchase_lines)) {
                if (!empty($edited_purchase_lines)) {
                    PurchaseLine::where('transaction_id', $purchase_transfer->id)
                    ->whereNotIn('id', $edited_purchase_lines)
                    ->delete();
                }
                $purchase_transfer->purchase_lines()->saveMany($purchase_lines);
            }

            \App\MovementWarehouse::update_move_transafer($sell_transfer->store,$purchase_transfer->transfer_parent_id,$sell_transfer,$purchase_transfer);

            \App\Models\ItemMove::transfer($purchase_transfer,$sell_transfer,0,0);

            $this->transactionUtil->activityLog($sell_transfer, 'edited', $sell_transfer_before);

            $output = ['success' => 1,
                            'msg' => __('lang_v1.updated_succesfully')
                        ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => __("messages.something_went_wrong")
                        ];
        }

        return redirect('stock-transfers')->with('status', $output);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        if (!auth()->user()->can('purchase.update') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views') && !auth()->user()->can('admin_supervisor.views') && !auth()->user()->can('admin_without.views')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $business_id = request()->session()->get('user.business_id');
            $location_id =  \App\BusinessLocation::where('business_id',$business_id)->first()->id;

            $purchase_transfer = Transaction::with(['purchase_lines'])
                                                ->find($id);
            $sell_transfer     = Transaction::where('business_id', $business_id)
                                            ->where('ref_no', $purchase_transfer->ref_no)
                                            ->where('type', 'Stock_Out')
                                            ->with(['sell_lines', 'sell_lines.product'])
                                            ->first();
                    


            $status = $request->input('status');

            DB::beginTransaction();
            if (($status == 'completed' && $sell_transfer->status != 'completed')  ) {

                foreach ($sell_transfer->sell_lines as $sell_line) {
                    if ($sell_line->product->enable_stock) {
                                    //**.............. EB
                                    WarehouseInfo::transferfromTo($sell_line->product->id,$sell_transfer->store,$purchase_transfer->transfer_parent_id,$sell_line->quantity,$business_id);
                                    $this->productUtil->decreaseProductQuantity(
                                        $sell_line->product_id,
                                        $sell_line->variation_id,
                                        $location_id ,
                                        $sell_line->quantity
                                    );
                                    $this->productUtil->updateProductQuantity(
                                        $location_id ,
                                        $sell_line->product_id,
                                        $sell_line->variation_id,
                                        $sell_line->quantity,
                                        0,
                                        null,
                                        false
                                    );
                                    MovementWarehouse::store_moves($sell_transfer->store,$purchase_transfer->transfer_parent_id,$sell_line->product->id,$sell_line->quantity,$sell_transfer,$purchase_transfer);

                                }
                }
                \App\Models\ItemMove::transfer($purchase_transfer,$sell_transfer,0,0);
                //Adjust stock over selling if found
                $this->productUtil->adjustStockOverSelling($purchase_transfer);

                //Map sell lines with purchase lines
                $business = ['id' => $business_id,
                            'accounting_method' => $request->session()->get('business.accounting_method'),
                            'location_id' => $sell_transfer->location_id
                        ];
                // $this->transactionUtil->mapPurchaseSell($business, $sell_transfer->sell_lines, 'purchase');
            }elseif(($status != 'completed' && $sell_transfer->status == 'completed') ){
                foreach ($sell_transfer->sell_lines as $sell_line) {
                    if ($sell_line->product->enable_stock) {
                            //**.............. EB
                            WarehouseInfo::transferfromTo($sell_line->product->id,$purchase_transfer->transfer_parent_id,$sell_transfer->store,$sell_line->quantity,$business_id);
                            $this->productUtil->updateProductQuantity(
                                $location_id ,
                                $sell_line->product_id,
                                $sell_line->variation_id,
                                $sell_line->quantity,
                                0,
                                null,
                                false
                            );
                            $this->productUtil->decreaseProductQuantity(
                                $sell_line->product_id,
                                $sell_line->variation_id,
                                $location_id ,
                                $sell_line->quantity
                            );
                            MovementWarehouse::store_moves($purchase_transfer->transfer_parent_id,$sell_transfer->store,$sell_line->product->id,$sell_line->quantity,$sell_transfer,$purchase_transfer);
                            \App\Models\ItemMove::delete_transafer($sell_transfer,$purchase_transfer);

                        }
                }

                //Adjust stock over selling if found
                $this->productUtil->adjustStockOverSelling($purchase_transfer);

                //Map sell lines with purchase lines
                $business = ['id' => $business_id,
                            'accounting_method' => $request->session()->get('business.accounting_method'),
                            'location_id' => $sell_transfer->location_id
                        ];
            }

            $purchase_transfer->status = $status == 'completed' ? 'completed' : $status;
            $purchase_transfer->save();
            $sell_transfer->status = $status == 'completed' ? 'final' : $status;
            $sell_transfer->save();

            DB::commit();

            $output = ['success' => 1,
                        'msg' => __('lang_v1.updated_succesfully')
                    ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' =>  $e->getMessage()
                        ];
        }

        return $output;
    }
}
