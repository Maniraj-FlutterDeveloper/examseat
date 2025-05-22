/**
 * Seating Layout JavaScript
 * 
 * This script provides interactive functionality for the seating layout visualization.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the seating layout
    initializeSeatingLayout();
    
    // Add event listeners for interactive elements
    addEventListeners();
});

/**
 * Initialize the seating layout visualization
 */
function initializeSeatingLayout() {
    // Get all seat boxes
    const seatBoxes = document.querySelectorAll('.seat-box');
    
    // Add hover effect
    seatBoxes.forEach(seat => {
        seat.addEventListener('mouseenter', function() {
            this.classList.add('hover');
        });
        
        seat.addEventListener('mouseleave', function() {
            this.classList.remove('hover');
        });
    });
    
    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

/**
 * Add event listeners for interactive elements
 */
function addEventListeners() {
    // Toggle seat selection
    const seatBoxes = document.querySelectorAll('.seat-box');
    seatBoxes.forEach(seat => {
        seat.addEventListener('click', function() {
            this.classList.toggle('selected');
            
            // Update the selected seat information
            updateSelectedSeatInfo();
        });
    });
    
    // Room filter
    const roomFilter = document.getElementById('room-filter');
    if (roomFilter) {
        roomFilter.addEventListener('change', function() {
            filterSeatingLayout(this.value);
        });
    }
    
    // Search student
    const studentSearch = document.getElementById('student-search');
    if (studentSearch) {
        studentSearch.addEventListener('input', function() {
            searchStudent(this.value);
        });
    }
    
    // Print button
    const printButton = document.getElementById('print-seating-plan');
    if (printButton) {
        printButton.addEventListener('click', function() {
            printSeatingPlan();
        });
    }
    
    // Export button
    const exportButton = document.getElementById('export-seating-plan');
    if (exportButton) {
        exportButton.addEventListener('click', function() {
            exportSeatingPlan();
        });
    }
}

/**
 * Update the selected seat information
 */
function updateSelectedSeatInfo() {
    const selectedSeats = document.querySelectorAll('.seat-box.selected');
    const selectedSeatInfo = document.getElementById('selected-seat-info');
    
    if (selectedSeatInfo) {
        if (selectedSeats.length > 0) {
            let html = '<h6>Selected Seats:</h6><ul>';
            
            selectedSeats.forEach(seat => {
                const seatNumber = seat.querySelector('.seat-number').textContent;
                const studentName = seat.querySelector('.student-name')?.textContent || 'Empty';
                const studentRoll = seat.querySelector('.student-roll')?.textContent || '';
                
                html += `<li>Seat ${seatNumber}: ${studentName} ${studentRoll ? `(${studentRoll})` : ''}</li>`;
            });
            
            html += '</ul>';
            selectedSeatInfo.innerHTML = html;
            selectedSeatInfo.style.display = 'block';
        } else {
            selectedSeatInfo.style.display = 'none';
        }
    }
}

/**
 * Filter the seating layout by room
 * 
 * @param {string} roomId - The ID of the room to filter by
 */
function filterSeatingLayout(roomId) {
    const roomLayouts = document.querySelectorAll('.room-layout');
    
    if (roomId === 'all') {
        roomLayouts.forEach(layout => {
            layout.style.display = 'block';
        });
    } else {
        roomLayouts.forEach(layout => {
            if (layout.dataset.roomId === roomId) {
                layout.style.display = 'block';
            } else {
                layout.style.display = 'none';
            }
        });
    }
}

/**
 * Search for a student in the seating layout
 * 
 * @param {string} query - The search query
 */
function searchStudent(query) {
    if (!query) {
        // Reset all seats to normal state
        document.querySelectorAll('.seat-box').forEach(seat => {
            seat.classList.remove('highlight');
        });
        return;
    }
    
    query = query.toLowerCase();
    
    // Search through all student info
    document.querySelectorAll('.seat-box').forEach(seat => {
        const studentName = seat.querySelector('.student-name')?.textContent.toLowerCase() || '';
        const studentRoll = seat.querySelector('.student-roll')?.textContent.toLowerCase() || '';
        const studentCourse = seat.querySelector('.student-course')?.textContent.toLowerCase() || '';
        
        if (studentName.includes(query) || studentRoll.includes(query) || studentCourse.includes(query)) {
            seat.classList.add('highlight');
        } else {
            seat.classList.remove('highlight');
        }
    });
}

/**
 * Print the seating plan
 */
function printSeatingPlan() {
    window.print();
}

/**
 * Export the seating plan as PDF
 */
function exportSeatingPlan() {
    // This would typically use a library like jsPDF or call a server-side endpoint
    alert('Export functionality would be implemented here.');
}

