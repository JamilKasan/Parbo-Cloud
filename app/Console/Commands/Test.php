<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        logTimeAttendance(fopen("test.csv","r"));
//        $this->line(basename('68P8IAcfC6TumW6FiFA0aH8HesX44Z-metadGVzdC5jc3Y=-.txt') );

    }
}
