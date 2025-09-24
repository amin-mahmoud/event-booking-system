# Event Booking System - Laravel 12

A comprehensive event booking system with authentication, role-based access control, payment processing, and notifications.

## Features

- **Role-based Authentication** (Admin, Organizer, Customer)
- **Event Management** (Create, update, delete events)
- **Ticket Booking System** with inventory tracking
- **Payment Processing** (Mock gateway)
- **Email & Database Notifications**
- **Redis Caching** for performance
- **Queue Processing** for background tasks

## Quick Setup

### 1. Installation
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan install:api
```

### 2. Database Configuration
Update `.env`:
```env
DB_CONNECTION=mysql
DB_DATABASE=event_booking_system
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
CACHE_STORE=redis
```

### 3. Database Setup
```bash
php artisan migrate
php artisan queue:table
php artisan notifications:table
php artisan migrate
php artisan db:seed
```

### 4. Start Services
```bash
php artisan serve
php artisan queue:work
```

## Seeded Test Data

The system comes with pre-populated data:
- **2 Admins** (admin@eventbooking.com / 12341234)
- **3 Organizers** (organizer1@eventbooking.com / 12341234)
- **10 Customers** (john.smith@customer.com / 12341234)
- **5 Events** with various categories
- **15 Tickets** (3 types per event)
- **20 Bookings** with different statuses

## Key API Endpoints

### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `GET /api/me` - Get current user

### Events
- `GET /api/events` - List events (public)
- `POST /api/events` - Create event (organizer only)
- `PUT /api/events/{id}` - Update event (owner only)

### Bookings
- `POST /api/tickets/{id}/bookings` - Create booking
- `GET /api/bookings` - User's bookings
- `PUT /api/bookings/{id}/cancel` - Cancel booking

### Payments
- `POST /api/bookings/{id}/payment` - Process payment
- `GET /api/payments/{id}` - Payment details

## Testing

```bash
php artisan test
php artisan test --coverage
```

## Role Permissions

- **Organizers**: Create/manage their events and tickets
- **Customers**: Book tickets, view their bookings
- **Admins**: System administration, payment refunds

## Requirements

- PHP 8.2+
- MySQL 8.0+
- Redis (recommended)
- Composer

Access the application at `http://localhost:8000` after running `php artisan serve`.

[1](https://github.com/dreamdev21/Laravel-Event-management-system)
[2](https://www.reddit.com/r/laravel/comments/13cbkwj/creating_a_reservation_system_in_laravel_step_by/)
[3](https://hackmd.io/@NVasWfYTTXCjDGs-lwWX0A/HkHn9CpV6)
[4](https://packagist.org/packages/reedware/laravel-events)
[5](https://itsourcecode.com/free-projects/php-project/event-booking-system-project-in-laravel-with-source-code/)
