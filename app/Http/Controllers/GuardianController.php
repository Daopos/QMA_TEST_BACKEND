<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Http\Requests\StoreGuardianRequest;
use App\Http\Requests\UpdateGuardianRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GuardianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuardianRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Guardian $guardian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGuardianRequest $request, Guardian $guardian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guardian $guardian)
    {
        //
    }

    public function guardianResetPassword(Request $request, $id) {

        $student = Guardian::where('student_id', $id)->first();

        $fields = $request->validate([
            "password" => "required"
        ]);

        $student->update($fields);

        return response()->json(["message" => "succcess"]);

    }

    public function resetPassword(Request $request)
{
    // Get the currently authenticated user
    $user = Auth::user();

    // Find the guardian record based on the user ID
    $guardian = Guardian::where('id', $user->id)->firstOrFail();

    // Validate incoming request data
    $fields = $request->validate([
        'current_password' => 'required',
        'new_password' => 'required',
    ]);

    // Check if the current password matches the one stored in the database
    if (!Hash::check($fields['current_password'], $guardian->password)) {
        return response()->json(['error' => 'Invalid Credentials'], 403);
    }

    // Hash the new password and update the guardian record
    $guardian->update([
        'password' => Hash::make($fields['new_password']),
    ]);

    return response()->json(['message' => 'Password updated successfully']);
}
}