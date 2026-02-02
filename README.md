# Student Voice Hub

![Student Voice Hub logo](logo.svg)

> A Laravel-based feedback portal for higher education classes. Students submit quick post-class feedback (ratings, mood checks, and optional comments), while lecturers and admins monitor trends, manage classes, and act on insights.

[![Laravel](https://img.shields.io/badge/Laravel-12-red)](https://laravel.com/)
[![Vite](https://img.shields.io/badge/Vite-5-646CFF)](https://vitejs.dev/)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-38B2AC)](https://tailwindcss.com/)

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

- **Backend**: Laravel 12
- **Frontend**: Blade + Tailwind CSS
- **Build**: Vite
- **Database**: SQLite/MySQL (configurable)
- **Exports**: dompdf (PDF)

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

### 2) Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3) Set up database

Update your `.env` file with the correct database credentials, then run:

```bash
php artisan migrate --seed
```

### 4) Run the app

```bash
php artisan serve
npm run dev
```

Open the app at `http://localhost:8000`.

## Role Setup

1. Log in as an admin (seeders create a default admin user).
2. Create subjects and classes from the admin dashboard.
3. Enroll students and assign lecturers.
4. Students can submit feedback for their enrolled classes.

> Tip: Review the seeders in `database/seeders/` if you want to customize default users or demo data.

## Useful Commands

| Task | Command |
| --- | --- |
| Run the local server | `php artisan serve` |
| Run Vite dev server | `npm run dev` |
| Build front-end assets | `npm run build` |
| Run tests | `php artisan test` |
| Clear caches | `php artisan optimize:clear` |

## Project Structure Highlights

The project follows a standard Laravel layout with a few front-end and tooling additions for Tailwind and Vite.

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

### Where to make changes

#### Back-end (Laravel)
- **Controllers and middleware** live in `app/Http/`.
- **Data models** are defined in `app/Models/`.
- **Route wiring** happens in `routes/` (start with `routes/web.php`).
- **Database changes** are tracked in `database/migrations/`, with seed data in `database/seeders/`.

#### Front-end (Blade + Tailwind)
- **Views** are in `resources/views/`.
- **Styles** start in `resources/css/`.
- **Scripts** and light interactivity are in `resources/js/`.

#### Tooling and configuration
- **Laravel and package configuration** is in `config/`.
- **Vite** and **Tailwind** settings live in `vite.config.js` and `tailwind.config.js`.
- **Environment variables** go in `.env` (based on `.env.example`).

#### Tests
- **Feature tests** are in `tests/Feature/`.
- **Unit tests** are in `tests/Unit/`.

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) for details.
