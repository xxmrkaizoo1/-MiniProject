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


