<?php

namespace App\Console\Commands;

use App\Http\Controllers\CURL\TebraController;
use Illuminate\Console\Command;

class DailyStoreTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-store-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(TebraController $tebra)
    {
        $tebra->get_everydayPatients();
        $tebra->get_everydayUpdatePatientData();

        $this->info('Every day task at 1am!');
    }
}
