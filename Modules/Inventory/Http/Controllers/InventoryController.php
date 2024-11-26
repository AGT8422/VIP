<?php

namespace Modules\Inventory\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\BusinessLocation;
use App\Transaction;
use App\Utils\ModuleUtil;

use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Datatables;
use App\StocktackingLine;
use DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (!auth()->user()->can('stocktacking.view') ) {
            abort(403, 'Unauthorized action.');
        }

        // close any open transaction
       /* $transactions=Transaction::where('transactions.type','stocktacking')->where*/

        $transactions=\DB::table('transactions')->join('business_locations','business_locations.id','transactions.location_id')
            ->where('transactions.type','stocktacking')
            ->where('transactions.business_id',$business_id)
            ->select(
                'transactions.*',
                'business_locations.name as location_name'
            )
            ->get();


        return view('inventory::index',['transactions'=>$transactions]);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get("user.business_id");
        $business_locations  = \App\BusinessLocation::forDropdown($business_id);
        return view('inventory::create')->with("business_locations",$business_locations);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('inventory::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('inventory::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
