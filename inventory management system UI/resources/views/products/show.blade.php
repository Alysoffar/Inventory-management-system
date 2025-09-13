@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 text-dark">Product Details</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">{{ $product->name }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <!-- Product Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Product Information</h6>
                    <div>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>Edit Product
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Products
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="120">Name:</th>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <th>SKU:</th>
                                    <td><code>{{ $product->sku }}</code></td>
                                </tr>
                                <tr>
                                    <th>Category:</th>
                                    <td>{{ $product->category }}</td>
                                </tr>
                                <tr>
                                    <th>Price:</th>
                                    <td><strong>${{ number_format($product->price, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Cost Price:</th>
                                    <td>${{ number_format($product->cost_price ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Supplier:</th>
                                    <td>{{ $product->supplier->name ?? 'Not assigned' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="140">Current Stock:</th>
                                    <td>
                                        <span class="badge 
                                            @if($product->stock_quantity == 0) bg-danger
                                            @elseif($product->stock_quantity <= $product->minimum_stock_level) bg-warning text-dark
                                            @else bg-success
                                            @endif">
                                            {{ $product->stock_quantity }} units
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Minimum Level:</th>
                                    <td>{{ $product->minimum_stock_level }} units</td>
                                </tr>
                                <tr>
                                    <th>Maximum Level:</th>
                                    <td>{{ $product->maximum_stock_level ?? 'Not set' }} units</td>
                                </tr>
                                <tr>
                                    <th>Location:</th>
                                    <td>{{ $product->location ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge {{ $product->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Value:</th>
                                    <td><strong>${{ number_format($product->stock_quantity * $product->price, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if($product->description)
                    <div class="mt-3">
                        <h6>Description:</h6>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Inventory Logs -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Recent Inventory Activity</h6>
                </div>
                <div class="card-body">
                    @if($recentLogs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Quantity Change</th>
                                    <th>Previous Stock</th>
                                    <th>New Stock</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge 
                                            @switch($log->type)
                                                @case('sale') bg-info @break
                                                @case('purchase') bg-success @break
                                                @case('adjustment') bg-warning text-dark @break
                                                @case('restock') bg-primary @break
                                                @default bg-secondary
                                            @endswitch">
                                            {{ ucfirst($log->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-{{ $log->quantity_changed > 0 ? 'success' : 'danger' }}">
                                            {{ $log->quantity_changed > 0 ? '+' : '' }}{{ $log->quantity_changed }}
                                        </span>
                                    </td>
                                    <td>{{ $log->previous_stock }}</td>
                                    <td>{{ $log->new_stock }}</td>
                                    <td>{{ $log->notes ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No inventory activity recorded yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar with Actions and Stats -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="adjustStock({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock_quantity }})">
                            <i class="fas fa-box me-2"></i>Adjust Stock
                        </button>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-success">
                            <i class="fas fa-edit me-2"></i>Edit Product
                        </a>
                        <button class="btn btn-outline-info">
                            <i class="fas fa-chart-line me-2"></i>View Analytics
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stock Movement Summary -->
            @if($stockMovements->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Stock Activity (Last 30 Days)</h6>
                </div>
                <div class="card-body">
                    @foreach($stockMovements as $movement)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-capitalize">{{ $movement->type }}:</span>
                        <div>
                            <span class="badge bg-light text-dark">{{ $movement->count }} times</span>
                            <span class="fw-bold">{{ $movement->total_quantity }} units</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Include the same stock adjustment modal -->
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
                           placeholder="Enter location" value="{{ $product->location }}">
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

<script>
function adjustStock(productId, productName, currentStock) {
    // Set up the modal with product info
    document.getElementById('stockProductId').value = productId;
    document.getElementById('stockProductName').textContent = productName;
    document.getElementById('stockCurrentQuantity').textContent = currentStock;
    
    // Reset form
    document.getElementById('adjustmentType').value = 'increase';
    document.getElementById('adjustmentQuantity').value = '';
    document.getElementById('adjustmentNotes').value = '';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('adjustStockModal'));
    modal.show();
}

function submitStockAdjustment() {
    const productId = document.getElementById('stockProductId').value;
    const adjustmentType = document.getElementById('adjustmentType').value;
    const quantity = document.getElementById('adjustmentQuantity').value;
    const notes = document.getElementById('adjustmentNotes').value;
    const location = document.getElementById('adjustmentLocation').value;
    
    if (!quantity || quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitStockAdjustment');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adjusting...';
    submitBtn.disabled = true;
    
    // Submit the adjustment
    fetch(`/products/${productId}/adjust-stock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            adjustment_type: adjustmentType,
            quantity: parseInt(quantity),
            notes: notes,
            location: location
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('adjustStockModal'));
            modal.hide();
            
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
        alert('Network error occurred while adjusting stock');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}
</script>
@endsection