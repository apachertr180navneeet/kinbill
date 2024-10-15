<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, SalesBook, SalesBookItem};
use Illuminate\Support\Facades\{Auth, DB, Mail, Hash, Validator, Session};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Exception;

class SalesReportController extends Controller
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
        return view('company.sales_report.index');
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
        $purchesBooks = SalesBook::join('users', 'sales_books.customer_id', '=', 'users.id')
            ->where('sales_books.company_id', $compId)
            ->select('sales_books.*', 'users.full_name as customer_name')
            ->orderByDesc('sales_books.id');

            // If a date filter is provided, apply the date filter to the query
            if ($request->start_date && $request->end_date) {
                $purchesBooks->whereBetween('sales_books.date', [$request->start_date, $request->end_date]);
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

        $salesReport = SalesBook::with('salesbookitem.item.variation','salesbookitem.item.tax')
        ->join('users', 'sales_books.customer_id', '=', 'users.id')
        ->select('sales_books.*', 'users.full_name as customer_name', 'users.city as customer_city', 'users.state as customer_state', 'users.gst_no as customer_gst_no', 'users.phone as customer_phone')
        ->find($id);

        //dd($salesReport);

        return view('company.sales_report.print', compact('salesReport'));
    }
}
