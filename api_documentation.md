# Bnb Industry API Documentation

This document provides details for the active API endpoints used for the Bnb Industry frontend.

**Base URL**: `{{domain}}/api/V1/`

---

## 1. Product Module

### Get Product Categories
Retrieve a list of all active product categories.
- **URL**: `productCategory`
- **Method**: `GET`
- **Body Parameters**: None
- **Response**: List of categories with `id`, `name`, `icon`, `description`, `is_popular`.

### Get Products
Retrieve products with filtering and search options. This endpoint handles listing, category filtering, and single product details.
- **URL**: `products`
- **Method**: `POST`
- **Body Parameters**:
    | Parameter | Type | Required | Description |
    | :--- | :--- | :--- | :--- |
    | `product_id` | Integer | No | If provided, returns full details of a single product + related products. |
    | `category_id` | Integer | No | Filter products by category ID. |
    | `search` | String | No | Search products by name, description, or includes. |
    | `per_page` | Integer | No | Number of results per page (Default: 24). |
    | `page` | Integer | No | Page number for pagination. |
- **Description**: Returns a paginated list of products or single product details if `product_id` is passed.

---

## 2. Blog Module

### Get Blog Categories
Retrieve a list of all active blog categories.
- **URL**: `blogCategory`
- **Method**: `GET`
- **Body Parameters**: None

### Get Blogs
Retrieve a list of blog posts with pagination and search.
- **URL**: `blogs`
- **Method**: `POST`
- **Body Parameters**:
    | Parameter | Type | Required | Description |
    | :--- | :--- | :--- | :--- |
    | `category_id` | Integer | No | Filter blogs by category ID. |
    | `search` | String | No | Search blogs by title, content, or author. |
    | `per_page` | Integer | No | Number of results per page (Default: 9). |
    | `page` | Integer | No | Page number for pagination. |

### Blog View (Single Blog)
Retrieve detailed content for a specific blog post using its slug.
- **URL**: `blogView`
- **Method**: `POST`
- **Body Parameters**:
    | Parameter | Type | Required | Description |
    | :--- | :--- | :--- | :--- |
    | `slug` | String | **Yes** | The unique slug of the blog post. |

---

## 3. Content & System Modules

### Home Counters
Retrieve statistics/counters for the home page.
- **URL**: `homeCounter`
- **Method**: `GET`
- **Body Parameters**: None

### FAQs
Retrieve a list of active Frequently Asked Questions.
- **URL**: `faqs`
- **Method**: `GET`
- **Body Parameters**: None

### Settings & Hero Content
Retrieve website settings, contact info, and dynamic Hero Section content (Slider, Title, Description).
- **URL**: `settings`
- **Method**: `GET`
- **Body Parameters**: None

### Contact Form Submission
Submit inquiries from the contact form.
- **URL**: `contactFormSubmit`
- **Method**: `POST`
- **Body Parameters**:
    | Parameter | Type | Required | Description |
    | :--- | :--- | :--- | :--- |
    | `first_name` | String | **Yes** | Submitter's first name. |
    | `last_name` | String | **Yes** | Submitter's last name. |
    | `email` | String | No | Submitter's email address. |
    | `phone` | String | No | Submitter's contact number. |
    | `subject` | String | No | Subject of the inquiry. |
    | `message` | String | No | The detailed message/inquiry. |
    | `product_id` | Integer | No | Specific product the inquiry is about. |

### Policies
Retrieve legal policies (Privacy Policy, Terms & Conditions, etc.).
- **URL**: `policies`
- **Method**: `POST`
- **Body Parameters**:
    | Parameter | Type | Required | Description |
    | :--- | :--- | :--- | :--- |
    | `type` | String | No | Filter by policy type (e.g., "Privacy Policy", "Terms"). |

---

## Response Structure
All APIs follow a standard response format:
```json
{
    "status": true,
    "message": "Data retrieved successfully",
    "data": { ... }
}
```
In case of errors:
```json
{
    "status": false,
    "message": "Error description here",
    "error_code": 500
}
```
