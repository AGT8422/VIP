<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemAccount extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'pattern_id',
        'purchase',
        'purchase_tax',
        'sale',
        'sale_tax',
        'cheque_debit',
        'cheque_collection',
        'journal_expense_tax',
        'sale_return',
        'sale_discount',
        'purchase_return',
        'purchase_discount'
    ];
    // ** Relation's functions
    
    // *1* accounts purchase
    public function account_purchase(){
        return $this->belongsTo('\App\Account','purchase'); 
    }
    // *2* accounts purchase tax
    public function account_purchase_tax(){
        return $this->belongsTo('\App\Account','purchase_tax'); 
    }
    // *3* accounts purchase discount
    public function account_purchase_discount(){
        return $this->belongsTo('\App\Account','purchase_discount'); 
    }
    // *4* accounts purchase return
    public function account_purchase_return(){
        return $this->belongsTo('\App\Account','purchase_return'); 
    }
    // *5* accounts sale
    public function account_sale(){
        return $this->belongsTo('\App\Account','sale'); 
    }
    // *6* accounts sale tax
    public function account_sale_tax(){
        return $this->belongsTo('\App\Account','sale_tax'); 
    }
    // *7* accounts sale discount
    public function account_sale_discount(){
        return $this->belongsTo('\App\Account','sale_discount'); 
    }
    // *8* accounts sale return
    public function account_sale_return(){
        return $this->belongsTo('\App\Account','sale_return'); 
    }

    // *9* accounts journal expense tax
    public function account_journal_expense_tax(){
        return $this->belongsTo('\App\Account','journal_expense_tax'); 
    }
    // *10* accounts cheque credit
    public function account_cheque_debit(){
        return $this->belongsTo('\App\Account','cheque_debit'); 
    }
    // *11* accounts cheque collection
    public function account_cheque_collection(){
        return $this->belongsTo('\App\Account','cheque_collection'); 
    }
    
     





    // *2* pattern
    public function pattern(){
        return $this->belongsTo('\App\Models\Pattern','pattern_id'); 
    }
}
