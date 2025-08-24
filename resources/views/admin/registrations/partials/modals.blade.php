<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="approvalForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Approve Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to approve the registration for <strong id="approvalUserName"></strong>?</p>
                    <div class="mb-3">
                        <label for="approvalMessage" class="form-label">Message to User (Optional)</label>
                        <textarea class="form-control" id="approvalMessage" name="admin_message" rows="3" 
                                  placeholder="Welcome message or additional instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Approve Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="declineForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Decline Registration</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to decline the registration for <strong id="declineUserName"></strong>?</p>
                    <div class="mb-3">
                        <label for="declineMessage" class="form-label">Reason for Decline <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="declineMessage" name="admin_message" rows="3" required
                                  placeholder="Please provide a clear reason for declining this registration..."></textarea>
                        <div class="form-text">This message will be sent to the user via email.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Decline Registration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
