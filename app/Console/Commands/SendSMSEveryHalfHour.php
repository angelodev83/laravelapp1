<?php

namespace App\Console\Commands;

use App\Repositories\PatientPrescriptionSmsLogRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SendSMSEveryHalfHour extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-s-m-s-every-half-hour';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $patientPrescriptionSmsLogRepository;

    public function __construct(PatientPrescriptionSmsLogRepository $patientPrescriptionSmsLogRepository)
    {
        parent::__construct();
        $this->patientPrescriptionSmsLogRepository = $patientPrescriptionSmsLogRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $res = $this->patientPrescriptionSmsLogRepository->saveQueuing('created');
        if($res) {
            $this->info('Run Every Half Hour Scheduler - Response:  message: '.$res);
            $res = $this->patientPrescriptionSmsLogRepository->saveQueuing('others');
            if($res) {
                $this->info('Run Every Half Hour Scheduler - Response:  message: '.$res);
                $res = $this->patientPrescriptionSmsLogRepository->sendSMSAlert();
                if($res) {
                    $this->info('Run Every Half Hour Scheduler - Response:  message: '.$res);
                }
            }
        }
    }
}
