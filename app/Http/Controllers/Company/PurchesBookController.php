<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Company, Tax, Item, PurchesBook, PurchesBookItem, StockReport};
use Illuminate\Support\Facades\{Auth, DB, Mail, Hash, Validator, Session};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Exception;

class PurchesBookController extends Controller
{
    public function getLastDigit($str) {
        // Use regular expression to find all digits in the string
        preg_match_all('/\d/', $str, $matches);

        // If there are digits found, return the last one
        if (!empty($matches[0])) {
            return end($matches[0]);
        }

        // Return null or a message if no digits are found
        return null;
    }
    /**
     * Display the purchase book index page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Simply returning the view for purchase book index page
        return view('company.purches_book.index');
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
            ->orderByDesc('purches_books.id')
            ->get();


        // Return the purchase books data as JSON response
        return response()->json(['data' => $purchesBooks]);
    }

    /**
     * Show the form for adding a new purchase book.
     *
     * @return \Illuminate\View\View
     */
    public function add()
    {
        // Retrieve the authenticated user and their company ID
        $authenticatedUser = Auth::user();
        $companyId = $authenticatedUser->company_id;


        // Fetch the company details for the authenticated user's company
        $companyDetails = Company::find($companyId);
        $companyShortCode = $companyDetails->short_code;
        $companyState = $companyDetails->state;

        // Fetch all active vendors associated with the user's company
        $activeVendors = User::where([
                ['role', 'vendor'],
                ['company_id', $companyId],
                ['status', 'active']
            ])->get();

        // Fetch items with their respective variations and tax details for the company
        $companyItems = Item::with(['variation:id,name', 'tax:id,rate']) // Eager loading relationships for efficiency
            ->where('company_id', $companyId)
            ->get(['id', 'name', 'tax_id' , 'company_id' , 'variation_id']); // Ensure proper field selection and eager loading

        // Get the maximum invoice number for the company's purchases
        $latestInvoiceNumber = PurchesBook::where('company_id', $companyId)->max('invoice_number');
        $lastDigit = $this->getLastDigit($latestInvoiceNumber);
        // Generate the next invoice number by incrementing the latest invoice or default to 1
        $lastDigit = (int) $lastDigit; // Convert to integer
        $nextInvoiceNumber = $lastDigit ? $lastDigit + 1 : 1;


        // Format the invoice number to have 5 digits, with leading zeros if necessary
        $formattedInvoiceNumber = sprintf('%05d', $nextInvoiceNumber);
        $finalInvoiceNumber = $companyShortCode . '-PB' . '-' . $formattedInvoiceNumber;

        // Get the current date
        $currentDate = Carbon::now()->toDateString(); // Y-m-d format

        // Return the view with the active vendors, items, and the generated invoice number
        return view('company.purches_book.add', [
            'vendors' => $activeVendors,
            'items' => $companyItems,
            'invoiceNumber' => $finalInvoiceNumber,
            'currentDate' => $currentDate,
            'companyState' => $companyState
        ]);
    }
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'date' => 'required|date',
            'invoice' => 'required|string|max:255',
            'vendor' => 'required|exists:users,id',
            'transport' => 'required|string|max:255',
            'igst' => 'required|numeric|min:0',
            'cgst' => 'required|numeric|min:0',
            'sgst' => 'required|numeric|min:0',
            'other_expense' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'round_off' => 'required|numeric',
            'grand_total' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*' => 'exists:items,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|numeric|min:1',
            'rates' => 'required|array|min:1',
            'rates.*' => 'required|numeric|min:0',
            'taxes' => 'required|array|min:1',
            'taxes.*' => 'required|numeric|min:0',
            'totalAmounts' => 'required|array|min:1',
            'totalAmounts.*' => 'required|numeric|min:0',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get the authenticated user and their company ID
            $user = Auth::user();
            $compId = $user->company_id;

            // Save the purchase book details in the purches_books table
            $purchesBook = PurchesBook::create([
                'date' => $request->date,
                'company_id' => $compId,
                'invoice_number' => $request->invoice,
                'vendor_id' => $request->vendor,
                'transport' => $request->transport,
                'igst' => $request->igst,
                'cgst' => $request->cgst,
                'sgst' => $request->sgst,
                'other_expense' => $request->other_expense,
                'discount' => $request->discount,
                'round_off' => $request->round_off,
                'grand_total' => $request->grand_total,
                'amount_before_tax' => $request->amount_before_tax,
                'given_amount' => $request->given_amount,
                'remaining_blance' => $request->remaining_blance,
            ]);

            // Initialize an array to store the purchase book items
            $purchesBookItems = [];

            // Save each item in the purches_book_items table
            foreach ($request->items as $index => $itemId) {
                $item = PurchesBookItem::create([
                    'purches_book_id' => $purchesBook->id,
                    'item_id' => $itemId,
                    'quantity' => $request->quantities[$index],
                    'rate' => $request->rates[$index],
                    'tax' => $request->taxes[$index],
                    'amount' => $request->totalAmounts[$index],
                ]);

                // Add the created item to the array
                $purchesBookItems[] = $item;

                // Update or create a StockReport entry
                $quantity = $request->quantities[$index];
                $stockReport = StockReport::where('item_id', $itemId)->first();
                if ($stockReport) {
                    $stockReport->quantity += $quantity;
                    $stockReport->save();
                } else {
                    StockReport::create([
                        'item_id' => $itemId,
                        'quantity' => $quantity,
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            // Fetch the last inserted PurchesBook and its items in array format
            $lastPurchesBook = PurchesBook::with('purchesbookitem')->find($purchesBook->id);

            // Redirect with success message and last inserted data
            return redirect()->route('company.purches.book.index')
                ->with('success', 'Purchase book entry saved successfully.')
                ->with('lastPurchesBook', $lastPurchesBook);

        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollback();
            dd($e);
            // Redirect with an error message
            return redirect()->back()->with('error', 'An error occurred while saving the purchase book entry.');
        }
    }
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Find the purchase book
                $purchaseBook = PurchesBook::with('purchesbookitem')->find($id);

                if (!$purchaseBook) {
                    throw new \Exception('Purchase Book not found.');
                }

                // Loop through the items to update the stock
                foreach ($purchaseBook->purchesbookitem as $item) {
                    $stockReport = StockReport::where('item_id', $item->item_id)->first();
                    if ($stockReport) {
                        $stockReport->quantity -= $item->quantity;
                        $stockReport->save();
                    }
                }

                // Delete items related to the purchase book
                $purchaseBook->purchesbookitem()->delete();

                // Delete the purchase book itself
                $purchaseBook->delete();
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

        // Fetch the company details for the authenticated user's company
        $companyDetails = Company::find($compId);
        $companyShortCode = $companyDetails->short_code;
        $companyState = $companyDetails->state;

        $purchaseBook = PurchesBook::with('purchesbookitem.item.variation')->find($id);

        // Fetch all active vendors for the user's company
        $vendors = User::where('role', 'vendor')
            ->where('company_id', $compId)
            ->where('status', 'active')
            ->get();

        // Fetch all items with their variations and tax details for the user's company
        $items = Item::join('variations', 'items.variation_id', '=', 'variations.id')
            ->join('taxes', 'items.tax_id', '=', 'taxes.id')
            ->where('items.company_id', $compId)
            ->select('items.*', 'variations.name as variation_name', 'taxes.rate as tax_rate')
            ->get();

        return view('company.purches_book.edit', compact('purchaseBook', 'vendors', 'items','companyState'));
    }

    public function view($id)
    {
        // Get the authenticated user and their company ID
        $user = Auth::user();
        $compId = $user->company_id;

        // Fetch the company details for the authenticated user's company
        $companyDetails = Company::find($compId);
        $companyShortCode = $companyDetails->short_code;
        $companyState = $companyDetails->state;

        $purchaseBook = PurchesBook::with('purchesbookitem.item.variation')->find($id);

        // Fetch all active vendors for the user's company
        $vendors = User::where('role', 'vendor')
            ->where('company_id', $compId)
            ->where('status', 'active')
            ->get();

        // Fetch all items with their variations and tax details for the user's company
        $items = Item::join('variations', 'items.variation_id', '=', 'variations.id')
            ->join('taxes', 'items.tax_id', '=', 'taxes.id')
            ->where('items.company_id', $compId)
            ->select('items.*', 'variations.name as variation_name', 'taxes.rate as tax_rate')
            ->get();

        return view('company.purches_book.view', compact('purchaseBook', 'vendors', 'items','companyState'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'date' => 'required|date',
            'invoice' => 'required|string',
            'vendor' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'quantities' => 'required|array|min:1',
            'rates' => 'required|array|min:1',
            'taxes' => 'required|array|min:1',
            'totalAmounts' => 'required|array|min:1',
            'igst' => 'required|numeric|min:0',
            'cgst' => 'required|numeric|min:0',
            'sgst' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
        ], [
            'items.required' => 'No items provided. Please add items to the purchase book.',
            'quantities.required' => 'Quantities are required for all items.',
            'rates.required' => 'Rates are required for all items.',
            'taxes.required' => 'Taxes are required for all items.',
            'totalAmounts.required' => 'Total amounts are required for all items.',
        ]);

        $purchaseBook = PurchesBook::with('purchesbookitem')->find($id);

        // Step 1: Subtract old quantities from StockReport
        foreach ($purchaseBook->purchesbookitem as $item) {
            $stockReport = StockReport::where('item_id', $item->item_id)->first();
            if ($stockReport) {
                $stockReport->quantity -= $item->quantity;
                $stockReport->save();
            }
        }

        // Step 2: Update Purchase Book details
        $purchaseBook->date = $request->date;
        $purchaseBook->invoice_number = $request->invoice;
        $purchaseBook->vendor_id = $request->vendor;
        $purchaseBook->transport = $request->transport;
        $purchaseBook->igst = $request->igst;
        $purchaseBook->cgst = $request->cgst;
        $purchaseBook->sgst = $request->sgst;
        $purchaseBook->other_expense = $request->other_expense;
        $purchaseBook->discount = $request->discount;
        $purchaseBook->round_off = $request->round_off;
        $purchaseBook->grand_total = $request->grand_total;
        $purchaseBook->amount_before_tax = $request->amount_before_tax;
        $purchaseBook->given_amount = $request->given_amount;
        $purchaseBook->remaining_blance = $request->remaining_blance;

        // Delete existing items to reattach with updated quantities
        $purchaseBook->purchesbookitem()->delete();

        // Step 3: Add new quantities to StockReport and attach items to PurchaseBook
        foreach ($request->items as $index => $itemId) {
            $quantity = $request->quantities[$index];
            $amount = $request->rates[$index];
            $tax = $request->taxes[$index];
            $total = $request->totalAmounts[$index];

            // Check if the item exists before updating or creating stock report entry
            if (Item::find($itemId)) {
                // Update or create a StockReport entry
                $stockReport = StockReport::where('item_id', $itemId)->first();
                if ($stockReport) {
                    $stockReport->quantity += $quantity;
                    $stockReport->save();
                } else {
                    StockReport::create([
                        'item_id' => $itemId,
                        'quantity' => $quantity,
                    ]);
                }

                // Recreate the purchase book item record
                $purchaseBook->purchesbookitem()->create([
                    'item_id' => $itemId,
                    'quantity' => $quantity,
                    'rate' => $amount,
                    'tax' => $tax,
                    'amount' => $total,
                    'purches_book_id' => $id,
                ]);
            } else {
                // Handle the case where the item does not exist
                return redirect()->back()->withInput()->withErrors(["Item with ID $itemId does not exist."]);
            }
        }

        $purchaseBook->save();

        return redirect()->route('company.purches.book.index')->with('success', 'Purchase book updated successfully.');
    }

    public function preturn($id)
    {
        // Get the authenticated user and their company ID
        $user = Auth::user();
        $compId = $user->company_id;

        // Fetch the company details for the authenticated user's company
        $companyDetails = Company::find($compId);
        $companyShortCode = $companyDetails->short_code;
        $companyState = $companyDetails->state;

        $purchaseBook = PurchesBook::with('purchesbookitem.item.variation','purchesbookitem.item.tax')->find($id);


        // Fetch all active vendors for the user's company
        $vendors = User::where('role', 'vendor')
            ->where('company_id', $compId)
            ->where('status', 'active')
            ->get();

        // Fetch all items with their variations and tax details for the user's company
        $items = Item::join('variations', 'items.variation_id', '=', 'variations.id')
            ->join('taxes', 'items.tax_id', '=', 'taxes.id')
            ->where('items.company_id', $compId)
            ->select('items.*', 'variations.name as variation_name', 'taxes.rate as tax_rate')
            ->get();

        return view('company.purches_book.preturn', compact('purchaseBook', 'vendors', 'items','companyState'));
    }

    public function preturn_update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Loop through each item in the request and update the stock and purchase book
            foreach ($request->items as $index => $itemId) {
                $quantity = $request->quantities[$index];
                $amount = $request->rates[$index];
                $tax = $request->taxes[$index];
                $total = $request->totalAmounts[$index];

                // Retrieve the item and validate its existence
                $item = Item::find($itemId);
                if (!$item) {
                    return redirect()->back()->withInput()->withErrors(["Item with ID $itemId does not exist."]);
                }

                // Find the existing PurchesBookItem entry
                $existingPurchesBookItem = PurchesBookItem::where('item_id', $itemId)
                    ->where('purches_book_id', $id)
                    ->first();

                // Update or create the PurchesBookItem entry if quantity has changed or record doesn't exist
                if (!$existingPurchesBookItem || $existingPurchesBookItem->preturn != $quantity) {
                    PurchesBookItem::updateOrCreate(
                        ['item_id' => $itemId, 'purches_book_id' => $id],
                        [
                            'preturn' => $quantity,
                            'rate' => $amount,
                            'tax' => $tax,
                            'amount' => $total
                        ]
                    );
                }

                $stkqty = $existingPurchesBookItem->quantity - $quantity;
                // dd($stkqty);
                // Update stock report
                $stockReport = StockReport::where('item_id', $itemId)->first();
                if ($stockReport) {
                    $stockReport->decrement('quantity', $quantity);
                }
            }

            // Update the PurchesBook with the calculated grand total
            $purchesBook = PurchesBook::find($id);
            if ($purchesBook) {
                $purchesBook->grand_total = $request->grand_total;
                $purchesBook->save();
            }

            DB::commit();

            return redirect()->route('company.purches.book.index')->with('success', 'Return Added successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'An error occurred while updating the return.']);
        }
    }

}
