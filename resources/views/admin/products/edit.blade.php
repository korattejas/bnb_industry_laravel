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

    .section-card {
        border: 1px solid #ebe9f1;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        background: #fff;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .section-card:hover {
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    }
    .section-header {
        padding: 0.75rem 1.25rem;
        background-color: #f8f8f8;
        border-bottom: 1px solid #ebe9f1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-title {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-body {
        padding: 1.5rem;
    }
    .add-section-btns {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .image-upload-wrapper-section {
        border: 2px dashed #ebe9f1;
        padding: 2rem;
        text-align: center;
        border-radius: 0.5rem;
        cursor: pointer;
        position: relative;
    }
    .image-upload-wrapper-section:hover {
        border-color: #7367f0;
    }
    .image-preview-section {
        max-width: 100%;
        max-height: 200px;
        margin-top: 10px;
        border-radius: 4px;
        display: none;
    }
    .badge-content { background-color: #e3f2fd; color: #1976d2; }
    .badge-image { background-color: #f3e5f5; color: #7b1fa2; }
    .badge-link { background-color: #fff3e0; color: #ef6c00; }
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

                                            <div class="col-12 mt-4">
                                                <input type="hidden" name="content_sections" id="content_sections_data">
                                                <label class="form-label-luxury">Description Content Sections</label>
                                                <div id="sections-container">
                                                    <!-- Sections will be added here via JS -->
                                                </div>

                                                <div class="add-section-btns mt-2">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSection('content')">
                                                        <i class="bi bi-file-text"></i> Add Content
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSection('image')">
                                                        <i class="bi bi-image"></i> Add Image
                                                    </button>
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSection('link')">
                                                        <i class="bi bi-link"></i> Add Link
                                                    </button>
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

    <!-- Section Templates -->
    <template id="template-content">
        <div class="section-card" data-type="content">
            <div class="section-header">
                <div class="section-title">
                    <span class="badge badge-content px-1"><i class="bi bi-file-text"></i> Content</span>
                    <span class="text-muted font-weight-normal section-label">Section 1</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm" style="width: 100px;" onchange="changeSectionType(this)">
                        <option value="content" selected>Content</option>
                        <option value="image">Image</option>
                        <option value="link">Link</option>
                    </select>
                    <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeSection(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="section-body">
                <textarea class="form-control editor" rows="6" placeholder="Write your content here.."></textarea>
            </div>
        </div>
    </template>

    <template id="template-image">
        <div class="section-card" data-type="image">
            <div class="section-header">
                <div class="section-title">
                    <span class="badge badge-image px-1"><i class="bi bi-image"></i> Image</span>
                    <span class="text-muted font-weight-normal section-label">Section 1</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm" style="width: 100px;" onchange="changeSectionType(this)">
                        <option value="content">Content</option>
                        <option value="image" selected>Image</option>
                        <option value="link">Link</option>
                    </select>
                    <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeSection(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="section-body">
                <div class="image-upload-wrapper-section mb-2" onclick="$(this).find('input').click()">
                    <i class="bi bi-cloud-arrow-up" style="font-size: 2rem; color: #b9b9c3;"></i>
                    <p class="mt-1 mb-0 small text-muted">Click or drag to upload section image</p>
                    <input type="file" class="d-none" onchange="previewImageSection(this, null)" onclick="event.stopPropagation()">
                    <img class="image-preview-section" src="#" alt="Preview">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label small font-weight-bold">Alt Text</label>
                        <input type="text" class="form-control form-control-sm section-alt" placeholder="Describe the image">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">Caption</label>
                        <input type="text" class="form-control form-control-sm section-caption" placeholder="Optional caption">
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="template-link">
        <div class="section-card" data-type="link">
            <div class="section-header">
                <div class="section-title">
                    <span class="badge badge-link px-1"><i class="bi bi-link"></i> Link</span>
                    <span class="text-muted font-weight-normal section-label">Section 1</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <select class="form-select form-select-sm" style="width: 100px;" onchange="changeSectionType(this)">
                        <option value="content">Content</option>
                        <option value="image">Image</option>
                        <option value="link" selected>Link</option>
                    </select>
                    <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeSection(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="section-body">
                <div class="row mb-2">
                    <div class="col-md-6 mb-2">
                        <label class="form-label small font-weight-bold">Link Text *</label>
                        <input type="text" class="form-control form-control-sm section-link-text" placeholder="e.g. Visit Website">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small font-weight-bold">URL *</label>
                        <input type="url" class="form-control form-control-sm section-link-url" placeholder="https://example.com">
                    </div>
                </div>
                <div>
                    <label class="form-label small font-weight-bold">Description</label>
                    <textarea class="form-control form-control-sm section-link-desc" rows="2" placeholder="Brief description of the link"></textarea>
                </div>
            </div>
        </div>
    </template>
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

        // Content Sections Logic
        function addSection(type, data = null) {
            const container = document.getElementById('sections-container');
            const template = document.getElementById('template-' + type);
            const clone = template.content.cloneNode(true);
            
            container.appendChild(clone);
            
            const lastSection = container.lastElementChild;
            updateSectionLabels();
            
            if (type === 'content') {
                const textarea = lastSection.querySelector('.editor');
                if (data && data.content) textarea.value = data.content;
                initEditor(textarea);
            } else if (type === 'image' && data) {
                lastSection.querySelector('.section-alt').value = data.alt || '';
                lastSection.querySelector('.section-caption').value = data.caption || '';
                if (data.image) {
                    const preview = lastSection.querySelector('.image-preview-section');
                    preview.src = "{{ asset('uploads/product') }}/" + data.image;
                    preview.style.display = 'block';
                    lastSection.querySelector('.image-upload-wrapper-section i').style.display = 'none';
                    lastSection.querySelector('.image-upload-wrapper-section p').style.display = 'none';
                }
            } else if (type === 'link' && data) {
                lastSection.querySelector('.section-link-text').value = data.link_text || '';
                lastSection.querySelector('.section-link-url').value = data.link_url || '';
                lastSection.querySelector('.section-link-desc').value = data.link_desc || '';
            }
        }

        function removeSection(btn) {
            btn.closest('.section-card').remove();
            updateSectionLabels();
        }

        function updateSectionLabels() {
            const sections = document.querySelectorAll('.section-card');
            sections.forEach((section, index) => {
                section.querySelector('.section-label').textContent = 'Section ' + (index + 1);
                const imgInput = section.querySelector('input[type="file"]');
                if (imgInput) {
                    imgInput.name = 'section_image_' + index;
                }
            });
        }

        function changeSectionType(select) {
            const card = select.closest('.section-card');
            const newType = select.value;
            const currentType = card.dataset.type;
            if (newType === currentType) return;
            
            const template = document.getElementById('template-' + newType);
            const clone = template.content.cloneNode(true);
            card.innerHTML = clone.querySelector('.section-card').innerHTML;
            card.dataset.type = newType;
            updateSectionLabels();
            if (newType === 'content') {
                initEditor(card.querySelector('.editor'));
            }
        }

        function initEditor(el) {
            if (typeof ClassicEditor !== 'undefined') {
                ClassicEditor.create(el, {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
                }).then(editor => {
                    el.ckeditorInstance = editor;
                }).catch(error => console.error(error));
            }
        }

        function previewImageSection(input, previewId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    let preview = input.closest('.section-body').querySelector('.image-preview-section');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    input.closest('.image-upload-wrapper-section').querySelector('p').style.display = 'none';
                    input.closest('.image-upload-wrapper-section').querySelector('i').style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            // Load existing sections
            @if(isset($product->content_sections) && !empty($product->content_sections))
                @php
                    $sections = is_array($product->content_sections) ? $product->content_sections : json_decode($product->content_sections, true);
                @endphp
                @if(is_array($sections))
                    @foreach($sections as $section)
                        addSection('{{ $section['type'] }}', @json($section));
                    @endforeach
                @endif
            @else
                addSection('content');
            @endif

            $('#addEditForm').on('submit', function(e) {
                const sectionsData = [];
                $('.section-card').each(function() {
                    const type = $(this).data('type');
                    const section = { type: type };
                    
                    if (type === 'content') {
                        const textarea = $(this).find('textarea.editor').get(0);
                        if (textarea && textarea.ckeditorInstance) {
                            section.content = textarea.ckeditorInstance.getData();
                        } else {
                            section.content = $(this).find('textarea').val();
                        }
                    } else if (type === 'image') {
                        section.alt = $(this).find('.section-alt').val();
                        section.caption = $(this).find('.section-caption').val();
                    } else if (type === 'link') {
                        section.link_text = $(this).find('.section-link-text').val();
                        section.link_url = $(this).find('.section-link-url').val();
                        section.link_desc = $(this).find('.section-link-desc').val();
                    }
                    sectionsData.push(section);
                });
                $('#content_sections_data').val(JSON.stringify(sectionsData));
            });
        });
    </script>
@endsection