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
                    <h4 class="mb-3">Raw Material</h4>
                    <p class="mb-0">A Raw Material dashboard lets you easily gather and visualize raw material data to optimize
                        inventory management, streamline procurement, and ensure production efficiency. </p>
                </div>
                <div>
                    <a href="{{ route('raw-material.create') }}" class="btn btn-primary add-list"><i class="fas fa-plus mr-3"></i>Create Category</a>
                    <a href="{{ route('raw-material.index') }}" class="btn btn-danger add-list"><i class="fa-solid fa-trash mr-3"></i>Clear Search</a>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="table mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th>No.</th>
                            <th>Name</th>
                            <th>Quantity</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @forelse($rawMaterials as $index => $rawMaterial)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $rawMaterial->name }}</td>
                            <td>{{ $rawMaterial->quantity }}</td>
                            <td>
                                <div class="d-flex align-items-center list-action">
                                    <a class="badge bg-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"
                                        href="{{ route('raw-material.edit', $rawMaterial) }}"><i class="ri-pencil-line mr-0"></i>
                                    </a>
                                    <form action="{{ route('raw-material.destroy', $rawMaterial) }}" method="POST" style="margin-bottom: 5px">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="badge bg-warning mr-2 border-none" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" data-placement="top" title="" data-original-title="Delete"><i class="ri-delete-bin-line mr-0"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>

                            <td colspan="4" class="text-center">No raw materials found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection