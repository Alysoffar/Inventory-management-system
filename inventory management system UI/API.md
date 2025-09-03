# API Documentation

## Overview

The Inventory Management System provides a RESTful API for managing products, suppliers, and inventory operations. All API endpoints return JSON responses.

## Base URL

```
Local Development: http://localhost:8000/api
Production: https://yourdomain.com/api
```

## Authentication

The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:

```
Authorization: Bearer your-token-here
```

## Response Format

All API responses follow this format:

```json
{
    "success": true,
    "data": {},
    "message": "Operation successful",
    "errors": []
}
```

## Endpoints

### Products

#### Get All Products
```http
GET /api/products
```

**Parameters:**
- `page` (optional): Page number for pagination
- `per_page` (optional): Items per page (default: 15)
- `search` (optional): Search products by name or SKU

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "name": "Product Name",
                "description": "Product description",
                "sku": "SKU001",
                "quantity": 100,
                "price": 29.99,
                "cost": 15.00,
                "reorder_level": 10,
                "location": "Warehouse A",
                "latitude": 40.7128,
                "longitude": -74.0060,
                "supplier_id": 1,
                "created_at": "2025-09-03T10:00:00Z",
                "updated_at": "2025-09-03T10:00:00Z",
                "supplier": {
                    "id": 1,
                    "name": "Supplier Name",
                    "email": "supplier@example.com"
                }
            }
        ],
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

#### Get Single Product
```http
GET /api/products/{id}
```

#### Create Product
```http
POST /api/products
```

**Request Body:**
```json
{
    "name": "Product Name",
    "description": "Product description",
    "sku": "SKU001",
    "quantity": 100,
    "price": 29.99,
    "cost": 15.00,
    "reorder_level": 10,
    "location": "Warehouse A",
    "latitude": 40.7128,
    "longitude": -74.0060,
    "supplier_id": 1
}
```

#### Update Product
```http
PUT /api/products/{id}
```

#### Delete Product
```http
DELETE /api/products/{id}
```

#### Adjust Stock
```http
POST /api/products/{id}/adjust-stock
```

**Request Body:**
```json
{
    "quantity": 50,
    "type": "increase", // or "decrease"
    "reason": "Stock adjustment reason",
    "reference": "REF001"
}
```

#### Get Low Stock Products
```http
GET /api/products/low-stock
```

### Suppliers

#### Get All Suppliers
```http
GET /api/suppliers
```

#### Create Supplier
```http
POST /api/suppliers
```

**Request Body:**
```json
{
    "name": "Supplier Name",
    "contact_person": "John Doe",
    "email": "supplier@example.com",
    "phone": "+1234567890",
    "address": "123 Supplier St",
    "city": "City",
    "country": "Country"
}
```

#### Update Supplier
```http
PUT /api/suppliers/{id}
```

#### Delete Supplier
```http
DELETE /api/suppliers/{id}
```

### Inventory Logs

#### Get Inventory History
```http
GET /api/inventory-logs
```

**Parameters:**
- `product_id` (optional): Filter by product ID
- `type` (optional): Filter by log type (adjustment, sale, purchase)
- `date_from` (optional): Start date (YYYY-MM-DD)
- `date_to` (optional): End date (YYYY-MM-DD)

### Dashboard

#### Get Dashboard Statistics
```http
GET /api/dashboard/stats
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total_products": 150,
        "low_stock_products": 12,
        "total_suppliers": 25,
        "total_inventory_value": 45000.50,
        "recent_activities": [...],
        "stock_alerts": [...],
        "monthly_trends": [...]
    }
}
```

#### Get Map Data
```http
GET /api/dashboard/map-data
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Product Name",
            "location": "Warehouse A",
            "latitude": 40.7128,
            "longitude": -74.0060,
            "quantity": 100,
            "status": "in_stock" // or "low_stock", "out_of_stock"
        }
    ]
}
```

### Notifications

#### Send Low Stock Alert
```http
POST /api/notifications/low-stock
```

**Request Body:**
```json
{
    "product_id": 1
}
```

#### Get Notification History
```http
GET /api/notifications
```

## Error Handling

### HTTP Status Codes

- `200` - OK
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

### Error Response Format

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "name": ["The name field is required."],
        "email": ["The email field must be a valid email address."]
    }
}
```

## Rate Limiting

API requests are limited to 60 requests per minute per IP address.

## Webhooks

### Low Stock Alert Webhook

When a product's stock falls below the reorder level, a webhook is sent to the configured endpoint:

```http
POST https://your-webhook-url.com/low-stock
```

**Payload:**
```json
{
    "event": "low_stock_alert",
    "product_id": 1,
    "product_name": "Product Name",
    "current_stock": 5,
    "reorder_level": 10,
    "supplier": {
        "id": 1,
        "name": "Supplier Name",
        "email": "supplier@example.com"
    },
    "timestamp": "2025-09-03T10:00:00Z"
}
```

## SDK Examples

### JavaScript (Axios)

```javascript
const apiClient = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Authorization': 'Bearer your-token-here',
        'Content-Type': 'application/json'
    }
});

// Get all products
const products = await apiClient.get('/products');

// Create new product
const newProduct = await apiClient.post('/products', {
    name: 'New Product',
    sku: 'SKU001',
    quantity: 100,
    price: 29.99
});

// Adjust stock
await apiClient.post(`/products/${productId}/adjust-stock`, {
    quantity: 50,
    type: 'increase',
    reason: 'Restocking'
});
```

### PHP (cURL)

```php
function makeApiCall($endpoint, $method = 'GET', $data = null) {
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://localhost:8000/api" . $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer your-token-here",
            "Content-Type: application/json"
        ),
    ));
    
    if ($data) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($curl);
    curl_close($curl);
    
    return json_decode($response, true);
}

// Example usage
$products = makeApiCall('/products');
$newProduct = makeApiCall('/products', 'POST', [
    'name' => 'New Product',
    'sku' => 'SKU001',
    'quantity' => 100,
    'price' => 29.99
]);
```

## Testing

Use tools like Postman or cURL to test the API endpoints. Import the provided Postman collection for quick testing.

For automated testing, refer to the test files in the `tests/Feature` directory.
