<?php

namespace App\Http\Controllers;

use App\BusinessLocation;
use App\Models\Purchase;
use App\Models\Sell;
use App\Models\Liability;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Budget;
use App\Models\Aasset;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AccountTreeController extends Controller
{
    



 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sells()
    {
        if (!auth()->user()->can('sells.view') && !auth()->user()->can('aacount_tree.accounts.index')) {
                abort(403, 'Unauthorized action.');
            }
        $business_id = request()->session()->get('user.business_id');
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // $business_locations->prepend(__('lang_v1.none'), 'none');
        // dd("stop");
        return view("aaccount_tree.accounts.sells")->with(compact("business_locations"));
    }
    public function allsells()
    {
        // dd("stop");
        
        if (!auth()->user()->can('purchases.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_location = BusinessLocation::where('business_id', $business_id)->select(['id'])->get();

        $Sell = Sell::where('business_id', $business_id)->get();

        // dd($Sell);

        return Datatables::of($Sell)
        ->addColumn(
          'action',
          '@can("warehouse.edit")
              <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
              &nbsp;
          @endcan
          @can("warehouse.edit")
          <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
          &nbsp;
          @endcan
          @can("warehouse.edit")
              <button data-href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
          @endcan'
      )
      ->rawColumns(['action'])
      ->make(true);
    }



    public function Purchases()
    {
        if (!auth()->user()->can('purchases.view') && !auth()->user()->can('aacount_tree.accounts.index')) {
                abort(403, 'Unauthorized action.');
            }
        $business_id = request()->session()->get('user.business_id');
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // $business_locations->prepend(__('lang_v1.none'), 'none');
        
        return view("aaccount_tree.accounts.purchases")->with(compact("business_locations"));
    }
    public function allPurcahses()
    {
        // dd("stop");
        
        if (!auth()->user()->can('purchases.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_location = BusinessLocation::where('business_id', $business_id)->select(['id'])->get();

        $Purchases = Purchase::where('business_id', $business_id)->get();
        // dd($Purchases);
        return Datatables::of($Purchases)
        ->addColumn(
          'action',
          '@can("warehouse.edit")
              <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
              &nbsp;
          @endcan
          @can("warehouse.edit")
          <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
          &nbsp;
          @endcan
          @can("warehouse.edit")
              <button data-href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
          @endcan'
      )
      ->rawColumns(['action'])
      ->make(true);
    }


 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function expenses()
    {
        // dd("stop");

        if (!auth()->user()->can('expenses.view') && !auth()->user()->can('aacount_tree.accounts.index')) {
                abort(403, 'Unauthorized action.');
            }
        $business_id = request()->session()->get('user.business_id');
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // $business_locations->prepend(__('lang_v1.none'), 'none');
        return view("aaccount_tree.accounts.expenses")->with(compact("business_locations"));
    }
    public function allexpense()
    {
        // dd("stop");
        
        if (!auth()->user()->can('expenses.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_location = BusinessLocation::where('business_id', $business_id)->select(['id'])->get();

        $expenses = Expense::where('business_id', $business_id)->get();

        // dd($Sell);

        return Datatables::of($expenses)
        ->addColumn(
          'action',
          '@can("warehouse.edit")
              <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
              &nbsp;
          @endcan
          @can("warehouse.edit")
          <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
          &nbsp;
          @endcan
          @can("warehouse.edit")
              <button data-href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
          @endcan'
      )
      ->rawColumns(['action'])
      ->make(true);
    }
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Revenue()
    {
        // dd("stop");

        if (!auth()->user()->can('imports.view') && !auth()->user()->can('aacount_tree.accounts.index')) {
                abort(403, 'Unauthorized action.');
            }
        $business_id = request()->session()->get('user.business_id');
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // $business_locations->prepend(__('lang_v1.none'), 'none');
        return view("aaccount_tree.accounts.imports")->with(compact("business_locations"));
    }
    public function allRevenue()
    {
        // dd("stop");
        
        if (!auth()->user()->can('imports.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_location = BusinessLocation::where('business_id', $business_id)->select(['id'])->get();

        $Income = Income::where('business_id', $business_id)->get();

        // dd($Sell);

        return Datatables::of($Income)
        ->addColumn(
          'action',
          '@can("warehouse.edit")
              <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
              &nbsp;
          @endcan
          @can("warehouse.edit")
          <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
          &nbsp;
          @endcan
          @can("warehouse.edit")
              <button data-href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
          @endcan'
      )
      ->rawColumns(['action'])
      ->make(true);
    }
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Budget()
    {
        // dd("stop");

        if (!auth()->user()->can('budget.view') && !auth()->user()->can('aacount_tree.accounts.index')) {
                abort(403, 'Unauthorized action.');
            }
        $business_id = request()->session()->get('user.business_id');
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // $business_locations->prepend(__('lang_v1.none'), 'none');
        return view("aaccount_tree.accounts.budget")->with(compact("business_locations"));
    }
    public function allBudget()
    {
        // dd("stop");
        
        if (!auth()->user()->can('budget.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_location = BusinessLocation::where('business_id', $business_id)->select(['id'])->get();

        $Budgets = Budget::where('business_id', $business_id)->get();

        // dd($Sell);

        return Datatables::of($Budgets)
        ->addColumn(
          'action',
          '@can("warehouse.edit")
              <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
              &nbsp;
          @endcan
          @can("warehouse.edit")
          <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
          &nbsp;
          @endcan
          @can("warehouse.edit")
              <button data-href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
          @endcan'
      )
      ->rawColumns(['action'])
      ->make(true);
    }
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Liability()
    {
        // dd("stop");

        if (!auth()->user()->can('antagonists.view') && !auth()->user()->can('aacount_tree.accounts.index')) {
                abort(403, 'Unauthorized action.');
            }
        $business_id = request()->session()->get('user.business_id');
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // $business_locations->prepend(__('lang_v1.none'), 'none');
        return view("aaccount_tree.accounts.antagonists")->with(compact("business_locations"));
    }
    public function allliability()
    {
        // dd("stop");
        
        if (!auth()->user()->can('assets.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_location = BusinessLocation::where('business_id', $business_id)->select(['id'])->get();

        $liability = Liability::where('business_id', $business_id)->get();

        // dd($Sell);

        return Datatables::of($liability)
        ->addColumn(
          'action',
          '@can("warehouse.edit")
              <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
              &nbsp;
          @endcan
          @can("warehouse.edit")
          <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
          &nbsp;
          @endcan
          @can("warehouse.edit")
              <button data-href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
          @endcan'
      )
      ->rawColumns(['action'])
      ->make(true);
    }
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function assets()
    {
        // dd("stop");

        if (!auth()->user()->can('assets.view') && !auth()->user()->can('aacount_tree.accounts.index')) {
                abort(403, 'Unauthorized action.');
            }
        $business_id = request()->session()->get('user.business_id');
        
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        // $business_locations->prepend(__('lang_v1.none'), 'none');
        return view("aaccount_tree.accounts.assets")->with(compact("business_locations"));
    }
    public function allassets()
    {
        // dd("stop");
        
        if (!auth()->user()->can('assets.view') ) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $business_location = BusinessLocation::where('business_id', $business_id)->select(['id'])->get();

        $Aasset = Aasset::where('business_id', $business_id)->get();

        // dd($Sell);

        return Datatables::of($Aasset)
        ->addColumn(
          'action',
          '@can("warehouse.edit")
              <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
              &nbsp;
          @endcan
          @can("warehouse.edit")
          <a href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
          &nbsp;
          @endcan
          @can("warehouse.edit")
              <button data-href="{{action(\'WarehouseController@edit\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
          @endcan'
      )
      ->rawColumns(['action'])
      ->make(true);
    }

 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


}
