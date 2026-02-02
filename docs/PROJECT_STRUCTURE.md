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
