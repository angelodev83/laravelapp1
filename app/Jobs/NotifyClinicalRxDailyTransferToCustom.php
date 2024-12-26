<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ClinicalRxDailyTransferNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyClinicalRxDailyTransferToCustom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $rxDailyTransfers, $summary, $emails, $user, $status;

    /**
     * Create a new job instance.
     */
    public function __construct($rxDailyTransfers, $summary, $emails, $user, $status)
    {
        $this->rxDailyTransfers = $rxDailyTransfers;
        $this->summary = $summary;
        $this->emails = $emails;
        $this->user = $user;
        $this->status = $status;
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
            $user->notify(new ClinicalRxDailyTransferNotification($this->rxDailyTransfers, $this->summary, $this->emails, $this->status));
        }
    }
}
