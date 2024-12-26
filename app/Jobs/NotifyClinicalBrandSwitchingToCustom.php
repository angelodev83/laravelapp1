<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ClinicalBrandSwitchingNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyClinicalBrandSwitchingToCustom implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $brandSwitchings, $summary, $emails, $user;

    /**
     * Create a new job instance.
     */
    public function __construct($brandSwitchings, $summary, $emails, $user)
    {
        $this->brandSwitchings = $brandSwitchings;
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
            $user->notify(new ClinicalBrandSwitchingNotification($this->brandSwitchings, $this->summary, $this->emails));
        }
    }
}
