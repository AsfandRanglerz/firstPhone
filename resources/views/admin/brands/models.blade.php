@extends('admin.layout.app')
@section('title', 'Brand Models')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Brand Models</h4>
                            </div>

                            <div class="card-body table-striped table-bordered table-responsive">
                                <button class="btn mb-3" style="background-color: #009245;"
                                    id="openCreateModal">Create</button>

                                <table class="table" id="brandsTable">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($models as $model)
                                            <tr id="brand-row-{{ $model->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="brand-name">
                                                    {{ is_array($model->name) ? implode(', ', $model->name) : $model->name }}
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <button class="btn btn-primary editBrand"
                                                            data-id="{{ $model->id }}"
                                                            data-name="{{ is_array($model->name) ? implode(', ', $model->name) : $model->name }}">
                                                            <i class="fa fa-edit"></i>
                                                        </button>

                                                        <button class="btn deleteBrand" style="background-color: #009245;"
                                                            data-id="{{ $model->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
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

    <!-- Create Modal -->
    <div class="modal fade" id="brandModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="brandForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Brand Models</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div id="inputWrapper">
                            <div class="brand-input-set mb-3" data-index="0">
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name[]" class="form-control name-input">
                                    <div class="text-danger error-message" data-error-for="name.0"></div>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm removeBtn"
                                    style="display:none;">Delete</button>
                                <hr>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" style="background-color: #009245;"
                            id="addMoreBtn">Add More</button>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form id="editForm">
                @csrf
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Brand Model</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="edit_name">
                            <div class="text-danger error-message" id="edit_name_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
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

            // Create Modal
            $('#openCreateModal').click(function() {
                $('#brandForm')[0].reset();
                $('#inputWrapper').html(getBrandInputSet());
                $('#brandModal').modal('show');
            });

            // Add more inputs
            $('#addMoreBtn').click(function() {
                let index = $('.brand-input-set').length;
                $('#inputWrapper').append(getBrandInputSet(index, true));
            });

            // Remove input set
            $(document).on('click', '.removeBtn', function() {
                $(this).closest('.brand-input-set').remove();
                $('.brand-input-set').each(function(index) {
                    $(this).attr('data-index', index);
                    $(this).find('.error-message').attr('data-error-for', `name.${index}`);
                });
            });

            // Create form submission
            $('#brandForm').submit(function(e) {
                e.preventDefault();
                $('.error-message').text('');

                $.ajax({
                    url: "{{ route('brands.model.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.data && Array.isArray(response.data)) {
                            response.data.forEach(function(model) {
                                addBrandToTable(model, table);
                            });
                        }
                        toastr.success('Brand models created successfully!');
                        $('#brandModal').modal('hide');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                if (field.startsWith('name.')) {
                                    let index = field.split('.')[1];
                                    $(`[data-error-for="name.${index}"]`).text(errors[field][
                                        0
                                    ]);
                                }
                            }
                        } else {
                            toastr.error(xhr.responseJSON.message || 'Something went wrong.');
                        }
                    }
                });
            });

            // Edit modal open
            $(document).on('click', '.editBrand', function() {
                $('#edit_id').val($(this).data('id'));
                $('#edit_name').val($(this).data('name'));
                $('#edit_name_error').text('');
                $('#editModal').modal('show');
            });

            // Edit form submission
            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                $('#edit_name_error').text('');

                $.ajax({
                    url: "{{ route('brands.model.update', ':id') }}".replace(':id', id),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        updateBrandInTable(response.data);
                        toastr.success('Brand model updated successfully!');
                        $('#editModal').modal('hide');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                $(`#edit_${field}_error`).text(errors[field][0]);
                            }
                        } else {
                            toastr.error(xhr.responseJSON.message || 'Something went wrong.');
                        }
                    }
                });
            });

            // Delete brand
            $(document).on('click', '.deleteBrand', function() {
                let id = $(this).data('id');
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this brand model!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: "{{ route('brands.model.delete', ':id') }}".replace(':id',
                                id),
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function() {
                                table.row($(`#brand-row-${id}`)).remove().draw();
                                toastr.success('Brand model deleted successfully!');
                            },
                            error: function() {
                                toastr.error('Delete failed.');
                            }
                        });
                    }
                });
            });

            // Add new brand to table
            function addBrandToTable(model, table) {
                let newRow = table.row.add([
                    table.rows().count() + 1,
                    model.name,
                    `<div class="d-flex gap-1">
                        <button class="btn btn-primary editBrand"
                            data-id="${model.id}" 
                            data-name="${model.name}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn deleteBrand" style="background-color: #009245;"
                            data-id="${model.id}">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>`
                ]).draw(false).node();

                $(newRow).attr('id', `brand-row-${model.id}`);
                $(newRow).find('td').eq(1).addClass('brand-name');
            }

            // Update brand in table
            function updateBrandInTable(model) {
                let row = $(`#brand-row-${model.id}`);
                row.find('.brand-name').text(model.name);
                row.find('.editBrand').data('name', model.name);
            }

            // Brand input set HTML
            function getBrandInputSet(index = 0, showRemove = false) {
                return `
                    <div class="brand-input-set mb-3" data-index="${index}">
                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name[]" class="form-control name-input">
                            <div class="text-danger error-message" data-error-for="name.${index}"></div>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm removeBtn" ${showRemove ? '' : 'style="display:none;"'}>Delete</button>
                        <hr>
                    </div>`;
            }
        });
    </script>
@endsection
