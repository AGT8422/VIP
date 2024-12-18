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

class CheckController extends Controller
{
    // .......... info payment ........ \\
    public function saveCheck(Request $request){
        try{
            $token     = $request->token;
            if($token == null || $token == ""){
                abort(403, 'Unauthorized action.');
            }
            $user      = User::where("api_token",$token)->first();
            if(!$user){
                abort(403, 'Unauthorized action.');
            }
            DB::beginTransaction();
            // note 0=> supplier , 1 =>customer
            $business_id = $user->business_id;

            $bills_   =  $request->only(['bill_id','bill_amount']);

            $id_trans = null;
            $transaction = null;
            if($request->bill_id != null  ){
                if(!empty($bills_)  ){
                   foreach($bills_["bill_id"] as $bl){ $id_trans =  $bl;  if( $transaction == null ) { $transaction = $bl;  }else { $transaction .= ",".$bl ; } }
                }
            }
       

            $setting               =  \App\Models\SystemAccount::where('business_id',$business_id)->first();
            $id                    =  $setting->cheque_collection;
            $ref_count   = $this->setAndGetReferenceCount("Cheque",$user->business_id ,$user->pattern_id);
            //Generate reference number
            $ref_no = $this->generateReferenceNumber("Cheque" , $ref_count,$user->business_id,"CH",$user->pattern_id);
            //return $this->add_main($request->cheque_type);
            if(isset($request->contact_id) && $request->contact_id != "" && $request->contact_id != null){
                $account_contact_id    =  \App\Account::where("contact_id",$request->contact_id)->first(); 
            }else{
                $account_contact_id    =  null;
            }
            $data                  =  new Check;
            $data->business_id     =  $business_id;
            $data->location_id     =  1;
            $data->cheque_no       =  $request->cheque_no;
            $data->contact_bank_id =  null;
            $data->type            =  0;
            $data->write_date      =  $request->write_date;
            $data->due_date        =  $request->due_date;
            $data->note            =  $request->note;
            $data->account_type    =  1;
            $data->ref_no          =  $ref_no;
            $data->amount          =  $request->amount;
            $data->contact_id      =  ($account_contact_id != null)?$account_contact_id->id:null;
            $data->account_id      =  $id;
            $data->save();
            Check::add_action($data->id,'added');
            
            $type        = 'debit';
            $credit_data = [
                'amount' => $data->amount,
                'account_id' =>$id,
                'transaction_id' =>$id_trans,
                'type' => $type,
                'sub_type' => 'deposit',
                'operation_date' => $request->write_date,
                'created_by' => $user->id,
                'note' => 'added cheque',
                'check_id'=> $data->id,
                'transaction_array'=> $transaction,
            ];
            $credit = \App\AccountTransaction::createAccountTransaction($credit_data);
            Check::contact_effect($data->id,$transaction,$id_trans,"all",$data->contact_id,$user->id);
 
            $bills   =  $request->only([
                'bill_id','bill_amount'
            ]);
           if($request->bill_id != null  ){
                if ( !empty($request->bill_id)  ) {
                    \App\Services\Cheque\Bill::pay_transaction($data->id,$bills,$user);
                }
            }
            
            $types = "check";
            \App\Models\Entry::create_entries($data,$types);

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
