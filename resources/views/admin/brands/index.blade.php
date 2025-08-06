    @extends('admin.layout.app')
    @section('title', 'Brands')

    @section('content')
        <div class="main-content">
            <section class="section">
                <div class="section-body">

                    <div class="row">
                        <div class="col-12">
                            <div class="card">

                                <div class="card-header">
                                    <h4>Brands</h4>
                                </div>

                                <div class="card-body table-striped table-bordered table-responsive">

                                    @if (Auth::guard('admin')->check() ||
                                            ($sideMenuPermissions->has('Brands') && $sideMenuPermissions['Brands']->contains('create')))
                                        <button class="btn mb-3" style="background-color: #009245;"
                                            id="openCreateModal">Create</button>
                                    @endif

                                    <table class="table" id="brandsTable">
                                        <thead>
                                            <tr>
                                                <th>Sr.</th>
                                                <th>Name</th>
                                                <td>Models</td>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($brands as $brand)
                                                <tr id="brand-row-{{ $brand->id }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td class="brand-name">{{ $brand->name }}</td>
                                                    <td>
                                                        <a href="{{ route('brands.model.view') }}"
                                                            style="background-color: #009245;" class="btn">
                                                            <span class="fa fa-eye"></span>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            @if (Auth::guard('admin')->check() ||
                                                                    ($sideMenuPermissions->has('Brands') && $sideMenuPermissions['Brands']->contains('edit')))
                                                                <button class="btn btn-primary editBrand"
                                                                    data-id="{{ $brand->id }}"
                                                                    data-name="{{ $brand->name }}"
                                                                    data-slug="{{ $brand->slug }}">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                            @endif

                                                            @if (Auth::guard('admin')->check() ||
                                                                    ($sideMenuPermissions->has('Brands') && $sideMenuPermissions['Brands']->contains('delete')))
                                                                <button class="btn deleteBrand"
                                                                    style="background-color: #009245;"
                                                                    data-id="{{ $brand->id }}">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        </div>
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

        <!-- Create/Edit Brand Modal -->
        <div class="modal fade" id="brandModal" tabindex="-1" role="dialog" aria-labelledby="brandModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="brandForm">
                    @csrf
                    <input type="hidden" id="brand-id" name="id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Create Brand</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label for="brand-name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="brand-name" name="name" required>
                                <div class="text-danger" id="name-error"></div>
                            </div>

                            <div class="form-group">
                                <label for="brand-slug">Slug</label>
                                <input type="text" class="form-control" id="brand-slug" name="slug" readonly>
                                <div class="text-danger" id="slug-error"></div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endsection

    @section('js')
        <script>
            $(document).ready(function() {
                let table = $('#brandsTable').DataTable();

                // Open create modal
                $('#openCreateModal').click(function() {
                    $('#brandForm')[0].reset();
                    $('#brand-id').val('');
                    $('#name-error').text('');
                    $('#slug-error').text('');
                    $('.modal-title').text('Create Brand');
                    $('#brandModal').modal('show');
                });

                // Auto-generate slug
                $('#brand-name').on('input', function() {
                    let slug = $(this).val().toLowerCase().trim()
                        .replace(/\s+/g, '-')
                        .replace(/[^\w\-]+/g, '');
                    $('#brand-slug').val(slug);
                });

                // Edit brand
                $(document).on('click', '.editBrand', function() {
                    $('#brandForm')[0].reset();
                    $('#name-error').text('');
                    $('#slug-error').text('');

                    let id = $(this).data('id');
                    $('#brand-id').val(id);
                    $('#brand-name').val($(this).data('name'));
                    $('#brand-slug').val($(this).data('slug'));

                    $('.modal-title').text('Edit Brand');
                    $('#brandModal').modal('show');
                });

                // Save brand (Create/Update)
                $('#brandForm').submit(function(e) {
                    e.preventDefault();

                    $('#name-error').text('');
                    $('#slug-error').text('');

                    let id = $('#brand-id').val();
                    let url = id ?
                        "{{ route('brands.update', ':id') }}".replace(':id', id) :
                        "{{ route('brands.store') }}";

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: $(this).serialize(),
                        success: function(res) {
                            if (id) {
                                // Update row
                                let row = $(`#brand-row-${id}`);
                                row.find('.brand-name').text(res.data.name);
                                toastr.success('Brand updated successfully');
                            } else {
                                // Add new row
                                let newRow = table.row.add([
                                    table.rows().count() + 1, // Sr.
                                    res.data.name, // Name
                                    `<a href="{{ route('brands.model.view') }}" style="background-color: #009245;" class="btn">
                        <span class="fa fa-eye"></span>
                    </a>`, // Models column
                                    `<div class="d-flex gap-1">
                        <button class="btn btn-primary editBrand" 
                            data-id="${res.data.id}" 
                            data-name="${res.data.name}" 
                            data-slug="${res.data.slug}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger deleteBrand" data-id="${res.data.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>` // Actions column
                                ]).draw(false).node();

                                $(newRow).attr('id', `brand-row-${res.data.id}`);
                                $(newRow).find('td').eq(1).addClass('brand-name');
                                toastr.success('Brand created successfully');
                            }

                            $('#brandModal').modal('hide');
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                if (errors.name) $('#name-error').text(errors.name[0]);
                                if (errors.slug) $('#slug-error').text(errors.slug[0]);
                            } else {
                                toastr.error('Something went wrong.');
                            }
                        }
                    });
                });

                // Delete brand
                $(document).on('click', '.deleteBrand', function() {
                    let id = $(this).data('id');
                    swal({
                        title: "Are you sure you want to delete this record?",
                        text: "If you delete this Brand Recored, it will be gone forever.",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{ route('brands.delete', ':id') }}".replace(':id', id),
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function() {
                                    table.row($(`#brand-row-${id}`)).remove().draw(false);
                                    toastr.success('Brand deleted successfully');
                                },
                                error: function() {
                                    toastr.error('Something went wrong.');
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endsection
