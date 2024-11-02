<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Http\Requests\StoreTestRequest;
use App\Http\Requests\UpdateTestRequest;
use App\Models\Choice;
use App\Models\Essay;
use App\Models\Identification;
use App\Models\Question;
use App\Models\StudentAnswer;
use App\Models\Task;
use App\Models\TestSubmission;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
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
    public function store(StoreTestRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Test $test)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTestRequest $request, Test $test)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Test $test)
    {
        //
    }


//     public function createTestAndQuestions(Request $request)
// {
//     // Validate test and questions data
//     $validated = $request->validate([
//         'subject_id' => 'required|exists:subjects,id',
//         'title' => 'required|string',
//         'description' => 'nullable|string',
//         'questions' => 'required|array',
//         'questions.*.type' => 'required|in:multiple-choice,identification,essay',
//         'questions.*.title' => 'required|string',
//         'questions.*.correct_answer' => 'nullable|string',
//         'questions.*.choices' => 'nullable|array', // For multiple-choice questions
//         'questions.*.pts' => 'required|integer',
//     ]);

//     // Use a transaction to ensure atomicity
//     DB::beginTransaction();

//     try {
//         // Create the test
//         $test = Test::create([
//             'subject_id' => $validated['subject_id'],
//             'title' => $validated['title'],
//             'description' => $validated['description'],
//         ]);

//         // Iterate over questions and create them based on type
//         foreach ($validated['questions'] as $questionData) {
//             $type = $questionData['type'];

//             if ($type === 'multiple-choice') {
//                 // Create the multiple-choice question
//                 $question = Question::create([
//                     'test_id' => $test->id,
//                     'title' => $questionData['title'],
//                     'correct_answer' => $questionData['correct_answer'],
//                     'pts' => $questionData['pts'],
//                 ]);

//                 // Check if choices are provided
//                 if (isset($questionData['choices'])) {
//                     foreach ($questionData['choices'] as $choice) {
//                         Choice::create([
//                             'question_id' => $question->id,
//                             'question_text' => $choice,  // Assuming there is a 'choice_text' field in your 'choices' table
//                         ]);
//                     }
//                 }
//             } elseif ($type === 'identification') {
//                 // Create the identification question
//                 Identification::create([
//                     'test_id' => $test->id,
//                     'title' => $questionData['title'],
//                     'correct_answer' => $questionData['correct_answer'],
//                     'pts' => $questionData['pts'],
//                 ]);
//             } elseif ($type === 'essay') {
//                 // Create the essay question
//                 Essay::create([
//                     'test_id' => $test->id,
//                     'title' => $questionData['title'],
//                     'pts' => $questionData['pts'],
//                 ]);
//             }
//         }

//         // Commit the transaction if all operations succeed
//         DB::commit();

//         return response()->json(['message' => 'Test and questions created successfully.']);

//     } catch (\Exception $e) {
//         // Rollback the transaction if any operation fails
//         DB::rollBack();

//         return response()->json(['error' => 'Failed to create test and questions: ' . $e->getMessage()], 500);
//     }
// }


public function createTestAndQuestions(Request $request)
{
    // Validate test and questions data
    $validated = $request->validate([
        'subject_id' => 'required|exists:subjects,id',
        'title' => 'required|string',
        'description' => 'nullable|string',
        'status' => 'required|string',
        'deadline' => 'nullable|date_format:Y-m-d H:i:s',
        'questions' => 'required|array',
        'questions.*.type' => 'required|in:multiple-choice,identification,essay',
        'questions.*.title' => 'required|string',
        'questions.*.correct_answer' => 'nullable|string', // correct_answer is nullable
        'questions.*.choices' => 'nullable|array', // For multiple-choice questions
        'questions.*.pts' => 'required|integer',
    ]);

    // Use a transaction to ensure atomicity
    DB::beginTransaction();

    try {
        // Create the test
        $test = Test::create([
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'deadline' => $validated['deadline'],

        ]);

        // Iterate over questions and create them based on type
        foreach ($validated['questions'] as $index => $questionData) {
            // Create the question for all types
            $question = Question::create([
                'test_id' => $test->id,
                'title' => $questionData['title'],
                'correct_answer' => $questionData['correct_answer'] ?? null, // Use null if not provided
                'pts' => $questionData['pts'],
                'index_position' => $index,
                'type' => $questionData['type'],
            ]);

            // Check if choices are provided for multiple-choice questions
            if ($questionData['type'] === 'multiple-choice' && isset($questionData['choices'])) {
                foreach ($questionData['choices'] as $choice) {
                    Choice::create([
                        'question_id' => $question->id,
                        'choice_text' => $choice,
                    ]);
                }
            }
        }

        // Commit the transaction if all operations succeed
        DB::commit();

        return response()->json(['message' => 'Test and questions created successfully.']);

    } catch (\Exception $e) {
        // Rollback the transaction if any operation fails
        DB::rollBack();

        return response()->json(['error' => 'Failed to create test and questions: ' . $e->getMessage()], 500);
    }
}


public function getTestWithQuestions($id)
{
    try {
        // Fetch the test details with all questions and their associated choices (if any)
        $test = Test::with([
            'questions' => function ($query) {
                $query->with('choices'); // Fetch choices only for multiple-choice questions
            }
        ])->findOrFail($id);

        // Return the test details along with questions
        return response()->json($test, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch test: ' . $e->getMessage()], 500);
    }
}

public function updateTestAndQuestions(Request $request, $testId)
{
    // Validate the data
    $validated = $request->validate([
        'title' => 'required|string',
        'description' => 'nullable|string',
        'questions' => 'required|array',
        'questions.*.id' => 'nullable|integer|exists:questions,id',
        'questions.*.type' => 'required|in:multiple-choice,identification,essay',
        'questions.*.title' => 'required|string',
        'questions.*.pts' => 'nullable|integer',
        'questions.*.choices' => 'nullable|array', // For multiple-choice questions
    ]);

    // Use a transaction to ensure atomicity
    try {
        // Your existing test update logic...

        // Start a database transaction
        DB::beginTransaction();

        // Update the test data
        $test = Test::findOrFail($testId);
        $test->update([
            'title' => $request->title,
            'description' => $request->description,
            // other test fields
        ]);

        // Initialize arrays to keep track of existing IDs
        $existingQuestionIds = [];
        $existingChoices = [];

        foreach ($request->questions as $index => $questionData) {
            $type = $questionData['type'];
            $questionId = $questionData['id'] ?? null; // Question ID

            // Handle multiple choice or identification questions
            if ($type === 'multiple-choice' || $type === 'identification') {
                $correctAnswer = $questionData['correct_answer'] ?? '';

                // Update or create the question
                $question = Question::updateOrCreate(
                    ['id' => $questionId], // Match by ID
                    [
                        'test_id' => $test->id,
                        'title' => $questionData['title'],
                        'correct_answer' => $correctAnswer,
                        'pts' => $questionData['pts'],
                        'index_position' => $index,
                        'type' => $type
                    ]
                );

                $existingQuestionIds[] = $question->id; // Track the updated or created question ID

                // Handle choices for multiple choice questions
                if ($type === 'multiple-choice' && isset($questionData['choices'])) {
                    foreach ($questionData['choices'] as $choice) {
                        if (isset($choice['id'])) {
                            $existingChoices[] = $choice['id']; // Track existing choice IDs
                        }

                        Choice::updateOrCreate(
                            ['id' => $choice['id'] ?? null, 'question_id' => $question->id],
                            ['choice_text' => $choice['choice_text'], 'correct_answer' => $choice['correct_answer'] ?? '']
                        );
                    }
                }
            }
            // Handle essay questions separately
            elseif ($type === 'essay') {
                $question = Question::updateOrCreate(
                    ['id' => $questionId], // Match by ID
                    [
                        'test_id' => $test->id,
                        'title' => $questionData['title'],
                        'pts' => $questionData['pts'],
                        'index_position' => $index,
                        'type' => 'essay',
                    ]
                );

                $existingQuestionIds[] = $question->id; // Track the essay question ID
            }
        }

        // Remove questions that are no longer present
        Question::where('test_id', $test->id)
            ->whereNotIn('id', $existingQuestionIds)
            ->delete();

        // Correct deletion logic for choices
        Choice::whereIn('question_id', $existingQuestionIds)
            ->whereNotIn('id', array_filter($existingChoices)) // Make sure to filter out null values
            ->delete();

        DB::commit();

        return response()->json(['message' => 'Test and questions updated successfully.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Failed to update test and questions: ' . $e->getMessage()], 500);
    }
}


// public function getTestsBySubject($id)
// {
//     // Get the authenticated student's ID using Sanctum
//     $studentId = Auth::user()->id;

//     // Get the tests associated with the subject
//     $tests = Test::where('subject_id', $id)
//                  ->orderBy('created_at', 'desc')
//                  ->get();

//     // Map the tests and include submission status and score
//     $testsWithSubmissions = $tests->map(function ($test) use ($studentId) {
//         // Check if the student has submitted the test
//         $submission = TestSubmission::where('test_id', $test->id)
//                                     ->where('student_id', $studentId)
//                                     ->first();

//         // Calculate total score from StudentAnswer table if the submission exists
//         $totalScore = null;
//         if ($submission) {
//             $totalScore = StudentAnswer::where('submission_id', $submission->id)
//                                        ->sum('score');
//         }

//         return [
//             'id' => $test->id,
//             'title' => $test->title,
//             'created_at' => $test->created_at,
//             'description' => $test->description,
//             'submitted' => $submission ? true : false,
//             'score' => $totalScore, // Get the score from StudentAnswer table
//         ];
//     });

//     return response()->json($testsWithSubmissions);
// }


public function getTestsBySubject($id)
{
    // Get the authenticated student's ID using Sanctum
    $studentId = Auth::user()->id;

    // Get the tests associated with the subject
    $tests = Test::where('subject_id', $id)
                 ->orderBy('created_at', 'desc')
                 ->get();

    // Map the tests and include submission status, score, total questions, and submission count
    $testsWithSubmissions = $tests->map(function ($test) use ($studentId) {
        // Check if the student has submitted the test
        $submission = TestSubmission::where('test_id', $test->id)
                                    ->where('student_id', $studentId)
                                    ->first();

        // Calculate total score from StudentAnswer table if the submission exists
        $totalScore = null;
        if ($submission) {
            $totalScore = StudentAnswer::where('submission_id', $submission->id)
                                       ->sum('score');
        }

        // Get the total number of questions in the test
        $totalQuestions = $test->questions()->count();  // Assuming there's a 'questions' relation

        // Get the total number of test submissions for this test
        $submissionCount = TestSubmission::where('test_id', $test->id)->count();

        return [
            'id' => $test->id,
            'title' => $test->title,
            'created_at' => $test->created_at,
            'description' => $test->description,
            'status' => $test->status,
            'deadline' => $test->deadline,
            'submitted' => $submission ? true : false,
            'score' => $totalScore, // Get the score from StudentAnswer table
            'total_questions' => $totalQuestions, // Total number of questions
            'submission_count' => $submissionCount, // Total number of submissions
        ];
    });

    return response()->json($testsWithSubmissions);
}


public function updateTest(Request $request, $id) {

    $test = Test::find($id);

    $fields = $request->validate([
        "title" => "required",
        "description" => "required"
    ]);

    $test->update($fields);


    return response()->json(['message' => "successfully updated"]);

}

public function getSortedQuestionsByTestId($id)
{
    try {
        // Fetch all questions related to the test, sorted by index_position
        $questions = Question::with('choices')  // Assuming a 'choices' relation is defined
            ->where('test_id', $id)
            ->orderBy('index_position')
            ->get();

        return response()->json($questions, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch sorted questions: ' . $e->getMessage()], 500);
    }
}


public function deleteTest($id) {

    $test = Test::find($id)->delete();

    return response()->json(['mesage' => 'successfully deleted']);
}

}