@extends('admin.layout.app')
@section('title', "{$brand->name} - Models")

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-body">
                <a class="btn btn-primary mb-3" href="{{ url('admin/brands') }}">Back</a>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ $brand->name }} - Models</h4>
                            </div>
                            <div class="card-body table-striped table-bordered table-responsive">
                                {{-- Create Button --}}
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
                        <h5 class="modal-title">Create Brands</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="brand_id" value="{{ $brand->id }}" placeholder="Enter model name">
                        <div id="inputWrapper"></div>
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
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="edit_name"
                                placeholder="Enter brand model name">
                            <div class="text-danger error-message" id="edit_name_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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

            function getBrandInputSet(index = null, showRemove = false) {
                return `
            <div class="brand-input-set mb-3" data-index="${index}">
                <div class="form-group">
                    <label>Name <span class="text-danger">*</span></label>
                    <input type="text" name="name[]" class="form-control name-input" placeholder="Enter model name">
                    <div class="text-danger error-message" data-error-for="name.${index ?? 0}"></div>
                </div>
                <button type="button" class="btn btn-danger btn-sm removeBtn" ${showRemove ? '' : 'style="display:none;"'}>Delete</button>
            </div>`;
            }

            // Open Create Modal
            $('#openCreateModal').click(function() {
                $('#brandForm')[0].reset();
                $('#inputWrapper').html(getBrandInputSet(0, false));
                $('#brandModal').modal('show');
            });

            // Add More Inputs
            $('#addMoreBtn').click(function() {
                let index = $('#inputWrapper .brand-input-set').length;
                $('#inputWrapper').append(getBrandInputSet(index, true));
            });

            // Remove Input Field
            $(document).on('click', '.removeBtn', function() {
                $(this).closest('.brand-input-set').remove();
            });

            // Auto-clear error on focus
            $(document).on('focus', '.name-input', function() {
                $(this).next('.error-message').text('');
            });

            // Create Form Submission
            $('#brandForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('brands.model.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.data && Array.isArray(response.data)) {
                            response.data.forEach(function(model) {
                                addBrandToTable(model);
                            });
                        }
                        $('#brandModal').modal('hide');
                        toastr.success('Model Created Successfully');
                        $('#brandForm')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            // Clear all previous errors first
                            $('.error-message').text('');

                            for (let field in errors) {
                                // Get the original error message
                                let originalMsg = errors[field][0];

                                // Customize the message to remove index numbers
                                let customMsg = originalMsg
                                    .replace(/\.\d+/g, '') // Remove all .0, .1, etc.
                                    .replace('name field', 'Name field') // Capitalize if needed
                                    .replace('The Name field is required',
                                        'This name field is required'); // Your custom message

                                // Find the corresponding error container and display the message
                                $(`.error-message[data-error-for="${field}"]`).text(customMsg);

                                // Set up click handler to clear error when field is focused
                                $(`[name="${field.replace('.', '[').replace('.', ']')}"]`).off(
                                        'focus.clearError')
                                    .on('focus.clearError', function() {
                                        $(`.error-message[data-error-for="${field}"]`).text(
                                            '');
                                    });
                            }
                        } else {
                            toastr.error('Something went wrong.');
                        }
                    }
                });
            });

            // Edit Modal Open
            // Edit button click
            $(document).on('click', '.editBrand', function() {
                $('#edit_id').val($(this).data('id'));
                $('#edit_name').val($(this).data('name'));
                $('#edit_name_error').text('');
                $('#editModal').modal('show');
            });

            // Hide error when clicking back on input
            $(document).on('focus', '#edit_name', function() {
                $('#edit_name_error').text('');
            });


            // Edit Form Submit
            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                $.ajax({
                    url: "{{ route('brands.model.update', ':id') }}".replace(':id', id),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        updateBrandInTable(response.data);
                        toastr.success('Model Updated Successfully');
                        $('#editModal').modal('hide');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $('#edit_name_error').text(xhr.responseJSON.errors.name ? xhr
                                .responseJSON.errors.name[0] : '');
                        } else {
                            toastr.error('Something went wrong.');
                        }
                    }
                });
            });

            // Delete Brand
            $(document).on('click', '.deleteBrand', function() {
                let id = $(this).data('id');
                swal({
                    title: "Are you sure you want to delete this record?",
                    text: "If you delete this, it will be gone forever.",
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
                                toastr.success('Model Deleted Successfully');
                            },
                            error: function() {
                                toastr.error('Delete failed.');
                            }
                        });
                    }
                });
            });

            function addBrandToTable(model) {
                let newRow = table.row.add([
                    table.rows().count() + 1,
                    Array.isArray(model.name) ? model.name.join(', ') : model.name,
                    `<div class="d-flex gap-1">
                <button class="btn btn-primary editBrand"
                    data-id="${model.id}"
                    data-name="${Array.isArray(model.name) ? model.name.join(', ') : model.name}">
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

            function updateBrandInTable(model) {
                let row = $(`#brand-row-${model.id}`);
                row.find('.brand-name').text(Array.isArray(model.name) ? model.name.join(', ') : model.name);
                row.find('.editBrand').data('name', model.name);
            }
        });
    </script>
@endsection
