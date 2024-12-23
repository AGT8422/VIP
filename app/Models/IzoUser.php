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
            $register->domain_url              = $data['domain_name'].".izocloud.com";
            $register->seats                   = 2; # number of user allowed
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
                'mobile'         => $register->mobile,
                'email'          => $register->email,
                'database'       => $register->database_name,
                'database_user'  => $register->database_user,
                'domain_url'     => $register->domain_url,
                'domain'         => $register->domain_name,
                'have_business'  => $register->have_business,
                'seats'          => $register->seats
            ];
            session(['user_main'  => $payload]);

           

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
                    'mobile'         => $user->mobile,
                    'email'          => $user->email,
                    'database'       => $user->database_name,
                    'database_user'  => $user->database_user,
                    'domain_url'     => $user->domain_url,
                    'domain'         => $user->domain_name,
                    'have_business'  => $user->have_business,
                    'seats'          => $user->seats
                ];
                
                session(['user_main'  => $payload]);
                session(['password'   => $data['password']]);
                 
                return   $outPut = [
                    'status'          => true,
                    'url'             => ($user->admin_user == 1)?'/panel-account':'/panel-account',
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
