<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ClinicalTherapyChangeAndRecoNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyClinicalTherapyChangeAndRecoToCustom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $therapyChangeAndReco, $summary, $emails, $user;

    /**
     * Create a new job instance.
     */
    public function __construct($therapyChangeAndReco, $summary, $emails, $user)
    {
        $this->therapyChangeAndReco = $therapyChangeAndReco;
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
            $user->notify(new ClinicalTherapyChangeAndRecoNotification($this->therapyChangeAndReco, $this->summary, $this->emails));
        }
    }
}
