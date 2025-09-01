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
                                            <th>User Type</th>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Storage </th>
                                            <th>Price (PKR)</th>
                                            <th>Condition</th>
                                            <th>Color</th>
                                            <th>RAM </th>
                                            <th>Processor</th>
                                            <th>Display</th>
                                            <th>Charging</th>
                                            <th>Refresh Rate</th>
                                            <th>Main Camera</th>
                                            <th>Ultra Wide Camera</th>
                                            <th>Telephoto Camera</th>
                                            <th>Front Camera</th>
                                            <th>Build</th>
                                            <th>Wireless</th>
                                            <th>Stock</th>
                                            <th>PTA Approved</th>
                                            <th>AI Features</th>
                                            <th>Battery Health (Hours)</th>
                                            <th>OS Version</th>
                                            <th>Warranty Start</th>
                                            <th>Warranty End</th>
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
                                                <td>
                                                    @if($mobile->customer)
                                                        <span>Customer</span>
                                                    @elseif($mobile->vendor)
                                                        <span>Vendor</span>
                                                    @else
                                                        <span class="text-muted">No User</span>
                                                    @endif
                                                </td>

                                                <td>
                                                @if($mobile->brand->name)
                                                    {{ $mobile->brand->name }}
                                                @else
                                                 <span class="text-muted">No Brand</span>
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->model->name)
                                                    {{ $mobile->model->name }}
                                                @else
                                                 <span class="text-muted">No Model</span>
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->storage)
                                                    {{ $mobile->storage }}
                                                @else
                                                 <span class="text-muted">No Storage</span>
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->price )
                                                    {{ $mobile->price }}
                                                @else
                                                 <span class="text-muted">No Price</span>
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->condition)
                                                    {{ $mobile->condition }}
                                                @else
                                                 <span class="text-muted">No Condition</span>
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->color)
                                                    {{ $mobile->color }}
                                                @else
                                                 <span class="text-muted">No Color</span>
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->ram)
                                                    {{ $mobile->ram }}
                                                @else
                                                 <span class="text-muted">No RAM</span>
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->processor)
                                                    {{ $mobile->processor }}
                                                @else
                                                 <span class="text-muted">No Processor</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->display)
                                                    {{ $mobile->display }}
                                                @else
                                                 <span class="text-muted">No Display</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->charging) 
                                                    {{ $mobile->charging }}
                                                @else
                                                 <span class="text-muted">No Charging</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->refresh_rate)
                                                    {{ $mobile->refresh_rate }}
                                                @else
                                                 <span class="text-muted">No Refresh Rate</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->main_camera)
                                                    {{ $mobile->main_camera }}
                                                @else
                                                 <span class="text-muted">No Main Camera</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->ultra_camera)
                                                    {{ $mobile->ultra_camera }}
                                                @else
                                                 <span class="text-muted">No Ultra Wide Camera</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->telephoto_camera)
                                                    {{ $mobile->telephoto_camera }}
                                                @else
                                                 <span class="text-muted">No TelePhoto Camera</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->front_camera)
                                                    {{ $mobile->front_camera }}
                                                @else
                                                 <span class="text-muted">No Front Camera</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->build)
                                                    {{ $mobile->build }}
                                                @else
                                                 <span class="text-muted">No Build</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->wireless)
                                                    {{ $mobile->wireless }}
                                                @else
                                                 <span class="text-muted">No Wireless</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->stock)
                                                    {{ $mobile->stock }}
                                                @else
                                                 <span class="text-muted">No Stock</span>   
                                                @endif
                                                </td>
                                                <td>{{ $mobile->pta_approved == 0 ? 'Approved' : 'Not Approved' }}</td>
                                                <td>
                                                @if($mobile->ai_features)
                                                    {{ $mobile->ai_features }}
                                                @else
                                                 <span class="text-muted">No AI Features</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->battery_health)
                                                    {{ $mobile->battery_health }}
                                                @else
                                                 <span class="text-muted">No Battery Health</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->os_version)
                                                    {{ $mobile->os_version }}
                                                @else
                                                 <span class="text-muted">No OS Version</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->warranty_start)
                                                    {{ $mobile->warranty_start }}
                                                @else
                                                 <span class="text-muted">No Warranty Start Date</span>   
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobile->warranty_end)
                                                    {{ $mobile->warranty_end }}
                                                @else
                                                 <span class="text-muted">No Warranty End Date</span>   
                                                @endif
                                                </td>
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
