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
                            <h2 class="content-header-title float-start mb-0">Blogs</h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('admin.dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Blogs</a></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-end col-md-3 col-12 d-md-block d-none">
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
                        Add Blog
                    </a>
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 300px;">
                            <div class="mb-2">
                                <label class="form-label">Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">All</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Is Featured</label>
                                <select id="filter-featured" class="form-select">
                                    <option value="">All</option>
                                    <option value="1">Featured</option>
                                    <option value="0">Normal</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Publish Date</label>
                                <input type="date" id="filter-publish-date" class="form-control">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Created Date</label>
                                <input type="date" id="filter-created-date" class="form-control">
                            </div>
                            <div class="d-flex justify-content-between">
                                <button id="btn-apply-filters" class="btn btn-sm btn-primary">
                                    Apply
                                </button>
                                <button id="btn-reset-filters" class="btn btn-sm btn-secondary">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-body">
                <!-- Column Search -->
                <section id="column-search-datatable">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-datatable">
                                    <table class="dt-column-search table w-100 dataTable" id="table-1">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Category</th>
                                                <th>Title</th>
                                                <th>Publish Date</th>
                                                <th data-stuff="Active,InActive">Status</th>
                                                <th data-stuff="High Priority,Low Priority">Featured</th>
                                                <th>Icon</th>
                                                <th data-search="false">Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <div id="c-viewBlogModal" class="c-modal">
        <div class="c-modal-dialog">
            <div class="c-modal-content">
                <div class="c-modal-header">
                    <h5 class="c-modal-title"><i class="bi bi-journal-text"></i> Blog Details</h5>
                    <button class="c-close-btn" data-c-close>&times;</button>
                </div>
                <div class="c-modal-body" id="c-blog-details">
                    <div class="c-loader">
                        <div class="c-spinner"></div>
                        <span>Fetching details...</span>
                    </div>
                </div>
                <div class="c-modal-footer">
                    <small><i class="bi bi-clock"></i> Updated just now</small>
                    <button class="c-btn" data-c-close><i class="bi bi-x-circle"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer_script_content')
    <script>
        const sweetalert_delete_title = "Delete Blog?";
        const sweetalert_change_status = "Change Status of Blog";
        const sweetalert_change_priority_status = "Change Featured Status of Blog";

        // base form and data URLs
        const form_url = '/blogs';
        datatable_url = '/getDataBlogs';

        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 100,
            lengthMenu: [
                [10, 25, 50, 100, 200, -1],
                [10, 25, 50, 100, 200, "All"]
            ],
            columns: [{
                    data: null,
                    name: 'id',
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'publish_date',
                    name: 'publish_date'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'featured',
                    name: 'featured'
                },
                {
                    data: 'icon',
                    name: 'icon',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false
                },
            ],
            order: [
                [0, 'DESC']
            ],
        });

        // Blog View Modal
        $(document).on('click', '.btn-view', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            const baseUrl = "{{ asset('uploads/blogs') }}/";

            $("#c-viewBlogModal").addClass("show");
            $("#c-blog-details").html(
                `<div class="c-loader"><div class="c-spinner"></div><span>Loading...</span></div>`);

            $.ajax({
                url: '/admin/blogs-view/' + id,
                type: 'GET',
                success: function(response) {
                    let data = response.data;
                    let sectionsHtml = '';
                    if (data.content_sections) {
                        try {
                            const sections = JSON.parse(data.content_sections);
                            sections.forEach((section, idx) => {
                                if (section.type === 'content') {
                                    sectionsHtml += `<div class="p-1 mb-1 border-start border-primary border-3 bg-light">
                                        <small class="text-primary font-weight-bold">Section ${idx+1}: Content</small>
                                        <div>${section.content}</div>
                                    </div>`;
                                } else if (section.type === 'image') {
                                    sectionsHtml += `<div class="p-1 mb-1 border-start border-info border-3 bg-light">
                                        <small class="text-info font-weight-bold">Section ${idx+1}: Image</small>
                                        <div class="d-flex gap-2 align-items-center mt-1">
                                            <img src="${baseUrl + section.image}" style="height: 60px; border-radius: 4px;">
                                            <div>
                                                <p class="mb-0 small"><strong>Alt:</strong> ${section.alt || '-'}</p>
                                                <p class="mb-0 small"><strong>Caption:</strong> ${section.caption || '-'}</p>                                            
                                            </div>
                                        </div>
                                    </div>`;
                                } else if (section.type === 'link') {
                                    sectionsHtml += `<div class="p-1 mb-1 border-start border-warning border-3 bg-light">
                                        <small class="text-warning font-weight-bold">Section ${idx+1}: Link</small>
                                        <p class="mb-0"><strong>${section.link_text}:</strong> <a href="${section.link_url}" target="_blank">${section.link_url}</a></p>
                                        <p class="mb-0 small text-muted">${section.link_desc || ''}</p>
                                    </div>`;
                                }
                            });
                        } catch (e) {
                            sectionsHtml = data.content_sections;
                        }
                    }

                    let html = `
                        <div class="row">
                            <div class="col-md-6 mb-1"><label class="form-label fw-bolder text-dark" style="font-size: 1rem;">Category</label><p class="border-bottom pb-25 text-primary fs-5">${data.category ?? '-'}</p></div>
                            <div class="col-md-6 mb-1"><label class="form-label fw-bolder text-dark" style="font-size: 1rem;">Title</label><p class="border-bottom pb-25 text-primary fs-5">${data.title ?? '-'}</p></div>
                            <div class="col-md-6 mb-1"><label class="form-label fw-bolder text-dark" style="font-size: 1rem;">Meta Title</label><p class="border-bottom pb-25 text-primary fs-5">${data.meta_title ?? '-'}</p></div>
                            <div class="col-md-6 mb-1"><label class="form-label fw-bolder text-dark" style="font-size: 1rem;">Slug</label><p class="border-bottom pb-25 text-primary fs-5">${data.slug ?? '-'}</p></div>
                            
                            <div class="col-md-12 mb-1"><label class="form-label fw-bolder text-dark" style="font-size: 1rem;">Excerpt</label><p class="border-bottom pb-25 fs-5">${data.excerpt ?? '-'}</p></div>
                            
                            <div class="col-md-12 mb-1">
                                <label class="form-label fw-bolder text-dark" style="font-size: 1.1rem;">Content Sections</label>
                                <div class="border rounded p-1 mb-1" style="max-height: 300px; overflow-y: auto; background: #fafafa;">
                                    ${sectionsHtml || '<p class="text-muted">No sections added</p>'}
                                </div>
                            </div>
                            
                             <div class="col-md-12 mb-1">
                                <label class="form-label fw-bolder text-dark" style="font-size: 1.1rem;">Legacy Content (Original)</label>
                                <div class="border rounded p-1 mb-1" style="max-height: 150px; overflow-y: auto;">
                                    ${data.content || '<p class="text-muted">-</p>'}
                                </div>
                            </div>

                            <div class="col-md-4 mb-1"><label class="form-label fw-bolder text-dark" style="font-size: 1rem;">Author</label><p class="border-bottom pb-25 fs-5">${data.author ?? '-'}</p></div>
                            <div class="col-md-4 mb-1"><label class="form-label fw-bolder text-dark" style="font-size: 1rem;">Author Email</label><p class="border-bottom pb-25 fs-5">${data.author_email ?? '-'}</p></div>
                            <div class="col-md-4 mb-1"><label class="form-label fw-bolder text-dark" style="font-size: 1rem;">Read Time</label><p class="border-bottom pb-25 fs-5">${data.read_time ?? '-'}</p></div>
                            
                            <div class="col-md-6 mb-1"><label class="form-label fw-bolder text-dark" style="font-size: 1rem;">Publish Date</label><p class="border-bottom pb-25 fs-5">${data.publish_date ?? '-'}</p></div>
                            <div class="col-md-6 mb-1"><label class="form-label fw-bolder text-dark" style="font-size: 1rem;">Status</label><p class="border-bottom pb-25">${data.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">InActive</span>'}</p></div>

                            <div class="col-md-12 mb-1">
                                <label class="form-label fw-bolder text-dark" style="font-size: 1.1rem;">Tags</label>
                                <div class="border-bottom pb-25">${
                                    data.tags ? JSON.parse(data.tags).map(tag => `<span class="badge bg-light-primary me-50 mb-50">${tag}</span>`).join(" ") : '-'
                                }</div>
                            </div>

                            <div class="col-md-12 mb-1">
                                <label class="form-label fw-bolder text-dark" style="font-size: 1rem;">SEO Meta Keywords</label>
                                <p class="border-bottom pb-25 text-muted small">${data.meta_keywords ?? '-'}</p>
                            </div>
                            <div class="col-md-12 mb-1">
                                <label class="form-label fw-bolder text-dark" style="font-size: 1rem;">SEO Meta Description</label>
                                <p class="border-bottom pb-25 text-muted small">${data.meta_description ?? '-'}</p>
                            </div>

                            <div class="col-md-12 text-center mt-2">
                                <label class="form-label fw-bolder text-dark" style="font-size: 1.1rem; d-block">Featured Image</label>
                                ${data.icon 
                                    ? `<img src="${baseUrl + data.icon}" alt="Blog Icon" class="img-fluid rounded" style="max-width:300px; cursor:pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.1);" onclick="window.open('${baseUrl + data.icon}', '_blank')">
                                       <p class="mt-1 small text-dark"><strong>Alt:</strong> ${data.featured_image_alt || '-'}</p>`
                                    : '<p class="text-muted">No image uploaded</p>'
                                }
                            </div>
                        </div>`;
                    $("#c-blog-details").html(html);
                },
                error: function() {
                    $("#c-blog-details").html(
                        `<div class="c-detail-card" style="color:red">Failed to load details.</div>`
                    );
                }
            });
        });

        $(document).on("click", "[data-c-close]", function() {
            $("#c-viewBlogModal").removeClass("show");
        });
    </script>
    <script src="{{ URL::asset('panel-assets/js/core/datatable.js') }}?v={{ time() }}"></script>
@endsection
