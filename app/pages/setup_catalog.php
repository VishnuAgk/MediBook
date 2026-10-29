<?php
session_start();
require_once dirname(__DIR__, 2) . '/db.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Access denied. Sign in with an administrator account.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Use the setup button in the Admin page.');
}

$postedToken = $_POST['csrf_token'] ?? '';
$sessionToken = $_SESSION['csrf_token'] ?? '';
if ($sessionToken === '' || !hash_equals($sessionToken, $postedToken)) {
    http_response_code(400);
    exit('The setup form expired. Return to Admin and try again.');
}

$setupError = null;
try {
    // These existing setup scripts are kept as the single source of catalog
    // and slot definitions. Buffer their standalone page output for this view.
    ob_start();
    require dirname(__DIR__) . '/scripts/create_catalog.php';
    ob_end_clean();

    ob_start();
    require dirname(__DIR__) . '/scripts/create_slots.php';
    ob_end_clean();
} catch (Throwable $error) {
    while (ob_get_level() > 0) ob_end_clean();
    $setupError = $error->getMessage();
}

if ($setupError === null) {
    $doctorCount = count(mongoFind('doctors'));
    $serviceCount = count(mongoFind('services'));
    $doctorSlotCount = count(mongoFind('appointment_slots', ['provider_type' => 'doctor']));
    $serviceSlotCount = count(mongoFind('appointment_slots', ['provider_type' => 'service']));
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediBook Data Setup</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="soft-page">
<main class="section">
    <section class="booking-panel" style="display:block;max-width:760px;margin:60px auto">
        <?php if ($setupError !== null): ?>
            <p class="eyebrow">DATABASE SETUP</p>
            <h1>Setup could not finish</h1>
            <p><?php echo htmlspecialchars($setupError, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php else: ?>
            <p class="eyebrow">DATABASE SETUP COMPLETE</p>
            <h1>MediBook is ready</h1>
            <p>The catalog and booking slots are ready to use.</p>
            <ul>
                <li><?php echo $doctorCount; ?> doctors</li>
                <li><?php echo $serviceCount; ?> scans and tests</li>
                <li><?php echo $doctorSlotCount; ?> doctor appointment slots</li>
                <li><?php echo $serviceSlotCount; ?> scan and test appointment slots</li>
            </ul>
        <?php endif; ?>
        <p><a class="btn-primary" href="admin.php">Return to Admin</a></p>
    </section>
</main>
</body>
</html>
