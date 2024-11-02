<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Choice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
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
    public function store(StoreQuestionRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestionRequest $request, Question $question)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        //
    }

    public function updateQuestion(Request $request, $id) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'correct_answer' => 'required|string|max:255',
            'pts' => 'nullable|integer',
            'index_position' => 'nullable',
            'type' => 'required|string|max:255',
            'choices' => 'required|array',
            'choices.*.id' => 'sometimes|exists:choices,id',
            'choices.*.choice_text' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $id) {
            // Update question
            $question = Question::findOrFail($id);
            $question->title = $validated['title'];
            $question->correct_answer = $validated['correct_answer'];
            $question->pts = $validated['pts'];
            $question->type = $validated['type'];
            $question->save();

            // Get existing choices
            $existingChoices = Choice::where('question_id', $question->id)->get()->keyBy('id');

            // Determine which choices to keep and which to remove
            $updatedChoices = collect($validated['choices'])->keyBy('id');
            $choicesToDelete = $existingChoices->keys()->diff($updatedChoices->keys());

            // Delete removed choices
            Choice::whereIn('id', $choicesToDelete)->delete();

            // Update or create choices
            foreach ($validated['choices'] as $choiceData) {
                if (isset($choiceData['id'])) {
                    // Update existing choice
                    $choice = Choice::findOrFail($choiceData['id']);
                    $choice->choice_text = $choiceData['choice_text'];
                    $choice->save();
                } else {
                    // Create new choice
                    Choice::create([
                        'question_id' => $question->id,
                        'choice_text' => $choiceData['choice_text'],
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Question and choices updated successfully']);
    }


    public function updateIdentification(Request $request, $id) {

        $identification = Question::find($id);

        $fields = $request->validate([
            "title" => 'required',
            'correct_answer' => 'required',
            'pts' => "nullable|integer",
        ]);

        $identification->update($fields);

        return response()->json(['message' => 'Successfully Updated']);
    }

    public function updateEssay(Request $request, $id) {
        $essay = Question::find($id);

        $fields = $request->validate([
            "title" => "required",
            "pts" => "nullable|integer",
        ]);

        $essay->update($fields);

        return response()->json(['message' => "Succesfully updated"]);
    }


    public function addEssay(Request $request) {
        $validated = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'title' => 'required|string',
            'correct_answer' => 'nullable|string',
            'pts' =>  'integer|required',
            'index_position' =>  'nullable|string',
            'type' =>  'nullable|string',
        ]);


        Question::create($validated);

        return response()->json(['message' => "Succesfully added"]);

    }

    public function addIdentification(Request $request) {
        $validated = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'title' => 'required|string',
            'correct_answer' => 'nullable|string',
            'pts' =>  'integer|required',
            'index_position' =>  'nullable|string',
            'type' =>  'nullable|string',
        ]);


        Question::create($validated);

        return response()->json(['message' => "Succesfully added"]);

    }

    public function addMultipleChoice(Request $request) {
        // Validate the incoming request
        $validated = $request->validate([
            'test_id' => 'required|exists:tests,id',
            'title' => 'required|string',
            'correct_answer' => 'nullable|string',
            'pts' => 'integer|required',
            'index_position' => 'nullable|string',
            'type' => 'nullable|string',
        ]);

        // Create the question and get the newly created instance
        $question = Question::create($validated);

        // Check if choices are provided and if the question type is multiple-choice
        if ($validated['type'] === 'multiple-choice' && $request->has('choices')) {
            foreach ($request->input('choices') as $choice) {
                // Assuming each choice is an array with 'choice_text' field
                if (is_array($choice) && isset($choice['choice_text'])) {
                    Choice::create([
                        'question_id' => $question->id,  // Use the created question's ID
                        'choice_text' => $choice['choice_text'],  // Ensure you're saving the text, not the array
                    ]);
                } else {
                    // If choices are passed as plain text
                    Choice::create([
                        'question_id' => $question->id,
                        'choice_text' => $choice,  // Save the plain choice text
                    ]);
                }
            }
        }

        // Return a success response
        return response()->json(['message' => "Successfully added"]);
    }


    public function deleteQuestion($id)
    {
        // Find the question by ID
        $question = Question::find($id);

        // Check if the question exists
        if (!$question) {
            return response()->json(['message' => 'Question not found'], 404);
        }

        // Delete the question
        $question->delete();

        // Return a success response
        return response()->json(['message' => 'Question deleted successfully'], 200);
    }


}
