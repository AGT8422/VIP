<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class mysqldump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mysql:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump The MySql Database';

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
        
        $db_host = env("DB_HOST");
        $db_port = env("DB_PORT");
        $db_name = env("DB_DATABASE");
        $db_user = env("DB_USERNAME");
        $db_pass = env("DB_PASSWORD");
        $db_file = $db_name . "-" . date("Y-m-d h:i:s");
        $command = "mysqldump --host=$db_host --port=$db_port --user=$db_user --password=$db_pass  $db_name | gzip > $db_file";
        system($command);
        return 0;   
    }
}
