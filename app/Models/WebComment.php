<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebComment extends Model
{
    use HasFactory,SoftDeletes;

        // SAVE COMMENT's    
        public static function saveComment($data,$client) {
            try{
                $rate                      = new WebComment();
                $rate->number_of_stars     = $data['number_of_stars'];
                $rate->message             = $data['message'];
                $rate->message_parent_id   = $data['message_parent_id'];
                $rate->reply               = $data['reply'];
                $rate->reply_parent_id     = $data['reply_parent_id'];
                $rate->liked_emoji         = $data['liked_emoji'];
                $rate->client_id           = $data['client_id'];
                $rate->save();
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        
        // LIST COMMENT's
        public static function listComment($data,$client) {
            try{
                $comments       =  WebComment::where("client_id",$client->id)->get();
                $list           = [] ;
                foreach($comments as $i){
                    $list[] = [
                        "id"                => $i->id,
                        "number_of_stars"   => $i->number_of_stars,
                        "message"           => $i->message,
                        "message_parent_id" => $i->message_parent_id,
                        "reply"             => $i->reply,
                        "reply_parent_id"   => $i->reply_parent_id,
                        "liked_emoji"       => $i->liked_emoji,
                        "client"            => $i->client->first_name
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
