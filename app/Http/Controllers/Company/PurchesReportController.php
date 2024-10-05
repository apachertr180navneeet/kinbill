<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, PurchesBook, PurchesBookItem};
use Illuminate\Support\Facades\{Auth, DB, Mail, Hash, Validator, Session};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Exception;

class PurchesReportController extends Controller
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
        return view('company.purches_report.index');
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
        $purchesBooks = PurchesBook::join('users', 'purches_books.vendor_id', '=', 'users.id')
            ->where('purches_books.company_id', $compId)
            ->select('purches_books.*', 'users.full_name as vendor_name')
            ->orderByDesc('purches_books.id');
            
            // If a date filter is provided, apply the date filter to the query
            if ($request->start_date && $request->end_date) {
                $purchesBooks->whereBetween('purches_books.date', [$request->start_date, $request->end_date]);
            } 
            // Fetch the filtered data
            $purchesBooks = $purchesBooks->get();


        // Return the purchase books data as JSON response
        return response()->json(['data' => $purchesBooks]);
    }


    public function print($id)
    {
        // Get the authenticated user and their company ID
        $user = Auth::user();
        $compId = $user->company_id;

        $purchaseReport = PurchesBook::with('purchesbookitem.item.variation')
        ->join('users', 'purches_books.vendor_id', '=', 'users.id')
        ->select('purches_books.*', 'users.full_name as vendor_name', 'users.city as vendor_city', 'users.state as vendor_state', 'users.gst_no as vendor_gst_no', 'users.phone as vendor_phone')
        ->find($id);

        return view('company.purches_report.print', compact('purchaseReport'));
    }
}
