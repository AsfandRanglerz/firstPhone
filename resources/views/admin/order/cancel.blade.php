@extends('admin.layout.app')
@section('title', 'Cancel Orders')

@section('content')
    <div class="main-content" style="min-height: 562px;">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Cancel Orders</h4>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                <table class="table responsive" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Order Number</th>
                                            <th>Order Item</th>
                                            <th>Vendor</th>
                                            <th>Reason</th>
                                            <th>Delivery Method</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($cancelOrders as $index => $cancelOrder)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>#{{ $cancelOrder->order->order_number ?? '-' }}</td>
                                                <td>
                                                    {{ $cancelOrder->orderItem->product->brand->name ?? '-' }} -
                                                    {{ $cancelOrder->orderItem->product->model->name ?? '-' }}
                                                </td>
                                                <td>{{ $cancelOrder->orderItem->vendor->name ?? '-' }}</td>
                                                <td>{{ $cancelOrder->reason }}</td>
                                                <td>
                                                    @if ($cancelOrder->order->delivery_method == 'cod')
                                                        <span class="badge bg-warning">COD</span>
                                                    @elseif ($cancelOrder->order->delivery_method == 'online')
                                                        <span class="badge bg-primary">Online</span>
                                                    @elseif ($cancelOrder->order->delivery_method == 'pickup')
                                                        <span class="badge bg-info">GoShop</span>
                                                    @else
                                                        <span
                                                            class="badge badge-secondary">{{ ucfirst($cancelOrder->order->delivery_method) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'requested' => 'btn-warning',
                                                            'approved' => 'btn-success',
                                                            'rejected' => 'btn-danger',
                                                        ];
                                                    @endphp

                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-sm dropdown-toggle {{ $statusColors[$cancelOrder->status] ?? 'btn-light' }}"
                                                            type="button" id="cancelStatusBtn-{{ $cancelOrder->id }}"
                                                            data-toggle="dropdown">
                                                            {{ ucfirst($cancelOrder->status) }}
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @foreach (['requested', 'approved', 'rejected'] as $status)
                                                                @if ($status !== $cancelOrder->status)
                                                                    <button type="button"
                                                                        class="dropdown-item change-cancel-status"
                                                                        data-id="{{ $cancelOrder->id }}"
                                                                        data-new-status="{{ $status }}">
                                                                        {{ ucfirst($status) }}
                                                                    </button>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <form id="delete-form-{{ $cancelOrder->id }}"
                                                        action="{{ route('cancel-orders.destroy', $cancelOrder->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>

                                                    <button class="show_confirm btn" style="background-color: #009245;"
                                                        data-form="delete-form-{{ $cancelOrder->id }}" type="button">
                                                        <span><i class="fa fa-trash"></i></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Modal for Approve Proof Upload --}}
    <div class="modal fade" id="approveFileModal" tabindex="-1" role="dialog" aria-labelledby="approveFileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="approveFileForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="cancel_order_id" id="cancel_order_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveFileModalLabel">Upload Proof File</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="proof_file_image" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#table_id_events')) {
                $('#table_id_events').DataTable().destroy();
            }
            $('#table_id_events').DataTable();

            // Delete confirmation
            $(document).on('click', '.show_confirm', function(event) {
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

            // Cancel Order Status Change
            $('.change-cancel-status').on('click', function() {
                let id = $(this).data('id');
                let newStatus = $(this).data('new-status');

                if (newStatus === 'approved') {
                    $.ajax({
                        url: "{{ route('cancel-orders.checkDeliveryStatus', ':id') }}".replace(
                            ':id', id),
                        type: 'GET',
                        success: function(res) {
                            if (res.delivery_method === 'online') {
                                $('#cancel_order_id').val(id);
                                $('#approveFileModal').modal('show');
                            } else if (res.delivery_method === 'approved_direct') {
                                toastr.success("Cancel order approved successfully!");
                                location.reload();
                            }
                        },
                        error: function() {
                            toastr.error("Failed to check delivery status");
                        }
                    });
                } else {
                    updateCancelStatus(id, newStatus);
                }
            });

            // Approve file form submit
            $('#approveFileForm').on('submit', function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                let id = $('#cancel_order_id').val();

                $.ajax({
                    url: "{{ route('cancel-orders.updateStatus', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        if (data.success) {
                            $('#approveFileModal').modal('hide');
                            toastr.success("Cancel order approved with proof!");
                            location.reload();
                        } else {
                            toastr.error("Something went wrong");
                        }
                    },
                    error: function() {
                        toastr.error("Failed to approve cancel order");
                    }
                });
            });

            // Helper function for status update without file
            function updateCancelStatus(id, newStatus) {
                $.ajax({
                    url: "{{ route('cancel-orders.updateStatus', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            location.reload();
                        } else {
                            toastr.error('Something went wrong');
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        toastr.error('Failed to update status');
                    }
                });
            }

        });
    </script>
@endsection
