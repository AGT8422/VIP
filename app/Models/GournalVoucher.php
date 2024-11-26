<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GournalVoucher extends Model
{
    use HasFactory;
    public function items()
    {
        return $this->hasMany('App\Models\GournalVoucherItem','gournal_voucher_id');
    }
    public function business()
    {
        return $this->belongsTo('App\Business','business_id');
    }
    public function account()
    {
        return $this->belongsTo('App\Account','main_account_id');
    }
    public function cost_center()
    {
        return $this->belongsTo('App\Account','cost_center_id');
    }
    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }
   
}
