@extends('admin.layout.app')
@section('title', 'Vendors')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ route('mobilerequest.index') }}">Back</a>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Vendors</h4>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">

                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($mobilerequests as $mobilerequest)
                                            @if ($mobilerequest && is_object($mobilerequest))
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $mobilerequest->name }}</td>
                                                    <td>{{ $mobilerequest->email }}</td>
                                                    <td>{{ $mobilerequest->phone }}</td>
                                                    
                                                    <td>
                                                        <div class="d-flex">
                                                            <div class="gap-1" style="display: flex; align-items: center; justify-content: center; column-gap: 4px">
                                                                @if (Auth::guard('admin')->check() ||
                                                                    ($sideMenuPermissions->has('MobileRequest') && $sideMenuPermissions['MobileRequest']->contains('delete')))
                                                                    <form id="delete-form-{{ $mobilerequest->id }}"
                                                                        action="{{ route('mobilerequest.delete', $mobilerequest->id) }}" method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button class="show_confirm btn d-flex gap-1"
                                                                            style="background-color: #009245;"
                                                                            data-form="delete-form-{{ $mobilerequest->id }}" type="button">
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
