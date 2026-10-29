MEDIBOOK - MONGODB VERSION
===========================

This version replaces the original MySQL/MariaDB database with MongoDB.
The website remains PHP + HTML + CSS + JavaScript and can still be served by XAMPP Apache.

1. KEEP MONGODB RUNNING
-----------------------
Open Windows Services and make sure your MongoDB service is Running.
Default MongoDB address used by this project:
    mongodb://127.0.0.1:27017

2. XAMPP
--------
You only need Apache from XAMPP for this version.
MySQL does NOT need to be running.

Start Apache and open:
    http://localhost:8080/MediBook/

3. REQUIRED PHP EXTENSION
-------------------------
The PHP used by XAMPP must have the MongoDB PHP extension (ext-mongodb).
The project checks for this automatically and shows a clear message if it is missing.

To check the PHP used by XAMPP, open Command Prompt and run:
    C:\xampp\php\php.exe -v

Then check:
    C:\xampp\php\php.exe -m | findstr mongodb

If the second command prints "mongodb", the extension is enabled.
If it prints nothing, install/enable ext-mongodb for the exact PHP version and architecture used by XAMPP, then restart Apache.

4. DATABASE / COLLECTIONS
-------------------------
MongoDB database:
    medibook

Collections created automatically:
    users
    doctors (reserved for future use)
    appointments
    appointment_slots

The current website keeps the doctor list in index.php, matching the original project.
Appointment slots and users are seeded automatically by db.php.

5. DEMO LOGIN
-------------
User:
    user@medibook.com
    user123

Admin:
    admin@medibook.com
    admin123

6. IMPORTANT
------------
Do not delete the MongoDB data directory.
Do not change the MongoDB port unless you also change the connection string in db.php.

7. ORIGINAL PROJECT BEHAVIOUR RETAINED
---------------------------------------
- Login/logout
- Doctor listing
- Doctor qualification and specialty
- Appointment date and time selection
- Available / Offline Visit / Blocked / Booked slots
- Appointment confirmation
- My Appointments
- Admin appointment status updates
- Admin appointment deletion
- Admin live slot status control
