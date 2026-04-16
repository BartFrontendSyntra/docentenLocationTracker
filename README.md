# Docenten App

The docenten app is a two-part system:

- `docenten-frontend` is the Angular application used by viewers and administrators.
- `docenten-api` is the Laravel API that provides authentication, teacher data, user management, and CSV import.

Together they provide role-based access to dashboards, teacher management, statistics, and a teacher import flow.

## Installation

### 1. Install the API

From the `docenten-api` folder:

```bash
composer install
php artisan serve
```

### Database setup

If you prefer a server database, update `docenten-api/.env` with your credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Then create the database in your database server, generate the app key, and run:

```bash
php artisan migrate --seed
```

This runs the migrations and seeds the database with starter data, including:

- `admin@example.com` with password `password`
- `viewer@example.com` with password `password`
- sample cities, addresses, teachers, courses, course types, and certificates

The API is expected to run at `http://127.0.0.1:8000/api`.

### 2. Install the frontend

From the `docenten-frontend` folder:

```bash
npm install
ng serve
```

Open the app at `http://localhost:4200/`.

## Testing As A User Or Administrator

The frontend has two protected areas:

- `Viewer` users can open the viewer dashboard, teacher list, map, and statistics.
- `Administrator` users can open the admin dashboard, user management, teacher management, and statistics.

To test the application:

1. Log in through the frontend with a Viewer or Administrator account.
2. Confirm you are redirected to the correct dashboard for that role.
3. Check that the menu and routes match your permissions.
4. Log out and confirm protected pages are no longer accessible.

## API Usage

The Laravel API exposes authenticated endpoints under `/api`.

### Authentication

- `POST /api/login` logs the user in and returns an access token.
- `POST /api/logout` logs the user out.
- `GET /api/user` returns the current authenticated user with their role.

### Core resources

The API also exposes CRUD endpoints for:

- `teachers`
- `users`
- `roles`
- `cities`
- `courses`
- `course-types`
- `certificates`
- `addresses`

These endpoints require authentication through Laravel Sanctum.

## CSV Import

CSV import is handled by the API and used from the frontend by administrators.

### In the frontend

1. Log in as an Administrator.
2. Open the teacher management section.
3. Upload a CSV file using the import action in the teacher interface.

### Exact CSV format

The CSV must use a header row with these columns in this exact order:

```csv
first_name,last_name,email,company_number,telephone,cellphone,street,house_number,city,postal_code,lat,lng,courses,certificates
```

Each following row represents one teacher. The importer expects:

- `first_name`, `last_name`, and `email` for the teacher identity.
- `company_number`, `telephone`, and `cellphone` as optional contact fields.
- `street`, `house_number`, `city`, `postal_code`, `lat`, and `lng` for the address data.
- `courses` and `certificates` as comma-separated lists inside a single CSV cell.

Example:

```csv
first_name,last_name,email,company_number,telephone,cellphone,street,house_number,city,postal_code,lat,lng,courses,certificates
Jane,Doe,jane.doe@example.com,BE0123456789,012345678,,Main Street,12,Antwerp,2000,51.2194,4.4025,"Mathematics,Physics","Pedagogy,First Aid"
```

Notes:

- Leave optional fields empty if you do not have data for them.
- Use quotes around `courses` and `certificates` if the field contains multiple values, because the importer splits those values on commas.
- The backend matches teachers by `email`, so each email should be unique.

