@extends('admin.layouts.app')
@section('header_style_content')
<style>
    .product-images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #fcfdfe;
        border-radius: 16px;
        border: 2px dashed #e2e8f0;
    }

    .image-preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        transition: 0.3s;
    }

    .image-preview-item:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 15px rgba(0,0,0,0.12);
    }

    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .remove-img-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 26px;
        height: 26px;
        background: rgba(220, 38, 38, 0.9);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.1rem;
        transition: 0.2s;
        border: 2px solid #fff;
        backdrop-filter: blur(4px);
    }

    .remove-img-btn:hover {
        background: #b91c1c;
        transform: scale(1.1);
    }
</style>
@endsection
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-start mb-0">Edit Product</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">{{ trans('admin_string.home') }}</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.product.index') }}">Products</a>
                                    </li>
                                    <li class="breadcrumb-item active">Edit Product</li>
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
                                        <input type="hidden" name="edit_value" value="{{ $product->id }}">
                                        <input type="hidden" id="form-method" value="edit">

                                        <div class="row row-sm">

                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select name="category_id" class="form-control select2" id="category_id" required>
                                                        <option value="">Select Category</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                @if($product->category_id == $category->id) selected @endif>
                                                                {{ $category->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Sub Category</label>
                                                    <select name="sub_category_id" class="form-control select2" id="sub_category_id">
                                                        <option value="">Select Sub Category</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>{{ trans('admin_string.name') }}</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ $product->name }}"
                                                        placeholder="{{ trans('admin_string.name') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Price</label>
                                                    <input type="text" class="form-control" name="price"
                                                        value="{{ $product->price }}" placeholder="Price" required>
                                                </div>
                                            </div>

                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Discount Price</label>
                                                    <input type="text" class="form-control"
                                                        name="discount_price" value="{{ $product->discount_price }}"
                                                        placeholder="Discount Price">
                                                </div>
                                            </div>



                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" name="description" rows="4"
                                                        placeholder="{{ trans('admin_string.description') }}">{{ $product->description }}</textarea>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Includes (comma separated)</label>
                                                    <textarea class="form-control" name="includes" rows="3"
                                                        placeholder="Hair wash, Cutting, Styling">
                                                        {{
                                                            is_array($product->includes)
                                                                ? implode(',', $product->includes)
                                                                : ($product->includes
                                                                    ? implode(',', json_decode($product->includes, true))  {{-- string hoy to decode --}}
                                                                    ? implode(',', json_decode($product->includes, true))
                                                                    : '')
                                                        }}
                                                    </textarea>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Gallery Images</label>
                                                    
                                                    @if(isset($product->images) && !empty($product->images))
                                                        <div class="product-images-grid" id="existing-images">
                                                            @foreach($product->images as $img)
                                                                <div class="image-preview-item" id="img-{{ md5($img) }}">
                                                                    <img src="{{ asset('uploads/product/' . $img) }}" alt="Product Image">
                                                                    <span class="remove-img-btn remove-image-ajax" 
                                                                          data-id="{{ $product->id }}" 
                                                                          data-image="{{ $img }}"
                                                                          title="Delete this image">
                                                                        <i class="bi bi-x"></i>
                                                                    </span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <input type="file" class="form-control filepond" name="images[]" multiple data-allow-reorder="true">
                                                </div>
                                            </div>

                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="1" @if($product->status == 1) selected @endif>Active
                                                        </option>
                                                        <option value="0" @if($product->status == 0) selected @endif>Inactive
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Priority</label>
                                                    <select name="is_popular" class="form-control" required>
                                                        <option value="1" @if($product->is_popular == 1) selected @endif>High
                                                            Priority</option>
                                                        <option value="0" @if($product->is_popular == 0) selected @endif>Low
                                                            Priority</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="form-group mb-0 mt-3 justify-content-end"
                                                    style="text-align: right;">
                                                    <div>
                                                        <button type="submit"
                                                            class="btn btn-primary">{{ trans('admin_string.submit') }}</button>
                                                        <a href="{{ route('admin.product.index') }}"
                                                            class="btn btn-secondary">{{ trans('admin_string.cancel') }}</a>
                                                    </div>
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

        $(document).on('click', '.remove-image-ajax', function() {
            var icon = $(this);
            var id = icon.data('id');
            var imageName = icon.data('image');
            var parent = icon.closest('.image-preview-item');

            Swal.fire({
                title: 'Delete Image?',
                text: "This image will be permanently removed from this product.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#102365',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.product.removeImage') }}",
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id,
                            image: imageName
                        },
                        success: function(response) {
                            if (response.success) {
                                parent.fadeOut(300, function() {
                                    $(this).remove();
                                });
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function() {
                            toastr.error('Something went wrong. Please try again.');
                        }
                    });
                }
            });
        });

        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '100%'
        });

        let selectedCategory = $('#category_id').val();      
        let selectedSubCategory = "{{ $product->sub_category_id ?? '' }}";

        function loadSubcategories(categoryId, selectedSubCategory = null) {
            $('#sub_category_id').empty().append('<option value="">Select Sub Category</option>');

            if (categoryId) {
                $.ajax({
                    url: '/admin/product/get-subcategories/' + categoryId,
                    type: 'GET',
                    success: function (data) {
                        $.each(data, function (key, subCategory) {
                            let selected = (selectedSubCategory == subCategory.id) ? 'selected' : '';
                            $('#sub_category_id').append('<option value="'+ subCategory.id +'" '+ selected +'>'+ subCategory.name +'</option>');
                        });

                        $('#sub_category_id').trigger('change'); 
                    }
                });
            }
        }

        $('#category_id').on('change', function () {
            let categoryId = $(this).val();
            loadSubcategories(categoryId);
        });

        if (selectedCategory) {
            loadSubcategories(selectedCategory, selectedSubCategory);
        }
    </script>
@endsection