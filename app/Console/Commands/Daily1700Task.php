<?php

namespace App\Console\Commands;

use App\Interfaces\ITaskRepository;
use App\Tasks\SetTask;
use Illuminate\Console\Command;

class Daily1700Task extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily1700-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $taskRepository;

    public function __construct(ITaskRepository $taskRepository)
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $erwin = 'erwin@mgmt88.com';

        // $daily = new SetTask($this->taskRepository);
        // $resArr = $daily->daily1700CustomReminder();
        // $resArr2 = $daily->daily1700CustomAssineeTask($erwin);
        
        // $resArr = array_merge($resArr, $resArr2);

        // foreach($resArr as $res) {
        //     $this->info('Done: '.$res.' (Daily/Weekly/Monthly 17:00 pm utc)');
        // }
    }
}
