<?php

namespace App\Notifications;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClinicalRxDailyTransferNotification extends Notification
{
    use Queueable;

    private $rxDailyTransfers, $summary, $emails, $status;

    /**
     * Create a new notification instance.
     */
    public function __construct($rxDailyTransfers, $summary, $emails, $status)
    {
        $this->rxDailyTransfers = $rxDailyTransfers;
        $this->summary = $summary;
        $this->emails = $emails;
        $this->status = $status;
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
        $rxDailyTransfers = $this->rxDailyTransfers;
        $summary = isset($this->summary->data) ? $this->summary->data : [];

        $emailSubject = '';

        $mainDetails = '';
        $summaryDetails = '';

        if(!empty($rxDailyTransfers)) {

            $date = $summary->formatted_date ?: '';

            $emailSubject = 'Daily ';
            if($this->status == 'pending') {
                $emailSubject .= 'Pending ';
            }
            $emailSubject .= 'Rx Transfer for '.$date;

            $topPendingProvidersHtml = '';

            $topPendingProviders = $summary->top_pending_providers;
            foreach($topPendingProviders as $tp) {
                $topPendingProvidersHtml .= '
                    <tr>
                        <th style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">'.$tp->provider.'</th>
                        <th style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$tp->sum_expected_rx.'</th>
                    </tr>
                ';
            }
            
            if(!empty($date)) {
                $summaryDetails = '<table style="width: 100%; padding: 10px; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Total Patients</td>
                        <th style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$summary->count_patient_names.'</th>
                    </tr>
                    <tr>
                        <td style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Patients w/ pending</td>
                        <th style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$summary->count_pending_patient_names.'</th>
                    </tr>';

                if($this->status != 'pending') {
                    $summaryDetails .= '
                        <tr>
                            <td style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Called patients</td>
                            <th style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$summary->count_is_called_status.'</th>
                        </tr>';
                    $summaryDetails .= '
                        <tr>
                            <td style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Transfers</td>
                            <th style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$summary->count_is_transfer_yes.'</th>
                        </tr>';
                }

                $summaryDetails .= '
                    <tr>
                        <td style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Expected scripts</td>
                        <th style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$summary->sum_expected_rx.'</th>
                    </tr>
                    <tr>
                        <td style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Received scripts</td>
                        <th style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$summary->count_not_pending_expected_rx.'</th>
                    </tr>
                    <tr>
                        <td style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;">Pending scripts</td>
                        <th style="text-align: left; border: 1px solid #eee; padding: 5px;">'.$summary->count_pending_expected_rx.'</th>
                    </tr>';

                if($this->status == 'pending') {
                    $summaryDetails .= '
                        <tr>
                            <td style="text-align: left; width: 32%; border: 1px solid #eee; padding: 5px;" colspan="2">Top 3 Providers w/ pending:</td>
                        </tr>'.$topPendingProvidersHtml;
                }

                $summaryDetails .= '</table>';

            }

            $mainDetails .= '<table style="width: 100%; padding: 10px; border-collapse: collapse;">
                <tr>
                    <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">Patient Name</th>
                    <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">DOB</th>
                    <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">Meds</th>
                    ';
            
            if($this->status != 'pending') {
                $mainDetails .= '
                    <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">Call status</th>
                    <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">Transfer</th>';
            }

            $mainDetails .= '<th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">MA</th>
                    <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">Provider</th>
                    <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">Pharmacy</th>
                    <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">Scripts Expected</th>
                    <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">Received</th>
                    <th style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">Remarks</th>
                </tr>';

            foreach($rxDailyTransfers as $bs) {
                $patient = isset($bs->patient) ? $bs->patient : null;

                $patient_fullname = '';
                if(isset($patient->firstname)) {
                    $patient_fullname = $patient->getDecryptedLastname().', '.$patient->getDecryptedFirstname();
                } else {
                    $patient_fullname = $bs->patient_name;
                }

                $birth_date = '';
                if(!empty($bs->birth_date)) {
                    $birth_date = date('F d, Y', strtotime($bs->birth_date));
                }

                $conditionHtml = '';

                if($this->status != 'pending') {
                    $conditionHtml = '<td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$bs->call_status.'</td>
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$bs->is_transfer.'</td>';
                }

                $receivedColor = '#e62e2e';
                if($bs->is_received == 'Yes') {
                    $receivedColor = '#29cc39';
                }

                $mainDetails .= '
                    <tr>
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$patient_fullname.'</td>
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$birth_date.'</td>
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$bs->medication_description.'</td>
                        '.$conditionHtml.'
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$bs->is_ma.'</td>
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$bs->provider.'</td>
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$bs->fax_pharmacy.'</td>
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$bs->expected_rx.'</td>
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px; color: white !important; background-color: '.$receivedColor.' !important;">'.$bs->is_received.'</td>
                        <td style="text-align: left; width: 10%; border: 1px solid #eee; padding: 5px;">'.$bs->remarks.'</td>
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
            $authEmployee = $this->rxDailyTransfers[0]->user->employee;
            if(isset($authEmployee->id)) {
                $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
            }
        }

        $subtext = 'TRP: Clinical > Daily ';
        if($this->status == 'pending') {
            $subtext .= 'Pending ';
        }
        $subtext .= 'Rx Transfer';

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
