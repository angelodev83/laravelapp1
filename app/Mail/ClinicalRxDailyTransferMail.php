<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClinicalRxDailyTransferMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date = $this->data['summary']->formatted_date ?: '';
        $pending =  strtolower($this->data['status']) == 'pending' ? 'Pending ' : '';
        $emailSubject = 'Daily '.$pending.'Rx Transfer for '.$date;
        $createdBy = '';
        $user = auth()->user();

        if(isset($user->id)) {
            $authEmployee = Employee::select('id','firstname', 'lastname','image','initials_random_color')->where('user_id', auth()->user()->id)->first();
            $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
        } else {
            $authEmployee = Employee::findOrFail(1);
            if(isset($authEmployee->id)) {
                $createdBy = $authEmployee->firstname.' '.$authEmployee->lastname;
            }
        }
        return $this->view('emails.clinical_rx_daily_transfers')
                    ->subject($emailSubject)
                    ->with([
                        'data'          => $this->data, 
                        'emailSubject'  => $emailSubject,
                        'createdBy'     => $createdBy
                    ]);
    }

}
