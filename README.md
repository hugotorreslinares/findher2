<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

# Project Marketplace

This is a Laravel-based marketplace application where users can create profiles, list products, and manage categories. It features a robust administration system and secure endpoints for data management.

## Prerequisites

- PHP ^7.2
- Composer
- Node.js & NPM
- MySQL

## Installation and Setup

Follow these steps to get the project running locally:

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd <repository-directory>
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   ```
   Modify the `.env` file to set your database credentials:
   ```env
   DB_DATABASE=your_database_name
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations and Seeders:**
   ```bash
   php artisan migrate --seed
   ```

6. **Install and Compile Assets:**
   ```bash
   npm install
   npm run dev
   ```

7. **Start the Development Server:**
   ```bash
   php artisan serve
   ```
   The application will be available at `http://localhost:8000`.

## How the Project Works

### User Roles
- **Regular User:** Can create and update their personal profile, including uploading images, and list products in different categories.
- **Admin:** Has access to the administration dashboard (`/admin`) where they can manage all registered users and create/edit categories.

### Key Features
- **Profiles:** Every user has a profile (`/user/{id}`) containing their location (latitude/longitude), contact information (WhatsApp, Phone), and a gallery of images.
- **Products:** Users can list products, associating them with specific categories.
- **Categories:** Products are organized into categories, which can be managed via the admin interface.
- **Security:**
    - Admin routes are protected by a custom `IsAdmin` middleware.
    - All data submissions are validated to prevent mass assignment.
    - Profile updates are strictly bound to the authenticated user's ID to prevent IDOR.
    - File uploads are validated for type and size.

## Project Structure

- `app/Http/Controllers/`: Contains the logic for users, products, and categories.
- `app/Http/Middleware/`: Includes the custom `IsAdmin` middleware.
- `routes/web.php`: Defines the web routes and applies security middlewares.
- `database/migrations/`: Database schema definitions.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
