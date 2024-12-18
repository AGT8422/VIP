<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('country', 100);
            $table->string('currency', 100);
            $table->string('code', 25);
            $table->string('symbol', 25);
            $table->string('thousand_separator', 10);
            $table->string('decimal_separator', 10);
            $table->integer('status')->unsigned();
            $table->nullableTimestamps();
        });
         
        $time = \Carbon::now();
        // DB::statement("INSERT INTO currencies (column1, column2) VALUES (?, ?)", [$column1, $column2]);

        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('1','Albania'                 ,'Leke','ALL','Lek',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('2','America'                 ,'Dollars','USD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('3','Afghanistan'             ,'Afghanis','AF','؋',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('4','Argentina'               ,'Pesos','ARS','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('5','Aruba'                   ,'Guilders','AWG','ƒ',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('6','Australia'               ,'Dollars','AUD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('7','Azerbaijan'              ,'New Manats','AZ','ман',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('8','Bahamas'                 ,'Dollars','BSD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('9','Barbados'                ,'Dollars','BBD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('10','Belarus'                 ,'Rubles','BYR','p.',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('11','Belgium'                 ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('12','Beliz'                   ,'Dollars','BZD','BZ$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('13','Bermuda'                 ,'Dollars','BMD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('14','Bolivia'                 ,'Bolivianos','BOB','\$b',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('15','Bosnia and Herzegovina'  ,'Convertible Marka','BAM','KM',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('16','Botswana'                ,'Pula s','BWP','P',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('17','Bulgaria'                ,'Leva','BG','лв',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('18','Brazil'                  ,'Reais','BRL','R$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('19','Britain [United Kingdom]','Pounds','GBP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('20','Brunei Darussalam'       ,'Dollars','BND','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('21','Cambodia'                ,'Riels','KHR','៛',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('22','Canada'                  ,'Dollars','CAD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('23','Cayman Islands'          ,'Dollars','KYD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('24','Chile'                   ,'Pesos','CLP','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('25','China'                   ,'Yuan Renminbi','CNY','¥',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('26','Colombia'                ,'Pesos','COP','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('27','Costa Rica'              ,'ColÃ³n','CRC','₡',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('28','Croatia'                 ,'Kuna','HRK','kn',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('29','Cuba'                    ,'Pesos','CUP','₱',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('30','Cyprus'                  ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('31','Czech Republic'          ,'Koruny','CZK','Kč',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('32','Denmark'                 ,'Kroner','DKK','kr',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('33','Dominican Republic'      ,'Pesos','DOP ','RD$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('34','East Caribbean'          ,'Dollars','XCD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('35','Egypt'                   ,'Ø§Ù„Ø¬Ù†ÙŠØ© Ø§Ù„Ù…ØµØ±ÙŠ','EGP','جنيها',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('36','El Salvador'             ,'Colones','SVC','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('37','England [United Kingdom]','Pounds','GBP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('38','Euro'                    ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('39','Falkland Islands'        ,'Pounds','FKP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('40','Fiji'                    ,'Dollars','FJD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('41','France'                  ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('42','Ghana'                   ,'Cedis','GHS','¢',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('43','Gibraltar'               ,'Pounds','GIP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('44','Greece'                  ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('45','Guatemala'               ,'Quetzales','GTQ','Q',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('46','Guernsey'                ,'Pounds','GGP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('47','Guyana'                  ,'Dollars','GYD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('48','Holland [Netherlands]'   ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('49','Honduras'                ,'Lempiras','HNL','L',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('50','Hong Kong'               ,'Dollars','HKD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('51','Hungary'                 ,'Forint','HUF','Ft',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('52','Iceland'                 ,'Kronur','ISK','kr',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('53','India'                   ,'Rupees','INR','₹',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('54','Indonesia'               ,'Rupiahs','IDR','Rp',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('55','Iran'                    ,'Rials','IRR','﷼',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('56','Ireland'                 ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('57','Isle of Man'             ,'Pounds','IMP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('58','Israel'                  ,'New Shekels','ILS','₪',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('59','Italy'                   ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('60','Jamaica'                 ,'Dollars','JMD','J$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('61','Japan'                   ,'Yen','JPY','¥',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('62','Jersey'                  ,'Pounds','JEP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('63','Kazakhstan'              ,'Tenge','KZT','лв',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('64','Korea [North]'           ,'Won','KPW','₩',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('65','Korea [South]'           ,'Won','KRW','₩',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('66','Kyrgyzstan'              ,'Soms','KGS','лв',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('67','Laos'                    ,'Kips','LAK','₭',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('68','Latvia'                  ,'Lati','LVL','Ls',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('69','Lebanon'                 ,'Pounds','LBP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('70','Liberia'                 ,'Dollars','LRD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('71','Liechtenstein'           ,'Switzerland Francs','CHF','CHF',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('72','Lithuania'               ,'Litai','LTL','Lt',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('73','Luxembourg'              ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('74','Macedonia'               ,'Denars','MKD','ден',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('75','Malaysia'                ,'Ringgits','MYR','RM',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('76','Malta'                   ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('77','Mauritius'               ,'Rupees','MUR','₨',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('78','Mexico'                  ,'Pesos','MXN','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('79','Mongolia'                ,'Tugriks','MNT','₮',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('80','Mozambique'              ,'Meticais','MZ','MT',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('81','Namibia'                 ,'Dollars','NAD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('82','Nepal'                   ,'Rupees','NPR','₨',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('83','Netherlands Antilles'    ,'Guilders','ANG','ƒ',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('84','Netherlands'             ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('85','New Zealand'             ,'Dollars','NZD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('86','Nicaragua'               ,'Cordobas','NIO','C$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('87','Nigeria'                 ,'Nairas','NGN','₦',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('88','North Korea'             ,'Won','KPW','₩',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('89','Norway'                  ,'Krone','NOK','kr',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('90','Oman'                    ,'Rials','OMR','﷼',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('91','Pakistan'                ,'Rupees','PKR','₨',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('92','Panama'                  ,'Balboa','PAB','B/.',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('93','Paraguay'                ,'Guarani','PYG','Gs',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('94','Peru'                    ,'Nuevos Soles','PE','S/.',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('95','Philippines'             ,'Pesos','PHP','Php',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('96','Poland'                  ,'Zlotych','PL','zł',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('97','Qatar'                   ,'Rials','QAR','﷼',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('98','Romania'                 ,'New Lei','RO','lei',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('99','Russia'                  ,'Rubles','RUB','руб',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('100','Saint Helena'            ,'Pounds','SHP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('101','Saudi Arabia'            ,'ريال سعودي','ر.س','﷼',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('102','Serbia'                  ,'Dinars','RSD','Дин.',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('103','Seychelles'              ,'Rupees','SCR','₨',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('104','Singapore'               ,'Dollars','SGD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('105','Slovenia'                ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('106','Solomon Islands'         ,'Dollars','SBD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('107','Somalia'                 ,'Shillings','SOS','S',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('108','South Africa'            ,'Rand','ZAR','R',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('109','South Korea'             ,'Won','KRW','₩',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('110','Spain'                   ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('111','Sri Lanka'               ,'Rupees','LKR','₨',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('112','Sweden'                  ,'Kronor','SEK','kr',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('113','Switzerland'             ,'Francs','CHF','CHF',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('114','Suriname'                ,'Dollars','SRD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('115','Syria'                   ,'Pounds','SYP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('116','Taiwan'                  ,'New Dollars','TWD','NT$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('117','Thailand'                ,'Baht','THB','฿',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('118','Trinidad and Tobago'     ,'Dollars','TTD','TT$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('119','Turkey'                  ,'Lira','TRY','TL',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('120','Turkey'                  ,'Liras','TRL','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('121','Tuvalu'                  ,'Dollars','TVD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('122','Ukraine'                 ,'Hryvnia','UAH','₴',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('123','United Kingdom'          ,'Pounds','GBP','£',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('124','United States of America','Dollars','USD','$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('125','Uruguay'                 ,'Pesos','UYU','\$U',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('126','Uzbekistan'              ,'Sums','UZS','лв',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('127','Vatican City'            ,'Euro','EUR','€',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('128','Venezuela'               ,'Bolivares Fuertes','VEF','Bs',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('129','Vietnam'                 ,'Dong','VND','₫',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('130','Yemen'                   ,'Rials','YER','﷼',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('131','Zimbabwe'                ,'Zimbabwe Dollars','ZWD','Z$',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('132','Iraq'                    ,'Iraqi dinar','IQD','د.ع',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('133','Kenya'                   ,'Kenyan shilling','KES','KSh',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('134','Bangladesh'              ,'Taka','BDT','৳',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('135','Algerie'                 ,'Algerian dinar','DZD','د.ج',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('136','United Arab Emirates'    ,'United Arab Emirates dirham','AED','AED',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('137','Uganda'                  ,'Uganda shillings','UGX','USh',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('138','Tanzania'                ,'Tanzanian shilling','TZS','TSh',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('139','Angola'                  ,'Kwanza','AOA','Kz',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('140','Kuwait'                  ,'Kuwaiti dinar','KWD','KD',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('141','Bahrain'                 ,'Bahraini dinar','BHD','BD',',','.',?,?,'1')",[$time,$time]);
        DB::statement("INSERT INTO  currencies (id,country,currency,code,symbol,thousand_separator,decimal_separator,created_at ,updated_at,status) VALUES ('142','Syrian Pound'            ,'الليرة السورية','SOR','SP',',','.',?,?,'1')",[$time,$time]);
}
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('currencies');
    }
}
