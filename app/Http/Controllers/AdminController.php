<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
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
    public function store(StoreAdminRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admin $admin)
    {
        // Validate the incoming request
        $fields = $request->validate([
            'new_username' => 'required',
            'email' => 'required|email',
            'current_password' => 'required',
            'new_password' => 'required'
        ]);

        // Get the authenticated user

        // Check if the current password is correct
        if (!Hash::check($fields['current_password'], $admin->password)) {
            return response()->json(['error' => 'Invalid Credentials'], 403);
        }

        // Update user credentials
        $new_credentials = [
            'username' => $fields['new_username'],
            'email' => $fields['email'],
            'password' => $fields['new_password'], // Hash the new password
        ];

        // Update the authenticated user's information
        $admin->update($new_credentials);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin)
    {
        //
    }


}