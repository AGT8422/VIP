<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Twilio\Jwt\JWT; 
use Illuminate\Support\Facades\Hash;
 use App\Utils\ProductUtil;
use App\Mail\ExceptionOccured;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerfyEmail;

// ...
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// ......

require '../vendor/autoload.php';
require_once  '../vendor/autoload.php';



class UserActivation extends Model
{


    use HasFactory;
    protected $customClaims;
    public function __construct() {
       
        $this->customClaims = array();
    }
    

    public static function post_add($data) {
        
        \DB::beginTransaction();
            
        $time = ($data['user_period'] != 0 && $data['user_period'] > 0)?$data['user_period']:1; // .. for one year
        $TM   = 3600*24*$time;
        $T    = \time() + $TM ;
        $UserActivation = \App\Models\UserActivation::select("*")->get()  ;
       
        $Dt                           = new UserActivation;
        $FIRST    =  rand(4, 9999) ;
        $SECOND   =  rand(3, 999) ;
        $THIRD    =  rand(4, 9999) ;
        $FOURTH   =  rand(3, 999) ;
        $PASSWORD =  $FIRST . "-" . $SECOND . "-" .$THIRD . "-" . $FOURTH ;
        
        $check    = UserActivation::check($PASSWORD,$UserActivation);
        while($check != 0){
            $FIRST    =  rand(4, 9999) ;
            $SECOND   =  rand(3, 999) ;
            $THIRD    =  rand(4, 9999) ;
            $FOURTH   =  rand(3, 999) ;
            $PASSWORD =  $FIRST . "-" . $SECOND . "-" .$THIRD . "-" . $FOURTH ;
            $check    = UserActivation::check($PASSWORD,$UserActivation);
        }
        $customClaims["name"] = $data['user_username'];
        $payload  = \array_merge($customClaims, array(
            "password" => $PASSWORD,        
            "exp"      => $T,
        )); 

        $Dt->user_name                = $data['user_name'];        
        $Dt->user_email               = $data['user_email'];        
        $Dt->user_address             = $data['user_address'];        
        $Dt->company_name             = $data['company_name'];        
        $Dt->user_mobile              = $data['user_mobile'];        
        $Dt->user_number_device       = $data['user_number_device'];        
        $Dt->user_username            = $data['user_username'];        
        $Dt->user_token               = JWT::encode($payload, "sss", 'HS256');        
        // $Dt->user_password            = $PASSWORD;        
        $Dt->user_password            = Hash::make($PASSWORD);        
        // $Dt->user_status              = $data['user_status'];        
        $Dt->user_service             = $data['user_service'];        
        // $Dt->user_products            = $data['user_products'];        
        $Dt->user_dateactivate        = $data['user_date'];        
        $Dt->activation_period        = $T;
        
  
        // ** email section   
        $mail                         = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug  = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            // $mail->Host       = 'sandbox.smtp.mailtrap.io';                     //Set the SMTP server to send through
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            // $mail->Username   = '26ba541e9256f2';                     //SMTP username
            // $mail->Password   = '06ed0ff8f2e290';                               //SMTP password
            $mail->Username   = 'alhamwi.agt@gmail.com';                     //SMTP username
            $mail->Password   = 'fmhmlparvdssqovw';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption ENCRYPTION_SMTPS 465
            $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            // Recipients
            $mail->setFrom('alhamwi.agt@gmail.com', 'AGT - Al Hamwi General Trading');
            // $mail->addAddress('albaseetcompany8422@gmail.com', 'Ebrahem Sai');     //Add a recipient
            $mail->addAddress('iebrahemsai944@gmail.com');               //Name is optional
            // $mail->addAddress('izocloud@outlook.com');               //Name is optional
            // $mail->addAddress('osama.hamwi@live.com');               //Name is optional
            $mail->addReplyTo('iebrahemsai944@gmail.com', 'Information 123');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');

            // Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            // Content
            // $mail->isHTML(true);                                  //Set email format to HTML
            // $mail->Subject = 'IZO-POS Version 2.2 Activation Code';
            // $mail->Body    = 'Hi Administrator , The Device That Have ID-NUMBER : ' . $data['user_username']. "\nMAKE Activation For this version v2.2 from IZO-POS Application Customer Information :  \n Name : " .$data['user_name']. " \n Email : ".$data['user_email']." \n Mobile : ".$data['user_mobile']. " \n <b>Activation Code :</b> ".$PASSWORD;
            // $mail->AltBody = 'MAKE Activation For this version v2.2 from IZO-POS Application';
            // $mail->send();

            // Create the Transport
            // $transport = (new \Swift_SmtpTransport('smtp.gmail.com', 25))
            //         ->setUsername('alhamwi.agt@gmail.com')
            //         ->setPassword('fmhmlparvdssqovw')
            //         ;

            // // Create the Mailer using your created Transport
            // $mailer = new \Swift_Mailer($transport);

            // // Create a message
            // $message = (new \Swift_Message(' IZO-POS Activation Code '))
            // ->setFrom(['alhamwi.agt@gmail.com' => 'AGT Al Hamwi General Trading '])
            // ->setTo(['albaseetcompany8422@gmail.com', 'iebrahemsai944@gmail.com' => 'Ebraheem'])
            // ->setBody('Hi Administrator , Activation Code 9999 ')
            // ;

            // // // Send the message
            // $mailer->send($message);

            // $to      = 'alhamwi.agt@gmail.com' ;
            $Now     = \Carbon::parse(\Carbon::now()->format('Y-m-d'));
            $UNTIL   = \Carbon::parse(\Carbon::createFromTimestamp($T)->format('Y-m-d'));
            $day     = $Now->diffInDays($UNTIL)  ;   
            $to      = 'iebrahemsai944@gmail.com' ;
            $subject = "Activation Code";
           
            $message = "
            <html>
            <head>
            <title>FUTURE VISION COMPUTERS TR LLC S.P</title>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            </head>
            <header>
                <h1>IZO-POS Version 1.0.55 Activation</h1> <br>
                <h2>Title : <b>Activation User</b> : ".$Dt->user_name." </h2><br>
                <img width='100' height='100' alt='izo pos'  src='https://agt.izocloud.com/public/uploads/POS.ico'>
            </header>
            <body style='text-align:left'>
                <div style='width:100%; border-bottom:3px solid #ee8600; border-radius:0px;padding:10px;' style='text-align:left'>
                    <h3> Company Details :  </h3>
                    <h4> - Name :  ".$Dt->company_name."</h4>
                    <h4> - Mobile :  ".$Dt->user_mobile."</h4>
                    <h4> - email :  ".$Dt->user_email."</h4>
                    <h4> - Devices :  ".$Dt->user_number_device."</h4>
                    <h4> - Expire After :  ".$day." Days</h4>
                    <h3> Activation Code </h3>
                    <b>".$PASSWORD."</b> 
                </div>
            </body>
            <footer>
                <h6>
                    <b>".config('app.name', 'IZO CLOUD ')." - V".config('author.app_version')." | Powered By AGT</b>
                    <b><br> All Rights Reserved | Copyright  &copy; ".date('Y')."  </b>
                    <b><br>Website : izo.ae </b>
                    <b><br>Customer Service : +971-56-777-9250  ,  +971-4-23-55-919</b>
                    
                </h6>
            </footer>
        
            </html>
        ";
            $customer_message = "
                <html>
                <head>
                <title>FUTURE VISION COMPUTERS TR LLC S.P</title>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                </head>
                <header>
                    <h1>IZO-POS Version 1.0.55 Activation</h1> <br>
                    <img width='100' height='100' alt='izo pos'  src='https://agt.izocloud.com/public/uploads/POS.ico'>
                    <h2>Dear : ".$Dt->user_name." </b> :</h2><br>
                </header>
                <body style='text-align:left'>
                    <div style='width:100%; border-bottom:3px solid #ee8600; border-radius:0px;padding:10px;' style='text-align:left'>
                        <h3> Your Company Details :  </h3>
                        <h4> - Name :  ".$Dt->company_name."</h4>
                        <h4> - Mobile :  ".$Dt->user_mobile."</h4>
                        <h4> - email :  ".$Dt->user_email."</h4>
                        <h4> - Devices :  ".$Dt->user_number_device."</h4>
                        <h4> - Expire After :  ".$day." Days</h4>
                    </div>
                </body>
                <footer>
                    <h6>
                        <b>ALHAMWI GENERAL TRADING L.L.C </b> 
                        <b><br>Website : izo.ae </b>
                        <b><br>Customer Service : +971-56-777-9250  ,  +971-4-23-55-919</b>
                        
                    </h6>
                </footer>
            
                </html>
            ";
            $customer_to  = $Dt->user_email ;
            // // Always set content-type when sending HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            // More headers
            $headers .= 'From: <alhamwi.agt@gmail.com>' . "\r\n";
            $headers .= 'Cc: izoclouduae@gmail.com' . "\r\n";
            
            mail($to,$subject,$message,$headers);
            mail($customer_to,$subject,$customer_message,$headers);

            
            $Dt->save();
            \DB::commit();
            return $output = [
                 "success" => 1,
                 "msg"    =>  "Message has been sent"
              ];
        } catch (Exception $e) {
            return $output = [
                      "success" => 0,
                       "msg"    =>  "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"
                    ];
        }
        // $data = [
        //     "title"    => "IZO-POS Version 2.2 Activation Code",
        //     "messages" => "Hi Administrator , The Device That Have ID-NUMBER : ".$data['user_username']." \nMAKE Activation For this version v2.2 from IZO-POS Application \nCustomer Information : \nName : " .$data['user_name']. "\nEmail : ".$data['user_email']."\nMobile : ".$data['user_mobile']. "\nActivation Code : ".$PASSWORD." . ",
        // ];
        // Mail::to("albaseetcompany8422@gmail.com")->send(new VerfyEmail($data));  
         

     

        


    }

    public static function check($PASSWORD,$UserActivation)  {
        $check = 0;
        if(count($UserActivation) > 0){
            foreach($UserActivation as $it){
                if(Hash::check($it->user_password,$PASSWORD)){
                    $check = 1;
                } 
            }
        }         
        return $check;
    }

    
}
