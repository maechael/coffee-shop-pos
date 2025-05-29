<?php

namespace App\Http\Controllers;

use App\Models\TypeOfSupplier;
use Clockwork\Request\Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class TypeOfSupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        return view('type-of-supplier.index', [
            'typeOfSuppliers' => TypeOfSupplier::paginate($row),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('type-of-supplier.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //\
        $rules = [
            'name' => 'required|unique:categories,name',

        ];

        $validatedData = $request->validate($rules);

        TypeOfSupplier::create($validatedData);

        return Redirect::route('type-of-supplier.index')->with('success', 'Category has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeOfSupplier $TypeOfSupplier)
    {
        //
        return view('type-of-supplier.edit', [
            'typeOfSupplier' => $TypeOfSupplier
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TypeOfSupplier $TypeOfSupplier)
    {
        //

        $rules = [
            'name' => 'required',

        ];

        $validatedData = $request->validate($rules);

        TypeOfSupplier::find($TypeOfSupplier->id)->update($validatedData);

        return Redirect::route('type-of-supplier.index')->with('success', 'Type of Supplier has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeOfSupplier $typeOfSupplier)
    {
        //
        try {
            DB::beginTransaction();
            $typeOfSupplier->delete();
            DB::commit();
            return Redirect::route('type-of_supplier.index')->with('success', 'Raw Material has been deleted!');
        } catch (Exception) {
            DB::rollBack();
        }
    }
}
