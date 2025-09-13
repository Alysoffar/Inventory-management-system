@extends('layouts.app')

@section('title', 'Inventory Analytics Report')

@section('content')
<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-boxes me-3"></i>Inventory Analytics Report
            </h1>
            <p class="page-subtitle">Comprehensive inventory analysis with real-time stock monitoring</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ url('/reports/export/inventory-pdf') }}" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </a>
                <button class="btn btn-success" onclick="refreshData()">
                    <i class="fas fa-sync me-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row section-spacing">
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-archive fa-2x mb-3 text-primary"></i>
                <h3 class="text-primary">{{ $totalProducts ?? 0 }}</h3>
                <p class="mb-1">Total Products</p>
                <small class="text-dark">In inventory</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign fa-2x mb-3 text-success"></i>
                <h3 class="text-success">${{ number_format($totalValue ?? 0, 2) }}</h3>
                <p class="mb-1">Total Value</p>
                <small class="text-dark">Current inventory value</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-3 text-warning"></i>
                <h3 class="text-warning">{{ $lowStockItems ?? 0 }}</h3>
                <p class="mb-1">Low Stock Items</p>
                <small class="text-dark">Need restocking</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x mb-3 text-danger"></i>
                <h3 class="text-danger">{{ $outOfStockItems ?? 0 }}</h3>
                <p class="mb-1">Out of Stock</p>
                <small class="text-dark">Immediate attention</small>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row section-spacing">
    <!-- Inventory Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Stock Levels by Product
                </h5>
            </div>
            <div class="card-body">
                <canvas id="inventoryStockChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Inventory Insights
                </h5>
            </div>
            <div class="card-body">
                <div class="insight-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Turnover Rate</span>
                        <strong class="text-success">6.4x</strong>
                    </div>
                </div>
                <div class="insight-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Stock Accuracy</span>
                        <strong class="text-info">96.8%</strong>
                    </div>
                </div>
                <div class="insight-item">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Reorder Point</span>
                        <strong class="text-warning">{{ $lowStockItems ?? 0 }} Items</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Inventory Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Inventory Details</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Unit Price</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // If no products are passed from controller, use sample data
                                    $inventoryItems = $products ?? [
                                        (object)[
                                            'id' => 1,
                                            'name' => 'Widget A',
                                            'sku' => 'WID-001',
                                            'category' => 'Electronics',
                                            'stock_quantity' => 5,
                                            'price' => 25.99,
                                            'status' => 'low_stock'
                                        ],
                                        (object)[
                                            'id' => 2,
                                            'name' => 'Widget B',
                                            'sku' => 'WID-002',
                                            'category' => 'Tools',
                                            'stock_quantity' => 2,
                                            'price' => 45.50,
                                            'status' => 'low_stock'
                                        ],
                                        (object)[
                                            'id' => 3,
                                            'name' => 'Widget C',
                                            'sku' => 'WID-003',
                                            'category' => 'Supplies',
                                            'stock_quantity' => 8,
                                            'price' => 12.75,
                                            'status' => 'low_stock'
                                        ],
                                        (object)[
                                            'id' => 4,
                                            'name' => 'Widget D',
                                            'sku' => 'WID-004',
                                            'category' => 'Electronics',
                                            'stock_quantity' => 25,
                                            'price' => 89.99,
                                            'status' => 'in_stock'
                                        ],
                                        (object)[
                                            'id' => 5,
                                            'name' => 'Widget E',
                                            'sku' => 'WID-005',
                                            'category' => 'Supplies',
                                            'stock_quantity' => 50,
                                            'price' => 8.99,
                                            'status' => 'in_stock'
                                        ]
                                    ];
                                @endphp

                                @forelse($inventoryData['products'] as $item)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $item['name'] }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $item['sku'] }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $item['category'] }}</span>
                                    </td>
                                    <td>
                                        <strong class="{{ $item['stock_quantity'] <= 10 ? 'text-warning' : 'text-success' }}">
                                            {{ $item['stock_quantity'] }}
                                        </strong>
                                    </td>
                                    <td>
                                        ${{ number_format($item['cost_price'], 2) }}
                                    </td>
                                    <td>
                                        <strong>${{ number_format($item['total_value'], 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($item['stock_quantity'] == 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($item['stock_quantity'] <= $item['minimum_stock_level'])
                                            <span class="badge bg-warning text-dark">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">In Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('products.show', $item['id']) }}">
                                                    <i class="ri-eye-line me-2"></i> View Details
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('products.edit', $item['id']) }}">
                                                    <i class="ri-edit-line me-2"></i> Edit Product
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="#" onclick="adjustStock({{ $item['id'] }}, '{{ addslashes($item['name']) }}', {{ $item['stock_quantity'] }})">
                                                    <i class="ri-add-circle-line me-2"></i> Adjust Stock
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="py-4">
                                            <i class="ri-archive-line display-4 text-muted"></i>
                                            <h5 class="mt-3">No Inventory Data</h5>
                                            <p class="text-muted">No inventory items found for the current filters.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adjustStockModalLabel">
                    <i class="fas fa-box me-2"></i>Adjust Stock
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="stockProductId">
                
                <div class="mb-3">
                    <label class="form-label"><strong>Product:</strong></label>
                    <div id="stockProductName" class="text-muted"></div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><strong>Current Stock:</strong></label>
                    <div id="stockCurrentQuantity" class="text-info fw-bold"></div>
                </div>
                
                <div class="mb-3">
                    <label for="adjustmentType" class="form-label">Adjustment Type</label>
                    <select class="form-select" id="adjustmentType" required>
                        <option value="increase">Increase Stock</option>
                        <option value="decrease">Decrease Stock</option>
                        <option value="set">Set Exact Quantity</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="adjustmentQuantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="adjustmentQuantity" min="1" required 
                           placeholder="Enter quantity">
                </div>
                
                <div class="mb-3">
                    <label for="adjustmentLocation" class="form-label">Location (Optional)</label>
                    <input type="text" class="form-control" id="adjustmentLocation" 
                           placeholder="Enter location">
                </div>
                
                <div class="mb-3">
                    <label for="adjustmentNotes" class="form-label">Notes (Optional)</label>
                    <textarea class="form-control" id="adjustmentNotes" rows="3" 
                              placeholder="Enter reason for adjustment"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitStockAdjustment" onclick="submitStockAdjustment()">
                    <i class="fas fa-save me-2"></i>Adjust Stock
                </button>
            </div>
        </div>
    </div>
</div>

    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Debug: Log all passed variables
    console.log('=== INVENTORY DEBUG INFO ===');
    console.log('totalProducts:', {{ $totalProducts ?? 0 }});
    console.log('totalValue:', {{ $totalValue ?? 0 }});
    console.log('lowStockItems:', {{ $lowStockItems ?? 0 }});
    console.log('outOfStockItems:', {{ $outOfStockItems ?? 0 }});

    // Prepare data for the inventory stock chart
    const inventoryData = @json($inventoryData ?? ['products' => []]);
    console.log('Raw inventory data:', inventoryData);

    let inventoryLabels = [];
    let inventoryStock = [];

    if (inventoryData && inventoryData.products && inventoryData.products.length > 0) {
        inventoryLabels = inventoryData.products.slice(0, 10).map(item => item.name || 'Unknown Product');
        inventoryStock = inventoryData.products.slice(0, 10).map(item => parseInt(item.stock_quantity) || 0);
    }

    console.log('Final Labels:', inventoryLabels);
    console.log('Final Stock:', inventoryStock);
    console.log('=== END DEBUG INFO ===');

    const ctx = document.getElementById('inventoryStockChart');
    
    if (!ctx) {
        console.error('Chart canvas not found');
        return;
    }
    
    if (inventoryLabels.length > 0 && inventoryStock.length > 0) {
        console.log('Creating chart with data:', { labels: inventoryLabels, data: inventoryStock });
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: inventoryLabels,
                datasets: [{
                    label: 'Stock Quantity',
                    data: inventoryStock,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                        '#9966FF', '#FF9F40', '#C7C7C7', '#5382FF'
                    ],
                    borderColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                        '#9966FF', '#FF9F40', '#C7C7C7', '#5382FF'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                return `${label}: ${value} units (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } else {
        console.log('No inventory data available for chart - showing message');
        
        // Create a message in the chart container
        const chartContainer = ctx.parentElement;
        const messageDiv = document.createElement('div');
        messageDiv.className = 'text-center p-4';
        messageDiv.innerHTML = `
            <i class="fas fa-chart-pie fa-3x text-muted mb-3"></i>
            <p class="text-muted">No inventory data available for chart</p>
            <small class="text-muted">Add products to see the stock distribution</small>
        `;
        
        // Hide the canvas and show the message
        ctx.style.display = 'none';
        chartContainer.appendChild(messageDiv);
    }
});

function refreshData() {
    location.reload();
}

function adjustStock(productId, productName, currentStock) {
    console.log('adjustStock called with:', { productId, productName, currentStock });
    
    // Check if modal elements exist
    const stockProductIdElement = document.getElementById('stockProductId');
    const stockProductNameElement = document.getElementById('stockProductName');
    const stockCurrentQuantityElement = document.getElementById('stockCurrentQuantity');
    
    if (!stockProductIdElement || !stockProductNameElement || !stockCurrentQuantityElement) {
        console.error('Modal elements not found');
        alert('Error: Modal elements not found. Please refresh the page and try again.');
        return;
    }
    
    // Set up the modal with product info
    stockProductIdElement.value = productId;
    stockProductNameElement.textContent = productName;
    stockCurrentQuantityElement.textContent = currentStock + ' units';
    
    // Reset form
    document.getElementById('adjustmentType').value = 'increase';
    document.getElementById('adjustmentQuantity').value = '';
    document.getElementById('adjustmentNotes').value = '';
    document.getElementById('adjustmentLocation').value = '';
    
    // Show modal
    try {
        const modalElement = document.getElementById('adjustStockModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Modal should be visible now');
        } else {
            console.error('Modal element not found');
            alert('Error: Modal not found. Please refresh the page and try again.');
        }
    } catch (error) {
        console.error('Error showing modal:', error);
        alert('Error showing modal: ' + error.message);
    }
}

function submitStockAdjustment() {
    console.log('submitStockAdjustment called');
    
    const productId = document.getElementById('stockProductId').value;
    const adjustmentType = document.getElementById('adjustmentType').value;
    const quantity = document.getElementById('adjustmentQuantity').value;
    const notes = document.getElementById('adjustmentNotes').value;
    const location = document.getElementById('adjustmentLocation').value;
    
    console.log('Form data:', { productId, adjustmentType, quantity, notes, location });
    
    if (!quantity || quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }
    
    // Check CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found');
        alert('Error: Security token not found. Please refresh the page and try again.');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitStockAdjustment');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adjusting...';
    submitBtn.disabled = true;
    
    console.log('Making API request to:', `/products/${productId}/adjust-stock`);
    
    // Submit the adjustment
    fetch(`/products/${productId}/adjust-stock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: JSON.stringify({
            adjustment_type: adjustmentType,
            quantity: parseInt(quantity),
            notes: notes,
            location: location
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            // Close modal
            const modalElement = document.getElementById('adjustStockModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            
            // Show success message
            alert(`Stock adjusted successfully! Changed from ${data.old_quantity} to ${data.new_quantity} units.`);
            
            // Refresh the page to show updated data
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to adjust stock'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error occurred while adjusting stock: ' + error.message);
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
</script>
@endsection