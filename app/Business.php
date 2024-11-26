<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business';
    protected $appends = ['ico_url','share_url'];
    
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id', 'woocommerce_api_settings'];

    /**
      * The attributes that should be hidden for arrays.
      *
      * @var array
      */
    protected $hidden = ['woocommerce_api_settings'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ref_no_prefixes' => 'array',
        'enabled_modules' => 'array',
        'email_settings' => 'array',
        'sms_settings' => 'array',
        'common_settings' => 'array',
        'weighing_scale_setting' => 'array'
    ];

    /**
     * Returns the date formats
     */
    public static function date_formats()
    {
        return [
            'd-m-Y' => 'dd-mm-yyyy',
            'm-d-Y' => 'mm-dd-yyyy',
            'd/m/Y' => 'dd/mm/yyyy',
            'm/d/Y' => 'mm/dd/yyyy'
        ];
    }

    public function getIcoUrlAttribute(){
        $ico_url = "";
        if(!empty($this->ico)){
            $ico_url = asset("public/uploads/img/" . rawurlencode($this->ico));
        }
        return $ico_url;
    }
    public function getShareUrlAttribute(){
        $share_url = "";
        if(!empty($this->share)){
            $share_url = asset("public/uploads/img/" . rawurlencode($this->share));
        }
        return $share_url;
    }
  

    /**
     * Get the owner details
     */
    public function owner()
    {
        return $this->hasOne(\App\User::class, 'id', 'owner_id');
    }

    /**
     * Get the Business currency.
     */
    public function currency()
    {
        return $this->belongsTo(\App\Currency::class);
    }

    /**
     * Get the Business currency.
     * this business has many location linke between business_id
     *
     *
     */
    public function locations()
    {
        return $this->hasMany(\App\BusinessLocation::class);
    }

    /**
     * Get the Business printers.
     */
    public function printers()
    {
        return $this->hasMany(\App\Printer::class);
    }

    /**
    * Get the Business subscriptions.
    */
    public function subscriptions()
    {
        return $this->hasMany('\Modules\Superadmin\Entities\Subscription');
    }
    

    /**
     * Creates a new business based on the input provided.
     *
     * @return object
     */
    public static function create_business($details)
    {
        $business = Business::create($details);
        return $business;
    }

    /**
     * Updates a business based on the input provided.
     * @param int $business_id
     * @param array $details
     *
     * @return object
     */
    public static function update_business($business_id, $details)
    {
        if (!empty($details)) {
            Business::where('id', $business_id)
                ->update($details);
        }
    }

    public function getBusinessAddressAttribute() 
    {
        $location = $this->locations->first();
        $address = $location->landmark . ', ' .$location->city . 
        ', ' . $location->state . '<br>' . $location->country . ', ' . $location->zip_code;

        return $address;
    }
    public static function changeColor($data,$client,$business_id) {
        try{
            $business                 =  \App\Business::find($business_id);
            if(isset($data["color"])){
                $business->web_color =  $data["color"] ;
            }
            if(isset($data["font_color"])){
                $business->web_font_color =  $data["font_color"] ;
            }
            if(isset($data["second_color"])){
                $business->web_second_color =  $data["second_color"] ;
            }
            $business->update();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    public static function getColor($data) {
        try{
            // $business            =  \App\Business::find($client->contact->business_id);
            $business                  =  \App\Business::first();
            $color["color"]            =  $business->web_color ;
            $color["font_color"]       =  $business->web_font_color ;
            $color["second_color"]     =  $business->web_second_color ;
            return $color;
        }catch(Exception $e){
            return false;
        }
    }
    public static function changeLogo($data,$client,$business_id,$request) {
        try{
            $business                 =  \App\Business::find($business_id);
            if ($request->hasFile('logo')) {
                $file                 = $request->file('logo');
                $file_name            = 'public/uploads/logo/'.time().".".$file->getClientOriginalExtension();
                $file->move('public/uploads/logo',$file_name);
                $business->web_logo   =  $file_name;
            }
            $business->update();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    public static function getLogo($data) {
        try{
            $business            =  \App\Business::first();
            if($business->web_logo != null || $business->web_logo != "" ){
                $logo                =  \URL::to($business->web_logo) ;
            }else{
                $logo                =  "nan";
            }
            return $logo;
        }catch(Exception $e){
            return false;
        }
    }
    public static function changeNavAlign($data,$client,$business_id) {
        try{
            $business                 =  \App\Business::find($business_id);
            if (isset($data['align'])) {
                $business->navigation = $data['align'];
                $business->update();
            }
      
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    public static function getNavAlign($data) {
        try{
            $business            =  \App\Business::first();
            if($business->navigation != null || $business->navigation != "" ){
                $align                =   $business->navigation  ;
            }else{
                $align                =  "nan";
            }
            
            return $align;
        }catch(Exception $e){
            return false;
        }
    }
    public static function changeFloatAlign($data,$client,$business_id) {
        try{
            $business                 =  \App\Business::find($business_id);
            if(isset($data["align"])){
                $business->floating   = $data["align"];
                $business->update();
            }
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    public static function getFloatAlign($data) {
        try{
            $business            =  \App\Business::first();
            if($business->floating != null || $business->floating != "" ){
                $align                =   $business->floating ;
            }else{
                $align                =  "nan";
            }
            return $align;
        }catch(Exception $e){
            return false;
        }
    }
    
}
