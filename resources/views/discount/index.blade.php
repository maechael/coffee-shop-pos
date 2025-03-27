@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
            <div class="alert text-white bg-success" role="alert">
                <div class="iq-alert-text">{{ session('success') }}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            @endif
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-3">Discount</h4>
                    <p class="mb-0">Let's you setup a different kind of discount. </p>
                </div>
                <div>
                    <a href="{{ route('discount.create') }}" class="btn btn-primary add-list"><i class="fas fa-plus mr-3"></i>Add Discount</a>
                    <a href="{{ route('discount.index') }}" class="btn btn-danger add-list"><i class="fa-solid fa-trash mr-3"></i>Clear Search</a>
                </div>
                <div class="col-lg-12">
                    <form action="{{ route('discount.index') }}" method="get">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="form-group row">
                                <label for="row" class="col-sm-3 align-self-center">Row:</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="row">
                                        <option value="10" @if(request('row')=='10' )selected="selected" @endif>10</option>
                                        <option value="25" @if(request('row')=='25' )selected="selected" @endif>25</option>
                                        <option value="50" @if(request('row')=='50' )selected="selected" @endif>50</option>
                                        <option value="100" @if(request('row')=='100' )selected="selected" @endif>100</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="control-label col-sm-3 align-self-center" for="search">Search:</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" id="search" class="form-control" name="search" placeholder="Search category" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text bg-primary"><i class="fa-solid fa-magnifying-glass font-size-20"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-12">
                    <div class="table-responsive rounded mb-3">
                        <table class="table mb-0">
                            <thead class="bg-white text-uppercase">
                                <tr class="ligth ligth-data">
                                    <th>No.</th>
                                    <th>@sortablelink('name')</th>
                                    <th>Discount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="ligth-body">
                                @forelse ($discounts as $discount)
                                <tr>
                                    <td>{{$discount->id}}</td>
                                    <td>{{ $discount->name }}</td>
                                    <td>{{ $discount->discount }}%</td>
                                    <td>
                                        <div class="d-flex align-items-center list-action">
                                            <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"
                                                href="{{ route('discount.edit', $discount->id) }}"><i class=" ri-pencil-line mr-0"></i>
                                            </a>
                                            <form action="{{ route('discount.destroy', $discount->id) }}" method="POST" style="margin-bottom: 5px">
                                                @method('delete')
                                                @csrf
                                                <button type="submit" class="badge bg-warning mr-2 border-none" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="ri-delete-bin-line mr-0"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                @empty
                                <div class="alert text-white bg-danger" role="alert">
                                    <div class="iq-alert-text">Data not Found.</div>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $discounts->links() }}

                </div>


            </div>
        </div>


    </div>
</div>
</div>
@endsection