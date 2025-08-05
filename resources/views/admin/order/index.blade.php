@extends('admin.layout.app')
@section('title', 'Orders')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Orders</h4>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                {{-- @if (Auth::guard('admin')->check() || ($sideMenuPermissions->has('Vendors') && $sideMenuPermissions['Vendors']->contains('create')))
                                    <a class="btn btn-primary mb-3 text-white"
                                        href="{{ url('/admin/vendor-create') }}">Create</a>
                                @endif --}}

                                {{-- @if (Auth::guard('admin')->check() || ($sideMenuPermissions->has('users') && $sideMenuPermissions['users']->contains('view')))
                                    <a class="btn btn-primary mb-3 text-white" href="{{ url('admin/users/trashed') }}">View
                                        Trashed</a>
                                @endif --}}

                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Products</th>
                                            <th>Payment Status</th>
                                            <th>Delivery Method</th>
                                            <th>Order Status</th>
                                            <th>Delivery Tracking</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                     <tbody>
        <!-- Loop through orders -->
        <tr>
            <td>1</td>
            <td>#ORD12345</td>
            <td>Ali Raza<br><small>ali@email.com</small></td>
            <td>
                • iPhone 14 Pro (1)<br>
                • AirPods Pro (2)
            </td>
            <td>
                <span class="badge bg-success">Paid</span>
            </td>
            <td>Cash on Delivery</td>
            <td>
                <select class="form-select form-select-sm">
                    <option value="confirmed">Confirmed</option>
                    <option value="in_progress">In Progress</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </td>
            <td>
                <a href="#" class="btn btn-info btn-sm">Track</a>
            </td>
            <td>
                <button class="btn btn-primary btn-sm">View</button>
                <button class="btn btn-danger btn-sm">Cancel</button>
                <button class="btn btn-warning btn-sm">Refund</button>
            </td>
        </tr>
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

            // Toggle status
            let currentToggle = null;
            let currentUserId = null;

            $('.toggle-status').change(function() {
                let status = $(this).is(':checked') ? 1 : 0;
                currentToggle = $(this);
                currentUserId = $(this).data('id');

                if (status === 0) {
                    $('#deactivatingUserId').val(currentUserId);
                    $('#deactivationModal').modal('show');
                } else {
                    updateUserStatus(currentUserId, 1);
                }
            });

            $('#confirmDeactivation').click(function() {
                let reason = $('#deactivationReason').val();
                if (reason.trim() === '') {
                    toastr.error('Please provide a deactivation reason');
                    return;
                }

                updateUserStatus(currentUserId, 0, reason);
            });

            $('#deactivationModal').on('hidden.bs.modal', function() {
                if ($('#deactivationReason').val().trim() === '') {
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                }
            });

            function updateUserStatus(userId, status, reason = null) {
                let $descriptionSpan = currentToggle.siblings('.custom-switch-description');
                $.ajax({
                    url: "{{ route('vendor.toggle-status') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: userId,
                        status: status,
                        reason: reason
                    },
                    success: function(res) {
                        if (res.success) {
                            $descriptionSpan.text(res.new_status);
                            toastr.success(res.message);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            currentToggle.prop('checked', !status);
                            toastr.error(res.message);
                        }
                    },
                    error: function() {
                        currentToggle.prop('checked', !status);
                        toastr.error('Error updating status');
                    }
                });
            }
        });
    </script>
@endsection
