<?php

namespace App\Jobs;

use App\Models\ClinicalRenewalComment;
use App\Models\User;
use App\Notifications\RenewalCommentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyRenewalCommentToCreator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $clinicalRenewalComment;

    /**
     * Create a new job instance.
     */
    public function __construct(ClinicalRenewalComment $clinicalRenewalComment)
    {
        $this->clinicalRenewalComment = $clinicalRenewalComment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->notifyCreator($this->clinicalRenewalComment->renewal->user);
    }

    private function notifyCreator(User $user){ 
        if(isset($user->id)) {
            $user->notify(new RenewalCommentNotification($this->clinicalRenewalComment));
        }
    }

}
