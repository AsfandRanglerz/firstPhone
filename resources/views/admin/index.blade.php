@extends('admin.layout.app')
@section('title', 'Dashboard')
@section('content')

<style>
    .banner-img img {
        width: 100%;
        height: 150px;
        object-fit: contain;
    }
</style>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="row mb-3">

            <!-- Total Vendors -->
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                            <div class="row">
                                <div class="col-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Total Vendors</h5>
                                        <h2 class="mb-3 font-18">{{ $totalVendors }}</h2>
                                    </div>
                                </div>
                                <div class="col-6 pl-0">
                                    <div class="banner-img">
                                        <img src="{{ asset('public/admin/assets/img/banner/vendors.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                            <div class="row">
                                <div class="col-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Total Customers</h5>
                                        <h2 class="mb-3 font-18">{{ $totalCustomers }}</h2>
                                    </div>
                                </div>
                                <div class="col-6 pl-0">
                                    <div class="banner-img">
                                        <img src="{{ asset('public/admin/assets/img/banner/customers.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Status -->
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                            <div class="row">
                                <div class="col-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Orders Status</h5>
                                        <h2 class="mb-3 font-18">
                                            Active: {{ $activeOrders }}
                                        </h2>
                                        <p class="mb-0">
                                            <span class="col-green">Pending: {{ $pendingOrders }}</span> |
                                            <span class="col-red">Cancelled: {{ $cancelledOrders }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-6 pl-0">
                                    <div class="banner-img">
                                        <img src="{{ asset('public/admin/assets/img/banner/orders.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Example Extra Card (Optional: Total Customers) -->
            {{-- 
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="card">
                    <div class="card-statistic-4">
                        <div class="align-items-center justify-content-between">
                            <div class="row">
                                <div class="col-6 pr-0 pt-3">
                                    <div class="card-content">
                                        <h5 class="font-15">Total Customers</h5>
                                        <h2 class="mb-3 font-18">{{ $totalCustomers }}</h2>
                                    </div>
                                </div>
                                <div class="col-6 pl-0">
                                    <div class="banner-img">
                                        <img src="{{ asset('public/admin/assets/img/banner/customers.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            --}}

        </div>
    </section>
</div>
@endsection
