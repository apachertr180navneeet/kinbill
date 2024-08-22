<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Company, Tax, Item, PaymentBook};
use Illuminate\Support\Facades\{Auth, DB, Mail, Hash, Validator, Session};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Exception;

class PaymentBookController extends Controller
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
        return view('company.payment_book.index');
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
        $ReceiptBookVoucher = PaymentBook::join('users', 'payment_books.vendor_id', '=', 'users.id')
            ->where('payment_books.company_id', $compId)
            ->select('payment_books.*', 'users.full_name as vendor_name')
            ->orderByDesc('payment_books.id')
            ->get();


        // Return the purchase books data as JSON response
        return response()->json(['data' => $ReceiptBookVoucher]);
    }


    /**
     * Show the form for adding a new sales book.
     *
     * @return \Illuminate\View\View
     */
    public function add()
    {
        // Get the authenticated user and their company ID
        $user = Auth::user();
        $compId = $user->company_id;

        // Fetch all active vendors for the user's company
        $vendors = User::where('role', 'vendor')
            ->where('company_id', $compId)
            ->where('status', 'active')
            ->get();

        // Pass the vendors and items data to the view for adding a new sales book
        return view('company.payment_book.add', compact('vendors'));
    }


    public function store(Request $request)
    {
        // Define validation rules
        $validatedData = $request->validate([
            'date' => 'required|date',
            'payment' => 'required|string|max:255',
            'vendor' => 'required|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'round_off' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'remark' => 'required|string',
            'payment_method' => 'required|string|in:cash,cheque,online bank,other',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get the authenticated user and their company ID
            $user = Auth::user();
            $compId = $user->company_id;

            // Save the payment book details in the payment_books table
            $salesBook = PaymentBook::create([
                'date' => $request->date,
                'company_id' => $compId,
                'payment_vouchers_number' => $request->payment,
                'vendor_id' => $request->vendor,
                'amount' => $request->amount,
                'discount' => $request->discount,
                'round_off' => $request->round_off,
                'grand_total' => $request->grand_total,
                'remark' => $request->remark,
                'payment_type' => $request->payment_method,
            ]);

            // Commit the transaction
            DB::commit();

            // Redirect with a success message
            return redirect()->route('company.payment.book.index')->with('success', 'Payment book entry saved successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollback();

            // Redirect with an error message
            return redirect()->back()->with('error', 'An error occurred while saving the payment book entry.');
        }
    }



    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Find the sales book
                $ReceiptBook = PaymentBook::find($id);

                if (!$ReceiptBook) {
                    throw new \Exception('sales Book not found.');
                }

                // Delete the sales book itself
                $ReceiptBook->delete();
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

    }


    public function edit($id)
    {
        // Get the authenticated user and their company ID
        $user = Auth::user();
        $compId = $user->company_id;

        $paymentBook = PaymentBook::find($id);

        // Fetch all active vendors for the user's company
        $vendors = User::where('role', 'vendor')
            ->where('company_id', $compId)
            ->where('status', 'active')
            ->get();

        return view('company.payment_book.edit', compact('paymentBook', 'vendors'));
    }


    public function update(Request $request , $id)
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get the authenticated user and their company ID
            $user = Auth::user();
            $compId = $user->company_id;
            // Find the specific ReceiptBookVoucher record by its ID
            $receiptBook = PaymentBook::findOrFail($id);

            // Update the record with the validated data
            $receiptBook->payment_vouchers_number = $request->payment;
            $receiptBook->date = $request->date;
            $receiptBook->vendor_id = $request->vendor;
            $receiptBook->remark = $request->remark;
            $receiptBook->amount = $request->amount;
            $receiptBook->discount = $request->discount;
            $receiptBook->round_off = $request->round_off;
            $receiptBook->grand_total = $request->grand_total;
            $receiptBook->payment_type = $request->payment_method;

            // Save the updated record
            $receiptBook->save();

            // Commit the transaction
            DB::commit();

            // Redirect with a success message
            return redirect()->route('company.payment.book.index')->with('success', 'Payment book entry saved successfully.');
        } catch (\Exception $e) {
            dd($e);
            // Rollback the transaction on error
            DB::rollback();

            // Redirect with an error message
            return redirect()->back()->with('error', 'An error occurred while saving the sales book entry.');
        }
    }


    public function print($id)
    {
        // Get the authenticated user and their company ID
        $user = Auth::user();
        $compId = $user->company_id;

        $paymentBook = PaymentBook::join('users', 'payment_books.vendor_id', '=', 'users.id')
        ->select('payment_books.*', 'users.full_name as vendor_name')
        ->find($id);

        // Fetch all active vendors for the user's company
        $customers = User::where('role', 'customer')
            ->where('company_id', $compId)
            ->where('status', 'active')
            ->get();

        return view('company.payment_book.print', compact('paymentBook', 'customers'));
    }
}
