<?php

namespace App\Jobs;

use App\Models\OperationRtsComment;
use App\Models\User;
use App\Notifications\RTSCommentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyRTSCommentToCreator implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $operationRtsComment;

    /**
     * Create a new job instance.
     */
    public function __construct(OperationRtsComment $operationRtsComment)
    {
        $this->operationRtsComment = $operationRtsComment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->notifyCreator($this->operationRtsComment->rts->user);
    }

    private function notifyCreator(User $user){ 
        if(isset($user->id)) {
            $user->notify(new RTSCommentNotification($this->operationRtsComment));
        }
    }

}
