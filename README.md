# Authentication System

A full-stack authentication system built with Laravel (Backend) and React + TypeScript (Frontend).

## Project Structure

```
auth-system/
├── backend/          # Laravel API Backend
│   ├── app/         # Application logic
│   ├── database/    # Migrations, seeders, factories
│   ├── routes/      # API routes
│   ├── tests/       # Backend tests
│   └── ...
├── frontend/         # React + TypeScript Frontend
│   ├── src/         # Source code
│   ├── public/      # Static assets
│   ├── tests/       # Frontend tests
│   └── ...
└── README.md         # This file
```

## Prerequisites

### Backend Requirements
- **PHP**: ^8.2
- **Composer**: Latest version
- **Node.js**: ^18.0 (for asset compilation)
- **NPM/Yarn**: For frontend dependencies
- **Database**: SQLite (included) or MySQL/PostgreSQL

### Frontend Requirements
- **Node.js**: ^18.0
- **NPM/Yarn**: Package manager

## Quick Start

For a fast setup, run these commands in sequence:

```bash
# 1. Setup Backend
cd backend
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
npm install && npm run build

# 2. Setup Frontend (in a new terminal)
cd ../frontend
npm install

# 3. Start Backend Server
cd ../backend
php artisan serve

# 4. Start Frontend Server (in another terminal)
cd frontend
npm run dev
```

Your application will be available at:
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8000

## Installation & Setup

### Backend Setup (Laravel API)

1. **Navigate to the backend directory:**
   ```bash
   cd backend
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Environment Configuration:**
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   ```

4. **Database Setup:**
   ```bash
   # Create SQLite database (if not exists)
   touch database/database.sqlite
   
   # Run migrations
   php artisan migrate
   ```

5. **Install Node.js dependencies for asset compilation:**
   ```bash
   npm install
   ```

6. **Build frontend assets:**
   ```bash
   npm run build
   ```

### Frontend Setup (React + TypeScript)

1. **Navigate to the frontend directory:**
   ```bash
   cd frontend
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

## Running the Project

### Backend Development Server

**Option 1: Simple Laravel Server**
```bash
cd backend
php artisan serve
```
The API will be available at: `http://localhost:8000`

**Option 2: Full Development Environment (Recommended)**
```bash
cd backend
composer run dev
```
This command runs:
- Laravel server (`http://localhost:8000`)
- Queue worker
- Log monitoring
- Asset compilation with hot reload

### Frontend Development Server

```bash
cd frontend
npm run dev
```
The frontend will be available at: `http://localhost:5173`

### Running Both Together

**Terminal 1 (Backend):**
```bash
cd backend
php artisan serve
```

**Terminal 2 (Frontend):**
```bash
cd frontend
npm run dev
```

## API Endpoints

The backend provides the following authentication endpoints:

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

### Backend Tests
```bash
cd backend

# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Feature  # Feature tests
php artisan test --testsuite=Unit     # Unit tests

# Run with coverage
php artisan test --coverage
```

### Frontend Tests
```bash
cd frontend

# Run tests
npm run test

# Run tests with UI
npm run test:ui

# Run tests with coverage
npm run test:coverage
```

## Environment Configuration

### Backend (.env)
Key environment variables for the backend:
```env
APP_NAME="Authentication System"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

MAIL_MAILER=log
```

### Frontend (Environment Variables)
Create a `.env` file in the frontend directory:
```env
VITE_API_URL=http://localhost:8000
VITE_APP_NAME="Authentication System"
```

## Features

### Authentication System
- ✅ User registration with validation
- ✅ User login with rate limiting
- ✅ User logout and session management
- ✅ Profile updates and management
- ✅ Password changes with security checks
- ✅ Password reset with email notifications
- ✅ Email verification with secure links

### Security Features
- ✅ Rate limiting on all endpoints
- ✅ CSRF protection
- ✅ Password strength validation
- ✅ Email uniqueness validation
- ✅ Session regeneration
- ✅ Token-based authentication

### API Functionality
- ✅ Route accessibility
- ✅ Authentication requirements
- ✅ JSON response format
- ✅ CORS handling
- ✅ Error handling
- ✅ Rate limiting

### Data Validation
- ✅ Required field validation
- ✅ Format validation (email, password)
- ✅ Length validation
- ✅ Uniqueness validation
- ✅ Type validation
- ✅ Special character handling

## Troubleshooting

### Common Issues

1. **Permission Issues (Linux/Mac):**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

2. **Composer Memory Limit:**
   ```bash
   php -d memory_limit=-1 /usr/local/bin/composer install
   ```

3. **Node.js Version Issues:**
   ```bash
   # Use Node Version Manager
   nvm use 18
   ```

4. **Database Connection Issues:**
   - Ensure SQLite file exists: `touch database/database.sqlite`
   - Check file permissions
   - Verify .env configuration

## Production Deployment

### Backend
```bash
cd backend

# Install production dependencies
composer install --optimize-autoloader --no-dev

# Generate optimized autoloader
composer dump-autoload --optimize

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Run migrations
php artisan migrate --force
```

### Frontend
```bash
cd frontend

# Install dependencies
npm ci

# Build for production
npm run build
```

## Technology Stack

### Backend
- **Laravel 12** - PHP Framework
- **Laravel Sanctum** - API Authentication
- **SQLite** - Database (can be changed to MySQL/PostgreSQL)
- **PHPUnit** - Testing Framework

### Frontend
- **React 19** - UI Framework
- **TypeScript** - Type Safety
- **Vite** - Build Tool
- **Bootstrap 5** - CSS Framework
- **React Router** - Client-side Routing
- **Axios** - HTTP Client
- **React Hook Form** - Form Management
- **Zod** - Schema Validation
- **Vitest** - Testing Framework

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
