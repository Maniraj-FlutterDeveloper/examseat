/**
 * AJAX Handler for Seating Plan Module
 * 
 * This script provides AJAX functionality for real-time updates and interactions
 * with the seating plan module.
 */

class SeatingAjaxHandler {
    constructor(options = {}) {
        this.options = Object.assign({
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            baseUrl: '/api',
            notificationContainer: '#notifications',
            loadingIndicator: '#loading-indicator',
        }, options);
        
        this.setupAjaxDefaults();
    }
    
    /**
     * Set up default AJAX settings
     */
    setupAjaxDefaults() {
        // Set up CSRF token for all AJAX requests
        if (this.options.csrfToken) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': this.options.csrfToken
                }
            });
        }
    }
    
    /**
     * Show loading indicator
     */
    showLoading() {
        const loadingIndicator = document.querySelector(this.options.loadingIndicator);
        if (loadingIndicator) {
            loadingIndicator.style.display = 'block';
        }
    }
    
    /**
     * Hide loading indicator
     */
    hideLoading() {
        const loadingIndicator = document.querySelector(this.options.loadingIndicator);
        if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
        }
    }
    
    /**
     * Show notification
     * 
     * @param {string} message - The notification message
     * @param {string} type - The notification type (success, error, warning, info)
     * @param {number} duration - The duration in milliseconds
     */
    showNotification(message, type = 'info', duration = 3000) {
        const container = document.querySelector(this.options.notificationContainer);
        if (!container) return;
        
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        container.appendChild(notification);
        
        // Auto-dismiss after duration
        if (duration > 0) {
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    notification.remove();
                }, 150);
            }, duration);
        }
    }
    
    /**
     * Fetch room capacity and layout
     * 
     * @param {number} roomId - The ID of the room
     * @returns {Promise} - A promise that resolves with the room data
     */
    fetchRoomCapacity(roomId) {
        this.showLoading();
        
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${this.options.baseUrl}/rooms/${roomId}/capacity`,
                method: 'GET',
                dataType: 'json',
                success: (data) => {
                    this.hideLoading();
                    resolve(data);
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    this.showNotification(`Error fetching room capacity: ${error}`, 'error');
                    reject(error);
                }
            });
        });
    }
    
    /**
     * Search for students
     * 
     * @param {string} query - The search query
     * @returns {Promise} - A promise that resolves with the search results
     */
    searchStudents(query) {
        if (!query || query.length < 2) {
            return Promise.resolve([]);
        }
        
        this.showLoading();
        
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${this.options.baseUrl}/students/search`,
                method: 'GET',
                data: { query },
                dataType: 'json',
                success: (data) => {
                    this.hideLoading();
                    resolve(data);
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    this.showNotification(`Error searching students: ${error}`, 'error');
                    reject(error);
                }
            });
        });
    }
    
    /**
     * Save a seating assignment
     * 
     * @param {number} seatingPlanId - The ID of the seating plan
     * @param {number} roomId - The ID of the room
     * @param {number} seatNumber - The seat number
     * @param {number} studentId - The ID of the student
     * @returns {Promise} - A promise that resolves when the assignment is saved
     */
    saveAssignment(seatingPlanId, roomId, seatNumber, studentId) {
        this.showLoading();
        
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${this.options.baseUrl}/seating-plans/${seatingPlanId}/assignments`,
                method: 'POST',
                data: {
                    room_id: roomId,
                    seat_number: seatNumber,
                    student_id: studentId
                },
                dataType: 'json',
                success: (data) => {
                    this.hideLoading();
                    this.showNotification('Assignment saved successfully', 'success');
                    resolve(data);
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    this.showNotification(`Error saving assignment: ${error}`, 'error');
                    reject(error);
                }
            });
        });
    }
    
    /**
     * Remove a seating assignment
     * 
     * @param {number} seatingPlanId - The ID of the seating plan
     * @param {number} roomId - The ID of the room
     * @param {number} seatNumber - The seat number
     * @returns {Promise} - A promise that resolves when the assignment is removed
     */
    removeAssignment(seatingPlanId, roomId, seatNumber) {
        this.showLoading();
        
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${this.options.baseUrl}/seating-plans/${seatingPlanId}/assignments`,
                method: 'DELETE',
                data: {
                    room_id: roomId,
                    seat_number: seatNumber
                },
                dataType: 'json',
                success: (data) => {
                    this.hideLoading();
                    this.showNotification('Assignment removed successfully', 'success');
                    resolve(data);
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    this.showNotification(`Error removing assignment: ${error}`, 'error');
                    reject(error);
                }
            });
        });
    }
    
    /**
     * Create a seating override
     * 
     * @param {Object} overrideData - The override data
     * @returns {Promise} - A promise that resolves when the override is created
     */
    createOverride(overrideData) {
        this.showLoading();
        
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${this.options.baseUrl}/seating-overrides`,
                method: 'POST',
                data: overrideData,
                dataType: 'json',
                success: (data) => {
                    this.hideLoading();
                    this.showNotification('Override created successfully', 'success');
                    resolve(data);
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    this.showNotification(`Error creating override: ${error}`, 'error');
                    reject(error);
                }
            });
        });
    }
    
    /**
     * Fetch seating assignments for a seating plan
     * 
     * @param {number} seatingPlanId - The ID of the seating plan
     * @returns {Promise} - A promise that resolves with the assignments
     */
    fetchAssignments(seatingPlanId) {
        this.showLoading();
        
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${this.options.baseUrl}/seating-plans/${seatingPlanId}/assignments`,
                method: 'GET',
                dataType: 'json',
                success: (data) => {
                    this.hideLoading();
                    resolve(data);
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    this.showNotification(`Error fetching assignments: ${error}`, 'error');
                    reject(error);
                }
            });
        });
    }
    
    /**
     * Generate seating assignments for a seating plan
     * 
     * @param {number} seatingPlanId - The ID of the seating plan
     * @returns {Promise} - A promise that resolves when the assignments are generated
     */
    generateAssignments(seatingPlanId) {
        this.showLoading();
        
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${this.options.baseUrl}/seating-plans/${seatingPlanId}/generate`,
                method: 'POST',
                dataType: 'json',
                success: (data) => {
                    this.hideLoading();
                    this.showNotification('Assignments generated successfully', 'success');
                    resolve(data);
                },
                error: (xhr, status, error) => {
                    this.hideLoading();
                    this.showNotification(`Error generating assignments: ${error}`, 'error');
                    reject(error);
                }
            });
        });
    }
    
    /**
     * Export seating plan to PDF
     * 
     * @param {number} seatingPlanId - The ID of the seating plan
     */
    exportToPDF(seatingPlanId) {
        window.open(`${this.options.baseUrl}/seating-plans/${seatingPlanId}/export-pdf`, '_blank');
    }
}

// Initialize the AJAX handler when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.seatingAjaxHandler = new SeatingAjaxHandler();
});

