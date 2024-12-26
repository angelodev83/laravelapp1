<?php

namespace App\Console\Commands;

use App\Tasks\SetTask;
use Illuminate\Console\Command;
use App\Interfaces\ITaskRepository;

class Daily1201Task extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily1201-task';

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
        $tasks = [
            // ['tag_id' => 10, 'subject' => 'Upload Daily Inventory Evaluation for Today - ']
        ];

        $pharmacy_store_id = 1;
        $status_id = 201;

        $daily = new SetTask($this->taskRepository);
        foreach($tasks as $task) {
            $res = $daily->daily1201(
                $task['subject']
                , $pharmacy_store_id
                , $status_id
                , $task['tag_id']
            );
            if(!empty($res)) {
                $infoText = 'Done: '.$task['subject'].' Daily 12:01 am';
                $this->info($infoText);
            } else {
                $infoText = 'Not Done: '.$task['subject'].' Daily 12:01 am';
                $this->warn($infoText);
            }
        }

        $task = $daily->daily1201CustomReminder();
        if(isset($task->id)) {
            $this->info('Done: '.$task->subject.' Daily 12:01 am');
        }
    }
}
