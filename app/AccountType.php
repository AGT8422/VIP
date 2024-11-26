<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountType extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $hidden  = ['sub_parent_id','active','created_at','updated_at','business_id'];
  
    public function parent() {
        return $this->belongsTo(self::class, 'parent_account_type_id');
    }
    public function sub_types()
    {
        return $this->hasMany(\App\AccountType::class, 'parent_account_type_id');
    }
    public function sub_types_id()
    {
        return $this->hasMany(\App\AccountType::class, 'sub_parent_id');
    }
    // and here is the trick for nestable child. 
    public static function nestable($accounts,$list) {
        foreach ($accounts as $account) {
            $list[] = [ 
                "id"                        => $account->id,
                "name"                      => $account->name,
                "code"                      => $account->code,
                "parent_account_type_id"    => $account->parent_account_type_id,
                "sub_types"                 => $account->sub_types
            ];
            if (!$account->sub_types->isEmpty()) {
                $account->sub_types = self::nestable($account->sub_types,$list);
            }
        }
        return $list;
    }
    public function parent_account()
    {
        return $this->belongsTo(\App\AccountType::class, 'parent_account_type_id');
    }
    
    public static function create_new($input)
    {

         $data               = new AccountType;
         $check_code         =  \App\AccountType::where("code","=",$input["code"])->first();
         if(!empty($check_code)){
                return false;
        }else{
            $data->code         = $input["code"];
         }
         
         $data->name         = $input["name"];
         $data->business_id  = $input["business_id"];
         if($input["parent_account_type_id"] == null){
             $data->save();
        }else{
            $old = AccountType::find($input["parent_account_type_id"]);
            if($old->active == 1 ){
                 $data->parent_account_type_id  = $input["parent_account_type_id"];
                $data->save();
                $old->save();
            }else{
                if($old->parent_account_type_id != null){
                    $data->parent_account_type_id  = $input["parent_account_type_id"];
                    $data->save();
                }else{
                    $data->parent_account_type_id  = $input["parent_account_type_id"];
                    $data->save();
                }
            } 
        }
        return true;
    }

    public static function update_new($input,$account_type)
    {
        
   
         $data                            =  AccountType::find($account_type->id);
         $check_code                      =  \App\AccountType::where("code","=",$input["code"])->where("id","!=",$data->id)->first();
         if(!empty($check_code)){
                return false;
         }else{
            $data->code         = $input["code"];
         }
         $data->name                      = $input["name"];
         $data->business_id               = $input["business_id"];

        if($input["parent_account_type_id"] == null){
            $data->parent_account_type_id = null;
            $data->sub_parent_id = null;
            $data->save();
        }else{
            if($data->active == 1 ){
                $data->active = null;
                $new = AccountType::find($input["parent_account_type_id"]);
                $data->parent_account_type_id = $new->id;
                $data->save(); 
            }else{
                $new = AccountType::find($input["parent_account_type_id"]);
                if($new->parent_account_type_id != null){
                    $data->parent_account_type_id = $new->id;
                    $data->save(); 
                }else{
                    $data->parent_account_type_id = $new->id;
                    $data->save(); 
                }
            } 
        }
        return true;
    }

    public static function all_accounts()
    {
       $allInMain = [];
       $all = AccountType::select()->get();
       foreach($all as $key => $value){$allInMain[$value->id]=$value->name;}
       return $allInMain;
    }
    public static function allInParent($id)
    {
       $allInMain = [];
       $all = AccountType::where("parent_account_type_id",$id)->whereNull("sub_parent_id")->get();
       foreach($all as $key => $value){$allInMain[]=$value->id;}
       return $allInMain;
    }

    public static function allInSubParent($id)
    {
       $allInMain = [];
       $all = AccountType::where("sub_parent_id",$id)->get();
       foreach($all as $key => $value){$allInMain[]=$value->id;}
       return $allInMain;
    }

    public static function findAccount($id)
    {
        $accountType = AccountType::find($id);
        return $accountType;
    }
}
