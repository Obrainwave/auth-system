# Backend Test Suite

This comprehensive test suite covers all aspects of the Laravel authentication system backend.

## Test Structure

### Feature Tests
- **Authentication Tests**
  - `RegistrationTest.php` - User registration functionality
  - `LoginTest.php` - User login functionality  
  - `LogoutTest.php` - User logout functionality
  - `ProfileTest.php` - User profile management
  - `PasswordChangeTest.php` - Password change functionality
  - `PasswordResetTest.php` - Password reset functionality
  - `EmailVerificationTest.php` - Email verification functionality

- **API Tests**
  - `ApiRoutesTest.php` - API route accessibility and behavior

- **Validation Tests**
  - `FormValidationTest.php` - Form validation rules and error handling

### Unit Tests
- **UserTest.php** - User model functionality and relationships

## Test Coverage

### ✅ Authentication Flow
- User registration with validation
- User login with rate limiting
- User logout and session management
- Profile updates and management
- Password changes with security checks
- Password reset with email notifications
- Email verification with secure links

### ✅ Security Features
- Rate limiting on all endpoints
- CSRF protection
- Password strength validation
- Email uniqueness validation
- Session regeneration
- Token-based authentication

### ✅ API Functionality
- Route accessibility
- Authentication requirements
- JSON response format
- CORS handling
- Error handling
- Rate limiting

### ✅ Data Validation
- Required field validation
- Format validation (email, password)
- Length validation
- Uniqueness validation
- Type validation
- Special character handling

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suites
```bash
# Run only feature tests
php artisan test --testsuite=Feature

# Run only unit tests
php artisan test --testsuite=Unit

# Run specific test file
php artisan test tests/Feature/Auth/RegistrationTest.php

# Run specific test method
php artisan test --filter test_user_can_register_with_valid_data
```

### Run Tests with Coverage
```bash
# Install coverage package first
composer require --dev phpunit/php-code-coverage

# Run with coverage
php artisan test --coverage
```

### Run Tests in Parallel
```bash
php artisan test --parallel
```

## Test Configuration

The test suite is configured in `phpunit.xml` with:
- SQLite in-memory database for fast tests
- Array cache and session drivers
- Mail array driver for testing emails
- Disabled external services (Pulse, Telescope, Nightwatch)

## Test Data

### Helper Methods
- `createUser($attributes = [])` - Creates a test user
- `createAuthenticatedUser($attributes = [])` - Creates and authenticates a user
- `getValidRegistrationData()` - Returns valid registration data
- `getValidLoginData()` - Returns valid login data

### Test Database
- Uses `RefreshDatabase` trait for clean state
- In-memory SQLite for speed
- Automatic migration on test setup

## Test Categories

### Authentication Tests (7 files)
- Registration: 9 tests
- Login: 10 tests  
- Logout: 4 tests
- Profile: 8 tests
- Password Change: 8 tests
- Password Reset: 12 tests
- Email Verification: 8 tests

### API Tests (1 file)
- API Routes: 12 tests

### Validation Tests (1 file)
- Form Validation: 15 tests

### Unit Tests (1 file)
- User Model: 15 tests

**Total: ~93 tests covering all backend functionality**

## Test Results

All tests should pass with:
- ✅ Authentication flows
- ✅ Security measures
- ✅ API endpoints
- ✅ Data validation
- ✅ Error handling
- ✅ Rate limiting
- ✅ Email notifications

## Continuous Integration

These tests are designed to run in CI/CD pipelines with:
- Fast execution (in-memory database)
- No external dependencies
- Comprehensive coverage
- Clear failure messages
- Parallel execution support
