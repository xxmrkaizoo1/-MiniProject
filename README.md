# Student Voice Hub

![Student Voice Hub logo](logo.svg)

> **Student Voice Hub** is a Laravel-based feedback system designed for higher education institutions.  
> Students can submit quick feedback after each class, while lecturers and administrators monitor feedback trends and improve teaching quality using data insights and optional AI assistance.

[![Laravel](https://img.shields.io/badge/Laravel-12-red)](https://laravel.com/)
[![Vite](https://img.shields.io/badge/Vite-5-646CFF)](https://vitejs.dev/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-38B2AC)](https://tailwindcss.com/)

---

# Table of Contents

- [Features](#features)
- [User Roles](#user-roles)
- [Tech Stack](#tech-stack)
- [System Overview](#system-overview)
- [Requirements](#requirements)
- [Getting Started](#getting-started)
- [Role Setup](#role-setup)
- [Useful Commands](#useful-commands)
- [AI Integration (Ollama)](#ai-integration-ollama)
- [Project Structure](#project-structure)
- [Development Guide](#development-guide)
- [Future Improvements](#future-improvements)
- [License](#license)

---

# Features

Student Voice Hub focuses on **fast and structured classroom feedback**.

### Student Feedback Flow

Students can easily submit feedback after a class session.

Features include:

- Subject-specific ratings
- Mood selection (positive / neutral / negative)
- Optional comment submission
- Anonymous feedback option

---

### Lecturer Dashboard

Lecturers can monitor feedback results through a dashboard.

Dashboard features:

- Class overview and statistics
- Total feedback submissions
- Average class rating
- Detection of negative feedback trends
- AI suggestions for improving the next lesson

---

### Admin Management Tools

Administrators can manage the academic system.

Admin tools include:

- Subject management
- Class creation and management
- Student enrollment
- Lecturer assignment
- Full feedback review panel

---

### AI Assistance

The system can optionally use **local AI models via Ollama** to generate teaching improvement suggestions based on student feedback.

This helps lecturers:

- identify learning issues
- understand student sentiment
- improve teaching strategies

---

# User Roles

The system supports **three primary user roles**.

### Student

Students can:

- submit feedback for enrolled subjects
- rate lectures
- share optional comments
- choose anonymous feedback

---

### Lecturer

Lecturers can:

- monitor feedback trends
- view class analytics
- read student comments
- receive AI suggestions

---

### Admin

Administrators can:

- manage subjects and classes
- enroll students
- assign lecturers
- review feedback data

---

# Tech Stack

Student Voice Hub is built using a **modern Laravel stack**.

### Backend
- Laravel 12
- PHP 8.2+

### Frontend
- Blade Templates
- Tailwind CSS
- Vite

### Database
- SQLite
- MySQL (optional)

### Additional Tools
- dompdf (PDF export)
- Ollama (local AI models)

---

# System Overview

The system follows a **standard MVC architecture**.

Student
↓
Submit Feedback
↓
Laravel Controller
↓
Database Storage
↓
Lecturer Dashboard
↓
Analytics + AI Suggestions



This architecture keeps the system **organized and easy to maintain**.

---

# Requirements

Before installing the project, make sure your system has the following installed:

- PHP 8.2 or newer
- Composer
- Node.js
- npm
- SQLite or MySQL database

---

# Getting Started

### 1 Install Dependencies

```bash
composer install
npm install


2 Configure Environment

cp .env.example .env


Generate the application key:

php artisan key:generate

3 Setup Database

Update the .env file with your database configuration.

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=student_voice_hub
DB_USERNAME=root
DB_PASSWORD=

Run migrations and seed the database:

php artisan migrate --seed

4 Run the Application

Start Laravel server:

php artisan serve

Run Vite:

npm run dev

Open the application : 

http://localhost:8000

Role Setup

After installation:

Log in as an admin user created by the seeders.

Create subjects.

Create classes.

Assign lecturers to classes.

Enroll students.

Students can now begin submitting feedback.


Useful Commands


Task	Command
Run Laravel server	php artisan serve
Run Vite dev server	npm run dev
Build frontend assets	npm run build
Run tests	php artisan test
Clear Laravel caches	php artisan optimize:clear


AI Integration (Ollama)

Student Voice Hub supports local AI integration using Ollama.

Install Ollama

Download from:

https://ollama.com


Run a Model

Example:

ollama run llama3

Configure Environment

Add this to your .env file:

OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_MODEL=llama3


Project Structure

.
├── app/
│   ├── Http/
│   ├── Models/
│   ├── Providers/
│   └── View/
├── bootstrap/
├── config/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
├── storage/
├── tests/
├── artisan
├── composer.json
├── package.json
└── vite.config.js


Development Guide


Backen :

app/Http/Controllers
app/Models
routes/web.php
database/migrations

Frontend : 

resources/views
resources/css
resources/js



Future Improvements

Possible improvements:

sentiment analysis for comments

advanced feedback analytics

email alerts for negative feedback

REST API support

mobile UI optimization

License

This project is licensed under the MIT License.

---

✅ Now your README will:

- render **correctly on GitHub**
- have **clean sections**
- look **professional for portfolio or internship**

---

If you want, I can also help you add **3 things that make your README look like a senior developer project**:

- screenshots section 📸  
- system architecture diagram 🧠  
- database ERD diagram 🗄️  

These make your project look **10× more impressive on GitHub.**



