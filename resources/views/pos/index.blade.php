@extends('dashboard.body.pos-main')

@section('container')

<div class="row">
    <div class="col-8">
        <div class="row mb-3">
            <div class="col-auto">
                <a href="{{ route('pos.index') }}"
                    class="btn {{ request('category') ? 'btn-outline-success' : 'btn-success' }}">
                    All
                </a>
            </div>
            @foreach($categories as $category)
            <div class="col-auto">
                <a href="{{ route('pos.index', ['category' => $category->id]) }}"
                    class="btn {{ request('category') == $category->id ? 'btn-success' : 'btn-outline-success' }}">
                    {{ $category->name }}
                </a>
            </div>
            @endforeach
        </div>
        <div class="row">
            @foreach ($products as $product)
            <div class="col-4 mb-3">
                <form action="{{ route('pos.addCart') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    <input type="hidden" name="name" value="{{ $product->product_name }}">
                    <input type="hidden" name="price" value="{{ $product->selling_price }}">
                    <button type="submit" class="btn btn-outline-success w-100" style="height: 100px;">
                        {{ $product->product_name }}
                    </button>
                </form>
            </div>
            @endforeach
        </div>

    </div>

    <div class="col-4 p-3" style="background-color: #f8f8f8; border-left: 2px solid #ddd; min-height: 100vh;">

        {{-- Order Summary --}}
        <div class="mb-3">
            <h5 class="text-muted mb-1">Order Summary</h5>
            <table class="table table-sm table-borderless">
                <tbody>
                    @foreach ($productItem as $item)
                    <tr
                        class="cart-item"
                        data-rowid="{{ $item->rowId }}"
                        data-name="{{ $item->name }}"
                        data-qty="{{ $item->qty }}"
                        data-discount="{{ $item->options->discount ?? 0 }}"
                        data-note="{{ $item->options->note ?? '' }}"
                        data-price="{{ $item->price }}"

                        style="cursor: pointer;">
                        <td class="text-truncate" style="max-width: 100px;">{{ $item->name }}</td>
                        <td>{{ $item->qty }}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->subtotal }}</td>
                        <td>
                            <a href="{{ route('pos.deleteCart', $item->rowId) }}" class="btn btn-danger btn-sm">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Subtotal Display --}}
        <div class="text-center mb-3">
            <i class="fa-solid fa-mug-hot fa-lg text-muted"></i>
            <h4 class="mt-1">Subtotal <strong class="text-dark">₱{{ Cart::subtotal() }}</strong></h4>
        </div>

        {{-- Change/Discount/Service Tabs (simplified visual) --}}
        <ul class="nav nav-pills justify-content-center mb-3">
            <li class="nav-item">
                <span class="nav-link active">Change</span>
            </li>
            <li class="nav-item">
                <span class="nav-link disabled">Discount</span>
            </li>
            <li class="nav-item">
                <span class="nav-link disabled">Service</span>
            </li>
        </ul>

        {{-- Cash Denominations --}}
        <div class="d-flex flex-wrap gap-2 justify-content-center mb-3">
            @foreach ([20, 50, 100, 200, 500, 1000] as $amount)
            <button class="btn btn-outline-primary btn-lg px-4 py-2 m-1">₱{{ $amount }}</button>
            @endforeach
            <button class="btn btn-outline-primary btn-lg px-4 py-2 m-1">Custom</button>
            <button class="btn btn-outline-primary btn-lg px-4 py-2 m-1">Clear</button>
        </div>

        {{-- Payment Summary --}}
        <div class="row mb-2 text-center">
            <div class="col">
                <p class="h6">Cash Received</p>
                <p class="h5">₱1000</p> {{-- Replace with dynamic value later --}}
            </div>
            <div class="col">
                <p class="h6">Change</p>
                <p class="h5 text-success">₱{{ number_format(1000 - floatval(Cart::subtotal()), 2) }}</p>
            </div>
        </div>

        {{-- Payment Buttons --}}
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
</div>


<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('pos.updateCartModal') }}">
            @csrf
            <input type="hidden" name="rowId" id="modal-rowId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>

                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal-name" class="form-label">Product</label>
                        <input type="text" class="form-control" id="modal-name" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="modal-qty" class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="qty" id="modal-qty" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="modal-discount" class="form-label">Discount (%)</label>
                        <input type="number" class="form-control" name="discount" id="modal-discount" min="0" max="100">
                    </div>
                    <div class="mb-3">
                        <label for="modal-discounted-price" class="form-label">Discounted Price</label>
                        <input type="text" class="form-control" id="modal-discounted-price" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="modal-note" class="form-label">Note</label>
                        <input type="text" class="form-control" name="note" id="modal-note">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(document).on('click', '.cart-item', function() {
            const rowId = $(this).data('rowid');
            const name = $(this).data('name');
            const qty = $(this).data('qty');
            const discount = $(this).data('discount') || 0;
            const note = $(this).data('note') || '';
            const price = $(this).data('price'); // make sure this exists!

            $('#modal-rowId').val(rowId);
            $('#modal-name').val(name);
            $('#modal-qty').val(qty);
            $('#modal-discount').val(discount);
            $('#modal-note').val(note);

            const updateDiscountedPrice = () => {
                const qtyVal = parseInt($('#modal-qty').val()) || 1;
                const discountVal = parseFloat($('#modal-discount').val()) || 0;
                const basePrice = parseFloat(price);

                const total = basePrice * qtyVal;
                const discountedTotal = total - (total * (discountVal / 100));

                $('#modal-discounted-price').val('₱' + discountedTotal.toFixed(2));
            };

            updateDiscountedPrice();

            $('#modal-qty, #modal-discount').on('input', updateDiscountedPrice);

            $('#editItemModal').modal('show');
        });
    });
</script>

@endsection