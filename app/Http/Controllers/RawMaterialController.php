<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class RawMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('raw-materials.index', [
            'rawMaterials' => RawMaterial::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('raw-materials.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        try {
            DB::beginTransaction();

            $data = $request->all();

            $data['quantity'] = (int)$data['quantity'];

            $rawMaterial = new RawMaterial();
            $rawMaterial->fill($data);
            $rawMaterial->save();

            DB::commit();
            return Redirect::route('raw-material.index')->with('success', 'Raw Material has been created!');
        } catch (Exception $e) {
            DB::rollBack();

            // Optionally log the error or rethrow
            // Log::error($e->getMessage());

            return back()->with('error', 'Something went wrong. Please try again.');
        }
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
    public function edit(RawMaterial $rawMaterial)
    {
        //

        return view('raw-materials.edit', [
            'rawMaterial' => $rawMaterial
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RawMaterial $rawMaterial)
    {
        //

        try {
            DB::beginTransaction();

            $data = $request->all();

            $data['quantity'] = (int)$data['quantity'];

            $rawMaterial = RawMaterial::find($rawMaterial->id);
            $rawMaterial->fill($data);
            $rawMaterial->save();

            DB::commit();
            return Redirect::route('raw-material.index')->with('success', 'Raw Material Successfully Updated!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RawMaterial $rawMaterial)
    {
        //
        try {
            DB::beginTransaction();
            $rawMaterial->delete();
            DB::commit();
            return Redirect::route('raw-material.index')->with('success', 'Raw Material has been deleted!');
        } catch (Exception) {
            DB::rollBack();
            Log::info('Error on deletion of raw material');
        }
    }
}
