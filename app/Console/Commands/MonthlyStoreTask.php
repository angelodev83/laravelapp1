<?php

namespace App\Console\Commands;

use App\Http\Controllers\Oig_exclusion_listController;
use App\Tasks\SetTask;
use Illuminate\Console\Command;
use App\Interfaces\ITaskRepository;

class MonthlyStoreTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monthly-store-task';

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
    public function handle(Oig_exclusion_listController $oig_exclusion_listController)
    {   
        $pharmacy_store_id = 1;
        $status_id = 201;

        $tasks = [
            ['tag_id' => 1, 'subject' => 'Upload Monthly Pharmacy DFI/QA for '],
            ['tag_id' => 2, 'subject' => 'Upload Monthly IHS Audit Checklist for '],
            ['tag_id' => 3, 'subject' => 'Upload Monthly Self Assessment QA for '],
            ['tag_id' => 8, 'subject' => 'Upload Monthly Control Counts C2 for '],
            ['tag_id' => 9, 'subject' => 'Upload Monthly Control Counts C3 - 5 for '],
            ['tag_id' => 12, 'subject' => 'Complete the Oregon Board of Pharmacy Self Inspection form for '],
        ];

        $monthlyTask = new SetTask($this->taskRepository);
        foreach($tasks as $task) {
            $res = $monthlyTask->task01OfTheMonth(
                $task['subject']
                , $pharmacy_store_id
                , $status_id
                , $task['tag_id']
            );
            if(!empty($res)) {
                $infoText = 'Done: '.$task['subject'].' 1st of the Month';
                $this->info($infoText);
            } else {
                $infoText = 'Not Done: '.$task['subject'].' 1st of the Month';
                $this->warn($infoText);
            }
        }

        $oig_exclusion_listController->downloadOigCsv();
    }
}
