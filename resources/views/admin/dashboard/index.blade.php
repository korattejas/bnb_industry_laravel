@extends('admin.layouts.app')
 
@section('header_style_content')
<style>
    :root {
        --mst-indigo: #102365;
        --mst-indigo-light: #f5f7ff;
        --mst-success: #059669;
        --mst-warning: #d97706;
        --mst-danger: #dc2626;
        --mst-info: #2563eb;
        --mst-text-main: #1e293b;
        --mst-text-muted: #64748b;
        --mst-bg-gray: #f8fafc;
    }
 
    #beautyden-dashboard {
        padding: 1.5rem;
        background: var(--mst-bg-gray);
        min-height: 100vh;
    }
 
    /* Welcome Header */
    .dashboard-welcome {
        margin-bottom: 2.5rem;
    }
    .dashboard-welcome h1 {
        font-weight: 800;
        color: var(--mst-indigo);
        margin-bottom: 0.5rem;
        font-size: 2rem;
    }
    .dashboard-welcome p {
        color: var(--mst-text-muted);
        font-size: 1.1rem;
    }
 
    /* Primary Stats Grid */
    .primary-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
 
    .stat-card-luxury {
        background: #fff;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(226, 232, 240, 0.8);
        text-decoration: none !important;
        display: block;
        position: relative;
        overflow: hidden;
    }
 
    .stat-card-luxury:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: var(--mst-indigo);
    }
 
    .stat-icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }
 
    .card-inquiries .stat-icon-wrapper { background: #fff5f5; color: var(--mst-danger); }
    .card-products .stat-icon-wrapper { background: #ecfdf5; color: var(--mst-success); }
    .card-reviews .stat-icon-wrapper { background: #fffbeb; color: var(--mst-warning); }
 
    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 4px;
        color: var(--mst-text-main);
    }
 
    .stat-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--mst-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
 
    /* Module Sections */
    .dashboard-section {
        margin-bottom: 3.5rem;
    }
 
    .section-title {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 1.5rem;
        padding-left: 8px;
    }
    .section-title i {
        font-size: 1.4rem;
        color: var(--mst-indigo);
    }
    .section-title h4 {
        margin: 0;
        font-weight: 700;
        color: var(--mst-text-main);
        font-size: 1.25rem;
    }
 
    .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.5rem;
    }
 
    .module-card-mini {
        background: #fff;
        padding: 30px 24px;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        text-decoration: none !important;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
 
    .module-card-mini:hover {
        background: var(--mst-indigo-light);
        border-color: var(--mst-indigo);
        transform: translateY(-4px);
    }
 
    .module-icon-mini {
        font-size: 1.85rem;
        color: var(--mst-indigo);
        margin-bottom: 15px;
        background: #f8fafc;
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        transition: 0.2s;
    }
    .module-card-mini:hover .module-icon-mini {
        background: var(--mst-indigo);
        color: #fff;
    }
 
    .module-count {
        font-weight: 800;
        font-size: 1.75rem;
        color: var(--mst-text-main);
        display: block;
        margin-bottom: 4px;
    }
 
    .module-name {
        font-size: 1rem;
        font-weight: 600;
        color: var(--mst-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
 
</style>
@endsection
 
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">
            <section id="beautyden-dashboard">
                
                <!-- Welcome Header -->
                <div class="dashboard-welcome">
                    <h1>Welcome Back, Admin! ✨</h1>
                    <p>Here's a quick overview of your business catalog and engagement.</p>
                </div>
 
                <!-- Primary Vital Signs -->
                <div class="primary-stats-grid">

 
                    <!-- Total Inquiries -->
                    <a href="{{ route('admin.contact-submissions.index') }}" class="stat-card-luxury card-inquiries">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-envelope-heart"></i>
                        </div>
                        <div class="stat-value">{{ $totalContacts }}</div>
                        <div class="stat-label">New Inquiries</div>
                    </a>
 
                    <!-- Active Products -->
                    <a href="{{ route('admin.product.index') }}" class="stat-card-luxury card-products">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="stat-value">{{ $totalProducts }}</div>
                        <div class="stat-label">Active Products</div>
                    </a>
 
                    <!-- Reviews -->
                    <a href="{{ route('admin.reviews.index') }}" class="stat-card-luxury card-reviews">
                        <div class="stat-icon-wrapper">
                            <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="stat-value">{{ $totalCustomerReviews }}</div>
                        <div class="stat-label">Member Reviews</div>
                    </a>
                </div>
 
                <!-- Content Management Group -->
                <div class="dashboard-section">
                    <div class="section-title">
                        <i class="bi bi-grid-1x2-fill"></i>
                        <h4>Product & Catalog</h4>
                    </div>
                    <div class="module-grid">
                        <a href="{{ route('admin.product-category.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-layers"></i></div>
                            <span class="module-count">{{ $totalProductCategory }}</span>
                            <span class="module-name">Categories</span>
                        </a>
                        <a href="{{ route('admin.product.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-box"></i></div>
                            <span class="module-count">{{ $totalProducts }}</span>
                            <span class="module-name">Products</span>
                        </a>
                    </div>
                </div>
 
                <!-- Engagement Group -->
                <div class="dashboard-section">
                    <div class="section-title">
                        <i class="bi bi-chat-heart-fill"></i>
                        <h4>Community & Growth</h4>
                    </div>
                    <div class="module-grid">
                        <a href="{{ route('admin.blogs.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-journal-text"></i></div>
                            <span class="module-count">{{ $totalBlogs }}</span>
                            <span class="module-name">Articles</span>
                        </a>
                        <a href="{{ route('admin.blog-category.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-bookmark-heart"></i></div>
                            <span class="module-count">{{ $totalBlogCategory }}</span>
                            <span class="module-name">Blog Categories</span>
                        </a>
{{-- 
                        <a href="{{ route('admin.hirings.index') }}" class="module-card-mini">
                            <div class="module-icon-mini"><i class="bi bi-person-plus-fill"></i></div>
                            <span class="module-count">{{ $totalHirings }}</span>
                            <span class="module-name">Hiring Apps</span>
                        </a> 
--}}
                    </div>
                </div>
 
            </section>
        </div>
    </div>
</div>
@endsection
