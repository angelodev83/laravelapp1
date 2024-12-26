<?php

namespace App\Http\Controllers;

use App\Models\ClinicalRenewal;
use Illuminate\Http\Request;

class RenewalController extends Controller
{
    public function index()
    {
        // Fetch the data from the clinical_renewals table
        $renewals = ClinicalRenewal::paginate(10); // Or use ->get() for all records

        // Return the data to the view
        return view('renewals.index', compact('renewals'));
    }
}

