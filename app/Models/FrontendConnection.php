<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrontendConnection extends Model
{
    use HasFactory;

    public static function saveApi($data){
        $api            =  $data->api_url;
        $front          = \App\Models\FrontendConnection::first();
        if(!empty($front)){
            $front->api = $api;
            $front->update();
        }else{
            $ft         = new  \App\Models\FrontendConnection();
            $ft->api    = $api;
            $ft->save();
        }
    }
}
