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
                                {{-- @if (Auth::guard('admin')->check() || ($sideMenuPermissions->has('MobileListing') && $sideMenuPermissions['MobileListing']->contains('create')))
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
                                            <th>Images/Videos</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($mobiles as $mobile)
                                            <tr>

                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $mobile->brand->name }}</td>
                                                <td>{{ $mobile->model->name }}</td>
                                                <td>{{ $mobile->storage }}</td>
                                                <td>{{ $mobile->ram }}</td>
                                                <td>{{ $mobile->price }}</td>
                                                <td>{{ $mobile->condition }}</td>
                                                <td>{{ $mobile->about }}</td>
                                                <td>
                                                    @php
                                                        $status = (int) $mobile->status;
                                                        $statusText = match ($status) {
                                                            0 => 'Approved',
                                                            1 => 'Rejected',
                                                            2 => 'Pending',
                                                            default => 'Unknown',
                                                        };

                                                        $buttonClass = match ($status) {
                                                            0 => 'btn btn-primary',
                                                            1 => 'btn-danger',
                                                            2 => 'btn-warning',
                                                            default => 'btn-secondary',
                                                        };
                                                    @endphp

                                                    <div class="dropdown">
                                                        <button class="btn btn-sm dropdown-toggle {{ $buttonClass }}"
                                                            type="button" data-toggle="dropdown">
                                                            {{ $statusText }}
                                                        </button>

                                                        <div class="dropdown-menu">
                                                            @if ($status == 0)
                                                                {{-- Show only Reject --}}
                                                                <form method="POST"
                                                                    action="{{ route('mobilelisting.reject', $mobile->id) }}">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="dropdown-item text-danger">Reject</button>
                                                                </form>
                                                            @elseif ($status == 1)
                                                                {{-- Show only Approve --}}
                                                                <form method="POST"
                                                                    action="{{ route('mobilelisting.approve', $mobile->id) }}">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="dropdown-item text-success">Approve</button>
                                                                </form>
                                                            @elseif ($status == 2)
                                                                {{-- Show both Approve and Reject --}}
                                                                <form method="POST"
                                                                    action="{{ route('mobilelisting.approve', $mobile->id) }}">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="dropdown-item text-success">Approve</button>
                                                                </form>
                                                                <form method="POST"
                                                                    action="{{ route('mobilelisting.reject', $mobile->id) }}">
                                                                    @csrf
                                                                    <button type="submit"
                                                                        class="dropdown-item text-danger">Reject</button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a class="btn btn-primary ml-3"
                                                        href="
                                                    {{ route('mobile.show', $mobile->id) }}
                                                     ">View</a>
                                                </td>
                                                <td>
                                                    <div class="d-flex">
                                                        <div class="gap-1"
                                                            style="display: flex; align-items: center; justify-content: center; column-gap: 4px">

                                                            {{-- @if (Auth::guard('admin')->check() || ($sideMenuPermissions->has('MobileListing') && $sideMenuPermissions['MobileListing']->contains('edit')))
                                                        <a href="{{ route('mobile.edit', $mobile->id) }}"
                                                            class="btn btn-primary me-2"
                                                            style="float: left; margin-left: 10px;">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif --}}

                                                            @if (Auth::guard('admin')->check() ||
                                                                    ($sideMenuPermissions->has('MobileListing') && $sideMenuPermissions['MobileListing']->contains('delete')))
                                                                <form id="delete-form-{{ $mobile->id }}"
                                                                    action="{{ route('mobile.delete', $mobile->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button class="show_confirm btn d-flex gap-1"
                                                                        style="background-color: #009245;"
                                                                        data-form="delete-form-{{ $mobile->id }}"
                                                                        type="button">
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
