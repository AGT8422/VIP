<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountSetting extends Model
{
    use HasFactory;
    use SoftDeletes;

    // **  save account setting information
    public static function SaveSetting($data){
        $accountSetting                          = new \App\Models\AccountSetting();
        $accountSetting->pattern_id              = $data["pattern_id"]; 
        $accountSetting->purchase                = $data["purchase"]; 
        $accountSetting->purchase_tax            = $data["purchase_tax"]; 
        $accountSetting->purchase_discount       = $data["purchase_discount"]; 
        $accountSetting->purchase_return         = $data["purchase_return"]; 
        $accountSetting->sale                    = $data["sale"]; 
        $accountSetting->sale_tax                = $data["sale_tax"]; 
        $accountSetting->sale_discount           = $data["sale_discount"]; 
        $accountSetting->sale_return             = $data["sale_return"]; 
        $accountSetting->client_account_id       = $data["client_account_id"]; 
        $accountSetting->client_visa_account_id  = $data["client_visa_account_id"]; 
        $accountSetting->client_store_id         = $data["client_store_id"]; 
        $accountSetting->tax_id                  = $data["tax_id"]; 
        $accountSetting->save(); 
    }

    // **  update account setting information
    public static function UpdateSetting($data){
        $accountSetting                          = \App\Models\AccountSetting::first();
        $accountSetting->pattern_id              = $data["pattern_id"]; 
        $accountSetting->purchase                = $data["purchase"]; 
        $accountSetting->purchase_tax            = $data["purchase_tax"]; 
        $accountSetting->purchase_discount       = $data["purchase_discount"]; 
        $accountSetting->purchase_return         = $data["purchase_return"]; 
        $accountSetting->sale                    = $data["sale"]; 
        $accountSetting->sale_tax                = $data["sale_tax"]; 
        $accountSetting->sale_discount           = $data["sale_discount"]; 
        $accountSetting->sale_return             = $data["sale_return"];
        $accountSetting->client_account_id       = $data["client_account_id"]; 
        $accountSetting->client_visa_account_id  = $data["client_visa_account_id"]; 
        $accountSetting->client_store_id         = $data["client_store_id"]; 
        $accountSetting->tax_id                  = $data["tax_id"]; 
        $accountSetting->update(); 

    }

    // **  all account setting information
    public static function allData(){
        $accountSetting    = \App\Models\AccountSetting::first();
        return $accountSetting;

    }
}
