<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Firebase\JWT\Key;
 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Mailgun\Mailgun;

require 'vendor/autoload.php';

// require '../../vendor/PHPMailer/PHPMailer/src/Exception.php';
// require '../../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
// require '../../vendor/PHPMailer/PHPMailer/src/SMTP.php';

class e_commerceClient extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    //  Auth Section *1* 
    // .................................... 
        // 1*** login            -/
        public static function login($data){
            if(count($data)>0){
                
                $client       = \App\Models\e_commerceClient::where("email",$data['username'])->first(); 
                $username     = trim($data['username']); 
              
                if(!$client || !Hash::check($data['password'],$client->password)){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Wrong Data!! ,Please Check Your Email Or Password ') ,
                    ],403);
                }

                $payload = [
                    'client_id' => $client->id,
                    'username'  => $client->username,
                    'exp'       => \Carbon::now()->addDay(),
                ];

                // $secretKey = 'izo-'.$data['password'].$username;
                $secretKey = "izo";
                
                $headers = [
                    'alg' => 'HS256', // HMAC SHA-256 algorithm (default)
                    'typ' => 'JWT',
                    'kid' => $client->id,
                ];
                
                $token             = JWT::encode($payload, $secretKey, 'HS256', null, $headers);
                $client->api_token = $token;
                $client->update();

                $data = [
                    "id"                => $client->id,
                    "first_name"        => $client->first_name,
                    "second_name"       => $client->second_name,
                    "username"          => $client->username,
                    "business_name"     => $client->business_name,
                    "email"             => $client->email,
                    "email_personal"    => $client->email_personal,
                    "email_work"        => $client->email_work,
                    "address"           => $client->address,
                    "business_type"     => $client->business_type,
                    "mobile"            => $client->mobile,
                    "dob"               => $client->dob,
                    "gender"            => $client->gender,
                ];

                $subscribe = (\App\Models\Ecommerce\Subscribe::where("email",$client->email)->first())?true:false;
                return response([
                    "status"    => 200,
                    "message"   => "LogIn Successfully",
                    "token"     => $token,
                    "user"      => $data,
                    "subscribe" => $subscribe,
                ]);

            
            }else{
                abort(403,"UnAuthenticated action");
            }
        }
        // 2*** Google Login     -/
        public static function loginGoogle($data,$request) {
            if(count($data)>0){
              
                $idToken    = json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $data['idToken'])[1]))));
                $cnt        = \App\Models\e_commerceClient::where("email",$idToken->email)->first();
                if(!$cnt){
                    $data  = [
                        "business_name"    => "", 
                        "business_type"    => "", 
                        "first_name"       => $idToken->email,
                        "second_name"      => "",
                        "username"         => $idToken->email,
                        "password"         => "",
                        "email"            => $idToken->email,
                        "mobile"           => "",
                        "address"          => "",
                        "dob"              => "",
                        "email_personal"   => "",
                        "email_work"       => "",
                        "mobile_work"      => "",
                        "gender"           => "",
                        "language"         => "en",
                        "google_client_id" => $data['idToken'],
                    ];
                    $google  = 1;
                    $client  = \App\Models\e_commerceClient::signup($data,$request,$google);
                    $payload = [
                        'client_id' => $client->id,
                        'username'  => $client->username,
                        'exp'       => \Carbon::now()->addDay(),
                    ];
                    $secretKey = 'izo-'.$client->username;
                    $headers   = [
                                    'alg' => 'HS256', // HMAC SHA-256 algorithm (default)
                                    'typ' => 'JWT',
                    ];
                    $token             = JWT::encode($payload, $secretKey, 'HS256', null, $headers);
                    $client->api_token =  $token ;
                    $client->update();
                    
                    return $output       = [
                                            "status"  => 200,
                                            "message" => "Login Successfully",
                                            "token"   => $token ,
                                            "email"   => $idToken->email 
                                        ] ;
                }else{
                    $payload = [
                        'client_id' => $cnt->id,
                        'username'  => $cnt->username,
                        'exp'       => \Carbon::now()->addDay(),
                    ];
                    $secretKey = 'izo-'.$cnt->username;
                    $headers   = [
                                    'alg' => 'HS256', // HMAC SHA-256 algorithm (default)
                                    'typ' => 'JWT',
                    ];
                    $token             = JWT::encode($payload, $secretKey, 'HS256', null, $headers);
                    $cnt->api_token    = $token ;
                    $cnt->update();
                    return $output       = [
                                            "status"  => 200,
                                            "message" => "Login Successfully",
                                            "token"   => $token, 
                                            "email"   => $idToken->email 
                                            
                                        ] ;
                }
            }
           
        }
        // 3*** refresh token    -/
        public static function refreshToken($data){
            if(count($data)>0){
                $client       = \App\Models\e_commerceClient::where("api_token",$data['token'])->first(); 
                if(!$client ){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $username     = trim($client->username);
                $payload = [
                    'client_id' => $client->id,
                    'username'  => $client->username,
                    'exp'       => \Carbon::now()->addDay(),
                ];
                $random    = Str::random(40);
                $secretKey = 'izo-'.$random.$username;
                $headers   = [
                    'alg'  => 'HS256', // HMAC SHA-256 algorithm (default)
                    'typ'  => 'JWT',
                ];
                $token = JWT::encode($payload, $secretKey, 'HS256', null, $headers);
                $client->api_token = $token;
                $client->update();
                return response([
                    "status"  => 200,
                    "message" => "Token Refreshed Successfully",
                    "token"   => $token,
                ]);
            }else{
                abort(403,"UnAuthenticated action");
            }
        }
        // 4*** forget password  -/
        public static function forgetPassword($data)  {
                // *5**********************************************************************PHPMAILER*
                    //         $mail = new PHPMailer; //From email address and name 
                           
                    //         $mail->From = "alhmwi.agt@gmail.com"; 
                    //         $mail->FromName = "ALHAMWI GENERAL TRADING"; //To address and name 
                    //         $mail->addAddress("iebrahemsai944@gmail.com", "12Recepient Name");//Recipient name is optional
                    //         // $mail->addAddress("recepient1@example.com"); //Address to which recipient will reply 
                    //         // $mail->addReplyTo("reply@yourdomain.com", "Reply"); //CC and BCC 
                    //         // $mail->addCC("cc@example.com"); 
                    //         // $mail->addBCC("bcc@example.com"); //Send HTML or Plain Text email 
                   	// 		$mail->SMTPDebug = 3;
                    //         $mail->isSMTP(); 
                    //         $mail->Host = 'smtp.gmail.com';    // Must be GoDaddy host name
                    //         $mail->SMTPAuth = true; 
                    //         $mail->Username = 'alhamwi.agt@gmail.com';
                    //         $mail->Password = 'fmhmlparvdssqovw';
                    //         $mail->SMTPSecure = 'tls';   // ssl will no longer work on GoDaddy CPanel SMTP
                    //         $mail->Port = 587 ;    // Must use port 587 with TLS
                              
                    //         $mail->isHTML(true); 
                    //         $mail->Subject = "Subject Text"; 
                    //         $mail->Body = "<i>Mail body in HTML</i>";
                    //         $mail->AltBody = "This is the plain text version of the email content"; 
                    //         if(!$mail->send()) {
                    //             dd($mail->ErrorInfo);
                    //             //  dd($mail->ErrorInfo);
                    //             return response([
                    //                 "status"   => 403 ,
                    //                 "message" => "Mailer Error: " . $mail->ErrorInfo ,
                    //             ],403); 
                    //         }  
                // *5*******************************************************************************END*
                
                
                // *0*********************************************************************SENDMAIL*
                    // $from     = "alhamwi.agt@gmail.com";
                    // $to       = $data['email']; 
                    // $subject  = "Reset Password Permission";
                    // $message  = 'Code Reset Password : '.$dat ;
                    // $headers  = "From: " . $from;
                    // // $headers  .= "MIME-Version: 1.0\r\n";
                    // // $headers .= "Content-type: text/html; charset: utf8\r\n";
                    // mail($to,$subject,$message, $headers);
                // *0*************************************************************************END*
                
                
                // *0*********************************************************************SENDMAIL*
                    // $to      = $data['email'] . ", alhamwi.agt@gmail.com";
                    // $to      = $data['email'] ;
                    // $subject = "Reset Password Permission";
                   
                    // $message = "
                    //     <html>
                    //     <head>
                    //     <title>Change Your Password</title>
                    //     </head>
                    //     <body>
                    //         <p> <b>Izo-Ecommerce Support</b> </p><br>
                    //         <p> Hi , Dear  \n Your Password Changing Code is <b> Expired </b> After <b> 2 Minute </b> From your Receive This Email </p><br>
                    //         <p> Your Code : <b>" .$random."</b> </p><br>
                    //         <p> Thank You For Use Our Service .</p>
                    //     </table>
                    //     </body>
                    //     </html>
                    // ";
                    
                    // // Always set content-type when sending HTML email
                    // $headers = "MIME-Version: 1.0" . "\r\n";
                    // $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    
                    // // More headers
                    // $headers .= 'From: <alhamwi.agt@gmail.com>' . "\r\n";
                    // $headers .= 'Cc: izoclouduae@gmail.com' . "\r\n";
                    
                    // mail($to,$subject,$message,$headers);
                // ***************************************************************************end*
                
                
                // **1**********************************************************MAILGUN*
                    # Instantiate the client.
                    // $mgClient   = new Mailgun('9ead3a7edfc2a09fdfe44490450a03cb-5e3f36f5-7eb1ffea');
                    // $domain     = "sandboxd0c55f1e3d84475fa41a4146e96691e2.mailgun.org";
                    # Make the call to the client.
                    // $result     = $mgClient->sendMessage($domain, array(
                    // 	'from'	=> 'Excited User <Alhamwi.agt@gmail.com>',
                    // 	'to'	=> 'Baz <iebrahemsai944@gmail.com>',
                    // 	'subject' => 'Hello #oooo#',
                    // 	'text'	=> 'Testing some Mailgun awesomeness!'
                    // ));
                // ********************************************************************END*
                
                
                // **2************************************************************MAILGUN*
                    // $mg = Mailgun::create('9ead3a7edfc2a09fdfe44490450a03cb-5e3f36f5-7eb1ffea'); // For US servers
                    // $mg = Mailgun::create('key-example', 'https://api.eu.mailgun.net'); // For EU servers
                     // Now, compose and send your message.
                    // $mg->messages()->send($domain, $params);
                    // $mg->messages()->send('sandboxd0c55f1e3d84475fa41a4146e96691e2.mailgun.org', [
                    //   'from'    => 'alhamwi.agt@gmail.com',
                    //   'to'      => 'albaseetcompany8422@gmail.com',
                    //   'subject' => 'The PHP SDK is awesome!',
                    //   'text'    => 'It is so simple to send a message.'
                    // ]);
                // ********************************************************************END*
                
                
            if(count($data)>0){
                $client                  = \App\Models\e_commerceClient::where("email",$data['email'])->first(); 
                if(!$client && $data["token"] == ""){
                    return $output = [
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ] ;
                }
                if($client->google_login != 0){
                     return $output = [
                        "status"   => 403 ,
                        "message" => __('You Are Not Registered Before') ,
                    ] ;
                }
               
                $date                    = \Carbon::now();
                $dat                     = intval($date->format('YmdHis'));
                $random                  = mt_rand(5123,999899);
                $payload = [
                    'client_id' => $client->id,
                    'username'  => $client->username,
                    'key'       => $random,
                    'exp'       => \Carbon::now()->addMinute(8),
                ];
                $secretKey = 'izo-'.$random.$client->username;
                $headers   = [
                    'alg'  => 'HS256', // HMAC SHA-256 algorithm (default)
                    'typ'  => 'JWT',
                ];
                $token = JWT::encode($payload, $secretKey, 'HS256', null, $headers);
                $client->forget_password =  $token;
                $client->update();
                $url                     = config('app.url')."api/Ecom/forget-save";  
                                
                
                return $output = [
                    "status"   => 200,
                    "message"  => "Access Successfully",
                    "api_url"  =>  $url,
                    // "key"      =>  $dat,
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 5*** forget save      -/
        public static function forgetSave($data)  {
            if(count($data)>0){
                $client       = \App\Models\e_commerceClient::where("email",$data['email'])->first(); 
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                if(!$client->forget_password){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                try {
                   
                    $list =  JWT::decode($client->forget_password, new Key('izo-'.$data['key'].$client->username, 'HS256'));
                    // Handle token expiration
                } catch (\Firebase\JWT\ExpiredException $e) {
                    // The token has expired
                     return dd(1);
                } catch (\Firebase\JWT\BeforeValidException $e) {
                        // Handle token not yet valid
                        // The token is not yet valid
                        return dd(2);
                } catch (\Firebase\JWT\SignatureInvalidException $e) {
                        // Handle an invalid signature
                        // The token's signature is not valid
                        return response([
                                    "status"  => 403,
                                    "message" => "Signature Invalid",
                                ],403);
                } catch (\Exception $e) {
                        // Handle other JWT decoding errors
                        // The token is not valid for some reason
                        return dd(4);
                }
                // if(Hash::check($data['key'],$client->forget_password)){
                if(\Carbon::now() < \Carbon::createFromFormat('Y-m-d\TH:i:s.u\Z', $list->exp)->addHour(4)){
                    if(isset($data['password'])){
                            if( ($data['password'] != null || $data['password'] != "") && ($data['password'] != null || $data['password'] != "")){
                                if($client->email == $data['email'] ){
                                $client->password        = hash::make($data['password']);
                            }else{
                                return response([
                                    "status"  => 403,
                                    "message" => "Invalid Data",
                                ],403);
                            }
                        }
                        
                        $client->forget_password = null;
                        $client->update();
                    }else{
                        return response([
                            "status"  => 403,
                            "message" => "Invalid Data",
                        ],403);
                    
                    }
                }else{ 
                     return response([
                            "status"  => 401,
                            "message" => "TOKEN IS EXPIRE",
                        ],401);
                }
                return $output = [
                    "status"  => 200,
                    "message" => "Your Password Changed Successfully",
                ] ;

            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 6*** signup           -/
        public static function signup($data,$request,$google=null){
            if(count($data)>0){
                try{
                    $old_Client = e_commerceClient::where("email",$data["email"])->first();
                    if(!empty($old_Client)){
                        return response([
                            "status"   => 405 ,
                            "message" => __('Sorry! ,This Email Is Already Exist') ,
                        ],405);
                    }
                    //    **  register 
                    $client                   = new e_commerceClient();
                    // $client->business_name    = $data['business_name'];
                    // $client->business_type    = $data['business_type'];
                    $client->first_name       = $data['first_name'];
                    $client->second_name      = $data['second_name'];
                    $client->username         = $data['email'];
                    if($data['password'] != null && $data['password'] != ""){
                        $client->password         = Hash::make($data['password']);
                    }
                    $client->email            = $data['email'];
                    $client->mobile           = $data['mobile'];
                    // $client->address          = $data['address'];
                    // $client->dob              = $data['dob'];
                    // $client->email_personal   = $data['email_personal'];
                    // $client->email_work       = $data['email_work'];
                    // $client->mobile_work      = $data['mobile_work'];
                    // $client->gender           = $data['gender'];
                    // $client->language         = $data['language'];
                    if($google!=null){
                        $client->google_login = 1;
                    }
                    $client->client_status = 1;
                    $client->ip_address    = $request->ip();
                    $client->id_device     = $request->header('User-Agent');
                    $client->save();
                    
                    // ** create as customer    
                    $contact                               = new \App\Contact(); 
                    $contact->business_id                  = 1; 
                    $contact->type                         = "customer"; 
                    $contact->first_name                   = $client->email; 
                    $contact->name                         = $client->email; 
                    $contact->supplier_business_name       = $client->email; 
                    $ref_count                             = e_commerceClient::setAndGetReferenceCount( "e-commerce", 1);
                    $reference_no                          = e_commerceClient::generateReferenceNumber( "e-commerce", $ref_count, 1,"EC");
                    $contact->contact_id                   = $reference_no; 
                    $contact->contact_status               = "active"; 
                    $contact->mobile                       = $client->mobile; 
                    $contact->email                        = $client->email; 
                    $contact->address_line_1               = $client->address; 
                    $contact->created_by                   = 1; 
                    $contact->e_commerce                   = 1; 
                    $contact->save();
                    
                    // ** create account 
                    \App\Contact::add_account($contact->id,1,"Ecom_Customers");

                    // ** update contact_id    
                    $account                = \App\Account::where("contact_id",$contact->id)->first();
                    $new_client             = e_commerceClient::find($client->id);
                    $new_client->contact_id = $contact->id;
                    $new_client->update();
                    if($google != null){
                        return $client;
                    }
                    return response([
                        "status "    => 200,
                        "message"   => __('Sign Up successfully')  
                    ]);
                }catch(Exception $e){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
            }
        }
        // 7*** logout           -/
        public static function logout($data){
            if(count($data)>0){
                
                $client       = \App\Models\e_commerceClient::where("api_token",$data['token'])->first(); 
                
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $client->api_token = null;
                $client->update();
                 
                return response([
                    "status"  => 200,
                    "message" => "Logout Successfully",
                ]);
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 8*** subscribe           -/
        public static function subscribe($email){
            if($email){
                try{
                    $check        = \App\Models\Ecommerce\Subscribe::where("email",$email)->first();
                    if(!empty($check)){
                        return "exist";
                    }else{
                        $subscribe               = new \App\Models\Ecommerce\Subscribe();
                        $subscribe->email        = $email;   
                        $subscribe->date         = \Carbon::now();   
                        $subscribe->expire_date  = \Carbon::now()->addYear(1);   
                        $subscribe->active       = 1 ;   
                        $subscribe->save(); 
                        return response([
                            "status"  => 200,
                            "message" => "Subscribe Successfully",
                        ]);
                    }
                }catch(Exception $e){
                    return "false";
                }
            }
        }
    // ........................................

    //  Account /Profile/ Section *2* 
    // ....................................
        // 1*** Get Details Of Account Page
        public static function Profile($data) {
            if(count($data)>0){
                 $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                 if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $account_info["General_info"] = [
                        "first_name"     =>  $client->first_name ,
                        "last_name"      =>  $client->second_name ,
                        "gender"         =>  $client->gender ,
                        "dob"            =>  $client->dob ,
                        "business_name"  =>  $client->business_name ,
                        "business_type"  =>  $client->business_type 
                ];

                $account_info["Contact_info"] = [
                        "personal_email"  =>  $client->email_personal ,
                        "personal_phone"  =>  $client->mobile ,
                        "work_email"      =>  $client->email_work ,
                        "work_phone"      =>  $client->mobile_work  
                ];

                $account_info["Account"] = [
                    "email"   =>  $client->email ,
                ];

                $allData["Account_info"] = $account_info; 
                
                return $output = [
                            "status"  => 200,
                            "message" => __("Access Successfully"),
                            "allData" => $allData,
                ];
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 2*** Update Details Of Account Page -/
        public static function UpdateProfile($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }

                $client->business_name   = $data["business_name"]; 
                $client->business_type   = $data["business_type"]; 
                $client->first_name      = $data["first_name"]; 
                $client->second_name       = $data["last_name"]; 
                $client->gender          = $data["gender"]; 
                $client->dob             = $data["dob"]; 
                $client->mobile          = $data["mobile"];
                $client->email_personal  = $data["email_personal"];
                $client->email_work      = $data["email_work"];
                $client->mobile_work     = $data["mobile_work"];
                $client->update(); 

                return $output = [
                            "status"  => 200,
                            "message" => __("Updated Successfully")
                ];

            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 3*** Change Password Of Account  -/
        public static function ChangePassword($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                if($client->email != $data["email"]){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Email') ,
                    ],403);
                }
                $date                    = \Carbon::now();
                $dat                     = intval($date->format('YmdHis'));
                $random                  = mt_rand(5123,999899);
                $payload = [
                    'client_id' => $client->id,
                    'username'  => $client->username,
                    'key'       => $random,
                    'exp'       => \Carbon::now()->addMinute(8),
                ];
                $secretKey = 'izo-'.$random.$client->username;
                $headers   = [
                    'alg'  => 'HS256', // HMAC SHA-256 algorithm (default)
                    'typ'  => 'JWT',
                ];
                $token = JWT::encode($payload, $secretKey, 'HS256', null, $headers);
                $client->forget_password =  $token;
                $client->update();
                $url                     = config('app.url')."api/Ecom/forget-save";
                // $from     = "alhamwi.agt@gmail.com";
                // $to       = $data['email']; 
                // $subject  = "Reset Password Permission";
                // $message  = 'Code Reset Password : '.$dat ;
                // $headers  = "From: " . $from;
                // // $headers  .= "MIME-Version: 1.0\r\n";
                // // $headers .= "Content-type: text/html; charset: utf8\r\n";
                // mail($to,$subject,$message, $headers);
                
                // $to      = $data['email'] . ", alhamwi.agt@gmail.com";
                $to      = $data['email'] ;
                $subject = "Reset Password Permission";
               
                $message = "
                    <html>
                    <head>
                    <title>Change Your Password</title>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    </head>
                    <body style='text-align:center'>
                        
                        <h2> <b>Izo-Ecommerce Support</b> </h2><br>
                        <div style='width:100%; border:3px solid #ee8600; border-radius:3px;padding:10px;'>
                            <h3>Change Password Verify</h3>
                            <a href='#' id='myLink' style='text-decoration:none;background-color:#ee8600;border-radius:10px;padding:10px;color:white;border:0px solid black;'>Change Password</a> 
                        </div>
                    </table>
                    
                    

                    <script>
                    document.getElementById('myLink').addEventListener('click', function(event) {
                      event.preventDefault();
                    
                       
                      var headers = new Headers();
                      headers.append('Authorization', 'Bearer ".$data["token"]."');
                    
                       
                      var data = {
                        key: ".$random.",
                        email:".$data['email'].",
                        password: ".$data['password']."
                      };
                    
                       
                      fetch('".$url."', {
                        method: 'POST', // or 'GET' or any other HTTP method
                        headers: headers,
                        body: JSON.stringify(data)
                      })
                      .then(response => response.json())
                      .then(data => {
                        
                        console.log(data);
                      })
                      .catch(error => {
                        console.error('Error:', error);
                      });
                    });
                    </script>
                    </body>
                    </html>
                ";
                
                // Always set content-type when sending HTML email
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                
                // More headers
                $headers .= 'From: <alhamwi.agt@gmail.com>' . "\r\n";
                $headers .= 'Cc: izoclouduae@gmail.com' . "\r\n";
                
                mail($to,$subject,$message,$headers);
                
                // \App\Models\e_commerceClient::CheckTokenExpire($data["token"]);
                // $client->password    = hash::make($data["password"]);
                // $client->update(); 

                return $output = [
                            "status"  => 200,
                            "message" => __("Password Changed Successfully"),
                ];

            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 4*** Delete Account  -/
        public static function DeleteAccount($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $client->delete();
                return $output = [
                    "status"  => 200,
                    "message" => __("Account Deleted Successfully"),
                ];
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 5*** Payments Card Account
        public static function PaymentsCardAccount($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                 if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                $account_payments = [] ;
                $cardPayment      = \App\Models\PaymentCard::where("client_id",$client->id)->where("card_active",0)->get();
                foreach($cardPayment as $ic){
                    $last_number = substr(decrypt($ic->card_number),12);
                    $account_payments[] = [
                           "id"                  =>  $ic->id,
                           "type"                =>  $ic->card_type,
                           "last_four_number"    =>  $last_number ,
                           "expire"              =>  $ic->card_expire 
                    ];
                }
                return $output = [
                            "status"  => 200,
                            "message" => __("Access Successfully"),
                            "Cards"   => $account_payments,
                ];
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 6*** Create Payments Card Account -/
        public static function CreatePaymentCard($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $payCart = \App\Models\PaymentCard::cardCreate($data,$client->id);
                return $payCart;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 7*** Update Payments Card Account -/
        public static function UpdatePaymentCard($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data['id'] = $id;
                $payCart = \App\Models\PaymentCard::cardUpdate($data,$client->id);
                return $payCart;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('something_wrong') ,
                ],403);
            }
        }
        // 8*** Delete Payments Card Account -/
        public static function DeletePaymentsCardAccount($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data['id'] = $id;
                $payCart = \App\Models\PaymentCard::cardDelete($data);
                return $payCart;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 9*** Addresses Account
        public static function AddressAccount($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                 if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                $account_addresses = [] ;
                $address     = \App\Models\AccountAddress::where("client_id",$client->id)->get();
                foreach($address as $it){
                    $account_addresses[] = [
                           "id"             =>  $it->id ,
                           "title"          =>  $it->title ,
                           "building"       =>  $it->building ,
                           "street"         =>  $it->street ,
                           "flat"           =>  $it->flat,
                           "area"           =>  $it->area ,
                           "city"           =>  $it->city ,
                           "country"        =>  $it->country ,
                           "address_name"   =>  $it->address_name ,
                           "address_type"   =>  $it->address_type ,
                    ];
                 }
                return $output = [
                            "status"      => 200,
                            "message"     => __("Access Successfully"),
                            "Addresses"   => $account_addresses,
                ];
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 10*** Create Address Account
        public static function CreateAddressAccount($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }   
                $payCart = \App\Models\AccountAddress::addressCreate($data,$client->id);
                return $payCart;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 11*** Update Address Account
        public static function UpdateAddressAccount($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data['id'] = $id; 
                $payCart    = \App\Models\AccountAddress::addressUpdate($data,$client->id);
                return $payCart;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 12*** Delete Address Account
        public static function DeleteAddressAccount($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data['id'] = $id;
                $payCart    = \App\Models\AccountAddress::addressDelete($data);
                return $payCart;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 13*** Wishlist 
        public static function Wishlist($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                 if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $account_wishlist = [] ;
                $wishlist    = \App\Models\WishList::where("client_id",$client->id)->whereNull("deleted_at")->get();
                foreach($wishlist as $ia){
                    $account_wishlist[] = [
                        "id"             =>  $ia->id,
                        "product_id"     =>  $ia->product->id,
                        "category"       =>  $ia->product->sub_category->name ,
                        "product_name"   =>  $ia->product->name ,
                        "image"          =>  $ia->product->image_url,            
                        "product_code"   =>  $ia->product->sku,
                        "warranty"       =>  $ia->product->warranty ,
                        "price"          =>  round($ia->product->variations[0]->default_sell_price,2),
                    ];
                }
                return $output = [
                            "status"      => 200,
                            "message"     => __("Access Successfully"),
                            "Wishlist"    => $account_wishlist,
                ];
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 14*** Wishlist Account
        public static function WishlistAccount($data) {
            if(count($data)>0){
                
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 15*** Add To Wishlist 
        public static function AddWishlist($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data['product_id'] = $id;
                $wishlist    = \App\Models\WishList::AddWishlist($data,$client->id);
                return $wishlist;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 16*** Remove From Wishlist 
        public static function RemoveWishlist($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data['product_id'] = $id;
                $wishlist    = \App\Models\WishList::RemoveWishlist($data,$client->id);
                return $wishlist;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 17*** Save Cart QTY  
        public static function SaveCartQty($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data['product_id'] = $id;
                $cart               = \App\Models\EcomTransaction::SaveCartQty($data,$client);
                return $cart;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 18*** Update Cart QTY  
        public static function UpdateCartQty($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data['product_id'] = $id;
                $cart               = \App\Models\EcomTransaction::UpdateCartQty($data,$client);
                return $cart;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 19*** Save Cart  
        public static function SaveCart($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data['product_id'] = $id;
                $cart               = \App\Models\EcomTransaction::saveCart($data,$client);
                return $cart;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 20*** Update Cart  
        public static function UpdateCart($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data['product_id'] = $id;
                $cart            = \App\Models\EcomTransaction::updateCart($data,$client);
                return $cart;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 21*** Delete Cart  
        public static function DeleteCart($data,$id) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                $data['product_id'] = $id;
                $wishlist    = \App\Models\EcomTransaction::deleteCart($data,$client);
                return $wishlist;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 22*** list Orders  
        public static function TaxInvoices($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $TaxInvoiceList    = \App\Models\EcomTransaction::TaxInvoiceList($data,$client);
                return $TaxInvoiceList;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 23*** Save Orders  
        public static function SaveTaxInvoice($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
               
                $orders        = \App\Models\EcomTransaction::saveTaxInvoice($data,$client);
                return $orders;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 24*** Print Orders  
        public static function PrintData($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $transaction = \App\Transaction::find($data["bill_id"]);
                if($transaction){
                    $url = \URL::to('reports/sell/'.$data["bill_id"].'?invoice_no='.$transaction->invoice_no);
                    // $url = 'reports/sell/'.$data["bill_id"].'?invoice_no='.$transaction->invoice_no ;
                }else{
                    $url = null;
                }
                return $output = [
                    "status"   => 200 ,
                    "url"      => $url ,
                    "message" => "Print Url Access Successfully" ,
                ];
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 25*** send Request  Orders Return  
        public static function OrderReturn($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],403);
                }
                $line_id   = []; 
                $line_qty  = []; 
                foreach($data["items"] as $i){
                    $line_id[]  = $i["id"]; 
                    $line_qty[] = $i["qty"]; 
                }
                $data["items"]    = $line_id;
                $data["quantity"] = $line_qty;
                $req        = \App\Models\RequestReturn::sendRequest($data,$client);
                if($req == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Wrong When Making Return Request') ,
                    ],403);
                }else{
                    return $output = [
                        "status"   => 200,
                        "message" => __('Request Order Return Successfully'),
                    ];
                }
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 26*** send Request  Last Product    
        public static function LastProduct($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $data["reference_number"]    = null;
                $data["order_no"]            = null;
                $move        = \App\Models\lastMovement::saveMovement($data,$client);
                if($move == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Wrong When Saving The Movement') ,
                    ],403);
                }else{
                    return $output = [
                            "status"   => 200,    
                            "message" => __("Saved Successfully"),    
                    ] ;
                }
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 27*** get Last Product list    
        public static function GetLastProduct($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $move        = \App\Models\lastMovement::GetLastProduct($data,$client);
                if($move == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('No Previous Movement') ,
                    ],403);
                }else{
                    return $output = [
                            "status"   => 200,    
                            "list"     => $move,    
                            "message" => __("Access Last Product Successfully"),    
                    ] ;
                }
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 28*** get Last request Order Return list    
        public static function GetLastReturn($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $move        = \App\Models\RequestReturn::GetLastReturn($data,$client);
                if($move == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('No Have Previous Return Requests') ,
                    ],403);
                }else{
                    return $output = [
                            "status"   => 200,    
                            "list"     => $move,    
                            "message" => __("Access Last Order Return Request Successfully"),    
                    ] ;
                }
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 29*** get Last all Order Return list    
        public static function GetLastOrderReturn($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],403);
                }
                $move        = \App\Transaction::GetLastOrderReturn($data,$client);
                if($move == false){
                    return response([
                        "status"   => 200 ,
                        "message" => __('No Have Previous Orders Return ') ,
                    ],200);
                }else{
                    return $output = [
                            "status"   => 200,    
                            "list"     => $move,    
                            "message" => __("Access Last Order Return Request Successfully"),    
                    ] ;
                }
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 30*** send checkout    
        public static function checkout($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $checkout        = \App\Models\EcomTransaction::checkout($data,$client);
                $delivery        = \App\Models\EcomTransaction::addresses($client);
                $card            = \App\Models\EcomTransaction::card($client);
                $condition       = \App\Models\Ecommerce\Condition::indexCondition($client);
                $installment     = \App\Models\Ecommerce\Installment::indexInstallment($client);
   
                if($checkout == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }else{
                    return $output = [
                            "status"                 => 200,    
                            "cart"                   => $checkout,    
                            "delivery_addresses"     => $delivery,    
                            "payment_card"           => $card,    
                            "card"                   => $condition,    
                            "installment"            => $installment,    
                            "message"               => __("Access Checkout Successfully"),    
                    ] ;
                }
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 31*** get location
        public static function Location() {
            $business               = \App\BusinessLocation::first();
            if(empty($business)){ 
                    return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }    
            $location               = json_decode($business->location_map);  
            $list                   = [];
            foreach($location as $key => $i){
                if($key == 0){
                    $list["lat"] = $i;
                }else{
                    $list["lng"] = $i;
                }
            }  
            return $output = [
                    "status"         => 200,
                    "location"       => $list,
                    "message"       => __("Access Location Successfully")    
            ] ;
             
        }
        // 32*** update location
        public static function EditLocation($data) {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $business_id            = $data["business_id"] ;
                $business               = \App\BusinessLocation::where("business_id",$business_id)->first();
                $data["location"]       = [0=>$data["lat"],1=>$data["lng"]];
                $business->location_map = json_encode($data['location']);
                $business->update();
                return $output = [
                        "status"         => 200,
                        "message"       => __("Updated Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 33*** get links social media
        public static function getLink($data)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $list    = \App\Models\Ecommerce\SocialMedia::getLinks($data,$client);
                if($list == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "links"          => $list,
                        "message"       => __("Access Links Social Media Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ]);
            }
        }
        // 34*** save links social media
        public static function saveLink($data,$request)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $add    = \App\Models\Ecommerce\SocialMedia::addLinks($data,$client,$request);
                if($add == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Added Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 35*** update links social media
        public static function updateLink($data,$request)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $update    = \App\Models\Ecommerce\SocialMedia::updateLinks($data,$client,$request);
                if($update == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Updated Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 36*** delete links social media
        public static function deleteLink($data)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $del    = \App\Models\Ecommerce\SocialMedia::delLinks($data,$client);
                if($del == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Deleted Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 37*** get Store Features
        public static function getStoreFeature($data)  {
            try{
                // $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                // if(!$client && $data["token"] == ""){
                //    abort(403,"Invalid Data");
                // }
                $list    = \App\Models\Ecommerce\StoreFeature::getStoreFeature();
                if($list == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "store_feature"  => $list,
                        "message"       => __("Access Stored Successfully")    
                ] ;
            }catch(Exception $e){
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ]);
            }
        }
        // 37*** get one Store Features
        public static function getStoreFeatureOne($data,$id)  {
            try{
                // $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                // if(!$client && $data["token"] == ""){
                //    abort(403,"Invalid Data");
                // }
                $list    = \App\Models\Ecommerce\StoreFeature::getStoreFeatureOne($data,$id);
                if($list == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "store_feature"  => $list,
                        "message"       => __("Access Stored Successfully")    
                ] ;
            }catch(Exception $e){
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 38*** save Store Features
        public static function addStoreFeature($data,$request)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $save    = \App\Models\Ecommerce\StoreFeature::saveStoreFeature($data,$client,$request);
                if($save == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Added Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 39*** update Store Features
        public static function updateStoreFeature($data,$request)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $update    = \App\Models\Ecommerce\StoreFeature::updateStoreFeature($data,$client,$request);
                if($update == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Updated Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 40*** delete Store Features
        public static function deleteStoreFeature($data)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $del    = \App\Models\Ecommerce\StoreFeature::deleteStoreFeature($data,$client);
                if($del == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Deleted Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 41*** send Messages
        public static function sendMessage($data)  {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $del    = \App\Models\Ecommerce\WebMessage::saveMessage($data,$client);
                if($del == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Message Send Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 42*** Change Color 
        public static function changeColor($data)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $business_id = $data["business_id"];
                $color    = \App\Business::changeColor($data,$client,$business_id);
                if($color == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Color Changed Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 43*** get Color 
        public static function getColor($data)  {
             
            // $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
            // if(!$client && $data["token"] == ""){
            //     return response([
            //         "status"   => 403 ,
            //         "message" => __('Invalid Data') ,
            //     ],403);
            // }
            $color    = \App\Business::getColor($data);
            if($color == false){
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
            return $output = [
                    "status"         => 200,
                    "value"          => $color,
                    "message"        => __("Color Access Successfully")    
            ] ;
            
        }
        // 44*** order movement
        public static function getMovementOrder($data){
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $list    = \App\Models\Ecommerce\OrderMovement::getMovement($data,$client);
                if($list == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "list"           => $list,
                        "message"       => __("Order Movement Access Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // 45*** order save movement
        public static function saveMovementOrder($data){
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $save    = \App\Models\Ecommerce\OrderMovement::saveMovement($data,$client);
                if($save == false){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Order Movement Saved Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
        // /new/ item Cart
        public static function itemCart($data){
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $transaction = \App\Models\EcomTransaction::where("status","=","draft")->where("created_by",$client->id)->get();
                $dataAll     = 0;
                foreach($transaction as $i){
                    $items  = \App\Models\EcomTransactionSellLine::where("ecom_transaction_id",$i->id)->get();
                    foreach($items as $ite){
                        $dataAll += $ite->quantity;
                    }
                }
                return response([
                    "status"   => 200 ,
                    "value"     => $dataAll ,
                    "message"  => __('Success Cart Item') ,
                ],200);
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            } 
        }
        // 46*** Change Logo 
        public static function changeLogo($data,$request)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message"  => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $business_id = $data["business_id"];
                $color    = \App\Business::changeLogo($data,$client,$business_id,$request);
                if($color == false){
                    return response([
                        "status"   => 403 ,
                        "message"  => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"        => __("Logo Changed Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message"  => __('Invalid Data') ,
                ],403);
            }
        }
        // 47*** get Logo 
        public static function getLogo($data)  {
                
            // $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
            // if(!$client && $data["token"] == ""){
            //     return response([
            //         "status"   => 403 ,
            //         "message" => __('Invalid Data') ,
            //     ],403);
            // }
            $color    = \App\Business::getLogo($data);
            if($color == false){
                return response([
                    "status"   => 403 ,
                    "message"  => __('Invalid Data') ,
                ],403);
            }elseif($color == "nan"){
                
                return $output = [
                        "status"         => 200,
                        "value"          => "",
                        "message"        => __("Logo Access Successfully")    
                ] ;
                
            }
            return $output = [
                    "status"         => 200,
                    "value"          => $color,
                    "message"        => __("Logo Access Successfully")    
            ] ;
            
        }
        // 48*** Change Float Align  
        public static function changeFloatAlign($data)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message"  => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $business_id = $data["business_id"];
                $color    = \App\Business::changeFloatAlign($data,$client,$business_id);
                if($color == false){
                    return response([
                        "status"   => 403 ,
                        "message"  => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"        => __("Floating Bar Changed Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message"  => __('Invalid Data') ,
                ],403);
            }
        }
        // 49*** get Float Align
        public static function getFloatAlign($data)  {
                
            // $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
            // if(!$client && $data["token"] == ""){
            //     return response([
            //         "status"   => 403 ,
            //         "message" => __('Invalid Data') ,
            //     ],403);
            // }
            $color    = \App\Business::getFloatAlign($data);
            if($color === false){
                return response([
                    "status"   => 403 ,
                    "message"  => __('Invalid Data') ,
                ],403);
            }elseif($color == "nan"){
                
                return $output = [
                        "status"         => 200,
                        "value"          => "",
                        "message"        => __("Floating Bar Access Successfully")    
                ] ;
                
            }
            return $output = [
                    "status"         => 200,
                    "value"          => $color,
                    "message"        => __("Floating Bar Access Successfully")    
            ] ;
            
        }  
        // 50*** Change Navigation Align  
        public static function changeNavAlign($data)  {
            if(count($data)>0){
                $client  = \App\Models\Ecommerce\Eadmin::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message"  => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $business_id = $data["business_id"];
                $color    = \App\Business::changeNavAlign($data,$client,$business_id);
                if($color == false){
                    return response([
                        "status"   => 403 ,
                        "message"  => __('Invalid Data') ,
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"        => __("Navigation Bar Changed Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message"  => __('Invalid Data') ,
                ],403);
            }
        }
        // 51*** get Navigation Align
        public static function getNavAlign($data)  {
                
            // $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
            // if(!$client && $data["token"] == ""){
            //     return response([
            //         "status"   => 403 ,
            //         "message" => __('Invalid Data') ,
            //     ],403);
            // }
            $color    = \App\Business::getNavAlign($data);
            if($color === false){
                return response([
                    "status"   => 403 ,
                    "message"  => __('Invalid Data') ,
                ],403);
            }elseif($color == "nan"){
                
                return $output = [
                        "status"         => 200,
                        "value"          => "",
                        "message"        => __("Navigation Bar Access Successfully")    
                ] ;
                
            }
            return $output = [
                    "status"         => 200,
                    "value"          => $color,
                    "message"        => __("Navigation Bar Access Successfully")    
            ] ;
            
        }  
    // ....................................

    //  Layouts Section *3*
    // ....................................
        // *** FLOATING
            // 1*** Get Floating Bar
            public static function getListFloatingBar() {
                try{
                    // $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                    // if(!$client && $data["token"] == ""){
                    //     abort(403,"Invalid Data");
                    // }
                    $list    = \App\Models\Ecommerce\FloatingBar::getListFloatingBar();
                    if($list == false){
                        return response([
                            "status"   => 403 ,
                            "message" => __('Invalid Data') ,
                        ],403);
                    }
                    return $output = [
                                "status"  => 200,
                                "list"    => $list,
                                "message" => __("Access Floating Bar Successfully"),
                    ];
                }catch(Exception $e){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
            }
            // 2*** Save Floating Bar
            public static function saveFloatingBar($data,$request) {
                if(count($data)>0){
                    $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                    if(!$client && $data["token"] == ""){
                        return response([
                            "status"   => 401 ,
                            "message" => __('Sorry ,You Should Login To Your Account') ,
                        ],401);
                    }
                    $save    = \App\Models\Ecommerce\FloatingBar::saveFloatingBar($data,$client,$request);
                    if($save == false){
                        return response([
                            "status"   => 403 ,
                            "message" => __('Invalid Data') ,
                        ],403);
                    }
                    return $output = [
                                "status"  => 200,
                                "message" => __("Added Successfully"),
                    ];
                }else{
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
            }
            // 3*** Update Floating Bar
            public static function updateFloatingBar($data) {
                if(count($data)>0){
                    $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                    if(!$client && $data["token"] == ""){
                        return response([
                            "status"   => 401 ,
                            "message" => __('Sorry ,You Should Login To Your Account') ,
                        ],401);
                    }
                    $update    = \App\Models\Ecommerce\FloatingBar::updateFloatingBar($data,$client);
                    if($update == false){
                        return response([
                            "status"   => 403 ,
                            "message" => __('Invalid Data') ,
                        ],403);
                    }
                    return $output = [
                                "status"  => 200,
                                "message" => __("Updated Successfully"),
                    ];
                }else{
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
            }
            // 4*** Delete Floating Bar
            public static function deleteFloatingBar($data) {
                if(count($data)>0){
                    $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                    if(!$client && $data["token"] == ""){
                        return response([
                            "status"   => 401 ,
                            "message" => __('Sorry ,You Should Login To Your Account') ,
                        ],401);
                    }
                    $delete    = \App\Models\Ecommerce\FloatingBar::deleteFloatingBar($data,$client);
                    if($delete == false){
                        return response([
                            "status"=>403,
                            "message"=>__("Failed Actions"),
                        ],403);
                    }
                    return $output = [
                                "status"  => 200,
                                "message" => __("Deleted Successfully"),
                    ];
                }else{
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
            }
        // *** AGT8422

        // *** NAVIGATION
            // 1*** Get Navigation Bar
            public static function getListNavigationBar() {
                try{
                    // $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                    // if(!$client && $data["token"] == ""){
                    //     abort(403,"Invalid Data");
                    // }
                    $list               = \App\Models\Ecommerce\NavigationBar::getListNavigationBar();
                    $business_location  = \App\BusinessLocation::first();
                    $social_media       = \App\Models\Ecommerce\SocialMedia::where("business_id",$business_location->business_id)->select(["id","title","link","icon","index_id"])->orderBy("index_id","asc")->get();
                    $list_              =  [];
                    foreach($social_media as $i){
                        $list_ [] = [
                                "id"    => $i->id,
                                "title" => $i->title,
                                "link"  => $i->link,
                                "icon"  => $i->icon_url,
                        ];
                    } 
                    if($list == false){
                        return response([
                            "status"=>403,
                            "message"=>__("Failed Actions"),
                        ],403);
                    }
                    return $output = [
                                "status"          => 200,
                                "list"            => $list,
                                "social_media"    => $list_,
                                "message"         => __("Access Navigation Bar Successfully"),
                    ];
                }catch(Exception $e){
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
            }
            // 2*** Save Navigation Bar
            public static function saveNavigationBar($data,$request) {
                if(count($data)>0){
                    $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                    if(!$client && $data["token"] == ""){
                        return response([
                            "status"   => 401 ,
                            "message" => __('Sorry ,You Should Login To Your Account') ,
                        ],401);
                    }
                    $save    = \App\Models\Ecommerce\NavigationBar::saveNavigationBar($data,$client,$request);
                    if($save == false){
                        return response([
                            "status"=>403,
                            "message"=>__("Failed Actions"),
                        ],403);
                    }
                    return $output = [
                                "status"  => 200,
                                "message" => __("Added Successfully"),
                    ];
                }else{
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
            }
            // 3*** Update Navigation Bar
            public static function updateNavigationBar($data) {
                if(count($data)>0){
                        $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                        if(!$client && $data["token"] == ""){
                            return response([
                                "status"   => 401 ,
                                "message" => __('Sorry ,You Should Login To Your Account') ,
                            ],401);
                    }
                    $update    = \App\Models\Ecommerce\NavigationBar::updateNavigationBar($data,$client);
                    if($update == false){
                        return response([
                            "status"=>403,
                            "message"=>__("Failed Actions"),
                        ],403);
                    }
                    return $output = [
                                "status"  => 200,
                                "message" => __("Updated Successfully"),
                    ];
                }else{
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
            }
            // 4*** Delete Navigation Bar
            public static function deleteNavigationBar($data) {
                if(count($data)>0){
                    $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                    if(!$client && $data["token"] == ""){
                        return response([
                            "status"   => 401 ,
                            "message" => __('Sorry ,You Should Login To Your Account') ,
                        ],401);
                    }
                    $delete    = \App\Models\Ecommerce\NavigationBar::deleteNavigationBar($data,$client);
                    if($delete == false){
                        return response([
                            "status"=>403,
                            "message"=>__("Failed Actions"),
                        ],403);
                    }
                    return $output = [
                                "status"  => 200,
                                "message" => __("Deleted Successfully"),
                    ];
                }else{
                    return response([
                        "status"   => 403 ,
                        "message" => __('Invalid Data') ,
                    ],403);
                }
            }
        // *** AGT8422 
    // ....................................

    //  E-commerce Rate  Section *4*
    // ....................................
        // 1** list rate
        public static function listRate($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $list    = \App\Models\Ecommerce\WebRate::listRate($data,$client);
                if($list == false){
                    return response([
                        "status"=>403,
                        "message"=>__("Failed Actions"),
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "list"           => $list,
                        "message"       => __("Rate Access Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }    
        // 2** add rate
        public static function addRate($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $color    = \App\Models\Ecommerce\WebRate::saveRate($data,$client);
                if($color == false){
                    return response([
                        "status"=>403,
                        "message"=>__("Failed Actions"),
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Rate Added Successfully")    
                ] ;
            }else{
                return response([
                    "status"   => 403 ,
                    "message" => __('Invalid Data') ,
                ],403);
            }
        }
    // ....................................

    //  Comments Section *5*
    // ....................................
        // 1*** list comments 
        public static function Comments($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $comment  = \App\Models\Ecommerce\WebComment::listComments($data,$client) ;
                if($comment == false){
                    return response([
                        "status"=>403,
                        "message"=>__("Failed Actions"),
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "comment"        => $comment,
                        "message"       => __("List Comments Access Successfully")    
                ] ; 
            }else{
                return response([
                    "status"  => 403,
                    "message" => __("Failed Actions"),
                ],403);
            }
        }
        // 2*** add comments 
        public static function addComments($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $comment  = \App\Models\Ecommerce\WebComment::addComments($data,$client) ;
                if($comment == false){
                    return response([
                        "status"=>403,
                        "message"=>__("Failed Actions"),
                    ],403);
                }
                return $output = [
                        "status"         => 200,
                        "message"       => __("Comments Saved Successfully")    
                ] ; 
            }else{
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 3*** update comments 
        public static function updateComments($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $updateComment  = \App\Models\Ecommerce\WebComment::updateComments($data,$client) ;
                if($updateComment == false){
                    return response([
                        "status"=>403,
                        "message"=>__("Failed Actions"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "message"       => __("Comments Update Successfully")    
                ] ;  
            }else{
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 4*** delete comments 
        public static function deleteComments($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $deleteComment  = \App\Models\Ecommerce\WebComment::deleteComments($data,$client) ;
                if($deleteComment == false){
                    return response([
                        "status"=>403,
                        "message"=>__("Failed Actions"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "message"       => __("Comments Deleted Successfully")    
                ] ; 
            }else{
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }
        }
        // 5*** replay comments 
        public static function replayComments($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $replayComment  = \App\Models\Ecommerce\WebComment::replayComments($data,$client) ;
                if($replayComment == false){
                    return response([
                        "status"=>403,
                        "message"=>__("Failed Actions"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "message"       => __("Comments Replayed Successfully")    
                ] ;  
            }else{
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            } 
        }
        // 6*** emoji comments 
        public static function saveEmojiComments($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $emojiComment  = \App\Models\Ecommerce\WebComment::saveEmojiComment($data,$client) ;
                if($emojiComment == false){
                    return response([
                        "status"=>403,
                        "message"=>__("Failed Actions"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "message"       => __("Emoji updated Successfully")    
                ] ;  
            }else{
                return response([
                    "status"=>403,
                    "message"=>__("Failed Actions"),
                ],403);
            }  
        }
    // ....................................

    // Store Page
    // ....................................
         // 1*** Store page
        public static function getStorePage($data,$request) {
            if(count($data)>0){
                // $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                // if(!$client && $data["token"] == ""){
                //     return response([
                //         "status" => 403,
                //         "message" => __("Token Expire"),
                //     ],403);
                // }
                $StorePage  = \App\Product::StorePage($data,null,$request) ;
                if($StorePage == false){
                    return response([
                        "status" => 403,
                        "message" => __("Invalid Input Data"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "items"          => $StorePage,
                    "message"       => __("Store Page Access Successfully")    
                ] ; 
            }else{
                return response([
                    "status" => 403,
                    "message" => __("Invalid Data"),
                ],403);
            }
        }
    // ....................................

    // Condition 
    // ....................................
        // 1** store condition 
        public static function StoreCondition($data,$request) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $Store        = \App\Models\Ecommerce\Condition::storeCondition($client,$data,$request) ;
                if($Store    == false){
                    return response([
                        "status" => 403,
                        "message" => __("Invalid Input Data"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "message"       => __("Added Successfully")    
                ] ; 
            }else{
                return response([
                    "status" => 403,
                    "message" => __("Invalid Data"),
                ],403);
            }
        }
        // 2** update condition
        public static function UpdateCondition($data,$request) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $Store        = \App\Models\Ecommerce\Condition::updateCondition($client,$data,$request) ;
                if($Store    == false){
                    return response([
                        "status" => 403,
                        "message" => __("Invalid Input Data"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "message"       => __("Updated Successfully")    
                ] ; 
            }else{
                return response([
                    "status" => 403,
                    "message" => __("Invalid Data"),
                ],403);
            }
        }
        // 3** delete condition
        public static function DeleteCondition($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $Store        = \App\Models\Ecommerce\Condition::deleteCondition($client,$data) ;
                if($Store    == false){
                    return response([
                        "status" => 403,
                        "message" => __("Invalid Input Data"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "message"       => __("Deleted Successfully")    
                ] ; 
            }else{
                return response([
                    "status" => 403,
                    "message" => __("Invalid Data"),
                ],403);
            }
        }
        // 4** store install
        public static function StoreInstallment($data,$request) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $Store        = \App\Models\Ecommerce\Installment::storeInstallment($client,$data,$request) ;
                if($Store    == false){
                    return response([
                        "status" => 403,
                        "message" => __("Invalid Input Data"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "message"       => __("Added Successfully")    
                ] ; 
            }else{
                return response([
                    "status" => 403,
                    "message" => __("Invalid Data"),
                ],403);
            }
        }
        // 5** update install
        public static function UpdateInstallment($data,$request) {
            if(count($data)>0){
                $client       = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $Store        = \App\Models\Ecommerce\Installment::updateInstallment($client,$data,$request) ;
                if($Store    == false){
                    return response([
                        "status" => 403,
                        "message" => __("Invalid Input Data"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "message"       => __("Updated Successfully")    
                ] ; 
            }else{
                return response([
                    "status" => 403,
                    "message" => __("Invalid Data"),
                ],403);
            }
        }
        // 6** delete install
        public static function DeleteInstallment($data) {
            if(count($data)>0){
                $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                if(!$client && $data["token"] == ""){
                    return response([
                        "status"   => 401 ,
                        "message" => __('Sorry ,You Should Login To Your Account') ,
                    ],401);
                }
                $Store        = \App\Models\Ecommerce\Installment::deleteInstallment($client,$data) ;
                if($Store    == false){
                    return response([
                        "status" => 403,
                        "message" => __("Invalid Input Data"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "message"       => __("Deleted Successfully")    
                ] ; 
            }else{
                return response([
                    "status" => 403,
                    "message" => __("Invalid Data"),
                ],403);
            }
        }
    // ....................................

    // Software
    // ....................................

        // 1*** Store page
        public static function software($data,$request) {
            if(count($data)>0){
                // $client  = \App\Models\e_commerceClient::where("api_token",$data["token"])->first();
                // if(!$client && $data["token"] == ""){
                //     return response([
                //         "status" => 403,
                //         "message" => __("Token Expire"),
                //     ],403);
                // }
                $StorePage  = \App\Product::Software($data,$request) ;
                if($StorePage == false){
                    return response([
                        "status" => 403,
                        "message" => __("Invalid Input Data"),
                    ],403);
                }
                return $output = [
                    "status"         => 200,
                    "items"          => $StorePage,
                    "message"       => __("Store Page Access Successfully")    
                ] ; 
            }else{
                return response([
                    "status" => 403,
                    "message" => __("Invalid Data"),
                ],403);
            }
        }
    // ....................................



    //*----------------------------------------*\\
    //*---1----- references  bill -------------*\\
    //******************************************\\
    public static function setAndGetReferenceCount($type,$business_id,$pattern=NULL)
    {
        $ref = \App\ReferenceCount::where('ref_type', $type)
                          ->where('business_id', $business_id)
                          ->where('pattern_id', $pattern)
                          ->first();
        if (!empty($ref)) {
            $ref->ref_count += 1;
            $ref->save();
            return $ref->ref_count;
        } else {
            $new_ref = \App\ReferenceCount::create([
                'ref_type' => $type,
                'business_id' => $business_id,
                'pattern_id' => $pattern,
                'ref_count' => 1
            ]);
            return $new_ref->ref_count;
        }
    }
    //*----------------------------------------*\\
    //*---2----- references  bill -------------*\\
    //******************************************\\
    public static function generateReferenceNumber($type, $ref_count, $business_id = null, $default_prefix = null,$pattern =null)
    {
        if (!empty($default_prefix)) {
            $prefix = $default_prefix;
        }
        $ref_digits =  str_pad($ref_count, 5, 0, STR_PAD_LEFT);
        if(!isset($prefix)){
                $prefix = "";
        }
        if (!in_array($type, ['contacts', 'business_location', 'username' ,"supplier","customer"   ])) {
            $ref_year = \Carbon::now()->year;
           
            $ref_number = $prefix . $ref_year . '/' . $ref_digits;
            
        } else {
             
            $ref_number = $prefix . $ref_digits;
        }
        return  $ref_number;
    }
    //*----------------------------------------*\\
    //*---3----- JWT token check expire -------*\\
    //******************************************\\
    public static function CheckTokenExpire($token){
        try {
            $headers  = ['16' => 'izo']; // Define your headers
            $check    = JWT::decode($token,$headers); 
            // dd($token);
            // Handle token expiration
        } catch (\Firebase\JWT\ExpiredException $e) {
            // The token has expired
             return dd(1);
        } catch (\Firebase\JWT\BeforeValidException $e) {
                // Handle token not yet valid
                // The token is not yet valid
                return dd(2);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
                // Handle an invalid signature
                // The token's signature is not valid
                return dd(3);
        } catch (\Exception $e) {
                // Handle other JWT decoding errors
                // The token is not valid for some reason
                return dd(4);
        }
       
        
        
        if($check == false){
            return false;
        }
        return true;
    }

    // **1** Relation
    public function contact()  {
        return $this->belongsTo("\App\Contact","contact_id");
    }


}
