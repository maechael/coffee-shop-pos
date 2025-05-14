<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderDetails;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $now = now();

        $orders = Order::with(['orderDetails.product'])
            ->whereDate('order_date', $now)
            ->get();

        $totalBuyingPrice = 0;
        $totalSellingPrice = 0;


        foreach ($orders as $order) {

            foreach ($order->orderDetails as $detail) {
                $sellingPrice = $detail->product->selling_price * $detail->quantity;
                $buyingPrice = $detail->product->buying_price * $detail->quantity;

                $totalBuyingPrice += $buyingPrice;
                $totalSellingPrice += $sellingPrice;
            }
        }
        $revenue = $totalSellingPrice - $totalBuyingPrice;


        return view('dashboard.index', [
            'total_paid' => Order::sum('pay'),
            'total_due' => Order::sum('due'),
            'complete_orders' => Order::where('order_status', 'complete')->get(),
            'products' => Product::orderBy('product_store')->take(5)->get(),
            'new_products' => Product::orderBy('buying_date')->take(2)->get(),
            'revenue' => $revenue,
            'totalBuyingPrice' => $totalBuyingPrice,
            'orders' => $orders

        ]);
    }
}
