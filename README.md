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

### Completed Modules (100%)
- ✅ Authentication System
- ✅ Seat Plan Module - Backend
- ✅ Seat Plan Module - Frontend
- ✅ Question Bank Module - Backend
- ✅ Question Bank Module - Frontend
- ✅ Notification System
- ✅ Reporting and Analytics
- ✅ User Management Interface
- ✅ System Settings
- ✅ Mobile Student Portal

### All Modules Completed! 🎉

## Detailed Project Progress

### Completed Tasks

#### Authentication System (100% Complete)
- ✅ Created LoginController with login/logout functionality
- ✅ Added role-based access control (admin/user)
- ✅ Implemented user status management (active/inactive)
- ✅ Created login page with navy blue theme
- ✅ Added admin dashboard with statistics and quick actions
- ✅ Added user dashboard with upcoming exams and notifications
- ✅ Created AdminMiddleware for protecting admin routes
- ✅ Updated User model with role and status fields
- ✅ Created admin user seeder with default credentials

#### Seat Plan Module - Backend (100% Complete)
- ✅ Created Block model and migration
- ✅ Created Room model and migration
- ✅ Created Course model and migration
- ✅ Created Student model and migration
- ✅ Created SeatingPlan model and migration
- ✅ Implemented BlockController with CRUD operations
- ✅ Implemented RoomController with CRUD operations
- ✅ Implemented CourseController with CRUD operations
- ✅ Implemented StudentController with CRUD operations
- ✅ Implemented SeatingPlanController with allocation algorithms
- ✅ Added student import functionality
- ✅ Implemented four seating allocation strategies:
  - Random allocation
  - Sequential allocation
  - Alternate course allocation
  - Mixed allocation
- ✅ Set up all necessary routes for the Seat Plan Module

#### Seat Plan Module - Frontend (100% Complete)
- ✅ Created Block management views (index, create, edit, show)
- ✅ Created Room management views (index, create, edit, show)
- ✅ Created Course management views (index, create, edit, show)
- ✅ Created Student management views (index, create, edit, show, import)
- ✅ Created Seating Plan management views (index, create, edit, show, print)
- ✅ Implemented admin layout with responsive design and navy blue theme

#### Question Bank Module - Backend (100% Complete)
- ✅ Created Subject model and migration
- ✅ Created Unit model and migration
- ✅ Created Topic model and migration
- ✅ Created Question model and migration
- ✅ Created BloomsTaxonomy model and migration
- ✅ Created Blueprint model and migration
- ✅ Created QuestionPaper model and migration
- ✅ Implemented SubjectController with CRUD operations
- ✅ Implemented UnitController with CRUD operations
- ✅ Implemented TopicController with CRUD operations
- ✅ Implemented QuestionController with CRUD operations
- ✅ Implemented BloomsTaxonomyController with CRUD operations
- ✅ Implemented BlueprintController with CRUD operations
- ✅ Implemented QuestionPaperController with CRUD operations
- ✅ Implemented question paper generation algorithms (blueprint-based and random)
- ✅ Set up all necessary routes for the Question Bank Module
- ✅ Added AJAX endpoints for dynamic form population

#### Question Bank Module - Frontend (100% Complete)
- ✅ Created Subject management views (index, create, edit, show)
- ✅ Created Unit management views (index, create, edit, show)
- ✅ Created Topic management views (index, create, edit, show)
- ✅ Created Bloom's Taxonomy management views (index, create, edit, show)
- ✅ Created Question management views (index, create, edit, show)
- ✅ Created Blueprint management views (index, create, edit, show)
- ✅ Created Question Paper management views:
  - ✅ Index view with filtering options
  - ✅ Create view with blueprint-based and random generation options
  - ✅ Edit view with question reordering and replacement functionality
  - ✅ Show view with paper preview and statistics
- ✅ Implemented PDF generation for question papers with:
  - ✅ Professional formatting and styling
  - ✅ Support for all question types
  - ✅ Optional answer key and marking scheme sections
  - ✅ Proper page breaks and headers/footers

#### Notification System (100% Complete)
- ✅ Created Notification model and migration
- ✅ Implemented NotificationController with CRUD operations
- ✅ Created NotificationService for sending notifications
- ✅ Added notification routes
- ✅ Created notification views (index, show)
- ✅ Implemented notification badges and counters
- ✅ Added notification dropdown in admin layout
- ✅ Implemented notification read/unread status
- ✅ Added functionality to send notifications to:
  - ✅ Single user
  - ✅ Multiple users
  - ✅ All users
- ✅ Implemented real-time notification count updates

#### Reporting and Analytics (100% Complete)
- ✅ Created Report model and migration
- ✅ Created ReportResult model and migration
- ✅ Created Dashboard model and migration
- ✅ Created DashboardWidget model and migration
- ✅ Implemented ReportController with CRUD operations
- ✅ Implemented DashboardController for analytics dashboard
- ✅ Created ReportService for report generation
- ✅ Created AnalyticsService for data processing
- ✅ Added reporting and analytics routes
- ✅ Created report management views (index, create, edit, show, result)
- ✅ Created analytics dashboard with:
  - ✅ Customizable widgets
  - ✅ Interactive charts
  - ✅ Data tables
  - ✅ Metrics display
  - ✅ Drag-and-drop layout
- ✅ Implemented export functionality for reports (Excel, PDF)
- ✅ Added real-time data updates for dashboard widgets
- ✅ Implemented various chart types:
  - ✅ Bar charts
  - ✅ Line charts
  - ✅ Pie charts
  - ✅ Doughnut charts
  - ✅ Polar area charts
- ✅ Added data visualization for:
  - ✅ Student distribution
  - ✅ Room utilization
  - ✅ Question paper analytics
  - ✅ Seating plan statistics

#### User Management Interface (100% Complete)
- ✅ Created Role model and migration
- ✅ Created Permission model and migration
- ✅ Created UserProfile model and migration
- ✅ Created UserActivity model and migration
- ✅ Updated User model with roles and permissions
- ✅ Implemented UserController with CRUD operations
- ✅ Implemented RoleController with CRUD operations
- ✅ Implemented PermissionController with CRUD operations
- ✅ Implemented UserActivityController for activity logging
- ✅ Created UserService for user management
- ✅ Added CheckPermission middleware
- ✅ Created RolesAndPermissionsSeeder
- ✅ Added user management routes
- ✅ Implemented role-based access control
- ✅ Added user profile management
- ✅ Implemented user activity logging
- ✅ Added user preferences management

#### System Settings (100% Complete)
- ✅ Created Setting model and migration
- ✅ Implemented SettingsController
- ✅ Created SettingsService for settings management
- ✅ Added system configuration options
- ✅ Created settings routes
- ✅ Implemented theme customization
- ✅ Added email configuration settings
- ✅ Implemented backup settings
- ✅ Added system information page
- ✅ Created SettingsSeeder with default settings
- ✅ Implemented settings by group
- ✅ Added cache management
- ✅ Implemented email testing functionality

#### Mobile Student Portal (100% Complete)
- ✅ Created responsive student dashboard
- ✅ Implemented mobile-friendly seating plan view
- ✅ Added exam schedule view for students
- ✅ Created mobile notification center
- ✅ Implemented student profile management
- ✅ Added mobile-optimized question paper view
- ✅ Created responsive login and registration pages
- ✅ Implemented student authentication system
- ✅ Added mobile-specific layout and styling
- ✅ Created StudentAuth middleware
- ✅ Updated Student model for authentication
- ✅ Added mobile routes
- ✅ Implemented mobile-friendly navigation

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

