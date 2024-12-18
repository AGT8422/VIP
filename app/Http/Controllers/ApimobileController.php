<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
class ApimobileController extends Controller
{
    
    // ......... list of mobile app / companies..... \\ 
    // .********************************************. \\ 
    public function getApiList(){


        if(!auth()->user()->can("business.create")){
            abort(403, 'Unauthorized action.');
        }
        
        $business_id                = request()->session()->get("user.business_id");
        $business_locations         = \App\BusinessLocation::allLocation($business_id);
        $name = [] ;
        $surename = [] ;
        $username = [] ;
        $device_id = [] ;
        $device_ip = [] ;
        $MobileApp = \App\Models\MobileApp::get();
        foreach($MobileApp as $i){
            $name[$i->name]           = $i->name ;
            $surename[$i->surename]   = $i->surename ;
            $username[$i->username]   = $i->username ;
            $device_id[$i->device_id] = $i->device_id ;
            $device_ip[$i->device_ip] = $i->device_ip ;
        }
        
        if(request()->ajax()){
                $MobileApp = \App\Models\MobileApp::select();
                if(!empty(request()->name)){
                    $name = request()->name;
                    $MobileApp->where("name" ,$name);
                }
                if(!empty(request()->surname)){
                    $surname = request()->surname;
                    $MobileApp->where("surname" ,$surname);
                }
                if(!empty(request()->username)){
                    $username = request()->username;
                    $MobileApp->where("username" ,$username);
                }
                if(!empty(request()->device_id)){
                    $device_id = request()->device_id;
                    $MobileApp->where("device_id" ,$device_id);
                }
                if(!empty(request()->device_ip)){
                    $device_ip = request()->device_ip;
                    $MobileApp->where("device_ip" ,$device_ip);
                }
                
                $MobileApp->get();

                return Datatables::of($MobileApp)
                        ->removeColumn("id")
                        ->addColumn("actions",function($row){
                            $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
                            </button> 
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                            // if (auth()->user()->can("purchase.view")) {
                            //     $html .= '<li><a href="#" data-href="' . action('ApimobileController@editApi', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fas fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                            // }
                            if (auth()->user()->can("purchase.update")) {
                                $html .= '<li><a href="' . action('ApimobileController@editApi', [$row->id]) . '"><i class="fas fa-edit"></i>' . __("messages.edit") . '</a></li>';
                            }
                            if (auth()->user()->can("purchase.delete")) {
                                $html .= '<li><a href="#" data-href="' . action('ApimobileController@delete', [$row->id]) . '" class="delete-mobile"><i class="fas fa-trash"></i>' . __("messages.delete") . '</a></li>';
                            }
                            $html .=  '</ul></div>';
                            return $html;
                        })
                        ->editColumn("name",function($row){
                            return $row->name;
                        })
                        ->editColumn("surename",function($row){
                            return $row->surname;
                        })
                        ->editColumn("username",function($row){
                            return $row->username;
                        })
                        ->editColumn("device_id",function($row){
                            return $row->device_id;
                        })
                        ->editColumn("device_ip",function($row){
                            return $row->device_ip;
                        })
                        ->editColumn("lastlogin",function($row){
                                $html  = "<span class='user-password-check' id='user-password-check'>";   
                                $Now   = \Carbon::parse(\Carbon::now()->format('Y-m-d'));
                                $UNTIL = \Carbon::parse(\Carbon::createFromTimestamp($row->lastlogin)->format('Y-m-d'));
                                $html .= $Now->diffInDays($UNTIL) . " Days"  ;   
                                $html .= "</span>"; 
                                $h = ($row->lastlogin!=NULL)?\Carbon::createFromTimestamp($row->lastlogin)->format('Y-m-d'):"";  
                            return  $h;  
                        })
                        ->editColumn("created_at",function($row){
                            $date = date_format( $row->created_at, "d/m/Y h:i:s");
                            return $date;
                        })
                        ->setRowAttr([
                            // "data-href"=> function ($row) { return action('PatternController@show', [$row->id]) ; }  
                        ])
                        ->rawColumns(['actions','lastlogin','device_ip','device_id','username','name'])
                        ->make(true);
        }


         
        return view('mobile_app.index')->with(compact("surename","name","username","device_id","device_ip"));
    }
    
    // ......... create   mobile app / companies..... \\ 
    // .********************************************. \\ 
    public function createApi(){
        
         
        
        return view('mobile_app.create');
    }
    
    // ......... store    mobile app / companies..... \\ 
    // .********************************************. \\ 
    public function storeApi(Request $request){
       
        try{
            \DB::beginTransaction();
            $username = \App\Models\MobileApp::where("username",trim($request->username))->first();
            $name     = \App\Models\MobileApp::where("name",trim($request->name))->first();
            if(!empty($username)){
                $output = [
                            "success"  => 0,
                            "msg"      => "Wrong This UserName is Exist in The System",
                        ];
                return  redirect("/get-api")->with("status",$output);

            }
            if(!empty($name)){
                $output = [
                            "success" => 0,
                            "msg"     => "Wrong This Name is Exist in The System",
                        ];
                return  redirect("/get-api")->with("status",$output);

            }
            $data = [];
            $data["name"]     = trim($request->name);
            $data["surname"]  = trim($request->surname);
            $data["email"]    = trim($request->email);
            $data["mobile"]   = trim($request->mobile);
            $data["username"] = trim($request->username);
            $data["password"] = $request->password;
            $data["api_url"]  = trim($request->api_url);
            \App\Models\MobileApp::createNew($data);
            \DB::commit();
            $output = [
                        "success" => 1,
                        "msg"     => "Added Successfully",
                    ];

        }catch(Exception $e){
            \DB::rollBack();
            \Log::emergency("File : " . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
              $output = [
                           "success"=>0, 
                           "msg"=>"wrong", 
                            ];
        }
        return  redirect("/get-api")->with("status",$output);
    }
    
    // ......... edit     mobile app / companies..... \\ 
    // .********************************************. \\ 
    public function editApi($id){
      
        $MobileApp = \App\Models\MobileApp::find($id);
        return view('mobile_app.edit')->with(compact("MobileApp"));
    }
    // ......... update   mobile app / companies..... \\ 
    // .********************************************. \\ 
    public function updateApi(Request $request,$id){
        try{
            \DB::beginTransaction();
            $data = [];
            $data["name"]     = trim($request->name);
            $data["surname"]  = trim($request->surname);
            $data["email"]    = trim($request->email);
            $data["mobile"]   = trim($request->mobile);
            $data["username"] = trim($request->username);
            $data["password"] = $request->password;
            $data["api_url"]  = trim($request->api_url);
            \App\Models\MobileApp::editNew($data,$id);
            \DB::commit();
            $output = [
                        "success" => 1,
                        "msg"     => "Updated Successfully",
                    ];
        }catch(Exception $e){
            \DB::rollBack();
            \Log::emergency("File : " . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            \Log::alert($e);
              $output = [
                           "success"=>0, 
                           "msg"=>"wrong", 
                            ];
        }
        return  redirect("/get-api")->with("status",$output);
        
    }
    // ......... delete  mobile app / companies..... \\ 
    // .********************************************. \\ 
    public function delete($id){
           
            try{
                \DB::beginTransaction();
                $app = \App\Models\MobileApp::find($id);
                if(!empty($app)){
                    $app->delete();
                    $output = [
                        "success" => 1,
                        "msg"     => "Added Successfully",
                    ];
                }else{
                    $output = [
                        "success" => 0,
                                "msg"=>"wrong", 
                            ];
                            
                }
                \DB::commit();
            }catch(Excption $e){
                \DB::rollBack();
                \Log::emergency("File : " . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                \Log::alert($e);
                  $output = [
                               "success"=>0, 
                               "msg"=>"wrong", 
                                ];
          
            }
             
        return $output;
    }
}
