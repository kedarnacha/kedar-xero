# Laravel Xero Integration Project

This project is a Laravel application integrated with Xero using the official Xero PHP SDK (`xeroapi/xero-php-oauth2`).

## Features
- Laravel 8.x based application
- Xero API integration (AccountingApi, etc.)
- Inertia.js for modern SPA experience
- Tailwind CSS and Vite for frontend assets
- **Lando** support for local development

## Requirements
- PHP 7.3 or 8.0+
- Composer
- Node.js & npm (for frontend assets)
- Xero developer account (for API credentials)
- [Lando](https://lando.dev/) (for local development, optional but recommended)

## Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd kedar-new-xero/app
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Copy the environment file and set your environment variables**
   ```bash
   cp .env.example .env
   # Edit .env and set your DB, Xero, and other credentials
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets**
   ```bash
   npm run dev
   # or for production
   npm run build
   ```

8. **Run the application**
   ```bash
   php artisan serve
   ```

## Lando Setup (Optional)

1. **Install Lando**
   - Download and install from [https://lando.dev/](https://lando.dev/)

2. **Add a `.lando.yml` file** (example):
   ```yaml
   name: kedar-new-xero
   recipe: laravel
   config:
     webroot: public
     database: mysql
   services:
     appserver:
       type: php:8.0
   proxy:
     appserver:
       - kedar-new-xero.lndo.site:80
   tooling:
     artisan:
       service: appserver
   ```

3. **Start Lando**
   ```bash
   lando start
   ```

4. **Access your app**
   - Visit: [http://kedar-new-xero.lndo.site](http://kedar-new-xero.lndo.site)

5. **Use Lando for common tasks**
   ```bash
   lando composer install
   lando npm install
   lando artisan migrate
   lando artisan serve
   lando npm run dev
   ```

## Xero Integration
- The Xero SDK is installed via Composer.
- Example usage: `XeroAPI\XeroPHP\Api\AccountingApi`.
- Store your Xero credentials in `.env` and configure as needed.

## Testing
```bash
php artisan test
```

## Useful Commands
- `php artisan` — Laravel CLI
- `npm run dev` — Start Vite dev server
- `npm run build` — Build frontend assets
- `lando <command>` — Run commands inside Lando (e.g., `lando artisan migrate`)

## License
MIT
