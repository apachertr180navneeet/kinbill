<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Company, Tax, Item, ReceiptBookVoucher};
use Illuminate\Support\Facades\{Auth, DB, Mail, Hash, Validator, Session};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Exception;

class ReceiptBookVoucherController extends Controller
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
        return view('company.receipt_book_voucher.index');
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
        $ReceiptBookVoucher = ReceiptBookVoucher::join('users', 'receipt_book_vouchers.customer_id', '=', 'users.id')
            ->where('receipt_book_vouchers.company_id', $compId)
            ->select('receipt_book_vouchers.*', 'users.full_name as customer_name')
            ->orderByDesc('receipt_book_vouchers.id')
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
        $customers = User::where('role', 'customer')
            ->where('company_id', $compId)
            ->where('status', 'active')
            ->get();

        // Pass the vendors and items data to the view for adding a new sales book
        return view('company.receipt_book_voucher.add', compact('customers'));
    }


    public function store(Request $request)
    {
        // Define validation rules
        $rules = [
            'date' => 'required|date',
            'receipt' => 'required|string|max:255',
            'customer' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'discount' => 'required|numeric',
            'round_off' => 'required|numeric',
            'grand_total' => 'required|numeric|min:0',
            'remark' => 'required|string|max:500',
            'payment_method' => 'required|string|in:cash,credit,debit',
        ];

        // Validate the request data
        $validatedData = $request->validate($rules);

        try {
            // Get the authenticated user and their company ID
            $user = Auth::user();
            $compId = $user->company_id;

            // Save the sales book details in the sales_books table
            $salesBook = ReceiptBookVoucher::create([
                'date' => $validatedData['date'],
                'company_id' => $compId,
                'receipt_vouchers_number' => $validatedData['receipt'],
                'customer_id' => $validatedData['customer'],
                'amount' => $validatedData['amount'],
                'discount' => $validatedData['discount'] ?? 0,
                'round_off' => $validatedData['round_off'] ?? 0,
                'grand_total' => $validatedData['grand_total'],
                'remark' => $validatedData['remark'] ?? '',
                'payment_type' => $validatedData['payment_method'],
            ]);

            // Commit the transaction
            DB::commit();

            // Redirect with a success message
            return redirect()->route('company.receipt.book.voucher.index')->with('success', 'Receipt book entry saved successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollback();

            // Redirect with an error message
            return redirect()->back()->with('error', 'An error occurred while saving the sales book entry.');
        }
    }



    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Find the sales book
                $ReceiptBook = ReceiptBookVoucher::find($id);

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

        $receiptBook = ReceiptBookVoucher::find($id);

        // Fetch all active vendors for the user's company
        $customers = User::where('role', 'customer')
            ->where('company_id', $compId)
            ->where('status', 'active')
            ->get();

        return view('company.receipt_book_voucher.edit', compact('receiptBook', 'customers'));
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
            $receiptBook = ReceiptBookVoucher::findOrFail($id);

            // Update the record with the validated data
            $receiptBook->receipt_vouchers_number = $request->receipt;
            $receiptBook->date = $request->date;
            $receiptBook->customer_id = $request->customer;
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
            return redirect()->route('company.receipt.book.voucher.index')->with('success', 'receipt book entry saved successfully.');
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

        $receiptBook = ReceiptBookVoucher::join('users', 'receipt_book_vouchers.customer_id', '=', 'users.id')
        ->select('receipt_book_vouchers.*', 'users.full_name as customer_name')
        ->find($id);

        // Fetch all active vendors for the user's company
        $customers = User::where('role', 'customer')
            ->where('company_id', $compId)
            ->where('status', 'active')
            ->get();

        return view('company.receipt_book_voucher.print', compact('receiptBook', 'customers'));
    }
}
