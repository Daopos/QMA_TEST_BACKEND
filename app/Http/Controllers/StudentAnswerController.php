<?php

namespace App\Http\Controllers;

use App\Models\StudentAnswer;
use App\Http\Requests\StoreStudentAnswerRequest;
use App\Http\Requests\UpdateStudentAnswerRequest;
use Illuminate\Http\Request;

class StudentAnswerController extends Controller
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
    public function store(StoreStudentAnswerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentAnswerRequest $request, StudentAnswer $studentAnswer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentAnswer $studentAnswer)
    {
        //
    }

    public function updateScores(Request $request, $testId, $studentId)
    {
        $updatedAnswers = $request->input('updatedAnswers');

        // Validate the incoming data
        $request->validate([
            'updatedAnswers.*.answerId' => 'required|exists:student_answers,id',
            'updatedAnswers.*.score' => 'required|integer|min:0',
        ]);

        foreach ($updatedAnswers as $answerData) {
            // Find the student's answer by the answer ID
            $studentAnswer = StudentAnswer::find($answerData['answerId']);

            // Update the score for each answer
            if ($studentAnswer) {
                $studentAnswer->score = $answerData['score'];
                $studentAnswer->save();
            }
        }

        return response()->json([
            'message' => 'Scores updated successfully',
        ], 200);
    }
}
