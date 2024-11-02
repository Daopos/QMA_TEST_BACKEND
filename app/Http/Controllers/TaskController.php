<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
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

        $user = Auth::user();

        // Validate the request fields
        $fields = $request->validate([
            "subject_id" => "required|exists:subjects,id",
            "title" => "required",
            "description" => "nullable",
            "file" => "nullable|file|mimes:pdf,doc,docx,ppt,pptx,png,jpg|max:10240", // Add validation rules for file types and size
            "due_date" => "nullable|date"
        ]);

        $fields['teacher_id'] = $user->id;

        // Handle file upload
        if ($request->hasFile('file')) {
            // Get the uploaded file
            $file = $request->file('file');

            // Get the original filename
            $fileName = $file->getClientOriginalName();

            // Store the file in the 'uploads' directory in the public storage
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            // Add file path to fields array
            $fields['file'] = $filePath;
        }

        // Create the task with the validated fields
        Task::create($fields);

        return response()->json(['message' => 'Task successfully added']);
    }
    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update() {

    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        $field = Task::findOrFail($id);

        // Check if the task has an associated file
        if ($field->file && Storage::disk('public')->exists($field->file)) {
            // Delete the file from storage
            Storage::disk('public')->delete($field->file);
        }

        // Delete the task from the database
        $field->delete();

        return response()->json(['message' => 'Task successfully deleted']);
    }

    public function getTaskBySubject($id) {

        $tasks = Task::where('subject_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $tasksWithFiles = $tasks->map(function ($task) {
            if ($task->file) {
                $task->file_url = asset('storage/' . $task->file); // Generate the URL for the file
                $task->file_name = basename($task->file); // Extract the file name
                $task->file_type = pathinfo($task->file, PATHINFO_EXTENSION); // Get the file type
            }
            return $task;
        });

        return response()->json($tasksWithFiles);

    }
    public function editTask(Request $request, $id)
    {
        $user = Auth::user();

        // Find the task by ID
        $task = Task::findOrFail($id);

        // Validate the request fields
        $fields = $request->validate([
            "title" => "required",
            "description" => "nullable",
            "file" => "nullable|file|mimes:pdf,doc,docx,ppt,pptx,png,jpg|max:10240", // Add validation rules for file types and size
            "due_date" => "nullable|date"
        ]);
        $fields['teacher_id'] = $user->id;

        // Handle file upload
        if ($request->hasFile('file')) {
            // Get the uploaded file
            $file = $request->file('file');

            // Get the original filename
            $fileName = $file->getClientOriginalName();

            // Store the file in the 'uploads' directory in the public storage
            $filePath = $file->storeAs('uploads', $fileName, 'public');

            // If the task already has a file, delete the old file
            if ($task->file && Storage::disk('public')->exists($task->file)) {
                Storage::disk('public')->delete($task->file);
            }

            // Add the new file path to fields array
            $fields['file'] = $filePath;
        } else {
            // No new file is uploaded, but if there's an existing file, delete it
            if ($task->file && Storage::disk('public')->exists($task->file)) {
                Storage::disk('public')->delete($task->file);
                $fields['file'] = null; // Remove the file reference from the database
            }
        }

        // Update the task with the validated fields
        $task->update($fields);

        return response()->json(['message' => 'Task successfully updated']);
    }

}