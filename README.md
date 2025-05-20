# Exam Seat Management System

A comprehensive system for managing examination seating arrangements and question paper generation.

## Project Overview

The Exam Seat Management System is designed to automate the process of allocating students to examination rooms and blocks, ensuring fairness, efficiency, and adherence to guidelines. Additionally, it provides a comprehensive module for creating, managing, and generating question papers for exams.

### Technology Stack

- **Frontend**: HTML, CSS, Bootstrap 5, JavaScript
- **Backend**: Laravel 10
- **Database**: MySQL

## Features

### Seat Plan Module
- Manage seating arrangements for various examination scenarios
- Maintain separate master tables for rooms, students, and blocks
- Generate seating plans based on predefined rules and constraints
- Handle special cases such as students with disabilities, mixed branches, and emergency situations
- Provide digital seating charts and notifications to students and staff

### Question Bank Module
- Organize questions hierarchically by Subject → Unit → Topic → Question Type
- Provide dynamic fields based on the selected question type
- Enable dynamic management of Bloom's Taxonomy levels (add, edit, delete)
- Allow users to define blueprints for question papers with customizable conditions
- Generate question papers based on user-defined blueprints or randomly

## Project Status

### Completed Tasks

#### Database Structure
- [x] Created migrations for all required tables:
  - Blocks
  - Rooms
  - Courses
  - Students
  - Seating Plans
  - Subjects
  - Units
  - Topics
  - Blooms Taxonomy
  - Questions
  - Blueprints
  - Question Papers
  - Invigilators
  - Room Invigilator Assignments

#### Models
- [x] Created all required models with relationships:
  - Block
  - Room
  - Course
  - Student
  - SeatingPlan
  - Subject
  - Unit
  - Topic
  - BloomsTaxonomy
  - Question
  - Blueprint
  - QuestionPaper
  - Invigilator
  - RoomInvigilatorAssignment

#### Controllers
- [x] Created controllers for all modules:
  - Auth/LoginController
  - DashboardController
  - BlockController
  - RoomController
  - CourseController
  - StudentController
  - SeatingPlanController
  - SubjectController
  - UnitController
  - TopicController
  - BloomsTaxonomyController
  - QuestionController
  - BlueprintController
  - QuestionPaperController
  - InvigilatorController
  - RoomInvigilatorAssignmentController

#### Views
- [x] Created layout template with navy blue theme
- [x] Implemented responsive design with Bootstrap 5
- [x] Created login page with custom design
- [x] Created dashboard with statistics and quick actions
- [x] Implemented Block management views (index, create, edit, show)
- [x] Implemented Room management views (index, create, edit, show)

### Pending Tasks

#### Views
- [ ] Create Course management views (index, create, edit, show)
- [ ] Create Student management views (index, create, edit, show)
- [ ] Create Invigilator management views (index, create, edit, show)
- [ ] Create Seating Plan management views (index, create, edit, show)
- [ ] Create Subject management views (index, create, edit, show)
- [ ] Create Unit management views (index, create, edit, show)
- [ ] Create Topic management views (index, create, edit, show)
- [ ] Create Bloom's Taxonomy management views (index, create, edit, show)
- [ ] Create Question management views (index, create, edit, show)
- [ ] Create Blueprint management views (index, create, edit, show)
- [ ] Create Question Paper management views (index, create, edit, show)

#### Functionality
- [ ] Implement authentication system with proper middleware
- [ ] Implement seating arrangement algorithm
- [ ] Develop question paper generation system
- [ ] Add import/export functionality for student data
- [ ] Implement user roles and permissions
- [ ] Add validation for all forms
- [ ] Implement search and filter functionality
- [ ] Add reporting and analytics features

#### Testing
- [ ] Write unit tests for models
- [ ] Write feature tests for controllers
- [ ] Perform user acceptance testing

## Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/examseat.git
```

2. Install dependencies
```bash
composer install
npm install
```

3. Create .env file and configure database
```bash
cp .env.example .env
php artisan key:generate
```

4. Run migrations
```bash
php artisan migrate
```

5. Seed the database (optional)
```bash
php artisan db:seed
```

6. Start the development server
```bash
php artisan serve
```

## Login Credentials

- **Username**: admin@kalvierp.com
- **Password**: kalvierp2025@coe

## License

This project is licensed under the MIT License.

