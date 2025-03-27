<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class DiscountController extends Controller
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
        return view('discount.index', [
            'discounts' => Discount::filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('discount.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            DB::beginTransaction();
            $rules = [
                'name' => 'required|unique:discount,name',
                'discount' => 'required'
            ];

            $validatedData = $request->validate($rules);

            Discount::create($validatedData);
            DB::commit();
            return Redirect::route('discount.index')->with('success', 'Discount has been created!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('discount.index')->with('error', 'Error On Saving Discount!');
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
    public function edit(string $id)
    {
        //
        $discount = Discount::find($id);
        return view('discount.edit', [
            'discount' => $discount
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        try {
            DB::beginTransaction();
            $rules = [
                'name' => 'required|unique:discount,name,' . $id,
                'discount' => 'required'
            ];

            $validatedData = $request->validate($rules);

            $discount = Discount::findOrFail($id);
            $discount->update($validatedData);

            DB::commit();

            return Redirect::route('discount.index')->with('success', 'Discount has been created!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('discount.index')->with('error', 'Error On Saving Discount!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try {
            DB::beginTransaction();
            $discount = Discount::find($id);
            $discount->delete();
            DB::commit();
            return Redirect::route('discount.index')->with('success', 'Discount Type has been deleted!');
        } catch (\Exception $e) {
            return Redirect::route('discount.index')->with('error', 'Discount Type has been deleted!');
        }
    }
}
