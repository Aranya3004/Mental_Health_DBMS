# 🧠 AI-Powered Mental Healthcare System

A comprehensive web-based mental healthcare management system designed to support mental health awareness, early intervention, and seamless communication between patients, counselors, clinics, and healthcare providers.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)

## 📋 Table of Contents

- [Features](#-features)
- [System Architecture](#-system-architecture)
- [Technologies Used](#-technologies-used)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [Usage](#-usage)
- [User Roles](#-user-roles)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)
- [License](#-license)
- [Disclaimer](#-disclaimer)

## ✨ Features

### Core Functionalities

- **Multi-Role Access System**
  - Patient Portal
  - Counselor Dashboard
  - Clinic Management
  - Admin Panel
  - Insurance Company Portal
  - Emergency SOS Team Interface
  - Data Analyst Dashboard

- **Patient Features**
  - Session booking with counselors
  - Daily mood tracking and journaling
  - Progress monitoring
  - Insurance plan management
  - Emergency SOS alerts
  - Personalized recommendations
  - Feedback system

- **Counselor Features**
  - Session management
  - Patient record access
  - Appointment scheduling
  - Session notes and documentation
  - Accept/decline session requests

- **Admin Features**
  - User management (all roles)
  - Session oversight
  - Crisis alert monitoring
  - Insurance plan management
  - Daily log analytics
  - System-wide reporting

- **AI-Powered Capabilities**
  - Mental health risk assessment
  - Mood pattern analysis
  - Automated recommendations
  - Early intervention alerts
  - Predictive analytics for crisis detection

- **Emergency Response**
  - 24/7 SOS alert system
  - Crisis detection and escalation
  - Real-time team coordination
  - Emergency contact notifications

## 🏗️ System Architecture

The system follows a modular architecture with clear separation of concerns:

```
MentalHealth_DBMS/
├── User Modules (Login/Registration)
├── Role-Based Dashboards
├── Session Management
├── Health Tracking (Daily Logs)
├── Crisis Management (SOS System)
├── Analytics & Reporting
└── Insurance Integration
```

### Database Schema

- **Users & Roles**: admin, users, counsellors, clinic, insurance_company, analyst, emergency_sos_team
- **Core Features**: sessions, daily_logs, recommendations, crisis_alert
- **Business Logic**: insurance_plan

## 🛠️ Technologies Used

| Technology | Purpose |
|------------|---------|
| **PHP** | Backend logic and server-side processing |
| **MySQL** | Database management |
| **HTML5/CSS3** | Frontend structure and styling |
| **JavaScript** | Client-side interactions |
| **AJAX** | Asynchronous data operations |
| **Bootstrap** | Responsive design framework |
| **Chart.js** | Data visualization |

## 📦 Installation

### Prerequisites

- **XAMPP** (or LAMP/WAMP/MAMP)
  - PHP 7.4 or higher
  - MySQL 5.7 or higher
  - Apache Web Server
- **Web Browser** (Chrome, Firefox, Edge, Safari)
- **Git** (for cloning the repository)

### Step 1: Clone the Repository

```bash
git clone https://github.com/YOUR_USERNAME/MentalHealth_DBMS.git
```

### Step 2: Move to XAMPP Directory

**Windows:**
```bash
move MentalHealth_DBMS C:\xampp\htdocs\
```

**Linux/Mac:**
```bash
sudo mv MentalHealth_DBMS /opt/lampp/htdocs/
# or
mv MentalHealth_DBMS /Applications/XAMPP/htdocs/
```

### Step 3: Start XAMPP Services

1. Open XAMPP Control Panel
2. Start **Apache**
3. Start **MySQL**

### Step 4: Create Database

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `mentalhealth`
3. Import the database schema:
   - Click on the `mentalhealth` database
   - Go to **Import** tab
   - Select `database/mentalhealth_database_schema.sql`
   - Click **Go**

### Step 5: Configure Database Connection

The database configuration is already set in `db.php`:

```php
<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mentalhealth';
?>
```

**Note:** Modify these credentials if your XAMPP MySQL settings are different.

### Step 6: Access the Application

Open your browser and navigate to:
```
http://localhost/MentalHealth_DBMS/
```

## 💾 Database Setup

### Option 1: Automated Setup (Recommended)

Import the provided SQL file:
```sql
mysql -u root -p mentalhealth < database/mentalhealth_database_schema.sql
```

### Option 2: Manual Setup

1. Go to `http://localhost/phpmyadmin`
2. Create database: `CREATE DATABASE mentalhealth;`
3. Import the SQL schema file from the `database/` folder

### Sample Credentials

After importing the database, you can login with these sample accounts:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@mentalhealth.com | password123 |
| User/Patient | rahim.khan@email.com | password123 |
| Counsellor | nusrat.jahan@mentalhealth.com | password123 |
| Clinic | dhaka@mindcare.com | password123 |
| Insurance | info@greendelta.com | password123 |
| Analyst | analyst@mentalhealth.com | password123 |
| SOS Team | sos@mentalhealth.com | password123 |

**⚠️ Important:** Change these passwords immediately in production!

## 🚀 Usage

### For Patients

1. **Register/Login**: Create an account or login at `login.php`
2. **Book Sessions**: Schedule appointments with counselors
3. **Daily Logging**: Track your mood, sleep, and activities
4. **View Progress**: Monitor your mental health journey
5. **Emergency SOS**: Quick access to crisis support
6. **Get Recommendations**: Receive personalized wellness suggestions

### For Counselors

1. **Login**: Access counselor dashboard at `counsellor_login.php`
2. **Manage Sessions**: View, accept, or decline session requests
3. **Patient Records**: Access patient history and notes
4. **Session Notes**: Document session outcomes
5. **Schedule Management**: Set availability and appointments

### For Administrators

1. **Login**: Access admin panel at `admin_login.php`
2. **User Management**: Add, edit, or remove users across all roles
3. **Monitor Crisis Alerts**: Track and respond to emergency situations
4. **Analytics**: View system-wide statistics and reports
5. **Insurance Plans**: Manage insurance coverage options

## 👥 User Roles

```
┌─────────────────────────────────────────────────┐
│              Admin (Full Control)               │
├─────────────────────────────────────────────────┤
│  ├─ Patients (Users)                           │
│  ├─ Counsellors (Healthcare Providers)         │
│  ├─ Clinics (Organizations)                    │
│  ├─ Insurance Companies                        │
│  ├─ Analysts (Data & Insights)                 │
│  └─ Emergency SOS Team                          │
└─────────────────────────────────────────────────┘
```

### Role Permissions

| Feature | Patient | Counsellor | Clinic | Admin | Insurance | Analyst | SOS Team |
|---------|---------|------------|--------|-------|-----------|---------|----------|
| Book Sessions | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |
| Manage Sessions | ❌ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Daily Logs | ✅ | ✅ | ❌ | ✅ | ❌ | ✅ | ❌ |
| Crisis Alerts | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ | ✅ |
| User Management | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ |
| Analytics | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ | ❌ |
| Insurance Plans | ✅ | ❌ | ❌ | ✅ | ✅ | ❌ | ❌ |

## 📸 Screenshots

<!-- Add screenshots here -->
```
Coming Soon!
```

## 🤝 Contributing

Contributions are welcome! Here's how you can help:

1. **Fork** the repository
2. **Create** a new branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** your changes (`git commit -m 'Add some AmazingFeature'`)
4. **Push** to the branch (`git push origin feature/AmazingFeature`)
5. **Open** a Pull Request

### Areas for Contribution

- UI/UX improvements
- Additional AI/ML features
- Security enhancements
- Mobile responsiveness
- Test coverage
- Documentation
- Bug fixes

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## ⚠️ Disclaimer

**This application is developed for academic and educational purposes only.**

- This system should **NOT** be used as a substitute for professional medical diagnosis or treatment
- Always consult qualified healthcare providers for medical advice
- In case of emergency, contact local emergency services immediately
- The AI features are for educational demonstration and not clinical use

### Security Notice

This is a **development version** with sample credentials. Before deploying to production:

- [ ] Change all default passwords
- [ ] Implement proper password hashing
- [ ] Add CSRF protection
- [ ] Enable HTTPS/SSL
- [ ] Implement rate limiting
- [ ] Add input validation and sanitization
- [ ] Review and update security policies
- [ ] Conduct security audit

## 📞 Support

For issues, questions, or suggestions:

- **Email**: 2310189@iub.edu.bd
- **Issues**: [GitHub Issues](https://github.com/Aranya3004/Mental_Health_DBMS/issues)
- **Discussions**: [GitHub Discussions](https://github.com/Aranya3004/Mental_Health_DBMS/discussions)

## 🙏 Acknowledgments

- Thanks to all contributors who helped build this project
- Inspired by the need for accessible mental healthcare solutions
- Built with ❤️ for mental health awareness

---

<div align="center">

**⭐ Star this repo if you find it helpful!**

Made with ❤️ for mental health awareness

[Report Bug](https://github.com/Aranya3004/Mental_Health_DBMS/issues) · [Request Feature](https://github.com/Aranya3004/Mental_Health_DBMS/issues)

</div>
