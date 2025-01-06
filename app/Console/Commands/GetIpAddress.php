<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GetIpAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:ip-address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve the IP address of the server or a client';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $serverIpv = exec("for /f \"tokens=14\" %i in ('ipconfig ^| findstr \"IPv4\"') do @echo %i");
        $serverIp  = shell_exec("sudo ip -4 addr show | grep inet | awk '{print $2}'| cut -d/ -f1");
        if($serverIpv){
            $this->info("ipv : " .$serverIpv);
            session()->put('ipv_device',$serverIpv);
            // session(['ipv_device'=>$serverIpv]);
        }
        if($serverIp){
            $this->info("ip : " .$serverIp);
            $this->info("session : " .session()->get('_token'));
            // session()->put('ip_device',$serverIp);
            // session(['ip_device'=>$serverIp]);
        }
        return Command::SUCCESS ;
    }
}
