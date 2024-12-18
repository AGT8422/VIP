<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
use Spatie\Activitylog\Models\Activity;


class HomeController extends Controller
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
    public function index(Request $request)
    {
        return dd($request->all());
    }
    public function check_stock($id,$status)
    {
        if ($status == 'Delivered' || $status == 'final') {
            $stock =  Warehouseinfo::where('product_id',$id)
                                ->where('store_id',app('request')->input('store_id'))->sum('product_qty');
            return $stock;
        }else{
            return 1000;
        }
    }
}
