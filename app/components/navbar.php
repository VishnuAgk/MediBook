<?php

$activeNav = $activeNav ?? '';
$userRole = $_SESSION['user']['role'] ?? '';
$navigationItems = [
    'home' => ['label' => 'Home', 'href' => 'index.php'],
    'doctors' => ['label' => 'Consult Doctor', 'href' => 'consult_doctor.php'],
    'scanning' => ['label' => 'Scanning', 'href' => 'scanning.php'],
    'testing' => ['label' => 'Testing', 'href' => 'testing.php'],
    'appointments' => ['label' => 'My Appointments', 'href' => 'mybookings.php'],
];

if ($userRole === 'admin') {
    $navigationItems['admin'] = ['label' => 'Admin', 'href' => 'admin.php'];
}
?>
<header class="navbar">
    <a class="logo" href="index.php">🩺 <span>Medi</span>Book</a>
    <nav aria-label="Main navigation">
        <?php foreach ($navigationItems as $key => $item): ?>
            <a href="<?php echo htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8'); ?>"<?php echo $activeNav === $key ? ' class="active" aria-current="page"' : ''; ?>><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></a>
        <?php endforeach; ?>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
