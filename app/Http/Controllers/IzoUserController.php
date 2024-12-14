<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IzoUser;
use App\Utils\BusinessUtil;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Config; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail; 
use App\Utils\ModuleUtil;
use Artisan;
use Illuminate\Support\Facades\Cookie;

use Illuminate\Support\Facades\Http;


class IzoUserController extends Controller
{

    use AuthenticatesUsers;

    protected $businessUtil;
    protected $moduleUtil;
 
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:api')->except('logout');
        $this->businessUtil = $businessUtil;
        $this->moduleUtil   = $moduleUtil;
    }

    


    /**
     * login.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginPageRedirect(Request $request,$id)
    {
        //
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #....................................
        $email                         = null;
        $password                      = null;
        $list_domains                  = [];
        $list_domain  = IzoUser::pluck("domain_url"); 
        foreach($list_domain as $li){
            if($li != null){
                $list_domains[] = $li;
            }
        } 
        #................................................
        if($id){ 
            $id = Crypt::decryptString($id);
            // Replace underscores with ampersands
            $queryString = str_replace('_', '&', $id);
            // Parse the query string into an associative array
            parse_str($queryString, $output);   
            $email    = (isset($output['email']))?$output['email']:null;
            $password = (isset($output['password']))?$output['password']:null;
        }  
        #................................................
        
        if(session()->has('user_main')){
         
            if(request()->session()->get('startLogin')){
                // return redirect('/login');
            }
            return redirect('/panel-account');
        }
        #................................................
        return view('izo_user.login')->with(compact(['list_domains','email','password']));
    }
    /**
     * login.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginPage(Request $request)
    {
        //
       
        \Config::set('session.driver','database');
        if(!$request->session()->get('user')){
            if ($request->session()->get('startLogin')) {
                // return redirect('/panel-account');
            }
        }
        
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #....................................
        $email                         = null;
        $password                      = null;
        $list_domains                  = [];
        $list_domain  = IzoUser::pluck("domain_url"); 
        foreach($list_domain as $li){
            if($li != null){
                $list_domains[] = $li;
            }
        } 
        #................................................
        
        $url                           = request()->session()->get('url.intended');
        if($url != null){$parsed_url               = parse_url($url);}else{$parsed_url = [];}
        if(isset($parsed_url['query'])){
            parse_str($parsed_url['query'], $query_params);
            if(isset($query_params['email'])){
                $email    = $query_params['email'];
                $password = $query_params['password'];
            }
        } 
        
        #................................................
        if(session()->has('user_main')){
            if(request()->session()->get('startLogin')){
                // return redirect('/login');
            } 
            return redirect('/panel-account');
        }
        
        #................................................
        return view('izo_user.login')->with(compact(['list_domains','email','password']));
    }
    /**
     * forget password.
     *
     * @return \Illuminate\Http\Response
     */
    public function forgetPassword(Request $request)
    {
        //
        
        
         
        if(!$request->session()->get('user')){
            if ($request->session()->get('startLogin')) {
                // return redirect('/panel-account');
            }
        }
         
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #....................................
        $email                         = null;
        $password                      = null;
        $list_domains                  = [];
        $list_domain  = IzoUser::pluck("domain_url"); 
        foreach($list_domain as $li){
            if($li != null){
                $list_domains[] = $li;
            }
        } 
        #................................................
        
        // $url                           = request()->session()->get('url.intended');
        // $parsed_url                    = parse_url($url);
        // if(isset($parsed_url['query'])){
        //     parse_str($parsed_url['query'], $query_params);
        //     if(isset($query_params['email'])){
        //         $email    = $query_params['email'];
        //         $password = $query_params['password'];
        //     }
        // } 
       
        #................................................
        if(session()->has('user_main')){
            if(request()->session()->get('startLogin')){
                // return redirect('/login');
            }
            return redirect('/panel-account');
        }
        #................................................
        return view('izo_user.forget_password')->with(compact(['list_domains']));
    }
    
    /**
     * Change authentication from email to username
     *
     * @return void
     */
    public function username()
    {
        return 'email';
    }
    
    /**
     * register.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    { 
        //
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #.................................... 
        $list_domains                  = [];
        $list_domain  = IzoUser::pluck("domain_url"); 
        foreach($list_domain as $li){
            if($li != null){
                $list_domains[] = $li;
            }
        } 
        #................................................
        return view('izo_user.register')->with(compact(['list_domains']));
    }

    /**
     * company register.
     *
     * @return \Illuminate\Http\Response
     */
    public function createCompany(Request $request)
    {
        //
        try{
            $businessUtil  = new BusinessUtil();
            DB::beginTransaction();
            // DD(request()->session()->get('user_main'));

           
            Config::set('database.connections.mysql.database', "izocloud");
            DB::purge('mysql');
            DB::reconnect('mysql');
            $izoCustomer   = IzoUser::where("email",request()->session()->get('user_main.email'))->first(); 
            $databaseName  = request()->session()->get('user_main.database') ;  
            Config::set('database.connections.mysql.database', $databaseName);
            DB::purge('mysql');
            DB::reconnect('mysql');
            $owner_details = $request->only(['first_name','second_name','language','currency']);
            #.......................................Create User
            $owner_details['last_name']     = $owner_details['second_name'];
            $owner_details['surname']       = "";
            $owner_details['username']      = request()->session()->get('user_main.email');
            $owner_details['email']         = request()->session()->get('user_main.email');
            $owner_details['password']      = $izoCustomer->password;
            $owner_details['language']      = empty($owner_details['language']) ? config('app.locale') : $owner_details['language'];
            // dd($owner_details);
            $user                           = User::create_user($owner_details,true);
            #.......................................Create Business
            $business_details = $request->only(['name', 'start_date', 'currency_id', 'time_zone']);
            $business_details['name']           = request()->session()->get('user_main.domain');
            $business_details['fy_start_month'] = 1;
            $business_details['owner_id']       = $user->id;
            $business_details['currency_id']    = $owner_details['currency'];
            if (!empty($business_details['start_date'])) {
                $business_details['start_date'] = \Carbon::createFromFormat(config('constants.default_date_format'), $business_details['start_date'])->toDateString();
            }
            #.......................................Create BusinessLocation
            $business_location                       = $request->only(['name', 'country',  'zip_code']);
            $business_location['name']               = request()->session()->get('user_main.domain');
            $business_location['landmark']           = "";
            $business_location['website']            = "";
            $business_location['mobile']             = "";
            $business_location['state']              = "";
            $business_location['city']               = "";  
            $business_location['alternate_number']   = "";
            $business_details['enabled_modules']     =["purchases","add_sale","pos_sale","stock_transfers","stock_adjustment","expenses","account","tables","modifiers","service_staff","booking","kitchen","Warehouse","subscription","types_of_service"];
            
            $business = $businessUtil->createNewBusiness($business_details);
           
            #.......................................Update user with business id
            $user->username     = request()->session()->get('user_main.email');
            $user->business_id  = $business->id;
            $user->is_admin_izo = 1;
            $user->update();
            #.......................................Create Location
            $businessUtil->newBusinessDefaultResources($business->id, $user->id);
            $new_location = $businessUtil->addLocation($business->id, $business_location);
            # .......................................create new permission with the new location
            Permission::create(['name' => 'location.' . $new_location->id ]);
            # .......................................Create Currency 
            $currency_details = $request->only(["currency_id"]);
            $exchange_rate    = \App\Models\ExchangeRate::where("currency_id",$currency_details)->where("source",1)->first();
            if(!empty($exchange_rate)){
                $exchange_rate->business_id     = $business->id;
                $exchange_rate->currency_id     = $business->currency_id;
                $exchange_rate->amount          = 1;
                $exchange_rate->opposit_amount  = 1;
                $exchange_rate->date            = \Carbon::now();
                $exchange_rate->default         = 0;
                $exchange_rate->source          = 1;
                $exchange_rate->update();
            }else{
                $exc                  = new \App\Models\ExchangeRate;
                $exc->business_id     = $business->id;
                $exc->currency_id     = $business->currency_id;
                $exc->amount          = 1;
                $exc->opposit_amount  = 1;
                $exc->date            = \Carbon::now();
                $exc->source          = 1;
                $exc->default         = 0;
                $exc->save();
            }  
            $payload =  [
                "value1" => Hash::make("success"),
                "value2" => $user->password,
            ];
            session(['startLogin'=> $payload ]);
            DB::commit();
            $outPut = [
                "success" => 1,
                "message" => __('Register Successfully'),
            ];
            return redirect("/login")->with("status",$outPut);
        }catch(Exception $e){
            DB::rollBack();
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $outPut = [
                "success" => 0,
                "message" => __('Failed'),
            ];
          return   back()->with('status', $output)->withInput();
        }
    }

    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function panel(Request $request)
    {
        // 
        $businessUtil  = new BusinessUtil();
        $timeZone      = [
            'Africa/Abidjan' => 'Ivory Coast Time',
            'Africa/Accra' => 'Ghana Time',
            'Africa/Addis_Ababa' => 'East Africa Time',
            'Africa/Algiers' => 'Central European Time',
            'Africa/Asmara' => 'East Africa Time',
            'Africa/Bamako' => 'Ghana Time',
            'Africa/Bangui' => 'West Africa Time',
            'Africa/Banjul' => 'Greenwich Mean Time',
            'Africa/Banjul' => 'Greenwich Mean Time',
            'Africa/Harare' => 'Central Africa Time',
            'Africa/Johannesburg' => 'South Africa Standard Time',
            'Africa/Kampala' => 'East Africa Time',
            'Africa/Lagos' => 'West Africa Time',
            'Africa/Luanda' => 'West Africa Time',
            'Africa/Nairobi' => 'East Africa Time',
            'Africa/Tripoli' => 'Eastern European Time',
            'Africa/Windhoek' => 'Central Africa Time',
            'America/Adak' => 'Hawaii-Aleutian Standard Time',
            'America/Anchorage' => 'Alaska Standard Time',
            'America/Argentina/Buenos_Aires' => 'Argentina Time',
            'America/Argentina/Catamarca' => 'Argentina Time',
            'America/Argentina/ComodRivadavia' => 'Argentina Time',
            'America/Argentina/La_Rioja' => 'Argentina Time',
            'America/Argentina/Mendoza' => 'Argentina Time',
            'America/Argentina/Rio_Gallegos' => 'Argentina Time',
            'America/Argentina/Salta' => 'Argentina Time',
            'America/Argentina/San_Juan' => 'Argentina Time',
            'America/Argentina/San_Luis' => 'Argentina Time',
            'America/Argentina/Tucuman' => 'Argentina Time',
            'America/Argentina/Ushuaia' => 'Argentina Time',
            'America/Chicago' => 'Central Standard Time',
            'America/Denver' => 'Mountain Standard Time',
            'America/Los_Angeles' => 'Pacific Standard Time',
            'Asia/Almaty' => 'Kazakhstan Time',
            'Asia/Amman' => 'Jordan Time',
            'Asia/Anadyr' => 'Kamchatka Time',
            'Asia/Aqtau' => 'Kazakhstan Time',
            'Asia/Aqtobe' => 'Kazakhstan Time',
            'Asia/Ashgabat' => 'Turkmenistan Time',
            'Asia/Baghdad' => 'Arabian Standard Time',
            'Asia/Bahrain' => 'Arabian Standard Time',
            'Asia/Baku' => 'Azerbaijan Time',
            'Asia/Bangkok' => 'Indochina Time',
            'Asia/Barnaul' => 'Altai Time',
            'Asia/Beirut' => 'Lebanon Time',
            'Asia/Bishkek' => 'Kyrgyzstan Time',
            'Asia/Brunei' => 'Brunei Time',
            'Asia/Calcutta' => 'Indian Standard Time',
            'Asia/Chita' => 'Transbaikal Time',
            'Asia/Choibalsan' => 'Ulaanbaatar Time',
            'Asia/Colombo' => 'Sri Lanka Time',
            'Asia/Damascus' => 'Syria Time',
            'Asia/Dhaka' => 'Bangladesh Time',
            'Asia/Dili' => 'Timor Leste Time',
            'Asia/Dubai' => 'Gulf Standard Time',
            'Asia/Dushanbe' => 'Tajikistan Time',
            'Asia/Gaza' => 'Palestine Time',
            'Asia/Hong_Kong' => 'Hong Kong Time',
            'Asia/Hovd' => 'Hovd Time',
            'Asia/Irkutsk' => 'Irkutsk Time',
            'Asia/Jakarta' => 'Western Indonesia Time',
            'Asia/Jayapura' => 'Eastern Indonesia Time',
            'Asia/Kabul' => 'Afghanistan Time',
            'Asia/Karachi' => 'Pakistan Standard Time',
            'Asia/Kathmandu' => 'Nepal Time',
            'Asia/Kolkata' => 'Indian Standard Time',
            'Asia/Krasnoyarsk' => 'Krasnoyarsk Time',
            'Asia/Kuala_Lumpur' => 'Malaysia Time',
            'Asia/Kuwait' => 'Arabian Standard Time',
            'Asia/Macau' => 'Macau Standard Time',
            'Asia/Magadan' => 'Magadan Time',
            'Asia/Makassar' => 'Central Indonesia Time',
            'Asia/Manila' => 'Philippine Time',
            'Asia/Muscat' => 'Gulf Standard Time',
            'Asia/Nicosia' => 'Eastern European Time',
            'Asia/Novosibirsk' => 'Novosibirsk Time',
            'Asia/Omsk' => 'Omsk Time',
            'Asia/Oral' => 'Oral Time',
            'Asia/Phnom_Penh' => 'Indochina Time',
            'Asia/Pontianak' => 'Western Indonesia Time',
            'Asia/Qatar' => 'Arabian Standard Time',
            'Asia/Qyzylorda' => 'Kazakhstan Time',
            'Asia/Riyadh' => 'Arabian Standard Time',
            'Asia/Sakhalin' => 'Sakhalin Time',
            'Asia/Samarkand' => 'Uzbekistan Time',
            'Asia/Seoul' => 'Korea Standard Time',
            'Asia/Shanghai' => 'China Standard Time',
            'Asia/Singapore' => 'Singapore Standard Time',
            'Asia/Taipei' => 'Taipei Time',
            'Asia/Tashkent' => 'Uzbekistan Time',
            'Asia/Tbilisi' => 'Georgia Time',
            'Asia/Tehran' => 'Iran Standard Time',
            'Asia/Thimphu' => 'Bhutan Time',
            'Asia/Tokyo' => 'Japan Standard Time',
            'Asia/Ulaanbaatar' => 'Ulaanbaatar Time',
            'Asia/Urumqi' => 'China Standard Time',
            'Asia/Vientiane' => 'Indochina Time',
            'Asia/Vladivostok' => 'Vladivostok Time',
            'Asia/Yakutsk' => 'Yakutsk Time',
            'Asia/Yangon' => 'Myanmar Time',
            'Asia/Yekaterinburg' => 'Yekaterinburg Time',
            'Asia/Yerevan' => 'Armenia Time',
            'Australia/Adelaide' => 'Australian Central Standard Time',
            'Australia/Brisbane' => 'Australian Eastern Standard Time',
            'Australia/Darwin' => 'Australian Central Standard Time',
            'Australia/Hobart' => 'Australian Eastern Standard Time',
            'Australia/Melbourne' => 'Australian Eastern Standard Time',
            'Australia/Perth' => 'Australian Western Standard Time',
            'Australia/Sydney' => 'Australian Eastern Standard Time',
            'Europe/Amsterdam' => 'Central European Time',
            'Europe/Andorra' => 'Central European Time',
            'Europe/Athens' => 'Eastern European Time',
            'Europe/Belgrade' => 'Central European Time',
            'Europe/Berlin' => 'Central European Time',
            'Europe/Brussels' => 'Central European Time',
            'Europe/Bucharest' => 'Eastern European Time',
            'Europe/Budapest' => 'Central European Time',
            'Europe/Chisinau' => 'Moldova Time',
            'Europe/Copenhagen' => 'Central European Time',
            'Europe/Dublin' => 'Irish Standard Time',
            'Europe/Helsinki' => 'Eastern European Time',
            'Europe/Istanbul' => 'Turkey Time',
            'Europe/Kiev' => 'Eastern European Time',
            'Europe/Lisbon' => 'Western European Time',
            'Europe/London' => 'Greenwich Mean Time',
            'Europe/Luxembourg' => 'Central European Time',
            'Europe/Madrid' => 'Central European Time',
            'Europe/Minsk' => 'Minsk Time',
            'Europe/Monaco' => 'Central European Time',
            'Europe/Moscow' => 'Moscow Time',
            'Europe/Oslo' => 'Central European Time',
            'Europe/Paris' => 'Central European Time',
            'Europe/Prague' => 'Central European Time',
            'Europe/Riga' => 'Eastern European Time',
            'Europe/Rome' => 'Central European Time',
            'Europe/Sofia' => 'Eastern European Time',
            'Europe/Stockholm' => 'Central European Time',
            'Europe/Tallinn' => 'Eastern European Time',
            'Europe/Vienna' => 'Central European Time',
            'Europe/Vilnius' => 'Eastern European Time',
            'Europe/Warsaw' => 'Central European Time',
            'Europe/Zagreb' => 'Central European Time',
            'Europe/Zaporozhye' => 'Eastern European Time',
        ]; 
         
        $currency     = $businessUtil->allCurrencies();
        if(count($currency)==0){
        $currency     = [
           "1"  => "Albania (ALL) ",
           "2"  => "America (USD) ",
           "3"  => "Afghanistan (AF) ",
           "4"  => "Argentina (ARS) ",
           "5"  => "Aruba (AWG) ",
           "6"  => "Australia (AUD) ",
           "7"  => "Azerbaijan (AZ) ",
           "8"  => "Bahamas (BSD) ",
           "9"  => "Barbados (BBD) ",
           "10"  => "Belarus (BYR) ",
           "11"  => "Belgium (EUR) ",
           "12"  => "Beliz (BZD) ",
           "13"  => "Bermuda (BMD) ",
           "14"  => "Bolivia (BOB) ",
           "15"  => "Bosnia and Herzegovina (BAM) ",
           "16"  => "Botswana (BWP) ",
           "17"  => "Bulgaria (BG) ",
           "18"  => "Brazil (BRL) ",
           "19"  => "Britain [United Kingdom] (GBP) ",
           "20"  => "Brunei Darussalam (BND) ",
           "21"  => "Cambodia (KHR) ",
           "22"  => "Canada (CAD) ",
           "23"  => "Cayman Islands (KYD) ",
           "24"  => "Chile (CLP) ",
           "25"  => "China (CNY) ",
           "26"  => "Colombia (COP) ",
           "27"  => "Costa Rica (CRC) ",
           "28"  => "Croatia (HRK) ",
           "29"  => "Cuba (CUP) ",
           "30"  => "Cyprus (EUR) ",
           "31"  => "Czech Republic (CZK) ",
           "32"  => "Denmark (DKK) ",
           "33"  => "Dominican Republic (DOP ) ",
           "34"  => "East Caribbean (XCD) ",
           "35"  => "Egypt (EGP) ",
           "36"  => "El Salvador (SVC) ",
           "37"  => "England [United Kingdom] (GBP) ",
           "38"  => "Euro (EUR) ",
           "39"  => "Falkland Islands (FKP) ",
           "40"  => "Fiji (FJD) ",
           "41"  => "France (EUR) ",
           "42"  => "Ghana (GHS) ",
           "43"  => "Gibraltar (GIP) ",
           "44"  => "Greece (EUR) ",
           "45"  => "Guatemala (GTQ) ",
           "46"  => "Guernsey (GGP) ",
           "47"  => "Guyana (GYD) ",
           "48"  => "Holland [Netherlands] (EUR) ",
           "49"  => "Honduras (HNL) ",
           "50"  => "Hong Kong (HKD) ",
           "51"  => "Hungary (HUF) ",
           "52"  => "Iceland (ISK) ",
           "53"  => "India (INR) ",
           "54"  => "Indonesia (IDR) ",
           "55"  => "Iran (IRR) ",
           "56"  => "Ireland (EUR) ",
           "57"  => "Isle of Man (IMP) ",
           "58"  => "Israel (ILS) ",
           "59"  => "Italy (EUR) ",
           "60"  => "Jamaica (JMD) ",
           "61"  => "Japan (JPY) ",
           "62"  => "Jersey (JEP) ",
           "63"  => "Kazakhstan (KZT) ",
           "64"  => "Korea [North] (KPW) ",
           "65"  => "Korea [South] (KRW) ",
           "66"  => "Kyrgyzstan (KGS) ",
           "67"  => "Laos (LAK) ",
           "68"  => "Latvia (LVL) ",
           "69"  => "Lebanon (LBP) ",
           "70"  => "Liberia (LRD) ",
           "71"  => "Liechtenstein (CHF) ",
           "72"  => "Lithuania (LTL) ",
           "73"  => "Luxembourg (EUR) ",
           "74"  => "Macedonia (MKD) ",
           "75"  => "Malaysia (MYR) ",
           "76"  => "Malta (EUR) ",
           "77"  => "Mauritius (MUR) ",
           "78"  => "Mexico (MXN) ",
           "79"  => "Mongolia (MNT) ",
           "80"  => "Mozambique (MZ) ",
           "81"  => "Namibia (NAD) ",
           "82"  => "Nepal (NPR) ",
           "83"  => "Netherlands Antilles (ANG) ",
           "84"  => "Netherlands (EUR) ",
           "85"  => "New Zealand (NZD) ",
           "86"  => "Nicaragua (NIO) ",
           "87"  => "Nigeria (NGN) ",
           "88"  => "North Korea (KPW) ",
           "89"  => "Norway (NOK) ",
           "90"  => "Oman (OMR) ",
           "91"  => "Pakistan (PKR) ",
           "92"  => "Panama (PAB) ",
           "93"  => "Paraguay (PYG) ",
           "94"  => "Peru (PE) ",
           "95"  => "Philippines (PHP) ",
           "96"  => "Poland (PL) ",
           "97"  => "Qatar (QAR) ",
           "98"  => "Romania (RO) ",
           "99"  => "Russia (RUB) ",
           "100"  => "Saint Helena (SHP) ",
           "101"  => "Saudi Arabia (?.?) ",
           "102"  => "Serbia (RSD) ",
           "103"  => "Seychelles (SCR) ",
           "104"  => "Singapore (SGD) ",
           "105"  => "Slovenia (EUR) ",
           "106"  => "Solomon Islands (SBD) ",
           "107"  => "Somalia (SOS) ",
           "108"  => "South Africa (ZAR) ",
           "109"  => "South Korea (KRW) ",
           "110"  => "Spain (EUR) ",
           "111"  => "Sri Lanka (LKR) ",
           "112"  => "Sweden (SEK) ",
           "113"  => "Switzerland (CHF) ",
           "114"  => "Suriname (SRD) ",
           "115"  => "Syria (SYP) ",
           "116"  => "Taiwan (TWD) ",
           "117"  => "Thailand (THB) ",
           "118"  => "Trinidad and Tobago (TTD) ",
           "119"  => "Turkey (TRY) ",
           "120"  => "Turkey (TRL) ",
           "121"  => "Tuvalu (TVD) ",
           "122"  => "Ukraine (UAH) ",
           "123"  => "United Kingdom (GBP) ",
           "124"  => "United States of America (USD) ",
           "125"  => "Uruguay (UYU) ",
           "126"  => "Uzbekistan (UZS) ",
           "127"  => "Vatican City (EUR) ",
           "128"  => "Venezuela (VEF) ",
           "129"  => "Vietnam (VND) ",
           "130"  => "Yemen (YER) ",
           "131"  => "Zimbabwe (ZWD) ",
           "132"  => "Iraq (IQD) ",
           "133"  => "Kenya (KES) ",
           "134"  => "Bangladesh (BDT) ",
           "135"  => "Algerie (DZD) ",
           "136"  => "United Arab Emirates (AED) ",
           "137"  => "Uganda (UGX) ",
           "138"  => "Tanzania (TZS) ",
           "139"  => "Angola (AOA) ",
           "140"  => "Kuwait (KWD) ",
           "141"  => "Bahrain (BHD) ",
           "142"  => "Syrian Pound (SOR) ",
            
            ];
        }
        $country      = [
            "US" => "United States",
            "CA" => "Canada",
            "GB" => "United Kingdom",
            "IN" => "India",
            "AU" => "Australia",
            "DE" => "Germany",
            "FR" => "France",
            "IT" => "Italy",
            "ES" => "Spain",
            "BR" => "Brazil",
            "MX" => "Mexico",
            "JP" => "Japan",
            "CN" => "China",
            "RU" => "Russia",
            "ZA" => "South Africa",
            "AR" => "Argentina",
            "KR" => "South Korea",
            "NG" => "Nigeria",
            "EG" => "Egypt",
            "SG" => "Singapore",
            "NZ" => "New Zealand",
            "SE" => "Sweden",
            "NO" => "Norway",
            "FI" => "Finland",
            "DK" => "Denmark",
            "BE" => "Belgium",
            "PL" => "Poland",
            "PT" => "Portugal",
            "GR" => "Greece",
            "CH" => "Switzerland",
            "AT" => "Austria",
            "NL" => "Netherlands",
            "CZ" => "Czech Republic",
            "HU" => "Hungary",
            "RO" => "Romania",
            "TR" => "Turkey",
            "KR" => "South Korea",
            "PH" => "Philippines",
            "TH" => "Thailand",
            "ID" => "Indonesia",
            "MY" => "Malaysia",
            "VN" => "Vietnam",
            "PK" => "Pakistan",
            "KE" => "Kenya",
            "PE" => "Peru",
            "CL" => "Chile",
            "CO" => "Colombia",
            "UY" => "Uruguay",
            "EC" => "Ecuador",
            "VE" => "Venezuela",
        ];
        $language     = [];  
        $config_languages = config('constants.langs');
         foreach ($config_languages as $key => $value) {
            $language[$key] = $value['full_name'];
        }
        $company_size = [
            "free_worker"    => "Free worker",
            "1_5_worker"     => "1 - 5 Male/female employee",
            "6_10_worker"    => "6 - 10 Male/female employee",
            "11_50_worker"   => "11 - 50 Male/female employee",
            "51_200_worker"  => "51 - 200 Male/female employee",
            "201_500_worker" => "201 - 500 Male/female employee",
            "500_worker"     => "More Than 500 employee",
        ]; 
        $jobs         = [
            "accounts_officer"           => "Accounts Officer",  
            "sales_officer"              => "Sales Officer",  
            "human_resources_officer"    => "Human Resources Officer",  
            "inventory_officer"          => "Inventory officer",  
            "customer_relations_officer" => "Customer Relations Officer",  
            "ceo"                        => "CEO",  
            "operations_officer"         => "Operations Officer"  
        ]; 
       
        #................................................................
        $url       = request()->root();
        $parsedUrl = parse_url($url);
        $host      = $parsedUrl['host'] ?? '';  
        $hostParts = explode('.', $host); 
        if (count($hostParts) == 3) {
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD
            array_pop($hostParts); // Domain

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else if(count($hostParts) == 2){
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else {
            // No subdomain
            $subdomain = '';

        }
        $subdomain     = $subdomain;  
        if(session()->has('startLogin')){
            if($subdomain == ""){
                $login_user = (request()->session()->get('login_user'))?request()->session()->get('login_user'):null; 
                if($login_user == null){
                    return view('izo_user.confirm');
                }else{ 
                    return view('izo_user.confirm')->with(compact(['login_user']));
                }
            } 
        }
         
        #................................................................
        $database_name  = request()->session()->get('user_main.database');
        $email          = request()->session()->get('user_main.email');
        Config::set('database.connections.mysql.database', $database_name);
        DB::purge('mysql');
        DB::reconnect('mysql');
        $user = \App\User::where("username",$email)->first();
        if($user){
            if(\Hash::check(request()->session()->get('password'),$user->password)){ 
                // return redirect("/");
            }else{
                // return redirect("/login");
            }
        }
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        return view('izo_user.panel')->with(compact(['jobs','company_size','language','country','currency','timeZone']));
    }

    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function saveCompany(Request $request)
    {
        // 
        // DD($request);
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql'); 
        
       
        $request->validate([
            // 'g-recaptcha-response' => 'required|captcha',
            'email'                => 'required|email',
            'mobile'               => 'required|min:7|max:9',
            // Other validation rules...
        ]);
        $data = $request->only(['company_name','email','domain_name','mobile','mobile_code','password']);
        $data['User-Agent']  = $request->header('User-Agent');
        $data['ip']          = $request->ip();
        // $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
        //     'secret' => env('NOCAPTCHA_SECRET'),
        //     'response' => $request->input('g-recaptcha-response'),
        // ]);

        // $responseBody = json_decode($response->getBody());

        // if ($responseBody->success && $responseBody->score >= 0.5) {
        //     // reCAPTCHA passed, handle form submission
        //     // return back()->with('success', 'Form submitted successfully.');
        // } else {
        //     // reCAPTCHA failed
        //     return back()->withErrors(['captcha' => 'reCAPTCHA verification failed.']);
        // }
        $save = IzoUser::saveUser($data);
        
        if(!$save){
            return redirect('/register-account');
        }else{
            return redirect('/panel-account');
        }
    }

    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    protected function login(Request $request)
    {
        // 
        #.....................................every time from the main
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        #....................................
        $data                = $request->only(['email','password','domain_name_sub']);
        $login = IzoUser::loginUser($data);
         
        if(!$login['status']){
            return redirect('/login-account');
        }
        session(['change_lang'  => "change"]);
        $url       = request()->root();
        $parsedUrl = parse_url($url);
        $host      = $parsedUrl['host'] ?? '';  
        $hostParts = explode('.', $host); 
        if (count($hostParts) == 3) {
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD
            array_pop($hostParts); // Domain

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else if(count($hostParts) == 2){
            // Remove the last two parts (domain and TLD)
            array_pop($hostParts); // TLD

            // The remaining parts are the subdomain
            $subdomain = implode('.', $hostParts);
        } else {
            // No subdomain
            $subdomain = '';

        }
        $subdomain     = $subdomain;
        if($subdomain == ""){
            $payload =  [
                "value1" => Hash::make("success"),
                "value2" => $login['password']
            ];
            session(['startLogin'  => $payload]);
            $database_info["database"] = $login['database'];
            $database_info["admin"]    = $login['admin'];
            $database_name             = $database_info ;
            
            // dd($subdomain != $login['domain']);
            if($subdomain == ""){
                $domain_name =  $login['domain'];
                $payload2 =  [
                    "email"    => $data['email'],
                    "password" => $data['password']
                ];
                session(['login_info'  => $payload2]);
                $login_user = 1;
                return redirect($login['url'])->with('login_user',$login_user);
            }
            //  return parent::login($request);
            return $this->traitLogin($request,$database_name);
        }else{
            if($subdomain != $login['domain']){
                // dd($login);
                request()->session()->flush();
                // $url = 
                // return view('izo_user.confirm');
                return redirect("/login-account");
            }else{
               
                $payload =  [
                    "value1" => Hash::make("success"),
                    "value2" => $login['password']
                ];
                session(['startLogin'  => $payload]);
                $database_info["database"] = $login['database'];
                $database_info["admin"]    = $login['admin'];
                $database_name             = $database_info ;
           
                
                // dd($subdomain != $login['domain']);
                if($subdomain == ""){
                        $domain_name =  $login['domain'];
                        $payload2 =  [
                        "email"    => $data['email'],
                        "password" => $data['password']
                    ];
                    session(['login_info'  => $payload2]); 
                    return redirect($login['url'])->with(compact('domain_name'));
                }
                
                
                //  return parent::login($request);
                return $this->traitLogin($request,$database_name);
            }
        } 
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function traitLogin(Request $request,$databaseInfo=null)
    { 
        $this->validateLogin($request);
        if($databaseInfo != null){
            $database = $databaseInfo['database'];
        }else{
            $database = null;
        }   
        // Determine the database connection settings dynamically
        $this->setDatabaseConnection($request,$database);
        
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
        $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }
         
        if ($this->attemptLogin($request)) {
            $credentials = $request->only('email', 'password');
            \Auth::attempt($credentials);
            // dd(\Auth::attempt($credentials),\Auth::user());
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            } 
            return $this->sendLoginResponse($request);
        }
        
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    { 
        // $url         = request()->root();
        // $parsedUrl   = parse_url($url);
        // $host        = $parsedUrl['host'] ?? '';  
        // $hostParts   = explode('.', $host);
        // $domain_url  = request()->session()->get('user_main.domain_url') ;
        // $final_url   = "http://".$domain_url.":8000/home";
        // return redirect()->away($final_url);
        
        #...............................................
        if($user->user_account_id != null || $user->user_visa_account_id != null){
            session()->flush();
            \Auth::logout();
            return redirect('/login-account')
            ->with(
                'status',
                ['success' => 0, 'msg' => __('lang_v1.login_not_allowed')]
            );
        }
        $session     = \App\Models\SessionTable::where("user_id",$user->id)->select()->first();
        if(!empty($session)){
            if($user->id == 1){ 
                if(isset($request->logout_other)){
                    $session->delete(); 
                    session()->put('delete_session','delete');
                }else{
                    session()->flush();
                    \Auth::logout();
                    return redirect('/login-account')
                    ->with(
                        'status',
                        ['success' => 0, 'msg' => __('lang_v1.sorry_there_is_device_active')]
                    );
                }
            }else if(isset($request->logout_other)){
                $session->delete(); 
            }
        }else{
            session()->put('create_session','first_login');
        }
        #...............................................
        if(!session()->get('lang')){
            session(['lang'  => "en"]); 
        }
        return redirect("/home");
      
    }


    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function logoutIzo(Request $request)
    {
        //  
        session()->flush();
        // session()->forget('user_main');
        return redirect('/login-account');
     
    }

    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkEmail(Request $request)
    {
        //
        // DD($request);
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        if(request()->ajax()){
            
            $em            = IzoUser::pluck('email')->toArray();
            $email         = request()->input('email');
            return $outPut = ["success"=>1,"message"=>(!in_array(trim($email),$em)) ];
        }
    }
    /**
     * company name.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkCompanyName(Request $request)
    {
        //
        // DD($request);
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        if(request()->ajax()){  
            $company       = IzoUser::pluck('company_name')->toArray();
            $company_name  = request()->input('company_name');
            return $outPut = ["success"=>1,"message"=>(!in_array(trim($company_name),$company)) ];
        }
    }
    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkDomainName(Request $request)
    {
        //
        // DD($request);
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        if(request()->ajax()){ 
            $domain        = IzoUser::pluck('domain_name')->toArray();
            $domain_name   = request()->input('domain_name');
            return $outPut = ["success"=>1,"message"=>(!in_array(trim($domain_name),$domain))];
        }
    }
    
    /**
     * panel.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkMobile(Request $request)
    {
        //
        // DD($request);
        Config::set('database.connections.mysql.database', "izocloud");
        DB::purge('mysql');
        DB::reconnect('mysql');
        if(request()->ajax()){
            $mob           = IzoUser::pluck('mobile')->toArray();
            $code          = request()->input('mobile_code');
            $mobile        = request()->input('mobile');
            return $outPut = ["success"=>1,"message"=>(!in_array(trim($code.$mobile),$mob))];
        }
    }
    
    

    
}
