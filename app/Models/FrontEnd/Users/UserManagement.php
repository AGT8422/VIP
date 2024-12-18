<?php

namespace App\Models\FrontEnd\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Model
{
    use HasFactory,SoftDeletes;
    // *** initialize the table 
    protected  $table = "users";
    // ...1 List Users
    public static function Users($business_id,$date) {
        $users          = \App\User::where("business_id",$business_id)->orderBy('id',"desc")->get();
        $count_of_users = 0;
        $list_users     = [];
        foreach($users as $i){
            if($i->is_cmmsn_agnt == 0)
            {
                $permissions = [];
                if($i->roles->first()){
                    $role = Role::where('roles.id', $i->roles->first()->id)
                                    ->with('permissions')
                                    ->first();
                    $allData =  $role->permissions ;
                    foreach($allData as $item){
                        $permissions[] = $item->name;
                    }
                    
                }
                $list_users[] =  [ 
                    "id"          => $i->id,
                    "name"        => $i->first_name,
                    "username"    => $i->username,
                    // "role"     => ($i->roles->first())?substr($i->roles->first()->name, 0, strpos($i->roles->first()->name, '#')):"",
                    "role"        => ($i->roles->first())?strrev(substr(strrev($i->roles->first()->name),strpos(strrev($i->roles->first()->name), '#')+1)):"",
                    "permission"  => $permissions
                ];
                $count_of_users++;
            }
        }
        $array = [
                "count" => $count_of_users,
                "list"  => $list_users
            ];
        return $array;  
    }
    // ...2 Create Users
    public static function CreateUsers($user){
        try{
            // ** list array 
            $list         = []; 
            // **1** For User Initialize 
            $agents       = UserManagement::agents($user);
            $cost_center  = UserManagement::cost_center($user);
            $warehouse    = UserManagement::warehouse($user);
            $patterns     = UserManagement::patterns($user);
            $accounts     = UserManagement::items($user);
            $contacts     = UserManagement::contacts($user);
            $roles        = UserManagement::roles($user);
            $gender       = UserManagement::gender($user);
            $marital      = UserManagement::marital($user);
            $pPrice       = UserManagement::pPrice($user);
            $taxes        = UserManagement::taxes($user);
            $locations    = UserManagement::locations($user);
            
            $list ["accounts"]          = $accounts;
            $list ["agents"]            = $agents;
            $list ["cost_center"]       = $cost_center;
            $list ["warehouse"]         = $warehouse;
            $list ["patterns"]          = $patterns;
            $list ["contacts"]          = $contacts;
            $list ["roles"]             = $roles;
            $list ["gender"]            = $gender;
            $list ["marital"]           = $marital;
            $list ["taxes"]             = $taxes;
            $list ["ProductPrice"]      = $pPrice;
            $list ["BusinessLocation"]  = $locations;

            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // ...3 Edit Users
    public static function EditUsers($user,$id){
        try{
            // ** list array 
            $list         = []; 
            // **1** For User Initialize 
            $locations    = UserManagement::locations($user);
            $agents       = UserManagement::agents($user);
            $cost_center  = UserManagement::cost_center($user);
            $warehouse    = UserManagement::warehouse($user);
            $patterns     = UserManagement::patterns($user);
            $accounts     = UserManagement::items($user);
            $contacts     = UserManagement::contacts($user);
            $roles        = UserManagement::roles($user);
            $gender       = UserManagement::gender($user);
            $marital      = UserManagement::marital($user);
            $pPrice       = UserManagement::pPrice($user);
            $taxes        = UserManagement::taxes($user);
            $user         = UserManagement::user($user,$id);
            
            $list ["BusinessLocation"]   = $locations;
            $list ["accounts"]           = $accounts;
            $list ["agents"]             = $agents;
            $list ["cost_center"]        = $cost_center;
            $list ["warehouse"]          = $warehouse;
            $list ["patterns"]           = $patterns;
            $list ["contacts"]           = $contacts;
            $list ["roles"]              = $roles;
            $list ["gender"]             = $gender;
            $list ["marital"]            = $marital;
            $list ["taxes"]              = $taxes;
            $list ["pPrice"]             = $pPrice;
            $list ["user"]               = $user;



            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // ...2 Store Users
    public static function StoreUsers($data,$request){
        try{
        
            // **1** User Data
            $user       = UserManagement::saveUser($data,$request);
            return $user;
        }catch(Exception $e){
            return false;
        }
    }
    // ...3 Update Users
    public static function UpdateUsers($data,$request,$id){
        try{
             
            // **1** User Data
            $user       = UserManagement::updateUser($data,$request,$id);
            return $user;
        }catch(Exception $e){
            return false;
        }
    }
    // ...4 Delete Users
    public static function DeleteUsers($id){
        try{
             
            // **1** User Data
            $user       = UserManagement::deleteUser($id);
            return $user;
        }catch(Exception $e){
            return false;
        }
    }
    // ...5 View Users ******** important ** 24-10-2023
    public static function ViewUsers($user,$id) {
        try{
            \DB::beginTransaction();
            // **1****VIEW* 
            $user         = UserManagement::user($user,$id,"view");
            \DB::commit();
            return $user;
        }catch(Exception $e){
            return false;
        }
    }

    // **1** SECTIONS ACTIONS ** \\
    // *** store *******************************
        public static function saveUser($data,$request) {
            try{
                $check                             =   0;
                $location                          = \App\BusinessLocation::find($data["business_id"]);
                \DB::beginTransaction();
                $user                              =   new \App\User();
                $user->user_type                   =   "user";
                $user->business_id                 =   $location->business_id;
                $user->surname                     =   $data["surname"];
                $user->first_name                  =   $data["first_name"];
                $user->last_name                   =   $data["last_name"];
                $user->email                       =   $data["email"];
                $user->status                      =   ($data["is_active"] == 1) ? 'active' : 'inactive';
                $user->user_visa_account_id        =   $data["user_visa_account_id"];
                $user->user_agent_id               =   $data["user_agent_id"];
                $user->user_cost_center_id         =   $data["user_cost_center_id"];
                $user->user_store_id               =   $data["user_store_id"];
                $user->user_account_id             =   $data["user_account_id"];
                $user->tax_id                      =   $data["user_tax_id"];
                $user->user_pattern_id             =   $data["user_pattern_id"];
                $user->allow_login                 =   $data["allow_login"];
                $user->gender                      =   $data["gender"];
                $user->username                    =   $data["username"];
                $user->password                    =   Hash::make($data["password"]);
                $user->cmmsn_percent               =   $data["cmmsn_percent"];
                $user->max_sales_discount_percent  =   $data["max_sales_discount_percent"];
                $user->pattern_id                  =   $data["pattern_id"];
                $user->selected_contacts           =   $data["selected_contacts"];
                $user->dob                         =   ($data["dob"]!= null || $data["dob"] != "")?\Carbon::createFromFormat("Y-m-d",$data["dob"]):null;
                $user->marital_status              =   $data["marital_status"];
                $user->blood_group                 =   $data["blood_group"];
                $user->contact_number              =   $data["contact_number"];
                $user->alt_number                  =   $data["alt_number"];
                $user->family_number               =   $data["family_number"];
                $user->fb_link                     =   $data["fb_link"];
                $user->twitter_link                =   $data["twitter_link"];
                $user->social_media_1              =   $data["social_media_1"];
                $user->social_media_2              =   $data["social_media_2"];
                $user->custom_field_1              =   $data["custom_field_1"];
                $user->custom_field_2              =   $data["custom_field_2"];
                $user->custom_field_3              =   $data["custom_field_3"];
                $user->custom_field_4              =   $data["custom_field_4"];
                $user->guardian_name               =   $data["guardian_name"];
                $user->id_proof_name               =   $data["id_proof_name"];
                $user->id_proof_number             =   $data["id_proof_number"];
                $user->permanent_address           =   $data["permanent_address"];
                $user->current_address             =   $data["current_address"];
                $user->bank_details                =   json_encode($data["bank_details"]);
                $user->save();
                if($data["selected_contacts"] != 0){
                    $user->contactAccess()->sync(json_decode($data["selected_contact_ids"]));
                    $user->save();
                }else{
                    $user->contactAccess()->sync([]);
                    $user->save();
                }
                // **1****ROLE* 
                $role_id                           = $data["role"];
                $previous_role                     = 0;
                if ($previous_role != $role_id) {
                    $role = Role::find($role_id);
                    if(empty($role)){
                       $check = 1;
                    }
                    $user->assignRole($role->name);
                }
                // **2****PERMISSION* 
                UserManagement::permissionLocations($user,$request);
                if($check == 1){
                    return false;
                }else{
                    \DB::commit();
                    return true;
                    
                }
            }catch(Exception $e){
                return false;
            }
        }
    // ************************************
    // *** update *******************************
        public static function updateUser($data,$request,$id) {
            try{
                
                $check                             =   0;
                \DB::beginTransaction();
                $location                          = \App\BusinessLocation::find($data["business_id"]);
                $user                              =   \App\User::find($id);
                $user->user_type                   =   "user";
                $user->business_id                 =   $location->business_id;
                $user->surname                     =   $data["surname"];
                $user->first_name                  =   $data["first_name"];
                $user->last_name                   =   $data["last_name"];
                $user->email                       =   $data["email"];
                $user->status                      =   ($data["is_active"] == 1) ? 'active' : 'inactive';
                $user->user_visa_account_id        =   $data["user_visa_account_id"];
                $user->user_agent_id               =   $data["user_agent_id"];
                $user->user_cost_center_id         =   $data["user_cost_center_id"];
                $user->user_store_id               =   $data["user_store_id"];
                $user->user_account_id             =   $data["user_account_id"];
                $user->user_pattern_id             =   $data["user_pattern_id"];
                $user->allow_login                 =   $data["allow_login"];
                $user->gender                      =   $data["gender"];
                $user->tax_id                      =   $data["user_tax_id"];
                $user->username                    =   $data["username"];
                if(isset($data["password"]) && $data["password"] != "" && $data["password"] != null){
                    $user->password                    =   Hash::make($data["password"]);
                }
                $user->cmmsn_percent               =   $data["cmmsn_percent"];
                $user->max_sales_discount_percent  =   $data["max_sales_discount_percent"];
                $user->pattern_id                  =   $data["pattern_id"];
                $user->selected_contacts           =   $data["selected_contacts"];
                $user->dob                         =   ($data["dob"]!= null || $data["dob"] != "")?\Carbon::createFromFormat("Y-m-d",$data["dob"]):null;
                $user->marital_status              =   $data["marital_status"];
                $user->blood_group                 =   $data["blood_group"];
                $user->contact_number              =   $data["contact_number"];
                $user->alt_number                  =   $data["alt_number"];
                $user->family_number               =   $data["family_number"];
                $user->fb_link                     =   $data["fb_link"];
                $user->twitter_link                =   $data["twitter_link"];
                $user->social_media_1              =   $data["social_media_1"];
                $user->social_media_2              =   $data["social_media_2"];
                $user->custom_field_1              =   $data["custom_field_1"];
                $user->custom_field_2              =   $data["custom_field_2"];
                $user->custom_field_3              =   $data["custom_field_3"];
                $user->custom_field_4              =   $data["custom_field_4"];
                $user->guardian_name               =   $data["guardian_name"];
                $user->id_proof_name               =   $data["id_proof_name"];
                $user->id_proof_number             =   $data["id_proof_number"];
                $user->permanent_address           =   $data["permanent_address"];
                $user->current_address             =   $data["current_address"];
                $user->bank_details                =   json_encode($data["bank_details"]);
                $user->update();
                if($data["selected_contacts"] != 0){
                    $user->contactAccess()->sync(json_decode($data["selected_contact_ids"]));
                    $user->update();
                }else{
                    $user->contactAccess()->sync([]);
                    $user->update();
                }
                // **1****ROLE* 
                $role_id                           = $data["role"];
                $user_role                         = $user->roles->first();
                $previous_role                     = !empty($user_role->id) ? $user_role->id : 0;
                if ($previous_role != $role_id) {
                    if (!empty($previous_role)) {
                        $user->removeRole($user_role->name);
                    }
                    $role = Role::find($role_id);
                    if(empty($role)){
                        $check = 1;
                    }
                    $user->assignRole($role->name);
                }
                // **2****PERMISSION* 
                UserManagement::permissionLocations($user,$request);
                if($check == 1){
                    return false;
                }else{
                    \DB::commit();
                    return true;

                }
            }catch(Exception $e){
                return false;
            }
        }
    // ************************************
    // *** delete *******************************
        public static function deleteUser($id) {
            try{
                \DB::beginTransaction();
                // **1****USER* 
                $user                              =   \App\User::find($id);
                $user->username                    =   $user->username."_deleted_at_".time();
                // $user_role                         =   ($user->roles->first())?$user->roles->first()->delete():NULL;
                $user->update();
                $user->delete();
                \DB::commit();
                return true;
            }catch(Exception $e){
                return false;
            }
        }
    // ************************************

    // **2** ADDITIONAL INFORMATION *** \\
    // *** agents ********************************
        public static function agents($users) {
            $agents           = [] ;
            $business_id      = $users->business_id;
            $us               = \App\User::where('business_id', $business_id)
                                            ->where('is_cmmsn_agnt', 1)->get();
            foreach($us as $it){
               $agents[] = [
                    "id"   => $it->id,        
                    "name" => $it->first_name,        
                ];
            }
            return $agents;
        }
    // *************************************
    // *** cost centers ***************************
        public static function cost_center($users) {
            $account_cost = \App\Account::where("cost_center",1)->get();
            $cost_center = [];
            foreach($account_cost as $i){
                $cost_center[] = [
                    "id"   => $i->id,        
                    "name" => $i->name . " || " . $i->account_number,        
                ];
            }
            return $cost_center;
        }
    // *************************************
    // *** warehouses  ***************************
        public static function warehouse($users) {
            $stores = [];
            $business_id      = $users->business_id;
            $mainStore =\App\Models\Warehouse::where('business_id', $business_id)->select(['name','status','id'])->get();
            if (!empty($mainStore)) {
                foreach ($mainStore as $mainStor) {
                    if($mainStor->status == 1){
                        $stores[] = [
                            "id"   => $mainStor->id,        
                            "name" => $mainStor->name,        
                        ];
                    }
                }
            }
            return $stores;
        }
    // *************************************
    // *** patterns  *****************************
        public static function patterns($users) {
            $patterns  = [];
            $pat       = \App\Models\Pattern::select()->get();
            foreach($pat as $it){
                    $patterns[] = [
                        "id"   => $it->id,        
                        "name" => $it->name,        
                    ];
            }
            return $patterns;
        }
    // *************************************
    // *** contacts  *****************************
        public static function contacts($user) {
            $business_id   = $user->business_id;
            $contacts      = [];
            $cont          = \App\Contact::where('business_id', $business_id)->get();
            foreach($cont as $it){
                $contacts[] = [
                    "id"   => $it->id,        
                    "name" => $it->name . " _ " . $it->supplier_business_name ,        
                ];
            }                       
            return $contacts;
        }
    // *************************************
    // *** accounts  *****************************
        public static function items($user) {
            $type        = null;
            $business_id = $user->business_id;
            $all_account = \App\Account::where('business_id',$business_id)
                                    ->OrderBy('id','desc')
                                    ->where('cost_center',0) 
                                    ->where(function($query) use($type){
                                        if ($type > 0) {
                                            $query->whereIn('account_type_id',[3,4]);
                                        }
                                    })->get();
            $accounts    = [];
            foreach ($all_account as $act) {
                $accounts[] = [
                    "id"   => $act->id,        
                    "name" => $act->name . " || " . $act->account_number ,        
                ];
            }
            return $accounts;
        }
    // *************************************
    // *** roles  *****************************
        public static function roles($user)
        {
            $business_id = $user->business_id;
            $roles_array = Role::where('business_id', $business_id)->get()->pluck('name', 'id');
            $roles       = [];
            // $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
            foreach ($roles_array as $key => $value) {
                // if ($value == 'Admin#' . $business_id) {
                //     continue;
                // }
                $roles[] = [
                    "id"    => $key,
                    "name"  => str_replace('#' . $business_id, '', $value)
                ];
            }
            return $roles;
        }
    // *************************************
    // *** gender  *****************************
        public static function gender($user)
        {
            $business_id = $user->business_id;
            $gender [] = [ "id"   => "male" ,
             "name" => "male" ];
            $gender [] = [ "id"   => "female" ,
             "name" => "female" ];
            return $gender;
        }
    // *************************************
    // *** marital status  *****************************
        public static function marital($user)
        {
            $business_id = $user->business_id;
            $list [] = [ "id"   => "married" ,
             "name" => "Married" ];
            $list [] = [ "id"   => "unmarried" ,
             "name" => "Unmarried" ];
            $list [] = [ "id"   => "divorced" ,
             "name" => "Divorced" ];
            return $list;
        }
    // *************************************
    // *** product price  *****************************
        public static function pPrice($user)
        {
            $business_id = $user->business_id;
            $list [] = [ "id"   => "0" ,
             "name" => "Exclude Tax" ];
            $list [] = [ "id"   => "1" ,
             "name" => "Include Tax" ];
            return $list;
        }
    // *************************************
    // *** taxes  *****************************
        public static function taxes($user)
        {
            $business_id = $user->business_id;
            $ta    = \App\TaxRate::where("business_id",$business_id)->get();
            $taxes = [];
            foreach($ta as $i){
                $taxes[] = [
                    "id" => $i->id,
                    "name" => $i->name,
                ];
            }
            return $taxes;
        }
    // *************************************
    // *** user  *****************************
        public static function user($user,$id,$type=null)
        {
            $business_id    = $user->business_id; $listLocation = [];
            $user           = \App\User::where("business_id",$business_id)->where("id",$id)->with(['contactAccess'])->first();
            $locations      = \App\BusinessLocation::where("business_id",$business_id)->get();
            $contact_access = $user->contactAccess->pluck('id')->toArray();

            $locAll      = 0;
            foreach ($locations as $key => $value) {
                $listLocation[$value->id]  = $value->name;
            }
            $permitted_locations                = $user->permitted_locations($user->business_id);
            if($permitted_locations == "all"){
                $locAll      = 1;
            }
            $pattern_list  = [];
            if($user->pattern_id != null){
                if(is_array(json_decode($user->pattern_id))){
                    foreach(json_decode($user->pattern_id) as $k => $ie){
                        $pattern_list[$k] = intVal($ie);
                    }
                    
                }else{
                    $pattern_list[] = json_decode($user->pattern_id);
                }
            }
            $users     = [

                "id"                            => $user->id,
                // ..1.....................................................
                "user_type"                     => $user->user_type,
                "prefix"                        => $user->surname,
                "firstName"                     => $user->first_name,
                "lastName"                      => $user->last_name,
                "email"                         => $user->email,
                "username"                      => $user->username,
                "password"                      => "",
                "confirmPassword"               => "",
                // ..2.....................................................
                "Location"                      => ($type==null)?$user->business_id:$user->location($user->business_id),
                // ..3.....................................................
                "mobileNumber"                  => $user->contact_number,
                "alternativeMobileNumber"       => $user->alt_number,
                "familyContactNumber"           => $user->family_number,
                // ..4.....................................................
                "facebookLink"                  => $user->fb_link,
                "twitterLink"                   => $user->twitter_link,
                "socialMedia1"                  => $user->social_media_1,
                "socialMedia2"                  => $user->social_media_2,
                "customField1"                  => $user->custom_field_1,
                "customField2"                  => $user->custom_field_2,
                "customField3"                  => $user->custom_field_3,
                "customField4"                  => $user->custom_field_4,
                // ..5.....................................................
                "guardianName"                  => $user->guardian_name,
                "idProofName"                   => $user->id_proof_name,
                "idProofNumber"                 => $user->id_proof_number,
                "permanentAddress"              => $user->permanent_address,
                "currentAddress"                => $user->current_address,
                // ..6.....................................................
                "holderName"                    => (json_decode($user->bank_details))?json_decode($user->bank_details)->account_holder_name:null,
                "accountNumber"                 => (json_decode($user->bank_details))?json_decode($user->bank_details)->account_number:null,
                "bankName"                      => (json_decode($user->bank_details))?json_decode($user->bank_details)->bank_name:null,
                "bankIdentifierCode"            => (json_decode($user->bank_details))?json_decode($user->bank_details)->bank_code:null,
                "bankBranchName"                => (json_decode($user->bank_details))?json_decode($user->bank_details)->branch:null,
                "taxPayerId"                    => (json_decode($user->bank_details))?json_decode($user->bank_details)->tax_payer_id:null,
                // ..7....................................................
                "department"                    => $user->essentials_department_id,
                "designation"                   => $user->essentials_designation_id,
                // ..8....................................................
                "salesCommission"               => $user->cmmsn_percent,
                "maxSalesDiscount"              => $user->max_sales_discount_percent,
                // ..9....................................................
                "language"                      => $user->language,
                "isActive"                      => ($user->status == "active")?true:false,
                "allowlogin"                    => ($user->allow_login == 1)?true:false,
                // ..10...................................................
                "dateOfBirth"                   => $user->dob,
                "bloodGroup"                    => $user->blood_group,
                "gender"                        => $user->gender,
                // ..11...................................................
                "patternId"                     => $pattern_list,
                "accounts"                      => $user->user_account_id,
                "visa"                          => $user->user_visa_account_id,
                "agents"                        => $user->user_agent_id,
                "costCenter"                    => $user->user_cost_center_id,
                "userPattern"                   => $user->user_pattern_id,
                "warehouse"                     => $user->user_store_id,
                "taxesItem"                     => $user->tax_id,
                "ProductPriceItem"              => $user->include."",
                "roles"                         => ($user->roles->first())?$user->roles->first()->id:NULL,
                // ..12...................................................
                "allowSlctdContacts"            => $user->selected_contacts,
                "selectedContact"               => $contact_access,
                // ..13...................................................
                "gender"                        => $user->gender,
                "marital"                       => $user->marital_status,
                "allLocations"                  => ($locAll!=0)?1:0,
            ];
            $lList = [];
            if($permitted_locations != "all"){
                foreach($permitted_locations as $key => $value){
                    // $lList[] = [
                    //         "id"     => $value ,
                    //         "name"   => $listLocation[$value] ,
                    //         "value"  => true 
                    // ];       
                    $lList[ ] = $value;
                }
                $users["locations"]   = $lList;       
            }else{
                foreach($listLocation as  $key => $value){
                    $lList[]   = $key ;
                            
                    // $lList[]   =  [
                    //         "id"   => $key ,
                    //         "name" => $value,
                    //         "value"=> true,
                    // ];       
                }
                $users["locations"]   = $lList;
            } 
            return $users;
        }
    // *************************************
    // *** business location  *****************************
        public static function locations($user)
        {
            $business_id = $user->business_id;
            $location    = \App\BusinessLocation::where("business_id",$business_id)->get();
            $business    = [];
            foreach($location as $i){
                $business[]     = [
                    "id"              => $i->id,
                    "name"            => $i->name,
                ];
            }
            return $business;
        }
    // *************************************
    // *** permission business  ***************************
        public static function permissionLocations($user,$request)
        {
            try{
                $permitted_locations = $user->permitted_locations($user->business_id);
                $permissions         = $request->input('access_all_locations');
                $revoked_permissions = [];
                //If not access all location then revoke permission
                if ($permitted_locations == 'all' && $permissions != 'access_all_locations') {
                    $user->revokePermissionTo('access_all_locations');
                }
                $array = [];
                //Include location permissions
                $location_permissions = $request->input('location_permissions');
                foreach(json_decode($request->input('location_permissions')) as $key => $i){
                    $array [$key] = "location.".$i;
                }
                $location_permissions = $array;
                if (empty($permissions) && !empty($location_permissions)) {
                    
                    $permissions = [];
                    foreach ($location_permissions as $location_permission) {
                        $permissions[] = $location_permission;
                    }
                    if (is_array($permitted_locations)) {
                        foreach ($permitted_locations as $key => $value) {
                            if (!in_array('location.' . $value, $permissions)) {
                                $revoked_permissions[] = 'location.' . $value;
                            }
                        }
                    }
                }
                    
                if (!empty($revoked_permissions)) {
                    $user->revokePermissionTo($revoked_permissions);
                }
                if (!empty($permissions)) {
                    $user->givePermissionTo($permissions);
                } else {
                    //if no location permission given revoke previous permissions
                    if (!empty($permitted_locations)) {
                        $revoked_permissions = [];
                        foreach ($permitted_locations as $key => $value) {
                            $revoke_permissions[] = 'location.' . $value;
                        }
                        $user->revokePermissionTo($revoke_permissions);
                    }
                }
                return true;
            }catch(Exception $e){
                return false;
            }
        }
    // *************************************

}
