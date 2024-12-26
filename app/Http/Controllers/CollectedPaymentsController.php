<?php

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CollectedPayment; // Assuming this is the model for your "collected_payments" table

class CollectedPaymentsController extends Controller
{
    /**
     * Display a listing of the collected payments.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch collected payments with pagination (15 records per page)
        $collectedPayments = CollectedPayment::select(
            'id',
            'account_number',
            'account_name',
            'pharmacy_store_id',
            'user_id',
            'paid_amount',
            'rx_number',
            'reconciling_account_name',
            'pos_sales_date',
            'posting_of_payment_date',
            'created_at',
            'updated_at'
        )->paginate(15); // You can adjust the number of records per page

        // Pass the data to the view
        return view('collected-payments.index', compact('collectedPayments'));
    }
}

