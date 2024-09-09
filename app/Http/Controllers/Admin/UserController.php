<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
        User,
        Company,
        city,
        State,
        Pincode
    };
use Mail, DB, Hash, Validator, Session, File,Exception,Redirect;

class UserController extends Controller
{
    /**
     * Display the User index page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Retrieve the company ID from the request
        $comId = $request->id;

        // Check if the session has a specific key
        if (Session::has('comId')) {
            // Destroy the existing session value
            Session::forget('comId');
        }

        // Create a new session value
        Session::put('comId', $comId);

        // Validate if the company exists
        $company = Company::find($comId);

        if (!$company) {
            // Redirect back with an error message if the company is not found
            return Redirect::back()->withErrors(['error' => 'Company not found.']);
        }

        $states = State::all();
        // Pass the company and comId to the view
        // Pass the company and comId to the view
        return view('admin.users.index', compact('comId', 'company','states'));
    }

    /**
     * Fetch all companies and return as JSON.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getall(Request $request)
    {
        $compId = Session::get('comId');

        $companies = User::where('role','user')->where('company_id',$compId)->orderBy('id', 'desc')->get();
        return response()->json(['data' => $companies]);
    }

    /**
     * Update the status of a User.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        try {
            $User = User::findOrFail($request->userId);
            $User->status = $request->status;
            $User->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
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
            User::where('id', $id)->delete();

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

    public function store(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'address' => 'nullable|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string',
            'password' => 'required|string',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $compId = Session::get('comId');
        // Save the User data
        $dataUser = [
            'full_name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'state' => $request->state,
            'city' => $request->city,
            'role' => 'user',
            'password' => Hash::make($request->password),
            'company_id' => $compId
        ];
        $User = User::create($dataUser);

        return response()->json([
            'success' => true,
            'message' => 'User saved successfully!',
        ]);
    }

    // Fetch user data
    public function get($id)
    {
        $user = User::find($id);
        return response()->json($user);
    }

    // Update user data
    public function update(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'id' => 'required|integer|exists:users,id', // Adjust as needed
        ]);

        $user = User::find($request->id);
        if ($user) {
            $user->update($request->all());
            return response()->json(['success' => true , 'message' => 'User Update Successfully']);
        }

        return response()->json(['success' => false, 'message' => 'User not found']);
    }

}
