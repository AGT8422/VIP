<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyPayment extends Model
{
    use HasFactory,SoftDeletes;
    public function items()
    {
        return $this->hasMany('\App\Models\DailyPaymentItem','daily_payment_id');
    }
    public function business()
    {
        return $this->belongsTo('App\Business','business_id');
    }
    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }
}
