<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\{
    User,
    BankAndCash
};
use Mail, DB, Hash, Validator, Session, File, Exception, Redirect, Auth;

class BankAndCashMangementController extends Controller
{
    /**
     * Display the User index page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $compId = $user->company_id;

        return view('company.bankandcash.index');
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

        $bankandcash = BankAndCash::where('company_id',$compId)->get();

        return response()->json(['data' => $bankandcash]);
    }


    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'date' => 'required',
            'serial_no' => 'required|string|unique:bank_and_cashes',
            'amount' => 'required',
            'payment_take' => 'required',
            'payment_type' => 'required',
            'description' => 'required',
            'particular' => 'required'
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $user = Auth::user();

        $compId = $user->company_id;

        // Save the User data
        $dataUser = [
            'date' => $request->date,
            'serial_no' => $request->serial_no,
            'amount' => $request->amount,
            'payment_take' => $request->payment_take,
            'payment_type' => $request->payment_type,
            'description' => $request->description,
            'particular' => $request->particular,
            'company_id' => $compId
        ];
        BankAndCash::create($dataUser);

        return response()->json([
            'success' => true,
            'message' => 'Bank And Cash saved successfully!',
        ]);
    }

    // Fetch user data
    public function get($id)
    {
        $user = BankAndCash::find($id);
        return response()->json($user);
    }

    // Update user data
    public function update(Request $request)
    {
        $request->validate([
            'date' => 'required',
            'serial_no' => 'required|string',
            'amount' => 'required',
            'payment_take' => 'required',
            'payment_type' => 'required',
            'description' => 'required',
            'particular' => 'required',
            'id' => 'required|integer|exists:bank_and_cashes,id', // Adjust as needed
        ]);

        $user = BankAndCash::find($request->id);
        if ($user) {
            $user->update($request->all());
            return response()->json(['success' => true , 'message' => 'User Update Successfully']);
        }

        return response()->json(['success' => false, 'message' => 'User not found']);
    }

    /**
     * Delete a User by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            BankAndCash::where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
