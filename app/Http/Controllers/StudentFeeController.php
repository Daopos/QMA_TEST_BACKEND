<?php

namespace App\Http\Controllers;

use App\Models\StudentFee;
use App\Http\Requests\StoreStudentFeeRequest;
use App\Http\Requests\UpdateStudentFeeRequest;
use App\Models\Audit;
use Illuminate\Http\Request;


class StudentFeeController extends Controller
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
        if (!$request->all() || !is_array($request->all()) || empty($request->all())) {
            return response()->json(['error' => 'No fees provided'], 400);
        }
        $fields = $request->validate([
            '*.title' => 'required|string',
            '*.amount' => 'required|integer',
            '*.student_id' => 'required',
        ]);

        $createdFees = [];

        // Iterate over the array of fee objects and create each fee
        foreach ($fields as $fee) {
            $createdFees[] = StudentFee::create($fee);
        }

        return response()->json($createdFees, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(StudentFee $studentFee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentFeeRequest $request, StudentFee $studentFee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentFee $studentFee)
    {
        //
    }

    public function studentFee($id) {


        $studentFee = StudentFee::where("student_id", $id)->get();

        return $studentFee;
    }
    // public function studentFeePay(Request $request) {
    //     // Check if request contains data and it's an array
    //     if (!$request->all() || !is_array($request->all()) || empty($request->all())) {
    //         return response()->json(['error' => 'No fees provided'], 400);
    //     }

    //     // Validate that the request contains an array of fee objects with 'id' and 'amount'
    //     $fields = $request->validate([
    //         '*.id' => 'required|integer|exists:student_fees,id',
    //         '*.amount' => 'required|integer',
    //     ]);

    //     $updatedFees = [];

    //     // Iterate over the array of fee objects and update each fee
    //     foreach ($fields as $fee) {
    //         $studentFee = StudentFee::find($fee['id']);
    //         if ($studentFee) {
    //             $studentFee->amount -= $fee['amount'];
    //             $studentFee->save();
    //             $updatedFees[] = $studentFee;
    //         }
    //     }

    //     return response()->json(['updated_fees' => $updatedFees], 200);
    // }

    public function studentFeePay(Request $request) {
        // Check if request contains data and it's an array
        if (!$request->all() || !is_array($request->all()) || empty($request->all())) {
            return response()->json(['error' => 'No fees provided'], 400);
        }

        // Validate that the request contains an array of fee objects with 'id' and 'amount'
        $fields = $request->validate([
            '*.id' => 'required|integer|exists:student_fees,id',
            '*.amount' => 'required|integer',
        ]);

        $updatedFees = [];
        $registrar = auth()->user(); // Retrieve the authenticated registrar's information

        // Iterate over the array of fee objects and update each fee
        foreach ($fields as $fee) {
            $studentFee = StudentFee::find($fee['id']);
            if ($studentFee) {
                // Ensure we do not deduct more than the available amount
                if ($studentFee->amount >= $fee['amount']) {
                    $studentFee->amount -= $fee['amount'];
                    $studentFee->save();
                    $updatedFees[] = $studentFee;

                    // Create an audit entry for each fee payment
                    Audit::create([
                        'user' => $registrar->fname . ' ' . ($registrar->mname ? $registrar->mname . ' ' : '') . $registrar->lname,
                        'action' => 'processed payment for Student Fee ID: ' . $studentFee->id . ' Amount: ' . $fee['amount'],
                        'user_level' => 'Finance',
                    ]);
                } else {
                    return response()->json(['error' => 'Insufficient amount for fee ID: ' . $fee['id']], 400);
                }
            }
        }

        return response()->json(['updated_fees' => $updatedFees], 200);
    }


}
