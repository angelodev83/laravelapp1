<?php

namespace App\Notifications;

use App\Models\Employee;
use App\Models\StoreStatus;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketNotification extends Notification
{
    use Queueable;

    protected $ticket, $currentStatus, $previousStatus = null;
    protected $event;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket
    , StoreStatus $currentStatus
    , StoreStatus $previousStatus = null
    , String $event)
    {
        $this->ticket = $ticket;
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
                $eventSubject = 'New ticket assigned to you';
                $emailSubject = 'New Ticket Assigned: '.$this->ticket->subject;
                $ccCreator = false;
                break;
            case "overdue":
                $eventSubject = 'Ticket overdue';
                $emailSubject = 'Ticket Overdue: '.$this->ticket->subject;
                $ccCreator = true;
                break;
            case "re-assigned":
                $eventSubject = 'Ticket re-assigned';
                $emailSubject = 'Ticket Re-assigned: '.$this->ticket->subject;
                $ccCreator = false;
                break;
            case "priority":
                $eventSubject = 'Ticket priority changed';
                $emailSubject = 'Ticket Priority Changed: '.$this->ticket->subject;
                $ccCreator = true;
                break;
            case "description":
                $eventSubject = 'Ticket description updated';
                $emailSubject = 'Ticket Description Updated: '.$this->ticket->subject;
                $ccCreator = true;
                break;
            case "deleted":
                $eventSubject = 'Ticket deleted';
                $emailSubject = 'Ticket Deleted: '.$this->ticket->subject;
                $ccCreator = true;
                break;
            case "comment":
                $eventSubject = 'Ticket New Comment';
                $emailSubject = 'Ticket New Comment: '.$this->ticket->subject;
                $ccCreator = true;
                break;
            default:
                $eventSubject = 'Ticket '.strtolower($this->currentStatus->name);
                $emailSubject = 'Ticket '.ucfirst(lcfirst($this->currentStatus->name)).': '.$this->ticket->subject;
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

        $ticketSubject = $this->ticket->subject;

        $createdBy = '';
        $user = auth()->user();

        $cc = [];
        if(isset($user->id)) {
            $authEmployee = Employee::select('id','firstname', 'lastname','image','initials_random_color')->where('user_id',auth()->user()->id)->first();
            $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
        } else {
            $authEmployee = $this->ticket->user->employee;
            if(isset($authEmployee->id)) {
                $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
            }
        }
        if($ccCreator === true) {
            $cc[] = $this->ticket->user->email;
        }

        $subtext = 'TRP: Escalation > Ticket : <b>#' .$this->ticket->code;

        // Get the count of rows where the ID is greater than the given ID
        $index = Ticket::where('id', '>', $this->ticket->id)->count();
        // The index is the count + 1 since it starts from 1
        // $index += 1;
        
        $actionUrl = '/store/escalation/'.$this->ticket->pharmacy_store_id.'/tickets';
        $button = '';
        if($this->event != "deleted") {
            $actionUrl .= '?ticket-id='.$this->ticket->id.'&ticket-index='.$index;

            $currButton = '<button style="color: '.$this->currentStatus->text_color.'; background-color: '.$this->currentStatus->color.'; border-color: '.$this->currentStatus->color.'; padding: 6px 35px 8px 35px; min-width: 150px; margin-top: 20px; border-radius: 3px;">'.$this->currentStatus->name.'</button>';

            if(!empty($this->previousStatus)) {
                $button = '<button style="color: '.$this->previousStatus->text_color.'; background-color: '.$this->previousStatus->color.'; border-color: '.$this->previousStatus->color.'; padding: 6px 35px 8px 35px; min-width: 150px; margin-top: 20px; border-radius: 3px;">'.$this->previousStatus->name.'</button>' . ' > ' .$currButton;
            } else {
                $button = $currButton;
            }
        }

        $subDetails = '<br>';
        if(!empty($this->ticket->due_date)) {
            if($this->event == "overdue") {
                $subDetails .= "<small style='color:red;'></i>Due Date on ".date("F d, Y", strtotime($this->ticket->due_date))."</small>";
            } else {
                $subDetails .= "<small>Due Date on ".date("F d, Y", strtotime($this->ticket->due_date))."</small>";
            }
            $subDetails .= "<br>";
        }
        $subDetails .= "<small>( ".$this->ticket->priority->name." PRIORITY )</small>";

        if($this->event != 'comment') {
            $details = '<small>'.$subtext.'</small>
            <p style="font-size: 25px; text-align: center; margin-bottom: 0;">'.$ticketSubject.'</p>
            '.$subDetails.'
            <div>'.$button.'</div>';
        } else {
            $details = '<small>'.$subtext.'</small>
            <p style="font-size: 25px; text-align: center; margin-bottom: 5px;;">'.$ticketSubject.'</p>
            <p style="font-size: 18px; text-align: center; margin-bottom: 0;">'.$this->ticket->newComment->comment.'</p>';
        }

        $watchers = $this->ticket->watchers()->get()->all();
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
                    ->subject($emailSubject) //"🎫 ".
                    ->cc($cc)
                    ->line('<div style="text-align: center; margin-bottom: 5px; padding-bottom: 0;">
                        <img src="https://home.mgmt88.com/images/mgmt88-logo.png" style="max-width: 100%; max-height: 100px;"></img>
                    </div>')
                    ->line('<p style="font-size: 30px; color: black; text-align: center; margin-bottom: 0; padding-bottom: 0;">'.$eventSubject.'</p>')
                    ->line('<p style="font-size: 12px; color: gray; text-align: center; margin-top: 0; padding-top: 0;">by '.$createdBy.'</p>')
                    ->line('<div style="background-color: #c2f4f58c; color: #0d677a; margin-left: -32px !important; margin-right: -32px !important; margin-bottom: 40px; padding: 20px 40px 40px 40px; text-align: center;">
                    '.$details.'
                    </div>')
                    ->action('View Ticket', url($actionUrl));
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
