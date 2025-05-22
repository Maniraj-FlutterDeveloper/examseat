/**
 * Student List Component for Seating Plan Module
 * 
 * This script provides functionality for the student list component,
 * including drag-and-drop functionality for assigning students to seats.
 */

class StudentListComponent {
    constructor(options = {}) {
        this.options = Object.assign({
            containerSelector: '#student-list-container',
            listSelector: '#student-list',
            searchSelector: '#student-search',
            filterSelector: '#student-filter',
            paginationSelector: '#student-pagination',
            studentItemTemplate: '#student-item-template',
            studentsPerPage: 20,
            ajaxHandler: window.seatingAjaxHandler,
        }, options);
        
        this.currentPage = 1;
        this.totalPages = 1;
        this.students = [];
        this.filteredStudents = [];
        this.searchTimeout = null;
        
        this.init();
    }
    
    /**
     * Initialize the component
     */
    init() {
        this.container = document.querySelector(this.options.containerSelector);
        this.list = document.querySelector(this.options.listSelector);
        this.searchInput = document.querySelector(this.options.searchSelector);
        this.filterSelect = document.querySelector(this.options.filterSelector);
        this.pagination = document.querySelector(this.options.paginationSelector);
        this.template = document.querySelector(this.options.studentItemTemplate);
        
        if (!this.container || !this.list) {
            console.error('Student list container or list not found');
            return;
        }
        
        this.initEventListeners();
        this.loadStudents();
    }
    
    /**
     * Initialize event listeners
     */
    initEventListeners() {
        // Search input
        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.searchStudents(this.searchInput.value);
                }, 300);
            });
        }
        
        // Filter select
        if (this.filterSelect) {
            this.filterSelect.addEventListener('change', () => {
                this.filterStudents(this.filterSelect.value);
            });
        }
        
        // Pagination
        if (this.pagination) {
            this.pagination.addEventListener('click', (e) => {
                if (e.target.tagName === 'A' && e.target.dataset.page) {
                    e.preventDefault();
                    this.goToPage(parseInt(e.target.dataset.page));
                }
            });
        }
    }
    
    /**
     * Load students from the server or from provided data
     * 
     * @param {Array} students - Optional array of students to use instead of fetching
     */
    loadStudents(students = null) {
        if (students) {
            this.students = students;
            this.filteredStudents = [...this.students];
            this.renderStudents();
            return;
        }
        
        // If no students provided, we would typically fetch them from the server
        // For now, we'll just use some dummy data
        this.students = this.getDummyStudents();
        this.filteredStudents = [...this.students];
        this.renderStudents();
    }
    
    /**
     * Get dummy student data for testing
     * 
     * @returns {Array} - Array of dummy student objects
     */
    getDummyStudents() {
        const students = [];
        const courses = ['B.Tech CS', 'B.Tech ECE', 'B.Tech ME', 'BBA', 'MBA', 'B.Com'];
        
        for (let i = 1; i <= 50; i++) {
            students.push({
                id: i,
                name: `Student ${i}`,
                roll_number: `R${i.toString().padStart(3, '0')}`,
                course: courses[Math.floor(Math.random() * courses.length)],
                year: Math.floor(Math.random() * 4) + 1,
                section: String.fromCharCode(65 + Math.floor(Math.random() * 4)), // A, B, C, or D
                has_disability: Math.random() < 0.05, // 5% chance of having a disability
            });
        }
        
        return students;
    }
    
    /**
     * Render the student list
     */
    renderStudents() {
        if (!this.list) return;
        
        // Clear the list
        this.list.innerHTML = '';
        
        // Calculate pagination
        const totalStudents = this.filteredStudents.length;
        this.totalPages = Math.ceil(totalStudents / this.options.studentsPerPage);
        
        // Get students for the current page
        const startIndex = (this.currentPage - 1) * this.options.studentsPerPage;
        const endIndex = Math.min(startIndex + this.options.studentsPerPage, totalStudents);
        const pageStudents = this.filteredStudents.slice(startIndex, endIndex);
        
        // Render each student
        pageStudents.forEach(student => {
            const item = this.createStudentItem(student);
            this.list.appendChild(item);
        });
        
        // Render pagination
        this.renderPagination();
        
        // Initialize drag and drop
        this.initDragAndDrop();
    }
    
    /**
     * Create a student list item
     * 
     * @param {Object} student - The student object
     * @returns {HTMLElement} - The student list item element
     */
    createStudentItem(student) {
        if (this.template) {
            // Use the template if available
            const clone = document.importNode(this.template.content, true);
            const item = clone.querySelector('.student-item');
            
            // Set data attributes
            item.dataset.studentId = student.id;
            item.dataset.studentName = student.name;
            item.dataset.studentRoll = student.roll_number;
            item.dataset.studentCourse = student.course;
            item.dataset.studentYear = student.year;
            item.dataset.studentSection = student.section;
            
            // Set content
            item.querySelector('.student-name').textContent = student.name;
            item.querySelector('.student-roll').textContent = student.roll_number;
            item.querySelector('.student-course').textContent = student.course;
            
            // Add disability indicator if applicable
            if (student.has_disability) {
                item.classList.add('has-disability');
                const indicator = document.createElement('span');
                indicator.className = 'disability-indicator';
                indicator.textContent = 'Special Needs';
                item.appendChild(indicator);
            }
            
            return item;
        } else {
            // Create a simple item if no template is available
            const item = document.createElement('div');
            item.className = 'student-item';
            item.dataset.studentId = student.id;
            item.dataset.studentName = student.name;
            item.dataset.studentRoll = student.roll_number;
            item.dataset.studentCourse = student.course;
            
            item.innerHTML = `
                <div class="student-name">${student.name}</div>
                <div class="student-roll">${student.roll_number}</div>
                <div class="student-course">${student.course}</div>
            `;
            
            if (student.has_disability) {
                item.classList.add('has-disability');
                const indicator = document.createElement('span');
                indicator.className = 'disability-indicator';
                indicator.textContent = 'Special Needs';
                item.appendChild(indicator);
            }
            
            return item;
        }
    }
    
    /**
     * Render pagination controls
     */
    renderPagination() {
        if (!this.pagination) return;
        
        this.pagination.innerHTML = '';
        
        if (this.totalPages <= 1) {
            this.pagination.style.display = 'none';
            return;
        }
        
        this.pagination.style.display = 'block';
        
        // Previous button
        const prevLi = document.createElement('li');
        prevLi.className = `page-item ${this.currentPage === 1 ? 'disabled' : ''}`;
        
        const prevLink = document.createElement('a');
        prevLink.className = 'page-link';
        prevLink.href = '#';
        prevLink.dataset.page = this.currentPage - 1;
        prevLink.innerHTML = '&laquo;';
        
        prevLi.appendChild(prevLink);
        this.pagination.appendChild(prevLi);
        
        // Page numbers
        for (let i = 1; i <= this.totalPages; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === this.currentPage ? 'active' : ''}`;
            
            const link = document.createElement('a');
            link.className = 'page-link';
            link.href = '#';
            link.dataset.page = i;
            link.textContent = i;
            
            li.appendChild(link);
            this.pagination.appendChild(li);
        }
        
        // Next button
        const nextLi = document.createElement('li');
        nextLi.className = `page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}`;
        
        const nextLink = document.createElement('a');
        nextLink.className = 'page-link';
        nextLink.href = '#';
        nextLink.dataset.page = this.currentPage + 1;
        nextLink.innerHTML = '&raquo;';
        
        nextLi.appendChild(nextLink);
        this.pagination.appendChild(nextLi);
    }
    
    /**
     * Go to a specific page
     * 
     * @param {number} page - The page number
     */
    goToPage(page) {
        if (page < 1 || page > this.totalPages) return;
        
        this.currentPage = page;
        this.renderStudents();
    }
    
    /**
     * Search students by name or roll number
     * 
     * @param {string} query - The search query
     */
    searchStudents(query) {
        if (!query) {
            this.filteredStudents = [...this.students];
        } else {
            query = query.toLowerCase();
            this.filteredStudents = this.students.filter(student => {
                return student.name.toLowerCase().includes(query) || 
                       student.roll_number.toLowerCase().includes(query);
            });
        }
        
        this.currentPage = 1;
        this.renderStudents();
    }
    
    /**
     * Filter students by course, year, or section
     * 
     * @param {string} filter - The filter value
     */
    filterStudents(filter) {
        if (!filter || filter === 'all') {
            this.filteredStudents = [...this.students];
        } else {
            // Parse the filter value (format: type:value)
            const [type, value] = filter.split(':');
            
            this.filteredStudents = this.students.filter(student => {
                if (type === 'course') {
                    return student.course === value;
                } else if (type === 'year') {
                    return student.year.toString() === value;
                } else if (type === 'section') {
                    return student.section === value;
                } else if (type === 'disability') {
                    return student.has_disability === (value === 'yes');
                }
                return true;
            });
        }
        
        this.currentPage = 1;
        this.renderStudents();
    }
    
    /**
     * Initialize drag and drop functionality
     */
    initDragAndDrop() {
        const studentItems = this.list.querySelectorAll('.student-item');
        
        studentItems.forEach(item => {
            item.setAttribute('draggable', 'true');
            
            item.addEventListener('dragstart', (e) => {
                e.dataTransfer.setData('text/plain', item.dataset.studentId);
                e.dataTransfer.effectAllowed = 'move';
                item.classList.add('dragging');
            });
            
            item.addEventListener('dragend', () => {
                item.classList.remove('dragging');
            });
            
            // Double-click to select
            item.addEventListener('dblclick', () => {
                this.selectStudent(item.dataset.studentId);
            });
        });
    }
    
    /**
     * Select a student (for assignment)
     * 
     * @param {string} studentId - The ID of the student
     */
    selectStudent(studentId) {
        // Find the student
        const student = this.students.find(s => s.id.toString() === studentId.toString());
        if (!student) return;
        
        // Dispatch a custom event
        const event = new CustomEvent('student-selected', {
            detail: { student }
        });
        document.dispatchEvent(event);
    }
    
    /**
     * Mark a student as assigned
     * 
     * @param {string} studentId - The ID of the student
     */
    markAsAssigned(studentId) {
        const items = this.list.querySelectorAll(`.student-item[data-student-id="${studentId}"]`);
        items.forEach(item => {
            item.classList.add('assigned');
        });
    }
    
    /**
     * Mark a student as unassigned
     * 
     * @param {string} studentId - The ID of the student
     */
    markAsUnassigned(studentId) {
        const items = this.list.querySelectorAll(`.student-item[data-student-id="${studentId}"]`);
        items.forEach(item => {
            item.classList.remove('assigned');
        });
    }
}

// Initialize the student list component when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.studentListComponent = new StudentListComponent();
});

