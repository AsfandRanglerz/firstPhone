@extends('admin.layout.app')
@section('title', 'Create Brand')
@section('content')
    <!-- Main Content -->
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <form action="{{ route('brands.store') }}" method="POST">
                    @csrf
                    <a href="{{ url('/admin/brands') }}" class="btn mb-3" style="background: #009245;">Back</a>
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card ">
                                <div class="card-header">
                                    <h4>Create Brand</h4>
                                </div>
                                <div style="display: flex;">
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="title">Name <span style="color: red;">*</span></label>
                                                <input type="text" class="form-control" id="title" name="name"
                                                    value="{{ old('title') }}" placeholder="Enter title" required
                                                    autofocus>
                                                @error('title')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="slug">Slug <span style="color: red;">*</span></label>
                                                <input type="text" class="form-control" id="slug" name="slug"
                                                    value="{{ old('slug') }}" placeholder="slug" readonly>
                                                @error('slug')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                        </div>
                                    </div>
                                </div>


                                <div class="card-footer text-center">
                                    <button type="submit" class="btn btn-primary mr-1" type="submit">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

@endsection
@section('js')
    <!-- Slug Script -->
    <script>
        $(document).ready(function() {
            // Slugify Function
            function slugify(text) {
                return text.toString().toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '')
                    .replace(/\-\-+/g, '-')
                    .replace(/^-+/, '')
                    .replace(/-+$/, '');
            }

            // Auto-fill slug
            $('#title').on('input', function() {
                const title = $(this).val();
                $('#slug').val(slugify(title));
            });

        });
    </script>
@endsection
