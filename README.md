# Exam Seat Management System

A comprehensive system for managing examination seating arrangements and question papers.

## Overview

The Exam Seat Management System automates the process of allocating students to examination rooms and blocks, ensuring fairness, efficiency, and adherence to guidelines. Additionally, it provides a comprehensive module for creating, managing, and generating question papers for exams.

## Technology Stack

- **Frontend**: HTML, CSS, Bootstrap, JavaScript
- **Backend**: Laravel (PHP)
- **Database**: MySQL

## Features

The system consists of two main modules:

### 1. Seat Plan Module
- Manage seating arrangements for various examination scenarios
- Maintain separate master tables for rooms, students, and blocks
- Generate seating plans based on predefined rules and constraints
- Handle special cases such as students with disabilities, mixed branches, and emergency situations
- Provide digital seating charts and notifications to students and staff

### 2. Question Bank Module
- Organize questions hierarchically by Subject → Unit → Topic → Question Type
- Provide dynamic fields based on the selected question type
- Enable dynamic management of Bloom's Taxonomy levels
- Allow users to define blueprints for question papers with customizable conditions
- Generate question papers based on user-defined blueprints or randomly

## Project Progress Summary

### Completed Items
- ✅ Seat Plan Module - Frontend Views
  - Room Management Views
  - Course Management Views
  - Student Management Views
  - Invigilator Management Views
  - Seating Plan Views
  - Seating Rules Views
  - Layout and Dashboard
- ✅ Question Bank Module - Frontend Views
  - Subject Management Views
  - Unit Management Views
  - Topic Management Views
  - Bloom's Taxonomy Management Views
  - Question Management Views
  - Blueprint Management Views
  - Question Paper Management Views

### Pending Items
- ⏳ Seat Plan Module - Backend Implementation
  - Block Model and Controller
  - Room Model and Controller
  - Course Model and Controller
  - Student Model and Controller
  - Invigilator Model and Controller
  - Seating Plan Model and Controller
  - Seating Rules Model and Controller
  - Allocation Algorithms Implementation
- ⏳ Question Bank Module - Backend Implementation
  - Subject Model and Controller
  - Unit Model and Controller
  - Topic Model and Controller
  - Question Model and Controller
  - Bloom's Taxonomy Model and Controller
  - Blueprint Model and Controller
  - Question Paper Model and Controller
  - Question Paper Generation Algorithms
- ⏳ Authentication System
  - User Model and Migration
  - Role-based Access Control
  - Login/Logout Functionality
  - Admin Dashboard
  - User Dashboard
- ⏳ Notification System
  - Email Notifications
  - In-app Notifications
  - SMS Notifications (optional)
- ⏳ Reporting and Analytics
  - Seating Plan Reports
  - Student Distribution Reports
  - Room Utilization Reports
  - Question Paper Analytics
- ⏳ Mobile Student Portal
  - Mobile-friendly Views
  - Student Authentication
  - Seating Plan View for Students
  - Exam Schedule View

## Detailed Implementation Status

### Seat Plan Module (Frontend: 100%, Backend: 0%)

#### Frontend Views (Completed)
- ✅ Room Management Views
  - `rooms/index.blade.php`: List of all rooms
  - `rooms/create.blade.php`: Form for creating new rooms
  - `rooms/edit.blade.php`: Form for editing room details
  - `rooms/show.blade.php`: Detailed view of a room with statistics
  - `rooms/layout.blade.php`: Interactive room layout visualization
- ✅ Course Management Views
  - `courses/index.blade.php`: List of all courses with filtering and actions
  - `courses/create.blade.php`: Form for creating new courses
  - `courses/edit.blade.php`: Form for editing course details
  - `courses/show.blade.php`: Detailed view of a course with student statistics and charts
- ✅ Student Management Views
  - `students/index.blade.php`: List of all students with filtering and actions
  - `students/create.blade.php`: Form for creating new students
  - `students/edit.blade.php`: Form for editing student details
  - `students/show.blade.php`: Detailed view of a student with ID card and exam history
  - `students/import.blade.php`: Interface for bulk importing students from Excel/CSV
- ✅ Invigilator Management Views
  - `invigilators/index.blade.php`: List of all invigilators with filtering and actions
  - `invigilators/create.blade.php`: Form for creating new invigilators
  - `invigilators/edit.blade.php`: Form for editing invigilator details
  - `invigilators/show.blade.php`: Detailed view of an invigilator with duty calendar and assignment history
- ✅ Seating Plan Views
  - `seating-plans/index.blade.php`: List of all seating plans with filtering and actions
  - `seating-plans/create.blade.php`: Form for creating new seating plans with allocation methods
  - `seating-plans/edit.blade.php`: Form for editing seating plan details
  - `seating-plans/show.blade.php`: Detailed view of a seating plan with rooms, students, and statistics
- ✅ Seating Rules Views
  - `seating-rules/index.blade.php`: List of all seating rules with filtering and actions
  - `seating-rules/create.blade.php`: Form for creating new seating rules with templates

#### Backend Implementation (Pending)
- ⏳ Models and Migrations
  - Block Model and Migration
  - Room Model and Migration
  - Course Model and Migration
  - Student Model and Migration
  - Invigilator Model and Migration
  - SeatingPlan Model and Migration
  - SeatingRule Model and Migration
- ⏳ Controllers
  - BlockController with CRUD operations
  - RoomController with CRUD operations
  - CourseController with CRUD operations
  - StudentController with CRUD operations
  - InvigilatorController with CRUD operations
  - SeatingPlanController with allocation algorithms
  - SeatingRuleController with rule management
- ⏳ Services
  - AllocationService for seating algorithms
  - ImportService for student data import
  - ExportService for seating plan export
- ⏳ Allocation Algorithms
  - Random allocation
  - Sequential allocation
  - Alternate course allocation
  - Mixed allocation

### Question Bank Module (Frontend: 100%, Backend: 0%)

#### Frontend Views (Completed)
- ✅ Subject Management Views
- ✅ Unit Management Views
- ✅ Topic Management Views
- ✅ Bloom's Taxonomy Management Views
- ✅ Question Management Views
- ✅ Blueprint Management Views
- ✅ Question Paper Management Views

#### Backend Implementation (Pending)
- ⏳ Models and Migrations
- ⏳ Controllers
- ⏳ Services
- ⏳ Question Paper Generation Algorithms

### Next Steps (Priority Order)
1. Implement Models and Migrations for Seat Plan Module
2. Implement Controllers for Seat Plan Module
3. Implement Allocation Algorithms
4. Implement Models and Migrations for Question Bank Module
5. Implement Controllers for Question Bank Module
6. Implement Question Paper Generation Algorithms
7. Implement Authentication System
8. Implement Notification System
9. Implement Reporting and Analytics
10. Implement Mobile Student Portal

## Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Seed the database: `php artisan db:seed`
7. Start the development server: `php artisan serve`

## Default Admin Credentials

- **Email**: admin@kalvierp.com
- **Password**: kalvierp2025@coe

## License

This project is licensed under the MIT License.

