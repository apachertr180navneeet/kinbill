<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\{
        User,
        Company,
        Tax,
        Item,
        PurchesBook,
        PurchesBookItem
    };
use Mail, DB, Hash, Validator, Session, File, Exception, Redirect, Auth;

class PurchesBookController extends Controller
{
     /**
     * Display the User index page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Pass the company and comId to the view
        return view('company.purches_book.index');
    }

    /**
     * Fetch all companies and return as JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getall(Request $request)
    {
        $user = Auth::user();
        $compId = $user->company_id;

        $purchesbook = PurchesBook::join('users', 'purches_books.vendor_id', '=', 'users.id')
            ->where('purches_books.company_id', $compId)  // Specify the table for company_id
            ->select('purches_books.*', 'users.full_name as vendor_name')
            ->orderBy('purches_books.id', 'desc')  // Specify the table for id
            ->get();

        return response()->json(['data' => $purchesbook]);
    }



    public function add()
    {
        $user = Auth::user();
        $compId = $user->company_id;

        $vendors = User::where('role', 'vendor')->where('company_id', $compId)->where('status', 'active')->get();

        $items = Item::join('variations', 'items.variation_id', '=', 'variations.id')
        ->join('taxes', 'items.variation_id', '=', 'taxes.id')
        ->where('items.company_id',$compId)
        ->select('items.*', 'variations.name as variation_name', 'taxes.rate as tax_rate')
        ->get();
        // Pass the company and comId to the view
        return view('company.purches_book.add', compact('vendors','items'));
    }

}
