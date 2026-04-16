@extends('admin.layouts.app')
@section('header_style_content')
<style>
    :root {
        --mst-indigo: #102365;
        --mst-indigo-light: #f5f7ff;
        --mst-text-main: #1e293b;
        --mst-text-muted: #64748b;
        --mst-bg-body: #f8fafc;
        --mst-border: #e2e8f0;
    }

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

    .upload-zone {
        background: #f1f5f9;
        border-radius: 16px;
        border: 2px dashed #cbd5e1;
        transition: all 0.3s;
        cursor: pointer;
    }

    .upload-zone:hover {
        border-color: var(--mst-indigo);
        background: #ecf1f6;
    }

    .form-label-luxury {
        font-weight: 700;
        color: var(--mst-text-main);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        display: block;
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

                                            <div class="col-12 mt-2">
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
                                            
                                            {{-- 
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Sub Category</label>
                                                    <select name="sub_category_id" class="form-control select2" id="sub_category_id">
                                                        <option value="">Select Sub Category</option>
                                                    </select>
                                                </div>
                                            </div>
                                            --}}
                                            
                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>{{ trans('admin_string.name') }}</label>
                                                    <input type="text" class="form-control" name="name"
                                                        value="{{ $product->name }}"
                                                        placeholder="{{ trans('admin_string.name') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Watt</label>
                                                    <input type="text" class="form-control" name="watt"
                                                        value="{{ $product->watt }}"
                                                        placeholder="Product Watt (e.g. 10W, 20W)">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-2">
                                                <div class="form-group">
                                                    <label>Price</label>
                                                    <input type="text" class="form-control" name="price"
                                                        value="{{ $product->price }}" placeholder="Price" required>
                                                </div>
                                            </div>

                                            {{-- 
                                            <div class="col-6 mt-2">
                                                <div class="form-group">
                                                    <label>Discount Price</label>
                                                    <input type="text" class="form-control"
                                                        name="discount_price" value="{{ $product->discount_price }}"
                                                        placeholder="Discount Price">
                                                </div>
                                            </div>
                                            --}}



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
                                                                : (is_string($product->includes) && !empty($product->includes)
                                                                    ? implode(',', json_decode($product->includes, true) ?: [])
                                                                    : '')
                                                        }}
                                                    </textarea>
                                                </div>
                                            </div>

                                            <div class="col-12 mt-4">
                                                <label class="form-label-luxury">Product Gallery Collection</label>
                                                
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

                                                <div class="upload-zone p-4 text-center">
                                                    <i class="bi bi-cloud-arrow-up-fill mb-2" style="font-size: 2.5rem; color: var(--mst-indigo);"></i>
                                                    <h5 class="mb-3">Add More Photos</h5>
                                                    <input type="file" class="form-control" name="photos[]" multiple accept="image/*">
                                                    <p class="text-muted mt-2 small">Upload high-quality JPG, PNG or WebP images.</p>
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