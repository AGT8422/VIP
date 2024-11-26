<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Utils\Util;

class ChildArchive extends Model
{

    public function getDocumentAttribute()
    {
        return json_decode($this->attributes['document']);
    }
    use HasFactory;
    public static function ship_save_child($id,$action_type,$parent=null)
    {

        $old_ship_child    = \App\Models\AdditionalShippingItem::find($id); 
        $transactions_olds = \App\Transaction::find($old_ship_child->additional_shipping->transaction_id); 
        $business_id       = (!empty($transactions_olds))?$transactions_olds->business_id:request()->session()->get("user.business_id");
        $archive           = \App\Models\ChildArchive::orderBy("id","desc")->where("additional_shipping_id",$id)->first();
        if($old_ship_child){
            $type                                    = 'log_file';
            $ref_count                               = Util::setAndGetReferenceCount($type,$business_id);
            $reciept_no                              = Util::generateReferenceNumber($type, $ref_count,$old_ship_child->business_id,"LOG_");
            $new_ship_child                          = new ChildArchive;
            $new_ship_child->business_id             = $old_ship_child->additional_shipping->business_id ;
            $new_ship_child->additional_shipping_id  = ($parent)?$parent->id:null;
            $new_ship_child->contact_id              = $old_ship_child->contact_id ;
            $new_ship_child->account_id              = $old_ship_child->account_id ;
            $new_ship_child->amount                  = $old_ship_child->amount ;
            $new_ship_child->vat                     = $old_ship_child->vat ;
            $new_ship_child->total                   = $old_ship_child->total ;
            $new_ship_child->text                    = $old_ship_child->text ;
            $new_ship_child->date                    = $old_ship_child->date ;
            $new_ship_child->cost_center_id          = $old_ship_child->cost_center_id ;
            $new_ship_child->line_id                 = $old_ship_child->id; //.......
            $new_ship_child->updated_at              = $old_ship_child->updated_at ;
            $new_ship_child->created_at              = $old_ship_child->created_at ;
            $new_ship_child->ref_number              = "SHIP_" . $reciept_no; 
            $new_ship_child->state_action            = ($action_type=="create")?"Add":"Edit"; 
            $new_ship_child->log_parent_id           = ($archive)?$archive->id:null ; //.......
            $new_ship_child->save() ;
            
            return $new_ship_child;
        }else{
            return null;
            
        }

    }
   
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
        return $this->belongsTo('App\Models\ParentArchive','additional_shipping_id');
    }
    public function cost_center()
    {
        return $this->belongsTo('App\Account','cost_center_id');
    }
}
