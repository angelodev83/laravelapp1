<?php

namespace App\Console\Commands;

use App\Tasks\SetTask;
use Illuminate\Console\Command;
use App\Interfaces\ITaskRepository;

class WeeklyStoreTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:weekly-store-task';

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
        $pharmacy_store_id = 1;
        $status_id = 201;

        $tasks = [
            ['tag_id' => 11, 'subject' => 'Upload Weekly Inventory Audit for '],
        ];


        $weekly = new SetTask($this->taskRepository);
        foreach($tasks as $task) {
            $res = $weekly->weeklyOnMonday(
                $task['subject']
                , $pharmacy_store_id
                , $status_id
                , $task['tag_id']
            );
            if(!empty($res)) {
                $infoText = 'Done: '.$task['subject'].' Weekly Monday 12:30am';
                $this->info($infoText);
            } else {
                $infoText = 'Not Done: '.$task['subject'].' Weekly Monday 12:30am';
                $this->warn($infoText);
            }
        }
    }
}
