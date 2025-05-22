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
- ✅ Database Setup and Configuration
  - Database user 'examseat' created with proper permissions
  - Basic migrations completed (15 migrations from 2023)
  - Admin user seeder implemented with specified credentials
- ✅ Models Implementation
  - All core models created and properly structured
  - Relationships defined between models
- ✅ Controllers Implementation
  - Seat Plan Module Controllers (BlockController, CourseController, RoomController, etc.)
  - Question Bank Module Controllers (BloomsTaxonomyController, BlueprintController, etc.)
- ✅ Services Implementation
  - InvigilatorReportService
  - SeatingPlanPdfService
  - SeatingRuleService
  - StudentNotificationService
- ✅ Views Implementation
  - Seat Plan Module Views (Blocks, Rooms, Courses, Students, etc.)
  - Question Bank Module Views (Subjects, Units, Topics, Questions, etc.)
  - Mobile Views (Student dashboard, Login/Profile, Seating plans, etc.)
- ✅ Authentication System
  - Basic login/logout functionality
  - Password reset functionality
  - User registration

### Pending Items
1. **Database Migrations**
   - ⏳ Resolve conflicts between 2023 and 2025 migrations
   - ⏳ Implement remaining 24 migrations from 2025
   - ⏳ Add indexes for performance optimization
   - ⏳ Add foreign key constraints where missing

2. **Authentication and Authorization**
   - ⏳ Implement role-based access control (RBAC)
   - ⏳ Add middleware for role-specific routes
   - ⏳ Implement two-factor authentication (optional)
   - ⏳ Implement API authentication for mobile access

3. **Core Functionality**
   - ⏳ Implement seating allocation algorithms
   - ⏳ Add question paper generation logic
   - ⏳ Implement student import/export functionality
   - ⏳ Add validation for all form submissions
   - ⏳ Implement file upload handling

4. **Notification System**
   - ⏳ Set up email notification service
   - ⏳ Implement in-app notifications
   - ⏳ Add SMS notification capability (optional)
   - ⏳ Create notification templates
   - ⏳ Add notification preferences

5. **Reporting and Analytics**
   - ⏳ Implement dashboard analytics
   - ⏳ Add seating plan reports
   - ⏳ Create question paper analytics
   - ⏳ Add student performance reports
   - ⏳ Implement export functionality for reports

6. **Mobile Application Enhancement**
   - ⏳ Implement push notifications
   - ⏳ Add offline capability
   - ⏳ Implement real-time updates
   - ⏳ Add barcode/QR code scanning
   - ⏳ Optimize mobile views for performance

7. **Testing**
   - ⏳ Write unit tests for models
   - ⏳ Add integration tests for controllers
   - ⏳ Implement feature tests
   - ⏳ Add API endpoint tests
   - ⏳ Perform security testing

8. **Documentation**
   - ⏳ Update API documentation
   - ⏳ Add code documentation
   - ⏳ Create user manual
   - ⏳ Document deployment process
   - ⏳ Add system architecture documentation

9. **Performance Optimization**
   - ⏳ Implement caching
   - ⏳ Optimize database queries
   - ⏳ Add job queues for long-running tasks
   - ⏳ Implement rate limiting
   - ⏳ Add request validation

10. **Security Enhancements**
    - ⏳ Implement input sanitization
    - ⏳ Add CSRF protection
    - ⏳ Implement rate limiting
    - ⏳ Add security headers
    - ⏳ Implement audit logging

## Next Steps (Priority Order)
1. Resolve migration conflicts and implement remaining migrations
2. Implement seating allocation algorithms
3. Add question paper generation logic
4. Implement role-based access control
5. Set up notification system
6. Implement reporting and analytics
7. Enhance mobile application
8. Add comprehensive testing
9. Optimize performance
10. Enhance security

## Installation

1. Clone the repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Generate application key: `php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Seed the database: `php artisan db:seed --class=AdminUserSeeder`
7. Start the development server: `php artisan serve`

## Default Admin Credentials

- **Email**: admin@kalvierp.com
- **Password**: kalvierp2025@coe

## License

This project is licensed under the MIT License.

