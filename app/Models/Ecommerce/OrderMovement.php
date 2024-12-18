<?php

namespace App\Models\Ecommerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderMovement extends Model
{
    use HasFactory,SoftDeletes;

    // *** GET ALL MOVEMENTS
    public static function getMovement($data,$client) {
        try{
            $all        = OrderMovement::where("bill_id",$data["bill_id"])->get();
            $list       = [];
            foreach($all as $i){
                $list[] = [
                    "id"              => $i->id,
                    "bill_id"         => $i->bill_id,
                    "bill_state"      => $i->state,
                    "reference_no"    => $i->reference_no,
                    "total"           => round($i->total,2),
                    "payment_status"  => $i->payment_status,
                    "delivery_status" => $i->delivery_status,
                    "date"            => $i->date,
                ];
            }
            return $list;
        }catch(Exception $e){
            return false;
        }
    }
    // *** SAVE MOVEMENTS
    public static function saveMovement($data,$client) {
        try{
            $move                   = new OrderMovement();
            $move->bill_id          = $data["bill_id"] ;
            $move->state            = $data["state"] ;
            $move->reference_no     = $data["reference_no"] ;
            $move->total            = $data["total"] ;
            $move->payment_status   = $data["payment_status"] ;
            $move->delivery_status  = $data["delivery_status"] ;
            $move->date             = $data["date"] ;
            $move->created_by       = $client->id ;
            $move->type             = "Ecommerce" ;
            $move->save();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    // *** SAVE erp MOVEMENTS
    public static function saveErpMovement($data,$client) {
        try{
            $move                   = new OrderMovement();
            $move->bill_id          = $data["bill_id"] ;
            $move->state            = $data["state"] ;
            $move->reference_no     = $data["reference_no"] ;
            $move->total            = $data["total"] ;
            $move->payment_status   = $data["payment_status"] ;
            $move->delivery_status  = $data["delivery_status"] ;
            $move->date             = $data["date"] ;
            $move->created_by       = $client->id ;
            $move->type             = "Erp" ;
            $move->save();
            return true;
        }catch(Exception $e){
            return false;
        }
    }
}
