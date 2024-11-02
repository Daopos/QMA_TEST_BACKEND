<?php

namespace App\Http\Controllers;

use App\Models\StudentPayment;
use App\Http\Requests\StoreStudentPaymentRequest;
use App\Http\Requests\UpdateStudentPaymentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentPaymentController extends Controller
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
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentPayment $studentPayment)
    {
        //

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentPaymentRequest $request, StudentPayment $studentPayment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentPayment $studentPayment)
    {
        //
    }

    // public function getStudentPayment($id) {

    //     $payment = StudentPayment::where('student_id', $id)->get();

    //     return response()->json($payment);

    // }


    public function getStudentPayment($id) {
        // Fetch all payments for the student and group by academic year
        $payments = StudentPayment::where('student_id', $id)
            ->select('academic_year', 'desc', 'amount', 'encoder')
            ->orderBy('academic_year')
            ->get()
            ->groupBy('academic_year');

        // Prepare a structured response
        $response = [];
        foreach ($payments as $academicYear => $paymentRecords) {
            $paymentDetails = [];
            foreach ($paymentRecords as $record) {
                $paymentDetails[] = [
                    'desc' => $record->desc,
                    'amount' => $record->amount,
                    'encoder' => $record->encoder,
                ];
            }
            $response[] = [
                'academic_year' => $academicYear,
                'payments' => $paymentDetails,
            ];
        }

        return response()->json($response);
    }

    public function getStudentSOA() {
        // Fetch all payments for the student and group by academic year
        $id = Auth::user()->id;

        $payments = StudentPayment::where('student_id', $id)
            ->select('academic_year', 'desc', 'amount', 'encoder', 'created_at')
            ->orderBy('academic_year')
            ->get()
            ->groupBy('academic_year');

        // Prepare a structured response
        $response = [];
        foreach ($payments as $academicYear => $paymentRecords) {
            $paymentDetails = [];
            foreach ($paymentRecords as $record) {
                $paymentDetails[] = [
                    'desc' => $record->desc,
                    'amount' => $record->amount,
                    'encoder' => $record->encoder,
                    'created_at' => $record->created_at
                ];
            }
            $response[] = [
                'academic_year' => $academicYear,
                'payments' => $paymentDetails,
            ];
        }

        return response()->json($response);
    }

    public function getParentSOA() {
        // Fetch all payments for the student and group by academic year
        $id = Auth::user()->student_id;

        $payments = StudentPayment::where('student_id', $id)
            ->select('academic_year', 'desc', 'amount', 'encoder', 'created_at')
            ->orderBy('academic_year')
            ->get()
            ->groupBy('academic_year');

        // Prepare a structured response
        $response = [];
        foreach ($payments as $academicYear => $paymentRecords) {
            $paymentDetails = [];
            foreach ($paymentRecords as $record) {
                $paymentDetails[] = [
                    'desc' => $record->desc,
                    'amount' => $record->amount,
                    'encoder' => $record->encoder,
                    'created_at' => $record->created_at
                ];
            }
            $response[] = [
                'academic_year' => $academicYear,
                'payments' => $paymentDetails,
            ];
        }

        return response()->json($response);
    }
}