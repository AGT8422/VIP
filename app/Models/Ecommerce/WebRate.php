<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class WebRate extends Model
{
    use HasFactory,SoftDeletes;

    // SAVE RATE WEB
    public static function saveRate($data,$client) {
        try{
            $rate                      = new WebRate();
            $rate->number_of_stars     = $data['number_of_stars'];
            $rate->comment             = $data['comment'];
            $rate->client_id           = $client->id;
            $rate->save();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    
    // LIST RATES WEB
    public static function listRate($data,$client) {
        try{
            $rates       =  WebRate::where("client_id",$client->id)->get();
            $list        = [] ;
            foreach($rates as $i){
                $list[] = [
                    "id"              => $i->id,
                    "number_of_stars" => $i->number_of_stars,
                    "comment"         => $i->comment,
                    "client"          => $i->client->first_name
                ]; 
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }

    // ***
    public function client() {
        return $this->belongsTo( \App\Models\e_commerceClient::class , "client_id");
    }

}
