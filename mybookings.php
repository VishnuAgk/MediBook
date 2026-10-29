<?php
session_start();
include "db.php";
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}
$email = $_SESSION["user"]["email"];
$appointments = mongoFind("appointments", ["user_email" => $email], [
    'sort' => ['appointment_date' => -1, 'appointment_time' => -1, 'created_at' => -1]
]);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Appointments - MediBook</title><link rel="stylesheet" href="style.css"></head>
<body>
<header class="navbar">
<div class="logo">🩺 <span>Medi</span>Book</div>
<nav><a href="index.php">Home</a><a href="index.php#doctors">Doctors</a><a href="logout.php">Logout</a></nav>
</header>
<main class="section">
<div class="section-heading"><p class="eyebrow">PATIENT DASHBOARD</p><h1>My Appointments</h1><p><?php echo htmlspecialchars($email); ?></p></div>
<div class="table-wrap">
<table class="admin-table">
<thead><tr><th>ID</th><th>Hospital</th><th>Type</th><th>Doctor / Service</th><th>Category</th><th>Date</th><th>Time</th><th>Token</th><th>Status</th></tr></thead>
<tbody>
<?php if (!$appointments): ?>
<tr><td colspan="9" class="empty">No appointments found. <a href="index.php#booking">Book one now</a>.</td></tr>
<?php else: foreach ($appointments as $a): ?>
<tr>
<td>#<?php echo htmlspecialchars($a["id"]); ?></td>
<td><?php echo htmlspecialchars($a["hospital"]); ?></td>
<td><?php echo htmlspecialchars(($a["provider_type"] ?? "doctor") === "doctor" ? "Doctor Consultation" : "Diagnostic / Health Checkup"); ?></td>
<td><?php echo htmlspecialchars($a["provider_name"] ?? ($a["doctor"] ?? "")); ?></td>
<td><?php echo htmlspecialchars($a["category"] ?? ($a["specialty"] ?? "")); ?></td>
<td><?php echo htmlspecialchars($a["appointment_date"]); ?></td>
<td><?php echo htmlspecialchars($a["appointment_time"]); ?></td>
<td><?php echo isset($a["token_no"]) && $a["token_no"] !== null ? "#" . htmlspecialchars((string)$a["token_no"]) : "Service"; ?></td>
<td><span class="status"><?php echo htmlspecialchars($a["status"]); ?></span></td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>
</main>
</body>
</html>