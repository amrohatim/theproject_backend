/**
 * Merchant Access Control Modal System
 * Handles access control modals for license status and registration step restrictions
 */

class MerchantAccessControl {
    constructor() {
        this.modal = null;
        this.init();
    }

    init() {
        // Check for session-based modal data on page load
        this.checkSessionModal();
        
        // Set up AJAX error handling for access control
        this.setupAjaxErrorHandling();
    }

    /**
     * Check if there's a modal to show from session data
     */
    checkSessionModal() {
        // Check if Laravel session has modal data
        const showModal = document.querySelector('meta[name="show-access-modal"]')?.content === 'true';
        
        if (showModal) {
            const title = document.querySelector('meta[name="modal-title"]')?.content || 'Access Restricted';
            const message = document.querySelector('meta[name="modal-message"]')?.content || 'Access denied';
            const licenseStatus = document.querySelector('meta[name="license-status"]')?.content;
            const registrationStep = document.querySelector('meta[name="registration-step"]')?.content;
            
            this.showAccessModal(title, message, licenseStatus, registrationStep);
        }
    }

    /**
     * Set up AJAX error handling for access control responses
     */
    setupAjaxErrorHandling() {
        // jQuery AJAX error handler
        if (typeof $ !== 'undefined') {
            $(document).ajaxError((event, xhr, settings) => {
                if (xhr.status === 403 && xhr.responseJSON && xhr.responseJSON.show_modal) {
                    const response = xhr.responseJSON;
                    this.showAccessModal(
                        response.modal_title || 'Access Restricted',
                        response.modal_message || response.message,
                        response.license_status,
                        response.registration_step,
                        response.redirect
                    );
                }
            });
        }

        // Fetch API error handler
        const originalFetch = window.fetch;
        window.fetch = async (...args) => {
            const response = await originalFetch(...args);
            
            if (response.status === 403) {
                try {
                    const data = await response.clone().json();
                    if (data.show_modal) {
                        this.showAccessModal(
                            data.modal_title || 'Access Restricted',
                            data.modal_message || data.message,
                            data.license_status,
                            data.registration_step,
                            data.redirect
                        );
                    }
                } catch (e) {
                    // Not JSON response, continue normally
                }
            }
            
            return response;
        };
    }

    /**
     * Show access control modal
     */
    showAccessModal(title, message, licenseStatus, registrationStep, redirectUrl = null) {
        // Remove existing modal if present
        this.closeModal();

        // Create modal HTML
        const modalHTML = this.createModalHTML(title, message, licenseStatus, registrationStep, redirectUrl);
        
        // Add to DOM
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('accessControlModal');
        
        // Show modal with animation
        setTimeout(() => {
            this.modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }, 10);

        // Set up event listeners
        this.setupModalEvents(redirectUrl);
    }

    /**
     * Create modal HTML
     */
    createModalHTML(title, message, licenseStatus, registrationStep, redirectUrl) {
        const actionButton = this.getActionButton(licenseStatus, registrationStep, redirectUrl);
        
        return `
            <div id="accessControlModal" class="access-control-modal-overlay">
                <div class="access-control-modal">
                    <div class="modal-header">
                        <div class="modal-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="modal-title">${title}</h3>
                        <button type="button" class="modal-close" onclick="merchantAccessControl.closeModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="modal-message">${message}</p>
                        ${this.getStatusInfo(licenseStatus, registrationStep)}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="merchantAccessControl.closeModal()">
                            Close
                        </button>
                        ${actionButton}
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Get status information HTML
     */
    getStatusInfo(licenseStatus, registrationStep) {
        let statusHTML = '';
        
        if (licenseStatus) {
            const statusInfo = this.getLicenseStatusInfo(licenseStatus);
            statusHTML += `
                <div class="status-info license-status">
                    <div class="status-label">License Status:</div>
                    <div class="status-value ${statusInfo.class}">
                        <i class="${statusInfo.icon}"></i>
                        ${statusInfo.text}
                    </div>
                </div>
            `;
        }
        
        if (registrationStep) {
            const stepInfo = this.getRegistrationStepInfo(registrationStep);
            statusHTML += `
                <div class="status-info registration-step">
                    <div class="status-label">Registration Step:</div>
                    <div class="status-value ${stepInfo.class}">
                        <i class="${stepInfo.icon}"></i>
                        ${stepInfo.text}
                    </div>
                </div>
            `;
        }
        
        return statusHTML;
    }

    /**
     * Get license status information
     */
    getLicenseStatusInfo(status) {
        const statusMap = {
            'verified': { text: 'Verified', class: 'status-success', icon: 'fas fa-check-circle' },
            'checking': { text: 'Under Review', class: 'status-warning', icon: 'fas fa-clock' },
            'expired': { text: 'Expired', class: 'status-danger', icon: 'fas fa-times-circle' },
            'rejected': { text: 'Rejected', class: 'status-danger', icon: 'fas fa-times-circle' }
        };
        
        return statusMap[status] || { text: status, class: 'status-info', icon: 'fas fa-info-circle' };
    }

    /**
     * Get registration step information
     */
    getRegistrationStepInfo(step) {
        const stepMap = {
            'phone_verified': { text: 'Phone Verified', class: 'status-warning', icon: 'fas fa-phone' },
            'license_completed': { text: 'License Uploaded', class: 'status-success', icon: 'fas fa-file-alt' },
            'verified': { text: 'Fully Verified', class: 'status-success', icon: 'fas fa-check-circle' },
            'pending': { text: 'Pending', class: 'status-warning', icon: 'fas fa-clock' }
        };
        
        return stepMap[step] || { text: step, class: 'status-info', icon: 'fas fa-info-circle' };
    }

    /**
     * Get appropriate action button
     */
    getActionButton(licenseStatus, registrationStep, redirectUrl) {
        let buttonText = 'Go to Dashboard';
        let buttonUrl = '/merchant/dashboard';
        
        // Determine appropriate action based on status
        if (registrationStep === 'phone_verified' || licenseStatus === 'expired' || licenseStatus === 'rejected') {
            buttonText = 'Upload License';
            buttonUrl = '/merchant/license/upload';
        } else if (licenseStatus === 'checking') {
            buttonText = 'View Status';
            buttonUrl = `/merchant/license/status/${licenseStatus}`;
        }
        
        // Use provided redirect URL if available
        if (redirectUrl) {
            buttonUrl = redirectUrl;
        }
        
        return `
            <button type="button" class="btn btn-primary" onclick="merchantAccessControl.redirectTo('${buttonUrl}')">
                ${buttonText}
            </button>
        `;
    }

    /**
     * Set up modal event listeners
     */
    setupModalEvents(redirectUrl) {
        // Close on overlay click
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.closeModal();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal) {
                this.closeModal();
            }
        });
    }

    /**
     * Close modal
     */
    closeModal() {
        if (this.modal) {
            this.modal.classList.remove('show');
            document.body.style.overflow = 'auto';
            
            setTimeout(() => {
                if (this.modal && this.modal.parentNode) {
                    this.modal.parentNode.removeChild(this.modal);
                }
                this.modal = null;
            }, 300);
        }
    }

    /**
     * Redirect to URL
     */
    redirectTo(url) {
        window.location.href = url;
    }
}

// Initialize the access control system
const merchantAccessControl = new MerchantAccessControl();

// Export for use in other scripts
window.merchantAccessControl = merchantAccessControl;
