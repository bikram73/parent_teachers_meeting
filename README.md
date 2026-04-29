# Parent-Teacher Meeting Management System

A comprehensive web-based application designed to streamline communication and meeting scheduling between parents and teachers. This system provides separate dashboards for parents and teachers, enabling efficient management of parent-teacher meetings with features like scheduling, status tracking, and student performance monitoring.

## 🚀 Features

### Core Functionality
- **User Registration & Authentication**: Separate registration for parents and teachers with role-based access
- **Meeting Management**: Schedule, update, delete, and track meeting status
- **Role-Based Dashboards**: Customized interfaces for parents and teachers
- **Meeting Status Tracking**: Track meetings as scheduled, completed, or cancelled
- **Response Management**: Accept/reject meeting requests with reason tracking
- **Student Performance Tracking**: Monitor and record student academic performance
- **Responsive Design**: Bootstrap-powered responsive UI for all devices

### User Roles
- **Parents**: View scheduled meetings, track student performance, manage profile
- **Teachers**: Schedule meetings, manage meeting requests, update student performance, subject management
- **System**: Automated meeting logging and status management

### Advanced Features
- Meeting acceptance/rejection system with reason tracking
- Student performance monitoring with USN-based tracking
- Comprehensive logging system for meeting deletions
- Secure password hashing and session management
- Clean, modern UI with gradient backgrounds and animations

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: PostgreSQL via Supabase
- **Frontend**: HTML5, CSS3, Bootstrap 5.1.3
- **Server**: Apache (XAMPP recommended)
- **Security**: PHP password hashing, prepared statements, session management

## 📋 Requirements

### System Requirements
- PHP 7.4 or higher
- PostgreSQL 14+ via Supabase
- Apache Web Server
- Modern web browser (Chrome, Firefox, Safari, Edge)

### Recommended Setup
- XAMPP 8.0+ (includes PHP, MySQL, Apache)
- 2GB RAM minimum
- 500MB free disk space

## 🔧 Installation

### Step 1: Environment Setup
1. Download and install [XAMPP](https://www.apachefriends.org/download.html)
2. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Project Setup
1. Clone or download this repository
```bash
git clone [repository-url]
```

2. Copy the project folder to your XAMPP htdocs directory:
```
C:\xampp\htdocs\parent-teacher-meeting-management\
```

### Step 3: Database Configuration
1. Create a Supabase project
2. Open the SQL editor in Supabase and run `database.sql`
3. Copy the Supabase database host, port, username, password, and database name from Project Settings > Database
4. Set those values as environment variables in your hosting panel

### Step 4: Configuration
1. Configure the Supabase database connection in your hosting environment:
```php
SUPABASE_DB_HOST=...
SUPABASE_DB_PORT=5432
SUPABASE_DB_NAME=postgres
SUPABASE_DB_USER=...
SUPABASE_DB_PASSWORD=...
SUPABASE_DB_SSLMODE=require
```

2. Ensure proper file permissions for the `logs/` directory
3. Create teacher invite codes in the `teacher_invite_codes` table before allowing teacher registration

### Step 5: Access Application
1. Open your web browser
2. Navigate to: `http://localhost/parent-teacher-meeting-management`
3. Register as a parent or teacher to get started

## 📁 Project Structure

```
parent-teacher-meeting-management/
├── 📄 index.php                    # Main landing page
├── 📄 teacher_meeting_form.php     # Teacher meeting scheduling form
├── 📄 database.sql                 # Database schema and initial data
├── 📄 README.md                    # Project documentation
├── 📄 .htaccess                    # Apache configuration
│
├── 📁 config/
│   └── 📄 db.php                   # Database connection and table creation
│
├── 📁 pages/
│   ├── 📄 dashboard.php            # Main user dashboard
│   ├── 📄 login.php                # User login page
│   ├── 📄 register.php             # User registration page
│   ├── 📄 logout.php               # Logout functionality
│   ├── 📄 meetings.php             # Meeting management page
│   ├── 📄 edit_meeting.php         # Meeting editing interface
│   ├── 📄 teacher_dashboard.php    # Teacher-specific dashboard
│   ├── 📄 parent_dashboard.php     # Parent-specific dashboard
│   └── 📄 student-performance.php  # Student performance tracking
│
├── 📁 actions/
│   ├── 📄 save_meeting.php         # Meeting creation handler
│   ├── 📄 update_meeting.php       # Meeting update handler
│   ├── 📄 delete_meeting.php       # Meeting deletion handler
│   ├── 📄 accept_meeting.php       # Meeting acceptance handler
│   ├── 📄 reject_meeting.php       # Meeting rejection handler
│   └── 📄 update_status.php        # Meeting status update handler
│
├── 📁 assets/
│   ├── 📄 background.jpg           # Background image
│   └── 📁 css/
│       └── 📄 style.css            # Custom styling
│
└── 📁 logs/
    └── 📄 meeting_deletion.log     # Meeting deletion audit log
```

## 🗄️ Database Schema

### Tables Overview

#### `parent_users`
- User information for parents
- Fields: id, name, email, password, phone, created_at

#### `teacher_users`
- User information for teachers
- Fields: id, name, email, password, phone, subject, signup_code, created_at

#### `teacher_invite_codes`
- Teacher registration codes
- Fields: id, code, is_used, teacher_user_id, used_at, created_at

#### `meetings`
- Meeting scheduling and tracking
- Fields: id, parent_name, student_name, subject, teacher_name, meeting_date, meeting_time, status, response_status, rejection_reason, created_at

#### `student_performance`
- Academic performance tracking
- Fields: id, usn, student_name, parent_name, marks, subject, created_at, updated_at

#### `notifications`
- System notifications (future enhancement)
- Fields: id, user_id, message, is_read, created_at

## 🎯 Usage Guide

### For Parents
1. **Registration**: Register with parent role using your email and personal details
2. **Login**: Access your dashboard using registered credentials
3. **View Meetings**: Check scheduled meetings with teachers
4. **Track Performance**: Monitor your child's academic performance
5. **Profile Management**: Update your contact information

### For Teachers
1. **Registration**: Register with teacher role, including subject specialization
2. **Schedule Meetings**: Create new meeting requests for parents
3. **Manage Requests**: Accept or reject meeting requests with reasons
4. **Update Status**: Mark meetings as completed or cancelled
5. **Performance Entry**: Record and update student performance data
6. **Meeting Overview**: View all scheduled meetings in organized dashboard

### Meeting Workflow
1. **Teacher** schedules a meeting with parent details
2. **System** creates meeting record with "pending" status
3. **Parent** can view the meeting in their dashboard
4. **Teacher** can accept/reject and update meeting status
5. **System** logs all meeting activities for audit purposes

## 🔐 Security Features

- **Password Security**: Bcrypt hashing for all user passwords
- **SQL Injection Protection**: Prepared statements for all database queries
- **Session Management**: Secure session handling with role-based access
- **Input Validation**: Server-side validation for all user inputs
- **XSS Protection**: HTML escaping for all output data

## 🚀 Getting Started

### Quick Start for Development
1. Install XAMPP and start Apache + MySQL
2. Clone project to `htdocs/parent-teacher-meeting-management`
3. Access `http://localhost/parent-teacher-meeting-management`
4. Register test accounts (one parent, one teacher)
5. Test meeting scheduling workflow

### Sample Data
The system will auto-create necessary database tables. You can:
1. Register sample users through the web interface
2. Create test meetings to explore functionality
3. Use the teacher dashboard to manage meeting requests

## 🔧 Configuration

### Database Configuration
Set these environment variables in your host or local `.env` file:
```php
SUPABASE_DB_HOST=your-supabase-host
SUPABASE_DB_PORT=5432
SUPABASE_DB_NAME=postgres
SUPABASE_DB_USER=postgres
SUPABASE_DB_PASSWORD=your-password
SUPABASE_DB_SSLMODE=require
```

### Apache Configuration
The `.htaccess` file handles URL rewriting and security headers. Ensure mod_rewrite is enabled in Apache.

## 📝 API Endpoints (Actions)

| Action | File | Purpose |
|--------|------|---------|
| Save Meeting | `actions/save_meeting.php` | Create new meeting |
| Update Meeting | `actions/update_meeting.php` | Modify existing meeting |
| Delete Meeting | `actions/delete_meeting.php` | Remove meeting (with logging) |
| Accept Meeting | `actions/accept_meeting.php` | Accept meeting request |
| Reject Meeting | `actions/reject_meeting.php` | Reject with reason |
| Update Status | `actions/update_status.php` | Change meeting status |

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error**
- Verify MySQL is running in XAMPP
- Check database credentials in `config/db.php`
- Ensure `ptm_system` database exists

**Page Not Found (404)**
- Verify project is in correct htdocs directory
- Check Apache is running
- Ensure proper file permissions

**Login Issues**
- Clear browser cache and cookies
- Verify user exists in correct table (parent_users/teacher_users)
- Check password hashing compatibility

**Meeting Not Saving**
- Check database table structure matches schema
- Verify all required fields are provided
- Check PHP error logs for detailed errors

## 🔮 Future Enhancements

- **Email Notifications**: Automated email alerts for meeting updates
- **Calendar Integration**: Export meetings to Google Calendar/Outlook
- **Mobile App**: Native mobile application for iOS/Android
- **Real-time Chat**: In-app messaging between parents and teachers
- **Document Sharing**: Upload and share student documents
- **Multi-language Support**: Internationalization for different languages
- **Advanced Reporting**: Analytics and reporting dashboard
- **Video Conferencing**: Integrated video meeting capabilities

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 👥 Support

For support and questions:
- Create an issue in the repository
- Check the troubleshooting section above
- Review the code documentation in individual files

## 🏆 Acknowledgments

- Bootstrap team for the responsive framework
- PHP community for excellent documentation
- XAMPP team for the development environment

---

**Version**: 1.0.0  
**Last Updated**: January 2025  
**Compatibility**: PHP 7.4+, MySQL 5.7+, Bootstrap 5.1.3
