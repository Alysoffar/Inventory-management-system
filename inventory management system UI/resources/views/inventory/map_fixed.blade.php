@extends('layouts.app')

@section('title', 'Live Tracking Dashboard')

@section('styles')
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
        position: relative;
        height: 600px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        overflow: hidden;
    }
    
    .moving-object {
        position: absolute;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        font-weight: bold;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        cursor: pointer;
        transition: transform 0.3s ease;
        z-index: 10;
    }
    
    .moving-object:hover {
        transform: scale(1.2);
        z-index: 20;
    }
    
    .supplier-truck {
        background: #ff6b35;
        width: 40px;
        height: 40px;
        border: 3px solid #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    
    .delivery-van {
        background: #28a745;
        width: 35px;
        height: 35px;
        border: 3px solid #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    
    .customer-car {
        background: #007bff;
        width: 30px;
        height: 30px;
        border: 3px solid #fff;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }
    
    .warehouse {
        position: absolute;
        background: #343a40;
        color: white;
        padding: 8px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        border: 3px solid #ff6b35;
        box-shadow: 0 6px 12px rgba(0,0,0,0.4);
    }
    
    .status-card {
        transition: all 0.2s ease;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    
    .status-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .delivery-status {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
    
    .status-delivered {
        background: #d4edda;
        color: #28a745;
    }
    
    .status-in-transit {
        background: #fff3cd;
        color: #ffc107;
    }
    
    .status-pending {
        background: #f8d7da;
        color: #dc3545;
    }
    
    .rating-stars {
        color: #ff6b35;
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sliders-h me-2"></i>Tracking Controls
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" onclick="toggleTracking('all')">
                                <i class="fas fa-eye me-1"></i>Show All
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-secondary w-100" onclick="toggleTracking('suppliers')">
                                <i class="fas fa-truck me-1"></i>Suppliers
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-secondary w-100" onclick="toggleTracking('deliveries')">
                                <i class="fas fa-shipping-fast me-1"></i>Deliveries
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-secondary w-100" onclick="toggleTracking('customers')">
                                <i class="fas fa-users me-1"></i>Customers
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-signal me-2"></i>Live Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="badge bg-success mb-1">Active</div>
                            <div class="fw-bold" id="activeCount">12</div>
                        </div>
                        <div class="col-4">
                            <div class="badge bg-warning mb-1">Transit</div>
                            <div class="fw-bold" id="transitCount">7</div>
                        </div>
                        <div class="col-4">
                            <div class="badge bg-danger mb-1">Delayed</div>
                            <div class="fw-bold" id="delayedCount">2</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Map and Tracking Panel -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map me-2"></i>Real-time Map
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="map-container" id="trackingMap">
                        <!-- Central Warehouse -->
                        <div class="warehouse" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <i class="fas fa-warehouse me-1"></i>Main Warehouse
                        </div>
                        
                        <!-- Moving Objects will be dynamically added here -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Delivery Status Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list-alt me-2"></i>Delivery Status
                    </h5>
                </div>
                <div class="card-body" id="deliveryStatusPanel">
                    <div class="status-card mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">GlobalTech Suppliers</h6>
                                <small class="text-muted">Laptops, Monitors, Keyboards</small>
                            </div>
                            <span class="delivery-status status-in-transit">in transit</span>
                        </div>
                        <small class="text-muted"><i class="fas fa-clock me-1"></i>ETA: 2 hours</small>
                    </div>
                    
                    <div class="status-card mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Express Delivery #1234</h6>
                                <small class="text-muted">Printers, Paper</small>
                            </div>
                            <span class="delivery-status status-delivered">delivered</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Customer Feedback Panel -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comments me-2"></i>Recent Feedback
                    </h5>
                </div>
                <div class="card-body" id="feedbackPanel">
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between mb-2">
                            <strong>Tech Corp</strong>
                            <div class="rating-stars small">★★★★★</div>
                        </div>
                        <p class="small mb-0 fst-italic">"Excellent service! Very fast delivery."</p>
                        <small class="text-muted">Delivered at 10:30 AM</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="trackingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tracking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Modal content will be populated here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Mock data for demonstration
const trackingData = {
    suppliers: [
        {
            id: 'sup1',
            name: 'GlobalTech Suppliers',
            type: 'supplier',
            status: 'in_transit',
            x: 10,
            y: 20,
            destination: { x: 50, y: 50 },
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
            x: 80,
            y: 30,
            destination: { x: 50, y: 50 },
            items: ['Office Chairs', 'Desks'],
            eta: '45 minutes',
            driver: 'Sarah Johnson',
            contact: '+1-555-0124'
        }
    ],
    deliveries: [
        {
            id: 'del1',
            name: 'Express Delivery #1234',
            type: 'delivery',
            status: 'delivered',
            x: 70,
            y: 80,
            destination: { x: 85, y: 85 },
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
            x: 30,
            y: 70,
            destination: { x: 15, y: 85 },
            customer: 'StartUp Ltd',
            items: ['Computers', 'Cables'],
            eta: '1.5 hours',
            driver: 'Mike Wilson'
        }
    ],
    customers: [
        {
            id: 'cust1',
            name: 'Premium Customer',
            type: 'customer',
            status: 'waiting',
            x: 85,
            y: 85,
            items: ['Urgent Order'],
            orderTime: '9:00 AM',
            priority: 'high'
        }
    ]
};

let animationIntervals = [];

function initializeMap() {
    const map = document.getElementById('trackingMap');
    
    // Clear existing objects
    const existingObjects = map.querySelectorAll('.moving-object');
    existingObjects.forEach(obj => obj.remove());
    
    // Add all objects
    Object.values(trackingData).forEach(category => {
        category.forEach(item => addMovingObject(item));
    });
    
    // Start animations
    startAnimations();
}

function addMovingObject(item) {
    const map = document.getElementById('trackingMap');
    const object = document.createElement('div');
    
    let className = 'moving-object ';
    let icon = '';
    
    switch(item.type) {
        case 'supplier':
            className += 'supplier-truck';
            icon = '<i class="fas fa-truck"></i>';
            break;
        case 'delivery':
            className += 'delivery-van';
            icon = '<i class="fas fa-shipping-fast"></i>';
            break;
        case 'customer':
            className += 'customer-car';
            icon = '<i class="fas fa-car"></i>';
            break;
    }
    
    object.className = className;
    object.innerHTML = icon;
    object.style.left = item.x + '%';
    object.style.top = item.y + '%';
    object.dataset.id = item.id;
    
    // Add click event
    object.addEventListener('click', () => showTrackingDetails(item));
    
    map.appendChild(object);
}

function startAnimations() {
    // Clear existing intervals
    animationIntervals.forEach(interval => clearInterval(interval));
    animationIntervals = [];
    
    // Animate supplier trucks
    trackingData.suppliers.forEach(supplier => {
        if (supplier.status === 'in_transit') {
            const interval = setInterval(() => {
                animateObject(supplier);
            }, 2000);
            animationIntervals.push(interval);
        }
    });
    
    // Animate delivery vans
    trackingData.deliveries.forEach(delivery => {
        if (delivery.status === 'in_transit') {
            const interval = setInterval(() => {
                animateObject(delivery);
            }, 1500);
            animationIntervals.push(interval);
        }
    });
}

function animateObject(item) {
    const object = document.querySelector(`[data-id="${item.id}"]`);
    if (!object) return;
    
    // Move towards destination
    const dx = item.destination.x - item.x;
    const dy = item.destination.y - item.y;
    const distance = Math.sqrt(dx*dx + dy*dy);
    
    if (distance > 2) {
        const speed = 1;
        item.x += (dx / distance) * speed;
        item.y += (dy / distance) * speed;
        
        object.style.left = item.x + '%';
        object.style.top = item.y + '%';
    } else {
        // Reached destination
        item.status = 'delivered';
        object.classList.add('pulse');
    }
}

function showTrackingDetails(item) {
    const modal = new bootstrap.Modal(document.getElementById('trackingModal'));
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    modalTitle.textContent = item.name || `${item.type.charAt(0).toUpperCase() + item.type.slice(1)} Details`;
    
    let content = `
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                <p><strong>Status:</strong> <span class="badge bg-${item.status === 'delivered' ? 'success' : item.status === 'in_transit' ? 'warning' : 'danger'}">${item.status.replace('_', ' ')}</span></p>
                <p><strong>Items:</strong> ${item.items ? item.items.join(', ') : 'N/A'}</p>
                ${item.eta ? `<p><strong>ETA:</strong> ${item.eta}</p>` : ''}
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-user me-2"></i>Contact Information</h6>
                ${item.driver ? `<p><strong>Driver:</strong> ${item.driver}</p>` : ''}
                ${item.contact ? `<p><strong>Contact:</strong> ${item.contact}</p>` : ''}
                ${item.customer ? `<p><strong>Customer:</strong> ${item.customer}</p>` : ''}
            </div>
        </div>
    `;
    
    if (item.feedback) {
        content += `
            <hr>
            <h6><i class="fas fa-star me-2"></i>Customer Feedback</h6>
            <div class="rating-stars mb-2">
                ${'★'.repeat(item.rating)}${'☆'.repeat(5-item.rating)}
            </div>
            <p class="fst-italic">"${item.feedback}"</p>
        `;
    }
    
    modalBody.innerHTML = content;
    modal.show();
}

function toggleTracking(type) {
    // Update button states
    const buttons = document.querySelectorAll('.card-body button');
    buttons.forEach(btn => btn.classList.remove('btn-primary'));
    buttons.forEach(btn => btn.classList.add('btn-secondary'));
    
    event.target.classList.remove('btn-secondary');
    event.target.classList.add('btn-primary');
    
    // Show/hide objects based on type
    const objects = document.querySelectorAll('.moving-object');
    objects.forEach(obj => {
        obj.style.display = 'flex';
        if (type !== 'all') {
            const objType = obj.className.includes('supplier') ? 'suppliers' :
                           obj.className.includes('delivery') ? 'deliveries' :
                           obj.className.includes('customer') ? 'customers' : '';
            
            if (objType !== type) {
                obj.style.display = 'none';
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
