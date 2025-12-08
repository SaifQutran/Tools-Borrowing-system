Tool & Educational Equipment Lending System Documentation

Overview

Note: The entire website must be in Arabic and fully support Right-to-Left (RTL) layout. All pages, forms, dashboards, and tables should be displayed from right to left, and all labels, buttons, and content must be localized into Arabic.

A university-based lending system for educational tools and equipment built using Laravel with SQLite as the database engine. The system digitizes the lending workflow, allowing students and staff members to borrow tools easily using QR codes. The admin manages inventory, approvals, user accounts, and system settings.

System Roles

1. Student

When creating an account, a student must provide:

Full Name

Academic Number

Mobile Number

Major (selected from a dropdown list)

Academic Year / Level

2. Staff Member

During registration, a staff member must provide:

Full Name

Department (selected from a dropdown list)

Employee ID

Mobile Number

3. Admin

The admin does not register through the signup page. Instead, the admin credentials are created manually in the database. The admin panel includes:

Dashboard

Loan Requests Management

Tools & Inventory Management

User Accounts Approval

System Settings (Majors, Levels, Departments, Tool Types)

System Features

1. Authentication

Login page for all users (Students, Staff, Admin)

Account creation form with role selection

Admin credentials stored manually (not through signup)

Admin Panel

1. Dashboard

Shows quick statistics:

Total tools

Tools currently borrowed

Pending loan requests

Total users (approved and pending)

Quick table of active loans

2. Tools Management

Admin can:

Add new tools

Edit tool information

Mark a tool as Available / Borrowed

Generate and download a QR code for each tool

Each tool has:

Tool Name

Tool Type (Microphone, Laptop, Speaker, Pointer, Projector, etc.)

Unique Tool Code

Status (Available / Borrowed)

Additional attributes depending on the type

Tool Type Attributes

Microphones

Hall Number (dropdown)

Microphone Type (Handle / Ceiling — checkbox)

Laptops

Laptop Number (unique)

Specifications (optional)

Projectors / Speakers / Pointers

Device Number

Additional notes (optional)

3. Loan Requests

Admin can:

View all submissions

Approve or reject a request

View loan history

Set due dates

Mark tool as returned

4. User Management

Admin handles:

Approving new accounts

Viewing all users

Filtering by role (student / staff)

Editing or disabling accounts

5. System Settings

Admin can manage the following lists:

Majors

Academic Levels

Departments

Tool Types

Hall Numbers

These settings appear as dropdowns in various forms.

Tool Borrowing Methods

There are two methods for submitting a borrowing request.

Method 1: Scanning a Tool QR Code

Each tool has a QR code stored physically on the device. When scanned:

The system opens a page for that specific tool

User submits a borrow request

Admin receives the request for approval

Method 2: Scanning the General System QR Code

A general QR code for the website is provided on posters. When scanned:

User opens the main website

Logs in

Selects a tool from the available list

Submits a borrow request

Borrowing Workflow

User logs in

User scans QR or selects a tool manually

Borrow request is submitted

Admin reviews the request

Admin approves or rejects

Tool status updates automatically

System records the transaction

Admin marks tool as returned

Database Structure (Proposed)

users

id

name

role (student/staff)

academic_number (nullable)

employee_number (nullable)

major_id (nullable)

level_id (nullable)

department_id (nullable)

phone

is_approved

password

tools

id

name

tool_type_id

code

status (available/borrowed)

attributes (JSON — microphone type, hall number, etc.)

tool_types

id

name (microphone, laptop, speaker, projector...)

loan_requests

id

user_id

tool_id

status (pending/approved/rejected/returned)

request_date

approved_date

return_date

settings tables:

majors

levels

departments

halls

QR Code System

Each tool and the main website will have a QR code:

Tool QR → /tool/{id} page

Main QR → homepage /

The QR codes are generated automatically when new tools are added.

Frontend Pages

1. Login Page

Simple email/password login.

2. Signup Page

Includes role selection:

If Student → show student fields

If Staff → show staff fields

3. User Dashboard (Student/Staff)

View available tools

Borrow tools

View borrowing history

4. Admin Dashboard

Contains all admin features mentioned above.

Future Enhancements

Email/SMS notifications

Tool return reminders

Multi-admin support

Role-based permissions

Analytics for tool usage

Summary

This document outlines the full specification for a university educational tool lending system built with Laravel + SQLite. It includes user roles, admin features, QR code workflows, database structure, and borrowing logic.

This file is suitable for import into development tools like Cursor, Antigravity, or Notion.