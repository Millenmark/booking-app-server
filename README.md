# KB Barbershop (Booking Application)

A robust API powering the booking system with features like authentication, service management, booking creation and tracking, payments, audit logs, role-based access, API key validation, and analytics.

## API Base URL

[https://mkdev-booking-app.laravel.cloud](https://mkdev-booking-app.laravel.cloud/)

## Run Locally

Clone the project

```bash
  git clone https://github.com/Millenmark/booking-app-server.git
```

Go to the project directory

```bash
  cd booking-app-server
```

Install dependencies

```bash
  composer install
```

Generate Application Key

```bash
  php artisan key:generate
```

Migrate and seed

```bash
  php artisan migrate --seed
```

Run the server

```
  php artisan serve
```

## Environment Variables

To run this project, you will need to add the following custom environment variables to your .env file.

`APP_URL=http://localhost:3000`

`API_KEY=local`

`CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:3001`

## Postman file for import

The Postman file is already included in the project directory. Please import the appropriate file based on your Postman version.

This setup includes:

-   Pre- and post-request scripts
-   API request samples
-   Preconfigured variables

Using this file is more convenient because it automatically sets the authorization token as a variable when you hit the login endpoint.
![Postman Screenshot](https://res.cloudinary.com/dcrkja9f8/image/upload/v1758471984/personal/booking-app/documentation/server_01_pndcpu.png)

## Initial ERD

![ERD Screenshot](https://res.cloudinary.com/dcrkja9f8/image/upload/v1758360443/personal/booking-app/documentation/erd_q1qhd9.png)

## API Reference if you didn't import postman file

#### Set Header in every request

| Key             | Value                       |
| :-------------- | :-------------------------- |
| `Authorization` | `Bearer {login token here}` |
| `X-Api-Key`     | `local`                     |

#### Auth: Login

```http
  POST /api/login
```

```JSON
{
    "email": "staff@mailsac.com",
    "password": "password"
}
```

#### Auth: Register

```http
  POST /api/register
```

```JSON
{
    "name": "sample",
    "email": "customer3@mailsac.com",
    "password": "password",
    "password_confirmation": "password"
}
```

#### Auth: Logout

```http
  POST /api/register
```

#### Auth: Forgot Password

```http
  POST /api/forgot-password
```

```JSON
{
    "email": "customer1@mailsac.com"
}
```

#### Auth: Reset Password

```http
  POST /api/reset-password
```

```JSON
{
    "email": "customer1@mailsac.com",
    "token": {Generated from the forgot-password endpoint},
    "password": "newpassword",
    "password_confirmation": "newpassword"
}
```

#### Booking: Get

```http
  GET /api/bookings
```

#### Booking: Create

```http
  POST /api/bookings
```

```JSON
{
    "service_id": 1,
    "scheduled_at": "2025-09-24T14:30:00"
}
```

#### Booking: Update

```http
  PUT /api/bookings/{id}
```

```JSON
{
    "service_id": 1,
    "scheduled_at": "2025-09-26T14:30:00"
}
```

#### Booking: Delete (Soft Delete)

```http
  DELETE /api/bookings/{id}
```

## Tech Stack

-   Laravel
-   PostgreSQL
