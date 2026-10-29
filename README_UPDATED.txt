MediBook catalog and booking setup

The catalog seed in create_catalog.php configures 70 doctors across 10 departments, diagnostic imaging services with scanner equipment details, and laboratory tests.

The installed MongoDB Composer package requires PHP 8.1 or newer. Use a PHP 8.1+ runtime for the app and setup scripts.

After updating the project:
1. Run create_catalog.php to insert or refresh the doctor and service catalog.
2. Run create_slots.php to add slots for new doctors and services.
3. Open index.php and use Consult Doctor, Scanning, or Testing.

Both setup scripts are intended to be re-runnable. They do not delete users or appointments.
Doctor token windows are generated per doctor; the total adjusts to the current doctor catalog.
