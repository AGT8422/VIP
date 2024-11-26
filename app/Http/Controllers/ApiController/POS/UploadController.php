<?php

namespace App\Http\Controllers\ApiController\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
USE App\Models\POS\Uploads;
class UploadController extends Controller
{
    // store database
    public function database(Request $request) {
        try{
            \DB::beginTransaction();
            $FILE = new Uploads();
            if ($request->hasFile('db')) {
                foreach ($request->file('db') as $file) {
                    $file_name =  'storage/izo_db/'.time().'_'.$file->getClientOriginalExtension();
                    $file->move('storage/izo_db',$file_name);
                    $FILE->file = $file_name;
                    $FILE->save();
                    \DB::commit();
                }
            }
            return response([
                "status" => 200,                
                "msg"    => "success",                
            ]);
        }catch(Exception $e){
            return response([
                "status" => 403,                
                "msg"    => "failed",                
            ]);
        }
    }
    // get database
    public function getDatabase(Request $request) {
        try{
            $upload = Uploads::get();
            
            return response([
                "status"  => 200,                
                "result"  =>  $upload,                
                "msg"     => "success",                
            ]);
        }catch(Exception $e){
            return response([
                "status" => 403,                
                "msg"    => "failed",                
            ]);
        }
    }
}
