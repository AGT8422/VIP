<?php

namespace App\Models\FrontEnd\Utils;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FrontEnd\Utils\GlobalUtil;
use App\BusinessLocation;
use App\Transaction;

class Contact extends Model
{
    use HasFactory;
    // ********************* CONTACT  //
    // TODO FOR NOTED NOT USED
    //****1** set opening balance for contact */
    public static function OpeningBalance($business_id, $contact_id, $amount, $created_by)
    {
        $business_location             = BusinessLocation::where('business_id', $business_id)->first();
        $final_amount                  = $amount;
        $data   = [
                    'business_id'      => $business_id,
                    'location_id'      => $business_location->id,
                    'type'             => 'opening_balance',
                    'status'           => 'final',
                    'payment_status'   => 'due',
                    'contact_id'       => $contact_id,
                    'transaction_date' => \Carbon::now(),
                    'total_before_tax' => $final_amount,
                    'final_total'      => $final_amount,
                    'created_by'       => $created_by
                ];
        
        $NumberOfCount                 = GlobalUtil::SetReferenceCount('opening_balance', $business_id);
        $data['ref_no']                = GlobalUtil::GenerateReferenceCount('opening_balance', $NumberOfCount, $business_id);
        Transaction::create($data);
    }
}
