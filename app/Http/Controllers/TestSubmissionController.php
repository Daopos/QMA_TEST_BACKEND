<?php

namespace App\Http\Controllers;

use App\Models\TestSubmission;
use App\Http\Requests\StoreTestSubmissionRequest;
use App\Http\Requests\UpdateTestSubmissionRequest;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestSubmissionController extends Controller
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
    public function store(StoreTestSubmissionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TestSubmission $testSubmission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestSubmissionRequest $request, TestSubmission $testSubmission)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TestSubmission $testSubmission)
    {
        //
    }

    public function submitTest(Request $request)
    {
        $studentId = Auth::user()->id;

        // Validate the incoming request data
        $validatedData = $request->validate([
            'test_id' => 'required|exists:tests,id', // Ensure test_id is part of the request body
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'nullable|string',
        ]);

        $testId = $validatedData['test_id']; // Extract test_id from validated data

        // Check if the test status is 'closed'
        $test = Test::find($testId);
        if ($test->status === 'closed') {
            return response()->json(['message' => 'The test is closed and cannot be submitted.'], 403);
        }

        // Create the TestSubmission record
        $submission = TestSubmission::create([
            'test_id' => $testId,
            'student_id' => $studentId
        ]);

        // Fetch correct answers for the test
        $questions = Question::whereIn('id', array_column($validatedData['answers'], 'question_id'))
                             ->get()
                             ->keyBy('id');

        // Iterate over answers and create StudentAnswer records
        foreach ($validatedData['answers'] as $answerData) {
            $question = $questions->get($answerData['question_id']);
            $isCorrect = null;
            $score = 0;

            if ($question) {
                if ($question->correct_answer == $answerData['answer']) {
                    $isCorrect = true;
                    $score = 1; // Increment score for correct answer
                } else {
                    $isCorrect = false;
                }
            }

            // Create the StudentAnswer record and store the score
            StudentAnswer::create([
                'submission_id' => $submission->id,
                'question_id' => $answerData['question_id'],
                'answer' => $answerData['answer'],
                'is_correct' => $isCorrect,
                'score' => $score, // Store score in StudentAnswer
            ]);
        }

        // Optionally set is_pass based on criteria
        $isPass = false; // Set this based on other logic if needed

        // Update the submission record with the is_pass boolean
        $submission->is_done = $isPass;
        $submission->save();

        return response()->json(['message' => 'Test submitted successfully.']);
    }



    public function getSubmissions($testId)
    {
        // Fetch submissions for the specified test, including student data
        $submissions = TestSubmission::where('test_id', $testId)
            ->with('student') // Assuming a relation to the student exists
            ->get();

        // Map through each submission and calculate the total score
        $submissionsWithScores = $submissions->map(function ($submission) {
            // Calculate the total score from StudentAnswer table
            $totalScore = StudentAnswer::where('submission_id', $submission->id)
                                       ->sum('score');

          return [
           'id' => $submission->id,
            'student' => $submission->student, // Include student information
            'test_id' => $submission->test_id,
            'is_pass' => $submission->is_pass,
            'total_score' => $totalScore, // Calculated total score
            'created_at' => $submission->created_at->toDateTimeString(), // Include created_at timestamplude created_at timestamp
        ];
        });

        return response()->json($submissionsWithScores);
    }


public function getStudentSubmissionAnswer($testId, $studentId)
{
    $submission = TestSubmission::where('test_id', $testId)
        ->where('student_id', $studentId)
        ->with(['student', 'answers.question']) // Load answers and related questions
        ->firstOrFail();

    return response()->json($submission);

}

}