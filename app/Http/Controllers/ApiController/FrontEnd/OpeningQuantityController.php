<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Products\OpeningQuantity;
use App\Models\FrontEnd\Utils\SearchProduct;
use App\Models\FrontEnd\Utils\SelectProduct;

class OpeningQuantityController extends Controller
{
    // 1 index
    public function OpeningQuantity(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"  => 401,
                    "error"   => true,
                    "message" => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // **** Filter
            $startDate     = request()->input("startDate");
            $endDate       = request()->input("endDate");
            $year          = request()->input("year");
            $month         = request()->input("month");
            $day           = request()->input("day");
            $week          = request()->input("week");

            $filter    = [
                "startDate"     => $startDate,
                "endDate"       => $endDate,
                "year"          => $year,
                "month"         => $month,
                "day"           => $day,
                "week"          => $week,
            ];
            $OpeningQuantity    = OpeningQuantity::getOpeningQuantity($user,$filter);
            if($OpeningQuantity == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Opening Quantities ",
                ],200);
            }
            return response([
                "status"             => 200,
                "error"              => false,
                "value"              => $OpeningQuantity,
                "message"            => "Opening Quantities Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }  
    }
    // 2 create
    public function OpeningQuantityCreate(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"  => 401,
                    "error"   => true,
                    "message" => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // ******************************************************START**
            $create    = OpeningQuantity::createOpeningQuantity($user,$data);
            // ********************************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Opening Quantities  ",
                ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,  
                "value"    => $create,
                "message"  => "Create Opening Quantities Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }   
    }
    // 3 Edit
    public function OpeningQuantityEdit(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"  => 401,
                    "error"   => true,
                    "message" => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // ******************************************************START**
            $edit    = OpeningQuantity::editOpeningQuantity($user,$data,$id);
            // ********************************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Opening Quantities ",
                ],200);
            }
            return response([
                "status"    => 200,
                "error"     => false,
                "value"     => $edit,
                "message"   => "Edit Opening Quantities Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }  
    }
    // 4 Store
    public function OpeningQuantityStore(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"  => 401,
                    "error"   => true,
                    "message" => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            \DB::beginTransaction();
            // *******************************************************************START**
                $data                = $request->only([ "store", "list_price", "date", "items" ]);
                $data["business_id"] = $user->business_id;
                $data["created_by"]  = $user->id;
                $save                = OpeningQuantity::storeOpeningQuantity($user,$data);
                if($save == false){
                    return response()->json([
                        "status"   => 200,
                        "error"    => true,
                        "message"  => " Failed Action ",
                    ],200);
                }
            // ********************************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Added Opening Quantities Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        } 
    }
    // 5 Update
    public function OpeningQuantityUpdate(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"  => 401,
                    "message" => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            \DB::beginTransaction(); 
            // **********************************************************START**
            $data      = $request->only([ "store", "list_price" , "date", "items" ]);
            $update    = OpeningQuantity::updateOpeningQuantity($user,$data,$id);
            if($update == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Opening Quantities ",
                ],200);
            }
            // ***********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Updated Opening Quantities Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 200,
                "error"   => true,
                "message" => "Failed Action Error 403",
            ],200);
        }
    }
    // 6 Delete
    public function OpeningQuantityDelete(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"  => 401,
                    "message" => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // *********************************START**
            $del    = OpeningQuantity::deleteOpeningQuantity($user,$id);
            // ***********************************END**
            if($del == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Opening Quantities ",
                ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Deleted Opening Quantities Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 7 View
    public function OpeningQuantityView(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"  => 401,
                    "message" => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            \DB::beginTransaction();
            // *******************************************START**
            $view             = OpeningQuantity::viewOpeningQuantity($user,$id);
            if($view == false){
                return response()->json([
                    "status"   => 200,
                    "error"    => true,
                    "message"  => " No Have Previous Opening Quantity",
                ],200);
            }
            // *********************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "error"    => false,
                "value"    => $view,
                "message"  => "View Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 8 Search Product 
    public function OpeningQuantityProduct(Request $request){
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"  => 401,
                    "message" => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $data       = $request->only(["value"]);
            // **********************************************START**
            $product    = SearchProduct::openQuantity($user,$data);
            // ************************************************END**
            if($product=="[]"){
                 return response()->json([
                     "status"   => 200,
                     "error"    => true,
                     "message"  => __("Not Found  !!"),
                 ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "info"     => json_decode($product),
                "message" => "Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 9 Last Product 
    public function OpeningQuantityLastProduct(Request $request){
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $data       = [];
            // **********************************************START**
            $product    = OpeningQuantity::lastProduct($user,$data);
            // ************************************************END**
            if($product=="[]"){
                 return response()->json([
                     "status"   => 200,
                     "error"    => true,
                     "message"  => __("Not Found  !!"),
                 ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "info"     => json_decode($product),
                "message" => "Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 10 Select Product 
    public function OpeningQuantitySelectProduct(Request $request){
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $data       = $request->only(["id"]);
            // **********************************************START**
            $product    = SelectProduct::openQuantity($data["id"]);
            // ************************************************END**
            if($product=="[]"){
                 return response()->json([
                     "status"   => 200,
                     "error"    => true,
                     "message"  => __("Not Found  !!"),
                 ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "info"     => $product,
                "message"  => "Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 11 export Opening Stock
    public function exportFile(Request $request) {
          try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $data       = [];
            // ******************************************************START**
            $product    = OpeningQuantity::openQuantityExport($user,$data,$request);
            // ********************************************************END**
            if($product=="[]"){
                 return response()->json([
                     "status"   => 200,
                     "error"    => true,
                     "message"  => __("Not Found  !!"),
                 ],200);
            }else{
                redirect($product);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "info"     => $product ,
                "message"  => "Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
    // 12 iMPORT Opening Stock
    public function importFile(Request $request) {
          try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"   => 401,
                    "error"    => true,
                    "message"  => "Invalid Token"
                ],401);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "error"    => true,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $data       = [];
            // **************************************************************START**
            $product    = OpeningQuantity::openQuantityImport($user,$data,$request);
            // ****************************************************************END**
            if($product==false){
                 return response()->json([
                     "status"   => 200,
                     "error"    => true,
                     "message"  => __("Not Found  !!"),
                 ],200);
            }
            return response([
                "status"   => 200,
                "error"    => false,
                "message"  => "Import Opening Stock Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"   => 200,
                "error"    => true,
                "message"  => "Failed Action Error 403",
            ],200);
        }
    }
}
