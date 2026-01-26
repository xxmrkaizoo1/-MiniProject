# Student Voice Hub

Student Voice Hub is a Laravel-based feedback portal for higher education classes. Students submit quick post-class feedback (ratings, mood checks, and optional comments), while lecturers and admins monitor trends, manage classes, and act on insights.

## Table of Contents

- [Features](#features)
- [User Roles](#user-roles)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Getting Started](#getting-started)
- [Role Setup](#role-setup)
- [Useful Commands](#useful-commands)
- [Project Structure Highlights](#project-structure-highlights)
- [License](#license)

## Features

- **Student feedback flow**: subject-specific ratings, mood ratings, optional comments, and an anonymous toggle.
- **Lecturer dashboard**: class overview, feedback totals, average rating, and negative sentiment indicators.
- **Admin tools**: manage subjects, create classes, enroll students, and review feedback lists.
- **Action prompts**: a lightweight lecturer chatbot prompt helper to guide next-lesson actions.

## User Roles

- **Student**: submit feedback for enrolled subjects.
- **Lecturer**: view class metrics and chatbot guidance on the dashboard.
- **Admin**: manage subjects/classes, enroll students, and review all feedback.

## Tech Stack

- Laravel 12
- Blade + Tailwind CSS
- Vite
- SQLite/MySQL (configurable)
- dompdf (PDF exports)

## Requirements

- PHP 8.2+
- Composer
- Node.js & npm
- A supported database (SQLite or MySQL)

## Getting Started

### 1) Install dependencies

```bash
composer install
npm install
