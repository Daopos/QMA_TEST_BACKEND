<?php

namespace App\Http\Controllers;

use App\Models\StudentGrade;
use App\Http\Requests\StoreStudentGradeRequest;
use App\Http\Requests\UpdateStudentGradeRequest;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentGradeController extends Controller
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
    public function store(StoreStudentGradeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentGrade $studentGrade)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentGradeRequest $request, StudentGrade $studentGrade)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentGrade $studentGrade)
    {
        //
    }

    public function createStudentGrade(Request $request)
{
    // Validate the request fields
    $fields = $request->validate([
        'student_id' => 'required',
        'subject_id' => 'nullable',
        'first_quarter' => 'nullable',
        'second_quarter' => 'nullable',
        'third_quarter' => 'nullable',
        'fourth_quarter' => 'nullable',
    ]);

    // Get the active academic year
    $activeAcademicYear = AcademicYear::where('active', 1)->first();

    if (!$activeAcademicYear) {
        return response()->json(['message' => 'No active academic year found'], 404);
    }

    // Get the grade level from the Student model
    $student = Student::find($fields['student_id']);

    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    // Get the title of the subject from the subject_id
    if ($fields['subject_id']) {
        $subject = Subject::find($fields['subject_id']);
        if (!$subject) {
            return response()->json(['message' => 'Subject not found'], 404);
        }
        $fields['subject'] = $subject->title;
    } else {
        return response()->json(['message' => 'Subject ID is required'], 400);
    }

    // Add the active academic year and the student's grade level to the fields
    $fields['academic_year'] = $activeAcademicYear->academic_year;
    $fields['grade_level'] = $student->grade_level;

    // Create the student grade record
    StudentGrade::create($fields);

    return response()->json(['message' => 'Student grade created successfully'], 201);
}

public function getStudentGrade($studentId, $subjectId)
{
    // Fetch the student's grade for the given subject and academic year
    $activeAcademicYear = AcademicYear::where('active', 1)->first();

    if (!$activeAcademicYear) {
        return response()->json(['message' => 'No active academic year found'], 404);
    }

    $grade = StudentGrade::where('student_id', $studentId)
        ->where('subject_id', $subjectId)
        ->where('academic_year', $activeAcademicYear->academic_year)
        ->first();

    if (!$grade) {
        return response()->json(null, 200); // No grade found, return null
    }

    return response()->json($grade, 200);
}

public function updateStudentGrade(Request $request)
{
    // Validate the request fields
    $fields = $request->validate([
        'student_id' => 'required',
        'subject_id' => 'required',
        'first_quarter' => 'nullable',
        'second_quarter' => 'nullable',
        'third_quarter' => 'nullable',
        'fourth_quarter' => 'nullable',
    ]);

    // Get the active academic year
    $activeAcademicYear = AcademicYear::where('active', 1)->first();

    if (!$activeAcademicYear) {
        return response()->json(['message' => 'No active academic year found'], 404);
    }

    // Get the student by ID
    $student = Student::find($fields['student_id']);
    if (!$student) {
        return response()->json(['message' => 'Student not found'], 404);
    }

    // Get the subject by ID
    $subject = Subject::find($fields['subject_id']);
    if (!$subject) {
        return response()->json(['message' => 'Subject not found'], 404);
    }

    // Update or create the student grade record
    $grade = StudentGrade::updateOrCreate(
        [
            'student_id' => $fields['student_id'],
            'subject_id' => $fields['subject_id'],
            'academic_year' => $activeAcademicYear->academic_year,
        ],
        [
            'first_quarter' => $fields['first_quarter'],
            'second_quarter' => $fields['second_quarter'],
            'third_quarter' => $fields['third_quarter'],
            'fourth_quarter' => $fields['fourth_quarter'],
            'subject' => $subject->title, // Set the subject title
            'grade_level' => $student->grade_level, // Set the student's grade level
        ]
    );

    return response()->json(['message' => 'Student grade updated successfully'], 200);
}

public function getStudentGradesByAcademicYear()
    {

        $studentId = Auth::user()->id;
        // Retrieve the grades for the student and group by academic_year
        $grades = StudentGrade::where('student_id', $studentId)
            ->select('academic_year', 'first_quarter', 'second_quarter', 'third_quarter', 'fourth_quarter', 'subject')
            ->get()
            ->groupBy('academic_year');

        // Check if grades exist for the student
        // if ($grades->isEmpty()) {
        //     return response()->json(['message' => 'No grades found for this student'], 404);
        // }

        // Return the grouped grades
        return response()->json($grades, 200);
    }


    public function getParentGradesByAcademicYear()
    {

        $studentId = Auth::user()->student_id;
        // Retrieve the grades for the student and group by academic_year
        $grades = StudentGrade::where('student_id', $studentId)
            ->select('academic_year', 'first_quarter', 'second_quarter', 'third_quarter', 'fourth_quarter', 'subject')
            ->get()
            ->groupBy('academic_year');

        // Check if grades exist for the student
        // if ($grades->isEmpty()) {
        //     return response()->json(['message' => 'No grades found for this student'], 404);
        // }

        // Return the grouped grades
        return response()->json($grades, 200);
    }


}