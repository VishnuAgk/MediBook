<?php
session_start();
require_once dirname(__DIR__, 2) . '/db.php';
if (!isset($_SESSION['user'])) { header('Location: login.php'); exit; }

$testCategories = ['Blood Tests', 'Organ Function Tests', 'Hormone & Vitamin Tests', 'Cardiac Tests', 'Health Checkups'];
$allServices = mongoFind('services', [], ['sort' => ['category' => 1, 'name' => 1]]);
$services = array_values(array_filter($allServices, static fn($service) => in_array($service['category'] ?? '', $testCategories, true)));
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
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Medical Testing - MediBook</title><link rel="stylesheet" href="assets/css/style.css"></head>
<body class="soft-page">
<?php $activeNav = 'testing'; require dirname(__DIR__) . '/components/navbar.php'; ?>
<main class="portal-main">
    <section class="page-hero test-hero"><div><p class="eyebrow">LABORATORY & HEALTH TESTING</p><h1>Check your health, one test at a time.</h1><p>Book blood tests, organ-function tests, cardiac tests and preventive health checkups.</p></div><div class="hero-art lab-art"><img src="assets/images/laboratory-testing.png" alt="Laboratory technician preparing a blood sample"><span>Laboratory Services</span></div></section>
    <section class="section compact">
        <div class="section-heading"><p class="eyebrow">1 · CHOOSE TEST CATEGORY</p><h2>What do you need?</h2><p>Choose a category to narrow the list, or view every test below.</p></div>
        <div class="choice-grid department-grid">
            <button type="button" class="choice-card test-cat selected" data-filter=""><span class="choice-icon">✚</span><strong>All tests</strong><small><?php echo count($services); ?> tests</small></button>
            <?php foreach ($groups as $category => $items): ?>
                <button type="button" class="choice-card test-cat" data-filter="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"><span class="choice-icon">🧪</span><strong><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></strong><small><?php echo count($items); ?> tests</small></button>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="section compact">
        <div class="section-heading"><p class="eyebrow">2 · AVAILABLE TESTS</p><h2 id="testHeading">All medical tests</h2><p id="testHint"><?php echo count($services); ?> tests available. Select a test to schedule an appointment.</p></div>
        <div class="service-directory" id="testDirectory">
            <?php if (!$services): ?><p class="empty">No laboratory tests are currently listed. Please check back later.</p><?php endif; ?>
            <?php foreach ($services as $service): ?>
                <article class="test-card lab-card" data-category="<?php echo htmlspecialchars($service['category'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="test-icon" aria-hidden="true">🧪</div>
                    <div><span class="tag">LAB TEST</span><h3><?php echo htmlspecialchars($service['name'] ?? 'Medical test', ENT_QUOTES, 'UTF-8'); ?></h3><p><?php echo htmlspecialchars($service['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p><small><?php echo htmlspecialchars($service['duration'] ?? '', ENT_QUOTES, 'UTF-8'); ?></small></div>
                    <button type="button" class="btn-primary small-btn test-book" data-id="<?php echo htmlspecialchars($service['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">Book Test</button>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <section class="booking-panel" id="testBooking"><div><p class="eyebrow">3 · APPOINTMENT</p><h2>Schedule your test</h2><p id="selectedTest">Select a test to continue.</p></div>
        <form action="book.php" method="post" class="booking-form">
            <input type="hidden" name="hospital" value="Aster MIMS Kannur"><input type="hidden" name="provider_type" value="service"><input type="hidden" name="provider_id" id="test_id"><input type="hidden" name="provider_name" id="test_name"><input type="hidden" name="category" id="test_category"><input type="hidden" name="service_name" id="test_service">
            <label>Date<input type="date" name="date" id="test_date" required></label><label>Available time<select name="time" id="test_time" required><option value="">Select test first</option></select></label>
            <label>Patient name<input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['user']['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></label><label>Email<input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></label>
            <label>Notes<input type="text" name="reason" placeholder="Preparation/referral note"></label><button class="btn-primary" type="submit">Confirm Test Booking</button>
        </form>
    </section>
</main>
<script>
const services = <?php echo json_encode($services, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
const slotMap = <?php echo json_encode($slotMap, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
const booked = <?php echo json_encode($booked, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
let chosen = null;
function filterTests(category, button) {
    document.querySelectorAll('.test-cat').forEach(item => item.classList.toggle('selected', item === button));
    document.querySelectorAll('#testDirectory .test-card').forEach(card => {
        card.style.display = !category || card.dataset.category === category ? 'grid' : 'none';
    });
    const count = services.filter(service => !category || service.category === category).length;
    document.getElementById('testHeading').textContent = category || 'All medical tests';
    document.getElementById('testHint').textContent = count + (count === 1 ? ' test' : ' tests') + ' available. Select a test to schedule an appointment.';
}
document.querySelectorAll('.test-cat').forEach(button => button.addEventListener('click', () => filterTests(button.dataset.filter, button)));
document.querySelectorAll('.test-book').forEach(button => button.addEventListener('click', () => {
    chosen = services.find(service => service.id === button.dataset.id);
    if (!chosen) return;
    document.getElementById('test_id').value = chosen.id;
    document.getElementById('test_name').value = chosen.name;
    document.getElementById('test_service').value = chosen.name;
    document.getElementById('test_category').value = chosen.category;
    document.getElementById('selectedTest').textContent = chosen.name + ' • ' + chosen.category;
    updateTimes();
    document.getElementById('testBooking').scrollIntoView({behavior: 'smooth'});
}));
function updateTimes() {
    const select = document.getElementById('test_time');
    select.innerHTML = '<option value="">Select an available time</option>';
    if (!chosen) return;
    const date = document.getElementById('test_date').value;
    const providerSlots = slotMap[chosen.id] || [];
    if (!providerSlots.length) {
        select.innerHTML = '<option value="">No appointment times are configured for this test</option>';
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
document.getElementById('test_date').min = [today.getFullYear(), String(today.getMonth() + 1).padStart(2, '0'), String(today.getDate()).padStart(2, '0')].join('-');
document.getElementById('test_date').addEventListener('change', updateTimes);
</script>
</body>
</html>
