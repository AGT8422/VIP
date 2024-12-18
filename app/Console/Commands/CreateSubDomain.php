<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateSubDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create New Subdomain';

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
        // Your custom command logic here
        
        exec('echo "127.0.0.1  tester.localhost" >> C:\Windows\System32\drivers\etc\hosts', $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->info('successfully');
            foreach ($output as $line) {
                $this->info($line);
            }
        } else {
            $this->error("Command execution failed with status $returnVar:");
            foreach ($output as $line) {
                $this->error($line);
            }
        }
        return 0;
    }
}
