<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class CompanyController extends Controller
{
    /**
     * Display the company index page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.company.index');
    }

    /**
     * Fetch all companies and return as JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCompany(Request $request)
    {
        $companies = Company::orderBy('id', 'desc')->get();
        return response()->json(['data' => $companies]);
    }

    /**
     * Update the status of a company.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyStatus(Request $request)
    {
        try {
            $company = Company::findOrFail($request->userid);
            $company->status = $request->status;
            $company->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a company by its ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            Company::where('id', $id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Company deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:companies',
            'phone' => 'required|string|max:20|unique:companies',
            'address' => 'nullable|string',
            'city' => 'required|string|max:100',
            'type' => 'required|string|in:type1,type2', // Adjust types as necessary
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        // Save the company data
        $company = Company::create($request->only([
            'name', 'email', 'phone', 'address', 'city', 'type'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Company saved successfully!',
        ]);
    }

    // Fetch user data
public function getCompany($id)
{
    $user = Company::find($id);
    return response()->json($user);
}

// Update user data
public function updateCompany(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'email' => 'required|email',
        'phone' => 'required|string',
        'address' => 'required|string',
        'city' => 'required|string',
        'type' => 'required|string',
        'id' => 'required|integer|exists:companies,id', // Adjust as needed
    ]);

    $user = Company::find($request->id);
    if ($user) {
        $user->update($request->all());
        return response()->json(['success' => true , 'message' => 'Company Update Successfully']);
    }

    return response()->json(['success' => false, 'message' => 'User not found']);
}


}
