<?php

namespace App\Console\Commands;

use App\Interfaces\ITaskRepository;
use App\Interfaces\ITicketRepository;
use App\Repositories\JotForm\PatientPrescriptionTransferRepository;
use App\Repositories\JotForm\PatientRepository;
use App\Repositories\JotForm\PharmacyServiceSatisfactionSurveyRepository;
use App\Tasks\SetTask;
use Illuminate\Console\Command;

ini_set('max_execution_time', '3600');

class Daily0800Task extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily0800-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $taskRepository
        , $ticketRepository
        , $patientPrescriptionTransferRepository
        , $jotFormPatientRepository
        , $pharmacyServiceSatisfactionSurveyRepository
    ;

    public function __construct(
        ITaskRepository $taskRepository
        , ITicketRepository $ticketRepository
        , PatientPrescriptionTransferRepository $patientPrescriptionTransferRepository
        , PatientRepository $jotFormPatientRepository
        , PharmacyServiceSatisfactionSurveyRepository $pharmacyServiceSatisfactionSurveyRepository
    )
    {
        parent::__construct();
        $this->taskRepository = $taskRepository;
        $this->ticketRepository = $ticketRepository;
        $this->patientPrescriptionTransferRepository = $patientPrescriptionTransferRepository;
        $this->jotFormPatientRepository = $jotFormPatientRepository;
        $this->pharmacyServiceSatisfactionSurveyRepository = $pharmacyServiceSatisfactionSurveyRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1AM PST = 8AM UTC
        $email1 = 'zedrec@mgmt88.com';
        $email2 = 'erwin@mgmt88.com';
        $email3 = 'vee@mgmt88.com';
        $email4 = 'lyda@mgmt88.com';
        $email5 = 'lyda@mgmt88.com';
        $watchers3 = ['lyda@mgmt88.com'];

        $timezone = 'America/Los_Angeles';

        $daily = new SetTask($this->taskRepository);

        $currDate = $daily->convertToTimeZone($timezone);
        $currDate = date('Y-m-d', strtotime($currDate));

        $taskList1 = [
            // erwin all
            'Cash Register Report' => [
                'daily' => true,
                'weekly' => [1], // weekday 1-7
                'monthly' => [5], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => ['current' => false, 'previous' => true, 'day' => 6, 'hasPrefix' => true]
            ],
            'RTS Report' => [
                'daily' => false,
                'weekly' => [], // weekday 1-7
                'monthly' => [1], // datedays 1-30/31
                'yearly' => []
            ],
            'Que Status Report' => [
                'daily' => false,
                'weekly' => [5], // weekday 1-7
                'monthly' => [1], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => ['current' => false, 'previous' => true, 'day' => 6, 'hasPrefix' => true],
            ],
            'COGS Inventory Report' => [
                'daily' => false,
                'weekly' => [5], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => ['current' => false, 'previous' => true, 'day' => 6, 'hasPrefix' => true]
            ],
            'AR Aging Report' => [
                'daily' => false,
                'weekly' => [4], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => ['current' => false, 'previous' => true, 'day' => 6]
            ],
            'Collected Payments Report' => [
                'daily' => false,
                'weekly' => [4], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => ['current' => false, 'previous' => true, 'day' => 6]
            ],
            'Inventory Audit Report' => [
                'daily' => false,
                'weekly' => [5], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'current' => false, 'previous' => true, 'day' => 6, 'hasPrefix' => false, 
                    'tag_id' => 11
                ],
            ],
        ];

        $taskList2 = [
            // erwin all
            'New Patient Report' => [
                'daily' => false,
                'weekly' => [1], // weekday 1-7
                'monthly' => [1], // datedays 1-30/31
                'yearly' => []
            ],
            // 'Gross Sales Report' => [
            //     'daily' => true,
            //     'weekly' => [1], // weekday 1-7
            //     'monthly' => [5], // datedays 1-30/31
            //     'yearly' => []
            // ],
            // 'Completed Sales Report' => [
            //     'daily' => true,
            //     'weekly' => [1], // weekday 1-7
            //     'monthly' => [5], // datedays 1-30/31
            //     'yearly' => []
            // ],
            // 'Reversal Summary' => [
            //     'daily' => true,
            //     'weekly' => [1], // weekday 1-7
            //     'monthly' => [5], // datedays 1-30/31
            //     'yearly' => []
            // ],
            'Transferred In/Out Report' => [
                'daily' => false,
                'weekly' => [1], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => []
            ],
            // erwin
            // 'Partial Fill Report' => [
            //     'daily' => true,
            //     'weekly' => [], // weekday 1-7
            //     'monthly' => [], // datedays 1-30/31
            //     'yearly' => []
            // ],
            // 'IOU Report' => [
            //     'daily' => true,
            //     'weekly' => [], // weekday 1-7
            //     'monthly' => [], // datedays 1-30/31
            //     'yearly' => []
            // ],
            'Future Fill Report (Ordering)' => [
                'daily' => false,
                'weekly' => [3], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate
                ]
            ],
            'Shipping Data Profitability Matrix' => [
                'daily' => false,
                'weekly' => [], // weekday 1-7
                'monthly' => [1], // datedays 1-30/31
                'yearly' => []
            ],
            'Inventory Year End Practices' => [
                'daily' => false,
                'weekly' => [], // weekday 1-7
                'monthly' => [20], // datedays 1-30/31
                'yearly' => [12], // month 1-12
            ],
            'New Patients - Completed med pick-up' => [
                'daily' => false,
                'weekly' => [3], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate,
                    'watchers' => ['pia@mgmt88.com']
                ]
            ],
            'New RX - Clinical team will do a wellness check.' => [
                'daily' => false,
                'weekly' => [3], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate,
                    'watchers' => ['pia@mgmt88.com']
                ]
            ],
            'Financial EOD Report' => [
                'daily' => false,
                'weekly' => [1,2,3,4,5], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate, 
                    'suffix' => ' for Today - '.date('F d, Y', strtotime($currDate))
                ]
            ],
            'Procurement EOD Report' => [
                'daily' => false,
                'weekly' => [1,2,3,4,5], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate, 
                    'suffix' => ' for Today - '.date('F d, Y', strtotime($currDate))
                ]
            ],
            'Daily IOU ordering' => [
                'daily' => false,
                'weekly' => [1,2,3,4,5], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate, 
                    'suffix' => ' for Today - '.date('F d, Y', strtotime($currDate))
                ]
            ],
            'Send AR Aging Report' => [
                'daily' => false,
                'weekly' => [4], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate, 
                    'suffix' => ' for Today - '.date('F d, Y', strtotime($currDate))
                ]
            ],
        ];

        $taskList3 = [
            // doc vee
            'Send Refills pending for THRC providers and nonTHRC providers' => [
                'daily' => true,
                'weekly' => [], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => []
            ],
            // 'Review Daily for Brand MTM Review:  Add new therapy change with Brand' => [
            //     'daily' => true,
            //     'weekly' => [], // weekday 1-7
            //     'monthly' => [], // datedays 1-30/31
            //     'yearly' => []
            // ],
            'Fill up TRP Leadership Call - Clinical' => [
                'daily' => false,
                'weekly' => [4], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'description' => '<p>https://form.jotform.com/241856794487476</p>',
                'weeklyDetails' => ['day' => null, 'due_date' => $currDate]
            ],
            'Prepare and Upload MOM documents on the Intranet' => [
                'daily' => false,
                'weekly' => [5], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => ['day' => null, 'due_date' => $currDate]
            ],
            // 'MTM weekly data' => [
            //     'daily' => false,
            //     'weekly' => [1,2,3,4,5], // weekday 1-7
            //     'monthly' => [], // datedays 1-30/31
            //     'yearly' => [],
            //     'weeklyDetails' => ['day' => null, 'due_date' => $currDate]
            // ],
            // 'Post Antibiotics' => [
            //     'daily' => false,
            //     'weekly' => [1,2,3,4,5], // weekday 1-7
            //     'monthly' => [], // datedays 1-30/31
            //     'yearly' => [],
            //     'weeklyDetails' => ['day' => null, 'due_date' => $currDate]
            // ],
            // 'Clinical Care Goals' => [
            //     'daily' => false,
            //     'weekly' => [1,2,3,4,5], // weekday 1-7
            //     'monthly' => [], // datedays 1-30/31
            //     'yearly' => [],
            //     'weeklyDetails' => ['day' => null, 'due_date' => $currDate]
            // ],
            // 'CCO cases' => [
            //     'daily' => false,
            //     'weekly' => [1,2,3,4,5], // weekday 1-7
            //     'monthly' => [], // datedays 1-30/31
            //     'yearly' => [],
            //     'weeklyDetails' => ['day' => null, 'due_date' => $currDate]
            // ],
            // '** Therapy changes for Brand** to yield profitability.' => [
            //     'daily' => false,
            //     'weekly' => [1,2,3,4,5], // weekday 1-7
            //     'monthly' => [], // datedays 1-30/31
            //     'yearly' => [],
            //     'weeklyDetails' => ['day' => null, 'due_date' => $currDate]
            // ],
            'Weekly RX from THRC provider to TRP' => [
                'daily' => false,
                'weekly' => [6], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => ['day' => null]
            ],
            'Send pending refill requests for TRHC/Outside providers' => [
                'daily' => false,
                'weekly' => [1,2,3,4,5], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => ['day' => null, 'due_date' => $currDate]
            ],
            'Brand switching for items in the IOU' => [
                'daily' => false,
                'weekly' => [1,2,3,4,5], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate,
                    'description' => 'For items in the IOU/those that will be ordered, check meds that can be switched from generic to brand. This allows TRP to yield more profit', 
                    'watchers' => ['erwin@mgmt88.com']
                ]
            ],
            'Adding brand meds to existing patients' => [
                'daily' => false,
                'weekly' => [1], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate,
                    'description' => 'For patients with diabetes, add Januvia to their medication regimen. This allows TRP to yield more profit'
                ]
            ],
            'Patient and script summary for TRHC' => [
                'daily' => false,
                'weekly' => [1], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate,
                    'description' => 'Send tally of live patients and number of scripts sent by TRHC'
                ]
            ],
            'Script transfers summary' => [
                'daily' => false,
                'weekly' => [1], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate,
                    'description' => 'Send tally of all patients who agreed to have all their meds filled by TRP, those that are pending from the MAs/providers'
                ]
            ],
            'Bridged patients' => [
                'daily' => false,
                'weekly' => [1], // weekday 1-7
                'monthly' => [], // datedays 1-30/31
                'yearly' => [],
                'weeklyDetails' => [
                    'day' => null, 'due_date' => $currDate,
                    'description' => 'All the patients bridged through Brooke for the week'
                ]
            ],
        ];

        $taskList4 = [
            // hero
            // 'Fill up TRP Leadership Call - Ops' => [
            //     'daily' => false,
            //     'weekly' => [4], // weekday 1-7
            //     'monthly' => [], // datedays 1-30/31
            //     'yearly' => [],
            //     'description' => '<p>https://form.jotform.com/241855927933065</p>'
            // ]
        ];

        $taskList5 = [
            // maam lyda
            // 'Fill up TRP Leadership Call - Compliance' => [
            //     'daily' => false,
            //     'weekly' => [4], // weekday 1-7
            //     'monthly' => [], // datedays 1-30/31
            //     'yearly' => [],
            //     'description' => '<p>https://form.jotform.com/241856159446466</p>'
            // ]
        ];

        $task1 = $daily->daily0800CustomReminder($email2, $taskList1, $timezone);
        $task2 = $daily->daily0800CustomReminder($email2, $taskList2, $timezone);
        $task3 = $daily->daily0800CustomReminder($email3, $taskList3, $timezone);
        $task4 = $daily->daily0800CustomReminder($email4, $taskList4, $timezone);
        $task5 = $daily->daily0800CustomReminder($email5, $taskList5, $timezone);
        
        $resArr = array_merge($task1, $task2, $task3, $task4, $task5);

        foreach($resArr['success'] as $res) {
            $this->info('Done: '.$res.' (Daily/Weekly/Monthly/Yearly - 08:00 am utc) '.date('Y-m-d H:i:s'));
        }
        foreach($resArr['fail'] as $res) {
            $this->warn('Failed: '.$res.' (Daily/Weekly/Monthly/Yearly - 08:00 am utc) '.date('Y-m-d H:i:s'));
        }

        $autoArchive = $this->taskRepository->autoArchive();
        $this->info('Done: '.$autoArchive.' (Auto Archive Tasks - 08:00 am utc) '.date('Y-m-d H:i:s'));

        $autoArchive = $this->ticketRepository->autoArchive();
        $this->info('Done: '.$autoArchive.' (Auto Archive Tickets - 08:00 am utc) '.date('Y-m-d H:i:s'));

        $res = $this->patientPrescriptionTransferRepository->sync();
        $this->info('Done: '.$res['message']);
        $res = $this->jotFormPatientRepository->sync();
        $this->info('Done: '.$res['message']);
        $res = $this->pharmacyServiceSatisfactionSurveyRepository->sync('242175635430453');
        $this->info('Done: Low Scores - '.$res['message']);
        $res = $this->pharmacyServiceSatisfactionSurveyRepository->sync('242176934304456');
        $this->info('Done: High Score - '.$res['message']);

    }
}
