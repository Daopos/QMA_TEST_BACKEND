<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\SubjectSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
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
        //
        $fields = $request->validate([
            'title' => 'required',
            'teacher_id' => 'nullable',
            'classroom_id' => 'required',
            'start' => 'nullable',
            'end' => 'nullable',
            'day' => 'nullable',
        ]);

        $subject = Subject::create($fields);

    }


    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        // Load the classroom relationship
        $subject->load(['classroom' => function($query) {
            $query->where('archived', false);
        }]);

        // Format start and end times with minutes
        $formattedStart = Carbon::parse($subject->start)->format('g:iA'); // e.g., 7:00AM
        $formattedEnd = Carbon::parse($subject->end)->format('g:iA');     // e.g., 8:00AM

        // Add the formatted times to the response
        $subject->formatted_time = $formattedStart . '-' . $formattedEnd;

        return response()->json($subject);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        //

        $fields = $request->validate([
            'title' => 'nullable',
            'teacher_id' => 'nullable',
            'start' => 'nullable',
            'end' => 'nullable',
            'day' => 'nullable'
        ]);

        $subject->update($fields);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        // Optionally, you can check for related data if necessary
        // For example, checking if there are any classes associated with this subject
        // If you have a relationship setup in your Subject model, you could use:
        // if ($subject->classlists()->exists()) {
        //     return response()->json(['message' => 'Cannot delete subject because it has associated classes.'], 400);
        // }

        // Delete the subject
        $subject->delete();

        return response()->json(['message' => 'Subject deleted successfully.'], 200);
    }

    public function getSubjectByClass($id) {
        // Join the subjects table with the employees table using teacher_id and employees.id
        $subjects = Subject::leftJoin('employees', 'subjects.teacher_id', '=', 'employees.id')
            ->select('subjects.*', 'employees.fname', 'employees.mname', 'employees.lname') // Assuming employees table has a 'name' column
            ->where('subjects.classroom_id', $id) // Filter by classroom_id
            ->get();

        // Return the subjects along with the teacher's name in the response
        return response()->json(['subjects' => $subjects]);
    }


    public function getSubjectByTeacher() {

        $user = Auth::user();


        // $subjects = Subject::where('teacher_id', $user->id)
        // ->join('classrooms', 'classrooms.id', '=', )
        // ->get();

        $subjects = Subject::join('classrooms', 'subjects.classroom_id', '=', 'classrooms.id')
    ->select('subjects.*', 'classrooms.title AS classroom_title', 'classrooms.grade_level')
    ->where('subjects.teacher_id', $user->id)  // Specify the table for teacher_id
    ->where(function($query) {
        $query->where('subjects.archived', false)
              ->orWhereNull('subjects.archived');
    })
    ->get();


        return response()->json(['subjects' => $subjects]);

    }

    public function getArchivedSubjectsByTeacher() {
        $user = Auth::user();

        // Retrieve archived subjects along with classroom information
        $archivedSubjects = Subject::join('classrooms', 'subjects.classroom_id', '=', 'classrooms.id')
            ->select('subjects.*', 'classrooms.title AS classroom_title', 'classrooms.grade_level')
            ->where('subjects.teacher_id', $user->id)  // Specify the table for teacher_id
            ->where('subjects.archived', true)  // Specify the table for archived subjects
            ->get();

        return response()->json(['archived_subjects' => $archivedSubjects]);
    }

    public function getSubjectByStudent() {
        $user = Auth::user();
        $studentId = $user->id;

        $subjects = DB::table('subjects')
            ->join('classrooms', 'subjects.classroom_id', '=', 'classrooms.id')
            ->join('classlists', 'classrooms.id', '=', 'classlists.class_id')
            ->join('employees', 'subjects.teacher_id', '=', 'employees.id')
            ->where('classlists.student_id', $studentId)
            ->where('classrooms.archived', false)
            ->select(
                'subjects.*',
                'employees.fname as teacher_fname',
                'employees.lname as teacher_lname',
                'classrooms.title as classroom_title'
            )
            ->get();

        return response()->json(['subjects' => $subjects]);
    }

    public function getStudentsBySubject($subjectId)
{
    $students = DB::table('subjects')
        ->join('classrooms', 'subjects.classroom_id', '=', 'classrooms.id')
        ->join('classlists', 'classlists.class_id', '=', 'classrooms.id')
        ->join('students', 'classlists.student_id', '=', 'students.id')
        ->select('students.*', 'classrooms.title AS classroom_title', 'subjects.title AS subject_title')
        ->where('subjects.id', $subjectId)
        ->get();

    return response()->json($students);
}

public function getSubjectByparent() {
    $user = Auth::user();


    // $subjects = Subject::where('teacher_id', $user->id)
    // ->join('classrooms', 'classrooms.id', '=', )
    // ->get();

    $studentId = $user->student_id; // Replace this with the actual student ID

    $subjects = DB::table('subjects')
        ->join('classrooms', 'subjects.classroom_id', '=', 'classrooms.id')
        ->join('classlists', 'classrooms.id', '=', 'classlists.class_id')
        ->join('employees', 'subjects.teacher_id', '=', 'employees.id')
        ->where('classlists.student_id', $studentId)
         ->where('classrooms.archived', false)

        ->select('subjects.*', 'employees.fname', 'employees.lname') // Select all fields from the subjects table
        ->get();

    return response()->json(['subjects' => $subjects]);
}
public function getSubjectCountByTeacher() {

    $user = Auth::user();

    $subjectCount = Subject::join('classrooms', 'subjects.classroom_id', '=', 'classrooms.id')
        ->where('subjects.teacher_id', $user->id)  // Specify the table for teacher_id
        ->where('subjects.archived', false)  // Specify the table for archived
        ->count();

    return response()->json(['subject_count' => $subjectCount]);

}

public function getSubjectCountByStudent() {
    $user = Auth::user();
    $studentId = $user->id;

    $subjectCount = DB::table('subjects')
        ->join('classrooms', 'subjects.classroom_id', '=', 'classrooms.id')
        ->join('classlists', 'classrooms.id', '=', 'classlists.class_id')
        ->join('employees', 'subjects.teacher_id', '=', 'employees.id')
        ->where('classlists.student_id', $studentId)
        ->where('classrooms.archived', false)
        ->count();

    return response()->json(['subject_count' => $subjectCount]);
}

}
