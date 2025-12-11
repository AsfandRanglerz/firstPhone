@extends('admin.layout.app')
@section('title', 'Product Images')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ route('vendormobile.index') }}">Back</a>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Product Images</h4>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">

                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Images/Videos</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($mobiles as $mobile)
                                            @if ($mobile && is_object($mobile))
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                  <td>
                                                        @php
                                                            $images = json_decode($mobile->image, true);
                                                        @endphp

                                                            @if(!empty($images))
                                                                {{-- Show only first image in table --}}
                                                                <img src="{{ asset($images[0]) }}" 
                                                                    alt="Mobile Image" 
                                                                    style="width: 50px; height: 50px; cursor: pointer;" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#imageModal" 
                                                                    data-images='@json(array_map("asset", $images))'
                                                                    data-start-index="0">
                                                            @else
                                                                <span class="text-muted">No Image</span>
                                                            @endif
                                                        </td>
                                                </tr>
                                            @endif
                                        @endforeach

                                    </tbody>
                                </table>
                            </div> <!-- /.card-body -->
                        </div> <!-- /.card -->
                    </div> <!-- /.col -->
                </div> <!-- /.row -->
            </div> <!-- /.section-body -->
        </section>
    </div>


<!-- Modal Structure -->
<div id="imageModal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-transparent shadow-none border-0">
            <div class="modal-header border-0">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="imageCarousel" class="carousel slide" data-bs-interval="false">
                    <div class="carousel-inner" id="carouselImages">
                        <!-- Images injected by JS -->
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            if ($.fn.DataTable.isDataTable('#table_id_events')) {
                $('#table_id_events').DataTable().destroy();
            }
            $('#table_id_events').DataTable();

            // SweetAlert2 delete confirmation
            $('.show_confirm').click(function(event) {
                event.preventDefault();
                var formId = $(this).data("form");
                var form = document.getElementById(formId);

                swal({
                   title: "Are you sure you want to delete this record?",
                    text: "If you delete this, it will be gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        form.submit();
                    }
                });
            });
             

        });

        
   document.addEventListener("DOMContentLoaded", function () {
    var imageModal = document.getElementById('imageModal');
    var carouselInner = document.getElementById('carouselImages');

    imageModal.addEventListener('show.bs.modal', function (event) {
        var triggerImg = event.relatedTarget;
        var images = JSON.parse(triggerImg.getAttribute('data-images'));
        var startIndex = parseInt(triggerImg.getAttribute('data-start-index'), 10);

        // Clear previous slides
        carouselInner.innerHTML = '';

        images.forEach((img, index) => {
            var div = document.createElement('div');
            div.classList.add('carousel-item');
            if (index === startIndex) {
                div.classList.add('active');
            }
            div.innerHTML = `<img src="${img}" class="img-fluid" 
             style="max-height:60vh; max-width:80%; object-fit:contain;">`;
            carouselInner.appendChild(div);
        });

        // Force carousel to start at clicked image
      var carousel = new bootstrap.Carousel(document.getElementById('imageCarousel'), {
    interval: false,   // disables auto-slide
    ride: false        // prevents starting automatically
});

        carousel.to(startIndex);
    });
});



    </script>
@endsection
