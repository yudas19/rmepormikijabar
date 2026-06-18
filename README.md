# Rekam Medis Elektronik (RME) - Pormiki Jabar

A state-of-the-art Electronic Medical Records (EMR) application built for polyclinics. This application handles vital signs (TTV), SOAP charting, ICD-10 diagnosis/ICD-9 procedures, electronic prescriptions (E-Resep), laboratory orders, and automated medical document generation (sick leave, health certificates, external referrals, drug-free letters, and medical consent).

---

## 💻 Tech Stack & Environment Specifications

- **Backend Framework**: [Laravel 13.x](https://laravel.com/)
- **Frontend Interactivity**: [Livewire 4.x](https://livewire.laravel.com/) & [Alpine.js](https://alpinejs.dev/)
- **Design System & UI Components**: [Flux UI](https://fluxui.dev/) & [Tailwind CSS v4](https://tailwindcss.com/)
- **Authentication**: [Laravel Fortify](https://laravel.com/docs/fortify)
- **Role & Permission Management**: [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6)
- **PDF Generation engine**: [Laravel DomPDF](https://github.com/barryvdh/laravel-dompdf)
- **Testing Framework**: [Pest 4.x](https://pestphp.com/)
- **PHP Version Requirement**: PHP `^8.3` or `8.4`
- **Database Support**: MySQL / MariaDB, PostgreSQL, or SQLite (configured for SQLite by default in local starter project)

---

## 🪟 Windows Native Installation Guide

Follow these steps to run the application natively on Windows using a local PHP and Node.js stack.

### Prerequisites
1. **PHP 8.3 or 8.4**: Install PHP on Windows (e.g., via [XAMPP](https://www.apachefriends.org/), [Herd for Windows](https://herd.laravel.com/), or manual zip installation). Enable the following extensions in your `php.ini`:
   - `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `curl`, `zip`, `sqlite3` (if using SQLite), `gd` (for PDF/images).
2. **Composer**: Install Composer using the official [Windows Installer](https://getcomposer.org/doc/00-intro.md#installation-windows).
3. **Node.js & NPM**: Install the LTS version of Node.js from the official website [Node.js](https://nodejs.org/).
4. **Git**: Install Git for Windows from [git-scm.com](https://git-scm.com/).

### Installation Steps

1. **Clone the Repository**:
   ```bash
   git clone <repository-url>
   cd rmepormikijabar
   ```

2. **Install Composer Dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment Variables**:
   Copy the `.env.example` file to create your own configuration:
   ```cmd
   copy .env.example .env
   ```
   Open the `.env` file and configure your database settings. For example, if you are using SQLite:
   ```env
   DB_CONNECTION=sqlite
   # DB_DATABASE=/absolute/path/to/database.sqlite
   ```
   *(Note: Ensure the file `database/database.sqlite` exists by running `type nul > database/database.sqlite` if it's missing).*

   For MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=rme_pormiki
   DB_USERNAME=root
   DB_PASSWORD=secret
   ```

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   ```

6. **Install Frontend Dependencies & Build Assets**:
   ```bash
   npm install
   npm run build
   ```

7. **Run the Application**:
   - Start the Laravel local server:
     ```bash
     php artisan serve
     ```
   - Start the Vite development bundler (in a separate terminal window):
     ```bash
     npm run dev
     ```
   - The application is now accessible at `http://127.0.0.1:8000`.

---

## 🐳 Docker Deployment & Laravel Sail Guide

Laravel Sail provides a lightweight command-line interface for interacting with Laravel's default Docker development environment.

### Prerequisites
1. **WSL 2 (Windows Subsystem for Linux)**: Ensure WSL 2 is installed on Windows.
2. **Docker Desktop**: Install and run [Docker Desktop for Windows](https://www.docker.com/products/docker-desktop/), making sure WSL 2 integration is enabled in settings.

### Initialization & Installation

1. **Publish Sail Configuration** (if not already initialized):
   If you have a local PHP setup, run:
   ```bash
   php artisan sail:install
   ```
   Select your preferred services (e.g., MySQL, Redis, Mailpit). This will create a `docker-compose.yml` file.

2. **Starting Sail Containers**:
   To start the application containers in the background, run:
   ```bash
   ./vendor/bin/sail up -d
   ```
   *(For ease of use, you can configure a shell alias: `alias sail="./vendor/bin/sail"`)*

3. **Install Dependencies inside Docker**:
   ```bash
   sail composer install
   sail npm install
   sail npm run build
   ```

4. **Run Database Migrations & Seeds**:
   ```bash
   sail artisan migrate --seed
   ```

5. **Run Frontend Assets in Dev Mode**:
   ```bash
   sail npm run dev
   ```
   The application will be accessible at `http://localhost`.

---

## 🚀 Production Deployment Guidelines

When deploying to a live production environment (e.g., Ubuntu server, AWS, or [Laravel Cloud](https://cloud.laravel.com/)):

### Recommended Server Setup
- **Web Server**: Nginx or Apache configured to serve the `/public` directory.
- **PHP-FPM**: PHP 8.3/8.4 running with OPcache enabled for maximum efficiency.
- **Node/NPM**: Pre-compile assets locally or in CI/CD pipeline.

### Production Environment Settings (`.env`)
Make sure your configuration is optimized for production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configurations
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=rme_production
DB_USERNAME=rme_db_user
DB_PASSWORD=secure_password

# Optimization Checks
QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_STORE=database
```

### Deployment Commands Checklist
Run these commands on your server as part of your deployment script:
```bash
# 1. Pull changes
git pull origin main

# 2. Install dependencies with optimization flags
composer install --no-dev --optimize-autoloader

# 3. Cache configurations & routes to avoid files overhead
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Run database migrations safely
php artisan migrate --force

# 5. Compile final production assets
npm ci
npm run build
```
