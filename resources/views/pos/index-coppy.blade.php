@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <!-- Left Panel (Products) -->
        <div class="col-8 p-3" style="background-color: black; height: 100vh;">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg" style="background-color: #1a1a1a;">
                <div class="navbar-nav ms-3">
                    @foreach($categories as $category)
                    <a href="#" class="nav-link text-white 
                            {{ request()->category == $category->id ? 'border-bottom border-success' : '' }}">
                        {{$category->name}}
                    </a>
                    @endforeach
                </div>
            </nav>

            <!-- Product Grid -->
            <div class="d-flex flex-wrap mt-3">
                @php
                $colors = ['green', 'purple', 'blue', 'pink', 'green', 'orange'];
                $index = 0;
                @endphp
                @foreach ($products as $product)
                <div class="m-2 p-3 text-white text-center"
                    style="border: 2px solid {{ $colors[$index % count($colors)] }};
                               width: 120px; height: 60px; line-height: 50px;">
                    {{ $product->product_name }}
                </div>
                @php $index++; @endphp
                @endforeach
            </div>
        </div>

        <!-- Right Panel (Order Summary & Payment) -->
        <div class="col-4 p-3" style="background-color: white; height: 100vh;">
            <div class="text-success fs-5">Hello !</div>
            <hr>

            <div class="mt-3">
                <div class="text-end">SubTotal <strong>P0</strong></div>
            </div>

            <!-- Payment Options -->
            <div class="mt-3">
                <div class="nav nav-tabs">
                    <a class="nav-link active text-success" href="#">Change</a>
                    <a class="nav-link text-secondary" href="#">Discount</a>
                    <a class="nav-link text-secondary" href="#">Service</a>
                    <a class="nav-link text-secondary" href="#">Print</a>
                </div>

                <!-- Payment Buttons -->
                <div class="d-flex flex-wrap mt-3">
                    @php
                    $amounts = [
                    ['amount' => 'P20', 'color' => 'red'],
                    ['amount' => 'P50', 'color' => 'red'],
                    ['amount' => 'P100', 'color' => 'purple'],
                    ['amount' => 'P200', 'color' => 'green'],
                    ['amount' => 'P500', 'color' => 'gold'],
                    ['amount' => 'P1000', 'color' => 'blue'],
                    ['amount' => 'Custom', 'color' => 'gray'],
                    ['amount' => 'Clear', 'color' => 'red']
                    ];
                    @endphp

                    @foreach ($amounts as $amt)
                    <button class="btn text-dark m-1"
                        style="border: 2px solid {{ $amt['color'] }}; width: 80px;">
                        {{ $amt['amount'] }}
                    </button>
                    @endforeach
                </div>

                <!-- Payment Summary -->
                <div class="mt-3">
                    <div>Cash Received: <strong>P0</strong></div>
                    <div>Change: <strong>P0</strong></div>
                </div>

                <!-- Payment Methods -->
                <div class="mt-3 d-flex justify-content-between">
                    <button class="btn btn-warning w-50">Card</button>
                    <button class="btn btn-success w-50">Cash P0</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 mb-3">
            <table class="table">
                <thead>
                    <tr class="ligth">
                        <th scope="col">Name</th>
                        <th scope="col">QTY</th>
                        <th scope="col">Price</th>
                        <th scope="col">SubTotal</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productItem as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td style="min-width: 140px;">
                            <form action="{{ route('pos.updateCart', $item->rowId) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="number" class="form-control" name="qty" required value="{{ old('qty', $item->qty) }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-success border-none" data-toggle="tooltip" data-placement="top" title="" data-original-title="Sumbit"><i class="fas fa-check"></i></button>
                                    </div>
                                </div>
                            </form>
                        </td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->subtotal }}</td>
                        <td>
                            <a href="{{ route('pos.deleteCart', $item->rowId) }}" class="btn btn-danger border-none" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="fa-solid fa-trash mr-0"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="container row text-center">
                <div class="form-group col-sm-6">
                    <p class="h4 text-primary">Quantity: {{ Cart::count() }}</p>
                </div>
                <div class="form-group col-sm-6">
                    <p class="h4 text-primary">Subtotal: {{ Cart::subtotal() }}</p>
                </div>
                <div class="form-group col-sm-6">
                    <p class="h4 text-primary">Vat: {{ Cart::tax() }}</p>
                </div>
                <div class="form-group col-sm-6">
                    <p class="h4 text-primary">Total: {{ Cart::total() }}</p>
                </div>
                <div class="form-group col-sm-6">
                    <p class="h4 text-primary">Discount: {{ Cart::discount() }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <form action="{{ route('pos.createInvoice') }}" method="POST">
                        @csrf
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="customer_name">Customer Name</label>
                                    <input class="form-control" id="customer_name" name="customer_name">
                                    @error('customer_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="d-flex flex-wrap align-items-center justify-content-center">
                                    <button type="submit" class="btn btn-success add-list mx-1">Create Invoice</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-6">
                    <form action="{{ route('pos.applyDiscount') }}" method="POST">
                        @csrf
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="discount_price">Discount Price</label>
                                    <select class="form-control @error('discount_price') is-invalid @enderror" name="discount_price">
                                        <option selected="" disabled="">-- Select Discount --</option>
                                        @foreach($discounts as $discount)
                                        <option value="{{ $discount->discount }}"
                                            {{ Cart::discount() == $discount->discount ? 'selected' : '' }}>
                                            {{ $discount->name }} ({{ $discount->discount }}%)
                                        </option>

                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="d-flex flex-wrap align-items-center justify-content-center">
                                    <button type="submit" class="btn btn-info add-list mx-1">Apply Discount</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>



        </div>
    </div>
</div>
@endsection