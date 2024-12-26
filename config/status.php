<?php
$prescribe = [
    [
        'id'            => 1,
        'name'          => 'Done', 
        'description'   => 'Done',
        'color'         => 'green',
        'text_color'    => '#000000',
        'sort'          => 1,
        'category'      => 'prescribe',
    ],
    [
        'id'            => 2,
        'name'          => 'Telemed Bridge', 
        'description'   => 'Telemed Bridge',
        'color'         => 'light blue',
        'text_color'    => '#000000',
        'sort'          => 2,
        'category'      => 'prescribe',
    ],
    [
        'id'            => 3,
        'name'          => '1st Prescriber Outreach', 
        'description'   => '1st Prescriber Outreach',
        'color'         => 'light blue',
        'text_color'    => '#000000',
        'sort'          => 3,
        'category'      => 'prescribe',
    ],
    [
        'id'            => 4,
        'name'          => '2nd Prescriber Outreach', 
        'description'   => '2nd Prescriber Outreach',
        'color'         => 'light blue',
        'text_color'    => '#000000',
        'sort'          => 4,
        'category'      => 'prescribe',
    ],
    [
        'id'            => 5,
        'name'          => '3rd Prescriber Outreach', 
        'description'   => '3rd Prescriber Outreach',
        'color'         => 'light blue',
        'text_color'    => '#000000',
        'sort'          => 5,
        'category'      => 'prescribe',
    ],
    [
        'id'            => 6,
        'name'          => 'Outreach Cooling Period', 
        'description'   => 'Outreach Cooling Period',
        'color'         => 'yellow',
        'text_color'    => '#000000',
        'sort'          => 6,
        'category'      => 'prescribe',
    ],
    [
        'id'            => 7,
        'name'          => 'Failed Prescriber Outreach', 
        'description'   => 'Failed Prescriber Outreach',
        'color'         => 'red',
        'text_color'    => '#000000',
        'sort'          => 7,
        'category'      => 'prescribe',
    ]
];

$stage = [
    [
        'id'            => 101,
        'name'          => 'Pending', 
        'description'   => 'Pending Stage',
        'color'         => 'pink',
        'text_color'    => '#000000',
        'sort'          => 1,
        'category'      => 'stage',
    ],
    [
        'id'            => 102,
        'name'          => 'In-progress', 
        'description'   => 'In-progress Stage',
        'color'         => 'yellow',
        'text_color'    => '#000000',
        'sort'          => 2,
        'category'      => 'stage',
    ],
    [
        'id'            => 103,
        'name'          => 'Uploaded', 
        'description'   => 'Uploaded Stage',
        'color'         => 'blue',
        'text_color'    => '#000000',
        'sort'          => 3,
        'category'      => 'stage',
    ]
];

$task = [
    [
        'id'            => 201,
        'name'          => 'To Do', 
        'description'   => 'Default Status on create',
        'color'         => '#6c757d',
        'text_color'    => 'white',
        'class'         => 'secondary',
        'sort'          => 1,
        'category'      => 'task'
    ],
    [
        'id'            => 202,
        'name'          => 'In Progress', 
        'description'   => 'The ticket is in progress',
        'color'         => '#15a0a3',
        'text_color'    => 'white',
        'class'         => 'primary',
        'sort'          => 2,
        'category'      => 'task'
    ],
    [
        'id'            => 203,
        'name'          => 'To Analyze', 
        'description'   => 'Needs analyzation',
        'color'         => '#007bff',
        'text_color'    => 'white',
        'class'         => 'info2',
        'sort'          => 3,
        'category'      => 'task'
    ],
    [
        'id'            => 204,
        'name'          => 'To Verify', 
        'description'   => 'Needs verification',
        'color'         => '#ffc107',
        'text_color'    => 'black',
        'class'         => 'warning',
        'sort'          => 4,
        'category'      => 'task'
    ],
    [
        'id'            => 205,
        'name'          => 'Waiting', 
        'description'   => 'Waiting for someone or something to be done first',
        'color'         => '#fd3550',
        'text_color'    => 'white',
        'class'         => 'danger',
        'sort'          => 5,
        'category'      => 'task'
    ],
    [
        'id'            => 206,
        'name'          => 'Completed', 
        'description'   => 'Ticket is done',
        'color'         => '#15ca20',
        'text_color'    => 'white',
        'class'         => 'success',
        'sort'          => 6,
        'category'      => 'task'
    ]
];

$shipment = [
    [
        'id'            => 301,
        'name'          => 'Label to be created', 
        'description'   => 'Label to be created',
        'color'         => 'green',
        'text_color'    => '#FD9F43',
        'class'         => 'info2',
        'icon'          => 'fa fa-file-lines',
        'sort'          => 1,
        'category'      => 'shipment'
    ],
    [
        'id'            => 302,
        'name'          => 'Label Created', 
        'description'   => 'Label Created',
        'color'         => 'red',
        'text_color'    => '#000000',
        'class'         => 'info',
        'icon'          => 'fa fa-cart-flatbed',
        'sort'          => 2,
        'category'      => 'shipment'
    ],
    [
        'id'            => 303,
        'name'          => 'Label printed', 
        'description'   => 'Label printed',
        'color'         => 'green',
        'text_color'    => '#000000',
        'class'         => 'info',
        'icon'          => 'fa fa-print',
        'sort'          => 3,
        'category'      => 'shipment'
    ],
    [
        'id'            => 304,
        'name'          => 'Picked up Orders', 
        'description'   => 'Picked up',
        'color'         => 'yellow',
        'text_color'    => '#000000',
        'class'         => 'info',
        'icon'          => 'fa fa-cart-flatbed',
        'sort'          => 4,
        'category'      => 'shipment'
    ],
    [
        'id'            => 305,
        'name'          => 'In Transit Orders', 
        'description'   => 'In Transit',
        'color'         => 'gray',
        'text_color'    => '#000000',
        'class'         => 'primary',
        'icon'          => 'fa fa-truck-fast',
        'sort'          => 5,
        'category'      => 'shipment'
    ],
    [
        'id'            => 306,
        'name'          => 'Pending Orders', 
        'description'   => 'Pending',
        'color'         => 'purple',
        'text_color'    => '#000000',
        'class'         => 'warning',
        'icon'          => 'fa fa-clock',
        'sort'          => 6,
        'category'      => 'shipment'
    ],
    [
        'id'            => 307,
        'name'          => 'On Hold Orders', 
        'description'   => 'On Hold',
        'color'         => 'purple',
        'text_color'    => '#000000',
        'class'         => 'danger',
        'icon'          => 'fa fa-stop',
        'sort'          => 7,
        'category'      => 'shipment'
    ],
    [
        'id'            => 308,
        'name'          => 'Delivered Orders', 
        'description'   => 'Delivered',
        'color'         => 'green',
        'text_color'    => '#000000',
        'class'         => 'success',
        'icon'          => 'fa fa-square-check',
        'sort'          => 8,
        'category'      => 'shipment'
    ]
];

$priority = [
    [
        'id'            => 401,
        'name'          => 'LOW', 
        'description'   => 'Low complexity',
        'color'         => '#6c757d',
        'class'         => 'secondary',
        'sort'          => 1,
        'category'      => 'priority'
    ],
    [
        'id'            => 402,
        'name'          => 'NORMAL', 
        'description'   => 'Second-level complexity',
        'color'         => '#15a0a3',
        'class'         => 'primary',
        'sort'          => 2,
        'category'      => 'priority'
    ],
    [
        'id'            => 403,
        'name'          => 'HIGH', 
        'description'   => 'Third-level complexity',
        'color'         => '#ffc107',
        'class'         => 'warning',
        'sort'          => 3,
        'category'      => 'priority'
    ],
    [
        'id'            => 404,
        'name'          => 'URGENT', 
        'description'   => 'Top Priority',
        'color'         => '#fd3550',
        'class'         => 'danger',
        'sort'          => 4,
        'category'      => 'priority'
    ]
];

$inmar = [
    [
        'id'            => 30,
        'name'          => 'Recieved', 
        'description'   => 'Recieved',
        'color'         => '#7a70f0',
        'class'         => 'primary',
        'sort'          => 1,
        'category'      => 'inmar'
    ],
    [
        'id'            => 31,
        'name'          => 'Submitted', 
        'description'   => 'Submitted',
        'color'         => '#1ec66c',
        'class'         => 'secondary',
        'sort'          => 2,
        'category'      => 'inmar'
    ],
    [
        'id'            => 32,
        'name'          => 'In Transit', 
        'description'   => 'In Transit',
        'color'         => '#38b5ff',
        'class'         => 'info',
        'sort'          => 3,
        'category'      => 'inmar'
    ],
    [
        'id'            => 33,
        'name'          => 'Missing', 
        'description'   => 'Missing',
        'color'         => '#38b5ff',
        'class'         => 'danger',
        'sort'          => 4,
        'category'      => 'inmar'
    ],
    [
        'id'            => 34,
        'name'          => 'Completed', 
        'description'   => 'Completed',
        'color'         => '#38b5ff',
        'class'         => 'success',
        'sort'          => 5,
        'category'      => 'inmar'
    ],
];

$returnType = [
    [
        'id'            => 40,
        'name'          => 'RETURNS', 
        'description'   => 'Returns',
        'color'         => '#7a70f0',
        'class'         => 'secondary',
        'sort'          => 1,
        'category'      => 'return_type'
    ],
    [
        'id'            => 41,
        'name'          => 'EXPIRED', 
        'description'   => 'Expired',
        'color'         => '#38b5ff',
        'class'         => 'primary',
        'sort'          => 2,
        'category'      => 'return_type'
    ],
    [
        'id'            => 42,
        'name'          => 'REBATE', 
        'description'   => 'Rebate',
        'color'         => '#1ec66c',
        'class'         => 'warning',
        'sort'          => 3,
        'category'      => 'return_type'
    ]
];

$return = [
    [
        'id'            => 501,
        'name'          => 'Waiting For Pick Up', 
        'description'   => 'Low complexity',
        'color'         => 'secondary',
        'class'         => 'secondary',
        'sort'          => 1,
        'category'      => 'return'
    ],

];

$invoice = [
    [
        'id'            => 601,
        'name'          => 'PENDING', 
        'description'   => 'Pending',
        'color'         => '#7a70f0',
        'class'         => 'secondary',
        'sort'          => 2,
        'category'      => 'invoice'
    ],
    [
        'id'            => 602,
        'name'          => 'PAID', 
        'description'   => 'Paid',
        'color'         => '#38b5ff',
        'class'         => 'primary',
        'sort'          => 1,
        'category'      => 'invoice'
    ],

];

$procurementOrders = [
    [
        'id'            => 701,
        'name'          => 'NEW REQUEST', 
        'description'   => 'New Request',
        'color'         => '#6c757d',
        'text_color'    => 'white',
        'class'         => 'secondary',
        'icon'          => 'fa fa-file-lines',
        'sort'          => 1,
        'category'      => 'procurement_order'
    ],
    [
        'id'            => 702,
        'name'          => 'RECEIVED', 
        'description'   => 'Received',
        'color'         => '#007bff',
        'text_color'    => 'white',
        'class'         => 'info2',
        'icon'          => 'fa fa-cart-flatbed',
        'sort'          => 2,
        'category'      => 'procurement_order'
    ],
    [
        'id'            => 703,
        'name'          => 'IN TRANSIT', 
        'description'   => 'In Transit',
        'color'         => '#15a0a3',
        'text_color'    => 'white',
        'class'         => 'primary',
        'icon'          => 'fa fa-print',
        'sort'          => 3,
        'category'      => 'procurement_order'
    ],
    [
        'id'            => 704,
        'name'          => 'SUBMITTED', 
        'description'   => 'Submitted',
        'color'         => '#ffc107',
        'text_color'    => 'black',
        'class'         => 'warning',
        'icon'          => 'fa fa-cart-flatbed',
        'sort'          => 4,
        'category'      => 'procurement_order'
    ],
    [
        'id'            => 705,
        'name'          => 'MISSING ORDER', 
        'description'   => 'Missing',
        'color'         => '#fd3550',
        'text_color'    => 'white',
        'class'         => 'danger',
        'icon'          => 'fa fa-truck-fast',
        'sort'          => 5,
        'category'      => 'procurement_order'
    ],
    [
        'id'            => 706,
        'name'          => 'COMPLETED', 
        'description'   => 'Completed',
        'color'         => '#15ca20',
        'text_color'    => 'white',
        'class'         => 'success',
        'icon'          => 'fa fa-clock',
        'sort'          => 6,
        'category'      => 'procurement_order'
    ]
];

$newsAndEventsType = [
    [
        'id'            => 801,
        'name'          => 'Local', 
        'sort'          => 1,
        'category'      => 'news_and_events'
    ],
    [
        'id'            => 802,
        'name'          => 'URL', 
        'sort'          => 2,
        'category'      => 'news_and_events'
    ]
];

$clinicalKpiCallStatus = [
    [
        'id'            => 811,
        'name'          => 'Called', 
        'sort'          => 1,
        'category'      => 'kpi_call_status'
    ],
    [
        'id'            => 812,
        'name'          => 'Voicemail', 
        'sort'          => 2,
        'category'      => 'kpi_call_status'
    ]
];

$clinicalDiagnosisStatus = [
    [
        'id'            => 831,
        'name'          => 'Hypertension',
        'description'   => '1.	Control blood pressure to sustained goal
                            2.	Education and patient self management ', 
        'sort'          => 1,
        'category'      => 'diagnosis'
    ],
    [
        'id'            => 832,
        'name'          => 'Diabetes', 
        'description'   => '1.	Control A1c to goal of x% per x guidelines
                            2.	Comprehensive medication review
                            3.	Education and patient self management
                            4.	Foot and or eye exam/referral needed
                            5.	Hypoglycemia/hyperglycemia education ',
        'sort'          => 2,
        'category'      => 'diagnosis'
    ],
    [
        'id'            => 833,
        'name'          => 'Statins', 
        'description'   => '',
        'sort'          => 2,
        'category'      => 'diagnosis'
    ],
    [
        'id'            => 834,
        'name'          => 'Infection',
        'description'   => '', 
        'sort'          => 2,
        'category'      => 'diagnosis'
    ],
    [
        'id'            => 835,
        'name'          => 'Asthma', 
        'description'   => '1.	Asthma education and patient self-management
                            2.	Asthma therapy assessment 
                            3.	COPD education and patient self-management 
                            4.	COPD therapy assessment 
                            5.	Counselling on the use of inhaler',
        'sort'          => 2,
        'category'      => 'diagnosis'
    ],
];

$clinicalProviderStatus = [
    [
        'id'            => 841,
        'name'          => 'Outside', 
        'sort'          => 1,
        'category'      => 'clinical_provider'
    ],
    [
        'id'            => 842,
        'name'          => 'TRHC', 
        'sort'          => 2,
        'category'      => 'clinical_provider'
    ],
];

$leaveStatus = [
    [
        'id'            => 901,
        'name'          => 'Requested', 
        'description'   => 'Requested',
        'color'         => '#6c757d',
        'text_color'    => 'white',
        'class'         => 'secondary',
        'sort'          => 1,
        'category'      => 'leave'
    ],
    [
        'id'            => 902,
        'name'          => 'Approved', 
        'description'   => 'Approved',
        'color'         => '#15ca20',
        'text_color'    => 'white',
        'class'         => 'success',
        'sort'          => 2,
        'category'      => 'leave'
    ],
    [
        'id'            => 903,
        'name'          => 'Rejected', 
        'description'   => 'Rejected',
        'color'         => '#fd3550',
        'text_color'    => 'white',
        'class'         => 'danger',
        'sort'          => 3,
        'category'      => 'leave'
    ],
];

$returnToStockV2 = [
    [
        'id'            => 921,
        'name'          => '7DS', 
        'description'   => '7DS',
        'color'         => '#1590b4',
        'text_color'    => 'white',
        'class'         => '',
        'sort'          => 1,
        'category'      => 'rts',
        'light_color'   => '#b3ebfa'
    ],
    [
        'id'            => 922,
        'name'          => '14DS', 
        'description'   => '14DS',
        'color'         => '#7438c2',
        'text_color'    => 'white',
        'class'         => '',
        'sort'          => 2,
        'category'      => 'rts',
        'light_color'   => '#cfbce9'
    ],
    [
        'id'            => 923,
        'name'          => 'RTS', 
        'description'   => 'RETURN TO STOCK',
        'color'         => '#137eff',
        'text_color'    => 'white',
        'class'         => '',
        'sort'          => 3,
        'category'      => 'rts',
        'light_color'   => '#c8e4fd'
    ],
    [
        'id'            => 924,
        'name'          => 'FOR PICK-UP/DELIVERY/SHIPMENT', 
        'description'   => 'FOR PICK-UP/DELIVERY/SHIPMENT',
        'color'         => '#fec106',
        'text_color'    => 'black',
        'class'         => 'warning',
        'sort'          => 4,
        'category'      => 'rts',
        'light_color'   => '#fbebbc'
    ],
    [
        'id'            => 925,
        'name'          => 'CLEARED', 
        'description'   => 'CLEARED',
        'color'         => '#1dca21',
        'text_color'    => 'white',
        'class'         => 'success',
        'sort'          => 5,
        'category'      => 'rts',
        'light_color'   => '#cfefd0'
    ],
];

$clinicalRenewals = [
    [
        'id'            => 951,
        'name'          => 'DENIED', 
        'description'   => 'DENIED',
        'color'         => '#6c757d',
        'text_color'    => 'white',
        'class'         => '',
        'sort'          => 1,
        'category'      => 'renewal',
        'light_color'   => '#e1e7ed'
    ],
    [
        'id'            => 952,
        'name'          => 'RENEW', 
        'description'   => 'RENEW',
        'color'         => '#fec106',
        'text_color'    => 'black',
        'class'         => '',
        'sort'          => 2,
        'category'      => 'renewal',
        'light_color'   => '#fcebbd'
    ],
    [
        'id'            => 953,
        'name'          => 'FAILED', 
        'description'   => 'FAILED',
        'color'         => '#ff6c07',
        'text_color'    => 'white',
        'class'         => '',
        'sort'          => 3,
        'category'      => 'renewal',
        'light_color'   => '#fcd0b2'
    ],
    [
        'id'            => 954,
        'name'          => 'NOT SENT', 
        'description'   => 'NOT SENT',
        'color'         => '#137eff',
        'text_color'    => 'white',
        'class'         => 'warning',
        'sort'          => 4,
        'category'      => 'renewal',
        'light_color'   => '#c8e4fd'
    ],
    [
        'id'            => 955,
        'name'          => 'SENT', 
        'description'   => 'SENT',
        'color'         => '#e1e7ed',
        'text_color'    => 'black',
        'class'         => 'success',
        'sort'          => 5,
        'category'      => 'renewal',
        'light_color'   => '#ffffff'
    ],
    [
        'id'            => 956,
        'name'          => 'SUSPECT RENEWED ALREADY', 
        'description'   => 'SUSPECT RENEWED ALREADY',
        'color'         => '#7845bf',
        'text_color'    => 'white',
        'class'         => 'success',
        'sort'          => 6,
        'category'      => 'renewal',
        'light_color'   => '#cfbce9'
    ],
    [
        'id'            => 957,
        'name'          => 'TELEBRIDGE', 
        'description'   => 'TELEBRIDGE',
        'color'         => '#16a0a3',
        'text_color'    => 'white',
        'class'         => 'success',
        'sort'          => 7,
        'category'      => 'renewal',
        'light_color'   => '#cbe6e6'
    ],
    [
        'id'            => 958,
        'name'          => 'CLEARED', 
        'description'   => 'CLEARED',
        'color'         => '#1dca21',
        'text_color'    => 'white',
        'class'         => 'success',
        'sort'          => 8,
        'category'      => 'renewal',
        'light_color'   => '#cfefd0'
    ],
];

return [
    /****************
     * FOR STORES eg. like task or shipment status w/c are inside stores menu pages
     ***************/
    'stores' => [
        $prescribe
        , $stage
        , $task
        , $shipment
        , $priority
        , $inmar
        , $returnType
        , $return
        , $invoice
        , $procurementOrders
        , $newsAndEventsType
        , $clinicalKpiCallStatus
        , $clinicalDiagnosisStatus
        , $clinicalProviderStatus
        , $leaveStatus
        , $returnToStockV2
        , $clinicalRenewals
    ],
    /**********************
     * FOR SYSTEM SETTINGS eg. like user or employee status w/c are outside stores menu pages
     *********************/
    'settings' => [

    ]
];
