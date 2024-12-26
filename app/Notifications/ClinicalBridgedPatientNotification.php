<?php

namespace App\Notifications;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClinicalBridgedPatientNotification extends Notification
{
    use Queueable;

    private $bridgedPatients, $summary, $emails;

    /**
     * Create a new notification instance.
     */
    public function __construct($bridgedPatients, $summary, $emails)
    {
        $this->bridgedPatients = $bridgedPatients;
        $this->summary = $summary;
        $this->emails = $emails;
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
        $bridgedPatients = $this->bridgedPatients;
        $summary = isset($this->summary->data) ? $this->summary->data : [];

        $emailSubject = '';

        $mainDetails = '';
        $summaryDetails = '';

        if(!empty($bridgedPatients)) {

            $date = $summary->formatted_date ?: '';

            $emailSubject = 'Bridged Patients for '.$date;
            
            if(!empty($date)) {
                
                $summaryDetails = '<table style="width: 100%; padding: 10px; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Bridged Patients</td>
                        <th style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$summary->count_patient_names.'</th>
                    </tr>
                    <tr>
                        <td style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Rx</td>
                        <th style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$summary->count_rx_numbers.'</th>
                    </tr>
                </table>';

            }

            $mainDetails .= '<table style="width: 100%; padding: 10px; border-collapse: collapse;">
            <tr>
                <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">Script No.</th>
                <th style="text-align: left; width: 25%; border: 1px solid #eee; padding: 5px;">Patient Name</th>
                <th style="text-align: left; width: 25%; border: 1px solid #eee; padding: 5px;">Meds</th>
            </tr>';

            foreach($bridgedPatients as $bs) {
                $patient = isset($bs->patient) ? $bs->patient : null;

                $patient_fullname = '';
                if(isset($patient->firstname)) {
                    $patient_fullname = $patient->getDecryptedLastname().', '.$patient->getDecryptedFirstname();
                } else {
                    $patient_fullname = $bs->patient_name;
                }

                $mainDetails .= '
                    <tr>
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$bs->rx_number.'</td>
                        <td style="text-align: left; width: 25%; border: 1px solid #eee; padding: 5px;">'.$patient_fullname.'</td>
                        <td style="text-align: left; width: 25%; border: 1px solid #eee; padding: 5px;">'.$bs->medication_description.'</td>
                    </tr>
                ';

                $bs->mail_sent_count += 1;
                $bs->save();
            }

            $mainDetails .= '</table>';
            
        }

        $createdBy = '';
        $user = auth()->user();

        if(isset($user->id)) {
            $authEmployee = Employee::select('id','firstname', 'lastname','image','initials_random_color')->where('user_id', auth()->user()->id)->first();
            $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
        } else {
            $authEmployee = $this->bridgedPatients[0]->user->employee;
            if(isset($authEmployee->id)) {
                $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
            }
        }

        $subtext = 'TRP: Clinical > Bridged Patients';   
        $button = '';
        $actionUrl = '';

        return (new MailMessage)
                ->subject($emailSubject)
                ->cc($this->emails)
                ->line('<div style="text-align: center; margin-bottom: 5px; padding-bottom: 0;">
                    <img src="https://home.mgmt88.com/images/mgmt88-logo.png" style="max-width: 100%; max-height: 100px;"></img>
                </div>')
                ->line('<p style="font-size: 30px; color: black; text-align: center; margin-bottom: 0; padding-bottom: 0;">'.$emailSubject.'</p>')
                ->line('<p style="font-size: 12px; color: gray; text-align: center; margin-top: 0; padding-top: 0;">by '.$createdBy.'</p>')
                ->line('<div style="background-color: #c2f4f58c; color: #0d677a; margin-left: -32px !important; margin-right: -32px !important; margin-bottom: 20px; padding: 20px 40px 20px 40px; text-align: center;">
                <small>'.$subtext.'</small>
                <p style="font-size: 25px; text-align: center; margin-bottom: 5px;">'.$summaryDetails.'</p>
                <p style="font-size: 25px; text-align: center; margin-bottom: 5px;">'.$mainDetails.'</p>
                <div>'.$button.'</div>
                </div>');
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
