# Exam Seat Management System

A comprehensive system for managing examination seating arrangements and question paper generation.

## Overview

The Exam Seat Management System is designed to automate the process of allocating students to examination rooms and blocks, ensuring fairness, efficiency, and adherence to guidelines. Additionally, it provides a comprehensive module for creating, managing, and generating question papers for exams.

## Technology Stack

- **Frontend**: HTML, CSS, Bootstrap, JavaScript
- **Backend**: Laravel
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

## Progress Report

### Completed Tasks:

#### Database Structure
- ✅ Created migrations for all 17 required tables:
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
  - Users
  - Cache
  - Jobs

#### Models
- ✅ Created all 15 models with relationships:
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
  - User

#### Controllers
- ✅ Created all 16 controllers:
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
- ✅ Created layout template with navy blue theme
- ✅ Implemented responsive design with Bootstrap 5
- ✅ Created login page with custom design
- ✅ Created dashboard with statistics and quick actions
- ✅ Implemented Block management views (index, create, edit, show)
- ✅ Implemented Room management views (index, create, edit, show)
- ✅ Implemented Course management views (index, create, edit, show)
- ✅ Implemented Student management views (index, create, edit, show, import)
- ✅ Implemented Subject management views (index, create, edit, show)
- ✅ Implemented Unit management views (index, create, edit, show)
- ✅ Implemented Topic management views (index, create, edit, show)
- ✅ Implemented Bloom's Taxonomy management views (index, create, edit, show)
- ✅ Implemented Question management views (index, create, edit, show)
- ✅ Implemented Blueprint management views (index, create, edit, show)

### Pending Tasks:

#### Views
- ❌ Create Question Paper management views (index, create, edit, show)
- ❌ Create Invigilator management views (index, create, edit, show)
- ❌ Create Seating Plan management views (index, create, edit, show)
- ❌ Create Room Invigilator Assignment views (index, create, edit, show)

#### Functionality
- ❌ Implement authentication system with proper middleware
- ❌ Implement seating arrangement algorithm
- ❌ Develop question paper generation system
- ❌ Add import/export functionality for student data
- ❌ Implement user roles and permissions
- ❌ Add validation for all forms
- ❌ Implement search and filter functionality
- ❌ Add reporting and analytics features

#### Testing
- ❌ Write unit tests for models
- ❌ Write feature tests for controllers
- ❌ Perform user acceptance testing

## Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Seed the database: `php artisan db:seed`
7. Start the development server: `php artisan serve`

## Login Credentials

- **Username**: admin@kalvierp.com
- **Password**: kalvierp2025@coe

## License

This project is licensed under the MIT License.

