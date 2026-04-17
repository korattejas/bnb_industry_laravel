@extends('admin.layouts.app')

@section('header_style_content')
<style>
    .blog-create-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 0;
        border-bottom: 1px solid #ebe9f1;
        margin-bottom: 2rem;
    }
    .form-label {
        font-weight: 700;
        font-size: 0.75rem;
        color: #000000;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .blog-create-header h2 {
        font-weight: 700;
        margin: 0;
        color: #5e5873;
    }
    .blog-create-header p {
        color: #b9b9c3;
        margin: 0;
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
    .sidebar-card {
        background: #fff;
        border-radius: 0.5rem;
        border: 1px solid #ebe9f1;
        margin-bottom: 1.5rem;
    }
    .sidebar-card-header {
        padding: 1rem;
        border-bottom: 1px solid #ebe9f1;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    .sidebar-card-body {
        padding: 1rem;
    }
    .add-section-btns {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .image-upload-wrapper {
        border: 2px dashed #ebe9f1;
        padding: 2rem;
        text-align: center;
        border-radius: 0.5rem;
        cursor: pointer;
        position: relative;
    }
    .image-upload-wrapper:hover {
        border-color: #7367f0;
    }
    .image-preview {
        max-width: 100%;
        max-height: 200px;
        margin-top: 10px;
        border-radius: 4px;
        display: block;
    }
    .btn-light-secondary {
        background-color: #f6f6f6;
        color: #4b4b4b;
    }
    .badge-content { background-color: #e3f2fd; color: #1976d2; }
    .badge-image { background-color: #f3e5f5; color: #7b1fa2; }
    .badge-link { background-color: #fff3e0; color: #ef6c00; }
    
    .switch-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <form id="addEditForm" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="edit_value" value="{{ $blog->id }}">
            <input type="hidden" name="content_sections" id="content_sections_data">

            <div class="blog-create-header">
                <div>
                    <a href="{{ route('admin.blogs.index') }}" class="text-secondary mb-1 d-inline-block">
                        <i data-feather="arrow-left"></i> Back to Blogs
                    </a>
                    <h2>Edit Blog</h2>
                    <p>Modify and update your blog post</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary ml-1">Update Blog</button>
                </div>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-md-8">
                    <!-- Basic Information -->
                    <div class="card mb-3">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">BASIC INFORMATION</h4>
                        </div>
                        <div class="card-body pt-2">
                            <div class="mb-2">
                                <label class="form-label font-weight-bold">Title *</label>
                                <input type="text" name="title" class="form-control" value="{{ $blog->title }}" placeholder="e.g. How to Choose the Best Website Development Company" required>
                            </div>
                            <div class="mb-0">
                                <label class="form-label font-weight-bold">Slug *</label>
                                <div class="input-group">
                                    <span class="input-group-text">/blog/</span>
                                    <input type="text" name="slug" class="form-control" value="{{ $blog->slug }}" placeholder="choose-best-website-development-company" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Sections -->
                    <h5 class="mt-4 mb-2 font-weight-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">Content Sections</h5>
                    <div id="sections-container">
                        @php
                            $sections = json_decode($blog->content_sections, true) ?: [['type' => 'content', 'content' => $blog->content]];
                        @endphp
                        @foreach($sections as $index => $section)
                            @if($section['type'] == 'content')
                                <div class="section-card" data-type="content">
                                    <div class="section-header">
                                        <div class="section-title">
                                            <span class="badge badge-content px-1"><i data-feather="file-text" style="width: 14px;"></i> Content</span>
                                            <span class="text-muted font-weight-normal section-label">Section {{ $index + 1 }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <select class="form-select form-select-sm" style="width: 100px;" onchange="changeSectionType(this)">
                                                <option value="content" selected>Content</option>
                                                <option value="image">Image</option>
                                                <option value="link">Link</option>
                                            </select>
                                            <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeSection(this)">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="section-body">
                                        <textarea class="form-control editor" rows="6" placeholder="Write your content here..">{{ $section['content'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            @elseif($section['type'] == 'image')
                                <div class="section-card" data-type="image">
                                    <div class="section-header">
                                        <div class="section-title">
                                            <span class="badge badge-image px-1"><i data-feather="image" style="width: 14px;"></i> Image</span>
                                            <span class="text-muted font-weight-normal section-label">Section {{ $index + 1 }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <select class="form-select form-select-sm" style="width: 100px;" onchange="changeSectionType(this)">
                                                <option value="content">Content</option>
                                                <option value="image" selected>Image</option>
                                                <option value="link">Link</option>
                                            </select>
                                            <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeSection(this)">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="section-body">
                                        <div class="image-upload-wrapper mb-2" onclick="$(this).find('input').click()">
                                            <i data-feather="upload-cloud" style="width: 30px; height: 30px; color: #b9b9c3; display: none;"></i>
                                            <p class="mt-1 mb-0 small" style="display: none;">Click or drag to upload section image</p>
                                            <input type="file" class="d-none" onchange="previewImage(this, null)" onclick="event.stopPropagation()">
                                            <img class="image-preview" src="{{ isset($section['image']) ? asset('uploads/blogs/'.$section['image']) : '#' }}" alt="Preview">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-2 mb-md-0">
                                                <label class="form-label small font-weight-bold">Alt Text</label>
                                                <input type="text" class="form-control form-control-sm section-alt" value="{{ $section['alt'] ?? '' }}" placeholder="Describe the image">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small font-weight-bold">Caption</label>
                                                <input type="text" class="form-control form-control-sm section-caption" value="{{ $section['caption'] ?? '' }}" placeholder="Optional caption">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($section['type'] == 'link')
                                <div class="section-card" data-type="link">
                                    <div class="section-header">
                                        <div class="section-title">
                                            <span class="badge badge-link px-1"><i data-feather="link" style="width: 14px;"></i> Link</span>
                                            <span class="text-muted font-weight-normal section-label">Section {{ $index + 1 }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <select class="form-select form-select-sm" style="width: 100px;" onchange="changeSectionType(this)">
                                                <option value="content">Content</option>
                                                <option value="image">Image</option>
                                                <option value="link" selected>Link</option>
                                            </select>
                                            <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeSection(this)">
                                                <i data-feather="trash-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="section-body">
                                        <div class="row mb-2">
                                            <div class="col-md-6 mb-2 mb-md-0">
                                                <label class="form-label small font-weight-bold">Link Text *</label>
                                                <input type="text" class="form-control form-control-sm section-link-text" value="{{ $section['link_text'] ?? '' }}" placeholder="e.g. Visit Dreamleo">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small font-weight-bold">URL *</label>
                                                <input type="url" class="form-control form-control-sm section-link-url" value="{{ $section['link_url'] ?? '' }}" placeholder="https://example.com">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label small font-weight-bold">Description</label>
                                            <textarea class="form-control form-control-sm section-link-desc" rows="2" placeholder="Brief description of the link">{{ $section['link_desc'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="add-section-btns">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSection('content')">
                            <i data-feather="plus"></i> Add Content
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSection('image')">
                            <i data-feather="plus"></i> Add Image
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSection('link')">
                            <i data-feather="plus"></i> Add Link
                        </button>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Settings -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">Settings</div>
                        <div class="sidebar-card-body">
                            <div class="switch-wrapper">
                                <div>
                                    <label class="form-label mb-0 d-block font-weight-bold">Published</label>
                                    <small class="text-muted">Visible on the marketing site</small>
                                </div>
                                <div class="form-check form-switch p-0">
                                    <input type="checkbox" class="form-check-input ms-0" id="statusSwitch" name="status" value="1" {{ $blog->status ? 'checked' : '' }} style="width: 3rem; height: 1.5rem;">
                                </div>
                            </div>
                            <div class="mt-2">
                                <label class="form-label font-weight-bold">Category</label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $blog->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">Featured Image</div>
                        <div class="sidebar-card-body">
                            <div class="image-upload-wrapper" onclick="document.getElementById('featured_image_input').click()">
                                <i data-feather="upload-cloud" style="width: 40px; height: 40px; color: #b9b9c3; {{ $blog->icon ? 'display:none;' : '' }}"></i>
                                <p class="mt-1" style="{{ $blog->icon ? 'display:none;' : '' }}">Click or drag to upload featured image</p>
                                <small class="text-muted" style="{{ $blog->icon ? 'display:none;' : '' }}">JPG, PNG, WEBP - max 10MB</small>
                                <input type="file" id="featured_image_input" name="featured_image" class="d-none" onchange="previewImage(this, 'featured-preview')" onclick="event.stopPropagation()">
                                <img id="featured-preview" class="image-preview" src="{{ $blog->icon ? asset('uploads/blogs/'.$blog->icon) : '#' }}" alt="Preview" style="{{ $blog->icon ? 'display:block;' : 'display:none;' }}">
                            </div>
                            <div class="mt-2">
                                <label class="form-label font-weight-bold">Alt Text</label>
                                <input type="text" name="featured_image_alt" class="form-control" value="{{ $blog->featured_image_alt }}" placeholder="Describe the featured image">
                            </div>
                        </div>
                    </div>

                    <!-- Author -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">Author</div>
                        <div class="sidebar-card-body">
                            <div class="mb-2">
                                <label class="form-label font-weight-bold">Name *</label>
                                <input type="text" name="author" class="form-control" value="{{ $blog->author }}" placeholder="e.g. Dreamleo Web Solution" required>
                            </div>
                            <div>
                                <label class="form-label font-weight-bold">Email *</label>
                                <input type="email" name="author_email" class="form-control" value="{{ $blog->author_email }}" placeholder="info@dreamleo.com" required>
                            </div>
                        </div>
                    </div>

                    <!-- SEO & Meta -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-header">SEO & Meta</div>
                        <div class="sidebar-card-body">
                            <div class="mb-2">
                                <label class="form-label font-weight-bold">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" value="{{ $blog->meta_title }}" placeholder="Meta Title">
                            </div>
                            <div class="mb-2">
                                <label class="form-label font-weight-bold">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="3" placeholder="Meta Description">{{ $blog->meta_description }}</textarea>
                            </div>
                            <div class="mb-2">
                                <label class="form-label font-weight-bold">Meta Keywords</label>
                                <textarea name="meta_keywords" class="form-control" rows="3" placeholder="website development, web design, it">{{ $blog->meta_keywords }}</textarea>
                            </div>
                            <div>
                                <label class="form-label font-weight-bold">Tags</label>
                                <input type="text" name="tags" class="form-control" value="{{ is_array($blog->tags) ? implode(',', $blog->tags) : ($blog->tags ? implode(',', json_decode($blog->tags, true)) : '') }}" placeholder="Website Development, UI/UX Design">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Section Templates -->
<template id="template-content">
    <div class="section-card" data-type="content">
        <div class="section-header">
            <div class="section-title">
                <span class="badge badge-content px-1"><i data-feather="file-text" style="width: 14px;"></i> Content</span>
                <span class="text-muted font-weight-normal section-label">Section 1</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select class="form-select form-select-sm" style="width: 100px;" onchange="changeSectionType(this)">
                    <option value="content" selected>Content</option>
                    <option value="image">Image</option>
                    <option value="link">Link</option>
                </select>
                <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeSection(this)">
                    <i data-feather="trash-2"></i>
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
                <span class="badge badge-image px-1"><i data-feather="image" style="width: 14px;"></i> Image</span>
                <span class="text-muted font-weight-normal section-label">Section 1</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select class="form-select form-select-sm" style="width: 100px;" onchange="changeSectionType(this)">
                    <option value="content">Content</option>
                    <option value="image" selected>Image</option>
                    <option value="link">Link</option>
                </select>
                <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeSection(this)">
                    <i data-feather="trash-2"></i>
                </button>
            </div>
        </div>
        <div class="section-body">
            <div class="image-upload-wrapper mb-2" onclick="$(this).find('input').click()">
                <i data-feather="upload-cloud" style="width: 30px; height: 30px; color: #b9b9c3;"></i>
                <p class="mt-1 mb-0 small">Click or drag to upload section image</p>
                <input type="file" class="d-none" onchange="previewImage(this, null)" onclick="event.stopPropagation()">
                <img class="image-preview" src="#" alt="Preview" style="display: none;">
            </div>
            <div class="row">
                <div class="col-md-6 mb-2 mb-md-0">
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
                <span class="badge badge-link px-1"><i data-feather="link" style="width: 14px;"></i> Link</span>
                <span class="text-muted font-weight-normal section-label">Section 1</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select class="form-select form-select-sm" style="width: 100px;" onchange="changeSectionType(this)">
                    <option value="content">Content</option>
                    <option value="image">Image</option>
                    <option value="link" selected>Link</option>
                </select>
                <button type="button" class="btn btn-link btn-sm text-danger p-0" onclick="removeSection(this)">
                    <i data-feather="trash-2"></i>
                </button>
            </div>
        </div>
        <div class="section-body">
            <div class="row mb-2">
                <div class="col-md-6 mb-2 mb-md-0">
                    <label class="form-label small font-weight-bold">Link Text *</label>
                    <input type="text" class="form-control form-control-sm section-link-text" placeholder="e.g. Visit Dreamleo">
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
    var form_url = 'blogs/store';
    var redirect_url = 'blogs';
    var is_one_image_and_multiple_image_status = 'is_one_image';

    // Mock pond for form.js compatibility
    window.pond = { getFiles: function() { return []; }, removeFiles: function() {} };
    window.thumbnailPond = { getFiles: function() { return []; }, removeFiles: function() {} };

    function addSection(type) {
        const container = document.getElementById('sections-container');
        const template = document.getElementById('template-' + type);
        const clone = template.content.cloneNode(true);
        
        container.appendChild(clone);
        
        // Update labels and input names
        updateSectionLabels();
        
        const lastSection = container.lastElementChild;

        // Initialize CKEditor for content type
        if (type === 'content') {
            const textarea = lastSection.querySelector('.editor');
            initEditor(textarea);
        }
        
        // Re-initialize Feather icons
        if (feather) feather.replace();
    }

    function removeSection(btn) {
        btn.closest('.section-card').remove();
        updateSectionLabels();
    }

    function updateSectionLabels() {
        const sections = document.querySelectorAll('.section-card');
        sections.forEach((section, index) => {
            section.querySelector('.section-label').textContent = 'Section ' + (index + 1);
            
            // Update image input name if it exists
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
        
        // Replace the card content but keep the label index
        card.innerHTML = clone.querySelector('.section-card').innerHTML;
        card.dataset.type = newType;

        // Re-init labels (sets new names for inputs)
        updateSectionLabels();
        
        // Re-init editors or icons
        if (newType === 'content') {
            initEditor(card.querySelector('.editor'));
        }
        
        if (feather) feather.replace();
    }

    function initEditor(el) {
        ClassicEditor.create(el, {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
        }).catch(error => console.error(error));
    }

    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                let preview;
                if (previewId) {
                    preview = document.getElementById(previewId);
                } else {
                    preview = input.closest('.section-body').querySelector('.image-preview');
                }
                preview.src = e.target.result;
                preview.style.display = 'block';
                input.closest('.image-upload-wrapper').querySelector('p').style.display = 'none';
                input.closest('.image-upload-wrapper').querySelector('i').style.display = 'none';
                if (input.closest('.image-upload-wrapper').querySelector('small')) {
                    input.closest('.image-upload-wrapper').querySelector('small').style.display = 'none';
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Update labels and input names on load
        updateSectionLabels();

        // Init existing editors
        document.querySelectorAll('.editor').forEach(el => {
            initEditor(el);
        });
        
        // Handle form submission to include section data
        $('#addEditForm').on('submit', function(e) {
            const sectionsData = [];
            $('.section-card').each(function() {
                const type = $(this).data('type');
                const section = { type: type };
                
                if (type === 'content') {
                    // Try to find editor instance
                    const textarea = $(this).find('textarea').get(0);
                    // This is a bit tricky with CKEditor 5 if we don't store instances
                    // But usually CKEditor 5 updates the textarea on form submit automatically
                    // Or we can find it via the element
                    section.content = $(this).find('.ck-editor__editable').get(0)?.ckeditorInstance?.getData() || $(this).find('textarea').val();
                } else if (type === 'image') {
                    section.alt = $(this).find('.section-alt').val();
                    section.caption = $(this).find('.section-caption').val();
                    // Keep existing image if no new file
                    const existingImg = $(this).find('.image-preview').attr('src');
                    if (existingImg && existingImg.includes('uploads/blogs/')) {
                        section.image = existingImg.split('/').pop();
                    }
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
