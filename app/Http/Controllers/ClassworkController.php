<?php

namespace App\Http\Controllers;

use App\Models\Classwork;
use App\Http\Requests\StoreClassworkRequest;
use App\Http\Requests\UpdateClassworkRequest;
use App\Models\ClassworkSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClassworkController extends Controller
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
    public function store(Request $request)
    {
        $fields = $request->validate([
            "title" => "required",
            "subject_id" => "required",
            'description' => "nullable",
            "status" => "required",
            "deadline" => 'nullable|date_format:Y-m-d H:i:s',
            "score" => "integer|required",
        ], [
            "title.required" => "Please provide a title for the classwork.",
            "subject_id.required" => "The subject is required. Please select a subject.",
            "status.required" => "Please specify the status of the classwork.",
            "deadline.date_format" => "The deadline must be in the format YYYY-MM-DD HH:MM:SS.",
            "score.required" => "A score is required and must be a whole number.",
            "score.integer" => "The score must be a whole number.",
        ]);

        Classwork::create($fields);

        return response()->json(['message' => 'Successfully Created']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Classwork $classwork)
    {
        return response()->json($classwork);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classwork $classwork)
    {


        $fields = $request->validate([
            "title" => "required",
            'description' => "nullable",
            "status" => "required",
            "deadline" => "date_format:Y-m-d H:i:s|nullable",
            "score" => "integer",
           ]);

        $classwork->update($fields);


        return response()->json(['message' => 'Successfully Updated']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classwork $classwork)
    {
        //
        $classwork->delete();

        return response()->json(['message' => 'Successfully Deleted']);

    }
public function getClassworkBySubject($id) {

    // Classwork::updateStatusBasedOnDeadline();

    $classworks = Classwork::where('subject_id', $id)
        ->withCount('submissions') // Count the submissions
        ->orderByDesc('created_at')
        ->get();



    return response()->json($classworks);
}
public function getClassworksForStudent($subjectId)
{

    // Classwork::updateStatusBasedOnDeadline();

    $studentId = Auth::user()->id;

    // Get classworks with submissions and student classworks for the student
    $classworks = Classwork::with(['submissions' => function($query) use ($studentId) {
        $query->where('student_id', $studentId)
              ->with(['studentClassworks' => function($query) {
                  $query->select('id', 'submission_id', 'file');  // Fetch student classwork details including files
              }]);
    }])->where('subject_id', $subjectId)
        ->orderBy('created_at', 'desc')
        ->get();

    // Check deadlines and append file URLs to each studentClasswork
    $classworks->each(function ($classwork) {


        // Append file URLs
        $classwork->submissions->each(function ($submission) {
            $submission->studentClassworks->each(function ($studentClasswork) {
                // Use Laravel Storage to get the file URL
                $studentClasswork->file_url = $studentClasswork->file ? Storage::url($studentClasswork->file) : null;
            });
        });
    });

    return response()->json($classworks);
}

public function getSubmissionsByClasswork($classworkId)
{
    // Fetch submissions for the specified classwork_id, including student and student_classworks
    $submissions = ClassworkSubmission::with(['studentClassworks', 'student'])
        ->where('classwork_id', $classworkId)
        ->get();

    if ($submissions->isEmpty()) {
        return response()->json(['error' => 'No submissions found for this classwork.'], 404);
    }

    // Attach file URLs to each student_classwork
    foreach ($submissions as $submission) {
        foreach ($submission->studentClassworks as $studentClasswork) {
            $studentClasswork->file_url = $studentClasswork->file ? asset('storage/studentfiles/' . $studentClasswork->file) : null;
        }
    }

    return response()->json($submissions);
}


}