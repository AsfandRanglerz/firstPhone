@extends('admin.layout.app')
@section('title', 'Edit Vendor')
@section('content')

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ url('admin/vendor') }}">Back</a>

                <form id="edit_vendor" action="{{ route('vendor.update', $user->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card">
                                <h4 class="text-center my-4">Edit Vendor</h4>
                                <div class="row mx-0 px-4">

                                    <!-- Name -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name', $user->name) }}"
                                                placeholder="Enter name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="email">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email', $user->email) }}"
                                                placeholder="example@gmail.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="phone">Phone <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                                placeholder="Enter phone">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="col-sm-6">
                                        <div class="form-group position-relative">
                                            <label for="password">Password (Optional)</label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                name="password" placeholder="Password">
                                            <span class="fa fa-eye toggle-password position-absolute"
                                                style="top: 42px; right: 15px; cursor: pointer;"></span>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="location">Location <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('location') is-invalid @enderror" id="location"
                                                name="location" value="{{ old('location', $user->location) }}"
                                                placeholder="Enter location">
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Hidden Latitude & Longitude -->
                                    <input type="hidden" id="latitude" 
                                        name="latitude" 
                                        value="{{ old('latitude', $vendor->latitude ?? '') }}">

                                    <input type="hidden" id="longitude" 
                                        name="longitude" 
                                        value="{{ old('longitude', $vendor->longitude ?? '') }}">

                                    <!-- CNIC Front -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="cnic_front">CNIC Front <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="cnic_front" name="cnic_front"
                                                    accept="image/*">
                                                @if ($user->cnic_front)
                                                    <button type="button" class="btn text-white"
                                                        style="background-color: #009245;" data-bs-toggle="modal"
                                                        data-bs-target="#cnicFrontModal">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            <small class="text-muted">(Upload CNIC front image, max 2MB)</small>
                                        </div>
                                    </div>


                                    <!-- CNIC Back -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="cnic_back">CNIC Back <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="file" class="form-control" id="cnic_back" name="cnic_back"
                                                    accept="image/*">
                                                @if ($user->cnic_back)
                                                    <button type="button" class="btn text-white"
                                                        style="background-color: #009245;" data-bs-toggle="modal"
                                                        data-bs-target="#cnicBackModal">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            <small class="text-muted">(Upload CNIC back image, max 2MB)</small>
                                        </div>
                                    </div>

                                    <!-- Shop Images -->
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="shop_images">Shop Images <span
                                                    class="text-danger">*</span></label>

                                            <div class="input-group">
                                                <input type="file"
                                                    class="form-control @error('shop_images') is-invalid @enderror"
                                                    id="shop_images" name="shop_images[]" accept="image/*" multiple>

                                                @if ($user->images && $user->images->count() > 0)
                                                    <button type="button" class="btn text-white"
                                                        style="background-color: #009245;" data-bs-toggle="modal"
                                                        data-bs-target="#shopImagesModal">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <small class="text-muted d-block mt-1">(Upload max 5 images, each up to
                                                2MB)</small>

                                            <!-- Validation Messages OUTSIDE input-group -->
                                            @if ($errors->has('shop_images'))
                                                <div class="invalid-feedback d-block">{{ $errors->first('shop_images') }}
                                                </div>
                                            @endif

                                            @foreach ($errors->get('shop_images.*') as $messages)
                                                @foreach ($messages as $message)
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @endforeach
                                            @endforeach
                                        </div>
                                    </div>




                                    <!-- Image -->
                                    <div class="col-sm-6 d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="form-group">
                                                <label for="image">Profile Image</label>
                                                <input type="file" class="form-control" id="image" name="image"
                                                    accept="image/*">
                                                <small class="text-muted">(Upload image, max 2MB)</small>
                                            </div>
                                        </div>
                                        @if ($user->image)
                                            <div class="ms-3">
                                                <img src="{{ asset($user->image) }}" alt="Profile Image"
                                                    style="width:80px;height:80px;border:1px solid #ddd;">
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Repairing Service Checkbox -->
                                    <div class="col-sm-12">
                                        <div class="form-check mt-3">
                                            <input type="checkbox" class="form-check-input" id="has_repairing"
                                                name="has_repairing" value="1"
                                                {{ old('repair_service', $user->repair_service) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="has_repairing" style="cursor: pointer;">
                                                Repairing Service
                                            </label>
                                        </div>
                                    </div>


                                </div>

                                <!-- Submit -->
                                <div class="card-footer text-center row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <!-- CNIC Front Modal -->
    <div class="modal fade" id="cnicFrontModal" tabindex="-1" aria-labelledby="cnicFrontModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">CNIC Front</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset($user->cnic_front) }}" alt="CNIC Front" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- CNIC Back Modal -->
    <div class="modal fade" id="cnicBackModal" tabindex="-1" aria-labelledby="cnicBackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">CNIC Back</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset($user->cnic_back) }}" alt="CNIC Back" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="shopImagesModal" tabindex="-1" aria-labelledby="shopImagesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shopImagesModalLabel">Shop Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($user->images && $user->images->count() > 0)
                        <div id="shopImagesCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach ($user->images as $index => $image)
                                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                        <img src="{{ asset($image->image) }}" class="d-block w-100"
                                            style="max-height:500px; object-fit:contain;" alt="Shop Image">
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#shopImagesCarousel"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#shopImagesCarousel"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    @else
                        <p>No shop images uploaded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBuqZO_NrSp3c7lSpGdI3tpVV8h7UdLMFM&libraries=places"></script>

    @if (session('message'))
        <script>
            toastr.success('{{ session('message') }}');
        </script>
    @endif

<script>
    $(document).ready(function () {

        // 🔐 Password toggle
        $('.toggle-password').on('click', function () {
            const $password = $('#password');
            if ($password.attr('type') === 'password') {
                $password.attr('type', 'text');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $password.attr('type', 'password');
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // 📍 Google Maps Autocomplete
        function initAutocomplete() {
            let input = document.getElementById('location');
            if (!input) return;

            let autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['geocode'],
                componentRestrictions: { country: "pk" }
            });

            autocomplete.addListener('place_changed', function () {
                let place = autocomplete.getPlace();
 
                if (!place.geometry) {
                    alert("No details found for selected location");
                    return;
                }

                // Set Latitude & Longitude
                $('#latitude').val(place.geometry.location.lat());
                $('#longitude').val(place.geometry.location.lng());
            });
        }

        initAutocomplete();
    });
</script>


@endsection
