# MediBook

MediBook is a PHP appointment-booking application for doctor consultations, diagnostic imaging, and laboratory tests. It stores its data in MongoDB; it does not use MySQL. The local development server is PHP's built-in server, so Apache is not required.

## Project structure

```text
app/
  components/  Shared interface pieces, such as the navigation bar
  config/      MongoDB connection and data-access helpers
  pages/       Page and booking-flow implementations
  scripts/     Catalog setup, account setup, and diagnostics
assets/
  css/         Page styles
  images/      Page illustrations
  js/          Browser-side booking helpers
docs/          Notes from earlier project iterations
vendor/        Composer dependencies (ignored by Git)
*.php          Stable local URLs that load the matching app page or script
```

Root PHP files remain as small entry points so forms, links, and existing URLs keep working while implementation code is grouped under `app/`.

## Technologies

- PHP 8.2, sessions, and the PHP built-in web server
- MongoDB with the PHP `mongodb` extension; no MySQL connection is used
- Composer-managed PHP packages: `mongodb/mongodb` and PHPMailer
- HTML, CSS, and vanilla JavaScript for the interface and interactive booking forms

## Start MediBook locally

1. Make sure MongoDB is installed and running. The default connection is `mongodb://127.0.0.1:27017` and the default database is `medibook_new`. You can set `MEDIBOOK_MONGODB_URI` and `MEDIBOOK_DATABASE` to use different values.
2. If the `vendor/` folder is missing, install dependencies from the project folder:

   ```powershell
   cd C:\xampp\htdocs\MediBook
   & C:\xampp82\php\php.exe "$env:LOCALAPPDATA\ComposerSetup\bin\composer.phar" install
   ```

3. Start the local web server from the project folder:

   ```powershell
   cd C:\xampp\htdocs\MediBook
   & C:\xampp82\php\php.exe -S localhost:8081
   ```

4. Open [http://localhost:8081](http://localhost:8081). The server serves the project root, where the stable PHP entry points live.
5. Create demo accounts with `http://localhost:8081/create_users.php`, then sign in. The demo patient is `user@medibook.com` / `user123`; the administrator is `admin@medibook.com` / `admin123`.
6. Sign in as the administrator and choose **Set up MediBook data** to add the doctor, service, and appointment-slot catalog. This setup adds or refreshes catalog data and does not erase users or appointments.

The PHP executable needs the MongoDB extension enabled. The PHP built-in server does not start MongoDB; MongoDB must be running separately for login, catalog, and booking features to read and save data.

## How it works

1. A visitor registers or signs in. PHP validates the submitted fields, stores password hashes in MongoDB, and keeps the signed-in user in a PHP session.
2. The doctor, scanning, and testing pages read their available providers, services, and slots from MongoDB. JavaScript filters categories and updates the visible booking times in the browser.
3. When a patient submits a booking, PHP checks the session, requested provider, date, and slot, then checks for a booking at the same date and time before saving an appointment.
4. Patients can view their own appointments. Administrators can update appointment status, manage slot availability, and prepare the catalog.

The database defaults to MongoDB database `medibook_new` and uses the `users`, `doctors`, `services`, `appointment_slots`, and `appointments` collections. The data-access functions are in `app/config/database.php`; shared interface pieces are in `app/components/`; page implementations and operational scripts are in `app/pages/` and `app/scripts/`.

## Local configuration

`vendor/` and local `.env` files are excluded from Git. Recreate dependencies with `composer install` after cloning. Do not commit credentials or expose the demo setup scripts on a public server without replacing the demo accounts and reviewing access controls.
