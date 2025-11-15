# Safarni - Travel Platform API

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

<p align="center">
  <strong>A comprehensive multi-category travel platform API built with Laravel 12</strong>
</p>

---

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Project Structure](#project-structure)
- [API Documentation](#api-documentation)
- [Development](#development)
- [Testing](#testing)
- [Code Style](#code-style)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 About

Safarni is a multi-category travel platform API that enables users to discover and book tours, flights, cars, and hotels. The platform provides a comprehensive booking system with features like availability checking, pricing calculations, reviews, favorites, and multi-language support.

### Current Implementation Status

✅ **Completed:**
- Authentication & Authorization (Email/OTP, Google OAuth)
- Tour System (CRUD, Search, Filtering, Similar Tours)
- Booking System (Availability, Pricing, Booking Management)
- Reviews & Ratings
- Favorites/Wishlist
- Questions & Answers
- Multi-language Support (English/Arabic)
- Media Management
- API Documentation
- Postman Collection

🚧 **In Progress:**
- Flights Module
- Cars/Rentals Module
- Hotels Module
- Admin Panel

---

## ✨ Features

### Authentication & User Management
- Email/Password registration with OTP verification
- Google OAuth integration
- Password reset via OTP
- Profile management
- Role-based access control (RBAC)

### Tour System
- Tour discovery with advanced filtering
- Search functionality
- Featured tours
- Similar tours recommendations
- Tour details with gallery, itinerary, and activities
- Availability checking
- Multi-language support (English/Arabic)

### Booking System
- Real-time availability checking
- Dynamic pricing calculation
- Booking creation and management
- Booking confirmation and cancellation
- Payment integration ready
- Booking history

### Reviews & Ratings
- Review submission for completed bookings
- Rating system (1-5 stars)
- Review moderation
- Aggregated ratings

### Additional Features
- Favorites/Wishlist
- Questions & Answers
- Media management (images, galleries)
- Multi-language API responses
- Unified API response format
- Rate limiting
- Comprehensive error handling

---

## 🛠 Technology Stack

### Backend
- **PHP** 8.2+
- **Laravel** 12.0
- **Laravel Sanctum** 4.0 (API Authentication)
- **Laravel Jetstream** 5.3 (User Management)
- **Laravel Fortify** (Authentication)

### Packages
- **Spatie Media Library** 11.17 (Media Management)
- **Spatie Permission** 6.23 (Role & Permission Management)
- **Astrotomic Translatable** 11.16 (Multi-language Support)

### Frontend (Admin Panel)
- **Livewire** 3.6
- **Tailwind CSS** 3.4
- **Vite** 7.0

### Development Tools
- **Pest** 3.8 (Testing Framework)
- **Laravel Pint** 1.24 (Code Style)
- **Laravel Sail** 1.41 (Docker Environment)
- **Laravel Boost** 1.7 (Development Tools)

---

## 📦 Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18.x and NPM
- MySQL >= 8.0 or PostgreSQL >= 13
- Redis (optional, for caching and queues)

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd safarni
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Environment

Edit `.env` file with your database credentials and other settings:

```env
APP_NAME="Safarni"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=safarni
DB_USERNAME=root
DB_PASSWORD=

# Media Storage (use 'public' for local, 's3' for AWS)
FILESYSTEM_DISK=public

# Google OAuth (optional)
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/v1/auth/google/callback
```

### 5. Run Migrations and Seeders

```bash
# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```

### 6. Create Storage Link

```bash
php artisan storage:link
```

### 7. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Start Development Server

```bash
# Using Laravel's built-in server
php artisan serve

# Or using Laravel Sail (Docker)
./vendor/bin/sail up

# Or using the dev script (includes queue, logs, and vite)
composer run dev
```

The application will be available at `http://localhost:8000`

---

## ⚙️ Configuration

### Database Configuration

Ensure your database is created and credentials are set in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=safarni
DB_USERNAME=root
DB_PASSWORD=
```

### Media Storage

By default, media files are stored in `storage/app/public`. Make sure to:

1. Create the storage link: `php artisan storage:link`
2. Set proper permissions on `storage` and `bootstrap/cache` directories

For production, consider using S3 or other cloud storage:

```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=your_region
AWS_BUCKET=your_bucket
```

### Google OAuth Setup

1. Create a project in [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Google+ API
3. Create OAuth 2.0 credentials
4. Add credentials to `.env`:

```env
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/api/v1/auth/google/callback
```

---

## 📁 Project Structure

```
safarni/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/V1/        # API Controllers
│   │   ├── Requests/
│   │   │   └── Api/V1/         # Form Request Validation
│   │   ├── Resources/
│   │   │   └── Api/V1/         # API Resources
│   │   ├── Middleware/         # Custom Middleware
│   │   └── Traits/             # Shared Traits (ApiResponseTrait)
│   ├── Models/                 # Eloquent Models
│   ├── Services/               # Business Logic Services
│   └── ...
├── database/
│   ├── migrations/             # Database Migrations
│   └── seeders/                # Database Seeders
├── routes/
│   ├── api.php                 # API Routes Entry Point
│   └── api/v1/                 # Versioned API Routes
│       ├── auth.php
│       ├── tours.php
│       ├── bookings.php
│       ├── reviews.php
│       ├── favorites.php
│       ├── questions.php
│       └── users.php
├── docs/
│   └── API_DOCUMENTATION.md    # Complete API Documentation
├── postman/
│   └── Safarni_API_Collection.json  # Postman Collection
└── tests/                      # Pest Tests
```

---

## 📚 API Documentation

Complete API documentation is available in [`docs/API_DOCUMENTATION.md`](docs/API_DOCUMENTATION.md)

### Quick Start

**Base URL:** `http://localhost:8000/api/v1`

**Authentication:** Bearer Token
```
Authorization: Bearer {token}
```

**Language Support:** Add `?locale=en` or `?locale=ar` to any endpoint

### Example Request

```bash
# Register a new user
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Postman Collection

Import the Postman collection from [`postman/Safarni_API_Collection.json`](postman/Safarni_API_Collection.json) for easy API testing.

---

## 💻 Development

### Available Commands

```bash
# Start development server with queue, logs, and vite
composer run dev

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Generate IDE helper (if using IDE Helper)
php artisan ide-helper:generate
```

### Code Style

The project uses Laravel Pint for code formatting:

```bash
# Format code
vendor/bin/pint

# Format only changed files
vendor/bin/pint --dirty

# Check code style (without fixing)
vendor/bin/pint --test
```

### API Response Format

All API responses use a unified `ApiResponseTrait` for consistency:

**Success Response:**
```json
{
  "success": true,
  "message": "Optional message",
  "data": { ... },
  "meta": { ... }  // For paginated responses
}
```

**Error Response:**
```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Error message",
    "details": { ... }  // For validation errors
  }
}
```

---

## 🧪 Testing

The project uses [Pest](https://pestphp.com) for testing.

### Run Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/TourTest.php

# Run with filter
php artisan test --filter=testName

# Run with coverage
php artisan test --coverage
```

### Writing Tests

```php
// Example Feature Test
it('can list tours', function () {
    $response = $this->getJson('/api/v1/tours');
    
    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'data',
            'meta'
        ]);
});
```

---

## 🎨 Code Style

This project follows Laravel coding standards and uses:

- **Laravel Pint** for code formatting
- **PSR-12** coding standard
- **Type hints** for all methods
- **Return type declarations**
- **PHPDoc** blocks for complex methods

### Key Conventions

- Use descriptive variable and method names
- Follow existing code structure and patterns
- Use traits for shared functionality
- Keep controllers thin, move business logic to services
- Use Form Requests for validation
- Use API Resources for response formatting

---

## 📝 API Endpoints Overview

### Authentication
- `POST /api/v1/auth/register` - Register new user
- `POST /api/v1/auth/verify-otp` - Verify OTP
- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/logout` - Logout
- `POST /api/v1/auth/forgot-password` - Request password reset
- `POST /api/v1/auth/reset-password` - Reset password

### Google OAuth
- `GET /api/v1/auth/google/url` - Get OAuth URL
- `POST /api/v1/auth/google/exchange` - Exchange code for token
- `POST /api/v1/auth/google/link` - Link Google account
- `POST /api/v1/auth/google/unlink` - Unlink Google account

### Tours
- `GET /api/v1/tours` - List tours (with filters)
- `GET /api/v1/tours/{id}` - Get tour details
- `GET /api/v1/tours/{id}/similar` - Get similar tours
- `GET /api/v1/tours/featured` - Get featured tours
- `GET /api/v1/tours/search` - Search tours

### Bookings
- `POST /api/v1/bookings/check-availability` - Check availability
- `POST /api/v1/bookings/calculate-price` - Calculate price
- `POST /api/v1/bookings` - Create booking
- `GET /api/v1/bookings` - List user bookings
- `GET /api/v1/bookings/{id}` - Get booking details
- `POST /api/v1/bookings/{id}/confirm` - Confirm booking
- `POST /api/v1/bookings/{id}/cancel` - Cancel booking

### Reviews
- `POST /api/v1/reviews` - Create review
- `PUT /api/v1/reviews/{id}` - Update review
- `DELETE /api/v1/reviews/{id}` - Delete review

### Favorites
- `GET /api/v1/favorites` - List favorites
- `POST /api/v1/favorites/{tourId}/toggle` - Toggle favorite
- `GET /api/v1/favorites/{tourId}/check` - Check if favorited

### Questions
- `POST /api/v1/questions` - Ask question
- `POST /api/v1/questions/{id}/answer` - Answer question (Admin)

For complete API documentation, see [`docs/API_DOCUMENTATION.md`](docs/API_DOCUMENTATION.md)

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines

- Follow Laravel coding standards
- Write tests for new features
- Update documentation as needed
- Use meaningful commit messages
- Ensure all tests pass before submitting PR

---

## 📄 License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 📞 Support

For API support or questions:
- Check the [API Documentation](docs/API_DOCUMENTATION.md)
- Review the [Postman Collection](postman/Safarni_API_Collection.json)
- Contact the development team

---

## 🗺 Roadmap

- [ ] Flights Module (Search, Booking, Seat Selection)
- [ ] Cars/Rentals Module (Search, Booking, Pricing Tiers)
- [ ] Hotels Module (Search, Room Booking, Availability)
- [ ] Admin Panel (Full CRUD for all modules)
- [ ] Payment Gateway Integration
- [ ] Email Notifications
- [ ] Advanced Search & Filters
- [ ] Recommendation Engine
- [ ] Analytics & Reporting

---

**Built with ❤️ using Laravel 12**
