<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResponsiveTableMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tableData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tableData)
    {
        $this->tableData = $tableData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.responsive_table')
                    ->with(['tableData' => $this->tableData]);
    }
}
