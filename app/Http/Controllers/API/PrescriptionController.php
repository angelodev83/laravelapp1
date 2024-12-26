<?php

namespace App\Http\Controllers\API;

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $validatedData = $request->validate([
            'details' => 'required|string',
            // Add other required fields for a new prescription
        ]);

        $prescription = new Prescription($validatedData);
        $patient->prescriptions()->save($prescription);

        return response()->json(['prescription' => $prescription], 201);
    }
}

