@extends('layouts.app')

@section('title', 'Inventory Map')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">🗺️ Inventory Map</h1>
            <p class="mb-0 text-muted">Track inventory locations and status</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('inventory.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" onclick="filterMap('all')">All Products</a>
                    <a class="dropdown-item" href="#" onclick="filterMap('low')">Low Stock Only</a>
                    <a class="dropdown-item" href="#" onclick="filterMap('out')">Out of Stock Only</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="row">
        <div class="col-lg-9">
            <div class="card shadow">
                <div class="card-body p-0">
                    <div id="inventoryMap" style="height: 600px; width: 100%;"></div>
                </div>
            </div>
        </div>
        
        <!-- Map Legend and Info Panel -->
        <div class="col-lg-3">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Map Legend</h6>
                </div>
                <div class="card-body">
                    <div class="legend-item mb-2">
                        <i class="fas fa-map-marker-alt text-success"></i>
                        <span class="ml-2">Normal Stock</span>
                    </div>
                    <div class="legend-item mb-2">
                        <i class="fas fa-map-marker-alt text-warning"></i>
                        <span class="ml-2">Low Stock</span>
                    </div>
                    <div class="legend-item mb-2">
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        <span class="ml-2">Out of Stock</span>
                    </div>
                </div>
            </div>

            <!-- Selected Location Info -->
            <div class="card shadow" id="locationInfo" style="display: none;">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">Location Details</h6>
                </div>
                <div class="card-body" id="locationDetails">
                    <!-- Location details will be populated here -->
                </div>
            </div>

            <!-- Statistics Panel -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-secondary">Map Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Normal Stock</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800" id="normalStockCount">0</div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Low Stock</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800" id="lowStockCount">0</div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Out of Stock</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800" id="outOfStockCount">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Details Modal -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalTitle">Product Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="productModalBody">
                <!-- Product details will be populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a href="#" id="viewProductBtn" class="btn btn-primary">View Product</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.legend-item {
    display: flex;
    align-items: center;
}

.leaflet-popup-content {
    font-size: 13px;
}

.status-badge {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-normal {
    background-color: #d4edda;
    color: #155724;
}

.status-low {
    background-color: #fff3cd;
    color: #856404;
}

.status-out {
    background-color: #f8d7da;
    color: #721c24;
}

#inventoryMap {
    border-radius: 0.35rem;
}
</style>
@endsection

@section('scripts')
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let map;
let markers = [];
let allProducts = [];
let currentFilter = 'all';

// Initialize map
function initMap() {
    // Create map centered on a default location (you can change this)
    map = L.map('inventoryMap').setView([40.7128, -74.0060], 10); // New York City

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Load product data
    loadProductData();
}

// Load product data from API
function loadProductData() {
    fetch('{{ route("api.products.map-data") }}')
        .then(response => response.json())
        .then(data => {
            allProducts = data;
            displayMarkers(data);
            updateStatistics(data);
            
            // Auto-fit map to show all markers
            if (data.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        })
        .catch(error => {
            console.error('Error loading product data:', error);
            showAlert('Failed to load inventory data', 'danger');
        });
}

// Display markers on map
function displayMarkers(products) {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];

    products.forEach(product => {
        // Determine marker color based on stock status
        let markerColor = getMarkerColor(product.status);
        
        // Create custom icon
        const customIcon = L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: ${markerColor}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 1px 3px rgba(0,0,0,0.3);"></div>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        // Create marker
        const marker = L.marker([product.latitude, product.longitude], {
            icon: customIcon
        }).addTo(map);

        // Create popup content
        const popupContent = createPopupContent(product);
        marker.bindPopup(popupContent);

        // Add click event for detailed view
        marker.on('click', () => showProductDetails(product));

        markers.push(marker);
    });
}

// Get marker color based on stock status
function getMarkerColor(status) {
    switch (status) {
        case 'normal': return '#28a745';
        case 'low_stock': return '#ffc107';
        case 'out_of_stock': return '#dc3545';
        default: return '#6c757d';
    }
}

// Create popup content
function createPopupContent(product) {
    const statusClass = product.status.replace('_', '-');
    const statusText = product.status.replace('_', ' ').toUpperCase();
    
    return `
        <div style="min-width: 200px;">
            <h6 class="font-weight-bold mb-2">${product.name}</h6>
            <p class="mb-1"><strong>SKU:</strong> ${product.sku}</p>
            <p class="mb-1"><strong>Location:</strong> ${product.location || 'Not specified'}</p>
            <p class="mb-1"><strong>Stock:</strong> ${product.stock_quantity} / ${product.minimum_stock_level} (min)</p>
            <p class="mb-2">
                <span class="status-badge status-${statusClass}">${statusText}</span>
            </p>
            <button class="btn btn-sm btn-primary" onclick="showProductDetails(${JSON.stringify(product).replace(/"/g, '&quot;')})">
                View Details
            </button>
        </div>
    `;
}

// Show product details in modal
function showProductDetails(product) {
    document.getElementById('productModalTitle').textContent = product.name;
    document.getElementById('viewProductBtn').href = product.url;
    
    const statusClass = product.status.replace('_', '-');
    const statusText = product.status.replace('_', ' ').toUpperCase();
    
    const modalBody = document.getElementById('productModalBody');
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Product Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>SKU:</strong></td><td>${product.sku}</td></tr>
                    <tr><td><strong>Name:</strong></td><td>${product.name}</td></tr>
                    <tr><td><strong>Location:</strong></td><td>${product.location || 'Not specified'}</td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="status-badge status-${statusClass}">${statusText}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Stock Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Current Stock:</strong></td><td>${product.stock_quantity}</td></tr>
                    <tr><td><strong>Minimum Level:</strong></td><td>${product.minimum_stock_level}</td></tr>
                    <tr><td><strong>Coordinates:</strong></td><td>${product.latitude.toFixed(6)}, ${product.longitude.toFixed(6)}</td></tr>
                </table>
            </div>
        </div>
        
        ${product.status !== 'normal' ? `
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle"></i>
                This product requires attention due to ${product.status === 'low_stock' ? 'low stock levels' : 'being out of stock'}.
            </div>
        ` : ''}
    `;
    
    $('#productModal').modal('show');
}

// Filter map markers
function filterMap(filter) {
    currentFilter = filter;
    
    let filteredProducts = allProducts;
    
    if (filter === 'low') {
        filteredProducts = allProducts.filter(p => p.status === 'low_stock');
    } else if (filter === 'out') {
        filteredProducts = allProducts.filter(p => p.status === 'out_of_stock');
    }
    
    displayMarkers(filteredProducts);
    updateStatistics(filteredProducts);
}

// Update statistics panel
function updateStatistics(products) {
    const normal = products.filter(p => p.status === 'normal').length;
    const lowStock = products.filter(p => p.status === 'low_stock').length;
    const outOfStock = products.filter(p => p.status === 'out_of_stock').length;
    
    document.getElementById('normalStockCount').textContent = normal;
    document.getElementById('lowStockCount').textContent = lowStock;
    document.getElementById('outOfStockCount').textContent = outOfStock;
}

// Show alert messages
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    document.querySelector('.container-fluid').prepend(alertDiv);
    
    // Auto-remove alert after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMap();
});

// Auto-refresh data every 2 minutes
setInterval(() => {
    loadProductData();
}, 120000);
</script>
@endsection
