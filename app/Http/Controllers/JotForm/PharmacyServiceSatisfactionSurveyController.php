<?php

namespace App\Http\Controllers\JotForm;

use App\Http\Controllers\Controller;
use App\Repositories\API\JotFormRepository;
use Illuminate\Http\Request;

class PharmacyServiceSatisfactionSurveyController extends Controller
{
    private $jotFormRepository;

    public function __construct(JotFormRepository $jotFormRepository
    ) {
        $this->jotFormRepository = $jotFormRepository;
    }

    public function index($id)
    {
        $formID = '242176934304456';
        $this->jotFormRepository->setConfiguration('0af9148eeac7ee7b3e7c727e9ecd4f9c', 'https://hipaa-api.jotform.com');
        $submissions = $this->jotFormRepository->getFormSubmissions($formID);

        dd($submissions);
    }

}
