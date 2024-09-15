# Laravel 11 Invoicing Application

This is a custom-built invoicing application built using **Laravel 11** and **PHP 8.2**. The application manages customers, services, and generates invoices in a manner similar to telecom billing systems. It supports both recurring and one-time charges, along with custom invoicing features such as QR code payment links, email reminders, and PDF invoice generation. This application is developed as part of the graduation thesis for the **Faculty of Electrical Engineering and Information Technologies**.


## Features
- **User Profiles**: Manage users who can create and maintain customer profiles.
- **Customer Management**: Create, update, and delete customer records.
- **Service Management**: Configure services (Recurring or One-time charges) at the user or customer level.
- **Invoice Generation**: Generate monthly invoices for all customers or for specific customers. Option for ad-hoc invoice generation.
- **Email Reminders**: Automatic email reminders for unpaid invoices.
- **PDF Invoice Generation**: Each invoice includes a downloadable PDF with a QR code for payments.
- **Invoicing Portal**: Users can access all previous invoices at the customer level.

---

## Requirements

To run this project locally, you will need to have the following installed:

- **PHP**: Version 8.2 or higher
- **Composer**: Dependency manager for PHP
- **MySQL**: Version 8.0 or higher (for the database)
- **Node.js**: Version 16 or higher (for frontend assets)
- **NPM**: Comes with Node.js for installing JavaScript dependencies
- **Laravel 11**: Installed via Composer
- **Git**: Version control system (optional, but recommended)
---

## Installation and Setup

### Step 1: Clone the Repository

```bash
git clone  https://github.com/anamitrevska1/Diplomska-FEIT.git
cd Diplomska-FEIT
```

### Step 2: Install PHP Dependencies

Make sure you have **Composer** installed on your system. Install the project dependencies by running:

```bash
composer install
```

### Step 3: Install JavaScript Dependencies

Make sure you have **Node.js** and **NPM** installed. Then, install the frontend dependencies:

```bash
npm install
```

### Step 4: Configure Environment Variables

Copy the `.env.example` file to `.env` and modify the environment variables, particularly the database connection information.

```bash
cp .env.example .env
```

Update your `.env` file with the following values:

```
APP_NAME=Laravel Invoicing
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```

### Step 5: Generate Application Key

Generate the application key required by Laravel:

```bash
php artisan key:generate
```

### Step 6: Set Up the Database

Run the following command to migrate the database schema and set up initial data:

```bash
php artisan migrate
```

If you have seeders to populate default data:

```bash
php artisan db:seed
```

### Step 7: Compile Frontend Assets

Compile the frontend assets using Laravel Mix (Tailwind CSS is used for styling):

```bash
npm run dev
```

For production builds:

```bash
npm run build
```

### Step 8: Run the Development Server

To start the local development server, run:

```bash
php artisan serve
```

You should now be able to access the application at `http://localhost:8000`.

---

## Testing

To run tests for the project, use Laravel's PHPUnit integration:

```bash
php artisan test
```

Make sure to configure your `.env.testing` file for the test database setup.

---

## Additional Setup

### Cron Jobs for Reminders and Scheduled Invoicing

Set up a cron job on your server to execute Laravel's task scheduler every minute:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

This will handle sending invoice reminders and any other scheduled tasks.

---

By following this guide, you will be able to set up and run the Laravel 11 Invoicing Application on your local machine.
