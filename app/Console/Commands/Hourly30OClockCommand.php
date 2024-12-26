<?php

namespace App\Console\Commands;

use App\Repositories\PatientPrescriptionSmsLogRepository;
use App\Repositories\PatientRepository;
use Illuminate\Console\Command;

class Hourly30OClockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:hourly30-o-clock-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $patientPrescriptionSmsLogRepository, $patientRepository;

    public function __construct(
        PatientPrescriptionSmsLogRepository $patientPrescriptionSmsLogRepository
        , PatientRepository $patientRepository
    )
    {
        parent::__construct();
        $this->patientPrescriptionSmsLogRepository = $patientPrescriptionSmsLogRepository;
        $this->patientRepository = $patientRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Send Prescription SMS to patients
        $res = $this->patientPrescriptionSmsLogRepository->saveQueuing('created');
        if($res) {
            $this->info('Run Every Hour At 30 o.clock Scheduler - Response:  message: '.$res);
            $res = $this->patientPrescriptionSmsLogRepository->saveQueuing('others');
            if($res) {
                $this->info('Run Every Hour At 30 o.clock Scheduler - Response:  message: '.$res);
                $res = $this->patientPrescriptionSmsLogRepository->sendSMSAlert();
                if($res) {
                    $this->info('Run Every Hour At 30 o.clock Scheduler - Response:  message: '.$res);
                }
            }
        }

        // Sync Pioneer Patient Masterlist
        $phamacy_store_id = 1;
        $res = $this->patientRepository->syncPioneerPatientMasterlist($phamacy_store_id);
        if($res) {
            $this->info('Run Every Hour At 30 o.clock Scheduler - Response:  message: '.$res);
        }
    }
}
