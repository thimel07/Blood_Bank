# Blood Bank Management System - Setup Instructions

## 🚀 Quick Start Guide

This is a complete, modern Blood Bank Management System built with PHP, MySQL, and an animated Bootstrap 5 UI.

### Prerequisites
- **PHP 7.4+** (with MySQLi extension)
- **MySQL 5.7+**
- **XAMPP** or any web server (Apache/Nginx)
- **Modern Web Browser**

---

## 📦 Installation Steps

### Step 1: Download & Extract Project
1. Extract the project to your web server directory:
   - **XAMPP**: `C:\xampp\htdocs\blood-bank\`
   - **WAMP**: `C:\wamp\www\blood-bank\`
   - **Linux**: `/var/www/html/blood-bank/`

### Step 2: Start XAMPP Services
```bash
# On Windows
1. Open XAMPP Control Panel
2. Click "Start" for Apache
3. Click "Start" for MySQL
```

### Step 3: Create Database

#### Method 1: Using phpMyAdmin (Easy)
```
1. Open browser → http://localhost/phpmyadmin
2. Click "New" on the left sidebar
3. Database name: blood_bank_system
4. Click "Create"
5. Click on the new database → "Import" tab
6. Select /blood-bank/database/blood_bank.sql
7. Click "Go" or "Import"
```

#### Method 2: Using MySQL Command Line
```bash
# Open Command Prompt/Terminal
mysql -u root -p
# Press Enter (no password by default)

# Then run these commands:
CREATE DATABASE blood_bank_system;
USE blood_bank_system;
SOURCE C:/xampp/htdocs/blood-bank/database/blood_bank.sql;
```

### Step 4: Configure Database Connection
Edit `/blood-bank/includes/db_connect.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');           // Add your password if you have one
define('DB_NAME', 'blood_bank_system');
```

### Step 5: Access the Application

**Public Landing Page:**
```
http://localhost/blood-bank/
```

**Admin Login:**
```
http://localhost/blood-bank/login.php

Demo Credentials:
Username: admin
Password: admin123
```

---

## 📁 Project Structure

```
blood-bank/
├── admin/                          # Admin Dashboard & Modules
│   ├── dashboard.php              # Main dashboard
│   ├── donors.php                 # Donor management
│   ├── donations.php              # Donation records
│   ├── blood_stock.php            # Blood inventory
│   ├── requests.php               # Blood requests
│   ├── patients.php               # Patient management
│   ├── receipts.php               # Receipt/Invoice
│   ├── profile.php                # Admin profile
│   ├── settings.php               # System settings
│   └── logout.php                 # Logout
│
├── api/                           # API Endpoints
│   └── search_blood.php           # Blood search API
│
├── assets/
│   ├── css/
│   │   └── style.css              # Main stylesheet
│   ├── js/
│   │   └── main.js                # Main JavaScript
│   ├── images/                    # Image assets
│   └── libs/                      # External libraries
│
├── database/
│   └── blood_bank.sql             # Database schema
│
├── includes/
│   ├── header.php                 # Page header
│   ├── footer.php                 # Page footer
│   └── db_connect.php             # Database connection
│
├── pages/                         # Future feature pages
│
├── index.php                      # Landing page
├── login.php                      # Admin login
└── README.md                      # This file
```

---

## 🔐 Default Admin Account

```
Username: admin
Password: admin123
```

**IMPORTANT:** Change this password immediately after first login!

---

## 🎨 Features Overview

### Landing Page (Public)
- **Hero Section** with animated typing effect
- **Blood Search** functionality
- **Live Statistics** (counters)
- **Service Cards** with hover animations
- **FAQ Section** with accordion
- **Responsive Design** (mobile-friendly)

### Admin Dashboard
- **Statistics Dashboard** with real-time data
- **Charts & Graphs** (Blood stock, Monthly donations)
- **Sidebar Navigation** (collapsible, animated)
- **Dark Mode** toggle

### Modules

#### 1. **Donor Management**
- Add/Edit/Delete donors
- Search & filter by blood type
- View donation history
- Status management

#### 2. **Donation System**
- Record new donations
- Auto-update blood stock
- Health screening form
- Expiry tracking

#### 3. **Blood Stock**
- Real-time inventory
- Color-coded status (Green=Full, Yellow=Low, Red=Critical)
- Stock distribution chart
- Expiry alerts

#### 4. **Blood Requests**
- Create/approve requests
- Urgency levels (Routine, Urgent, Emergency)
- Track fulfillment
- Automatic stock reduction

#### 5. **Patient Management**
- Manage patient records
- Hospital association
- Medical conditions tracking
- Status monitoring

#### 6. **Receipt System**
- Generate invoices
- Print receipts
- Export to CSV/PDF
- Transaction history

---

## 🎯 Admin Panel Guide

### Dashboard
- View key statistics
- See recent donations
- Quick action buttons
- Charts for reports

### How to Add a New Donor
1. Go to **Donors** → Click **Add Donor**
2. Fill in form:
   - Full Name
   - Contact (Phone/Email)
   - Blood Type
   - Date of Birth
   - Medical History (optional)
3. Click **Save**

### How to Record a Donation
1. Go to **Donations** → Click **Record Donation**
2. Select donor
3. Enter donation details:
   - Blood Type
   - Quantity (default: 450ml)
   - Health status
   - Blood pressure & temperature
4. Click **Save**
5. Stock automatically updates ✓

### How to Process a Blood Request
1. Go to **Requests**
2. Find pending request
3. Click **Approve**
4. System checks stock availability
5. Stock automatically reduced ✓

### How to Export Data
- Click **Export CSV** button on any table
- Data downloads as Excel-compatible file

---

## 🎨 Customization

### Change Colors
Edit `/assets/css/style.css`:
```css
:root {
    --primary-color: #e63946;    /* Red - Change here */
    --secondary-color: #ffffff;
    --accent-color: #f1f1f1;
    --dark-color: #2d3436;
    /* ... etc */
}
```

### Change Hospital Name
Edit `/includes/header.php` and `/index.php`:
```html
<i class="fas fa-droplet me-2"></i>
YOUR_HOSPITAL_NAME
```

### Modify Logo
Replace images in `/assets/images/` folder

---

## 🔧 Advanced Configuration

### Email Notifications
To enable email alerts for blood requests, edit the API files and configure SMTP:
```php
// Example: api/send_notification.php
$mail = new PHPMailer();
$mail->Host = 'smtp.gmail.com';
$mail->Username = 'your-email@gmail.com';
$mail->Password = 'app-password';
```

### Database Backups
Use phpMyAdmin:
1. Select database → Export
2. Choose Format: SQL
3. Click Download

---

## 🐛 Troubleshooting

### Issue: "Database Connection Failed"
**Solution:**
- Check MySQL is running
- Verify credentials in `db_connect.php`
- Make sure database exists
```bash
mysql -u root -p blood_bank_system
```

### Issue: "Login page shows but login fails"
**Solution:**
- Check username/password are correct
- Verify database has admin_users table
- Check browser console for errors (F12)

### Issue: "Animations not working"
**Solution:**
- Check internet connection (CDN libraries)
- Clear browser cache (Ctrl+Shift+Delete)
- Check browser supports ES6 JavaScript

### Issue: "Blood stock not updating"
**Solution:**
- Check database triggers are created
- Verify donation status is set to "approved"
- Check trigger logs in MySQL

---

## 🔒 Security Best Practices

1. **Change Default Password**
   - Login → Admin → Change password immediately

2. **Use Strong Passwords**
   - Min 8 characters: uppercase, lowercase, numbers, symbols

3. **Restrict File Access**
   - Don't expose `/database/` folder in production
   - Use `.htaccess` to block:
   ```htaccess
   <FilesMatch "db_connect.php">
       Deny from all
   </FilesMatch>
   ```

4. **Enable HTTPS**
   - Use SSL certificate in production

5. **Regular Backups**
   - Export database weekly
   - Store in secure location

6. **Update PHP**
   - Keep PHP version current
   - Enable all security patches

---

## 📱 Mobile Responsiveness

The system is fully responsive:
- ✓ Mobile (320px - 480px)
- ✓ Tablet (481px - 768px)
- ✓ Desktop (769px+)
- ✓ Large Desktop (1200px+)

Test on mobile:
```
http://localhost/blood-bank/ 
(Access from mobile on same network)
```

---

## 🚀 Deployment to Live Server

### 1. Get Web Hosting
- Recommended: Bluehost, SiteGround, HostGator
- Requirements: PHP 7.4+, MySQL 5.7+

### 2. Upload Files
Using FTP (FileZilla):
```
Host: your-domain.com
User: your-ftp-username
Pass: your-ftp-password
Port: 21

Upload all files to: public_html/blood-bank/
```

### 3. Create Database on Server
```bash
# Via Hosting cPanel:
1. Go to MySQL Databases
2. Create new database
3. Import blood_bank.sql
```

### 4. Update Configuration
Edit `db_connect.php` with server details:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'your_db_name');
```

### 5. Set File Permissions
```bash
chmod 755 /blood-bank/
chmod 644 /blood-bank/admin/
```

### 6. Access Live Site
```
https://your-domain.com/blood-bank/
```

---

## 📊 Key Technologies Used

| Component | Technology |
|-----------|-----------|
| Backend | Core PHP (OOP style) |
| Database | MySQL 5.7+ |
| Frontend | HTML5, CSS3, Bootstrap 5 |
| Animation | GSAP, AOS |
| Charts | Chart.js |
| Alerts | SweetAlert2 |
| Icons | Font Awesome 6 |
| Fonts | Google Fonts (Poppins) |

---

## 📞 Support & Contact

For issues or questions:
- Check troubleshooting section above
- Review browser console (F12 → Console tab)
- Check database logs in MySQL
- Contact: support@bloodbank.com

---

## 📄 License

This project is provided as-is for educational and commercial use.

---

## ✅ Checklist for Deployment

- [ ] Database created and imported
- [ ] PHP version 7.4+ installed
- [ ] MySQL running and accessible
- [ ] Admin credentials changed
- [ ] Email notifications configured (optional)
- [ ] HTTPS enabled (production)
- [ ] Backups scheduled
- [ ] All modules tested
- [ ] Charts loading correctly
- [ ] Animations working smoothly

---

## 🎓 Learning Resources

- PHP OOP: https://www.php.net/manual/en/language.oop5.php
- MySQL: https://dev.mysql.com/doc/
- Bootstrap 5: https://getbootstrap.com/docs/5.0/
- Chart.js: https://www.chartjs.org/docs/latest/
- GSAP: https://greensock.com/gsap/

---

**Congratulations! Your Blood Bank System is ready! 🎉**

For detailed feature documentation, see individual module comments in PHP files.
