<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\ReferenceCount;
use App\Models\Check;
use App\Models\PaymentVoucher;
use App\Models\TransactionDelivery;
use App\Models\DeliveredPrevious;
use App\Models\Entry;
use App\Contact;

class PaymentVoucherController extends Controller
{
    // .......... info payment ........ \\
    public function savePayment(Request $request){
         
        try{
          $token     = $request->token;
          $user      = User::where("api_token",$token)->first();
          if(!$user){
              abort(403, 'Unauthorized action.');
          }
          DB::beginTransaction();
          // note 0=> supplier , 1 =>customer
          $business_id = $user->business_id;
          $ref_count   = $this->setAndGetReferenceCount("voucher",$user->business_id ,$user->pattern_id);
          //Generate reference number
          $ref_no = $this->generateReferenceNumber("voucher" , $ref_count,$user->business_id,"VCH_",$user->pattern_id);
          //return $this->add_main($request->cheque_type);
          
          $amount_total       = $request->amount + $request->visa_amount;
           
          $data               =  new PaymentVoucher;
          $data->business_id  =  $business_id;
          $data->ref_no       =  $ref_no;
          if($request->visa_amount > 0){
               $data->amount       =  $request->visa_amount;
              $data->account_id   =  $user->user_visa_account_id;
          }else{
              $data->amount       =  $request->amount;
              $data->account_id   =  $user->user_account_id;
           }
          $data->additional_account_id   =  ($request->visa_amount > 0)?$user->user_visa_account_id:null;
          $data->contact_id   =  $request->contact_id;
          $data->type         =  1; 
          $data->date         =  \Carbon::now()->tz('Asia/Dubai');;
          $data->save();
          if($request->visa_amount > 0){
              $check = "visa";
          }else{
              $check = "cash";
          }
          $this->effect_account($data->id,$data->type,$user->id,$check);
          $bills   =  $request->only([
              'bill_id','bill_amount'
          ]);
        
          if ($request->bill_id != null) {
               if($request->visa_amount > 0){
                  \App\Services\PaymentVoucher\Bill::pay_transaction($data->id,$bills,$user,1);
              }else{
                  \App\Services\PaymentVoucher\Bill::pay_transaction($data->id,$bills,$user);
              }
      }
      $type="voucher";
      \App\Models\Entry::create_entries($data,$type);
      DB::commit();
      $output = ['success' => 1,
                  'msg' => " Added Successfully " ,
                  ];
      return response()->json([
                      "status"   => 200,
                      "message"  => " Added Successfully ",
                      "token"    => $token,
                      "output"   => $output
                  ]);
        }catch(Exception $e){
            DB::rollBack();
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                \Log::alert($e);
            $output = ['success' => 0,
                            'msg' => $e
                        ];
            return response()->json([
                            "status"   => 403,
                            "message"  => " Failed ",
                            "token"    => $token,
                            "output"   => $output

                        ]);
        }

  }
    //*----------------------------------------*\\
    //*--------- references  bill -------------*\\
    //******************************************\\
    public function setAndGetReferenceCount($type,$business_id,$pattern)
    {
        $ref = ReferenceCount::where('ref_type', $type)
                          ->where('business_id', $business_id)
                          ->where('pattern_id', $pattern)
                          ->first();
        if (!empty($ref)) {
            $ref->ref_count += 1;
            $ref->save();
            return $ref->ref_count;
        } else {
            $new_ref = ReferenceCount::create([
                'ref_type' => $type,
                'business_id' => $business_id,
                'pattern_id' => $pattern,
                'ref_count' => 1
            ]);
            return $new_ref->ref_count;
        }
    }
    //*----------------------------------------*\\
    //*--------- references  bill -------------*\\
    //******************************************\\
    public function generateReferenceNumber($type, $ref_count, $business_id = null, $default_prefix = null,$pattern =null)
    {
        if (!empty($default_prefix)) {
            $prefix = $default_prefix;
        }
        $ref_digits =  str_pad($ref_count, 5, 0, STR_PAD_LEFT);
        if(!isset($prefix)){
                $prefix = "";
        }
        if (!in_array($type, ['contacts', 'business_location', 'username' ,"supplier","customer"   ])) {
            $ref_year = \Carbon::now()->year;
           
            $ref_number = $prefix . $ref_year . '/' . $ref_digits;
            
        } else {
             
            $ref_number = $prefix . $ref_digits;
        }
        return  $ref_number;
    }
    //*----------------------------------------*\\
    //*--------- entries  voucher -------------*\\
    //******************************************\\
    public function effect_account($id,$type,$created_by=null,$check=null)
    {
        // supplier depit  => bank  credit
        // customer credit => debit  
        $data      =  PaymentVoucher::find($id);
        $state     =  'debit';
        $re_state  =  'credit';
        if ($type == 1 ) {
            $state     =  'credit';
            $re_state  =  'debit';
        }
        // effect cash  account 
        $credit_data = [
            'amount' => $data->amount,
            'account_id' =>($check=="cash")?$data->account_id:intVal($data->additional_account_id),
            'type' => $re_state,
            'sub_type' => 'deposit',
            'operation_date' => $data->date,
            'created_by' => ($created_by != null)? $created_by:session()->get('user.id'),
            'note' => $data->text,
            'payment_voucher_id'=>$id
        ];
        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);

        // effect contact account 
        $account_id  =  Contact::add_account($data->contact_id,$data->business_id);
        $credit_data = [
            'amount' => $data->amount,
            'account_id' =>$account_id,
            'type' => $state,
            'sub_type' => 'deposit',
            'operation_date' => $data->date,
            'created_by' => ($created_by != null)? $created_by:session()->get('user.id'),
            'note' => $data->text,
            'payment_voucher_id'=>$data->id
        ];
        $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
        
    } 


}
