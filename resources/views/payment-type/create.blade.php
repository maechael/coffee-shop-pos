@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Create Category</h4>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('payment_type.store') }}" method="POST">
                        @csrf
                        <!-- begin: Input Data -->
                        <div class=" row align-items-center">
                            <div class="form-group col-md-12">
                                <label for="name">Payment Type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <!-- end: Input Data -->
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary mr-2">Save</button>
                            <a class="btn bg-danger" href="{{ route('payment_type.index') }}">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

<!-- <script>
    // Slug Generator
    const title = document.querySelector("#name");
    const slug = document.querySelector("#slug");
    title.addEventListener("keyup", function() {
        let preslug = title.value;
        preslug = preslug.replace(/ /g, "-");
        slug.value = preslug.toLowerCase();
    });
</script> -->

@include('components.preview-img-form')
@endsection