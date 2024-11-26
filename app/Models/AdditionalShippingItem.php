<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalShippingItem extends Model
{
    use HasFactory;
    public function contact()
    {
        return $this->belongsTo('App\Contact','contact_id');

    }
    public function account()
    {
        return $this->belongsTo('App\Account','account_id');

    }
    public function additional_shipping()
    {
        return $this->belongsTo('App\Models\AdditionalShipping','additional_shipping_id');
    }
    public function cost_center()
    {
        return $this->belongsTo('App\Account','cost_center_id');
    }
    public function currency()
    {
        return $this->belongsTo('App\Currency','currency_id');
    }
}
