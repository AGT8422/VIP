<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

class PaymentCard extends Model
{
    use HasFactory;
    use SoftDeletes;

    // ** 1 E-commerce  create new card
    public static function cardCreate($data,$client_id) {
        $card                     = new PaymentCard();
        $card->card_number        = encrypt($data['card_number']);
        $card->card_type          = $data['card_type'];
        $card->last_four_number   = encrypt(substr($data['card_number'],12));
        $card->card_expire        = $data['card_expire'];
        $card->card_cvv           = encrypt($data['card_cvv']);
        $card->client_id          = $client_id;
        $card->save();
        return  $output = [
                    "status"   => 200,
                    "messages" => __('Card Added Successfully')
        ];
    }

    // ** 2 E-commerce  update old card
    public static function cardUpdate($data,$client_id) {
        $card                     = PaymentCard::find($data['id']);
        if(empty($card)){
            return  $output = [
                "status"   => 403,
                "messages" => __('Invalid Data'),
                // "status"   =>  decrypt($card->card_number),
            ]; 
        }
        $card->card_number        = encrypt($data['card_number']);
        $card->card_type          = $data['card_type'];
        $card->last_four_number   = encrypt(substr($data['card_number'],12));
        $card->card_expire        = $data['card_expire'];
        $card->card_active        = $data['card_active'];
        $card->card_cvv           = encrypt($data['card_cvv']);
        $card->client_id          = $client_id;
        $card->update();
        return  $output = [
            "status"   => 200,
            "messages" => __('Card Updated Successfully'),
            // "status"   =>  decrypt($card->card_number),
        ];
    }

    // ** 3 E-commerce delete old card
    public static function cardDelete($data) {
        $card                     = PaymentCard::find($data['id']);
        if(empty($card)){
            return  $output = [
                "status"   => 403,
                "messages" => __('Invalid Data')
            ];
        }
        $card->delete();
        return  $output = [
            "status"   => 200,
            "messages" => __('Card Deleted Successfully')
        ];
    }

}
