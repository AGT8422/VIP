<?php

namespace App\Http\Controllers\ApiController\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrontEnd\Products\Category;

class CategoryController extends Controller
{
    // 1 index F
    public function Category(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $category    = Category::getCategory($user);
            if($category == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Categories ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $category,
                "message"  => "Categories Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 2 index Tree F
    public function CategoryTree(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            $category    = Category::getCategoryTree($user);
            if($category == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Categories ",
                ],403);
            }
            return response([
                "status"   => 200,
                "value"    => $category,
                "message"  => "Categories Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 3 create F
    public function CategoryCreate(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // ***************************************START**
            $create    = Category::createCategory($user,$data);
            // *****************************************END**
            if($create == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Categories ",
                ],403);
            }
            return response([
                "status"      => 200,
                "categories"  => $create,
                "message"     => "Create Categories Access Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }
    }
    // 4 Edit F
    public function CategoryEdit(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // ****************************************START**
            $edit    = Category::editCategory($user,$data,$id);
            // *******************************************END**
            if($edit == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Categories ",
                ],403);
            }
            return response([
                "status"    => 200,
                "value"     => $edit,
                "message"   => "Edit Categories Access Successfully",
            ]);
        }catch(Exception $e){   
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 5 Store
    public function CategoryStore(Request $request) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            \DB::beginTransaction();
            // **************************************************************START**
                $data         = $request->only([
                    "name","short_code",
                    "parent_id","woocommerce_cat_id",
                    "category_type","description","slug","image",
                ]);
                $data["business_id"] = $user->business_id;
                $data["created_by"]  = $user->id;
                $save                = Category::storeCategory($user,$data,$request);
                if($save == false){
                    return response()->json([
                        "status"   => 403,
                        "message"  => " Failed Action ",
                    ],403);
                }
            // ****************************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Added Category Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        }  
    }
    // 6 Update
    public function CategoryUpdate(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $data         = $request->only(["type"]);
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            \DB::beginTransaction();
            // ******************************************************START**
            $data         = $request->only([
                "name","short_code",
                "parent_id","woocommerce_cat_id",
                "category_type","description","slug","image",
            ]);
            $data["business_id"] = $user->business_id;
            $data["created_by"]  = $user->id;
            $update    = Category::updateCategory($user,$data,$id,$request);
            if($update == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Category ",
                ],403);
            }
            // ********************************************************END**
            \DB::commit();
            return response([
                "status"   => 200,
                "message" => "Updated Category Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
    // 7 Delete
    public function CategoryDelete(Request $request,$id) {
        try{
            $main_token   = $request->header("Authorization");
            $token        = substr($main_token,7);
            $type         = $request->input("type");
            if($token == "" || $token == null){
                return response([
                    "status"  => 403,
                    "message" => "Invalid Token"
                ],403);
            }
            $user      = \App\User::where("api_token",$token)->first();
            if(!$user){
                return response()->json([
                    "status"   => 403,
                    "message"  => " Unauthorized action ",
                ],403);
            } 
            // *********************************START**
            $del    = Category::deleteCategory($user,$id);
            // ***********************************END**
            if($del == false){
                return response()->json([
                    "status"   => 403,
                    "message"  => " No Have Category ",
                ],403);
            }
            if($del == "success"){
                 return response([
                    "status"   => 200,
                    "message" => "Deleted Category Successfully",
                ]);
            }else{
                    
                if($del == "product"){
                    return response()->json([
                        "status"   => 403,
                        "message"  => "  You Can't Delete This Category Because There are Products Depending on This Category ",
                    ],403);
                }
                if($del == "parent"){
                    return response()->json([
                        "status"   => 403,
                        "message"  => "  You Can't Delete This Category",
                    ],403);
                }
            }
            return response([
                "status"   => 200,
                "message" => "Deleted Cheques Successfully",
            ]);
        }catch(Exception $e){
            return response([
                "status"  => 403,
                "message" => "Failed Action Error 403",
            ],403);
        } 
    }
}
