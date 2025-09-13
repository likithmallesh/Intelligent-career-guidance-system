# Intelligent-career-guidance-system
A smart web-based Career Guidance System built with PHP, MySQL, HTML, CSS, and JavaScript. It provides personalized career recommendations, skill suggestions, success predictions, and resume generation. Includes an admin panel for managing careers, skills, and users.
🎓 Career Guidance System

The Career Guidance System is a smart web application designed to help people discover the best career path for them.
It works like a personal career advisor: learning about your skills, interests, and experiences, then suggesting suitable careers along with skills to learn.
It even includes a built-in resume builder for users.

📌 What This Project Is About

Provides personalized career recommendations based on user profiles, skills, and quiz responses.

Offers admin functionality to manage careers, skills, and user data.

Helps users generate a professional resume instantly.

👥 Two Types of Users

Regular Users – Create profiles, take quizzes, and receive career suggestions.

Administrators (Admins) – Manage the platform by updating careers, skills, and users.

✨ Key Features
🔑 Authentication

Easy sign-up and login for users.

Separate admin login for secure access.

👤 User Profile

Add/Update personal details:

University name & graduation year

Major & GPA

Select from a wide range of skills (Python, Java, Cloud, AI, etc.).

Use skill search for faster selection.

📝 Career Assessment Quiz

Includes questions on:

Work environment preferences

Problem-solving approach

Interests & personality traits

📚 Experience & Projects

Add work experience summaries.

Add descriptions of previous projects.

🎯 Smart Job Suggestions

Top Career Paths with a Compatibility Score.

Recommended skills to learn for improvement.

Success Prediction Percentage using machine learning algorithms.

📄 Resume Builder

Generate a basic, professional PDF resume instantly.

🛠️ Admin Control Panel

Manage Careers (Add/Edit/Delete).

Manage Skills (Add/Edit/Delete).

Manage Users (View/Delete).

🛠️ Built With
🔹 Frontend

HTML – Structure

CSS – Styling

JavaScript – Interactivity

🔹 Backend

PHP – Server-side logic & algorithms

🔹 Database

MySQL – Data storage

📂 Project Structure
CareerGuidanceSystem/
├── admin/                     
│   ├── dashboard.php          # Admin's main page
│   ├── manage_careers.php     # Manage careers
│   ├── manage_skills.php      # Manage skills
│   └── manage_users.php       # Manage user accounts
├── algorithms/                
│   ├── association_rule_mining.php # Suggests new skills
│   ├── career_scoring.php     # Calculates compatibility
│   └── linear_regression.php  # Predicts success %
├── assets/
│   ├── css/style.css          # Styles
│   └── js/script.js           # Frontend logic
├── backend/
│   ├── process_admin_login.php
│   ├── process_login.php
│   └── process_registration.php
├── config/config.php          # Database connection
├── user/
│   ├── dashboard.php
│   ├── generate_resume.php
│   ├── profile.php
│   └── recommendations.php
├── admin_login.php
├── index.php
├── login.php
├── logout.php
├── register.php
├── README.md
└── database_schema.sql

🚀 How to Run

Clone the repository

git clone https://github.com/your-username/CareerGuidanceSystem.git
cd CareerGuidanceSystem


Set up the database

Import database_schema.sql into MySQL.

Update database credentials in config/config.php.

Run the project

Place the project folder inside htdocs (for XAMPP) or your PHP server directory.

Start Apache and MySQL from your server manager.

Open in browser:

http://localhost/CareerGuidanceSystem

✅ Features Summary

🔒 Secure login & registration (User & Admin).

🧠 Career recommendations with smart algorithms.

📊 Compatibility score & success prediction.

📝 Resume builder with instant PDF generation.

🛠️ Admin dashboard to manage careers, skills & users.

👨‍💻 Tech Stack

Frontend: HTML, CSS, JavaScript

Backend: PHP

Database: MySQL

📜 License

This project is licensed under the MIT License – you can freely use and improve it.
