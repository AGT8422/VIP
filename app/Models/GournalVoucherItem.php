<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GournalVoucherItem extends Model
{
    use HasFactory;
    public function gournal_voucher()
    {
        return $this->belongsTo('App\Models\GournalVoucher','gournal_voucher_id');
    }
    public function credit_account()
    {
        return $this->belongsTo('App\Account','credit_account_id');
    }
    public function debit_account()
    {
        return $this->belongsTo('App\Account','debit_account_id');
    }
    public function tax_account()
    {
        return $this->belongsTo('App\Account','tax_account_id');
    }
    public function cost_center()
    {
        return $this->belongsTo('App\Account','cost_center_id');
    }
    
}
