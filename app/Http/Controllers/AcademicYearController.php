<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Models\Audit;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Subject;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
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
    public function store(StoreAcademicYearRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $academicYear)
    {
        //
    }
    public function getAcademicYears()
{
    $academicYears = AcademicYear::where('status', '!=', 'Inactive') // Exclude inactive status
        ->orderBy('id', 'desc') // Order by ID in descending order
        ->get(); // Fetch all matching academic years

    return response()->json($academicYears);
}

    public function createAcademicYear(Request $request) {

        // Validate the request data, ensuring academic_year is unique
        $fields = $request->validate([
            'academic_year' => 'required|string|unique:academic_years,academic_year',
        ]);

        // Check if there is already an "Inactive" academic year
        $existingInactiveYear = AcademicYear::where('status', 'Inactive')->first();

        if ($existingInactiveYear) {
            return response()->json(['message' => 'An inactive academic year already exists.'], 400);
        }

        // Assign default values for 'active' and 'status' fields
        $fields['active'] = false;
        $fields['status'] = 'Inactive';

        AcademicYear::create($fields);


        Audit::create([
            'user' =>  'Admin',  // Use . for concatenation and add spaces as needed
            'action' => 'Create an Academic Year',
            'user_level' => "Admin",
        ]);

        return response()->json(['message' => 'Academic year created successfully.'], 201);
    }

    public function deleteAcademicYear($id) {
        // Find the academic year by ID
        $academicYear = AcademicYear::find($id);

        // Check if the academic year exists and its status is 'Inactive'
        if (!$academicYear) {
            return response()->json(['message' => 'Academic year not found.'], 404);
        }

        if ($academicYear->status !== 'Inactive') {
            return response()->json(['message' => 'Only inactive academic years can be deleted.'], 400);
        }

        // Delete the academic year
        $academicYear->delete();

        Audit::create([
            'user' =>  'Admin',  // Use . for concatenation and add spaces as needed
            'action' => 'Delete an Academic Year',
            'user_level' => "Admin",
        ]);

        return response()->json(['message' => 'Academic year deleted successfully.'], 200);
    }
    public function getAcademicYear() {

        $academic_year = AcademicYear::all();

        return response()->json($academic_year);

    }

    // public function makeAcademicYearActive(Request $request) {
    //     // Validate the request to ensure an academic year ID is provided
    //     $request->validate([
    //         'id' => 'required|exists:academic_years,id', // Adjust the table name as necessary
    //     ]);

    //     // Get the currently active academic year
    //     $currentActive = AcademicYear::where('active', true)->first();

    //     // If there's an active academic year, mark it as Deactivated
    //     if ($currentActive) {
    //         $currentActive->update([
    //             'active' => false,
    //             'status' => 'Deactivated',
    //         ]);
    //     }

    //     // Now activate the specified academic year
    //     $academicYearToActivate = AcademicYear::find($request->id);
    //     $academicYearToActivate->update([
    //         'active' => true,
    //         'status' => 'Active',
    //     ]);

    //     // Deactivate all previous academic years
    //     AcademicYear::where('id', '<', $academicYearToActivate->id)
    //                  ->where('active', true)
    //                  ->update(['active' => false, 'status' => 'Deactivated']);

    //     // Update subsequent years (if they exist) to Inactive
    //     AcademicYear::where('id', '>', $academicYearToActivate->id)
    //                  ->update(['active' => false, 'status' => 'Inactive']);

    //     // Return a response indicating success
    //     return response()->json([
    //         'message' => 'Academic year activated successfully',
    //         'academic_year' => $academicYearToActivate,
    //     ]);
    // }


//     public function makeAcademicYearActive(Request $request)
// {
//     // Validate the request to ensure an academic year ID is provided
//     $request->validate([
//         'id' => 'required|exists:academic_years,id',
//     ]);

//     // Fetch the academic year that you want to activate
//     $newAcademicYear = AcademicYear::find($request->id);

//     if (!$newAcademicYear) {
//         return response()->json(['message' => 'Academic year not found'], 404);
//     }

//     // Check if there's an already active academic year
//     $currentActiveYear = AcademicYear::where('active', true)->first();

//     if ($currentActiveYear) {
//         // Archive all enrollments of the current active year
//         Enrollment::where('academic_year_id', $currentActiveYear->id)
//             ->update(['archived' => true, 'enrollment_status' => 'unenrolled']);

//         // Deactivate the current active academic year
//         $currentActiveYear->update([
//             'active' => false,
//             'status' => 'Deactivated',
//         ]);
//     }

//     // Activate the new academic year
//     $newAcademicYear->update([
//         'active' => true,
//         'status' => 'Active',
//     ]);

//     // Deactivate all previous academic years
//     AcademicYear::where('id', '<', $newAcademicYear->id)
//                  ->update(['active' => false, 'status' => 'Deactivated']);

//     // Set all subsequent academic years to Inactive
//     AcademicYear::where('id', '>', $newAcademicYear->id)
//                  ->update(['active' => false, 'status' => 'Inactive']);

//     // Return a response indicating success
//     return response()->json([
//         'message' => 'Academic year activated successfully, and previous year archived',
//         'academic_year' => $newAcademicYear,
//     ], 200);
// }

// public function makeAcademicYearActive(Request $request)
// {
//     // Validate the request to ensure an academic year ID is provided
//     $request->validate([
//         'id' => 'required|exists:academic_years,id',
//     ]);

//     // Fetch the academic year that you want to activate
//     $newAcademicYear = AcademicYear::find($request->id);

//     if (!$newAcademicYear) {
//         return response()->json(['message' => 'Academic year not found'], 404);
//     }

//     // Check if there's an already active academic year
//     $currentActiveYear = AcademicYear::where('active', true)->first();

//     if ($currentActiveYear) {
//         // Archive all enrollments of the current active year
//         Enrollment::where('academic_year_id', $currentActiveYear->id)
//             ->update(['archived' => true, 'enrollment_status' => 'unenrolled']);

//         // Archive all classrooms of the current active year
//         Classroom::where('archived', false)->update(['archived' => true]);

//         // Archive all subjects of the current active year
//         Subject::where('archived', false)->update(['archived' => true]);

//         // Copy classrooms and set adviser_id to null
//         $classrooms = Classroom::where('archived', false)->get();

//         foreach ($classrooms as $classroom) {
//             Classroom::create([
//                 'title' => $classroom->title,
//                 'grade_level' => $classroom->grade_level,
//                 'adviser_id' => null,  // Set adviser_id to null
//                 'created_at' => now(),
//                 'updated_at' => now(),
//             ]);
//         }

//         // Deactivate the current active academic year
//         $currentActiveYear->update([
//             'active' => false,
//             'status' => 'Deactivated',
//         ]);
//     }

//     // Activate the new academic year
//     $newAcademicYear->update([
//         'active' => true,
//         'status' => 'Active',
//     ]);

//     // Deactivate all previous academic years
//     AcademicYear::where('id', '<', $newAcademicYear->id)
//                  ->update(['active' => false, 'status' => 'Deactivated']);

//     // Set all subsequent academic years to Inactive
//     AcademicYear::where('id', '>', $newAcademicYear->id)
//                  ->update(['active' => false, 'status' => 'Inactive']);

//     // Return a response indicating success
//     return response()->json([
//         'message' => 'Academic year activated successfully, and previous year archived',
//         'academic_year' => $newAcademicYear,
//     ], 200);
// }

public function makeAcademicYearActive(Request $request)
{
    // Validate the request to ensure an academic year ID is provided
    $request->validate([
        'id' => 'required|exists:academic_years,id',
    ]);

    // Fetch the academic year that you want to activate
    $newAcademicYear = AcademicYear::find($request->id);

    if (!$newAcademicYear) {
        return response()->json(['message' => 'Academic year not found'], 404);
    }

    // Check if there's an already active academic year
    $currentActiveYear = AcademicYear::where('active', true)->first();

    if ($currentActiveYear) {
        // Archive all enrollments of the current active year
        Enrollment::where('academic_year_id', $currentActiveYear->id)
            ->update(['archived' => true, 'enrollment_status' => 'unenrolled']);

        // Copy classrooms and set adviser_id to null (before archiving them)
        $classrooms = Classroom::where('archived', false)->get(); // Get non-archived classrooms

        foreach ($classrooms as $classroom) {
            Classroom::create([
                'title' => $classroom->title,
                'grade_level' => $classroom->grade_level,
                'adviser_id' => null,  // Set adviser_id to null for the new academic year
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Now archive the original classrooms
        Classroom::where('archived', false)
        ->where('created_at', '<', now()) // Ensure it archives only the old classrooms
        ->update(['archived' => true]);
        // Archive all subjects of the current active year
        Subject::where('archived', false)->update(['archived' => true]);

        // Deactivate the current active academic year
        $currentActiveYear->update([
            'active' => false,
            'status' => 'Deactivated',
        ]);
    }

    // Activate the new academic year
    $newAcademicYear->update([
        'active' => true,
        'status' => 'Active',
    ]);

    // Deactivate all previous academic years
    AcademicYear::where('id', '<', $newAcademicYear->id)
                 ->update(['active' => false, 'status' => 'Deactivated']);

    // Set all subsequent academic years to Inactive
    AcademicYear::where('id', '>', $newAcademicYear->id)
                 ->update(['active' => false, 'status' => 'Inactive']);


                 Audit::create([
                    'user' =>  'Admin',  // Use . for concatenation and add spaces as needed
                    'action' => 'Activata an Academic Year',
                    'user_level' => "Admin",
                ]);

    // Return a response indicating success
    return response()->json([
        'message' => 'Academic year activated successfully, and previous year archived',
        'academic_year' => $newAcademicYear,
    ], 200);
}


public function getArchivedAcademiicYear() {

    $academic_year = AcademicYear::where('status', 'Deactivated')->get();

    return response()->json($academic_year);
}

}