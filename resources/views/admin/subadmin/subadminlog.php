@extends('admin.layout.app')

@section('title', 'Rankings')

@section('content')
@php
$type = $type ?? 'overall';
$selectedMonth = $selectedMonth ?? now()->month;
@endphp

<div class="main-content" style="min-height: 562px;">
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center pb-0">


                            <!-- <div class="d-flex flex-wrap align-items-center gap-2">
                                <label class="me-2 mb-0 fw-bold">Filter by:</label>

                                <select class="form-select form-select-sm w-auto" id="rankingTypeSelect">
                                    <option value="overall" {{ $type == 'overall' ? 'selected' : '' }}>All</option>
                                    <option value="year" {{ $type == 'year' ? 'selected' : '' }}>This Year</option>
                                    <option value="month" {{ $type == 'month' ? 'selected' : '' }}>Select Month
                                    </option>
                                </select>

                                <select class="form-select form-select-sm w-auto {{ $type != 'month' ? 'd-none' : '' }}"
                                    id="monthSelect">
                                    @for ($i = 1; $i <= 12; $i++) <option value="{{ $i }}"
                                        {{ (int) $selectedMonth === $i ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($i)->format('F') }}
                                        </option>
                                        @endfor
                                </select>
                            </div> -->
                        </div>

                        <div class="card-body table-striped table-bordered table-responsive">
                            <!-- @if ($rankings->isEmpty())
                            <div class="alert alert-info m-4">
                                No ranking data available for this {{ $type }}.
                            </div>
                            @else -->
                            <table class="table responsive" id="table_id_rankings">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;"></th>
                                        <th>User Name</th>
                                        <th>Install (Products)</th>
                                        <th>Earned Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rankings as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->products_count }}</td>
                                        <td>{{ $user->points }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('js')
<script type="text/javascript">
$(document).ready(function() {
    $('#table_id_rankings').DataTable({
        responsive: true,
        pageLength: 10,
        ordering: true,
        autoWidth: false
    });

    function applyFilter() {
        const type = $('#rankingTypeSelect').val();
        let url = "{{ route('ranking.index') }}?type=" + type;

        if (type === 'month') {
            const month = $('#monthSelect').val();
            url += '&month=' + month;
        }

        window.location.href = url;
    }

    $('#rankingTypeSelect').on('change', function() {
        if ($(this).val() === 'month') {
            $('#monthSelect').removeClass('d-none');
        } else {
            $('#monthSelect').addClass('d-none');
        }
        applyFilter();
    });

    $('#monthSelect').on('change', applyFilter);


    const titleElement = $('.card-header h4');
    let html = titleElement.html();
    html = html.replace(/\s+\(/g, ' (');
    titleElement.html(html);


    if ("{{ $type }}" === 'year') {
        const currentYear = new Date().getFullYear();
        let updated = titleElement.html().replace(/Current Year/i, currentYear);
        titleElement.html(updated);
    }
});
</script>

<style>
/* Reduce spacing between "Users Rankings - year" and month */
.card-header h4 span.text-primary {
    margin-right: 4px;
}
</style>
@endsection