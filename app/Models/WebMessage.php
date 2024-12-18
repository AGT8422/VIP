<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebMessage extends Model
{
    use HasFactory,SoftDeletes;

        // SAVE MESSAGES CONTACT US
        public static function saveMessage($data,$client) {
            try{
                $rate                      = new WebMessage();
                $rate->name                = $data['name'];
                $rate->phone               = $data['phone'];
                $rate->email               = $data['email'];
                $rate->message             = $data['message'];
                $rate->client_id           = $data['client_id'];
                $rate->save();
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        
        // LIST MESSAGES
        public static function listMessages($data,$client) {
            try{
                $messages       =  WebMessage::where("client_id",$client->id)->get();
                $list        = [] ;
                foreach($messages as $i){
                    $list[] = [
                        "id"              => $i->id,
                        "name"            => $i->name,
                        "phone"           => $i->phone,
                        "email"           => $i->email,
                        "message"         => $i->message,
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
