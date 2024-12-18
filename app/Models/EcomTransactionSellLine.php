<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EcomTransactionSellLine extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function Ecom_transaction(){
        return $this->belongsTo("\App\Models\EcomTransaction","Ecom_transaction_id");
    }

    public function product(){
        return $this->belongsTo("\App\Product","product_id");
    } 

}
