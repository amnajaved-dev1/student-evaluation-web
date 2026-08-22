# 🎓 Student Evaluation System — Web Portal

A full-stack, role-based **University Evaluation & Analytics Portal** built with PHP, MySQL, and Bootstrap. The system digitizes student attendance, grading, fee status, and course evaluation feedback, and turns raw academic data into live dashboards and charts for Admins, Teachers, and Students.

> 📱 A native mobile client for this same system (React Native + Expo) is available here: **[Student Evaluation System — Mobile App](#)** *(add your mobile repo link once created)*

---

## 📖 Overview

Traditional academic record-keeping is scattered across registers and spreadsheets, making it hard to spot trends in attendance, grades, or teaching feedback. This project centralizes that data in a MySQL database and visualizes it through role-based dashboards — so admins, teachers, and students can each see exactly what matters to them, instantly.

Built as a Web Engineering project at **Fatima Jinnah Women University**.

---

## ✨ Features

### 🛡️ Admin
- Full system control from a single dashboard
- Manage users (students & teachers), courses, and enrollments
- Manage & edit attendance records
- Manage fee status
- Analytics dashboard — attendance % by course, evaluation ratings, grade distribution

### 👩‍🏫 Teacher
- Mark and edit student attendance by course
- Enter and update grades/marks
- View evaluation results submitted by students
- Personal analytics dashboard for their own courses

### 👩‍🎓 Student
- View personal attendance (chart + table)
- View grades across all completed semesters
- View fee status
- Submit course evaluation / teaching-quality feedback
- Live-updating charts as more evaluation data is submitted

### 🔐 Core System
- Secure, role-based login and session handling
- Role-based access control (Admin / Teacher / Student redirects)
- Dynamic chart generation (JavaScript) driven directly from MySQL data
- Fully responsive UI (Bootstrap) — usable on desktop, tablet, and mobile browsers

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, JavaScript, Bootstrap |
| Backend | PHP |
| Database | MySQL |
| Server | Apache (via XAMPP) |
| Charts | JavaScript charting (dynamic, DB-driven) |
| Architecture | Client–Server, role-based MVC-style structure |

---

## 📂 Project Structure

```
university_portal/
├── admin/
│   ├── dashboard.php
│   ├── admin_analytics.php
│   ├── manage_users.php
│   ├── manage_courses.php
│   ├── manage_enrollments.php
│   ├── manage_fees.php
│   ├── manage_attendance.php
│   └── edit_attendance.php
├── teacher/
│   ├── dashboard.php
│   ├── attendance.php
│   ├── view_attendance.php
│   ├── enter_marks.php
│   ├── save_marks.php
│   ├── evaluation_results.php
│   └── analytics.php
├── student/
│   ├── dashboard.php
│   ├── attendance.php
│   ├── view_marks.php
│   └── evaluate.php
├── includes/
│   ├── db.php          # DB connection
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/
│   └── images/
├── index.php
├── login.php
└── logout.php
```

---

## ⚙️ Getting Started (Local Setup)

1. **Install XAMPP** (or any Apache + MySQL + PHP stack).
2. Clone this repo into your `htdocs` folder:
   ```bash
   git clone https://github.com/<your-username>/university-portal.git
   ```
3. Start **Apache** and **MySQL** from the XAMPP control panel.
4. Create a database (e.g. `university_portal`) in **phpMyAdmin** and import the SQL schema (see `/database` if included, or set up `users`, `students`, `teachers`, `attendance`, `evaluations`, `grades`, and `courses` tables).
5. Update the DB credentials in `includes/db.php`.
6. Visit `http://localhost/university_portal/` in your browser.

---

## 🖼️ Screenshots

*(Add screenshots here — Login page, Admin Analytics, Student Dashboard, Teacher Attendance, etc. Drag them into a `/screenshots` folder and reference them like below:)*

```md
![Login](screenshots/login.png)
![Admin Analytics](screenshots/admin-analytics.png)
![Student Dashboard](screenshots/student-dashboard.png)
```

---

## 🚧 Limitations & Future Enhancements

- Currently runs on a local server environment (XAMPP); not yet deployed online
- Basic authentication — no advanced security hardening (e.g. hashed passwords, CSRF protection) yet
- **Planned:** cloud deployment, password hashing & stronger security, advanced analytics, REST API layer shared with the mobile app

---

## 👥 Team

Built as a Web Engineering course project at Fatima Jinnah Women University.

| Name | Roll No. |
|---|---|
| Ibtisam | BCS-042 |
| Amna Javed | BCS-009 |
| Ayesha | BCS-022 |

**Course:** Web Engineering · **Submitted to:** Ma'am Qurat

---

## 📄 License

This project was built for academic purposes. Feel free to fork and adapt for learning — attribution appreciated.
