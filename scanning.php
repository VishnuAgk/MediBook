<?php
session_start();
require_once __DIR__ . '/db.php';
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

$scanCategories = ['MRI & Imaging', 'CT & Imaging', 'Ultrasound & X-Ray'];
$allServices = mongoFind('services', [], ['sort' => ['category' => 1, 'name' => 1]]);
$services = array_values(array_filter($allServices, static fn($service) => in_array($service['category'] ?? '', $scanCategories, true)));
$appointments = mongoFind('appointments', ['status' => ['$ne' => 'Cancelled']]);
$booked = [];
foreach ($appointments as $appointment) {
    $key = 'service|' . ($appointment['provider_id'] ?? '') . '|' . ($appointment['appointment_date'] ?? '') . '|' . ($appointment['appointment_time'] ?? '');
    $booked[$key] = true;
}
$slots = mongoFind('appointment_slots', ['provider_type' => 'service'], ['sort' => ['slot_time' => 1]]);
$slotMap = [];
foreach ($slots as $slot) {
    $providerId = $slot['provider_id'] ?? '';
    if ($providerId !== '') $slotMap[$providerId][] = $slot;
}
$groups = [];
foreach ($services as $service) $groups[$service['category']][] = $service;
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Scanning - MediBook</title><link rel="stylesheet" href="style.css"></head>
<body class="soft-page">
<header class="navbar"><a class="logo" href="index.php">🩺 <span>Medi</span>Book</a><nav><a href="index.php">Home</a><a href="consult_doctor.php">Consult Doctor</a><a class="active" href="scanning.php">Scanning</a><a href="testing.php">Testing</a><a href="mybookings.php">My Appointments</a><a href="logout.php">Logout</a></nav></header>
<main class="portal-main">
    <section class="page-hero scan-hero"><div><p class="eyebrow">DIAGNOSTIC IMAGING</p><h1>Book your scan with ease.</h1><p>Choose an imaging service, select a convenient time, and avoid unnecessary waiting.</p></div><div class="hero-art scan-art"><img src="imaging-centre.png" alt="MRI scanner in a modern imaging centre"><span>Imaging Centre</span></div></section>
    <section class="section compact">
        <div class="section-heading"><p class="eyebrow">1 · CHOOSE SCAN TYPE</p><h2>Imaging services</h2><p>Choose a category to narrow the list, or view every scan below.</p></div>
        <div class="choice-grid department-grid">
            <button type="button" class="choice-card scan-cat selected" data-filter=""><span class="choice-icon">✚</span><strong>All scans</strong><small><?php echo count($services); ?> services</small></button>
            <?php foreach ($groups as $category => $items): ?>
                <button type="button" class="choice-card scan-cat" data-filter="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"><span class="choice-icon">🩻</span><strong><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></strong><small><?php echo count($items); ?> services</small></button>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="section compact">
        <div class="service-directory" id="scanDirectory">
            <?php if (!$services): ?><p class="empty">No imaging services are currently listed. Please check back later.</p><?php endif; ?>
            <?php foreach ($services as $service): ?>
                <article class="test-card" data-category="<?php echo htmlspecialchars($service['category'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="test-icon" aria-hidden="true">🩻</div>
                    <div><span class="tag">SCANNING</span><h3><?php echo htmlspecialchars($service['name'] ?? 'Imaging service', ENT_QUOTES, 'UTF-8'); ?></h3><p><?php echo htmlspecialchars($service['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p><?php if (!empty($service['equipment'])): ?><small>Equipment: <?php echo htmlspecialchars($service['equipment'], ENT_QUOTES, 'UTF-8'); ?></small><?php endif; ?><small><?php echo htmlspecialchars($service['duration'] ?? '', ENT_QUOTES, 'UTF-8'); ?></small></div>
                    <button type="button" class="btn-primary small-btn scan-book" data-id="<?php echo htmlspecialchars($service['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">Book Scan</button>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="booking-panel" id="scanBooking"><div><p class="eyebrow">2 · APPOINTMENT</p><h2>Schedule your scan</h2><p id="selectedScan">Select a scan to continue.</p></div>
        <form action="book.php" method="post" class="booking-form">
            <input type="hidden" name="hospital" value="Aster MIMS Kannur"><input type="hidden" name="provider_type" value="service"><input type="hidden" name="provider_id" id="scan_id"><input type="hidden" name="provider_name" id="scan_name"><input type="hidden" name="category" id="scan_category"><input type="hidden" name="service_name" id="scan_service">
            <label>Date<input type="date" name="date" id="scan_date" required></label><label>Available time<select name="time" id="scan_time" required><option value="">Select scan first</option></select></label>
            <label>Patient name<input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['user']['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></label><label>Email<input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></label>
            <label>Notes<input type="text" name="reason" placeholder="Any preparation or referral note"></label><button class="btn-primary" type="submit">Confirm Scan</button>
        </form>
    </section>
</main>
<script>
const services = <?php echo json_encode($services, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
const slotMap = <?php echo json_encode($slotMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
const booked = <?php echo json_encode($booked, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
let chosen = null;
document.querySelectorAll('.scan-cat').forEach(button => button.addEventListener('click', () => {
    const category = button.dataset.filter;
    document.querySelectorAll('.scan-cat').forEach(item => item.classList.toggle('selected', item === button));
    document.querySelectorAll('#scanDirectory .test-card').forEach(card => {
        card.style.display = !category || card.dataset.category === category ? 'grid' : 'none';
    });
}));
document.querySelectorAll('.scan-book').forEach(button => button.addEventListener('click', () => {
    chosen = services.find(service => service.id === button.dataset.id);
    if (!chosen) return;
    document.getElementById('scan_id').value = chosen.id;
    document.getElementById('scan_name').value = chosen.name;
    document.getElementById('scan_service').value = chosen.name;
    document.getElementById('scan_category').value = chosen.category;
    document.getElementById('selectedScan').textContent = chosen.name + ' • ' + chosen.category;
    updateTimes();
    document.getElementById('scanBooking').scrollIntoView({behavior: 'smooth'});
}));
function updateTimes() {
    const select = document.getElementById('scan_time');
    select.innerHTML = '<option value="">Select an available time</option>';
    if (!chosen) return;
    const date = document.getElementById('scan_date').value;
    const providerSlots = slotMap[chosen.id] || [];
    if (!providerSlots.length) {
        select.innerHTML = '<option value="">No appointment times are configured for this scan</option>';
        return;
    }
    providerSlots.forEach(slot => {
        const key = 'service|' + chosen.id + '|' + date + '|' + slot.slot_time;
        const isBooked = Boolean(booked[key]);
        const isAvailable = slot.slot_status === 'Available';
        const option = document.createElement('option');
        option.value = slot.slot_time;
        option.textContent = slot.slot_time + (isBooked ? ' — Booked' : isAvailable ? ' — Available' : ' — Unavailable');
        option.disabled = isBooked || !isAvailable;
        select.appendChild(option);
    });
}
const today = new Date();
document.getElementById('scan_date').min = [today.getFullYear(), String(today.getMonth() + 1).padStart(2, '0'), String(today.getDate()).padStart(2, '0')].join('-');
document.getElementById('scan_date').addEventListener('change', updateTimes);
</script>
</body>
</html>
