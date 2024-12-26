<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ClinicalRxDailyCensusNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyClinicalRxDailyCensusToCustom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $rxDailyCensus, $summary, $emails, $user;

    /**
     * Create a new job instance.
     */
    public function __construct($rxDailyCensus, $summary, $emails, $user)
    {
        $this->rxDailyCensus = $rxDailyCensus;
        $this->summary = $summary;
        $this->emails = $emails;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->notifyCreator($this->user);
    }

    private function notifyCreator(User $user){ 
        if(isset($user->id)) {
            $user->notify(new ClinicalRxDailyCensusNotification($this->rxDailyCensus, $this->summary, $this->emails));
        }
    }
}
