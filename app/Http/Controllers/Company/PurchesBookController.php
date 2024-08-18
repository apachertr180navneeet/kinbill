<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Company, Tax, Item, PurchesBook, PurchesBookItem, StockReport};
use Illuminate\Support\Facades\{Auth, DB, Mail, Hash, Validator, Session};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Exception;

class PurchesBookController extends Controller
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
        // Get the authenticated user and their company ID
        $user = Auth::user();
        $compId = $user->company_id;

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

        // Pass the vendors and items data to the view for adding a new purchase book
        return view('company.purches_book.add', compact('vendors', 'items'));
    }

    public function store(Request $request)
    {
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
                'total_tax' => $request->total_tax,
                'other_expense' => $request->other_expense,
                'discount' => $request->discount,
                'round_off' => $request->round_off,
                'grand_total' => $request->grand_total,
            ]);

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
            }

            // Commit the transaction
            DB::commit();

            // Redirect with a success message
            return redirect()->route('company.purches.book.index')->with('success', 'Purchase book entry saved successfully.');
        } catch (\Exception $e) {
            dd($e);
            // Rollback the transaction on error
            DB::rollback();

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

        return view('company.purches_book.edit', compact('purchaseBook', 'vendors', 'items'));
    }

    public function update(Request $request, $id)
    {
        // Step 1: Check if items are provided
        if (empty($request->items) || empty($request->quantities) || empty($request->rates) || empty($request->taxes) || empty($request->totalAmounts)) {
            return redirect()->back()->with(['error' => 'No items provided. Please add items to the purchase book.']);
        }

        $purchaseBook = PurchesBook::with('purchesbookitem')->find($id);

        // Step 2: Subtract old quantities from StockReport
        foreach ($purchaseBook->purchesbookitem as $item) {
            $stockReport = StockReport::where('item_id', $item->item_id)->first();
            if ($stockReport) {
                $stockReport->quantity -= $item->quantity;
                $stockReport->save();
            }
        }

        // Step 3: Update Purchase Book details
        $purchaseBook->date = $request->date;
        $purchaseBook->invoice_number = $request->invoice;
        $purchaseBook->vendor_id = $request->vendor;
        $purchaseBook->transport = $request->transport;
        $purchaseBook->total_tax = $request->total_tax;
        $purchaseBook->other_expense = $request->other_expense;
        $purchaseBook->discount = $request->discount;
        $purchaseBook->round_off = $request->round_off;
        $purchaseBook->grand_total = $request->grand_total;

        // Delete existing items to reattach with updated quantities
        $purchaseBook->purchesbookitem()->delete();

        // Step 4: Add new quantities to StockReport and attach items to PurchaseBook
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
                ]);
            } else {
                // Handle the case where the item does not exist
                return redirect()->back()->with(['error' => "Item with ID $itemId does not exist."]);
            }
        }

        $purchaseBook->save();

        return redirect()->route('company.purches.book.index')->with('success', 'Purchase book updated successfully.');
    }

}
