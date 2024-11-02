<?php

namespace App\Http\Controllers;

use App\Models\StudentTotalFee;
use App\Http\Requests\StoreStudentTotalFeeRequest;
use App\Http\Requests\UpdateStudentTotalFeeRequest;
use App\Models\AcademicYear;
use App\Models\Audit;
use App\Models\Student;
use App\Models\StudentPayment;
use Illuminate\Http\Request;

class StudentTotalFeeController extends Controller
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
    public function store(StoreStudentTotalFeeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentTotalFee $studentTotalFee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentTotalFeeRequest $request, StudentTotalFee $studentTotalFee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentTotalFee $studentTotalFee)
    {
        //
    }

    // public function makePayment(Request $request) {
    //     // Validate the request data
    //     $request->validate([
    //         'student_id' => 'required|exists:students,id',
    //         'payment' => 'required|numeric|min:0',
    //         'desc' => 'required'
    //     ]);

    //     // Retrieve the student ID and payment amount from the request
    //     $studentId = $request->student_id;
    //     $paymentAmount = $request->payment;
    //     $desc = $request->desc;

    //     // Get the authenticated user
    //     $user = auth()->user(); // Fetch the authenticated user

    //     // Construct the encoder's full name
    //     $encoder = trim($user->fname . ' ' . $user->mname . ' ' . $user->lname);

    //     // Find the active academic year
    //     $activeAcademicYear = AcademicYear::where('active', true)->first();

    //     if (!$activeAcademicYear) {
    //         return response()->json(['error' => 'No active academic year found'], 404);
    //     }

    //     // Find the StudentTotalFee record for the given student
    //     $studentTotalFee = StudentTotalFee::where('student_id', $studentId)->first();

    //     if (!$studentTotalFee) {
    //         return response()->json(['error' => 'Student total fee record not found'], 404);
    //     }

    //     // Calculate the new total fee after payment
    //     $newTotalFee = $studentTotalFee->total_fee - $paymentAmount;

    //     if ($newTotalFee < 0) {
    //         return response()->json(['error' => 'Payment amount exceeds the total fee'], 400);
    //     }

    //     // Update the total fee
    //     $studentTotalFee->update(['total_fee' => $newTotalFee]);

    //     // Create a new StudentPayment record
    //     StudentPayment::create([
    //         'student_id' => $studentId,
    //         'amount' => $paymentAmount,
    //         'desc' => $desc,
    //         'encoder' => $encoder, // Encoder's full name
    //         'academic_year' => $activeAcademicYear->academic_year // Add active academic year
    //     ]);

    //     return response()->json(['message' => 'Payment successfully recorded']);
    // }

    public function makePayment(Request $request) {
        // Validate the request data
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment' => 'required|numeric|min:0',
            'desc' => 'required'
        ]);

        // Retrieve the student ID and payment amount from the request
        $studentId = $request->student_id;
        $paymentAmount = $request->payment;
        $desc = $request->desc;

        // Get the authenticated user
        $user = auth()->user(); // Fetch the authenticated user

        // Construct the encoder's full name
        $encoder = trim($user->fname . ' ' . ($user->mname ? $user->mname . ' ' : '') . $user->lname);

        // Find the active academic year
        $activeAcademicYear = AcademicYear::where('active', true)->first();

        if (!$activeAcademicYear) {
            return response()->json(['error' => 'No active academic year found'], 404);
        }

        // Find the Student record for the given student ID
        $student = Student::find($studentId);

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Find the StudentTotalFee record for the given student
        $studentTotalFee = StudentTotalFee::where('student_id', $studentId)->first();

        if (!$studentTotalFee) {
            return response()->json(['error' => 'Student total fee record not found'], 404);
        }

        // Calculate the new total fee after payment
        $newTotalFee = $studentTotalFee->total_fee - $paymentAmount;

        if ($newTotalFee < 0) {
            return response()->json(['error' => 'Payment amount exceeds the total fee'], 400);
        }

        // Update the total fee
        $studentTotalFee->update(['total_fee' => $newTotalFee]);

        // Create a new StudentPayment record
        StudentPayment::create([
            'student_id' => $studentId,
            'amount' => $paymentAmount,
            'desc' => $desc,
            'encoder' => $encoder, // Encoder's full name
            'academic_year' => $activeAcademicYear->academic_year // Add active academic year
        ]);

        // Construct the student's full name
        $studentFullName = trim($student->firstname . ' ' . ($student->middlename ? $student->middlename . ' ' : '') . $student->surname);

        // Log an audit entry for the payment
        Audit::create([
            'user' => $encoder,
            'action' => 'Payment made for Student: ' . $studentFullName . ' Amount: ' . $paymentAmount,
            'user_level' => 'Finance', // Adjust this according to your user levels
        ]);

        return response()->json(['message' => 'Payment successfully recorded']);
    }

    public function getStudentBalance($id)
    {
        // Join `StudentTotalFee` with `Student` and select necessary fields
        $student = StudentTotalFee::join('students', 'student_total_fees.student_id', '=', 'students.id')
            ->where('student_total_fees.student_id', $id)
            ->select('students.id','students.grade_level', 'students.middlename', 'students.firstname','students.lrn','students.surname','student_total_fees.total_fee') // Adjust fields as necessary
            ->first(); // Use first() to get a single record instead of get()

        return response()->json($student);
    }

}