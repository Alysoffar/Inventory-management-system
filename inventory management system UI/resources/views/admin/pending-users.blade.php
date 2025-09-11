@extends('layouts.app')

@section('title', 'Pending User Approvals')

@section('content')
<style>
/* AWS Cloudscape Design System */
:root {
    --aws-color-blue-600: #146eb4;
    --aws-color-blue-700: #0972d3;
    --aws-color-grey-900: #16191f;
    --aws-color-grey-600: #5f6b7a;
    --aws-color-grey-200: #e9ebed;
    --aws-color-green-600: #037f0c;
    --aws-color-orange-600: #b7740e;
    --aws-color-red-600: #d13212;
}

.admin-panel .card {
    border: 1px solid var(--aws-color-grey-200);
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.admin-panel .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.admin-panel .card-body {
    padding: 24px;
}

.admin-panel .page-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--aws-color-grey-900);
    margin-bottom: 24px;
}

.admin-panel .user-card {
    margin-bottom: 20px;
    border-left: 4px solid var(--aws-color-orange-600);
}

.admin-panel .user-info h5 {
    color: var(--aws-color-grey-900);
    font-weight: 600;
    margin-bottom: 8px;
}

.admin-panel .user-details {
    color: var(--aws-color-grey-600);
    font-size: 14px;
    margin-bottom: 16px;
}

.admin-panel .btn-approve {
    background-color: var(--aws-color-green-600);
    border-color: var(--aws-color-green-600);
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    margin-right: 8px;
}

.admin-panel .btn-approve:hover {
    background-color: #025c09;
    border-color: #025c09;
}

.admin-panel .btn-reject {
    background-color: var(--aws-color-red-600);
    border-color: var(--aws-color-red-600);
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
}

.admin-panel .btn-reject:hover {
    background-color: #b02a0e;
    border-color: #b02a0e;
}

.admin-panel .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--aws-color-grey-600);
}

.admin-panel .empty-state i {
    font-size: 3rem;
    margin-bottom: 16px;
    color: var(--aws-color-grey-200);
}
</style>

<div class="admin-panel">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1 class="page-title">
                    <i class="fas fa-user-clock"></i> Pending User Approvals
                </h1>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($pendingUsers->count() > 0)
                    @foreach($pendingUsers as $user)
                        <div class="card user-card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="user-info">
                                            <h5>{{ $user->name }}</h5>
                                            <div class="user-details">
                                                <div><strong>Email:</strong> {{ $user->email }}</div>
                                                <div><strong>Company:</strong> {{ $user->company }}</div>
                                                @if($user->phone)
                                                    <div><strong>Phone:</strong> {{ $user->phone }}</div>
                                                @endif
                                                <div><strong>Registration Date:</strong> {{ $user->created_at->format('F j, Y \a\t g:i A') }}</div>
                                                <div><strong>Status:</strong> 
                                                    <span class="badge bg-warning">Pending Approval</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <form method="POST" action="{{ route('admin.approve-user', $user->id) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-approve" onclick="return confirm('Are you sure you want to approve this user?')">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('admin.reject-user', $user->id) }}" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-reject" onclick="return confirm('Are you sure you want to reject and remove this user?')">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="card">
                        <div class="card-body">
                            <div class="empty-state">
                                <i class="fas fa-clipboard-check"></i>
                                <h4>No Pending Approvals</h4>
                                <p>All user registrations have been processed. New registration requests will appear here.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
