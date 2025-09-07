@extends('layouts.app')

@section('title', 'Live Tracking Dashboard')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

<style>
    * {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .page-title {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .page-subtitle {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        font-weight: 400;
        color: #6c757d;
    }
    
    .card-title {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        font-weight: 600;
    }
    
    .btn {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        font-weight: 500;
    }

    .map-container {
        background: transparent;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        position: relative;
    }
    
    #trackingMap {
        width: 100%;
        height: 500px;
        border-radius: 12px;
        z-index: 1;
    }
    
    /* Custom Leaflet marker styles */
    .moving-marker {
        background: white;
        border-radius: 50%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        animation: bounce 2s infinite;
        z-index: 1000;
    }
    
    .supplier-marker {
        background: linear-gradient(135deg, #ff6b35, #f7931e);
        color: white;
    }
    
    .delivery-marker {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .truck-marker {
        background: linear-gradient(135deg, #6c757d, #495057);
        color: white;
        border: 2px solid #343a40;
    }
    
    .customer-marker {
        background: linear-gradient(135deg, #007bff, #6f42c1);
        color: white;
    }
    
    .warehouse-marker {
        background: linear-gradient(135deg, #dc3545, #fd7e14);
        color: white;
        border-radius: 8px;
        padding: 8px 12px;
        font-weight: bold;
        font-size: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    
    .destination-marker {
        background: #ffc107;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        animation: pulse 2s infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
    
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
        }
    }
    
    /* Status cards */
    .status-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .delivery-status {
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-delivered {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-in-transit {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-pending {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .status-approaching {
        background: #cce7ff;
        color: #004085;
        border: 1px solid #b3d7ff;
    }
    
    .status-waiting {
        background: #e2e3e5;
        color: #383d41;
        border: 1px solid #d1d3d4;
    }
    
    .rating-stars {
        color: #ffc107;
        font-size: 14px;
    }
    
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="page-header mb-4">
        <h1 class="page-title">
            <i class="fas fa-map-marked-alt me-2"></i>Live Tracking Dashboard
        </h1>
        <p class="page-subtitle">Real-time monitoring of deliveries, suppliers, and inventory movement</p>
    </div>

    <!-- Control Panel -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-value text-success" id="activeCount">12</div>
                    <div class="stat-label">Active Deliveries</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-value text-warning" id="transitCount">7</div>
                    <div class="stat-label">In Transit</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-value text-danger" id="delayedCount">2</div>
                    <div class="stat-label">Delayed</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-value text-info">98%</div>
                    <div class="stat-label">On-Time Rate</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-filter me-2"></i>Tracking Filters
                    </h5>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" onclick="toggleTracking('all')">
                            <i class="fas fa-globe me-1"></i>All Objects
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="toggleTracking('suppliers')">
                            <i class="fas fa-truck me-1"></i>Suppliers
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="toggleTracking('deliveries')">
                            <i class="fas fa-shipping-fast me-1"></i>Deliveries
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="toggleTracking('customers')">
                            <i class="fas fa-car me-1"></i>Customers
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Live Map -->
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map me-2"></i>Live Map Tracking
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="map-container">
                        <div id="trackingMap"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="col-lg-4">
            <!-- Delivery Status Panel -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Delivery Status
                    </h5>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;" id="deliveryStatusPanel">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>

            <!-- Customer Feedback Panel -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comments me-2"></i>Recent Feedback
                    </h5>
                </div>
                <div class="card-body" style="max-height: 250px; overflow-y: auto;" id="feedbackPanel">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
// Initialize map
let map;
let markers = [];
let animationIntervals = [];

// Real coordinates for different cities (you can change this to your preferred location)
const mapCenter = [40.7128, -74.0060]; // New York City
// Alternative locations:
// [51.5074, -0.1278] // London
// [34.0522, -118.2437] // Los Angeles
// [25.7617, -80.1918] // Miami

const trackingData = {
    suppliers: [
        {
            id: 'sup1',
            name: 'GlobalTech Suppliers',
            type: 'supplier',
            status: 'in_transit',
            lat: 40.7589, lng: -73.9851, // Near Central Park
            destination: { lat: 40.7128, lng: -74.0060 }, // To warehouse
            items: ['Laptops', 'Monitors', 'Keyboards'],
            eta: '2 hours',
            driver: 'John Smith',
            contact: '+1-555-0123'
        },
        {
            id: 'sup2',
            name: 'FastDelivery Inc',
            type: 'supplier',
            status: 'approaching',
            lat: 40.6782, lng: -73.9442, // Brooklyn
            destination: { lat: 40.7128, lng: -74.0060 }, // To warehouse
            items: ['Office Chairs', 'Desks'],
            eta: '45 minutes',
            driver: 'Sarah Johnson',
            contact: '+1-555-0124'
        },
        {
            id: 'sup3',
            name: 'MegaTruck Express',
            type: 'supplier',
            status: 'in_transit',
            lat: 40.8176, lng: -73.9482, // Bronx area
            destination: { lat: 40.7128, lng: -74.0060 }, // To warehouse
            items: ['Heavy Machinery', 'Industrial Equipment'],
            eta: '3.5 hours',
            driver: 'Mike Rodriguez',
            contact: '+1-555-0125'
        },
        {
            id: 'sup4',
            name: 'QuickSupply Trucks',
            type: 'supplier',
            status: 'loading',
            lat: 40.6892, lng: -74.0445, // Staten Island
            destination: { lat: 40.7128, lng: -74.0060 }, // To warehouse
            items: ['Office Supplies', 'Stationery'],
            eta: '1.2 hours',
            driver: 'Lisa Chen',
            contact: '+1-555-0126'
        },
        {
            id: 'sup5',
            name: 'TechTruck Logistics',
            type: 'supplier',
            status: 'in_transit',
            lat: 40.7282, lng: -73.7949, // Queens
            destination: { lat: 40.7128, lng: -74.0060 }, // To warehouse
            items: ['Servers', 'Network Equipment'],
            eta: '2.8 hours',
            driver: 'David Kumar',
            contact: '+1-555-0127'
        }
    ],
    deliveries: [
        {
            id: 'del1',
            name: 'Express Delivery #1234',
            type: 'delivery',
            status: 'delivered',
            lat: 40.7505, lng: -73.9934, // Times Square area
            destination: { lat: 40.7614, lng: -73.9776 },
            customer: 'Tech Corp',
            items: ['Printers', 'Paper'],
            deliveryTime: '10:30 AM',
            rating: 5,
            feedback: 'Excellent service! Very fast delivery.'
        },
        {
            id: 'del2',
            name: 'Standard Delivery #5678',
            type: 'delivery',
            status: 'in_transit',
            lat: 40.7282, lng: -74.0776, // Jersey side
            destination: { lat: 40.7549, lng: -73.9840 },
            customer: 'StartUp Ltd',
            items: ['Computers', 'Cables'],
            eta: '1.5 hours',
            driver: 'Mike Wilson'
        },
        {
            id: 'del3',
            name: 'Priority Delivery #9876',
            type: 'delivery',
            status: 'in_transit',
            lat: 40.7831, lng: -73.9712, // Upper Manhattan
            destination: { lat: 40.6892, lng: -73.9442 }, // Brooklyn destination
            customer: 'Brooklyn Solutions',
            items: ['Emergency Parts', 'Tools'],
            eta: '45 minutes',
            driver: 'Emma Thompson',
            contact: '+1-555-0128'
        },
        {
            id: 'del4',
            name: 'Bulk Delivery #4321',
            type: 'delivery',
            status: 'loading',
            lat: 40.7128, lng: -74.0060, // Starting from warehouse
            destination: { lat: 40.8176, lng: -73.9482 }, // To Bronx
            customer: 'Bronx Manufacturing',
            items: ['Raw Materials', 'Components'],
            eta: '2 hours',
            driver: 'Carlos Martinez',
            contact: '+1-555-0129'
        },
        {
            id: 'del5',
            name: 'Rush Delivery #7890',
            type: 'delivery',
            status: 'in_transit',
            lat: 40.7505, lng: -74.0134, // Lower Manhattan
            destination: { lat: 40.7282, lng: -73.7949 }, // To Queens
            customer: 'Queens Tech Hub',
            items: ['Urgent Repairs', 'Replacement Parts'],
            eta: '1.8 hours',
            driver: 'Anna Kim',
            contact: '+1-555-0130'
        },
        {
            id: 'del6',
            name: 'Heavy Cargo #3456',
            type: 'delivery',
            status: 'approaching',
            lat: 40.7614, lng: -73.9776, // Upper West Side
            destination: { lat: 40.6892, lng: -74.0445 }, // To Staten Island
            customer: 'Island Industries',
            items: ['Industrial Machinery', 'Heavy Equipment'],
            eta: '3 hours',
            driver: 'Robert Singh',
            contact: '+1-555-0131'
        }
    ],
    customers: [
        {
            id: 'cust1',
            name: 'Premium Customer',
            type: 'customer',
            status: 'waiting',
            lat: 40.7614, lng: -73.9776, // Upper West Side
            items: ['Urgent Order'],
            orderTime: '9:00 AM',
            priority: 'high'
        },
        {
            id: 'cust2',
            name: 'Corporate Client',
            type: 'customer',
            status: 'scheduled',
            lat: 40.6892, lng: -73.9442, // Brooklyn
            items: ['Bulk Order', 'Office Setup'],
            orderTime: '2:00 PM',
            priority: 'medium'
        }
    ],
    trucks: [
        {
            id: 'truck1',
            name: '18-Wheeler #001',
            type: 'truck',
            status: 'in_transit',
            lat: 40.8002, lng: -74.0431, // Hoboken area
            destination: { lat: 40.7128, lng: -74.0060 }, // To warehouse
            items: ['Bulk Cargo', 'Large Equipment'],
            eta: '2.5 hours',
            driver: 'Frank Johnson',
            contact: '+1-555-0132',
            capacity: '40 tons'
        },
        {
            id: 'truck2',
            name: 'Refrigerated Truck #205',
            type: 'truck',
            status: 'loading',
            lat: 40.7128, lng: -74.0060, // Starting from warehouse
            destination: { lat: 40.7831, lng: -73.9712 }, // To Upper Manhattan
            items: ['Temperature Sensitive Goods', 'Electronics'],
            eta: '1.2 hours',
            driver: 'Maria Santos',
            contact: '+1-555-0133',
            capacity: '15 tons',
            temperature: '-18°C'
        },
        {
            id: 'truck3',
            name: 'Flatbed Truck #308',
            type: 'truck',
            status: 'in_transit',
            lat: 40.6350, lng: -74.0850, // Bay Ridge area
            destination: { lat: 40.7282, lng: -73.7949 }, // To Queens
            items: ['Construction Materials', 'Steel Beams'],
            eta: '4 hours',
            driver: 'Tony Rivera',
            contact: '+1-555-0134',
            capacity: '25 tons'
        },
        {
            id: 'truck4',
            name: 'Box Truck #412',
            type: 'truck',
            status: 'returning',
            lat: 40.7505, lng: -73.9934, // Times Square
            destination: { lat: 40.7128, lng: -74.0060 }, // Back to warehouse
            items: ['Empty - Returning'],
            eta: '30 minutes',
            driver: 'Steve Wilson',
            contact: '+1-555-0135',
            capacity: '5 tons'
        },
        {
            id: 'truck5',
            name: 'Tanker Truck #520',
            type: 'truck',
            status: 'in_transit',
            lat: 40.8002, lng: -73.9378, // Bronx River
            destination: { lat: 40.6892, lng: -74.0445 }, // To Staten Island
            items: ['Liquid Chemicals', 'Industrial Fluids'],
            eta: '5.5 hours',
            driver: 'Ahmed Hassan',
            contact: '+1-555-0136',
            capacity: '30,000 liters'
        }
    ]
};

function initializeMap() {
    // Initialize Leaflet map
    map = L.map('trackingMap').setView(mapCenter, 12);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Add warehouse marker
    const warehouseIcon = L.divIcon({
        className: 'warehouse-marker',
        html: '<i class="fas fa-warehouse"></i> Warehouse',
        iconSize: [120, 40],
        iconAnchor: [60, 20]
    });
    
    L.marker(mapCenter, { icon: warehouseIcon })
        .addTo(map)
        .bindPopup('<b>Main Warehouse</b><br>Distribution Center');
    
    // Add all moving objects
    Object.values(trackingData).forEach(category => {
        category.forEach(item => addMovingMarker(item));
    });
    
    // Start animations
    startAnimations();
    updateStatusPanels();
}

function addMovingMarker(item) {
    let iconClass = '';
    let iconHTML = '';
    
    switch(item.type) {
        case 'supplier':
            iconClass = 'supplier-marker';
            iconHTML = '<i class="fas fa-truck"></i>';
            break;
        case 'delivery':
            iconClass = 'delivery-marker';
            iconHTML = '<i class="fas fa-shipping-fast"></i>';
            break;
        case 'truck':
            iconClass = 'truck-marker';
            iconHTML = '<i class="fas fa-truck-loading"></i>';
            break;
        case 'customer':
            iconClass = 'customer-marker';
            iconHTML = '<i class="fas fa-car"></i>';
            break;
    }
    
    const customIcon = L.divIcon({
        className: `moving-marker ${iconClass}`,
        html: iconHTML,
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });
    
    const marker = L.marker([item.lat, item.lng], { icon: customIcon })
        .addTo(map)
        .bindPopup(createPopupContent(item));
    
    marker.itemData = item;
    markers.push(marker);
    
    // Add destination marker if exists
    if (item.destination) {
        const destIcon = L.divIcon({
            className: 'destination-marker',
            html: '📍',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
        
        L.marker([item.destination.lat, item.destination.lng], { icon: destIcon })
            .addTo(map)
            .bindPopup(`<b>Destination for ${item.name}</b>`);
        
        // Draw route line
        const polyline = L.polyline([
            [item.lat, item.lng],
            [item.destination.lat, item.destination.lng]
        ], {
            color: item.type === 'supplier' ? '#ff6b35' : 
                   item.type === 'delivery' ? '#28a745' : '#007bff',
            weight: 3,
            opacity: 0.7,
            dashArray: '10, 10'
        }).addTo(map);
    }
}

function addCustomerMarker(item) {
    const iconClass = 'customer-marker';
    const iconHTML = '<i class="fas fa-store"></i>';
    
    const customIcon = L.divIcon({
        className: `moving-marker ${iconClass}`,
        html: iconHTML,
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });
    
    const marker = L.marker([item.lat, item.lng], { icon: customIcon })
        .addTo(map)
        .bindPopup(createPopupContent(item));
    
    marker.itemData = item;
    markers.push(marker);
}

function createPopupContent(item) {
    let content = `<b>${item.name}</b><br>`;
    content += `<strong>Status:</strong> ${item.status}<br>`;
    content += `<strong>Items:</strong> ${item.items ? item.items.join(', ') : 'N/A'}<br>`;
    
    if (item.eta) content += `<strong>ETA:</strong> ${item.eta}<br>`;
    if (item.driver) content += `<strong>Driver:</strong> ${item.driver}<br>`;
    if (item.contact) content += `<strong>Contact:</strong> ${item.contact}<br>`;
    if (item.capacity) content += `<strong>Capacity:</strong> ${item.capacity}<br>`;
    if (item.temperature) content += `<strong>Temperature:</strong> ${item.temperature}<br>`;
    if (item.customer) content += `<strong>Customer:</strong> ${item.customer}<br>`;
    
    if (item.feedback) {
        content += `<hr><strong>Rating:</strong> ${'★'.repeat(item.rating)}${'☆'.repeat(5-item.rating)}<br>`;
        content += `<em>"${item.feedback}"</em>`;
    }
    
    return content;
}

function startAnimations() {
    // Clear existing intervals
    animationIntervals.forEach(interval => clearInterval(interval));
    animationIntervals = [];
    
    // Animate moving objects
    markers.forEach(marker => {
        const item = marker.itemData;
        if (item.status === 'in_transit' && item.destination) {
            const interval = setInterval(() => {
                animateMarker(marker, item);
            }, 2000);
            animationIntervals.push(interval);
        }
    });
}

function animateMarker(marker, item) {
    const currentPos = marker.getLatLng();
    const destLat = item.destination.lat;
    const destLng = item.destination.lng;
    
    // Calculate distance
    const latDiff = destLat - currentPos.lat;
    const lngDiff = destLng - currentPos.lng;
    const distance = Math.sqrt(latDiff * latDiff + lngDiff * lngDiff);
    
    if (distance > 0.001) { // Still moving
        const speed = 0.0005; // Adjust speed as needed
        const newLat = currentPos.lat + (latDiff / distance) * speed;
        const newLng = currentPos.lng + (lngDiff / distance) * speed;
        
        // Update marker position
        marker.setLatLng([newLat, newLng]);
        item.lat = newLat;
        item.lng = newLng;
    } else {
        // Reached destination
        item.status = 'delivered';
        marker.bindPopup(createPopupContent(item));
    }
}

function updateStatusPanels() {
    // Update delivery status panel
    const deliveryPanel = document.getElementById('deliveryStatusPanel');
    let deliveryHTML = '';
    
    [...trackingData.deliveries, ...trackingData.suppliers, ...trackingData.trucks].forEach(item => {
        const statusClass = item.status === 'delivered' ? 'status-delivered' : 
                           item.status === 'in_transit' ? 'status-in-transit' : 'status-pending';
        
        deliveryHTML += `
            <div class="status-card mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">${item.name}</h6>
                        <small class="text-muted">${item.items ? item.items.join(', ') : ''}</small>
                    </div>
                    <span class="delivery-status ${statusClass}">${item.status.replace('_', ' ')}</span>
                </div>
                ${item.eta ? `<small class="text-muted"><i class="fas fa-clock me-1"></i>ETA: ${item.eta}</small>` : ''}
            </div>
        `;
    });
    
    deliveryPanel.innerHTML = deliveryHTML;
    
    // Update feedback panel
    const feedbackPanel = document.getElementById('feedbackPanel');
    let feedbackHTML = '';
    
    trackingData.deliveries.filter(d => d.feedback).forEach(delivery => {
        feedbackHTML += `
            <div class="mb-3 p-3 border rounded">
                <div class="d-flex justify-content-between mb-2">
                    <strong>${delivery.customer}</strong>
                    <div class="rating-stars small">
                        ${'★'.repeat(delivery.rating)}${'☆'.repeat(5-delivery.rating)}
                    </div>
                </div>
                <p class="small mb-0 fst-italic">"${delivery.feedback}"</p>
                <small class="text-muted">Delivered at ${delivery.deliveryTime}</small>
            </div>
        `;
    });
    
    if (!feedbackHTML) {
        feedbackHTML = '<p class="text-muted">No recent feedback available.</p>';
    }
    
    feedbackPanel.innerHTML = feedbackHTML;
}

function toggleTracking(type) {
    // Update button states
    const buttons = document.querySelectorAll('.card-body button');
    buttons.forEach(btn => btn.classList.remove('btn-primary'));
    buttons.forEach(btn => btn.classList.add('btn-secondary'));
    
    event.target.classList.remove('btn-secondary');
    event.target.classList.add('btn-primary');
    
    // Show/hide markers based on type
    markers.forEach(marker => {
        const item = marker.itemData;
        if (type === 'all') {
            map.addLayer(marker);
        } else {
            const itemType = item.type + 's'; // Convert to plural
            if (itemType === type) {
                map.addLayer(marker);
            } else {
                map.removeLayer(marker);
            }
        }
    });
}

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    
    // Update counts every few seconds
    setInterval(() => {
        document.getElementById('activeCount').textContent = Math.floor(Math.random() * 5) + 10;
        document.getElementById('transitCount').textContent = Math.floor(Math.random() * 3) + 5;
        document.getElementById('delayedCount').textContent = Math.floor(Math.random() * 3);
    }, 5000);
});
</script>
@endsection
