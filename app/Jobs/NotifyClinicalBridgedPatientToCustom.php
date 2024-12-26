<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ClinicalBridgedPatientNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyClinicalBridgedPatientToCustom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $bridgedPatients, $summary, $emails, $user;

    /**
     * Create a new job instance.
     */
    public function __construct($bridgedPatients, $summary, $emails, $user)
    {
        $this->bridgedPatients = $bridgedPatients;
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
            $user->notify(new ClinicalBridgedPatientNotification($this->bridgedPatients, $this->summary, $this->emails));
        }
    }
}
