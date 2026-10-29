MediBook booking pages

The signed-in user flow is:
- index.php: home and service overview
- consult_doctor.php: browse departments and doctors, then book a consultation
- scanning.php: browse imaging services and book a scan
- testing.php: browse laboratory tests and book a test
- mybookings.php: review appointments

Doctors are loaded from the doctors collection. The catalog contains 70 doctors across 10 departments. Doctor appointment slots use provider_type=doctor. Older slot records that contain doctor_id rather than provider_id remain supported; create_slots.php writes both fields for compatibility.

Scanning and testing services are loaded from the services collection and use appointment_slots records with provider_type=service. Scanning entries list the equipment used; testing includes blood sugar, creatinine, kidney, liver, cardiac and other tests. Both pages provide an all-items view and category filtering.

Setup if the MongoDB catalog or slots are missing:
1. Sign in with an administrator account and open Admin.
2. Select “Set up MediBook data”; this prepares the catalog before generating doctor and service slots.
3. The standalone create_catalog.php and create_slots.php scripts remain available for local maintenance.
