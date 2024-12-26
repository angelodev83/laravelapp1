<?php

namespace App\Notifications;

use App\Models\ClinicalRenewalComment;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RenewalCommentNotification extends Notification
{
    use Queueable;

    private $clinicalRenewalComment;

    /**
     * Create a new notification instance.
     */
    public function __construct(ClinicalRenewalComment $clinicalRenewalComment)
    {
        $this->clinicalRenewalComment = $clinicalRenewalComment;
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $renewal = $this->clinicalRenewalComment->renewal ?? null;

        $emailSubject = '';

        $mainDetails = '';

        if(!empty($renewal)) {
            $patient_fullname = $renewal->patient->getDecryptedFirstname().' '.$renewal->patient->getDecryptedLastname();
            $emailSubject = 'Renewal - RX#'.$renewal->rx_number.' for Patient: '.$patient_fullname;

            $date_today = $this->getCurrentPSTDate('Y-m-d');
            $date1 = Carbon::createFromFormat('Y-m-d', $renewal->renew_date);
            $date2 = Carbon::createFromFormat('Y-m-d', $date_today);
            $days = $date1->diffInDays($date2);

            $days_in_queue = $days.' Days';
            $days_in_queue_bg_color = $days >= 14 ? 'red' : 'white';
            $days_in_queue_text_color = $days >= 14 ? 'white' : 'black';


            $mainDetails = '<table style="width: 100%; padding: 10px; border-collapse: collapse;">
                <tr>
                    <th style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Patient</th>
                    <td style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$patient_fullname.'</td>
                </tr>
                <tr>
                    <th style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">RX Number</th>
                    <td style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$renewal->rx_number.'</td>
                </tr>
                <tr>
                    <th style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Renew Date</th>
                    <td style="text-align: left; border: 1px solid #eee; padding: 5px;">'.date('F d, Y', strtotime($renewal->renew_date)).'</td>
                </tr>
                <tr>
                    <th style="text-align: left; width: 32%; border: 1px solid #eee;  padding: 5px;">Days in Queue</th>
                    <td style="text-align: left; border: 1px solid #eee; padding: 5px; background-color: '.$days_in_queue_bg_color.'; color: '.$days_in_queue_text_color.';">'.$days_in_queue.'</td>
                </tr>
                <tr>
                    <th style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Call Attempts</th>
                    <td style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$renewal->call_attempts.'</td>
                </tr>
                <tr>
                    <th style="text-align: left; width: 32%; border: 1px solid #eee;  padding: 5px;">Status</th>
                    <td style="text-align: left; border: 1px solid #eee; padding: 5px; background-color: '.$renewal->status->color.'; color: '.$renewal->status->text_color.';">'.$renewal->status->name.'</td>
                </tr>
                <tr>
                    <th style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Telebridge</th>
                    <td style="text-align: left; border: 1px solid #eee; padding: 5px;">'.(empty($renewal->telebridge) ? '' : $renewal->telebridge).'</td>
                </tr>
                <tr>
                    <th style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Reason for Denial</th>
                    <td style="text-align: left; border: 1px solid #eee; padding: 5px;">'.(empty($renewal->reason_for_denial) ? '' : $renewal->reason_for_denial).'</td>
                </tr>
                <tr>
                    <th style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Comment</th>
                    <td style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$this->clinicalRenewalComment->comment.'</td>
                </tr>
            </table>';
        }

        $createdBy = '';
        $user = auth()->user();
        $cc = $user->email;

        if(isset($user->id)) {
            $authEmployee = Employee::select('id','firstname', 'lastname','image','initials_random_color')->where('user_id', auth()->user()->id)->first();
            $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
        } else {
            $authEmployee = $renewal->user->employee;
            if(isset($authEmployee->id)) {
                $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
            }
        }

        $subtext = 'TRP: Clinical > Renewal';        

        $button = '';
        $actionUrl = '';

        return (new MailMessage)
                ->subject($emailSubject)
                ->cc($cc)
                ->line('<div style="text-align: center; margin-bottom: 5px; padding-bottom: 0;">
                    <img src="https://home.mgmt88.com/images/mgmt88-logo.png" style="max-width: 100%; max-height: 100px;"></img>
                </div>')
                ->line('<p style="font-size: 30px; color: black; text-align: center; margin-bottom: 0; padding-bottom: 0;">Renewal New Comment</p>')
                ->line('<p style="font-size: 12px; color: gray; text-align: center; margin-top: 0; padding-top: 0;">by '.$createdBy.'</p>')
                ->line('<div style="background-color: #c2f4f58c; color: #0d677a; margin-left: -32px !important; margin-right: -32px !important; margin-bottom: 20px; padding: 20px 40px 20px 40px; text-align: center;">
                <small>'.$subtext.'</small>
                <p style="font-size: 25px; text-align: center; margin-bottom: 5px;">'.$mainDetails.'</p>
                <div>'.$button.'</div>
                </div>');
                // ->action('View Renewal', url($actionUrl));
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

    protected function getCurrentPSTDate($format = 'Y-m-d', $date = null)
    {

        if(!empty($date)) {
            $pst = Carbon::createFromFormat('Y-m-d', $date);
            $pst = $pst->setTimezone('America/Los_Angeles');
        }else {
            $pst = Carbon::now('America/Los_Angeles');
        }
        
        return $pst->format($format);
    }

}
