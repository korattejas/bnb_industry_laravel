@extends('admin.layouts.app')
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Add Product</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.product.index') }}">Products</a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        <a href="#">Add Product</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <section class="horizontal-wizard">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="edit_value" value="0">
                                        <input type="hidden" id="form-method" value="add">

                                        <div class="row row-sm">
                                            <!-- Category -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select name="category_id" class="form-control select2"
                                                        id="category_id">
                                                        <option value="">Select Category</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Sub Category -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Sub Category</label>
                                                    <select name="sub_category_id" class="form-control select2"
                                                        id="sub_category_id">
                                                        <option value="">Select Sub Category</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Name -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input type="text" class="form-control" name="name"
                                                        placeholder="Product Name" required>
                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Price</label>
                                                    <input type="text" class="form-control" name="price"
                                                        placeholder="Price" required>
                                                </div>
                                            </div>

                                            <!-- Discount Price -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Discount Price</label>
                                                    <input type="text" class="form-control"
                                                        name="discount_price" placeholder="Discount Price">
                                                </div>
                                            </div>



                                            <!-- Description -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" name="description" rows="4" placeholder="Product Description" required></textarea>
                                                </div>
                                            </div>

                                            <!-- Includes -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Includes (comma separated)</label>
                                                    <textarea class="form-control" name="includes" rows="3" placeholder="e.g. Haircut, Coloring, Styling"></textarea>
                                                </div>
                                            </div>

                                            <!-- Images -->
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Images</label>
                                                    <input type="file" class="form-control filepond" name="images[]" multiple data-allow-reorder="true">
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="1" selected>Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Priority -->
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Priority Status</label>
                                                    <select name="is_popular" class="form-control" required>
                                                        <option value="1">High Priority</option>
                                                        <option value="0" selected>Low Priority</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Submit -->
                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 justify-content-end"
                                                    style="text-align: right;">
                                                    <button type="submit"
                                                        class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                    <a href="{{ route('admin.product.index') }}"
                                                        class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
                                                </div>
                                            </div>

                                        </div> <!-- row end -->
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        var form_url = 'product/store';
        var redirect_url = 'product';
        var is_one_image_and_multiple_image_status = 'is_multiple_image';

        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '100%'
        });

        $('#category_id').on('change', function() {
            var categoryId = $(this).val();

            $('#sub_category_id').empty().append('<option value="">Select Sub Category</option>');

            if (categoryId) {
                $.ajax({
                    url: 'get-subcategories/' + categoryId,
                    type: 'GET',
                    success: function(data) {
                        $.each(data, function(key, subCategory) {
                            $('#sub_category_id').append('<option value="' + subCategory.id +
                                '">' + subCategory.name + '</option>');
                        });

                        $('#sub_category_id').trigger('change');
                    }
                });
            }
        });
    </script>
@endsection
