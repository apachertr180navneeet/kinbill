<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Company, Tax, Item, SalesBook, SalesBookItem, StockReport};
use Illuminate\Support\Facades\{Auth, DB, Mail, Hash, Validator, Session};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Exception;

class SalesBookController extends Controller
{
    /**
     * Display the sales book index page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Simply returning the view for sales book index page
        return view('company.sales_book.index');
    }

    /**
     * Fetch all sales books for the authenticated user's company and return them as JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getall(Request $request)
    {
        // Get the authenticated user and their company ID
        $user = Auth::user();
        $compId = $user->company_id;

        // Fetch all sales books for the user's company, including vendor details
        $salesBooks = SalesBook::join('users', 'sales_books.customer_id', '=', 'users.id')
            ->where('sales_books.company_id', $compId)
            ->select('sales_books.*', 'users.full_name as customer_name')
            ->orderByDesc('sales_books.id')
            ->get();


        // Return the sales books data as JSON response
        return response()->json(['data' => $salesBooks]);
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

        // Fetch all items with their variations and tax details for the user's company
        $items = Item::join('variations', 'items.variation_id', '=', 'variations.id')
            ->join('taxes', 'items.tax_id', '=', 'taxes.id')
            ->where('items.company_id', $compId)
            ->select('items.*', 'variations.name as variation_name', 'taxes.rate as tax_rate')
            ->get();

        // Pass the vendors and items data to the view for adding a new sales book
        return view('company.sales_book.add', compact('customers', 'items'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'date' => 'required|date',
            'dispatch' => 'required|string|max:255',
            'customer' => 'required|exists:users,id',
            'weight' => 'required|numeric',
            'total_tax' => 'required|numeric',
            'other_expense' => 'required|numeric',
            'discount' => 'required|numeric',
            'round_off' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'items.*' => 'required|exists:items,id',
            'quantities.*' => 'required|numeric|min:0',
            'rates.*' => 'required|numeric|min:0',
            'taxes.*' => 'required|numeric|min:0',
            'totalAmounts.*' => 'required|numeric|min:0',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get the authenticated user and their company ID
            $user = Auth::user();
            $compId = $user->company_id;

            // Save the sales book details in the sales_books table
            $salesBook = SalesBook::create([
                'date' => $request->date,
                'company_id' => $compId,
                'dispatch_number' => $request->dispatch,
                'customer_id' => $request->customer,
                'item_weight' => $request->weight,
                'total_tax' => $request->total_tax,
                'other_expense' => $request->other_expense,
                'discount' => $request->discount,
                'round_off' => $request->round_off,
                'grand_total' => $request->grand_total,
            ]);

            // Save each item in the sales_book_items table
            foreach ($request->items as $index => $itemId) {
                SalesBookItem::create([
                    'sales_book_id' => $salesBook->id,
                    'item_id' => $itemId,
                    'quantity' => $request->quantities[$index],
                    'rate' => $request->rates[$index],
                    'tax' => $request->taxes[$index],
                    'amount' => $request->totalAmounts[$index],
                ]);

                $quantity = $request->quantities[$index];
                // Update or create a StockReport entry
                $stockReport = StockReport::where('item_id', $itemId)->first();
                if ($stockReport) {
                    $stockReport->quantity -= $quantity;
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
            return redirect()->route('company.sales.book.index')->with('success', 'Sales book entry saved successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollback();
            dd($e);
            // Redirect with an error message
            return redirect()->back()->with('error', 'An error occurred while saving the sales book entry.');
        }
    }


    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // Find the sales book
                $salesBook = SalesBook::with('salesbookitem')->find($id);

                if (!$salesBook) {
                    throw new \Exception('sales Book not found.');
                }

                // Loop through the items to update the stock
                foreach ($salesBook->salesbookitem as $item) {
                    $stockReport = StockReport::where('item_id', $item->item_id)->first();
                    if ($stockReport) {
                        $stockReport->quantity -= $item->quantity;
                        $stockReport->save();
                    }
                }

                // Delete items related to the sales book
                $salesBook->salesbookitem()->delete();

                // Delete the sales book itself
                $salesBook->delete();
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

        $salesBook = SalesBook::with('salesbookitem.item.variation')->find($id);

        // Fetch all active vendors for the user's company
        $customers = User::where('role', 'customer')
            ->where('company_id', $compId)
            ->where('status', 'active')
            ->get();

        // Fetch all items with their variations and tax details for the user's company
        $items = Item::join('variations', 'items.variation_id', '=', 'variations.id')
            ->join('taxes', 'items.tax_id', '=', 'taxes.id')
            ->where('items.company_id', $compId)
            ->select('items.*', 'variations.name as variation_name', 'taxes.rate as tax_rate')
            ->get();

        return view('company.sales_book.edit', compact('salesBook', 'customers', 'items'));
    }

    public function update(Request $request, $id)
    {
        // Step 1: Check if items are provided
        if (empty($request->items) || empty($request->quantities) || empty($request->rates) || empty($request->taxes) || empty($request->totalAmounts)) {
            return redirect()->back()->with(['error' => 'No items provided. Please add items to the sales book.']);
        }

        $salesBook = SalesBook::with('salesbookitem')->find($id);

        // Step 2: Subtract old quantities from StockReport
        foreach ($salesBook->salesbookitem as $item) {
            $stockReport = StockReport::where('item_id', $item->item_id)->first();
            if ($stockReport) {
                $stockReport->quantity -= $item->quantity;
                $stockReport->save();
            }
        }

        // Step 3: Update sales Book details
        $salesBook->date = $request->date;
        $salesBook->dispatch_number = $request->dispatch;
        $salesBook->customer_id = $request->customer;
        $salesBook->item_weight = $request->weight;
        $salesBook->total_tax = $request->total_tax;
        $salesBook->other_expense = $request->other_expense;
        $salesBook->discount = $request->discount;
        $salesBook->round_off = $request->round_off;
        $salesBook->grand_total = $request->grand_total;

        // Delete existing items to reattach with updated quantities
        $salesBook->salesbookitem()->delete();

        // Step 4: Add new quantities to StockReport and attach items to salesBook
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
                    $stockReport->quantity -= $quantity;
                    $stockReport->save();
                } else {
                    StockReport::create([
                        'item_id' => $itemId,
                        'quantity' => $quantity,
                    ]);
                }

                // Recreate the sales book item record
                $salesBook->salesbookitem()->create([
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

        $salesBook->save();

        return redirect()->route('company.sales.book.index')->with('success', 'sales book updated successfully.');
    }
}