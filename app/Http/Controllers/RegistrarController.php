<?php

namespace App\Http\Controllers;

use App\Models\Registrar;
use App\Http\Requests\StoreRegistrarRequest;
use App\Http\Requests\UpdateRegistrarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegistrarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return Registrar::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        $fields = $request->validate([
            'name' => 'required|max:255',
        ]);

        $registrar = Registrar::create($fields);

        return [
            'registrar' => $registrar
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(Registrar $registrar)
    {
        //

        return [
            'registrar' => $registrar
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Registrar $registrar)
    {
        //

        $fields = $request->validate([
            'name' => 'required|max:255',
        ]);

        $registrar->update($fields);

        return  $registrar;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Registrar $registrar)
    {
        //

        $registrar->delete();

        return ['message' => 'deleted'];
    }


    public function getEnrollmentForm()
    {
        $filePath = 'enrollment_form/form.pdf'; // Path relative to storage/app/public/

        // Check if the file exists
        if (Storage::disk('public')->exists($filePath)) {
            // Generate the full public URL including the domain
            $fileUrl = url(Storage::url($filePath));
            return response()->json(['file_url' => $fileUrl], 200);  // Return the URL inside a JSON object
        }

        return response()->json(['error' => 'File not found.'], 404);
    }
}