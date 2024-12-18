<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    // ** 1 E-Commerce Create address
    public static function addressCreate($data,$client_id) {
        $address                = new AccountAddress();
        $address->title         = $data['title'];
        $address->building      = $data['building'];
        $address->street        = $data['street'];
        $address->flat          = $data['flat'];
        $address->area          = $data['area'];
        $address->city          = $data['city'];
        $address->country       = $data['country'];
        $address->address_name  = $data['address_name'];
        $address->address_type  = $data['address_type'];
        $address->client_id     = $client_id;
        $address->save();
        return  $output = [
                    "status"   => 200,
                    "messages" => __('Address Added Successfully')
        ];
    }
    // ** 2 E-Commerce Update address
    public static function addressUpdate($data,$client_id) {
        $address                = AccountAddress::find($data['id']);
        $address->title         = $data['title'];
        $address->building      = $data['building'];
        $address->street        = $data['street'];
        $address->flat          = $data['flat'];
        $address->area          = $data['area'];
        $address->city          = $data['city'];
        $address->country       = $data['country'];
        $address->address_name  = $data['address_name'];
        $address->address_type  = $data['address_type'];
        $address->client_id     = $client_id;
        $address->update();
        return  $output = [
                    "status"   => 200,
                    "messages" => __('Address Updated Successfully')
        ];
    }
    // ** 3 E-Commerce Delete address
    public static function addressDelete($data) {
        $address                =  AccountAddress::find($data["id"]);
        $address->delete();
        return  $output = [
                    "status"   => 200,
                    "messages" => __('Address Deleted Successfully')
        ];
    }
}
