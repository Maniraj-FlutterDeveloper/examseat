/**
 * Interactive Seating Layout
 * 
 * This script provides interactive functionality for the seating layout visualization,
 * including drag-and-drop seat assignment, filtering, and search capabilities.
 */

class InteractiveSeatingLayout {
    constructor(options = {}) {
        this.options = Object.assign({
            layoutSelector: '.seating-layout',
            seatSelector: '.seat-box',
            studentListSelector: '#student-list',
            searchSelector: '#student-search',
            roomFilterSelector: '#room-filter',
            saveButtonSelector: '#save-assignments-btn',
            printButtonSelector: '#print-seating-plan',
            exportButtonSelector: '#export-seating-plan',
            dragEnabled: true,
            searchEnabled: true,
            filterEnabled: true,
        }, options);
        
        this.init();
    }
    
    /**
     * Initialize the interactive layout
     */
    init() {
        this.layout = document.querySelector(this.options.layoutSelector);
        
        if (!this.layout) {
            console.error('Seating layout not found');
            return;
        }
        
        this.seats = this.layout.querySelectorAll(this.options.seatSelector);
        this.studentList = document.querySelector(this.options.studentListSelector);
        this.searchInput = document.querySelector(this.options.searchSelector);
        this.roomFilter = document.querySelector(this.options.roomFilterSelector);
        this.saveButton = document.querySelector(this.options.saveButtonSelector);
        this.printButton = document.querySelector(this.options.printButtonSelector);
        this.exportButton = document.querySelector(this.options.exportButtonSelector);
        
        this.initializeSeats();
        
        if (this.options.dragEnabled && this.studentList) {
            this.initializeDragAndDrop();
        }
        
        if (this.options.searchEnabled && this.searchInput) {
            this.initializeSearch();
        }
        
        if (this.options.filterEnabled && this.roomFilter) {
            this.initializeFilter();
        }
        
        if (this.saveButton) {
            this.initializeSave();
        }
        
        if (this.printButton) {
            this.initializePrint();
        }
        
        if (this.exportButton) {
            this.initializeExport();
        }
    }
    
    /**
     * Initialize seat interactions
     */
    initializeSeats() {
        this.seats.forEach(seat => {
            // Add hover effect
            seat.addEventListener('mouseenter', () => {
                seat.classList.add('hover');
            });
            
            seat.addEventListener('mouseleave', () => {
                seat.classList.remove('hover');
            });
            
            // Add click selection
            seat.addEventListener('click', () => {
                seat.classList.toggle('selected');
                this.updateSelectedSeatsInfo();
            });
            
            // Add context menu for additional options
            seat.addEventListener('contextmenu', (e) => {
                e.preventDefault();
                this.showSeatContextMenu(seat, e.clientX, e.clientY);
            });
        });
    }
    
    /**
     * Initialize drag and drop functionality
     */
    initializeDragAndDrop() {
        // Make students draggable
        const students = this.studentList.querySelectorAll('.student-item');
        students.forEach(student => {
            student.setAttribute('draggable', 'true');
            
            student.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', student.dataset.studentId);
                e.dataTransfer.effectAllowed = 'move';
            });
        });
        
        // Make seats droppable
        this.seats.forEach(seat => {
            seat.addEventListener('dragover', (e) => {
                e.preventDefault();
                seat.classList.add('dragover');
            });
            
            seat.addEventListener('dragleave', () => {
                seat.classList.remove('dragover');
            });
            
            seat.addEventListener('drop', (e) => {
                e.preventDefault();
                seat.classList.remove('dragover');
                
                const studentId = e.dataTransfer.getData('text/plain');
                this.assignStudentToSeat(studentId, seat);
            });
        });
    }
    
    /**
     * Initialize search functionality
     */
    initializeSearch() {
        this.searchInput.addEventListener('input', () => {
            const query = this.searchInput.value.toLowerCase();
            this.searchStudents(query);
        });
    }
    
    /**
     * Initialize filter functionality
     */
    initializeFilter() {
        this.roomFilter.addEventListener('change', () => {
            const roomId = this.roomFilter.value;
            this.filterByRoom(roomId);
        });
    }
    
    /**
     * Initialize save functionality
     */
    initializeSave() {
        this.saveButton.addEventListener('click', () => {
            this.saveAssignments();
        });
    }
    
    /**
     * Initialize print functionality
     */
    initializePrint() {
        this.printButton.addEventListener('click', () => {
            window.print();
        });
    }
    
    /**
     * Initialize export functionality
     */
    initializeExport() {
        this.exportButton.addEventListener('click', () => {
            this.exportToPDF();
        });
    }
    
    /**
     * Assign a student to a seat
     * 
     * @param {string} studentId - The ID of the student
     * @param {HTMLElement} seat - The seat element
     */
    assignStudentToSeat(studentId, seat) {
        // Get student information
        const student = document.querySelector(`.student-item[data-student-id="${studentId}"]`);
        
        if (!student) {
            console.error(`Student with ID ${studentId} not found`);
            return;
        }
        
        // Get seat information
        const seatNumber = seat.querySelector('.seat-number').textContent;
        
        // Update the seat with student information
        seat.classList.remove('empty');
        seat.classList.add('occupied');
        
        // Clear existing student info if any
        const existingStudentInfo = seat.querySelector('.student-info');
        if (existingStudentInfo) {
            existingStudentInfo.remove();
        }
        
        const emptyLabel = seat.querySelector('.empty-seat');
        if (emptyLabel) {
            emptyLabel.remove();
        }
        
        // Create new student info
        const studentInfo = document.createElement('div');
        studentInfo.className = 'student-info';
        studentInfo.innerHTML = `
            <div class="student-name">${student.dataset.studentName}</div>
            <div class="student-roll">${student.dataset.studentRoll}</div>
            <div class="student-course">${student.dataset.studentCourse}</div>
        `;
        
        seat.appendChild(studentInfo);
        
        // Update the assignment in the data structure
        this.updateAssignment(seat.dataset.roomId, seatNumber, studentId);
        
        // Mark the student as assigned in the list
        student.classList.add('assigned');
    }
    
    /**
     * Update the assignment data
     * 
     * @param {string} roomId - The ID of the room
     * @param {string} seatNumber - The seat number
     * @param {string} studentId - The ID of the student
     */
    updateAssignment(roomId, seatNumber, studentId) {
        // This would typically update a data structure or make an AJAX call
        console.log(`Assigned student ${studentId} to seat ${seatNumber} in room ${roomId}`);
        
        // For demonstration purposes, we'll update a hidden input field if it exists
        const assignmentInput = document.querySelector(`#assignment-${roomId}-${seatNumber}`);
        if (assignmentInput) {
            assignmentInput.value = studentId;
        } else {
            // Create a hidden input if it doesn't exist
            const input = document.createElement('input');
            input.type = 'hidden';
            input.id = `assignment-${roomId}-${seatNumber}`;
            input.name = `assignments[${roomId}][${seatNumber}]`;
            input.value = studentId;
            this.layout.appendChild(input);
        }
    }
    
    /**
     * Update the selected seats information
     */
    updateSelectedSeatsInfo() {
        const selectedSeats = this.layout.querySelectorAll('.seat-box.selected');
        const infoContainer = document.getElementById('selected-seat-info');
        
        if (!infoContainer) {
            return;
        }
        
        if (selectedSeats.length > 0) {
            let html = '<h6>Selected Seats:</h6><ul>';
            
            selectedSeats.forEach(seat => {
                const seatNumber = seat.querySelector('.seat-number').textContent;
                const studentName = seat.querySelector('.student-name')?.textContent || 'Empty';
                const studentRoll = seat.querySelector('.student-roll')?.textContent || '';
                
                html += `<li>Seat ${seatNumber}: ${studentName} ${studentRoll ? `(${studentRoll})` : ''}</li>`;
            });
            
            html += '</ul>';
            infoContainer.innerHTML = html;
            infoContainer.style.display = 'block';
        } else {
            infoContainer.style.display = 'none';
        }
    }
    
    /**
     * Show context menu for a seat
     * 
     * @param {HTMLElement} seat - The seat element
     * @param {number} x - The x coordinate
     * @param {number} y - The y coordinate
     */
    showSeatContextMenu(seat, x, y) {
        // Remove any existing context menu
        const existingMenu = document.querySelector('.seat-context-menu');
        if (existingMenu) {
            existingMenu.remove();
        }
        
        // Create context menu
        const menu = document.createElement('div');
        menu.className = 'seat-context-menu';
        menu.style.position = 'fixed';
        menu.style.left = `${x}px`;
        menu.style.top = `${y}px`;
        menu.style.backgroundColor = '#fff';
        menu.style.border = '1px solid #ccc';
        menu.style.borderRadius = '4px';
        menu.style.padding = '5px 0';
        menu.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.2)';
        menu.style.zIndex = '1000';
        
        // Add menu items
        const menuItems = [
            { text: 'Mark as Priority', action: () => this.markSeatAsPriority(seat) },
            { text: 'Clear Assignment', action: () => this.clearSeatAssignment(seat) },
            { text: 'Add Override', action: () => this.addSeatOverride(seat) },
        ];
        
        menuItems.forEach(item => {
            const menuItem = document.createElement('div');
            menuItem.className = 'seat-context-menu-item';
            menuItem.textContent = item.text;
            menuItem.style.padding = '8px 12px';
            menuItem.style.cursor = 'pointer';
            
            menuItem.addEventListener('mouseenter', () => {
                menuItem.style.backgroundColor = '#f0f0f0';
            });
            
            menuItem.addEventListener('mouseleave', () => {
                menuItem.style.backgroundColor = 'transparent';
            });
            
            menuItem.addEventListener('click', () => {
                item.action();
                menu.remove();
            });
            
            menu.appendChild(menuItem);
        });
        
        // Add the menu to the document
        document.body.appendChild(menu);
        
        // Close the menu when clicking outside
        document.addEventListener('click', function closeMenu(e) {
            if (!menu.contains(e.target)) {
                menu.remove();
                document.removeEventListener('click', closeMenu);
            }
        });
    }
    
    /**
     * Mark a seat as priority
     * 
     * @param {HTMLElement} seat - The seat element
     */
    markSeatAsPriority(seat) {
        seat.classList.toggle('priority');
        
        if (seat.classList.contains('priority')) {
            seat.dataset.isPriority = 'true';
        } else {
            delete seat.dataset.isPriority;
        }
    }
    
    /**
     * Clear a seat assignment
     * 
     * @param {HTMLElement} seat - The seat element
     */
    clearSeatAssignment(seat) {
        // Remove student info
        const studentInfo = seat.querySelector('.student-info');
        if (studentInfo) {
            studentInfo.remove();
        }
        
        // Add empty label
        if (!seat.querySelector('.empty-seat')) {
            const emptyLabel = document.createElement('div');
            emptyLabel.className = 'empty-seat';
            emptyLabel.textContent = 'Empty';
            seat.appendChild(emptyLabel);
        }
        
        // Update classes
        seat.classList.remove('occupied');
        seat.classList.add('empty');
        
        // Clear assignment in data structure
        const roomId = seat.dataset.roomId;
        const seatNumber = seat.querySelector('.seat-number').textContent;
        this.updateAssignment(roomId, seatNumber, '');
    }
    
    /**
     * Add an override for a seat
     * 
     * @param {HTMLElement} seat - The seat element
     */
    addSeatOverride(seat) {
        const roomId = seat.dataset.roomId;
        const seatNumber = seat.querySelector('.seat-number').textContent;
        
        // This would typically open a modal or form to add an override
        // For demonstration purposes, we'll just add a class and data attribute
        seat.classList.add('override');
        seat.dataset.isOverride = 'true';
        
        // Add an indicator to the seat
        if (!seat.querySelector('.override-indicator')) {
            const indicator = document.createElement('div');
            indicator.className = 'override-indicator';
            indicator.textContent = 'Override';
            indicator.style.position = 'absolute';
            indicator.style.top = '5px';
            indicator.style.right = '5px';
            indicator.style.backgroundColor = '#ffc107';
            indicator.style.color = '#000';
            indicator.style.fontSize = '0.7rem';
            indicator.style.padding = '2px 5px';
            indicator.style.borderRadius = '3px';
            seat.appendChild(indicator);
        }
    }
    
    /**
     * Search for students in the seating layout
     * 
     * @param {string} query - The search query
     */
    searchStudents(query) {
        if (!query) {
            // Reset all seats to normal state
            this.seats.forEach(seat => {
                seat.classList.remove('highlight');
            });
            return;
        }
        
        // Search through all student info
        this.seats.forEach(seat => {
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
     * Filter the seating layout by room
     * 
     * @param {string} roomId - The ID of the room to filter by
     */
    filterByRoom(roomId) {
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
     * Save the current assignments
     */
    saveAssignments() {
        // Collect all assignments
        const assignments = {};
        
        this.seats.forEach(seat => {
            const roomId = seat.dataset.roomId;
            const seatNumber = seat.querySelector('.seat-number').textContent;
            const studentInfo = seat.querySelector('.student-info');
            
            if (studentInfo) {
                const studentId = seat.dataset.studentId;
                
                if (!assignments[roomId]) {
                    assignments[roomId] = {};
                }
                
                assignments[roomId][seatNumber] = studentId;
            }
        });
        
        // This would typically submit the form or make an AJAX call
        console.log('Saving assignments:', assignments);
        
        // For demonstration purposes, we'll submit the form if it exists
        const form = document.querySelector('#seating-assignments-form');
        if (form) {
            form.submit();
        }
    }
    
    /**
     * Export the seating plan to PDF
     */
    exportToPDF() {
        // This would typically use a library like jsPDF
        alert('Export to PDF functionality would be implemented here.');
    }
}

// Initialize the interactive seating layout when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new InteractiveSeatingLayout();
});

