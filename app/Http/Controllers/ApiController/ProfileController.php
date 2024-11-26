<?php

namespace App\Http\Controllers\ApiController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
require_once 'vendor/autoload.php';

class ProfileController extends Controller
{
    //.........................................
    // Profile Data
    // ****************************************
        // ** 1 get profile data 
        public function Profile(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                } 
                $check         = \App\Models\e_commerceClient::Profile($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 2 post update profile   
        public function UpdateProfile(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only([
                                            "business_name",
                                            "business_type",
                                            "first_name",
                                            "last_name",
                                            "gender",
                                            "dob",
                                            "mobile",
                                            "email",
                                            "email_personal",
                                            "email_work",
                                            "mobile_work",
                                        ]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }                         
                $check         = \App\Models\e_commerceClient::UpdateProfile($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 3 post Change Password Profile   
        public function ChangePassword(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only([
                                            "email",
                                            "password"
                                        ]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::ChangePassword($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 4 post Delete Account     
        public function DeleteAccount(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::DeleteAccount($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
    // ****************************************
    // Card Data
    // ****************************************
        // ** 5 get list Payment Card    
        public function getCards(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::PaymentsCardAccount($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        }
        // ** 6 post Create Payment Card    
        public function CreatePaymentCard(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only([
                                            "card_number",
                                            "card_type",
                                            "card_cvv",
                                            "card_expire"
                                        ]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::CreatePaymentCard($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 7 post Update Payment Card    
        public function UpdatePaymentCard(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only([
                                            "token",
                                            "card_number",
                                            "card_type",
                                            "card_cvv",
                                            "card_expire",
                                            "card_active"
                                        ]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::UpdatePaymentCard($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 8 post Delete Payment Card    
        public function DeletePaymentCard(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::DeletePaymentsCardAccount($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
    // ****************************************
    // Address Data
    // ****************************************
        // ** 9 get List Address      
        public function  getAddresses(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         =  \App\Models\e_commerceClient::AddressAccount($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        }   
        // ** 10 post Create Address      
        public function CreateAddress(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only([
                                            "token",
                                            "title",
                                            "building",
                                            "street",
                                            "flat",
                                            "area",
                                            "city",
                                            "country",
                                            "address_name",
                                            "address_type"
                                        ]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::CreateAddressAccount($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 11 post Update Address      
        public function UpdateAddress(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only([
                                            "title",
                                            "building",
                                            "street",
                                            "flat",
                                            "area",
                                            "city",
                                            "country",
                                            "address_name",
                                            "address_type"
                                        ]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::UpdateAddressAccount($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 12 post Delete Address      
        public function DeleteAddress(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data['id']    = $id;
                $data['token'] = $token;
                $check         = \App\Models\e_commerceClient::DeleteAddressAccount($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
    // ****************************************
    // Wishlist Data
    // ****************************************
        // ** 13 get List WishList      
        public function getWishlists(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::Wishlist($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        }
        // ** 14 post Add To WishList      
        public function AddWishlist(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only([
                                            "wishlist",
                                        ]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::AddWishlist($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 15 post Remove From WishList      
        public function RemoveWishlist(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data['id']    = $id;
                $data['token'] = $token;
                $check         = \App\Models\e_commerceClient::RemoveWishlist($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
    // ****************************************
    // Cart Data
    // ****************************************
        // ** 16 post save Cart Qty      
        public function SaveCartQty(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only(["qty"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::SaveCartQty($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 17 post update Cart Qty      
        public function UpdateCartQty(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only(["qty"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $data['id']    = $id;
                $check         = \App\Models\e_commerceClient::UpdateCartQty($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 18 post save Cart        
        public function SaveCart(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::SaveCart($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 19 post update Cart       
        public function UpdateCart(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only(["type"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $data['id']    = $id;
                $check         = \App\Models\e_commerceClient::UpdateCart($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 20 post Delete Cart       
        public function DeleteCart(Request $request,$id){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $data['id']    = $id;
                $check         = \App\Models\e_commerceClient::DeleteCart($data,$id);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 21 post Cart CheckOut       
        public function checkout(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::checkout($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** /new/ 22 post Cart Item       
        public function GetCart(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::itemCart($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
    // ****************************************
    // Orders Data
    // ****************************************   
        // ** 22 get Invoices       
        public function Orders(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::TaxInvoices($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        }
        // ** 23 post Save Tax Invoice      
        public function saveOrder(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only([ 
                                                "address_id"
                                                ,"card_id"
                                                ,"payment_type"
                                            ]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Token Expire') ,
                    ],401);
                }
     
                    
                if($data["payment_type"] == "card"){
                        $StripeSetting   = \App\Models\Ecommerce\StripeSetting::first();
                        $websiteUrl      = (!empty($StripeSetting))?$StripeSetting->url_website:"";   
                        $privateKey      = (!empty($StripeSetting))?$StripeSetting->api_private:"";   
                        $productKey      = (!empty($StripeSetting))?$StripeSetting->product_key:"";   
                        \Stripe\Stripe::setApiKey($privateKey);
                        header('Content-Type: application/json');
                        $YOUR_DOMAIN      = $websiteUrl;
                        $draft            = \App\Models\EcomTransaction::orderBy("id","desc")->where("created_by",$client->id)->first();
                        $PRICE            = doubleVal($draft->final_total)  * 100 ;
                        $checkout_session = \Stripe\Checkout\Session::create([
                        'line_items' => [[
                            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                            // 'price' => 'price_1OP2cGIasJEHL6ye9pJYyWoA',
                            'price_data' => [
                                'unit_amount' =>  $PRICE ,
                                'currency'    => 'AED',
                                'product'     => $productKey,
                            ],
                            'quantity' => 1,
                        ]],
                        'mode'        => 'payment',
                        'success_url' => $YOUR_DOMAIN . '?result=order-success&status=1&address_id='.$data["address_id"].'&card_id='.$data["card_id"].'&payment_type='.$data["payment_type"].'&token='.$data["token"],
                        'cancel_url'  => $YOUR_DOMAIN . '?result=order-failed&status=0&address_id='.$data["address_id"].'&card_id='.$data["card_id"].'&payment_type='.$data["payment_type"].'&token='.$data["token"],
                        ]);

                    // $check         = \App\Models\e_commerceClient::SaveTaxInvoice($data);
                        return response([
                            "status"       => 200,
                            "pay_link"     => $checkout_session->url, 
                            "message"      => __("Payment Link Access Successfully"),
                        ],200);
                } else{
                
                    $check         = \App\Models\e_commerceClient::SaveTaxInvoice($data);
                    
                } 
                
                
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 24 get Print Invoice       
        public function OrderPrint(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only(["bill_id"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::PrintData($data);
                // if($check["url"] != "" || $check["url"] != null ){
                //     // dd($check["url"]);
                //     return redirect(\URL::to($check["url"]));
                // }
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 25 post Order return       
        public function OrderReturn(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only(["bill_id","items"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::OrderReturn($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 26 post Last Product       
        public function LastProduct(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only(["type","url","product_id"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::LastProduct($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 27 get Last Product       
        public function GetLastProduct(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::GetLastProduct($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 28 get Last request Order Return       
        public function GetLastReturn(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::GetLastReturn($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 29 get Last all Order Return       
        public function GetLastOrderReturn(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::GetLastOrderReturn($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
        // ** 30 get List Order Movement       
        public function GetListOrderMovement(Request $request){
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only(["bill_id"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check      = \App\Models\e_commerceClient::getMovementOrder($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
                 // ** 31 post stripe Tax Invoice      
         public function stripe(Request $request){
            try{
               
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7);
                $data          = $request->only([ 
                                                "status"
                                                ,"address_id"
                                                ,"card_id"
                                                ,"payment_type"
                                            ]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $client        = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Token Expire') ,
                    ],401);
                }
                if(!$data["status"]){
                    return response([
                        "status"   => 405 ,
                        "message" => __('Failed Payment') ,
                    ],405);
                }
                $check         = \App\Models\e_commerceClient::SaveTaxInvoice($data);
                return  $check;
            }catch(Exception $e){
                return response([
                    "status"    => 403,
                    "message"  => __("Invalid data"),
                ],403);
            }
        } 
    // ****************************************   
    // Color Of E-commerce
    // ****************************************
        // 1** get Color 
        public function ChangeColor(Request $request) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["font_color","color","second_color","business_id"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                if($token == "" &&  $token == null){
                    abort(403, 'Unauthorized action.');
                }
                $check         = \App\Models\e_commerceClient::changeColor($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 2** change Color
        public function getColor(Request $request) {
            try{
                // $main_token    = $request->header("Authorization");
                // $token         = substr($main_token,7); 
                $data          = $request->only(["color"]);
                // $data["token"] = $token;
                // if($token == false){
                //     return response([
                //         "status"    => 401,
                //         "message"  => __('Sorry ,You Should Login To Your Account'),
                //     ],401);
                    
                // }
                // if($token == "" &&  $token == null){
                //     abort(403, 'Unauthorized action.');
                // }
                $check         = \App\Models\e_commerceClient::getColor($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
    // ****************************************
    // ****************************************   
    // LOGO Of E-commerce
    // ****************************************
        // 1** get Logo 
        public function ChangeLogo(Request $request) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["business_id"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                if($token == "" &&  $token == null){
                    abort(403, 'Unauthorized action.');
                }
                $check         = \App\Models\e_commerceClient::changeLogo($data,$request);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 2** change Logo
        public function getLogo(Request $request) {
            try{
                // $main_token    = $request->header("Authorization");
                // $token         = substr($main_token,7); 
                $data             =  [ ] ;
                // $data["token"] = $token;
                // if($token == false){
                //     return response([
                //         "status"    => 401,
                //         "message"  => __('Sorry ,You Should Login To Your Account'),
                //     ],401);
                    
                // }
                // if($token == "" &&  $token == null){
                //     abort(403, 'Unauthorized action.');
                // }
                $check         = \App\Models\e_commerceClient::getLogo($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
    // ****************************************
    // Floating Navigation align Of E-commerce
    // ****************************************
        // 1** get Floating   
        public function ChangeFloatAlign(Request $request) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["align","business_id"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                if($token == "" &&  $token == null){
                    abort(403, 'Unauthorized action.');
                }
                $check         = \App\Models\e_commerceClient::changeFloatAlign($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 2** change Floating   
        public function getFloatAlign(Request $request) {
            try{
                // $main_token    = $request->header("Authorization");
                // $token         = substr($main_token,7); 
                $data             =  [ ] ;
                // $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                // if($token == "" &&  $token == null){
                //     abort(403, 'Unauthorized action.');
                // }
                $check         = \App\Models\e_commerceClient::getFloatAlign($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 3** get Navigation   
        public function ChangeNavAlign(Request $request) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["align","business_id"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                if($token == "" &&  $token == null){
                    abort(403, 'Unauthorized action.');
                }
                $check         = \App\Models\e_commerceClient::changeNavAlign($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 4** change Navigation   
        public function getNavAlign(Request $request) {
            try{
                // $main_token    = $request->header("Authorization");
                // $token         = substr($main_token,7); 
                $data             =  [ ] ;
                // $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                // if($token == "" &&  $token == null){
                //     abort(403, 'Unauthorized action.');
                // }
                $check         = \App\Models\e_commerceClient::getNavAlign($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
    // ****************************************
    // Comment Of E-commerce
    // ****************************************
        // 1** get Comments 
        public function Comments(Request $request) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["product_id"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check              = \App\Models\e_commerceClient::Comments($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 2** save Comment
        public function addComments(Request $request) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["product_id","message","number_of_stars"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check   = \App\Models\e_commerceClient::addComments($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 3** update Comment
        public function updateComments(Request $request,$id) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["message","number_of_stars"]);
                $data["id"]    = $id;
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::updateComments($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 4** update Comment
        public function deleteComments(Request $request,$id) {
            try{
                $main_token     = $request->header("Authorization");
                $token          = substr($main_token,7); 
                $data["id"]     = $id;
                $data["token"]  = $token;
                $check          = \App\Models\e_commerceClient::deleteComments($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 5** replay Comment
        public function replayComments(Request $request,$id) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["message","number_of_stars"]);
                $data["id"]    = $id;
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::replayComments($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 6** Emoji Comment
        public function saveEmojiComments(Request $request,$id) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["liked_emoji"]);
                $data["id"]    = $id;
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::saveEmojiComments($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
    // ****************************************
    //  Conditions
    // ****************************************
        // 1** store condition 
        public function StoreCondition(Request $request) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["name"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check              = \App\Models\e_commerceClient::StoreCondition($data,$request);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 2** update condition
        public function UpdateCondition(Request $request,$id) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["name"]);
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $data["id"]    = $id;
                $check   = \App\Models\e_commerceClient::UpdateCondition($data,$request);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 3** delete condition
        public function DeleteCondition(Request $request,$id) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data["id"]    = $id;
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::DeleteCondition($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 4** store install
        public function StoreInstallment(Request $request) {
            try{
                $main_token     = $request->header("Authorization");
                $token          = substr($main_token,7); 
                $data           = $request->only(["name"]);
                $data["token"]  = $token;
                $check          = \App\Models\e_commerceClient::StoreInstallment($data,$request);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 5** update install
        public function UpdateInstallment(Request $request,$id) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data          = $request->only(["name"]);
                $data["id"]    = $id;
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::UpdateInstallment($data,$request);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 6** delete install
        public function DeleteInstallment(Request $request,$id) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data["id"]    = $id;
                $data["token"] = $token;
                if($token == false){
                    return response([
                        "status"    => 401,
                        "message"  => __('Sorry ,You Should Login To Your Account'),
                    ],401);
                    
                }
                $check         = \App\Models\e_commerceClient::DeleteInstallment($data);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
    // ****************************************


    //  Software
    // ****************************************
        // 1** get software page 
        public function software(Request $request) {
            try{
                $main_token    = $request->header("Authorization");
                $token         = substr($main_token,7); 
                $data["token"] = $token;
                // if($token == false){
                //     return response([
                //         "status"    => 401,
                //         "message"  => __('Sorry ,You Should Login To Your Account'),
                //     ],401);
                    
                // }
                $check              = \App\Models\e_commerceClient::software($data,$request);
                return $check;
            }catch(Exception $e){
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
    // ****************************************

    //......................................... 
}
