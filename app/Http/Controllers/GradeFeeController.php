<?php

namespace App\Http\Controllers;

use App\Models\GradeFee;
use App\Http\Requests\StoreGradeFeeRequest;
use App\Http\Requests\UpdateGradeFeeRequest;
use App\Models\Audit;
use Illuminate\Http\Request;

class GradeFeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $gradefees = GradeFee::all();
        return ['gradefees' => $gradefees];
    }

    /**
     * Store a newly created resource in storage.
     */
//     public function store(Request $request)
//     {
//  // Check if the request is null or if it doesn't contain any fees
//         if (!$request->all() || !is_array($request->all()) || empty($request->all())) {
//             return response()->json(['error' => 'No fees provided'], 400);
//         }
//         // Validate that the request contains an array of fee objects
//         $fields = $request->validate([
//             '*.title' => 'required|string',
//             '*.amount' => 'required|integer',
//             '*.gradetype' => 'required|string',
//         ]);

//         $createdFees = [];

//         // Iterate over the array of fee objects and create each fee
//         foreach ($fields as $fee) {
//             $createdFees[] = GradeFee::create($fee);
//         }

//         return response()->json($createdFees, 201);
//     }

    public function store(Request $request)
{
    // Check if the request body is empty or doesn't contain any fee data
    if (!$request->all() || !is_array($request->all()) || empty($request->all())) {
        return response()->json(['error' => 'No fees provided'], 400);
    }

    // Validate that each fee entry has the required fields
    $fields = $request->validate([
        '*.title' => 'required|string',
        '*.amount' => 'required|integer',
        '*.gradetype' => 'required|string',
    ]);

    $createdFees = [];

    // Iterate over each validated fee, create a new GradeFee entry, and log an audit entry
    foreach ($fields as $fee) {
        $newFee = GradeFee::create($fee);
        $createdFees[] = $newFee;

        // Log an audit entry for each created fee with grade type included
        Audit::create([
            'user' => 'Admin',
            'action' => 'Admin created fee: ' . $newFee->title . ' with grade type: ' . $newFee->gradetype,
            'user_level' => 'Admin',
        ]);
    }

    return response()->json([
        'message' => 'Fees created successfully',
        'data' => $createdFees,
    ], 201);
}

    /**
     * Display the specified resource.
     */
    public function show(GradeFee $gradeFee)
    {
        //
        return [
            'gradeFee' => $gradeFee
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        // Validate and retrieve the validated data
        $gradeFee = GradeFee::find($id);
        $fields = $request->validate([
            'amount' => 'required|integer',
            'title' => 'required|string',
        ]);
        // Update the grade fee with validated data


        Audit::create([
            'user' => 'Admin',
            'action' => 'Admin updated fee: ' . $gradeFee->title . ' with grade type: ' . $gradeFee->gradetype,
            'user_level' => 'Admin',
        ]);


       $gradeFee->update($fields);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        $gradeFee = GradeFee::find($id);

        Audit::create([
            'user' => 'Admin',
            'action' => 'Admin deleted fee: ' . $gradeFee->title . ' with grade type: ' . $gradeFee->gradetype,
            'user_level' => 'Admin',
        ]);

        $gradeFee->delete();

    }

    public function gradetype(GradeFee $gradeFee, $type) {


        $fee = $gradeFee->where('gradetype', $type)->get();

        return [
            'gradefees' => $fee
        ];



    }


}
