<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\{
    User,
    Variation,
    Item,
    city,
    State,
    Pincode
};
use Mail, DB, Hash, Validator, Session, File, Exception, Redirect, Auth;

class CustomerController extends Controller
{
    /**
     * Display the User index page.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $states = State::all();
        // Pass the company and comId to the view
        return view('company.customer.index', compact('states'));
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

        $items = User::where('role', 'customer')->where('company_id', $compId)->get();

        return response()->json(['data' => $items]);
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
        $user = Auth::user();

        $compId = $user->company_id;
        // Validation rules
        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                // Rule::unique('users')->where(function ($query) use ($request) {
                //     return $query->where('role', $request->role);
                // }),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                // Rule::unique('users')->where(function ($query) use ($request) {
                //     return $query->where('role', $request->role);
                // }),
            ],
            'address' => 'nullable|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string',
            'gst' => 'required|string',
        ];

        // Custom messages
        $messages = [
            'email.unique' => "The email has already been taken in the {$request->role}s.",
            'phone.unique' => "The phone number has already been taken in the {$request->role}s.",
        ];


        // Validate the request data
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ]);
        }


        // Save the User data
        $dataUser = [
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'state' => $request->state,
            'city' => $request->city,
            'zipcode' => $request->zipcode,
            'role' => $request->role,
            'company_id' => $compId,
            'gst_no' =>  $request->gst,
            'password' => Hash::make('12345678'),
        ];
        User::create($dataUser);

        return response()->json([
            'success' => true,
            'message' => 'Item saved successfully!',
        ]);
    }

    // Fetch user data
    public function get($id)
    {
        $user = User::find($id);

        // Retrieve state data based on state name from user
        $stateData = State::where('state_name', $user->state)->first();

        // Retrieve city data based on city name from user
        $cityData = City::where('city_name', $user->city)->first();


        // Retrieve cities based on state id
        $cities = City::where('state_id', $stateData->state_id ?? null)->get(); // Safeguard in case state data is not found

        // Retrieve pincodes based on city id
        $pincodes = Pincode::where('city_id', $cityData->id ?? null)->get(); // Safeguard in case city data is not found

        return response()->json([
            'user' => $user,
            'state' => $stateData,
            'city' => $cityData,
            'cities' => $cities,
            'pincodes' => $pincodes
        ]);
    }

    // Update user data
    public function update(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $request->id,
            'address' => 'nullable|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string',
            'gst_no' => 'required|string',
            'id' => 'required|integer|exists:users,id', // Adjust as needed
        ]);

        $user = User::find($request->id);
        if ($user) {
            $user->update($request->all());
            return response()->json(['success' => true, 'message' => 'User Update Successfully']);
        }

        return response()->json(['success' => false, 'message' => 'User not found']);
    }
}
