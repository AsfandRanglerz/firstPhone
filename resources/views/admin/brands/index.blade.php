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
                                <button class="btn mb-3" style="background-color: #009245;"
                                    id="openCreateModal">Create</button>

                                <table class="table" id="brandsTable">
                                    <thead>
                                        <tr>
                                            <th>Sr.</th>
                                            <th>Name</th>
                                            <th>Models</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($brands as $brand)
                                            <tr id="brand-row-{{ $brand->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="brand-name">{{ $brand->name }}</td>
                                                <td>
                                                    <a href="{{ route('brands.model.view', $brand->id) }}" class="btn"
                                                        style="background-color: #009245;">
                                                        <span class="fa fa-eye"></span>
                                                    </a>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <button class="btn btn-primary editBrand"
                                                            data-id="{{ $brand->id }}" data-name="{{ $brand->name }}"
                                                            data-slug="{{ $brand->slug }}">
                                                            <i class="fa fa-edit"></i>
                                                        </button>

                                                        <button class="btn deleteBrand" style="background-color: #009245;"
                                                            data-id="{{ $brand->id }}">
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
                        <div id="inputWrapper">

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
                        <h5 class="modal-title">Edit Brand</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="edit_name"
                                placeholder="Enter model name">
                            <div class="text-danger error-message" id="edit_name_error"></div>
                        </div>
                        <div class="form-group">
                            <label>Slug</label>
                            <input type="text" class="form-control" name="slug" id="edit_slug" readonly>
                            <div class="text-danger error-message" id="edit_slug_error"></div>
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

            // Initialize create modal
            $('#openCreateModal').click(function() {
                $('#brandForm')[0].reset();
                $('#inputWrapper').html(getBrandInputSet());
                $('#brandModal').modal('show');
            });

            // Add more inputs
            $('#addMoreBtn').click(function() {
                $('#inputWrapper').append(getBrandInputSet(null, true));
            });

            // Remove input set
            $(document).on('click', '.removeBtn', function() {
                $(this).closest('.brand-input-set').remove();
            });

            // Auto-generate slug on name input
            $(document).on('input', '.name-input', function() {
                let name = $(this).val();
                let slug = name.toLowerCase().trim()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '');
                $(this).closest('.brand-input-set').find('.slug-input').val(slug);
            });

            // Clear error when clicking on input
            $(document).on('focus', '.name-input', function() {
                $(this).siblings('.error-message').text('');
            });


            // Create form submission
            $('#brandForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('brands.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.data && Array.isArray(response.data)) {
                            response.data.forEach(function(brand) {
                                addBrandToTable(brand, table);
                            });
                        }
                        toastr.success('Brand created successfully');
                        $('#brandModal').modal('hide');
                    },
                    // In your AJAX error handling
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
                            toastr.error(xhr.responseJSON.message || 'Something went wrong.');
                        }
                    }
                });
            });

            // Edit modal open
            $(document).on('click', '.editBrand', function() {
                $('#edit_id').val($(this).data('id'));
                $('#edit_name').val($(this).data('name'));
                $('#edit_slug').val($(this).data('slug'));
                $('#edit_name_error').text('');
                $('#edit_slug_error').text('');
                $('#editModal').modal('show');
            });

            // Auto-update slug in edit modal
            $('#edit_name').on('input', function() {
                let name = $(this).val();
                let slug = name.toLowerCase().trim()
                    .replace(/\s+/g, '-')
                    .replace(/[^\w\-]+/g, '');
                $('#edit_slug').val(slug);
            });

            // Edit form submission
            $('#editForm').submit(function(e) {
                e.preventDefault();
                let id = $('#edit_id').val();
                $.ajax({
                    url: "{{ route('brands.update', ':id') }}".replace(':id', id),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        updateBrandInTable(response.data);
                        toastr.success('Brand updated successfully!');
                        $('#editModal').modal('hide');
                    },
                    // In your AJAX error handling for both create and update
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;

                            // For create modal (array fields)
                            if ($('#brandModal').is(':visible')) {
                                $('.error-message').text(''); // Clear all errors first
                                for (let field in errors) {
                                    if (field.includes('name.')) {
                                        let index = field.split('.')[1];
                                        let input = $(`input[name="name[${index}]"]`);
                                        input.next('.error-message').text(errors[field][0]);

                                        // Add click handler to clear error when focused
                                        input.off('focus.clearError').on('focus.clearError',
                                            function() {
                                                $(this).next('.error-message').text('');
                                            });
                                    }
                                }
                            }

                            // For edit modal (single field)
                            if ($('#editModal').is(':visible')) {
                                $('#edit_name_error').text(''); // Clear previous error
                                for (let field in errors) {
                                    $(`#edit_${field}_error`).text(errors[field][0]);

                                    // Add click handler to clear error when focused
                                    $(`#edit_${field}`).off('focus.clearError').on(
                                        'focus.clearError',
                                        function() {
                                            $(`#edit_${field}_error`).text('');
                                        });
                                }
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
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function() {
                                table.row($(`#brand-row-${id}`)).remove().draw();
                                toastr.success('Brand deleted successfully!');
                            },
                            error: function() {
                                toastr.error('Delete failed.');
                            }
                        });
                    }
                });
            });

            // Helper function to add new brand to table
            function addBrandToTable(brand, table) {
                let newRow = table.row.add([
                    table.rows().count() + 1, // Sr.
                    brand.name, // Name
                    `<a href="${brand.model_view_url}" class="btn" style="background-color: #009245;">
        <span class="fa fa-eye"></span>
    </a>`, // Models Details
                    `<div class="d-flex gap-1">
        <button class="btn btn-primary editBrand"
            data-id="${brand.id}" 
            data-name="${brand.name}"
            data-slug="${brand.slug}">
            <i class="fa fa-edit"></i>
        </button>
        <button class="btn deleteBrand" style="background-color: #009245;"
            data-id="${brand.id}">
            <i class="fa fa-trash"></i>
        </button>
    </div>` // Actions
                ]).draw(false).node();




                $(newRow).attr('id', `brand-row-${brand.id}`);
                $(newRow).find('td').eq(1).addClass('brand-name');
            }

            // Helper function to update brand in table
            function updateBrandInTable(brand) {
                let row = $(`#brand-row-${brand.id}`);
                row.find('.brand-name').text(brand.name);
                row.find('.editBrand')
                    .data('name', brand.name)
                    .data('slug', brand.slug);
            }

            // Helper function to get input set HTML
            function getBrandInputSet(index = null, showRemove = false) {
                let idx = index !== null ? index : $('.brand-input-set').length;
                return `
        <div class="brand-input-set mb-3" data-index="${idx}">
            <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input type="text" name="name[]" class="form-control name-input" placeholder="Enter model name">
                <div class="text-danger error-message" data-error-for="name.${idx}"></div>
            </div>
            <div class="form-group">
                <label>Slug</label>
                <input type="text" name="slug[]" class="form-control slug-input" readonly>
            </div>
            <button type="button" class="btn btn-danger btn-sm removeBtn" ${showRemove ? '' : 'style="display:none;"'}>Delete</button>
        </div>`;
            }

        });
    </script>
@endsection