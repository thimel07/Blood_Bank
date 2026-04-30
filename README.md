# 🩸 Blood Bank Management System

A modern, fully-responsive, animated Blood Bank Management System built with **PHP**, **MySQL**, and **Bootstrap 5**. Perfect for hospitals, clinics, and blood donation centers.

![Status](https://img.shields.io/badge/status-active-success)
![Version](https://img.shields.io/badge/version-1.0.0-blue)
![License](https://img.shields.io/badge/license-MIT-green)

---

## ✨ Key Features

### 🎯 Core Functionality
- ✅ **Donor Management** - Register and manage blood donors
- ✅ **Donation Tracking** - Record and track donations with health screening
- ✅ **Blood Stock Inventory** - Real-time blood unit tracking with color-coded status
- ✅ **Blood Requests** - Hospital blood request management with urgency levels
- ✅ **Patient Database** - Manage patient information and medical records
- ✅ **Receipt Generation** - Invoice and receipt system for transactions
- ✅ **Audit Logging** - Track all system actions for compliance

### 🎨 UI/UX Features
- 📱 **Fully Responsive** - Works seamlessly on mobile, tablet, and desktop
- 🎭 **Modern Design** - Hospital-style clean and professional interface
- ✨ **Smooth Animations** - Scroll animations, hover effects, transitions
- 🌙 **Dark Mode** - Toggle dark mode for better accessibility
- 📊 **Interactive Charts** - Chart.js integration for data visualization
- ⚡ **Fast Performance** - Optimized CSS, JS, and database queries

### 🔐 Security Features
- 🔒 **Session-based Authentication** - Secure login with password hashing
- 🛡️ **Prepared Statements** - Protection against SQL injection
- 📋 **Audit Logs** - Track user actions for compliance
- 🔑 **Role-based Access** - Admin and staff roles
- 🧹 **Input Sanitization** - Prevent XSS attacks

### 📊 Data Analytics
- 📈 **Statistics Dashboard** - Real-time metrics and KPIs
- 📉 **Charts & Reports** - Blood stock distribution, donation trends
- 💾 **Export Functionality** - CSV export for data analysis
- 🖨️ **Print Reports** - Print-friendly layouts

---

## 🛠️ Tech Stack

```
Frontend:
├── HTML5
├── CSS3 (Custom + Bootstrap 5)
├── JavaScript ES6+
├── Chart.js (Data Visualization)
├── GSAP (Animations)
├── AOS (Scroll Animations)
└── SweetAlert2 (Beautiful Alerts)

Backend:
├── PHP 7.4+ (OOP Style)
├── MySQL 5.7+
├── MySQLi (Database Driver)
└── AJAX (Async Operations)

Libraries:
├── Bootstrap 5
├── Font Awesome 6
├── Google Fonts
└── jQuery (Optional)
```

---

## 📦 Installation

### Quick Start (5 minutes)

1. **Extract Project**
   ```bash
   Extract to: C:\xampp\htdocs\blood-bank\
   ```

2. **Import Database**
   ```
   phpMyAdmin → Import → blood_bank.sql
   ```

3. **Configure Database**
   ```
   Edit: includes/db_connect.php
   ```

4. **Access Application**
   ```
   http://localhost/blood-bank/
   ```

**[Detailed Setup Guide →](SETUP.md)**

---

## 🚀 Usage

### For Public Users
```
Landing Page: http://localhost/blood-bank/
├── Search Blood Availability
├── View Statistics
├── Learn About Services
└── FAQs
```

### For Admin Users
```
Login: http://localhost/blood-bank/login.php

Demo Credentials:
├── Username: admin
└── Password: admin123

Dashboard Features:
├── Manage Donors
├── Record Donations
├── Manage Blood Stock
├── Process Requests
├── Manage Patients
└── Generate Receipts
```

---

## 📸 Screenshots

### Landing Page
- Hero section with animated typing effect
- Blood type search bar
- Live statistics counters
- Service cards with hover animations
- FAQ accordion
- Responsive design

### Admin Dashboard
- Real-time statistics cards
- Blood stock distribution chart
- Monthly donation graph
- Recent donations table
- Quick action buttons
- Dark mode support

### Blood Stock Page
- Color-coded blood type cards (Green/Yellow/Red)
- Stock levels display
- Detailed inventory table
- Status indicators

### Donor Management
- Searchable donor table
- Filter by blood type
- View/Edit/Delete functionality
- Donation count tracking
- Last donation date

---

## 🔑 Default Credentials

```
Username: admin
Password: admin123

⚠️ IMPORTANT: Change password after first login!
```

---

## 📁 Project Structure

```
blood-bank/
├── admin/                      # Admin Dashboard
│   ├── dashboard.php          # Main dashboard with stats
│   ├── donors.php             # Donor management
│   ├── donations.php          # Donation records
│   ├── blood_stock.php        # Inventory management
│   ├── requests.php           # Blood request handling
│   ├── patients.php           # Patient management
│   ├── receipts.php           # Payment/Receipt system
│   ├── profile.php            # Admin profile
│   ├── settings.php           # System settings
│   └── logout.php             # Logout handler
│
├── api/                       # API Endpoints
│   └── search_blood.php       # Blood search API
│
├── assets/
│   ├── css/
│   │   └── style.css          # Master stylesheet
│   ├── js/
│   │   └── main.js            # Main JavaScript
│   ├── images/                # Image assets
│   └── libs/                  # External libraries
│
├── database/
│   └── blood_bank.sql         # Complete database schema
│
├── includes/
│   ├── header.php             # HTML header template
│   ├── footer.php             # HTML footer template
│   └── db_connect.php         # Database connection
│
├── index.php                  # Public landing page
├── login.php                  # Admin login page
├── SETUP.md                   # Setup guide
└── README.md                  # This file
```

---

## 🎯 Main Features in Detail

### 1. Donor Management
```
✓ Register new donors
✓ Track donation history
✓ Medical history notes
✓ Status management (active/inactive/suspended)
✓ Search and filter
✓ Donation count tracking
✓ Last donation date
```

### 2. Donation System
```
✓ Record new donations
✓ Health screening form
✓ Hemoglobin level tracking
✓ Blood pressure & temperature
✓ Auto-update blood stock
✓ Expiry date tracking
✓ Status flow: collected → tested → approved
```

### 3. Blood Stock Management
```
✓ Real-time inventory
✓ Color-coded status indicator
✓ Low stock alerts
✓ Stock distribution chart
✓ Conversion: units → milliliters
✓ Multi-blood type support (8 types)
```

### 4. Blood Request Processing
```
✓ Hospital can request blood
✓ Urgency levels: routine/urgent/emergency
✓ Approval workflow
✓ Automatic stock reduction
✓ Request tracking
✓ Fulfillment confirmation
```

### 5. Patient Management
```
✓ Patient registration
✓ Medical condition tracking
✓ Hospital association
✓ Admission date tracking
✓ Status management
✓ Blood type compatibility
```

### 6. Receipt/Invoice System
```
✓ Generate unique receipts
✓ Track transactions
✓ Print receipts
✓ Export to CSV
✓ Receipt history
```

---

## 🎨 Customization

### Change Color Scheme
```css
/* File: assets/css/style.css */
:root {
    --primary-color: #e63946;      /* Red */
    --secondary-color: #ffffff;    /* White */
    --accent-color: #f1f1f1;       /* Light Gray */
    --dark-color: #2d3436;         /* Dark */
    --success-color: #06d6a0;      /* Green */
    --warning-color: #ffd166;      /* Yellow */
    --danger-color: #ef476f;       /* Red */
    --info-color: #118ab2;         /* Blue */
}
```

### Change Hospital Name
```html
<!-- File: index.php -->
<a class="navbar-brand" href="/blood-bank/">
    <i class="fas fa-droplet me-2"></i>
    YOUR_HOSPITAL_NAME
</a>
```

### Modify Database
```sql
-- Add new blood type
INSERT INTO blood_types (blood_type, description) 
VALUES ('NEW_TYPE', 'Description');

-- Add admin user
INSERT INTO admin_users (username, email, password, full_name, role)
VALUES ('user', 'user@hospital.com', 'hashed_password', 'Full Name', 'admin');
```

---

## 🔒 Security Considerations

### Best Practices Implemented
- ✅ Password hashing with bcrypt
- ✅ Prepared statements for all queries
- ✅ Session-based authentication
- ✅ CSRF tokens (can be added)
- ✅ Input validation and sanitization
- ✅ Audit logging for compliance

### Recommendations for Production
1. Enable HTTPS/SSL
2. Change default credentials
3. Use strong database passwords
4. Enable firewall rules
5. Regular database backups
6. Update PHP to latest version
7. Hide error messages from users
8. Implement rate limiting
9. Add 2FA for admin accounts
10. Regular security audits

---

## 📊 Database Schema

Key Tables:
- **admin_users** - Staff/Admin accounts
- **donors** - Donor information
- **donations** - Donation records
- **blood_types** - Blood type definitions
- **blood_stock** - Inventory management
- **patients** - Patient information
- **blood_requests** - Blood request management
- **receipts** - Transaction records
- **audit_logs** - Action tracking

---

## 🐛 Known Limitations & Future Enhancements

### Current Limitations
- Single admin account in demo
- Email notifications not configured
- No PDF export (can be added with dompdf)
- No SMS alerts (can be added with Twilio)

### Planned Features
- [ ] Email notifications
- [ ] SMS alerts
- [ ] Advanced reporting (PDF)
- [ ] Mobile app (React Native)
- [ ] API documentation
- [ ] Multi-branch support
- [ ] Advanced analytics
- [ ] Integration with hospital systems

---

## 🆘 Troubleshooting

### Database Connection Failed
```
Solution:
1. Check MySQL service is running
2. Verify credentials in db_connect.php
3. Ensure database exists
```

### Login Not Working
```
Solution:
1. Clear browser cookies
2. Check admin_users table exists
3. Verify password is correct
```

### Animations Not Working
```
Solution:
1. Check internet (CDN libraries)
2. Clear browser cache
3. Check browser console (F12)
```

### Stock Not Updating
```
Solution:
1. Verify triggers exist in database
2. Check donation status = 'approved'
3. Review MySQL error logs
```

---

## 📚 Documentation

- **[Setup Guide](SETUP.md)** - Detailed installation instructions
- **[API Documentation](API.md)** - REST API endpoints (coming soon)
- **[Admin Guide](ADMIN_GUIDE.md)** - How to use admin panel (coming soon)
- **[Database Schema](DATABASE.md)** - detailed schema info (coming soon)

---

## 💡 Tips & Tricks

### Keyboard Shortcuts
```
Ctrl+K  → Global search (future)
Escape  → Close modals
Enter   → Submit forms
```

### Mobile Access
```
Access from mobile on same network:
http://YOUR_IP:80/blood-bank/
Example: http://192.168.1.5/blood-bank/
```

### Export Data
```
1. Go to any table
2. Click "Export CSV"
3. Open in Excel
4. Generate reports
```

---

## 🤝 Contributing

To contribute improvements:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/AmazingFeature`)
3. **Commit** changes (`git commit -m 'Add AmazingFeature'`)
4. **Push** to branch (`git push origin feature/AmazingFeature`)
5. **Open** a Pull Request

---

## 📄 License

This project is open source and available under the MIT License.

```
MIT License - Feel free to use in commercial and personal projects
```

---

## 👨‍💼 Author

**Blood Bank Management System**
- Created with ❤️ for healthcare
- Built for hospitals and blood donation centers
- Made modern with animations and responsive design

---

## 📞 Support

- **Email:** support@bloodbank.com
- **Issues:** GitHub Issues (if on GitHub)
- **Documentation:** See SETUP.md for detailed guide

---

## 🙏 Acknowledgments

- Bootstrap 5 - UI Framework
- Chart.js - Data visualization
- GSAP - Smooth animations
- SweetAlert2 - Beautiful alerts
- Font Awesome - Icons
- Google Fonts - Typography

---

## 📈 Stats & Metrics

```
✓ 100% Responsive Design
✓ Modern Animations
✓ Professional UI
✓ Secure Backend
✓ Real-time Data
✓ Easy to Deploy
✓ Well Documented
✓ Production Ready
```

---

## 🎓 Learning

This project demonstrates:
- Object-Oriented PHP
- MySQL Database Design
- Bootstrap 5 Development
- AJAX Integration
- Chart.js Usage
- GSAP Animations
- Responsive Web Design
- Security Best Practices
- User Authentication
- Data Management

---

## 🔄 Version History

### v1.0.0 - Current Version
- ✅ Complete core functionality
- ✅ Modern UI with animations
- ✅ Full database schema
- ✅ Admin dashboard
- ✅ Public landing page
- ✅ Comprehensive documentation

---

**Made with ❤️ for healthcare professionals**

*Let's save lives together* 🩸

---

## 🌟 Show Your Support

If you find this project helpful:
- ⭐ Star this repository
- 🔗 Share with others
- 💬 Provide feedback
- 🐛 Report issues

---

Last updated: 2024 | Built for Healthcare Excellence
