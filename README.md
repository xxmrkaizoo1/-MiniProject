# Student Voice Hub

![Student Voice Hub logo](logo.svg)

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
```
#Project Structure Highlight

The project follows a standard Laravel layout with a few front-end and tooling additions for Tailwind and Vite. A full directory-by-directory breakdown lives in [`docs/PROJECT_STRUCTURE.md`](docs/PROJECT_STRUCTURE.md).

- `app/`: Application source code (controllers, models, services, policies, etc.).
- `routes/`: Web route definitions.
- `resources/`: Blade templates, Tailwind styles, and front-end assets.
- `database/`: Migrations, factories, and seeders.
- `public/`: Public web root and compiled asset entry points.
- `config/`: Configuration files for Laravel and third-party packages.
- `tests/`: Feature and unit tests.
- `storage/`: Logs, cache, and generated files.


# Project Structure

This document describes the folder-level structure of the Student Voice Hub codebase and where to look for common tasks.

```
.
├── app/                     # Core Laravel application code
│   ├── Http/                # Controllers, middleware, form requests
│   ├── Models/              # Eloquent models
│   ├── Providers/           # Service providers
│   └── View/                # View composers and related helpers
├── bootstrap/               # Framework bootstrapping and cache
├── config/                  # Application configuration
├── database/                # Migrations, factories, seeders
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/                  # Web root and static entry points
├── resources/               # UI and front-end source
│   ├── css/                 # Tailwind entry points
│   ├── js/                  # Front-end scripts
│   └── views/               # Blade templates
├── routes/                  # Route definitions
├── storage/                 # Logs, cache, user uploads
├── tests/                   # Feature and unit tests
├── artisan                  # Laravel CLI entry point
├── composer.json            # PHP dependencies
├── package.json             # Node dependencies
└── vite.config.js           # Vite bundler configuration
```

## Where to make changes

### Back-end (Laravel)
- **Controllers and middleware** live in `app/Http/`.
- **Data models** are defined in `app/Models/`.
- **Route wiring** happens in `routes/` (start with `routes/web.php`).
- **Database changes** are tracked in `database/migrations/`, with seed data in `database/seeders/`.

### Front-end (Blade + Tailwind)
- **Views** are in `resources/views/`.
- **Styles** start in `resources/css/`.
- **Scripts** and light interactivity are in `resources/js/`.

### Tooling and configuration
- **Laravel and package configuration** is in `config/`.
- **Vite** and **Tailwind** settings live in `vite.config.js` and `tailwind.config.js`.
- **Environment variables** go in `.env` (based on `.env.example`).

### Tests
- **Feature tests** are in `tests/Feature/`.
- **Unit tests** are in `tests/Unit/`.






