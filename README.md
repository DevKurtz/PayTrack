# 💳 Paytrack — Student Tuition Fee Payment System

A web-based tuition fee payment portal for students and parents, built with **Native PHP**, **Bootstrap 5**, and **PHPMailer**. Admins manage student accounts and fees; students view their balance and pay online; email confirmations are sent automatically to students and parents after every transaction.

**Design Theme:** Clean, minimal dashboard UI — light gray background (`#f4f4f5`), white panels, dark sidebar accents, blue active states (`#3b4bff`), and green/red status badges.

---

## 🗂️ Project Structure

```
SystemProposal/                     ← Project root (place inside htdocs)
│
├── assets/                         # Static assets (shared across the app)
│   ├── css/
│   │   ├── main.css                # Global custom styles (Paytrack theme variables)
│   │   ├── auth.css                # Login / change-password page styles
│   │   ├── admin.css               # Admin panel styles
│   │   └── student.css             # Student portal styles
│   ├── js/
│   │   ├── main.js                 # Global JS utilities
│   │   ├── admin.js                # Admin-specific scripts
│   │   └── student.js              # Student-specific scripts
│   └── img/
│       ├── logo.png                # Paytrack logo
│       └── favicon.ico
│
├── config/                         # Application configuration
│   ├── config.php                  # App-wide constants (DB, app name, URLs, mail)
│   ├── database.php                # PDO connection to MariaDB
│   └── mailer.php                  # PHPMailer SMTP setup & send helper
│
├── core/                           # Core application logic (reusable)
│   ├── Auth.php                    # Session management & role-based access control
│   └── helpers.php                 # Global utility functions (sanitize, redirect, OR gen, etc.)
│
├── controllers/                    # Business logic — NO HTML output here
│   ├── AuthController.php          # Login / logout handling
│   ├── AdminController.php         # Admin CRUD actions (students, fees)
│   ├── StudentController.php       # Student dashboard & profile logic
│   └── PaymentController.php       # Payment processing & receipt generation
│
├── models/                         # Data access layer — PDO queries only
│   ├── User.php                    # User login credentials model
│   ├── Student.php                 # Student profile & parent info model
│   ├── TuitionFee.php              # Fee records per student per term
│   └── Payment.php                 # Payment transaction records
│
├── views/                          # HTML templates — NO inline styles, NO business logic
│   ├── layouts/
│   │   ├── base.php                # Master layout (head, CDN links, meta tags)
│   │   ├── admin_layout.php        # Admin shell (sidebar + topbar + content slot)
│   │   └── student_layout.php      # Student shell (navbar + content area)
│   │
│   ├── auth/
│   │   ├── login.php               # Shared login page
│   │   └── change_password.php     # Forced password change on first login
│   │
│   ├── admin/
│   │   ├── dashboard.php           # Admin overview (stats cards, recent activity)
│   │   ├── students/
│   │   │   ├── index.php           # Student list with search & pagination
│   │   │   ├── create.php          # Create new student account form
│   │   │   ├── edit.php            # Edit student profile form
│   │   │   └── view.php            # Student detail + fee history
│   │   ├── fees/
│   │   │   ├── index.php           # All tuition fee records
│   │   │   ├── create.php          # Assign fee to a student
│   │   │   └── edit.php            # Edit fee amount or due date
│   │   └── payments/
│   │       ├── index.php           # All payment transactions (filterable)
│   │       └── view.php            # Single payment detail / receipt
│   │
│   └── student/
│       ├── dashboard.php           # Student home (balance summary cards)
│       ├── tuition.php             # Remaining tuition balance breakdown
│       ├── pay.php                 # Payment form
│       ├── receipt.php             # On-screen receipt after successful payment
│       └── payment_history.php     # Student's full payment transaction list
│
├── mail/                           # Email HTML templates — pure HTML + PHP variables only
│   ├── account_created.php         # Welcome email with login credentials
│   ├── payment_confirmation.php    # Payment receipt email (student + parent)
│   └── payment_reminder.php        # Optional: upcoming due date reminder
│
├── database/
│   ├── schema.sql                  # Full MariaDB schema (CREATE TABLE statements)
│   └── seed.sql                    # Sample data for development/testing
│
├── vendor/                         # Composer packages (auto-generated, do not edit)
│
├── public/                         # Web-accessible entry points
│   ├── index.php                   # Main entry / redirects to login
│   ├── admin/
│   │   └── index.php               # Admin portal entry (role-protected)
│   └── student/
│       └── index.php               # Student portal entry (role-protected)
│
├── .htaccess                       # URL rewriting & directory access security
├── composer.json                   # Composer dependency manifest
└── README.md                       # This file
```

---

## 🚀 Features

### 👤 Admin Panel

| Feature                  | Description                                                                   |
|--------------------------|-------------------------------------------------------------------------------|
| Create Student Account   | Admin creates student; default password = `studentID` + `lastname`            |
| Send Welcome Email       | Credentials auto-emailed to student on account creation via PHPMailer         |
| Manage Tuition Fees      | Assign, edit, and view tuition fee records per student per term               |
| View All Payments        | Browse and filter all payment transactions system-wide                        |
| View Payment Detail      | See full breakdown of any individual payment                                  |
| Student Overview         | View student profile, current balance, and full payment history               |

### 🎓 Student / Parent Portal

| Feature                  | Description                                                                   |
|--------------------------|-------------------------------------------------------------------------------|
| Secure Login             | Login with Student ID and current password                                    |
| Force Password Change    | First-login prompt to replace the default password                            |
| View Tuition Balance     | See total fee, amount paid, and remaining balance                             |
| Pay Tuition              | Submit a payment through the portal                                           |
| On-Screen Receipt        | Instant receipt displayed after payment (with OR number)                      |
| Payment History          | Full list of all personal payment transactions                                |
| Email Notification       | Student **and** parent receive a payment confirmation email after each payment|

---

## 🛠️ Tech Stack

| Layer        | Technology                                        |
|--------------|---------------------------------------------------|
| Backend      | Native PHP 8+ (MVC-inspired architecture)         |
| Frontend     | Bootstrap 5 + Custom CSS (external files only)    |
| Database     | **MariaDB** (XAMPP) via PDO with prepared statements |
| Email        | PHPMailer (SMTP)                                  |
| Alerts / UX  | SweetAlert2                                       |
| Server       | Apache (XAMPP)                                    |
| Icons        | Bootstrap Icons / Custom emoji icons              |

---

## 🎨 Design Theme (Paytrack)

The UI follows the custom Paytrack design language:

| Token                  | Value                          | Usage                          |
|------------------------|--------------------------------|--------------------------------|
| `--bg-app`             | `#f4f4f5`                      | App background                 |
| `--bg-panel`           | `#ffffff`                      | Cards / panels                 |
| `--border`             | `#e6e6e8`                      | All borders                    |
| `--text-main`          | `#18181b`                      | Primary text                   |
| `--text-sub`           | `#9a9aa0`                      | Secondary / muted text         |
| `--sidebar-active-bg`  | `#eef1ff`                      | Active nav item background     |
| `--sidebar-active-text`| `#3b4bff`                      | Active nav item text           |
| `--green-bg/text`      | `#e4f5e9` / `#2f9e5b`         | "Paid" / "Completed" badges    |
| `--red-bg/text`        | `#fbe9e9` / `#d9534f`         | "Overdue" / "Failed" badges    |
| `--btn-dark-bg`        | `#18181b`                      | Primary action buttons         |
| Org icon               | `#0b3d2e` background           | Paytrack brand icon            |
| Font                   | System UI / Segoe UI           | `font-size: 13px` base         |

> All CSS variables are defined in `assets/css/main.css` and reused across all other CSS files.

---

## 🗄️ Database Schema (MariaDB)

Import via **phpMyAdmin** → `paytrack` database → Import → select `database/schema.sql`.

### Tables

| Table          | Description                                              | Key Columns                                   |
|----------------|----------------------------------------------------------|-----------------------------------------------|
| `users`        | Login credentials for admins and students                | `username`, `password_hash`, `role`, `is_first_login` |
| `students`     | Student profile, parent name, parent email               | `student_id`, `user_id`, `email`, `parent_email` |
| `tuition_fees` | Fee records per student per school term                  | `total_amount`, `amount_paid`, `status`, `due_date` |
| `payments`     | Payment transactions (OR number, amount, method)         | `or_number`, `amount`, `payment_method`, `paid_at` |
| `email_logs`   | Audit trail for all sent emails                          | `type`, `status`, `recipient_email`           |

### Fee Status Flow
```
unpaid  →  partial  →  paid
```
- `unpaid`  : amount_paid = 0
- `partial` : 0 < amount_paid < total_amount
- `paid`    : amount_paid >= total_amount

### OR Number Format
Auto-generated in code: `OR-YYYY-XXXXX` (e.g., `OR-2024-00042`)

---

## ⚙️ Setup Instructions

### 1. Place the Project
```
C:\xampp\htdocs\SystemProposal\
```

### 2. Install Dependencies via Composer
```bash
cd C:\xampp\htdocs\SystemProposal
composer install
```

### 3. Create the MariaDB Database
1. Start **XAMPP** → start **Apache** and **MySQL (MariaDB)**
2. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
3. Click **New** → Database name: `paytrack` → Collation: `utf8mb4_unicode_ci` → **Create**
4. Select the `paytrack` database → **Import** tab → choose `database/schema.sql` → **Go**
5. *(Optional)* Import `database/seed.sql` for sample data

### 4. Configure the App
Edit `config/config.php`:
```php
// Database (MariaDB via XAMPP)
define('DB_HOST',   'localhost');
define('DB_NAME',   'paytrack');
define('DB_USER',   'root');
define('DB_PASS',   '');           // default XAMPP MariaDB has no password

// PHPMailer SMTP
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'your_email@gmail.com');
define('MAIL_PASS', 'your_app_password');   // Gmail App Password
define('MAIL_FROM', 'your_email@gmail.com');
define('MAIL_NAME', 'Paytrack');

// App
define('APP_NAME',  'Paytrack');
define('APP_URL',   'http://localhost/SystemProposal');
```

### 5. Access the App

| Role    | URL                                               |
|---------|---------------------------------------------------|
| Admin   | `http://localhost/SystemProposal/public/`         |
| Student | `http://localhost/SystemProposal/public/student/` |

---

## 🔐 Default Admin Credentials

Seeded via `database/seed.sql`:

| Field    | Value      |
|----------|------------|
| Username | `admin`    |
| Password | `admin123` |

> ⚠️ **Change the admin password immediately after first login.**

---

## 📋 System Flow

```
[ADMIN]
  └── Creates student account
       ├── Default password: studentID + lastname  (e.g. "2024001DELACRUZ")
       └── Credentials emailed to student via PHPMailer

[STUDENT]
  └── Logs in → forced to change default password on first login
       └── Views tuition balance (total, paid, remaining)
            └── Submits payment
                 ├── On-screen receipt shown (OR number, amount, date, balance)
                 └── Email confirmation sent to student + parent
```

---

## 📧 Email Notifications

| Trigger         | Recipients         | Content                                              |
|-----------------|--------------------|------------------------------------------------------|
| Account Created | Student            | Username, default password, login URL                |
| Payment Made    | Student + Parent   | OR number, amount paid, date, remaining balance      |

---

## 📁 Coding Standards

- ✅ **No inline styles** in any `.php` view file — all styling via external CSS classes only
- ✅ **No spaghetti code** — Controllers handle logic, Views handle display, Models handle DB
- ✅ **PDO prepared statements** — prevents SQL injection on MariaDB
- ✅ **Session-based role checks** — every protected page validates the session and role
- ✅ **PHPMailer SMTP** — no native `mail()` function used
- ✅ **SweetAlert2** — for elegant confirmation dialogs and alerts

---

## 👥 Roles & Permissions

| Action                    | Admin | Student          |
|---------------------------|-------|------------------|
| Create student accounts   | ✅    | ❌               |
| Assign tuition fees       | ✅    | ❌               |
| View all students         | ✅    | ❌               |
| View all payments         | ✅    | ❌               |
| View own tuition balance  | ❌    | ✅               |
| Pay tuition               | ❌    | ✅               |
| View own payment history  | ❌    | ✅               |
| Receive payment email     | ❌    | ✅ + parent      |

---

## 📦 Dependencies

```json
{
    "require": {
        "php": ">=8.0",
        "phpmailer/phpmailer": "^6.9"
    }
}
```

**CDN (loaded in `views/layouts/base.php`):**
- Bootstrap 5.3
- SweetAlert2
- Bootstrap Icons

---

*Paytrack — Student Tuition Fee Payment Portal | Native PHP · Bootstrap 5 · MariaDB · PHPMailer*
