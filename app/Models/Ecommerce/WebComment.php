<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebComment extends Model
{
    use HasFactory,SoftDeletes;

        // ** 1 ** LIST COMMENT's
        public static function listComments($data,$client) {
            try{
                $comments       =  WebComment::where("product_id",$data["product_id"])->get();
                $list           = [] ;
                foreach($comments as $i){
                    if($i->client){
                        $list[] = [
                            "id"                => $i->id,
                            "number_of_stars"   => $i->number_of_stars,
                            "message"           => $i->message,
                            "message_parent_id" => $i->message_parent_id,
                            "liked_emoji"       => $i->liked_emoji,
                            "client"            => ($i->client)?$i->client->first_name:""
                        ]; 
                    }
                }
                return $list;
            }catch(Exception $e){
                return false;
            }
        }
        // ** 2 ** SAVE COMMENT's    
        public static function addComments($data,$client) {
            try{
                $rate                      = new WebComment();
                $rate->number_of_stars     = $data['number_of_stars'];
                $rate->message             = $data['message'];
                $rate->product_id          = $data['product_id'];
                $rate->client_id           = $client->id;
                $rate->save();
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        // ** 3 ** UPDATE COMMENT's    
        public static function updateComments($data,$client) {
            try{
                $rate                      = WebComment::find($data["id"]);
                if($rate->client_id != $client->id){
                    return false;
                }
                $rate->number_of_stars     = $data['number_of_stars'];
                $rate->message             = $data['message'];
                $rate->client_id           = $client->id;
                $rate->update();
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        // ** 4 ** DELETE COMMENT's    
        public static function deleteComments($data,$client) {
            try{
                $rate                      = WebComment::find($data["id"]);
                if($rate->client_id != $client->id){
                    return false;
                }
                $rate->delete();
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        // ** 5 ** REPAY COMMENT's    
        public static function replayComments($data,$client) {
            try{
                $parent                    = WebComment::find($data["id"]);
                $rate                      = new WebComment();
                $rate->number_of_stars     = $data['number_of_stars'];
                $rate->message             = $data['message'];
                $rate->message_parent_id   = $parent->id;
                $rate->product_id          = $parent->product_id;
                $rate->client_id           = $client->id;
                $rate->save();
                return true;
            }catch(Exception $e){
                return false;
            }
        }       
        // ** 6 ** emoji COMMENT's
        public static function saveEmojiComment($data,$client) {
            try{
                $comments       =  WebComment::find($data["id"]);
                switch ($data["liked_emoji"]) {
                        case 1:
                            $val = "";
                            break;
                        case 2:
                            $val = "like";
                            break;
                        case 3:
                            $val = "loved";
                            break;
                        case 4:
                            $val = 'dislike';
                            break;
                        case 5:
                            $val = "angry";
                            break;
                        default:
                            $val = "";
                    }
                if($val == $comments->liked_emoji){
                    $comments->liked_emoji = 1;
                }else{
                    $comments->liked_emoji = $val;
                }
              
               
                $comments->update();
                return true;
            }catch(Exception $e){
                return false;
            }
        }
        // ***
        public function client() {
            return $this->belongsTo( \App\Models\e_commerceClient::class , "client_id");
        }
        // ** 7 ** LIST COMMENT's
        public static function listGlobalComments($id) {
            try{
                $comments       =  WebComment::where("product_id",$id)->get();
                $list           = [] ;
                foreach($comments as $i){
                    if($i->client){
                        $list[] = [
                            "id"                => $i->id,
                            "number_of_stars"   => $i->number_of_stars,
                            "message"           => $i->message,
                            "message_parent_id" => $i->message_parent_id,
                            "liked_emoji"       => $i->liked_emoji,
                            "client"            => ($i->client)?$i->client->first_name:"",
                            "date"              => $i->created_at->format("Y-m-d h:i:s a")
                        ]; 
                    }
                }
                return $list;
            }catch(Exception $e){
                return false;
            }
        }
        // ** 8 ** rate COMMENT's
        public static function listRateComments($id) {
            try{
                $comments       =  WebComment::where("product_id",$id)->get();
                $list           = [] ;
                $old            = [] ;
                $numbers        = [] ;
                $numbers[1]     = 0;
                $numbers[2]     = 0;
                $numbers[3]     = 0;
                $numbers[4]     = 0;
                $numbers[5]     = 0;$total=0;$qty=0;
                foreach($comments as $i){
                    switch ($i->number_of_stars) {
                        case 1 : $numbers[$i->number_of_stars] = $numbers[$i->number_of_stars]+1 ;break;
                        case 2 : $numbers[$i->number_of_stars] = $numbers[$i->number_of_stars]+1 ;break;
                        case 3 : $numbers[$i->number_of_stars] = $numbers[$i->number_of_stars]+1 ;break;
                        case 4 : $numbers[$i->number_of_stars] = $numbers[$i->number_of_stars]+1 ;break;
                        case 5 : $numbers[$i->number_of_stars] = $numbers[$i->number_of_stars]+1 ;break;
                        default: break; ;
                    }
                    // if(!in_array($i->number_of_stars,$old)){
                    //     $numbers[$i->number_of_stars] =  1;
                    //     $old[]                        =  $i->number_of_stars;
                    // }else{
                    //     $numbers[$i->number_of_stars] =  $numbers[$i->number_of_stars] + 1;
                    // } 
                }
                foreach($numbers as $key => $value){
                    $total += $key * $value;
                    $qty   += $value;
                }
                $rate = ($qty!=0)?($total/$qty):0; 
                return [ 
                    "numbers" => $numbers,
                    "rate"    => $rate
                ];
            }catch(Exception $e){
                return false;
            }
        }

}
