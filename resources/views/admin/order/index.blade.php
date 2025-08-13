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
                                {{-- Totals Row Inside Card Header but Below Title --}}
                                <div class="row mt-3 w-100">
                                    <div class="col-md-4">
                                        <div class="card shadow border-0 text-white mb-0">
                                            <div class="card-body py-2">
                                                <h6 class="mb-1">Total COD</h6>
                                                <h6 class="mb-0 fw-bold">Rs {{ number_format($codTotal) }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card shadow border-0 text-white mb-0">
                                            <div class="card-body py-2">
                                                <h6 class="mb-1">Total Online</h6>
                                                <h6 class="mb-0 fw-bold">Rs {{ number_format($onlineTotal) }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card shadow border-0 text-white mb-0">
                                            <div class="card-body py-2">
                                                <h6 class="mb-1">Total Pickup</h6>
                                                <h6 class="mb-0 fw-bold">Rs {{ number_format($pickupTotal) }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                <table class="table" id="table_id_events">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Shipping Address</th>
                                            <th>Buy From</th>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Payment Status</th>
                                            <th>Delivery Method</th>
                                            <th>Order Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $index => $order)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>#{{ $order->order_number }}</td>
                                                <td>
                                                    {{ $order->customer->name ?? 'N/A' }}<br>
                                                    <small>{{ $order->customer->email ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    {{ $order->shipping_address ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @foreach ($order->items as $item)
                                                        {{ $item->vendor->name ?? 'No Vendor' }}<br>
                                                        <small><a href="mailto:{{ $item->vendor->email }}">{{ $item->vendor->email }}</a></small><br>
                                                        <small>{{ $item->vendor->phone ?? 'N/A' }}</small>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($order->items as $item)
                                                        {{ $item->product->brand->name ?? 'No Brand' }}
                                                        {{ $item->product->model->name ?? 'No Model' }}<br> (Qty:
                                                        {{ $item->quantity }}, Price:
                                                        {{ number_format($item->price, 2) }})
                                                        <br>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($order->items as $item)
                                                        {{ number_format($item->price * $item->quantity) }}
                                                    @endforeach
                                                </td>

                                                <td>
                                                    @php
                                                        $paymentClass = match ($order->payment_status) {
                                                            'paid' => 'bg-primary',
                                                            'pending' => 'bg-warning',
                                                            'failed' => 'bg-danger',
                                                            'refunded' => 'bg-secondary',
                                                            default => 'bg-light',
                                                        };
                                                    @endphp

                                                    <span class="badge {{ $paymentClass }}">
                                                        {{ ucfirst($order->payment_status) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    @php
                                                        $deliveryClass = match ($order->delivery_method) {
                                                            'cod' => 'bg-warning',
                                                            'online' => 'bg-primary',
                                                            'pickup' => 'bg-info',
                                                            default => 'bg-secondary',
                                                        };
                                                    @endphp

                                                    <span class="badge {{ $deliveryClass }}">
                                                        {{ ucfirst($order->delivery_method) }}
                                                    </span>
                                                </td>

                                                @php
                                                    $statusColors = [
                                                        'pending' => 'btn-warning',
                                                        'confirmed' => 'btn-info',
                                                        'in_progress' => 'btn-primary',
                                                        'shipped' => 'btn-secondary',
                                                        'delivered' => 'btn-success',
                                                        'cancelled' => 'btn-danger',
                                                    ];
                                                @endphp

                                                <td>
                                                    {{-- @if ($order->order_status === 'delivered' || $order->order_status === 'cancelled')
                                                        <button
                                                            class="btn btn-sm {{ $statusColors[$order->order_status] ?? 'btn-light' }}"
                                                            type="button">
                                                            {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                                                        </button>
                                                    @else --}}
                                                    <div class="dropdown">
                                                        <button
                                                            class="btn btn-sm dropdown-toggle {{ $statusColors[$order->order_status] ?? 'btn-light' }}"
                                                            type="button" data-toggle="dropdown"
                                                            id="statusBtn-{{ $order->id }}">
                                                            {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @foreach ($statuses as $status)
                                                                @if ($status !== $order->order_status)
                                                                    <button type="button"
                                                                        class="dropdown-item change-order-status"
                                                                        data-order-id="{{ $order->id }}"
                                                                        data-new-status="{{ $status }}">
                                                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                                    </button>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    {{-- @endif --}}
                                                </td>

                                                <td>
                                                    @if (Auth::guard('admin')->check() ||
                                                            ($sideMenuPermissions->has('Orders') && $sideMenuPermissions['Orders']->contains('delete')))
                                                        <form id="delete-form-{{ $order->id }}"
                                                            action="{{ route('order.destroy', $order->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>

                                                        <button class="show_confirm btn" style="background-color: #009245;"
                                                            data-form="delete-form-{{ $order->id }}" type="button">
                                                            <span><i class="fa fa-trash"></i></span>
                                                        </button>
                                                    @endif
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
    <script>
        $(document).ready(function() {
            // Order status change via jQuery AJAX
            $('.change-order-status').on('click', function() {
                let orderId = $(this).data('order-id');
                let newStatus = $(this).data('new-status');

                $.ajax({
                    url: "{{ route('order.updateStatus', ':id') }}".replace(':id', orderId),
                    type: 'POST',
                    data: {
                        order_status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.success) {
                            // Update button text & color
                            const statusText = newStatus.replace(/_/g, ' ').replace(/\b\w/g,
                                c => c.toUpperCase());
                            const button = $(`#statusBtn-${orderId}`);

                            const colorClasses = {
                                'pending': 'btn-warning',
                                'confirmed': 'btn-info',
                                'in_progress': 'btn-primary',
                                'shipped': 'btn-secondary',
                                'delivered': 'btn-success',
                                'cancelled': 'btn-danger',
                            };

                            button
                                .text(statusText)
                                .removeClass()
                                .addClass(
                                    `btn btn-sm dropdown-toggle ${colorClasses[newStatus]}`);

                            toastr.success(data.message);
                        } else {
                            toastr.error('Something went wrong');
                        }
                    },
                    error: function() {
                        toastr.error('Failed to update status');
                    }
                });
            });
        });
    </script>



@endsection
