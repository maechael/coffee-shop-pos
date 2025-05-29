<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\OrdersExport;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pendingOrders()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = Order::where('order_status', 'pending')->sortable()->paginate($row);

        return view('orders.pending-orders', [
            'orders' => $orders
        ]);
    }

    public function completeOrders()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = Order::where('order_status', 'complete')->sortable()->paginate($row);

        return view('orders.complete-orders', [
            'orders' => $orders
        ]);
    }

    public function toggleStatus(Request $request)
    {
        $order = Order::find($request->order_id);
        if ($order) {
            $order->status = $request->is_active == 1 ? 'active' : 'inactive'; // Update status
            $order->save();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function stockManage()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        return view('stock.index', [
            'products' => Product::with(['category', 'supplier'])
                ->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeOrder(Request $request)
    {

        $request->merge([
            'pay' => str_replace(',', '', $request->pay) // Remove commas
        ]);

        $rules = [
            'customer_name' => 'required|string',
            'payment_status' => 'required|string',
            'pay' => 'nullable|numeric|min:0', // Allow null, but must be a number if provided
            'due' => 'nullable|numeric|min:0'
        ];

        $invoice_no = IdGenerator::generate([
            'table' => 'orders',
            'field' => 'invoice_no',
            'length' => 10,
            'prefix' => 'INV-'
        ]);


        $validatedData = $request->validate($rules);

        $validatedData['order_date'] = Carbon::now()->format('Y-m-d');
        $validatedData['order_status'] = 'pending';
        $validatedData['total_products'] = Cart::count();
        $validatedData['sub_total'] = floatval(str_replace(',', '', Cart::subtotal()));
        $validatedData['vat'] = Cart::tax();
        $validatedData['invoice_no'] = $invoice_no;
        $validatedData['total'] = floatval(str_replace(',', '', Cart::total()));
        $validatedData['due'] = floatval(str_replace(',', '', Cart::total())) - floatval($validatedData['pay']);
        $validatedData['created_at'] = Carbon::now();

        $order_id = Order::insertGetId($validatedData);

        // Create Order Details
        $contents = Cart::content();
        $oDetails = array();

        foreach ($contents as $content) {
            $oDetails['order_id'] = $order_id;
            $oDetails['product_id'] = $content->id;
            $oDetails['quantity'] = $content->qty;
            $oDetails['unitcost'] = $content->price;
            $oDetails['total'] = $content->total;
            $oDetails['created_at'] = Carbon::now();

            OrderDetails::insert($oDetails);

            $product = Product::find($content->id);

            foreach ($product->rawMaterials as $rawMaterial) {
                // Subtract from the raw material stock based on quantity_cost
                // $quantityUsedPerUnit = $rawMaterial->pivot->quantity_cost ?? 1;
                // $totalUsed = $quantityUsedPerUnit * $content->qty;
                $rawMaterial->quantity -= 1;
                $rawMaterial->save();
            }
        }

        // Delete Cart Sopping History
        Cart::destroy();
        Cart::setDiscount(0);

        return response()->json([
            'success' => true,
            'message' => 'Order has been created!',
            'redirect' => route('dashboard') // Optional: include redirect URL if needed
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function orderDetails(Int $order_id)
    {
        $order = Order::where('id', $order_id)->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->orderBy('id', 'DESC')
            ->get();

        return view('orders.details-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request)
    {
        $order_id = $request->id;

        // Reduce the stock
        $products = OrderDetails::where('order_id', $order_id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                ->update(['product_store' => DB::raw('product_store-' . $product->quantity)]);
        }

        Order::findOrFail($order_id)->update(['order_status' => 'complete']);

        return Redirect::route('order.pendingOrders')->with('success', 'Order has been completed!');
    }

    public function invoiceDownload(Int $order_id)
    {
        $order = Order::where('id', $order_id)->first();
        $orderDetails = OrderDetails::with('product')
            ->where('order_id', $order_id)
            ->orderBy('id', 'DESC')
            ->get();

        // show data (only for debugging)
        return view('orders.invoice-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    public function pendingDue()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        $orders = Order::where('due', '>', '0')
            ->sortable()
            ->paginate($row);

        return view('orders.pending-due', [
            'orders' => $orders
        ]);
    }

    public function orderDueAjax(Int $id)
    {
        $order = Order::findOrFail($id);

        return response()->json($order);
    }

    public function updateDue(Request $request)
    {
        $rules = [
            'order_id' => 'required|numeric',
            'due' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        $order = Order::findOrFail($request->order_id);
        $mainPay = $order->pay;
        $mainDue = $order->due;

        $paid_due = $mainDue - $validatedData['due'];
        $paid_pay = $mainPay + $validatedData['due'];

        Order::findOrFail($request->order_id)->update([
            'due' => $paid_due,
            'pay' => $paid_pay,
        ]);

        return Redirect::route('order.pendingDue')->with('success', 'Due Amount Updated Successfully!');
    }

    public function generateReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $orders = Order::where('status', 'active')->with(['orderDetails.product'])
            ->whereBetween('order_date', [$request->start_date, $request->end_date])
            ->get();

        $data = [];
        $totalBuyingPrice = 0;
        $totalSellingPrice = 0;
        $totalVat = 0;

        foreach ($orders as $order) {
            $orderVatAdded = false;

            foreach ($order->orderDetails as $detail) {
                $sellingPrice = $detail->product->selling_price * $detail->quantity;
                $buyingPrice = $detail->product->buying_price * $detail->quantity;

                $totalBuyingPrice += $buyingPrice;
                $totalSellingPrice += $sellingPrice;

                // Only add VAT once per order
                if (!$orderVatAdded) {
                    $totalVat += $order->vat;
                    $orderVatAdded = true;
                }

                $data[] = [
                    'Invoice No' => $order->invoice_no,
                    'Order Date' => $order->order_date,
                    'Product' => $detail->product->product_name,
                    'Quantity' => $detail->quantity,
                    'Buying Price' => $buyingPrice,
                    'Selling Price' => $sellingPrice,
                    'VAT' => !$orderVatAdded ? $order->vat : '',
                ];
            }
        }

        // Calculate Revenue
        $revenue = $totalSellingPrice - ($totalBuyingPrice + $totalVat);

        // Add totals at the end of the report
        $data[] = [
            'Invoice No' => '',
            'Order Date' => '',
            'Product' => 'TOTALS',
            'Quantity' => '',
            'Buying Price' => $totalBuyingPrice,
            'Selling Price' => $totalSellingPrice,
            'VAT' => $totalVat,
        ];
        $data[] = [
            'Invoice No' => '',
            'Order Date' => '',
            'Product' => 'REVENUE',
            'Quantity' => '',
            'Buying Price' => '',
            'Selling Price' => '',
            'VAT' => '',
            'Revenue' => $revenue,
        ];


        return Excel::download(new OrdersExport($data), 'Order_Report.xlsx');
    }
}
