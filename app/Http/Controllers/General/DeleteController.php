<?php

namespace App\Http\Controllers\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    // .. Delete functions

    public function index()
    {
        if(!request()->session()->get("user.id") == 1){
            abort(403,"UnAutherization Actions.");
        }

        return view("delete_file.index"); 
    }
    //..... delete  every thing
    public function delete_all()
    {
        if(!request()->session()->get("user.id") == 1){
                abort(403,"UnAutherization Actions.");
        }
        if(request()->ajax()){

            $business_id = request()->session()->get("user.business_id");
            $location    = \App\BusinessLocation::where("business_id",$business_id)->first();

            /*....... start from this table  ...
              . .. .. . . . . . .. . . . . . ............................................................................................
              /.01./_ products - product_location - product_variation  - variation - variations_details  - itemMove - warehouseInfo - movmentWarehouse
              /.02./_ transaction - pattern - account_transaction - accounts - open_quantity - accounts_type - suppliers - customers  - purchase_line - transactionSellsLine - entries - wr/delivery_previous - wr/recieve_previous
              /.03./_ check  - voucher  - transaction_payment - gournal_voucher - daily_payments - expense_voucher  - brand - contact_banks
              /.04./_ brand  - category -  users - map - mfg_recipe - mfg_recipe_integrient - unit
              /.05./_ references-count  - warehouse - adjusment - transafer - warrenty
              . .. .. .. .. . . . . . . . . . . ........................................................................................
            */

            $_01_pro        = \App\Product::where("business_id",$business_id)->get() ;
            $_01_pro_loca   = \App\Models\ProductLocation::where("location_id",$location->id)->get() ;
            $_01_pro_var    = \App\ProductVariation::whereIn("product_id",$_01_pro->pluck("id"))->get() ;
            $_01_var        = \App\Variation::whereIn("product_id",$_01_pro->pluck("id"))->get() ;
            $_01_var_det    = \App\VariationLocationDetails::whereIn("product_id",$_01_pro->pluck("id"))->get() ;
            $_01_it_move    = \App\Models\ItemMove::where("business_id",$business_id)->whereIn("product_id",$_01_pro->pluck("id"))->get() ;
            $_01_ware_inf   = \App\Models\WarehouseInfo::where("business_id",$business_id)->whereIn("product_id",$_01_pro->pluck("id"))->get() ;
            $_01_move_ware  = \App\MovementWarehouse::where("business_id",$business_id)->whereIn("product_id",$_01_pro->pluck("id"))->get() ;
            

            $_02_tr         = \App\Transaction::where("business_id",$business_id)->get() ;
            $_02_pur        = \App\PurchaseLine::whereIn("transaction_id",$_02_tr->pluck("id"))->get() ;
            $_02_pr_tr_rev  = \App\Models\TransactionRecieved::whereIn("transaction_id",$_02_tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $_02_pr_recev   = \App\Models\RecievedPrevious::whereIn("transaction_id",$_02_tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $_02_pr_wrong   = \App\Models\RecievedWrong::whereIn("transaction_id",$_02_tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $_02_sell       = \App\TransactionSellLine::whereIn("transaction_id",$_02_tr->pluck("id"))->get() ;
            $_02_s__tr_deliv= \App\Models\TransactionDelivery::whereIn("transaction_id",$_02_tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $_02_s_deliv    = \App\Models\DeliveredPrevious::whereIn("transaction_id",$_02_tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $_02_s_wrong    = \App\Models\DeliveredWrong::whereIn("transaction_id",$_02_tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $_02_open_qtit  = \App\Models\OpeningQuantity::where("business_location_id",$business_id)->get() ;
            $_02_acct_tr    = \App\AccountTransaction::whereIn("transaction_id",$_02_tr->pluck("id"))->get() ;
            $_02_account    = \App\Account::where("business_id",$business_id)->get() ;
            $_02_acct_typ   = \App\AccountType::where("business_id",$business_id)->get() ;
            $_02_entries    = \App\Models\Entry::where("business_id",$business_id)->get() ;
            $_02_pattern    = \App\Models\Pattern::where("business_id",$business_id)->get() ;
            $_02_customer   = \App\Contact::where("type","customer")->where("business_id",$business_id)->get() ;
            $_02_supplier   = \App\Contact::where("type","supplier")->where("business_id",$business_id)->get() ;
            $_02_qua_term   = \App\Models\QuatationTerm::where("business_id",$business_id)->get() ;
            

            $_03_check      = \App\Models\Check::where("business_id",$business_id)->get() ;
            $_03_check_act  = \App\Models\ChequeAction::whereIn("check_id", $_03_check->pluck("id"))->get() ;
            $_03_contact_bk = \App\Models\ContactBank::where("business_id", $business_id)->get() ;
            $_03_pay_voucher= \App\Models\PaymentVoucher::where("business_id", $business_id)->get() ;
            $_03_dail_pay   = \App\Models\DailyPayment::where("business_id",$business_id)->get() ;
            $_03_dail_pay_it= \App\Models\DailyPaymentItem::whereIn("daily_payment_id",$_03_dail_pay->pluck("id"))->get() ;
            $_03_g_voucher  = \App\Models\GournalVoucher::where("business_id",$business_id)->get() ;
            $_03_g_voucher_i= \App\Models\GournalVoucherItem::whereIn("gournal_voucher_id",$_03_g_voucher->pluck("id"))->get() ;
            $_03_add_ship   = \App\Models\AdditionalShipping::whereIn("transaction_id",$_02_tr->pluck("id"))->get() ;
            $_03_add_ship_it= \App\Models\AdditionalShippingItem::whereIn("additional_shipping_id",$_03_add_ship->pluck("id"))->get() ;
            $_03_paym       = \App\TransactionPayment::where("business_id",$business_id)->whereIn("transaction_id",$_02_tr->pluck("id"))->get() ;
            

            $_04_brand      = \App\Brands::where("business_id",$business_id)->get() ;
            $_04_category   = \App\Category::where("business_id",$business_id)->get() ;
            $_04_users      = \App\User::where("business_id",$business_id)->get() ;
            $_04_map        = \App\Models\StatusLive::where("business_id",$business_id)->get() ;
            $_04_recipe     = \Modules\Manufacturing\Entities\MfgRecipe::whereIn("product_id",$_01_pro->pluck("id"))->get();
            $_04_recipe_int = \Modules\Manufacturing\Entities\MfgRecipeIngredient::whereIn("mfg_recipe_id",$_04_recipe->pluck("id"))->get();
            $_04_unit       = \App\Unit::where("business_id",$business_id)->get();

            $_05_ref_count  = \App\ReferenceCount::where("business_id",$business_id)->get();
            $_05_warehouse  = \App\Models\Warehouse::where("business_id",$business_id)->get();
            $_05_transafer  = \App\StocktackingLine::whereIn("transaction_id",$_02_tr->pluck("id"))->get();
            $_05_adjusment  = \App\StockAdjustmentLine::whereIn("transaction_id",$_02_tr->pluck("id"))->get();
            $_05_warrenty   = \App\Warranty::where("business_id",$business_id)->get();



            $output = [
                "success"=>1,
                "msg"=>__("messages.deleted_successfully")
            ];
            return $output;
        }

    }
    //..... delete  accounts
    public function delete_accounts()
    {
        if(!request()->session()->get("user.id") == 1){
                abort(403,"UnAutherization Actions.");
        }
        if(request()->ajax()){

            $business_id = request()->session()->get("user.business_id");

            $tr          = \App\Transaction::where("business_id",$business_id)->get() ;
            $account     = \App\Account::where("business_id",$business_id)->get() ;
            $acct_typ    = \App\AccountType::where("business_id",$business_id)->get() ;
            $acct_tr     = \App\AccountTransaction::whereIn("transaction_id",$tr->pluck("id"))->get() ;
            $entries     = \App\Models\Entry::where("business_id",$business_id)->get() ;
        
            $ref_count   = \App\ReferenceCount::where("business_id",$business_id)->get();

            $output = [
                "success"=>1,
                "msg"=>__("messages.deleted_successfully")
            ];
            return $output;
        }

    }
    //..... delete  users
    public function delete_users()
    {
        if(!request()->session()->get("user.id") == 1){
                abort(403,"UnAutherization Actions.");
        }
        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");
            $users       = \App\User::where("business_id",$business_id)->get() ;
            $this->delete_all_for_one($user_id);

            $output = [
                "success"=>1,
                "msg"=>__("messages.deleted_successfully")
            ];
            return $output;
        }

    }
    //..... delete  customers
    public function delete_customers()
    {
        if(!request()->session()->get("user.id") == 1){
                abort(403,"UnAutherization Actions.");
        }
        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");

            $customer   = \App\Contact::where("type","customer")->where("business_id",$business_id)->get() ;

            $ref_count  = \App\ReferenceCount::where("business_id",$business_id)->get();

            $output = [
                "success"=>1,
                "msg"=>__("messages.deleted_successfully")
            ];
            return $output;
        }

    }
    //..... delete  suppliers
    public function delete_suppliers()
    {
        if(!request()->session()->get("user.id") == 1){
                abort(403,"UnAutherization Actions.");
        }
        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");
            $supplier   = \App\Contact::where("type","supplier")->where("business_id",$business_id)->get() ;

            $ref_count  = \App\ReferenceCount::where("business_id",$business_id)->get();


            $output = [
                "success"=>1,
                "msg"=>__("messages.deleted_successfully")
            ];
            return $output;
        }

    }
    //..... delete  items
    public function delete_items()
    {
        if(!request()->session()->get("user.id") == 1){
                abort(403,"UnAutherization Actions.");
        }
        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");
            $location    = \App\BusinessLocation::where("business_id",$business_id)->first();

            $pro        = \App\Product::where("business_id",$business_id)->get() ;
            $pro_loca   = \App\Models\ProductLocation::where("location_id",$location->id)->get() ;
            $pro_var    = \App\ProductVariation::whereIn("product_id",$pro->pluck("id"))->get() ;
            $var        = \App\Variation::whereIn("product_id",$pro->pluck("id"))->get() ;
            $var_det    = \App\VariationLocationDetails::whereIn("product_id",$pro->pluck("id"))->get() ;
            $it_move    = \App\Models\ItemMove::where("business_id",$business_id)->whereIn("product_id",$pro->pluck("id"))->get() ;
            $ware_inf   = \App\Models\WarehouseInfo::where("business_id",$business_id)->whereIn("product_id",$pro->pluck("id"))->get() ;
            $move_ware  = \App\MovementWarehouse::where("business_id",$business_id)->whereIn("product_id",$pro->pluck("id"))->get() ;
            
            $output = [
                "success"=>1,
                "msg"=>__("messages.deleted_successfully")
            ];
            return $output;
        }

    }
    //..... delete  purchases
    public function delete_purchases()
    {
        if(!request()->session()->get("user.id") == 1){
                abort(403,"UnAutherization Actions.");
        }
        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");
            
            $tr         = \App\Transaction::where("business_id",$business_id)->get() ;
            $pur        = \App\PurchaseLine::whereIn("transaction_id",$tr->pluck("id"))->get() ;
            $tr_rev     = \App\Models\TransactionRecieved::whereIn("transaction_id",$tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $pr_recev   = \App\Models\RecievedPrevious::whereIn("transaction_id",$tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $pr_wrong   = \App\Models\RecievedWrong::whereIn("transaction_id",$tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $map        = \App\Models\StatusLive::where("business_id",$business_id)->get() ;

            $ref_count  = \App\ReferenceCount::where("business_id",$business_id)->get();

            $output = [
                "success"=>1,
                "msg"=>__("messages.deleted_successfully")
            ];
            return $output;
        }

    }
    //..... delete  payments
    public function delete_payments()
    {
        if(!request()->session()->get("user.id") == 1){
                abort(403,"UnAutherization Actions.");
        }
        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");
            
            
            $tr          = \App\Transaction::where("business_id",$business_id)->get() ;
            $check       = \App\Models\Check::where("business_id",$business_id)->get() ;
            $check_act   = \App\Models\ChequeAction::whereIn("check_id", $check->pluck("id"))->get() ;
            $contact_bk  = \App\Models\ContactBank::where("business_id", $business_id)->get() ;
            $pay_voucher = \App\Models\PaymentVoucher::where("business_id", $business_id)->get() ;
            $dail_pay    = \App\Models\DailyPayment::where("business_id",$business_id)->get() ;
            $dail_pay_it = \App\Models\DailyPaymentItem::whereIn("daily_payment_id",$dail_pay->pluck("id"))->get() ;
            $g_voucher   = \App\Models\GournalVoucher::where("business_id",$business_id)->get() ;
            $g_voucher_i = \App\Models\GournalVoucherItem::whereIn("gournal_voucher_id",$g_voucher->pluck("id"))->get() ;
            $add_ship    = \App\Models\AdditionalShipping::whereIn("transaction_id",$tr->pluck("id"))->get() ;
            $add_ship_it = \App\Models\AdditionalShippingItem::whereIn("additional_shipping_id",$add_ship->pluck("id"))->get() ;
            $paym        = \App\TransactionPayment::where("business_id",$business_id)->whereIn("transaction_id",$tr->pluck("id"))->get() ;
            $map         = \App\Models\StatusLive::where("business_id",$business_id)->get() ;

            $ref_count  = \App\ReferenceCount::where("business_id",$business_id)->get();

            $output = [
                "success"=>1,
                "msg"=>__("messages.deleted_successfully")
            ];
            return $output;
        }

    }
    //..... delete  sells
    public function delete_sells()
    {
        if(!request()->session()->get("user.id") == 1){
                abort(403,"UnAutherization Actions.");
        }
        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");
            
            $tr         = \App\Transaction::where("business_id",$business_id)->get() ;
            $sell       = \App\TransactionSellLine::whereIn("transaction_id",$tr->pluck("id"))->get() ;
            $tr_deliv   = \App\Models\TransactionDelivery::whereIn("transaction_id",$tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $s_deliv    = \App\Models\DeliveredPrevious::whereIn("transaction_id",$tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $s_wrong    = \App\Models\DeliveredWrong::whereIn("transaction_id",$tr->pluck("id"))->where("business_id",$business_id)->get() ;
            $map        = \App\Models\StatusLive::where("business_id",$business_id)->get() ;

            $ref_count  = \App\ReferenceCount::where("business_id",$business_id)->get();


            $output = [
                "success"=>1,
                "msg"=>__("messages.deleted_successfully")
            ];
            return $output;
        }

    }
    //..... delete  reset numbers
    public function reset_numbers()
    {
        if(!request()->session()->get("user.id") == 1){
                abort(403,"UnAutherization Actions.");
        }
        if(request()->ajax()){
            $business_id = request()->session()->get("user.business_id");
            
            $ref_count  = \App\ReferenceCount::where("business_id",$business_id)->get();

            $output = [
                "success"=>1,
                "msg"=>__("messages.deleted_successfully")
            ];
            return $output;
        }

    }

    //....... delete _ for _one
    public function delete_all_for_one($id)
    {
        
    }

}
