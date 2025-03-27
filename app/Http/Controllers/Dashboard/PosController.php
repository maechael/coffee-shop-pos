<?php

namespace App\Http\Controllers\Dashboard;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\PaymentType;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session as FacadesSession;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class PosController extends Controller
{
    public function index()
    {
        $todayDate = Carbon::now();
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        return view('pos.index', [
            // 'customers' => Customer::all()->sortBy('name'),
            'productItem' => Cart::content(),
            'discounts' => Discount::get(),
            'products' => Product::where('expire_date', '>', $todayDate)->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    public function addCart(Request $request)
    {
        $rules = [
            'id' => 'required|numeric',
            'name' => 'required|string',
            'price' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::add([
            'id' => $validatedData['id'],
            'name' => $validatedData['name'],
            'qty' => 1,
            'price' => $validatedData['price'],
            'options' => ['size' => 'large']
        ]);

        return Redirect::back()->with('success', 'Product has been added!');
    }

    public function updateCart(Request $request, $rowId)
    {
        $rules = [
            'qty' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        Cart::update($rowId, $validatedData['qty']);

        return Redirect::back()->with('success', 'Cart has been updated!');
    }

    public function applyDiscount(Request $request)
    {
        // Validate input
        // $request->validate([
        //     'discount_price' => 'required|numeric|min:0|max:100',
        //     'password' => 'required'
        // ]);

        // // Get currently authenticated user
        // $user = Auth::user();

        // // Check if user is admin
        // if (!$user || !$user->hasRole('Admin')) {
        //     return back()->with('error', 'Only admins can apply discounts.');
        // }

        // // Verify password
        // if (!Hash::check($request->password, $user->password)) {
        //     return back()->with('error', 'Incorrect password.');
        // }

        Cart::setDiscount($request->discount_price);



        return Redirect::back()->with('success', 'Cart has been updated!');
    }

    public function deleteCart(String $rowId)
    {
        Cart::remove($rowId);

        return Redirect::back()->with('success', 'Cart has been deleted!');
    }

    public function createInvoice(Request $request)
    {
        $rules = [
            'customer_name' => 'required'
        ];

        $validatedData = $request->validate($rules);
        // $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::content();
        return view('pos.create-invoice', [
            'content' => $content,
            'paymentTypes' => PaymentType::get(),
            'customer_name' => $validatedData['customer_name']
        ]);
    }

    public function printInvoice(Request $request)
    {
        $rules = [
            'customer_id' => 'required'
        ];

        $validatedData = $request->validate($rules);
        $customer = Customer::where('id', $validatedData['customer_id'])->first();
        $content = Cart::content();

        return view('pos.print-invoice', [
            'customer' => $customer,
            'content' => $content
        ]);
    }
}
