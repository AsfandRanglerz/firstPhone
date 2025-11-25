@extends('admin.layout.app')
@section('title', 'Create Vendor')
@section('content')

    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ route('vendor.index') }}">Back</a>

                <form id="edit_vendor" action="{{ route('vendor.create') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-md-12 col-lg-12">
                            <div class="card">
                                <h4 class="text-center my-4">Create Vendor</h4>
                                <div class="row mx-0 px-4">

                                    <!-- Name -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="name">Name <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}"
                                                placeholder="Enter name" autofocus>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="email">Email <span style="color: red;">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                id="email" name="email" value="{{ old('email') }}"
                                                placeholder="example@gmail.com" autofocus>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="phone">Phone <span style="color: red;">*</span></label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                                id="phone" name="phone" value="{{ old('phone') }}"
                                                placeholder="Enter phone" autofocus>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Password Field -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group position-relative">
                                            <label for="password">Password <span style="color: red;">*</span></label>
                                            <input type="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password"
                                                name="password" placeholder="Password">

                                            <span class="fa fa-eye position-absolute toggle-password"
                                                style="top: 42px; right: 15px; cursor: pointer;"></span>
                                        </div>
                                    </div>

                                    <!-- Location -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="location">Location <span style="color: red;">*</span></label>
                                            <input type="text"
                                                class="form-control @error('location') is-invalid @enderror" id="location"
                                                name="location" value="{{ old('location') }}" placeholder="Enter location"
                                                autofocus>
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- Hidden Latitude & Longitude -->
                                    <input type="hidden" id="latitude" name="latitude">
                                    <input type="hidden" id="longitude" name="longitude">

                                    <!-- CNIC Front -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="cnic_front">CNIC Front <span style="color: red;">*</span></label>
                                            <input type="file"
                                                class="form-control @error('cnic_front') is-invalid @enderror"
                                                id="cnic_front" name="cnic_front" accept="image/*">
                                            <small class="text-muted">(Upload CNIC front image, max 2MB)</small>
                                            @error('cnic_front')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- CNIC Back -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="cnic_back">CNIC Back <span style="color: red;">*</span></label>
                                            <input type="file"
                                                class="form-control @error('cnic_back') is-invalid @enderror" id="cnic_back"
                                                name="cnic_back" accept="image/*">
                                            <small class="text-muted">(Upload CNIC back image, max 2MB)</small>
                                            @error('cnic_back')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Shop Images -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="shop_images">Shop Images <span style="color: red;">*</span></label>
                                            <input type="file"
                                                class="form-control @error('shop_images') is-invalid @enderror"
                                                id="shop_images" name="shop_images[]" accept="image/*" multiple>
                                            <small class="text-muted">(Max 5 images, each up to 2MB)</small>
                                            @error('shop_images')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Image Upload -->
                                    <div class="col-sm-6 pl-sm-0 pr-sm-3">
                                        <div class="form-group">
                                            <label for="image">Profile Image</label>
                                            <input type="file"
                                                class="form-control @error('image') is-invalid @enderror" id="image"
                                                name="image" accept="image/*">
                                            <small class="text-muted">(Image should be of size 2MB)</small>
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Service Repair Checkbox -->
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="has_repairing"
                                                    name="has_repairing" value="1">
                                                <label class="form-check-label" for="has_repairing"
                                                    style="cursor: pointer;">Repairing Service</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="card-footer text-center row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 btn-bg">Save</button>
                                    </div>
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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBuqZO_NrSp3c7lSpGdI3tpVV8h7UdLMFM&libraries=places"></script>

    @if (session('success'))
        <script>
            toastr.success('{{ session('success') }}');
        </script>
    @endif

<script>
    $(document).ready(function () {

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
                    alert("No details found for this location. Try again.");
                    return;
                }

                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();
            });
        }

        initAutocomplete();

        // 🔐 Password toggle
        $('.toggle-password').on('click', function () {
            const $passwordInput = $('#password');
            const $icon = $(this);

            if ($passwordInput.attr('type') === 'password') {
                $passwordInput.attr('type', 'text');
                $icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $passwordInput.attr('type', 'password');
                $icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // ✅ Auto hide validation error on focus
        $('input, select, textarea').on('focus', function () {
            const $feedback = $(this).parent().find('.invalid-feedback');
            if ($feedback.length) {
                $feedback.hide();
                $(this).removeClass('is-invalid');
            }
        });

    });
</script>

@endsection
