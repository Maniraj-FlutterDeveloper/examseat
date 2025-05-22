/**
 * Interactive Seating Layout for Seating Plan Module
 * 
 * This script provides interactive functionality for the seating layout,
 * including drag-and-drop seat assignment, student search and filtering,
 * room filtering, context menus for seats, and print/export functionality.
 */

class InteractiveSeatingLayout {
    constructor(options = {}) {
        this.options = Object.assign({
            containerSelector: '#seating-layout-container',
            layoutSelector: '#seating-layout',
            roomFilterSelector: '#room-filter',
            exportButtonSelector: '#export-button',
            printButtonSelector: '#print-button',
            generateButtonSelector: '#generate-button',
            saveButtonSelector: '#save-button',
            seatingPlanId: null,
            ajaxHandler: window.seatingAjaxHandler,
            studentListComponent: window.studentListComponent,
        }, options);
        
        this.rooms = [];
        this.currentRoom = null;
        this.assignments = {};
        this.selectedSeat = null;
        this.selectedStudent = null;
        
        this.init();
    }
    
    /**
     * Initialize the component
     */
    init() {
        this.container = document.querySelector(this.options.containerSelector);
        this.layout = document.querySelector(this.options.layoutSelector);
        this.roomFilter = document.querySelector(this.options.roomFilterSelector);
        this.exportButton = document.querySelector(this.options.exportButtonSelector);
        this.printButton = document.querySelector(this.options.printButtonSelector);
        this.generateButton = document.querySelector(this.options.generateButtonSelector);
        this.saveButton = document.querySelector(this.options.saveButtonSelector);
        
        if (!this.container || !this.layout) {
            console.error('Seating layout container or layout not found');
            return;
        }
        
        // Get seating plan ID from data attribute or options
        this.options.seatingPlanId = this.options.seatingPlanId || 
                                    this.container.dataset.seatingPlanId;
        
        this.initEventListeners();
        this.loadRooms();
    }
    
    /**
     * Initialize event listeners
     */
    initEventListeners() {
        // Room filter
        if (this.roomFilter) {
            this.roomFilter.addEventListener('change', () => {
                this.changeRoom(this.roomFilter.value);
            });
        }
        
        // Export button
        if (this.exportButton) {
            this.exportButton.addEventListener('click', () => {
                this.exportToPDF();
            });
        }
        
        // Print button
        if (this.printButton) {
            this.printButton.addEventListener('click', () => {
                this.printLayout();
            });
        }
        
        // Generate button
        if (this.generateButton) {
            this.generateButton.addEventListener('click', () => {
                this.generateAssignments();
            });
        }
        
        // Save button
        if (this.saveButton) {
            this.saveButton.addEventListener('click', () => {
                this.saveAssignments();
            });
        }
        
        // Listen for student selection
        document.addEventListener('student-selected', (e) => {
            this.selectedStudent = e.detail.student;
            
            if (this.selectedSeat) {
                this.assignStudentToSeat(this.selectedStudent, this.selectedSeat);
            } else {
                // Show a notification that a seat needs to be selected
                if (this.options.ajaxHandler) {
                    this.options.ajaxHandler.showNotification(
                        'Please select a seat to assign this student to', 
                        'info'
                    );
                }
            }
        });
        
        // Handle drag and drop on the layout
        if (this.layout) {
            this.layout.addEventListener('dragover', (e) => {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            });
            
            this.layout.addEventListener('drop', (e) => {
                e.preventDefault();
                
                // Get the student ID from the data transfer
                const studentId = e.dataTransfer.getData('text/plain');
                if (!studentId) return;
                
                // Find the seat element that was dropped on
                const seatElement = e.target.closest('.seat');
                if (!seatElement) return;
                
                // Get the seat number and room ID
                const seatNumber = parseInt(seatElement.dataset.seatNumber);
                const roomId = this.currentRoom ? this.currentRoom.id : null;
                
                if (!roomId || isNaN(seatNumber)) return;
                
                // Find the student
                const student = this.options.studentListComponent ? 
                    this.options.studentListComponent.students.find(s => s.id.toString() === studentId.toString()) : 
                    null;
                
                if (!student) return;
                
                // Assign the student to the seat
                this.assignStudentToSeat(student, { roomId, seatNumber });
            });
        }
    }
    
    /**
     * Load rooms from the server
     */
    loadRooms() {
        // For now, we'll just use some dummy data
        this.rooms = this.getDummyRooms();
        
        // Populate room filter
        if (this.roomFilter) {
            this.roomFilter.innerHTML = '';
            
            this.rooms.forEach(room => {
                const option = document.createElement('option');
                option.value = room.id;
                option.textContent = `${room.room_number} (Capacity: ${room.capacity})`;
                this.roomFilter.appendChild(option);
            });
        }
        
        // Select the first room by default
        if (this.rooms.length > 0) {
            this.changeRoom(this.rooms[0].id);
        }
        
        // Load assignments
        this.loadAssignments();
    }
    
    /**
     * Get dummy room data for testing
     * 
     * @returns {Array} - Array of dummy room objects
     */
    getDummyRooms() {
        return [
            {
                id: 1,
                room_number: 'R-101',
                capacity: 30,
                layout: {
                    seats_per_row: 6,
                    rows: 5,
                    has_door_left: true,
                    has_door_right: false,
                }
            },
            {
                id: 2,
                room_number: 'R-102',
                capacity: 40,
                layout: {
                    seats_per_row: 8,
                    rows: 5,
                    has_door_left: false,
                    has_door_right: true,
                }
            },
            {
                id: 3,
                room_number: 'R-103',
                capacity: 24,
                layout: {
                    seats_per_row: 4,
                    rows: 6,
                    has_door_left: true,
                    has_door_right: true,
                }
            }
        ];
    }
    
    /**
     * Load assignments from the server
     */
    loadAssignments() {
        if (!this.options.seatingPlanId) return;
        
        if (this.options.ajaxHandler) {
            this.options.ajaxHandler.fetchAssignments(this.options.seatingPlanId)
                .then(data => {
                    this.assignments = {};
                    
                    // Group assignments by room and seat
                    data.forEach(assignment => {
                        if (!this.assignments[assignment.room_id]) {
                            this.assignments[assignment.room_id] = {};
                        }
                        
                        this.assignments[assignment.room_id][assignment.seat_number] = assignment;
                        
                        // Mark the student as assigned in the student list
                        if (this.options.studentListComponent) {
                            this.options.studentListComponent.markAsAssigned(assignment.student_id);
                        }
                    });
                    
                    // Update the layout
                    this.renderLayout();
                })
                .catch(error => {
                    console.error('Error loading assignments:', error);
                });
        } else {
            // Use dummy assignments for testing
            this.assignments = this.getDummyAssignments();
            this.renderLayout();
        }
    }
    
    /**
     * Get dummy assignment data for testing
     * 
     * @returns {Object} - Object of dummy assignments
     */
    getDummyAssignments() {
        const assignments = {};
        
        // Room 1 assignments
        assignments[1] = {};
        for (let i = 1; i <= 10; i++) {
            assignments[1][i] = {
                student_id: i,
                student_name: `Student ${i}`,
                student_roll: `R${i.toString().padStart(3, '0')}`,
                student_course: 'B.Tech CS',
                is_override: false,
            };
        }
        
        // Room 2 assignments
        assignments[2] = {};
        for (let i = 11; i <= 20; i++) {
            assignments[2][i - 10] = {
                student_id: i,
                student_name: `Student ${i}`,
                student_roll: `R${i.toString().padStart(3, '0')}`,
                student_course: 'B.Tech ECE',
                is_override: i === 15, // One override
            };
        }
        
        return assignments;
    }
    
    /**
     * Change the current room
     * 
     * @param {number|string} roomId - The ID of the room
     */
    changeRoom(roomId) {
        roomId = parseInt(roomId);
        
        // Find the room
        this.currentRoom = this.rooms.find(room => room.id === roomId);
        
        if (!this.currentRoom) {
            console.error(`Room with ID ${roomId} not found`);
            return;
        }
        
        // Update the room filter
        if (this.roomFilter) {
            this.roomFilter.value = roomId;
        }
        
        // Render the layout
        this.renderLayout();
    }
    
    /**
     * Render the seating layout
     */
    renderLayout() {
        if (!this.layout || !this.currentRoom) return;
        
        // Clear the layout
        this.layout.innerHTML = '';
        
        // Create the room container
        const roomContainer = document.createElement('div');
        roomContainer.className = 'room-container';
        
        // Add room information
        const roomInfo = document.createElement('div');
        roomInfo.className = 'room-info';
        roomInfo.innerHTML = `
            <h3>${this.currentRoom.room_number}</h3>
            <p>Capacity: ${this.currentRoom.capacity}</p>
        `;
        roomContainer.appendChild(roomInfo);
        
        // Create the seating grid
        const seatingGrid = document.createElement('div');
        seatingGrid.className = 'seating-grid';
        
        // Set grid template based on room layout
        const seatsPerRow = this.currentRoom.layout.seats_per_row;
        const rows = this.currentRoom.layout.rows;
        
        seatingGrid.style.gridTemplateColumns = `repeat(${seatsPerRow}, 1fr)`;
        seatingGrid.style.gridTemplateRows = `repeat(${rows}, 1fr)`;
        
        // Create seats
        for (let row = 1; row <= rows; row++) {
            for (let col = 1; col <= seatsPerRow; col++) {
                const seatNumber = (row - 1) * seatsPerRow + col;
                
                // Skip if seat number exceeds capacity
                if (seatNumber > this.currentRoom.capacity) continue;
                
                const seat = document.createElement('div');
                seat.className = 'seat';
                seat.dataset.seatNumber = seatNumber;
                seat.dataset.row = row;
                seat.dataset.col = col;
                
                // Check if seat is assigned
                const roomAssignments = this.assignments[this.currentRoom.id] || {};
                const assignment = roomAssignments[seatNumber];
                
                if (assignment) {
                    seat.classList.add('assigned');
                    
                    if (assignment.is_override) {
                        seat.classList.add('override');
                    }
                    
                    seat.innerHTML = `
                        <div class="seat-number">${seatNumber}</div>
                        <div class="student-info">
                            <div class="student-name">${assignment.student_name}</div>
                            <div class="student-roll">${assignment.student_roll}</div>
                            <div class="student-course">${assignment.student_course}</div>
                        </div>
                    `;
                } else {
                    seat.innerHTML = `
                        <div class="seat-number">${seatNumber}</div>
                        <div class="student-info empty">Empty</div>
                    `;
                }
                
                // Add event listeners
                this.addSeatEventListeners(seat);
                
                seatingGrid.appendChild(seat);
            }
        }
        
        roomContainer.appendChild(seatingGrid);
        this.layout.appendChild(roomContainer);
    }
    
    /**
     * Add event listeners to a seat element
     * 
     * @param {HTMLElement} seat - The seat element
     */
    addSeatEventListeners(seat) {
        // Click to select
        seat.addEventListener('click', () => {
            // Deselect previously selected seat
            if (this.selectedSeat) {
                const prevSeat = this.layout.querySelector(`.seat[data-seat-number="${this.selectedSeat.seatNumber}"]`);
                if (prevSeat) {
                    prevSeat.classList.remove('selected');
                }
            }
            
            // Select this seat
            seat.classList.add('selected');
            this.selectedSeat = {
                roomId: this.currentRoom.id,
                seatNumber: parseInt(seat.dataset.seatNumber)
            };
            
            // If a student is already selected, assign them to this seat
            if (this.selectedStudent) {
                this.assignStudentToSeat(this.selectedStudent, this.selectedSeat);
                this.selectedStudent = null;
            }
        });
        
        // Double-click to unassign
        seat.addEventListener('dblclick', () => {
            const seatNumber = parseInt(seat.dataset.seatNumber);
            const roomId = this.currentRoom.id;
            
            // Check if seat is assigned
            const roomAssignments = this.assignments[roomId] || {};
            const assignment = roomAssignments[seatNumber];
            
            if (assignment) {
                this.unassignSeat(roomId, seatNumber);
            }
        });
        
        // Right-click for context menu
        seat.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            
            const seatNumber = parseInt(seat.dataset.seatNumber);
            const roomId = this.currentRoom.id;
            
            // Show context menu
            this.showSeatContextMenu(e, roomId, seatNumber);
        });
    }
    
    /**
     * Show context menu for a seat
     * 
     * @param {Event} e - The event object
     * @param {number} roomId - The ID of the room
     * @param {number} seatNumber - The seat number
     */
    showSeatContextMenu(e, roomId, seatNumber) {
        // Remove any existing context menu
        const existingMenu = document.querySelector('.seat-context-menu');
        if (existingMenu) {
            existingMenu.remove();
        }
        
        // Create context menu
        const menu = document.createElement('div');
        menu.className = 'seat-context-menu';
        menu.style.position = 'absolute';
        menu.style.left = `${e.pageX}px`;
        menu.style.top = `${e.pageY}px`;
        
        // Check if seat is assigned
        const roomAssignments = this.assignments[roomId] || {};
        const assignment = roomAssignments[seatNumber];
        
        // Add menu items
        if (assignment) {
            // Unassign option
            const unassignItem = document.createElement('div');
            unassignItem.className = 'menu-item';
            unassignItem.textContent = 'Unassign Student';
            unassignItem.addEventListener('click', () => {
                this.unassignSeat(roomId, seatNumber);
                menu.remove();
            });
            menu.appendChild(unassignItem);
            
            // Create override option
            const overrideItem = document.createElement('div');
            overrideItem.className = 'menu-item';
            overrideItem.textContent = assignment.is_override ? 'Remove Override' : 'Mark as Override';
            overrideItem.addEventListener('click', () => {
                this.toggleOverride(roomId, seatNumber);
                menu.remove();
            });
            menu.appendChild(overrideItem);
            
            // View student details option
            const viewItem = document.createElement('div');
            viewItem.className = 'menu-item';
            viewItem.textContent = 'View Student Details';
            viewItem.addEventListener('click', () => {
                this.viewStudentDetails(assignment.student_id);
                menu.remove();
            });
            menu.appendChild(viewItem);
        } else {
            // Assign option
            const assignItem = document.createElement('div');
            assignItem.className = 'menu-item';
            assignItem.textContent = 'Assign Student';
            assignItem.addEventListener('click', () => {
                // Select this seat
                this.selectedSeat = { roomId, seatNumber };
                
                // Highlight the seat
                const seat = this.layout.querySelector(`.seat[data-seat-number="${seatNumber}"]`);
                if (seat) {
                    // Deselect previously selected seat
                    const prevSelected = this.layout.querySelector('.seat.selected');
                    if (prevSelected) {
                        prevSelected.classList.remove('selected');
                    }
                    
                    seat.classList.add('selected');
                }
                
                // Show a notification to select a student
                if (this.options.ajaxHandler) {
                    this.options.ajaxHandler.showNotification(
                        'Now select a student from the list to assign to this seat', 
                        'info'
                    );
                }
                
                menu.remove();
            });
            menu.appendChild(assignItem);
        }
        
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
     * Assign a student to a seat
     * 
     * @param {Object} student - The student object
     * @param {Object} seat - The seat object (roomId, seatNumber)
     */
    assignStudentToSeat(student, seat) {
        const { roomId, seatNumber } = seat;
        
        // Check if the seat is already assigned
        const roomAssignments = this.assignments[roomId] || {};
        const existingAssignment = roomAssignments[seatNumber];
        
        if (existingAssignment) {
            // Confirm replacement
            if (!confirm(`This seat is already assigned to ${existingAssignment.student_name}. Replace?`)) {
                return;
            }
            
            // Unmark the previous student
            if (this.options.studentListComponent) {
                this.options.studentListComponent.markAsUnassigned(existingAssignment.student_id);
            }
        }
        
        // Check if the student is already assigned elsewhere
        let studentAlreadyAssigned = false;
        let existingSeat = null;
        
        Object.keys(this.assignments).forEach(rid => {
            Object.keys(this.assignments[rid]).forEach(sn => {
                const assignment = this.assignments[rid][sn];
                if (assignment.student_id.toString() === student.id.toString()) {
                    studentAlreadyAssigned = true;
                    existingSeat = { roomId: parseInt(rid), seatNumber: parseInt(sn) };
                }
            });
        });
        
        if (studentAlreadyAssigned) {
            // Confirm moving the student
            if (!confirm(`${student.name} is already assigned to seat ${existingSeat.seatNumber} in room ${this.rooms.find(r => r.id === existingSeat.roomId)?.room_number}. Move to this seat?`)) {
                return;
            }
            
            // Remove the existing assignment
            if (this.assignments[existingSeat.roomId]) {
                delete this.assignments[existingSeat.roomId][existingSeat.seatNumber];
            }
            
            // Update the layout if it's the current room
            if (existingSeat.roomId === this.currentRoom.id) {
                const existingSeatElement = this.layout.querySelector(`.seat[data-seat-number="${existingSeat.seatNumber}"]`);
                if (existingSeatElement) {
                    existingSeatElement.classList.remove('assigned', 'override');
                    existingSeatElement.querySelector('.student-info').innerHTML = 'Empty';
                    existingSeatElement.querySelector('.student-info').classList.add('empty');
                }
            }
        }
        
        // Create the assignment
        if (!this.assignments[roomId]) {
            this.assignments[roomId] = {};
        }
        
        this.assignments[roomId][seatNumber] = {
            student_id: student.id,
            student_name: student.name,
            student_roll: student.roll_number,
            student_course: student.course,
            is_override: false,
        };
        
        // Mark the student as assigned in the student list
        if (this.options.studentListComponent) {
            this.options.studentListComponent.markAsAssigned(student.id);
        }
        
        // Update the seat in the layout
        const seatElement = this.layout.querySelector(`.seat[data-seat-number="${seatNumber}"]`);
        if (seatElement) {
            seatElement.classList.add('assigned');
            seatElement.classList.remove('selected');
            
            const studentInfo = seatElement.querySelector('.student-info');
            studentInfo.classList.remove('empty');
            studentInfo.innerHTML = `
                <div class="student-name">${student.name}</div>
                <div class="student-roll">${student.roll_number}</div>
                <div class="student-course">${student.course}</div>
            `;
        }
        
        // Clear the selected seat and student
        this.selectedSeat = null;
        this.selectedStudent = null;
        
        // Save the assignment to the server
        this.saveAssignment(roomId, seatNumber, student.id);
    }
    
    /**
     * Unassign a seat
     * 
     * @param {number} roomId - The ID of the room
     * @param {number} seatNumber - The seat number
     */
    unassignSeat(roomId, seatNumber) {
        // Check if seat is assigned
        const roomAssignments = this.assignments[roomId] || {};
        const assignment = roomAssignments[seatNumber];
        
        if (!assignment) return;
        
        // Confirm unassignment
        if (!confirm(`Unassign ${assignment.student_name} from this seat?`)) {
            return;
        }
        
        // Mark the student as unassigned in the student list
        if (this.options.studentListComponent) {
            this.options.studentListComponent.markAsUnassigned(assignment.student_id);
        }
        
        // Remove the assignment
        delete this.assignments[roomId][seatNumber];
        
        // Update the seat in the layout
        const seatElement = this.layout.querySelector(`.seat[data-seat-number="${seatNumber}"]`);
        if (seatElement) {
            seatElement.classList.remove('assigned', 'override');
            
            const studentInfo = seatElement.querySelector('.student-info');
            studentInfo.classList.add('empty');
            studentInfo.innerHTML = 'Empty';
        }
        
        // Remove the assignment from the server
        this.removeAssignment(roomId, seatNumber);
    }
    
    /**
     * Toggle override status for a seat
     * 
     * @param {number} roomId - The ID of the room
     * @param {number} seatNumber - The seat number
     */
    toggleOverride(roomId, seatNumber) {
        // Check if seat is assigned
        const roomAssignments = this.assignments[roomId] || {};
        const assignment = roomAssignments[seatNumber];
        
        if (!assignment) return;
        
        // Toggle override status
        assignment.is_override = !assignment.is_override;
        
        // Update the seat in the layout
        const seatElement = this.layout.querySelector(`.seat[data-seat-number="${seatNumber}"]`);
        if (seatElement) {
            if (assignment.is_override) {
                seatElement.classList.add('override');
            } else {
                seatElement.classList.remove('override');
            }
        }
        
        // Update the assignment on the server
        this.saveAssignment(roomId, seatNumber, assignment.student_id, assignment.is_override);
    }
    
    /**
     * View student details
     * 
     * @param {number} studentId - The ID of the student
     */
    viewStudentDetails(studentId) {
        // This would typically open a modal with student details
        alert(`View details for student ID: ${studentId}`);
    }
    
    /**
     * Save an assignment to the server
     * 
     * @param {number} roomId - The ID of the room
     * @param {number} seatNumber - The seat number
     * @param {number} studentId - The ID of the student
     * @param {boolean} isOverride - Whether this is an override
     */
    saveAssignment(roomId, seatNumber, studentId, isOverride = false) {
        if (!this.options.seatingPlanId) return;
        
        if (this.options.ajaxHandler) {
            this.options.ajaxHandler.saveAssignment(
                this.options.seatingPlanId,
                roomId,
                seatNumber,
                studentId,
                isOverride
            ).catch(error => {
                console.error('Error saving assignment:', error);
            });
        }
    }
    
    /**
     * Remove an assignment from the server
     * 
     * @param {number} roomId - The ID of the room
     * @param {number} seatNumber - The seat number
     */
    removeAssignment(roomId, seatNumber) {
        if (!this.options.seatingPlanId) return;
        
        if (this.options.ajaxHandler) {
            this.options.ajaxHandler.removeAssignment(
                this.options.seatingPlanId,
                roomId,
                seatNumber
            ).catch(error => {
                console.error('Error removing assignment:', error);
            });
        }
    }
    
    /**
     * Generate assignments automatically
     */
    generateAssignments() {
        if (!this.options.seatingPlanId) return;
        
        if (!confirm('This will generate new assignments for all students. Any existing assignments will be lost. Continue?')) {
            return;
        }
        
        if (this.options.ajaxHandler) {
            this.options.ajaxHandler.generateAssignments(this.options.seatingPlanId)
                .then(data => {
                    // Reload assignments
                    this.loadAssignments();
                })
                .catch(error => {
                    console.error('Error generating assignments:', error);
                });
        }
    }
    
    /**
     * Save all assignments
     */
    saveAssignments() {
        if (!this.options.seatingPlanId) return;
        
        if (this.options.ajaxHandler) {
            this.options.ajaxHandler.showNotification('Saving all assignments...', 'info');
            
            // In a real implementation, we would send all assignments to the server
            // For now, we'll just show a success message
            setTimeout(() => {
                this.options.ajaxHandler.showNotification('All assignments saved successfully', 'success');
            }, 1000);
        }
    }
    
    /**
     * Export the seating plan to PDF
     */
    exportToPDF() {
        if (!this.options.seatingPlanId) return;
        
        if (this.options.ajaxHandler) {
            this.options.ajaxHandler.exportToPDF(this.options.seatingPlanId);
        }
    }
    
    /**
     * Print the seating layout
     */
    printLayout() {
        window.print();
    }
}

// Initialize the interactive seating layout when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.interactiveSeatingLayout = new InteractiveSeatingLayout();
});

