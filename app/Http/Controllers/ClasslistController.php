<?php

namespace App\Http\Controllers;

use App\Models\Classlist;
use App\Http\Requests\StoreClasslistRequest;
use App\Http\Requests\UpdateClasslistRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClasslistController extends Controller
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
            '*.student_id' => 'required',
            '*.class_id' => 'required'
        ]);

        $createdclasslist = [];


        foreach ($fields as $tudent) {
            $createdclasslist[] = Classlist::create($tudent);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Classlist $classlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClasslistRequest $request, Classlist $classlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ids) {
        // Convert the comma-separated string of IDs into an array
        $idsArray = explode(',', $ids);

        // Ensure $idsArray is an array of integers
        $idsArray = array_map('intval', $idsArray);

        // Delete the records by IDs
        Classlist::whereIn('id', $idsArray)->delete();

        return response()->json(['message' => 'Records deleted successfully.']);
    }

        //


        public function getByClassId($id) {
            $classLists = ClassList::with('student')  // Eager load the student relationship
                ->where('class_id', $id)
                ->get();

            // Map over the results to include both the student and the classlist ID
            $students = $classLists->map(function($classList) {
                return [
                    'classlist_id' => $classList->id,
                    'student' => $classList->student,
                ];
            });

            return response()->json(['students' => $students]);
        }

    public function getStudentByAdviser() {

        $user = Auth::user();


        $students = Classlist::join('students', 'classlists.student_id', '=', 'students.id')
        ->join('classrooms', 'classlists.class_id', '=', 'classrooms.id')
        ->where('classrooms.adviser_id', $user->id)
        ->select('students.*')
        ->get();


        return response()->json($students);
    }

}