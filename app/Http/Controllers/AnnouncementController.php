<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $announcement = Announcement::all();

        return response()->json($announcement);

        // return "hello";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $fields = $request->validate([
            "title" => "required",
            "desc" => "required",
            "owner" => "required",
            "to" => "required"
        ], [
            "title.required" => "Please provide a title.",
            "desc.required" => "Please provide a description.",
            "owner.required" => "The owner field is required.",
            "to.required" => "You must specify a recipients."
        ]);

        Announcement::create($fields);

        return response()->json(["message" => "Succesfully created Announcement"]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $fields = $request->validate([
            "title" => "nullable",
            "desc" => "nullable",
            "to" => "nullable"
        ]);

        // Find the announcement by id
        $announcement = Announcement::findOrFail($id);

        // Filter out fields that are null from the validated data
        $filteredFields = array_filter($fields, function ($value) {
            return !is_null($value);
        });

        // Check if there are no fields to update
        if (empty($filteredFields)) {
            return response()->json(["message" => "No fields to update"], 200);
        }

        // Update the announcement with the filtered data
        $announcement->update($filteredFields);

        return response()->json(["message" => "Successfully updated"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the announcement by id
        $announcement = Announcement::findOrFail($id);

        // Delete the announcement
        $announcement->delete();

        return response()->json(["message" => "Successfully deleted Announcement"]);
    }

    public function getAdminAnnouncement() {
        $announcements = Announcement::where('owner', '=', 'admin')
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        return response()->json($announcements);
    }

    public function getStudentAnnouncement() {
        $announcements = Announcement::whereIn('to', ['students and teacher', 'users'])
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        return response()->json($announcements);
    }

    public function getEmployeeAnnouncement() {
        $announcements = Announcement::where('to', '=', 'employees')
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        return response()->json($announcements);
    }

    public function getPrincipalAnnouncement() {
        $announcements = Announcement::orderBy('created_at', 'desc')
                                    ->get();

        return response()->json($announcements);
    }

    public function getTeacherAnnouncement() {
        $announcements = Announcement::orderBy('created_at', 'desc')
                                    ->get();

        return response()->json($announcements);
    }

    public function getTotalAdminAnnouncement() {
        $count = Announcement::where('owner', 'admin')->count();

        return response()->json($count);
    }

    public function getTotalPrincipalAnnouncement() {
        $count = Announcement::where('owner', 'principal')->count();

        return response()->json($count);
    }
}
