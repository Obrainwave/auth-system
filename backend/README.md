# Backend - Laravel API

This is the backend API for the Authentication System, built with Laravel 12.

> **Note**: For complete project setup and installation instructions, see the main [README.md](../README.md) in the project root.

## Backend Setup

1. **Install PHP dependencies:**
   ```bash
   composer install
   ```

2. **Environment Configuration:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup:**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

4. **Build assets:**
   ```bash
   npm install && npm run build
   ```

## Running the Backend

**Simple Server:**
```bash
php artisan serve
```

**Full Development Environment:**
```bash
composer run dev
```

## API Endpoints

- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get authenticated user
- `PUT /api/profile` - Update user profile
- `POST /api/password/change` - Change password
- `POST /api/password/email` - Request password reset
- `POST /api/password/reset` - Reset password
- `POST /api/email/verify` - Verify email address

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage
```

## Backend Technology Stack

- **Laravel 12** - PHP Framework
- **Laravel Sanctum** - API Authentication
- **SQLite** - Database
- **PHPUnit** - Testing Framework

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
