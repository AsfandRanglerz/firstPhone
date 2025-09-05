@extends('admin.layout.app')
@section('title', 'Vendors')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Vendors</h4>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                @if (Auth::guard('admin')->check() ||
                                        ($sideMenuPermissions->has('Vendors') && $sideMenuPermissions['Vendors']->contains('create')))
                                    <a class="btn btn-primary mb-3 text-white"
                                        href="{{ url('/admin/vendor-create') }}">Create</a>
                                @endif

                                {{-- @if (Auth::guard('admin')->check() || ($sideMenuPermissions->has('users') && $sideMenuPermissions['users']->contains('view')))
                                    <a class="btn btn-primary mb-3 text-white" href="{{ url('admin/users/trashed') }}">View
                                        Trashed</a>
                                @endif --}}

                                <table class="table responsive" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>CNIC Front</th>
                                            <th>CNIC Back</th>
                                            <th>Shop Images</th>
                                            <th>Profile Image</th>
                                            <th>Toggle</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>
                                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                                </td>
                                                <td>{{ $user->phone }}</td>
                                                <td>
                                                    @if ($user->cnic_front)
                                                        <button class="btn btn-sm btn-info view-cnic"
                                                            data-front="{{ asset($user->cnic_front) }}"
                                                             title="View CNIC">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">No CNIC</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($user->cnic_back)
                                                        <button class="btn btn-sm btn-info view-cnic-back"
                                                            data-back="{{ asset($user->cnic_back) }}" title="View CNIC">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">No CNIC Back</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($user->images && $user->images->count() > 0)
                                                        <button class="btn btn-sm btn-info view-shop-images"
                                                            data-images='@json($user->images->pluck('image'))' title="View CNIC">
                                                            <i class="fa fa-eye"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">No Shop Images</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if ($user->image)
                                                        <img src="{{ asset($user->image) }}" alt="CNIC Back Image"
                                                            style="width: 50px; height: 50px;">
                                                    @else
                                                        <span class="text-muted">No Image</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <label class="custom-switch">
                                                        <input type="checkbox" class="custom-switch-input toggle-status"
                                                            data-id="{{ $user->id }}"
                                                            {{ $user->toggle ? 'checked' : '' }}>
                                                        <span class="custom-switch-indicator"></span>
                                                        <span class="custom-switch-description">
                                                            {{ $user->toggle ? 'Activated' : 'Deactivated' }}
                                                        </span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-0">
                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Vendors') && $sideMenuPermissions['Vendors']->contains('edit')))
                                                            <a href="{{ route('vendor.edit', $user->id) }}"
                                                                class="btn btn-primary me-2"
                                                                style="float: left; margin-left: 10px;">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        @endif

                                                        @if (Auth::guard('admin')->check() ||
                                                                ($sideMenuPermissions->has('Vendors') && $sideMenuPermissions['Vendors']->contains('delete')))
                                                            <form id="delete-form-{{ $user->id }}"
                                                                action="{{ route('vendor.delete', $user->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>

                                                            <button class="show_confirm btn d-flex gap-4"
                                                                style="background-color: #009245;"
                                                                data-form="delete-form-{{ $user->id }}" type="button">
                                                                <span><i class="fa fa-trash"></i></span>
                                                            </button>
                                                        @endif
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
                    <h5 class="modal-title" id="deactivationModalLabel">Deactivation Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="deactivationForm">
                        @csrf
                        <input type="hidden" name="user_id" id="deactivatingUserId">
                        <div class="form-group">
                            <label for="deactivationReason">Please specify the reason for deactivation:</label>
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

    <div class="modal fade" id="cnicModal" tabindex="-1" aria-labelledby="cnicModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cnicModalLabel">CNIC Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="cnicFront" src="" class="img-fluid mb-3" alt="CNIC Front">
                    <img id="cnicBack" src="" class="img-fluid" alt="CNIC Back">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shopImagesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Shop Images</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="shopCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" id="shopImagesContainer"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#shopCarousel"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#shopCarousel"
                            data-bs-slide="next">
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
            // ===== DataTable Initialization =====
            if ($.fn.DataTable.isDataTable('#table_id_events')) {
                $('#table_id_events').DataTable().destroy();
            }
            $('#table_id_events').DataTable();

            // ===== SweetAlert2 Delete Confirmation =====
            $('.show_confirm').click(function(event) {
    event.preventDefault();
    var formId = $(this).data("form");
    var form = document.getElementById(formId);

    swal({
        title: "Are you sure?",
        text: "If you delete this, it will be gone forever.",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then(function(willDelete) {
        if (willDelete) {
            form.submit();
        }else{
            console.error('Deletion cancelled');
        }
    });
});


            // ===== Toggle Status =====
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

                $('#deactivationModal').modal('hide');
                $('#deactivationReason').val('');
                updateUserStatus(currentUserId, 0, reason);
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

            // ===== CNIC Front Modal =====
            $('.view-cnic').on('click', function() {
                let front = $(this).data('front');
                $('#cnicBack').hide();
                $('#cnicFront').attr('src', front).show();
                $('#cnicModal').modal('show');
            });

            // ===== CNIC Back Modal =====
            $('.view-cnic-back').on('click', function() {
                let back = $(this).data('back');
                $('#cnicFront').hide();
                $('#cnicBack').attr('src', back).show();
                $('#cnicModal').modal('show');
            });

            // ===== Shop Images Modal =====
            $('.view-shop-images').on('click', function() {
                let images = $(this).data('images');
                let container = $('#shopImagesContainer');
                container.empty();

                images.forEach((img, i) => {
                    container.append(`
                    <div class="carousel-item ${i === 0 ? 'active' : ''}">
                        <img src="{{ asset('') }}${img}" class="d-block w-100" style="max-height:500px; object-fit:contain;">
                    </div>
                `);
                });

                $('#shopImagesModal').modal('show');
            });
        });
    </script>
@endsection
