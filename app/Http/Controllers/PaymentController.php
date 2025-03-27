<?php

namespace App\Http\Controllers;

use App\Models\PaymentType;
use Clockwork\Request\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PaymentController extends Controller
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
        return view('payment-type.index', [
            'paymentTypes' => PaymentType::filter(request(['search']))
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
        return view('payment-type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            DB::beginTransaction();
            $rules = [
                'name' => 'required|unique:categories,name',
            ];

            $validatedData = $request->validate($rules);

            PaymentType::create($validatedData);
            DB::commit();
            return Redirect::route('payment_type.index')->with('success', 'Payment Type has been created!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('payment_type.index')->with('error', 'Error On Saving Payment Type!');
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
        $paymentType = PaymentType::find($id);
        return view('payment-type.edit', [
            'paymentType' => $paymentType
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $rules = [
                'name' => 'required|unique:payment_type,name,' . $id,
            ];


            $validatedData = $request->validate($rules);

            $paymentType = PaymentType::findOrFail($id);
            $paymentType->update($validatedData);

            DB::commit();
            return Redirect::route('payment_type.index')->with('success', 'Payment Type has been created!');
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('payment_type.index')->with('error', 'Error On Saving Payment Type!');
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
            $paymentType = PaymentType::find($id);
            $paymentType->delete();
            DB::commit();
            return Redirect::route('payment_type.index')->with('success', 'Payment Type has been deleted!');
        } catch (\Exception $e) {
            return Redirect::route('payment_type.index')->with('error', 'Payment Type has been deleted!');
        }
    }
}
