@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block">
                <div class="card-header d-flex justify-content-between bg-primary">
                    <div class="iq-header-title">
                        <h4 class="card-title mb-0">Invoice</h4>
                    </div>

                    <div class="invoice-btn d-flex">
                        <!-- <form action="{{ route('pos.printInvoice') }}" method="post">
                            @csrf
                           
                            <button type="submit" class="btn btn-primary-dark mr-2"><i class="las la-print"></i> Print</button>
                        </form> -->

                        <button type="button" class="btn btn-primary-dark mr-2" data-toggle="modal" data-target=".bd-example-modal-lg">Create</button>

                        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-white">
                                        <h3 class="modal-title text-center mx-auto">Invoice of {{$customer_name}}<br />Total Amount ₱{{ Cart::total() }}</h3>
                                    </div>
                                    <form id="formStoreOrder">
                                        @csrf
                                        <div class="modal-body">
                                            <input type="hidden" name="customer_name" id="customer_name" value={{$customer_name}}>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="payment_status">Payment</label>
                                                    <select class="form-control @error('payment_status') is-invalid @enderror" name="payment_status">
                                                        <option selected="" disabled="">-- Select Payment --</option>
                                                        @foreach($paymentTypes as $paymentType)
                                                        <option value="{{$paymentType->name}}">{{$paymentType->name}}</option>

                                                        @endforeach
                                                    </select>
                                                    @error('payment_status')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="pay">Pay Now</label>
                                                    <input type="text" class="form-control @error('pay') is-invalid @enderror" id="pay" name="pay" value="{{ old('pay') }}">
                                                    @error('pay')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <img src="{{ asset('assets/images/logo.png') }}" class="logo-invoice img-fluid mb-3">
                            <h5 class="mb-3">Hello, {{$customer_name}}</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="table-responsive-sm">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Order Date</th>
                                            <th scope="col">Order Status</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ Carbon\Carbon::now()->format('M d, Y') }}</td>
                                            <td><span class="badge badge-danger">Unpaid</span></td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="mb-3">Order Summary</h5>
                            <div class="table-responsive-lg">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="text-center" scope="col">#</th>
                                            <th scope="col">Item</th>
                                            <th class="text-center" scope="col">Quantity</th>
                                            <th class="text-center" scope="col">Price</th>
                                            <th class="text-center" scope="col">Totals</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($content as $item)
                                        <tr>
                                            <th class="text-center" scope="row">{{ $loop->iteration }}</th>
                                            <td>
                                                <h6 class="mb-0">{{ $item->name }}</h6>
                                            </td>
                                            <td class="text-center">{{ $item->qty }}</td>
                                            <td class="text-center">{{ $item->price }}</td>
                                            <td class="text-center"><b>{{ $item->subtotal }}</b></td>
                                        </tr>

                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>



                    <div class="row mt-4 mb-3">
                        <div class="offset-lg-8 col-lg-4">
                            <div class="or-detail rounded">
                                <div class="p-3">
                                    <h5 class="mb-3">Order Details</h5>
                                    <div class="mb-2">
                                        <h6>Sub Total</h6>
                                        <p>${{ Cart::subtotal() }}</p>
                                    </div>
                                    <div>
                                        <h6>Vat (5%)</h6>
                                        <p>${{ Cart::tax() }}</p>
                                    </div>
                                </div>
                                <div class="ttl-amt py-2 px-3 d-flex justify-content-between align-items-center">
                                    <h6>Total</h6>
                                    <h3 class="text-primary font-weight-700">${{ Cart::total() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {

        $("#pay").inputmask("decimal", {
            radixPoint: ".",
            groupSeparator: ",",
            digits: 2,
            autoGroup: true,
            rightAlign: false,


        });

        $('#formStoreOrder').on('submit', function(e) {
            e.preventDefault();

            let payInput = $('#pay');
            let payValue = payInput.val().replace(/,/g, '').trim(); // Remove commas and extra spaces

            if (payValue === "") {
                payInput.val(0); // If empty, set it to 0
            } else {
                payInput.val(parseFloat(payValue)); // Convert to float before sending
            }
            console.log(payInput.val());

            let formData = $(this).serialize(); // Serialize after conversion
            $.ajax({
                url: "{{ route('pos.storeOrder') }}", // Laravel route helper
                method: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure CSRF token is set
                },
                success: function(response) {
                    alert('Order successfully saved!');
                    if (response.success) {
                        alert(response.message);
                        window.location.href = response.redirect; // Redirect if necessary
                    } else {
                        alert('Something went wrong.');
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    if (errors) {
                        let errorMessage = '';
                        $.each(errors, function(key, value) {
                            errorMessage += value + '\n';
                        });
                        alert(errorMessage);
                    } else {
                        alert('An error occurred. Please try again later.');
                    }
                }
            });
        });


    });
</script>
@endsection