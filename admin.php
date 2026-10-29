<?php
session_start();
include "db.php";
if (!isset($_SESSION["user"]) || $_SESSION["user"]["role"] !== "admin") {
    die("Access Denied!");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    if ($_POST["action"] === "delete" && isset($_POST["id"])) {
        mongoDelete("appointments", ["id" => (string)$_POST["id"]]);
    } elseif ($_POST["action"] === "status" && isset($_POST["id"])) {
        $status = $_POST["status"] ?? "Booked";
        $allowed = ["Booked","Confirmed","Completed","Cancelled"];
        if (in_array($status, $allowed, true)) {
            mongoUpdate("appointments", ["id" => (string)$_POST["id"]], ['$set' => ["status" => $status]]);
        }
    } elseif ($_POST["action"] === "slot_status" && isset($_POST["slot_id"])) {
        $slotStatus = $_POST["slot_status"] ?? "Available";
        $allowedSlotStatuses = ["Available","Offline Visit","Blocked","Booked"];
        if (in_array($slotStatus, $allowedSlotStatuses, true)) {
            mongoUpdate("appointment_slots", ["id" => (string)$_POST["slot_id"]], ['$set' => ["slot_status" => $slotStatus]]);
        }
    }
}

$appointments = mongoFind("appointments", [], [
    'sort' => ['created_at' => -1]
]);
$slots = mongoFind("appointment_slots", [], [
    'sort' => ['doctor' => 1, 'slot_time' => 1]
]);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - MediBook</title><link rel="stylesheet" href="style.css"></head>
<body>
<header class="navbar">
<div class="logo">🩺 <span>Medi</span>Book <small>ADMIN</small></div>
<nav><a href="index.php">Home</a><a href="logout.php">Logout</a></nav>
</header>
<main class="section">
<div class="section-heading"><p class="eyebrow">ADMIN PANEL</p><h1>Appointment Management</h1><p>View, update and delete patient appointments.</p></div>
<section class="slot-admin">
    <div class="section-heading compact">
        <p class="eyebrow">DATABASE SETUP</p>
        <h2>Prepare doctors, tests, scans and booking slots</h2>
        <p>Run this after installing MediBook or when the catalog or appointment slots are missing.</p>
    </div>
    <form action="setup_catalog.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ??= bin2hex(random_bytes(32)), ENT_QUOTES, 'UTF-8'); ?>">
        <button class="btn-primary" type="submit">Set up MediBook data</button>
    </form>
</section>
<div class="table-wrap">
<table class="admin-table">
<thead><tr><th>ID</th><th>Patient</th><th>Email</th><th>Type</th><th>Doctor / Service</th><th>Category</th><th>Date</th><th>Time</th><th>Token</th><th>Status</th><th>Action</th></tr></thead>
<tbody>
<?php if (!$appointments): ?>
<tr><td colspan="11" class="empty">No appointments yet.</td></tr>
<?php else: foreach ($appointments as $a): ?>
<tr>
<td>#<?php echo htmlspecialchars($a["id"]); ?></td>
<td><?php echo htmlspecialchars($a["user_name"]); ?></td>
<td><?php echo htmlspecialchars($a["user_email"]); ?></td>
<td><?php echo htmlspecialchars(($a["provider_type"] ?? "doctor") === "doctor" ? "Doctor Consultation" : "Diagnostic / Health Checkup"); ?></td>
<td><?php echo htmlspecialchars($a["provider_name"] ?? ($a["doctor"] ?? "")); ?></td>
<td><?php echo htmlspecialchars($a["category"] ?? ($a["specialty"] ?? "")); ?></td>
<td><?php echo htmlspecialchars($a["appointment_date"]); ?></td>
<td><?php echo htmlspecialchars($a["appointment_time"]); ?></td>
<td><?php echo isset($a["token_no"]) && $a["token_no"] !== null ? "#" . htmlspecialchars((string)$a["token_no"]) : "Service"; ?></td>
<td>
<form method="post" class="inline-form">
<input type="hidden" name="action" value="status"><input type="hidden" name="id" value="<?php echo $a["id"]; ?>">
<select name="status" onchange="this.form.submit()">
<?php foreach (["Booked","Confirmed","Completed","Cancelled"] as $s): ?>
<option <?php echo $a["status"] === $s ? "selected" : ""; ?>><?php echo $s; ?></option>
<?php endforeach; ?>
</select>
</form>
</td>
<td>
<form method="post" onsubmit="return confirm('Delete this appointment?');">
<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $a["id"]; ?>">
<button class="action-btn btn-danger">Delete</button>
</form>
</td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>

<div class="slot-admin">
    <div class="section-heading compact">
        <p class="eyebrow">LIVE SLOT CONTROL</p>
        <h2>Doctor appointment slots</h2>
        <p>Set individual slots to Available, Offline Visit or Blocked. Booked slots are controlled automatically.</p>
    </div>
    <div class="table-wrap">
    <table class="admin-table">
        <thead><tr><th>Type</th><th>Doctor / Service</th><th>Category</th><th>Time</th><th>Current Status</th><th>Change Status</th></tr></thead>
        <tbody>
        <?php foreach ($slots as $slot): ?>
        <tr>
            <td><?php echo htmlspecialchars(($slot["provider_type"] ?? "doctor") === "doctor" ? "Doctor" : "Service"); ?></td>
            <td><?php echo htmlspecialchars($slot["doctor"] ?? ($slot["provider_name"] ?? "")); ?></td>
            <td><?php echo htmlspecialchars($slot["category"] ?? ""); ?></td>
            <td><?php echo htmlspecialchars($slot["slot_time"]); ?></td>
            <td><span class="slot-status <?php echo strtolower(str_replace(' ', '-', $slot["slot_status"])); ?>"><?php echo htmlspecialchars($slot["slot_status"]); ?></span></td>
            <td>
                <?php if ($slot["slot_status"] === "Booked"): ?>
                    <strong>Booked</strong>
                <?php else: ?>
                <form method="post" class="inline-form">
                    <input type="hidden" name="action" value="slot_status">
                    <input type="hidden" name="slot_id" value="<?php echo $slot["id"]; ?>">
                    <select name="slot_status" onchange="this.form.submit()">
                        <?php foreach (["Available","Offline Visit","Blocked"] as $ss): ?>
                        <option value="<?php echo $ss; ?>" <?php echo $slot["slot_status"] === $ss ? "selected" : ""; ?>><?php echo $ss; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
</main>
</body>
</html>
