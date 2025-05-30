@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Information Order Details</h4>
                    </div>
                </div>

                <div class="card-body">
                    <!-- begin: Show Data -->
                    <div class="form-group row align-items-center">
                        <div class="col-md-12">
                            <div class="profile-img-edit">
                                <div class="crm-profile-img-edit">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="form-group col-md-12">
                            <label>Customer Name</label>
                            <input type="text" class="form-control bg-white" value="{{ $order->customer_name }}" readonly>
                        </div>

                    </div>
                    <!-- end: Show Data -->

                    @if ($order->order_status == 'pending')
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="d-flex align-items-center list-action">
                                <form action="{{ route('order.updateStatus') }}" method="POST" style="margin-bottom: 5px">
                                    @method('put')
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $order->id }}">
                                    <button type="submit" class="btn btn-success mr-2 border-none" data-toggle="tooltip" data-placement="top" title="" data-original-title="Complete">Complete Order</button>

                                    <a class="btn btn-danger mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Cancel" href="{{ route('order.pendingOrders') }}">Cancel</a>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>


        <!-- end: Show Data -->
        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="table mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th>No.</th>
                            <th>Photo</th>
                            <th>Product Name</th>
                            <th>Product Code</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total(+vat)</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @foreach ($orderDetails as $item)
                        <tr>
                            <td>{{ $loop->iteration  }}</td>
                            <td>
                                <img class="avatar-60 rounded" src="{{ $item->product->product_image ? asset('assets/images/product/'.$item->product->product_image) : asset('assets/images/product/default.webp') }}">
                            </td>
                            <td>{{ $item->product->product_name }}</td>
                            <td>{{ $item->product->product_code }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unitcost }}</td>
                            <td>{{ $item->total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

@include('components.preview-img-form')
@endsection