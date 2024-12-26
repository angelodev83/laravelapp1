<?php

namespace App\Console\Commands;

use App\Repositories\SFTPRepository;
use Illuminate\Console\Command;

class DailySftpAutomation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-sftp-automation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    private $sftpRepository;

    public function __construct(SFTPRepository $sftpRepository)
    {
        parent::__construct();
        $this->sftpRepository = $sftpRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $res = $this->sftpRepository->run();
        foreach($res as $r) {
            $this->info('Done import sftp file path: '.$r.' ('.date('Y-m-d g:i A').')');
        }
    }
}
