<?php

namespace App\Http\Controllers;

use App\Business;

use App\BusinessLocation;
use App\Product;
use App\Transaction;
use App\Utils\ProductUtil;
use App\Variation;
use DB;
use Excel;
use Illuminate\Http\Request;
use App\Utils\TransactionUtil;

class ImportOpeningStockController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display import product screen.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('product.opening_stock') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views')&& !auth()->user()->can('admin_without.views')&& !auth()->user()->can('admin_supervisor.views')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded   = extension_loaded('zip') ? true : false;

        $date_formats = Business::date_formats();
        $date_format  = session('business.date_format');
        $date_format  = isset($date_formats[$date_format]) ? $date_formats[$date_format] : $date_format;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $notification = ['success' => 0,
                            'msg' => 'Please install/enable PHP Zip archive for import'
                        ];

            return view('import_opening_stock.index')
                ->with(compact('notification', 'date_format'));
        } else {
            return view('import_opening_stock.index')
                ->with(compact('date_format'));
        }
    }

    /**
     * Imports the uploaded file to database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('product.opening_stock') && !auth()->user()->can('warehouse.views')&& !auth()->user()->can('manufuctoring.views')&& !auth()->user()->can('admin_without.views')&& !auth()->user()->can('admin_supervisor.views')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $notAllowed = $this->productUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }
            
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('products_csv')) {
                $file = $request->file('products_csv');
                
                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';
                
                DB::beginTransaction();
                $business_id = request()->session()->get('user.business_id');
                $ref_count = $this->productUtil->setAndGetReferenceCount('Open Quantity');
                $ref_no  = $this->productUtil->generateReferenceNumber('Open Quantity', $ref_count);
               
                $store   = \App\Models\Warehouse::where('name',$imported_data[0][2])->first();
                if (empty($store)) {
                    $store =  \App\Models\Warehouse::where('business_id',$business_id)
                                    ->where('parent_id','>',0)->first();
                }
                $location =  \App\BusinessLocation::where('business_id',$business_id)
                                    ->where('name',$imported_data[0][1])->first();
                if (empty($location)) {
                    $location =  \App\BusinessLocation::where('business_id',$business_id)
                                    ->first();
                }
                
                $tr =   Transaction::create([
                                            'ref_no'=>$ref_no,
                                            'type'=>'opening_stock',
                                            'status'=>'received',
                                            'business_id'=>$business_id,
                                            'store'=>$store->id,
                                            'location_id'=> $location->id,  
                                            'transaction_date'=>date('Y-m-d h:i:s a',time())
                                        ]);
                foreach ($imported_data as $key => $value) {
                   // process start
                    $product =  \App\Product::where('sku',$value[0])->first();
                    if ($product) {
                        $store   = \App\Models\Warehouse::where('name',$value[2])->first();
                        if (empty($store)) {
                            $store =  \App\Models\Warehouse::where('business_id',$business_id)
                                            ->where('parent_id','>',0)->first();
                        }
                        // create_purchase line 
                        $pr                         =  new \App\PurchaseLine;
                        $pr->store_id               = $request->store_id;
                        $pr->product_id             = $product->id;
                        $pr->transaction_id         = $tr->id;
                        $pr->variation_id           = isset($product->variations[0]->id)?$product->variations[0]->id:NULL;
                        $pr->quantity               = $value[3];
                        $pr->pp_without_discount    = $value[4];
                        $pr->discount_percent       = 0;
                        $pr->purchase_price         = $value[4];
                        $pr->purchase_price_inc_tax =  ($value[4] + $value[4]*.05);
                        $pr->item_tax               =  $value[4]*.05;
                        $pr->tax_id    =  1;
                        $pr->save();
                        //end
                        $data                          =  new \App\Models\OpeningQuantity;
                        $data->warehouse_id            =  $store->id;
                        $data->business_location_id    =  $location->id;
                        $data->quantity                =  $value[3];
                        $data->product_id              =  $product->id;
                        $data->price                   =  $value[4];
                        $data->transaction_id          =  $tr->id;
                        $data->purchase_line_id        =  $pr->id;
                        $data->save();
                        $data =  \App\Models\OpeningQuantity::find($data->id);
                        \App\Models\WarehouseInfo::update_stoct($data->product_id,$data->warehouse_id,$data->quantity,$business_id);
                        //****** eb ..............................................................
                        $variation_id = Variation::where('product_id', $data->product_id)->first();
                         //.........................................................................
                        //move
                        $info =  \App\Models\WarehouseInfo::where('store_id',$data->warehouse_id)
                        ->where('product_id',$data->product_id)->first();
                        $move                      =  new \App\MovementWarehouse;
                        $move->business_id         =  $tr->business_id;
                        $move->transaction_id      =  $tr->id  ;
                        $move->product_name        =  $data->product->name;
                        $move->unit_id             =  $data->product->unit_id;
                        $move->store_id            =  $data->warehouse_id  ;
                        $move->movement            =   'opening quantity';
                        $move->plus_qty            =  $data->quantity ;
                        $move->minus_qty           =  0;
                        $move->current_qty         =  $info->product_qty ;
                        
                        $move->product_id          =  $data->product_id;
                        $move->current_price       =  $data->price;
                        $move->opening_quantity_id =  $data->id;
                        $move->save();
                        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($tr->business_id);
                        $this->productUtil->updateProductQuantity($location->id,$data->product_id,$variation_id->id 
                                                          ,$data->quantity,0, $currency_details);
                        $before = \App\Models\WarehouseInfo::qty_before($tr);
                        \App\Models\ItemMove::create_open($tr,0,$before,null,0,$pr->id); 
                        
                    }
                    
                    
                   
                }
            }
            if (!$is_valid) {
                throw new \Exception($error_msg);
            }

            $output = ['success' => 1,
                            'msg' => __('product.file_imported_successfully')
                        ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            
            $output = ['success' => 0,
                            'msg' => "File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage()
                        ];
            return redirect('import-opening-stock')->with('notification', $output);
        }

        return redirect('import-opening-stock')->with('status', $output);
    }

  
}
