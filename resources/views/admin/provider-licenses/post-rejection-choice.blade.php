@extends('layouts.dashboard')

@section('title', 'Post-Rejection User Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">License Rejection Completed</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> License Rejection Successful</h5>
                        <p>The provider license has been rejected and an email notification has been sent to the user.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>User Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Name:</strong></td>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Role:</strong></td>
                                            <td><span class="badge badge-info">{{ ucfirst($user->role) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Registration Date:</strong></td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td><span class="badge badge-warning">{{ ucfirst($user->status) }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>License Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>License ID:</strong></td>
                                            <td>#{{ $license->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td><span class="badge badge-danger">{{ ucfirst($license->status) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Submitted:</strong></td>
                                            <td>{{ $license->created_at->format('M d, Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Rejected:</strong></td>
                                            <td>{{ $license->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @if($license->notes)
                                        <tr>
                                            <td><strong>Rejection Reason:</strong></td>
                                            <td class="text-danger">{{ $license->notes }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> What would you like to do with this user?</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">
                                        Since the license has been rejected, you can choose to either keep the user account 
                                        (allowing them to resubmit their license) or permanently remove the user and all associated data.
                                    </p>

                                    <form action="{{ route('admin.provider-licenses.handle-post-rejection-choice') }}" method="POST" id="userActionForm">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                                        <input type="hidden" name="license_id" value="{{ $license->id }}">
                                        <input type="hidden" name="action" id="actionInput">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card border-success">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-user-check fa-3x text-success mb-3"></i>
                                                        <h5>Keep User</h5>
                                                        <p class="text-muted">
                                                            Maintain the user account so they can resubmit their license application.
                                                        </p>
                                                        <button type="button" class="btn btn-success btn-lg" onclick="submitAction('keep')">
                                                            <i class="fas fa-check"></i> Keep User
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="card border-danger">
                                                    <div class="card-body text-center">
                                                        <i class="fas fa-user-times fa-3x text-danger mb-3"></i>
                                                        <h5>Remove User</h5>
                                                        <p class="text-muted">
                                                            Permanently delete the user and all associated data from the system.
                                                        </p>
                                                        <button type="button" class="btn btn-danger btn-lg" onclick="confirmRemoval()">
                                                            <i class="fas fa-trash"></i> Remove User
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="{{ route('admin.provider-licenses.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Provider Licenses
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmRemovalModal" tabindex="-1" role="dialog" aria-labelledby="confirmRemovalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmRemovalModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirm User Removal
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <strong>Warning:</strong> This action cannot be undone!
                </div>
                <p>Are you sure you want to permanently delete the user <strong>{{ $user->name }}</strong> and all associated data?</p>
                <p>This will remove:</p>
                <ul>
                    <li>User account and profile information</li>
                    <li>All license records and documents</li>
                    <li>Any uploaded files (logos, images, etc.)</li>
                    <li>All related provider data</li>
                    <li>Services created by this provider</li>
                </ul>
                <p class="text-danger"><strong>This action is irreversible!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitAction('remove')">
                    <i class="fas fa-trash"></i> Yes, Remove User
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function submitAction(action) {
    document.getElementById('actionInput').value = action;
    document.getElementById('userActionForm').submit();
}

function confirmRemoval() {
    $('#confirmRemovalModal').modal('show');
}
</script>
@endsection
