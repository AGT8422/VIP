<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPaymentItem extends Model
{
    use HasFactory;
    public function daily_payment()
    {
        return $this->belongsTo('\App\Models\DailyPayment','daily_payment_id');
    }
    public function account()
    {
        return $this->belongsTo('App\Account','account_id');
    }
    public function cost_center()
    {
        return $this->belongsTo('App\Account','cost_center_id');
    }

    
}
