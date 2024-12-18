<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\Util;
use App\Media;


class ArchiveTransaction extends Model
{
    use HasFactory;

    //  .......... IF Have Any  Parent Action  .............  \\ 
    //       This Take The warranty As Source Variable        \\ 
    //  ****************************************************  \\ 
    public static function have_parent($source)
    { 
        $parent = \App\Warranty::find($source->parent_id);
        if(!empty($parent)){
            $result = $parent->id ;
        }else{
            $result = null ;
        }
        return $result;
    }


    //  ......... Save Any Action On Bill Edit   ...........  \\ 
    //         This Take The Bill As Source Variable          \\ 
    //  ****************************************************  \\ 
    public static function save_parent($source,$action_type)
    { 
        $parent = \App\Transaction::find($source->id);
        $archive_parent  = \App\Models\ArchiveTransaction::orderBy("id","desc")->where("new_id",$source->id)->first();
        if(!empty($parent)){
            $type                      = 'log_file';
            $ref_count                 = Util::setAndGetReferenceCount($type,$parent->business_id);
            $reciept_no                = Util::generateReferenceNumber($type, $ref_count,$parent->business_id,"LOG_");
            $item                      =  new ArchiveTransaction;
            $item->new_id              = $parent->id;  
            $item->store               = $parent->store;  
            $item->business_id         = $parent->business_id;  
            $item->location_id         = $parent->location_id;  
            $item->type                = $parent->type;  
            $item->sub_type            = $parent->sub_type;  
            $item->status              = $parent->status;  
            $item->sub_status          = $parent->sub_status;  
            $item->is_quotation        = $parent->is_quotation;  
            $item->payment_status      = $parent->payment_status;  
            $item->contact_id          = $parent->contact_id;  
            $item->customer_group_id   = $parent->customer_group_id;  
            $item->invoice_no          = $parent->invoice_no;  
            $item->ref_no              = $parent->ref_no;  
            $item->subscription_no     = $parent->subscription_no;  
            $item->subscription_repeat_on = $parent->subscription_repeat_on;  
            $item->transaction_date    = $parent->transaction_date;  
            $item->total_before_tax    = $parent->total_before_tax;  
            $item->tax_id              = $parent->tax_id;  
            $item->tax_amount          = $parent->tax_amount;  
            $item->discount_type       = $parent->discount_type;  
            $item->discount_amount     = $parent->discount_amount;  
            $item->shipping_details    = $parent->shipping_details;  
            $item->delivered_to        = $parent->delivered_to;  
            $item->additional_notes    = $parent->additional_notes;  
            $item->final_total         = $parent->final_total;
            $document_sell = [];
            if ($parent->document) {
                foreach ($parent->document as $file) {
                    array_push($document_sell,$file);
                }
            }
            $item->document            = json_encode($document_sell);  
            $item->is_direct_sale      = $parent->is_direct_sale;  
            $item->is_suspend          = $parent->is_suspend;  
            $item->exchange_rate       = $parent->exchange_rate;  
            $item->transfer_parent_id  = $parent->transfer_parent_id;  
            $item->return_parent_id    = $parent->return_parent_id;  
            $item->opening_stock_product_id = $parent->opening_stock_product_id;  
            $item->created_by          = $parent->created_by;  
            $item->mfg_parent_production_purchase_id = $parent->mfg_parent_production_purchase_id;  
            $item->mfg_wasted_units    = $parent->mfg_wasted_units;  
            $item->mfg_production_cost = $parent->mfg_production_cost;  
            $item->mfg_production_cost_type = $parent->mfg_production_cost_type;  
            $item->mfg_is_final        = $parent->mfg_is_final;  
            $item->refe_no = $parent->refe_no;  
            $item->due_state	       = $parent->due_state;  
            $item->project_no          = $parent->project_no;  
            $item->store_in            = $parent->store_in;  
            $item->dis_type            = $parent->dis_type;  
            $item->agent_id            = $parent->agent_id;  
            $item->sup_refe            = $parent->sup_refe;  
            $item->first_ref_no        = $parent->first_ref_no;  
            $item->previous            = $parent->previous;  
            $item->ship_amount         = $parent->ship_amount;  
            $item->cost_center_id      = $parent->cost_center_id;  
            $item->pattern_id          = $parent->pattern_id; 
            $item->ref_number          = "TRANS_" . $reciept_no; 
            $item->state_action        = ($action_type=="create")?"Add":"Edit"; 
            $item->parent_id           = (!empty($archive_parent))?$archive_parent->id:null ; 
            $item->save(); 
            return $item;
        }else{
            return [];
        }
        
    }


    //  .........  User Connection   ...........  \\ 
    //  *****************************************  \\ 
    public function sales_person()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
    //  .........  Store Connection   ...........  \\ 
    //  *****************************************  \\ 
    public function storex()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'store');
    }
    //  .........  Store Connection   ...........  \\ 
    //  *****************************************  \\ 
    public function warehouse()
    {
        return $this->belongsTo(\App\Models\Warehouse::class, 'store');
    }

    //  .........  location Connection   ...........  \\ 
    //  ********************************************  \\ 
    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }
    //  .........  Cost Center Connection   ...........  \\ 
    //  ***********************************************  \\ 
    public function cost_center()
    {
        return $this->belongsTo(\App\Account::class,'cost_center_id');
    }
    //  .........  Pattern Connection   ...........  \\ 
    //  *******************************************  \\ 
    public function pattern()
    {
        return $this->belongsTo(\App\Models\Pattern::class,'pattern_id');
    }
    //  .........  Contact Connection   ...........  \\ 
    //  *******************************************  \\ 
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class, 'contact_id');
    }
    //  .........  Purchase lines Connection   ...........  \\ 
    //  **************************************************  \\ 
    public function purchase_lines()
    {
        return $this->hasMany(\App\Models\ArchivePurchaseLine::class , "transaction_id" );
    }
    //  .........  Payment lines Connection   ...........  \\ 
    //  *************************************************  \\ 
    public function payment_lines()
    {
        return $this->hasMany(\App\TransactionPayment::class, 'transaction_id');
    }
    //  .........  Tax   Connection   ...........  \\ 
    //  *****************************************  \\ 
    public function tax()
    {
        return $this->belongsTo(\App\TaxRate::class, 'tax_id');
    }
    //  .........  Business Connection   ...........  \\ 
    //  ********************************************  \\ 
    public function business()
    {
        return $this->belongsTo(\App\Business::class, 'business_id');
    }
    //  .........  Sells Line Connection   ...........  \\ 
    //  **********************************************  \\ 
    public function sell_lines()
    {
         return $this->hasMany(\App\Models\ArchiveTransactionSellLine::class , "transaction_id");
    }
    //  .........  Table Connection   ...........  \\ 
    //  *****************************************  \\ 
    public function table()
    {
        return $this->belongsTo(\App\Restaurant\ResTable::class, 'res_table_id');
    }
    //  ......... Service Staff Connection ..........  \\ 
    //  *********************************************  \\ 
    public function service_staff()
    {
        return $this->belongsTo(\App\User::class, 'res_waiter_id');
    }
    //  ......... Types Of Service Connection ..........  \\ 
    //  ************************************************  \\ 
    public function types_of_service()
    {
        return $this->belongsTo(\App\TypesOfService::class, 'types_of_service_id');
    }
    //  .........      Media   Connection     ..........  \\ 
    //  ************************************************  \\ 
    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }
   
    //  .........      document   Connection ..........  \\ 
    //  ************************************************  \\ 
    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }
    
    /**
     * Retrieves documents path if exists
     */
    public function getDocumentPathAttribute()
    {
        $path = !empty($this->document) ? asset('public/uploads/documents/' . $this->document) : null;
        
        return $path;
    }
    /**
     * Removes timestamp from document name
     */
    public function getDocumentNameAttribute()
    {
        $document_name = !empty(explode("_", $this->document, 2)[1]) ? explode("_", $this->document, 2)[1] : $this->document ;
        return $document_name;
    }
  
     
   
}
