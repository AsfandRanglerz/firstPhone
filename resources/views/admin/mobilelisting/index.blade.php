@extends('admin.layout.app')
@section('title', 'Mobile Listings')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Mobile Listings</h4>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                {{-- @if (Auth::guard('admin')->check() ||
                                        ($sideMenuPermissions->has('MobileListing') && $sideMenuPermissions['MobileListing']->contains('create')))
                                    <a class="btn btn-primary mb-3 text-white"
                                        href="{{ url('/admin/vendor-create') }}">Create</a>
                                @endif --}}

                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Storage </th>
                                            <th>RAM </th>
                                            <th>Price</th>
                                            <th>Condition</th>
                                            <th>About</th>
                                            <th>Status</th>
                                            <th>Image/Video</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($mobiles as $mobile)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $mobile->brand }}</td>
                                                <td>{{ $mobile->model }}</td>
                                                <td>{{ $mobile->storage }}</td>
                                                <td>{{ $mobile->ram }}</td>
                                                <td>{{ $mobile->price }}</td>
                                                <td>{{ $mobile->condition }}</td>
                                                <td>{{ $mobile->about }}</td>
                                               <td>
                                                    
                                                    @if ($mobile->status == 0)
                                                        <div class="badge badge-success badge-shadow">Approved</div>
                                                    @elseif($mobile->status == 1)
                                                        <div class="badge badge-danger badge-shadow">Rejected</div>
                                                    @elseif($mobile->status == 2)
                                                        <div class="badge badge-warning badge-shadow">Pending</div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($mobile->image)
                                                        <img src="{{ asset( $mobile->image) }}"
                                                            alt="Mobile Image" style="width: 100px; height: auto;">
                                                    @else
                                                        No Image
                                                    @endif
                                                <td>
                                                    <div class="d-flex">
                                                     <div class="gap-1" style="display: flex; align-items: center; justify-content: center; column-gap: 4px">
                                                    <a href="javascript:void(0);" 
                                                    onclick="showActivationModal({{ $mobile->id }})"
                                                    class="btn btn-success {{ $mobile->status == 0 ? 'disabled' : '' }}">
                                                    Approve
                                                    </a>

                                                    <a href="javascript:void(0);" 
                                                    onclick="showDeactivationModal({{ $mobile->id }})"
                                                    class="btn btn-danger {{ $mobile->status == 1 ? 'disabled' : '' }}">
                                                    Reject
                                                    </a>
                                                    {{-- @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('MobileListing') && $sideMenuPermissions['MobileListing']->contains('edit')))
                                                        <a href="{{ route('mobile.edit', $mobile->id) }}"
                                                            class="btn btn-primary me-2"
                                                            style="float: left; margin-left: 10px;">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif --}}

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


    <!-- Deactivation Reason Modal -->
    <div class="modal fade" id="deactivationModal" tabindex="-1" role="dialog" aria-labelledby="deactivationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deactivationModalLabel">Reason for Rejection</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="deactivationForm" method="POST">
                        @csrf
                        <input type="hidden" name="user_id" id="deactivatingUserId">
                        <div class="form-group">
                            <label for="deactivationReason">Please specify the reason for rejection:</label>
                            <textarea class="form-control" id="deactivationReason" name="reason" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmDeactivation">Submit</button>
                </div>
            </div>
        </div>
    </div>

     <div class="modal fade" id="activationModal" tabindex="-1" role="dialog" aria-labelledby="activationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                 <form id="activationForm" method="POST">
                    @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="activationModalLabel">Approve this mobile listing?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmactivation">Approve</button>
                </div>
                 </form>
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
             $('#confirmactivation').on('click', function () {
             $('#activationForm').submit();
    });
             $('#confirmDeactivation').on('click', function () {
             $('#deactivationForm').submit();
    });

        });

         function showDeactivationModal(managerId) {
            $('#deactivationForm').attr('action', '{{ url('admin/mobilelistingDeactivate') }}/' + managerId);
            $('#deactivationModal').modal('show');
        }

        function showActivationModal(managerId) {
            $('#activationForm').attr('action', '{{ url('admin/mobilelistingActivate') }}/' + managerId);
            $('#activationModal').modal('show');
        }
    </script>
@endsection
