@extends('admin.layout.app')
@section('title', 'Mobile Requests')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Mobile Requests</h4>
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
                                            <th>Customer Name</th>
                                            <th>Location</th> {{--  --}}
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Min Price (PKR)</th>
                                            <th>Max Price (PKR)</th>
                                            <th>Storage</th>
                                            <th>RAM</th>
                                            <th>Color</th>
                                            <th>Condition</th>
                                            <th>Description</th>
                                            <th>Vendors</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($mobilerequests as $mobilerequest)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $mobilerequest->name }}</td>
                                                <td>{{ $mobilerequest->location }}</td>
                                                <td>{{ $mobilerequest->brand->name }}</td>
                                                <td>{{ $mobilerequest->model->name}}</td>
                                                <td>
                                                @if($mobilerequest->min_price)
                                                    {{ number_format($mobilerequest->min_price, 0) }}
                                                @else
                                                <span class="text-muted">No Price</span>
                                                @endif
                                                </td>
                                                <td>
                                                @if($mobilerequest->max_price)
                                                    {{ number_format($mobilerequest->max_price, 0) }}
                                                @else
                                                <span class="text-muted">No Price</span>
                                                @endif
                                                </td>
                                                <td>{{ $mobilerequest->storage }}</td>
                                                <td>{{ $mobilerequest->ram }}</td>
                                                <td>{{ $mobilerequest->color }}</td>
                                                <td>{{ $mobilerequest->condition }}</td>
                                                <td>
                                                @if($mobilerequest->description)
                                                    {{ Str::limit($mobilerequest->description, 50) }}
                                                @else
                                                <span class="text-muted">No Description</span>
                                                @endif
                                                </td>
                                                 <td>
                                                    <a class="btn btn-primary ml-1" href="
                                                    {{ route('mobilerequest.show', $mobilerequest->id) }}
                                                     ">View</a>
                                                </td>
                                               <td>
                                                    
                                                    @if ($mobilerequest->status == 0)
                                                        <div class="badge badge-success badge-shadow">Notified</div>
                                                    @elseif($mobilerequest->status == 2)
                                                        <div class="badge badge-warning badge-shadow">Pending</div>
                                                    @endif
                                                </td>
                                              
                                                <td>

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
