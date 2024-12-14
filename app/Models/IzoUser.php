<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

require '../vendor/autoload.php';
require_once  '../vendor/autoload.php';

class IzoUser extends Model
{
    use HasFactory,SoftDeletes;
    
    /***
     * create new company in system admin user
     * create new database 
     * create new subdomain
     * 
     */
    public static function saveUser($data) {
        try{
          
            $register                          = new  IzoUser();
            $device                            = $data['User-Agent'];
            $ip                                = $data['ip'];
            $database_prefix                   = 'izo26102024_'; # prefix for naming database
            $database                          = 'izo26102024_'.$data['domain_name'];
            $register->admin_user              = 1;
            $register->company_name            = $data['company_name'];
            $register->mobile                  = $data['mobile_code'].$data['mobile'];
            $register->email                   = $data['email'];
            $register->password                = Hash::make($data['password']);
            $register->status                  = 'Newer';
            $register->database_user           = $database;
            $register->database_name           = $database;
            $register->device_id               = $device;
            $register->ip                      = $ip;
            $register->domain_name             = $data['domain_name'];
            $register->domain_url              = $data['domain_name'].".localhost";
            $register->seats                   = 3; # number of user allowed
            $register->subscribe_date          = \Carbon::now();
            $register->subscribe_expire_date   = \Carbon::now()->addWeeks(3);
            $register->not_active	           = 0;
            #.1...................................................... name of directory
            $new_dir_business_logo             = '/uploads/companies/'.$data["company_name"]."/business_logo";
            $new_dir_documents                 = '/uploads/companies/'.$data["company_name"]."/documents";
            $new_dir_img                       = '/uploads/companies/'.$data["company_name"]."/img";
            $new_dir_logo                      = '/uploads/companies/'.$data["company_name"]."/logo";
            $new_dir_media                     = '/uploads/companies/'.$data["company_name"]."/media";
            $new_dir_payments                  = '/uploads/companies/'.$data["company_name"]."/payments";
            $new_dir_video                     = '/uploads/companies/'.$data["company_name"]."/video";
            
            // $out_dir_payments                  = '../uploads/companies/'.$data["company_name"]."/payments";
            // $out_dir_logo                      = '../uploads/companies/'.$data["company_name"]."/business_logos";
            // $out_dir_documents                 = '../uploads/companies/'.$data["company_name"]."/invoice_logos";
            // $out_dir_img                       = '../uploads/companies/'.$data["company_name"]."/img";
            // $out_dir_media                     = '../uploads/companies/'.$data["company_name"]."/media";
            #.2...................................................... create url directory
            // $out_new_dir_logo                  = public_path($out_dir_logo);
            // $out_new_dir_payments              = public_path($out_dir_payments);
            // $out_new_path_documents            = public_path($out_dir_documents);
            // $out_new_dir_img                   = public_path($out_dir_img);
            // $out_new_dir_media                 = public_path($out_dir_media);

            $new_path_business_logo            = public_path($new_dir_business_logo);
            $new_path_documents                = public_path($new_dir_documents);
            $new_path_img                      = public_path($new_dir_img);
            $new_dir_logo                      = public_path($new_dir_logo);
            $new_path_media                    = public_path($new_dir_media);
            $new_dir_payments                  = public_path($new_dir_payments);
            $new_path_video                    = public_path($new_dir_video);

            #.3...................................................... Create the directory
            (!File::exists($new_path_business_logo))?File::makeDirectory($new_path_business_logo, 0755, true):null; 
            (!File::exists($new_path_documents))?File::makeDirectory($new_path_documents, 0755, true):null; 
            (!File::exists($new_path_img))?File::makeDirectory($new_path_img, 0755, true):null; 
            (!File::exists($new_dir_logo))?File::makeDirectory($new_dir_logo, 0755, true):null; 
            (!File::exists($new_path_media))?File::makeDirectory($new_path_media, 0755, true):null; 
            (!File::exists($new_dir_payments))?File::makeDirectory($new_dir_payments, 0755, true):null; 
            (!File::exists($new_path_video))?File::makeDirectory($new_path_video, 0755, true):null; 
            
            // (!File::exists($out_new_dir_logo))?File::makeDirectory($out_new_dir_logo, 0755, true):null; 
            // (!File::exists($out_new_dir_payments))?File::makeDirectory($out_new_dir_payments, 0755, true):null; 
            // (!File::exists($out_new_path_documents))?File::makeDirectory($out_new_path_documents, 0755, true):null; 
            // (!File::exists($out_new_dir_img))?File::makeDirectory($out_new_dir_img, 0755, true):null; 
            // (!File::exists($out_new_dir_media))?File::makeDirectory($out_new_dir_media, 0755, true):null; 
            #.........................................................
            $query                             = 'CREATE DATABASE '.$database;
            DB::statement($query);
            $register->save();
            $payload = [
                'role'           => $register->admin_user,
                'email'          => $register->email,
                'database'       => $register->database_name,
                'database_user'  => $register->database_user,
                'domain_url'     => $register->domain_url,
                'domain'         => $register->domain_name
            ];
            session(['user_main'  => $payload]);

            // $save = false;
            $to         = 'iebrahemsai944@gmail.com' ;
            // $to         = 'albaseetcompany8422@gmail.com' ;
            $subject = "Verify Account IzoCloud";
            // $Now   = \Carbon::parse(\Carbon::now()->format('Y-m-d'));
            // $UNTIL = \Carbon::parse(\Carbon::createFromTimestamp($T)->format('Y-m-d'));
            // $day   = $Now->diffInDays($UNTIL)  ;  
            $message = "
                <html>
                <head>
                <title>FUTURE VISION COMPUTERS TR LLC S.P</title>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                </head>
                <header>
                    <h1>IZOCLOUD Version 1.0.1 Verification</h1> <br>
                    <h2>Title : <b>Verification Your Email</b> : ".$data['email']." </h2><br>
                    <img width='100' height='100' alt='izo pos'  src='https://agt.izocloud.com/public/uploads/POS.ico'>
                </header>
                <body style='text-align:left'>
                    <div style='width:100%; border-bottom:3px solid #ee8600; border-radius:0px;padding:10px;' style='text-align:left'>
                        <h3> Company Details :  </h3>
                        <h4> - Name :  ".$data['company_name']."</h4>
                        <h4> - Mobile :  ".$data['mobile_code'].$data['mobile']."</h4>
                        <h4> - email :  ".$data['email']."</h4> 
                        <h3> Verify Email </h3>
                        <b><button>Verify</button></b> 
                    </div>
                </body>
                <footer>
                    <h6>
                        <b>".config('app.name', 'IZO CLOUD ')." - V".config('author.app_version')." | Powered By AGT</b>
                        <b><br> All Rights Reserved | Copyright  &copy; ".date('Y')."  </b>
                        <b><br>Website : izo.ae </b>
                        <b><br>Customer Service : +971-50-1770-199  ,  +971-6-70-44-218</b>
                        
                    </h6>
                </footer>
            
                </html>
            ";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            // More headers
            $headers .= 'From: <alhamwi.agt@gmail.com>' . "\r\n";
            // mail($to,$subject,$message,$headers) ;

            return true;
        }catch(Exception $e){
            return false;
        }
    }

    /***
     * login admin user
     * if user admin for manager of company  go to panel
     * if user employment for company go to izo cloud
     * 
     */
    public static function loginUser($data) {
        try{
            $user        = IzoUser::where('email',$data['email'])->first();
            
            if(!$user){
                return $outPut = [
                    'status'  => false,
                    'message' => 'Email or password is incorrect',
                ];
            }
            if($user->admin_user == 1){
                
            }else{
                
            }
             
            if(!Hash::check($data['password'], $user->password)){
                return $outPut = [  
                    'status'  => false,
                    'message' => 'Email or password is incorrect',
                ];
            }else{
                $payload = [
                    'role'           => $user->admin_user,
                    'email'          => $user->email,
                    'database'       => $user->database_name,
                    'database_user'  => $user->database_user,
                    'domain_url'     => $user->domain_url,
                    'domain'         => $user->domain_name
                ];
                
                session(['user_main'  => $payload]);
                session(['password'   => $data['password']]);
                 
                return   $outPut = [
                    'status'          => true,
                    'url'             => ($user->admin_user)?'/panel-account':'/login-account',
                    'database'        => $user->database_name,
                    'database_user'   => $user->database_user,
                    'domain'          => $user->domain_name,
                    'message'         => 'Login successfully',
                    'admin'           => $user->admin_user,
                    'password'        => $user->password,
                ];
            }
        }catch(Exception $e){
            return $outPut = [
                'status'  => false,
                'message' => 'SomeThing Wrong',
            ];
        }
    }
}
