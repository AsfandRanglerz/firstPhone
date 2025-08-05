@extends('admin.layout.app')
@section('title', 'Product Images')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ route('mobile.index') }}">Back</a>
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
                                            <th>Image/Video</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($mobiles as $mobile)
                                            @if ($mobile && is_object($mobile))
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        @if($mobile->image)
                                                            <img src="{{ asset($mobile->image) }}" alt="Mobile Image" style="width: 50px; height: 50px;">
                                                        @else
                                                            <span class="text-muted">No Image</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex">
                                                            <div class="gap-1" style="display: flex; align-items: center; justify-content: center; column-gap: 4px">
                                                                @if (Auth::guard('admin')->check() ||
                                                                    ($sideMenuPermissions->has('MobileListing') && $sideMenuPermissions['MobileListing']->contains('delete')))
                                                                    <form id="delete-form-{{ $mobile->id }}"
                                                                        action="{{ route('mobile.delete', $mobile->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="show_confirm btn d-flex gap-1"
                                                                            style="background-color: #009245;"
                                                                            data-form="delete-form-{{ $mobile->id }}" type="button">
                                                                            <span><i class="fa fa-trash"></i></span>
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
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

        
    </script>
@endsection
