<?php

namespace App\Console\Commands;

use App\Repositories\ManualTesterRepository;
use Illuminate\Console\Command;

class ManualTester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:manual-tester';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $manualTesterRepository;

    public function __construct(ManualTesterRepository $manualTesterRepository)
    {
        parent::__construct();
        $this->manualTesterRepository = $manualTesterRepository;
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $res = $this->manualTesterRepository->run();
        $this->info($res);
    }
}
