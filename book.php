<?php
session_start();
include "db.php";

if (!isset($_SESSION['user']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$hospital = trim($_POST["hospital"] ?? "Aster MIMS Kannur");
$providerType = trim($_POST["provider_type"] ?? "");
$providerId = trim($_POST["provider_id"] ?? "");
$providerName = trim($_POST["provider_name"] ?? "");
$category = trim($_POST["category"] ?? "");
$qualification = trim($_POST["qualification"] ?? "");
$specialty = trim($_POST["specialty"] ?? "");
$serviceName = trim($_POST["service_name"] ?? "");
$date = trim($_POST["date"] ?? "");
$time = trim($_POST["time"] ?? "");
$reason = trim($_POST["reason"] ?? "");
$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");

if (!in_array($providerType, ['doctor', 'service'], true) || !$providerId || !$date || !$time || !$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid appointment details. Please go back and try again.");
}

if ($date < date("Y-m-d")) {
    die("Please select today or a future date.");
}

// Confirm that the provider/service exists in the selected collection.
$catalogCollection = $providerType === 'doctor' ? 'doctors' : 'services';
$catalogItem = mongoFindOne($catalogCollection, ['id' => $providerId]);
if (!$catalogItem) {
    die("The selected doctor/service could not be found.");
}

$providerName = $catalogItem['name'];
$category = $catalogItem['category'] ?? $category;
if ($providerType === 'doctor') {
    $qualification = $catalogItem['qualification'] ?? '';
    $specialty = $catalogItem['specialty'] ?? '';
    $serviceName = '';
} else {
    $serviceName = $catalogItem['name'];
    $qualification = '';
    $specialty = $catalogItem['category'] ?? '';
}

// The master slot is shared across dates. A booking is date-specific, so the same
// time can be reused on another day without changing the master slot document.
$slot = mongoFindOne("appointment_slots", [
    "provider_type" => $providerType,
    "provider_id" => $providerId,
    "slot_time" => $time,
    "slot_status" => "Available"
]);

if (!$slot && $providerType === 'doctor') {
    $slot = mongoFindOne("appointment_slots", [
        "provider_type" => "doctor",
        "doctor_id" => $providerId,
        "slot_time" => $time,
        "slot_status" => "Available"
    ]);
}

if (!$slot) {
    die("Sorry, that time is not available for online booking. Please choose another slot.");
}

// Prevent two patients from booking the same provider/service, date and time.
$tokenNo = $slot['token_no'] ?? null;

$alreadyBooked = mongoFindOne("appointments", [
    "provider_type" => $providerType,
    "provider_id" => $providerId,
    "appointment_date" => $date,
    "appointment_time" => $time,
    "status" => ['$ne' => 'Cancelled']
]);

if ($alreadyBooked) {
    die("Sorry, that slot has just been booked. Please choose another time.");
}
$appointmentId = bin2hex(random_bytes(8));
$createdAt = date('Y-m-d H:i:s');

mongoInsert("appointments", [
    "id" => $appointmentId,
    "user_name" => $name,
    "user_email" => $email,
    "hospital" => $hospital,
    "provider_type" => $providerType,
    "provider_id" => $providerId,
    "provider_name" => $providerName,
    "doctor" => $providerType === 'doctor' ? $providerName : "",
    "qualification" => $qualification,
    "specialty" => $specialty,
    "category" => $category,
    "service_name" => $serviceName,
    "appointment_date" => $date,
    "appointment_time" => $time,
    "token_no" => $tokenNo,
    "reason" => $reason,
    "status" => "Booked",
    "created_at" => $createdAt
]);

$_SESSION['appointment_info'] = [
    "id" => $appointmentId,
    "name" => $name,
    "provider_name" => $providerName,
    "date" => $date,
    "time" => $time,
    "type" => $providerType
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appointment Confirmed - MediBook</title><link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar"><div class="logo">🩺 <span>Medi</span>Book</div><nav><a href="index.php">Home</a><a href="mybookings.php">My Appointments</a><a href="logout.php">Logout</a></nav></header>
<main class="confirmation-wrap"><section class="confirmation-card">
<div class="success-icon">✓</div><p class="eyebrow">APPOINTMENT CONFIRMED</p><h1>Your appointment is booked.</h1>
<p>Thank you, <strong><?php echo htmlspecialchars($name); ?></strong>. Your appointment details are below.</p>
<div class="confirmation-details">
<div><span>Appointment Type</span><strong><?php echo $providerType === 'doctor' ? 'Doctor Consultation' : 'Diagnostic / Health Checkup'; ?></strong></div>
<div><span>Department / Category</span><strong><?php echo htmlspecialchars($category); ?></strong></div>
<div><span>Doctor / Service</span><strong><?php echo htmlspecialchars($providerName); ?></strong></div>
<div><span>Date</span><strong><?php echo htmlspecialchars($date); ?></strong></div>
<div><span>Time</span><strong><?php echo htmlspecialchars($time); ?></strong></div>
<?php if ($tokenNo !== null): ?><div><span>Online Token</span><strong>#<?php echo htmlspecialchars((string)$tokenNo); ?></strong></div><?php endif; ?>
<div><span>Appointment ID</span><strong>#<?php echo htmlspecialchars($appointmentId); ?></strong></div>
</div>
<div class="notice">Please arrive before your appointment time. Offline visitor windows are kept separate from online booking tokens.</div>
<a href="index.php" class="btn-primary">Book Another Appointment</a><a href="mybookings.php" class="btn-secondary">View My Appointments</a>
</section></main>
</body>
</html>
