<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

use App\Models\Task;
use App\Models\Employee;
use App\Models\StoreStatus;

class TaskNotification extends Notification
{
    use Queueable;

    protected $task, $currentStatus, $previousStatus = null;
    protected $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task
        , StoreStatus $currentStatus
        , StoreStatus $previousStatus = null
        , String $event)
    {
        $this->task = $task;
        $this->currentStatus = $currentStatus;
        $this->previousStatus = $previousStatus;
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    private function getEventData()
    {
        switch($this->event) {
            case "new":
                $eventSubject = 'New task assigned to you';
                $emailSubject = 'New Task Assigned: '.$this->task->subject;
                $ccCreator = false;
                break;
            case "overdue":
                $eventSubject = 'Task overdue';
                $emailSubject = 'Task Overdue: '.$this->task->subject;
                $ccCreator = true;
                break;
            case "re-assigned":
                $eventSubject = 'Task re-assigned';
                $emailSubject = 'Task Re-assigned: '.$this->task->subject;
                $ccCreator = false;
                break;
            case "priority":
                $eventSubject = 'Task priority changed';
                $emailSubject = 'Task Priority Changed: '.$this->task->subject;
                $ccCreator = true;
                break;
            case "description":
                $eventSubject = 'Task description updated';
                $emailSubject = 'Task Description Updated: '.$this->task->subject;
                $ccCreator = true;
                break;
            case "deleted":
                $eventSubject = 'Task deleted';
                $emailSubject = 'Task Deleted: '.$this->task->subject;
                $ccCreator = true;
                break;
            default:
                $eventSubject = 'Task '.strtolower($this->currentStatus->name);
                $emailSubject = 'Task '.ucfirst(lcfirst($this->currentStatus->name)).': '.$this->task->subject;
                $ccCreator = true;
                break;
        }

        return [
            'eventSubject' => $eventSubject,
            'emailSubject' => $emailSubject,
            'ccCreator' => $ccCreator
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $eventArray = $this->getEventData();
        $eventSubject = $eventArray['eventSubject'];
        $emailSubject = $eventArray['emailSubject'];
        $ccCreator = $eventArray['ccCreator'];
        
        $taskSubject = $this->task->subject;

        $createdBy = '';
        $user = auth()->user();

        $cc = [];
        if(isset($user->id)) {
            $authEmployee = Employee::select('id','firstname', 'lastname','image','initials_random_color')->where('user_id', auth()->user()->id)->first();
            $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
        } else {
            $authEmployee = $this->task->user->employee;
            if(isset($authEmployee->id)) {
                $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
            }
        }
        if($ccCreator === true) {
            $cc[] = $this->task->user->email;
        }

        $subtext = 'TRP: Bulletin > Task Reminders';

        // Get the count of rows where the ID is greater than the given ID
        $index = Task::where('id', '>', $this->task->id)->count();
        // The index is the count + 1 since it starts from 1
        // $index += 1;

        $actionUrl = '/store/bulletin/'.$this->task->pharmacy_store_id.'/task-reminders';
        $button = '';
        if($this->event != "deleted") {
            $actionUrl .= '?task-id='.$this->task->id.'&task-index='.$index;

            $currButton = '<button style="color: '.$this->currentStatus->text_color.'; background-color: '.$this->currentStatus->color.'; border-color: '.$this->currentStatus->color.'; padding: 6px 35px 8px 35px; min-width: 150px; margin-top: 20px; border-radius: 3px;">'.$this->currentStatus->name.'</button>';
    
            if(!empty($this->previousStatus)) {
                $button = '<button style="color: '.$this->previousStatus->text_color.'; background-color: '.$this->previousStatus->color.'; border-color: '.$this->previousStatus->color.'; padding: 6px 35px 8px 35px; min-width: 150px; margin-top: 20px; border-radius: 3px;">'.$this->previousStatus->name.'</button>' . ' > ' .$currButton;
            } else {
                $button = $currButton;
            }
        }

        $subDetails = '<br>';
        if(!empty($this->task->due_date)) {
            if($this->event == "overdue") {
                $subDetails .= "<small style='color:red;'></i>Due Date on ".date("F d, Y", strtotime($this->task->due_date))."</small>";
            } else {
                $subDetails .= "<small>Due Date on ".date("F d, Y", strtotime($this->task->due_date))."</small>";
            }
            $subDetails .= "<br>";
        }
        $subDetails .= "<small>( ".$this->task->priorityStatus->name." PRIORITY )</small>";

        $watchers = $this->task->watchers()->get()->all();
        $emails = [];
        foreach($watchers as $watcher)
        {
            $user = $watcher->user ?? null;
            if(isset($user->email)) {
                $emails[] = $user->email;
            }
        }

        $cc = array_merge($cc, $emails);
        $cc = array_unique($cc);

        return (new MailMessage)
                ->subject($emailSubject) //"🗒️ ".
                ->cc($cc)
                ->line('<div style="text-align: center; margin-bottom: 5px; padding-bottom: 0;">
                    <img src="https://home.mgmt88.com/images/mgmt88-logo.png" style="max-width: 100%; max-height: 100px;"></img>
                </div>')
                ->line('<p style="font-size: 30px; color: black; text-align: center; margin-bottom: 0; padding-bottom: 0;">'.$eventSubject.'</p>')
                ->line('<p style="font-size: 12px; color: gray; text-align: center; margin-top: 0; padding-top: 0;">by '.$createdBy.'</p>')
                ->line('<div style="background-color: #c2f4f58c; color: #0d677a; margin-left: -32px !important; margin-right: -32px !important; margin-bottom: 40px; padding: 20px 40px 40px 40px; text-align: center;">
                <small>'.$subtext.'</small>
                <p style="font-size: 25px; text-align: center; margin-bottom: 0;">'.$taskSubject.'</p>
                '.$subDetails.'
                <div>'.$button.'</div>
                </div>')
                ->action('View Task', url($actionUrl));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
