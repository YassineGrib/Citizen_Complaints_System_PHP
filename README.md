# Citizen Complaints System

![PHP](https://img.shields.io/badge/PHP-%3E%3D7.4-blue)
![MySQL](https://img.shields.io/badge/MySQL-%3E%3D5.7-orange)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple)
![Languages](https://img.shields.io/badge/Languages-3-success)
![License](https://img.shields.io/badge/license-MIT-green)
![PHPMailer](https://img.shields.io/badge/PHPMailer-v6.8%2B-yellow)

A comprehensive web application for managing and processing citizen complaints. Built using PHP and Bootstrap with multilingual support (English, French, Arabic with RTL), dark mode, and email notifications.

## Overview

The Citizen Complaints System is an electronic platform that allows citizens to submit and track complaints and suggestions in an easy and effective manner. The system provides a user-friendly interface for citizens to submit their complaints, and a control panel for administrators to manage and process these complaints.

## Key Features

- **Complaint Submission**: User-friendly interface for submitting complaints with file attachment capability
- **Complaint Classification**: Support for different types of complaints (roads, public lighting, public parks, sports facilities, etc.)
- **Admin Dashboard**: Administrative interface for officials to review and process complaints
- **Email Notifications**: Automatic notifications sent to citizens when their complaint status is updated
- **Status Tracking**: Updating complaint status (new, in progress, resolved, rejected)
- **Search and Filtering**: Ability to search and filter complaints according to various criteria
- **Dark Mode**: Toggle between light and dark themes with persistent preference
- **Multilingual Support**: Available in English, French, and Arabic with RTL support
- **Responsive Design**: Works on all devices (desktop, tablet, mobile)
- **Automatic Setup**: Automatic database and admin user creation on first run
- **Password Management**: Admin password change functionality for enhanced security

## Technical Requirements

- PHP 7.4 or newer
- Web server (Apache/Nginx)
- MySQL 5.7 or newer
- Enable `fileinfo` extension in PHP
- Enable `mysqli` extension in PHP
- PHPMailer 6.8 or newer (optional for email notifications)
- XAMPP/WAMP/MAMP (for local development)

## Installation

### Automatic Installation (Recommended)

1. Clone or download this repository to your web server directory
2. Access the system through your web browser (e.g., http://localhost/Citizen_Complaints_System/)
3. The system will automatically:
   - Check if the database exists
   - Create the database and tables if they don't exist
   - Create a default admin user if one doesn't exist

### Manual Installation

1. Clone or download this repository to your web server directory
2. Run the database setup script to create the database and tables:

```
php setup_database.php
```

3. Create an admin user using the provided script:

```
php add_admin.php
```

4. Alternatively, you can manually:
   - Create a new MySQL database named `complaints_db`
   - Import the `database_setup.sql` file to create the required tables
   - Update the database connection settings in `config/database.php`
   - Update the email settings in `config/mail.php` (optional)

5. Make sure the `uploads` directory exists and is writable by the web server:

```
mkdir uploads
chmod 777 uploads  # For Linux/Unix systems
```

## Default Admin Account

After installation, you can log in to the admin dashboard with the following credentials:

- **Email**: admin@admin.com
- **Password**: admin

**Important**: Change the default admin password after your first login for security reasons. You can do this by clicking on the "Forgot or change password?" link on the admin login page.

## Usage

### For Citizens

1. Visit the system's homepage
2. Click on "Submit a Complaint" button
3. Fill out the form with your details and complaint information
4. Attach supporting documents if needed (optional)
5. Click "Submit Complaint" to send your complaint
6. You will receive a tracking ID that you can use to check the status of your complaint

### For Administrators

1. Visit the admin login page (`/admin/login.php`)
2. Enter your email and password
3. Use the dashboard to manage submitted complaints:
   - View all complaints in the dashboard
   - Filter complaints by status (All, New, In Progress, Resolved, Rejected)
   - Search for specific complaints by name or subject
   - Click on "View" to see complaint details
4. From the complaint details page:
   - View citizen information and complaint details
   - Update complaint status using the dropdown
   - Add responses to the complaint
   - View any attached files

## Multilingual Support

The system supports three languages out of the box:

- **English**: Default language
- **French**: Full translation
- **Arabic**: Full translation with RTL (Right-to-Left) support

Language selection is persistent across sessions and is stored in both cookies and session variables.

## Dark Mode

The dark mode feature provides:

- Reduced eye strain in low-light environments
- Persistent preference across sessions
- Automatic detection of system-level dark mode preference
- Smooth transitions between light and dark themes

## Security Features

- Password hashing for admin authentication
- Password change functionality for admin users
- Input validation and sanitization to prevent SQL injection and XSS attacks
- File uploads validation for type and size

## License

This project is licensed under the MIT License - see the LICENSE file for details.
