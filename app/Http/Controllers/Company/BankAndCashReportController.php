<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, BankAndCash, Bank};
use Illuminate\Support\Facades\{Auth, DB, Mail, Hash, Validator, Session};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Exception;

class BankAndCashReportController extends Controller
{
    /**
     * Display the purchase book index page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Simply returning the view for purchase book index page
        return view('company.bank_and_cash_report.index');
    }

    /**
     * Fetch all purchase books for the authenticated user's company and return them as JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getall(Request $request)
    {
        // Get the authenticated user and their company ID
        $user = Auth::user();
        $compId = $user->company_id;

        // Fetch all purchase books for the user's company, including vendor details
        $bankandcash = BankAndCash::where('bank_and_cashes.company_id', $compId)
        ->leftJoin('banks as deposite_bank', 'deposite_bank.id', '=', 'bank_and_cashes.deposite_in') // Join for deposite_in
        ->leftJoin('banks as withdraw_bank', 'withdraw_bank.id', '=', 'bank_and_cashes.withdraw_in') // Join for withdraw_in
        ->select(
            'bank_and_cashes.*',
            'deposite_bank.bank_name as deposite_bank_name',  // Select the name of the bank for deposite_in
            'withdraw_bank.bank_name as withdraw_bank_name'   // Select the name of the bank for withdraw_in
        )
        ->get();


        // Return the purchase books data as JSON response
        return response()->json(['data' => $bankandcash]);
    }
}
