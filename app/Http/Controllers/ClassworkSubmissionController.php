<?php

namespace App\Http\Controllers;

use App\Models\ClassworkSubmission;
use App\Http\Requests\StoreClassworkSubmissionRequest;
use App\Http\Requests\UpdateClassworkSubmissionRequest;
use App\Models\Classwork;
use App\Models\StudentClasswork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassworkSubmissionController extends Controller
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
    public function store(StoreClassworkSubmissionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(ClassworkSubmission $classworkSubmission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClassworkSubmissionRequest $request, ClassworkSubmission $classworkSubmission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClassworkSubmission $classworkSubmission)
    {
        //
    }

    public function submitClasswork(Request $request, $classworkId)
{
    // Validate the request
    $validatedData = $request->validate([
        'files.*' => 'nullable|file|mimes:jpg,png,pdf,docx,txt,zip|max:2048', // Adjust as needed
        'score' => 'nullable|integer',
    ]);

    // Get the authenticated student ID
    $studentId = Auth::user()->id;

    try {
        // Create a new classwork submission
        $classworkSubmission = ClassworkSubmission::create([
            'classwork_id' => $classworkId,
            'student_id' => $studentId,
            'score' => $validatedData['score'] ?? 0,
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) { // Check if the file is valid
                    // Store the file in the storage (e.g., public disk)
                    $filePath = $file->store('classwork_submissions', 'public');

                    // Create an entry in the student_classworks table
                    StudentClasswork::create([
                        'submission_id' => $classworkSubmission->id,
                        'student_id' => $studentId,
                        'file' => $filePath,
                    ]);
                } else {
                    return response()->json(['error' => 'Invalid file upload.'], 400);
                }
            }
        }

        return response()->json([
            'message' => 'Classwork submitted successfully!',
            'classwork_submission_id' => $classworkSubmission->id,
        ], 201);

    } catch (\Exception $e) {
        // Log the error with details for debugging
        \Log::error('Error in submitClasswork: ' . $e->getMessage(), [
            'classwork_id' => $classworkId,
            'student_id' => $studentId,
            'files' => $request->file('files'), // Log uploaded files if any
        ]);

        // Return a more detailed error message
        return response()->json(['error' => 'An error occurred while submitting classwork: ' . $e->getMessage()], 500);
    }
}



public function getStudentClassworkSubmissions($classworkId)
{
    // Get the authenticated student ID
    $studentId = Auth::user()->id;

    try {
        // Retrieve the student's submissions for the specified classwork
        $studentClassworkSubmissions = StudentClasswork::whereHas('classworkSubmission', function ($query) use ($classworkId, $studentId) {
            $query->where('classwork_id', $classworkId)
                  ->where('student_id', $studentId);
        })
        ->with('classworkSubmission.classwork') // Load related classwork details if necessary
        ->get();

        // Return the student classwork submissions in a JSON response
        return response()->json([
            'student_classwork_submissions' => $studentClassworkSubmissions,
        ], 200);
    } catch (\Exception $e) {
        \Log::error('Error retrieving student classwork submissions: ' . $e->getMessage(), [
            'classwork_id' => $classworkId,
            'student_id' => $studentId,
        ]);

        return response()->json(['error' => 'An error occurred while retrieving submissions.'], 500);
    }
}


public function createOrUpdateScore(Request $request, $id) {
    // Validate incoming request data
    $request->validate([
        'score' => 'required|integer|min:0', // Ensure score is required and is a non-negative integer
    ]);

    // Find the submission by its ID
    $submission = ClassworkSubmission::find($id);

    // Check if submission exists
    if (!$submission) {
        return response()->json(['error' => 'Submission not found.'], 404);
    }

    // Find the associated classwork to get the maximum score
    $classwork = $submission->classwork;

    // Check if the submitted score exceeds the maximum score allowed for the classwork
    if ($request->score > $classwork->score) {
        return response()->json(['error' => 'The submitted score exceeds the maximum score for this classwork.'], 400);
    }

    // Update the score
    $submission->score = $request->score;
    $submission->save(); // Save the updated submission

    return response()->json([
        'message' => 'Score updated successfully.',
        'submission' => $submission
    ]);
}
}