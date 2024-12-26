<?php

namespace App\Console\Commands;

use App\Tasks\SetTask;
use Illuminate\Console\Command;
use App\Interfaces\ITaskRepository;
use App\Interfaces\ITicketRepository;

class DailyJobUpdateStore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-job-update-store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $taskRepository, $ticketRepository;

    public function __construct(
        ITaskRepository $taskRepository
        , ITicketRepository $ticketRepository
    )
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {   
        $this->taskRepository->sendNotificationOverDue();
        $this->ticketRepository->sendNotificationOverDue();
        $this->info('Every day job at 12:15am');
    }
}
