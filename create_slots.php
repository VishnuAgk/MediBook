<?php
require_once __DIR__ . '/db.php';

// 10:00 AM through 5:00 PM at 30-minute intervals = 15 windows per doctor.
$slotTimes = [];
$start = new DateTime('10:00 AM');
$end = new DateTime('05:00 PM');
while ($start <= $end) {
    $slotTimes[] = $start->format('h:i A');
    $start->modify('+30 minutes');
}

$doctors = mongoFind('doctors', [], ['sort'=>['category'=>1,'name'=>1]]);
if (!$doctors) {
    die('No doctors found. Run create_catalog.php first.');
}

// Two online token windows per doctor; the total adjusts with the doctor catalog.
// Every other 30-minute window is reserved for offline visitors.
$onlineTimes = ['10:00 AM','02:00 PM'];

$created = 0;
$updated = 0;
$tokenNo = 1;
foreach ($doctors as $doctor) {
    foreach ($slotTimes as $slotTime) {
        $isOnline = in_array($slotTime, $onlineTimes, true);
        $assignedToken = $isOnline ? $tokenNo++ : null;
        $doc = [
            'provider_type' => 'doctor',
            'provider_id' => $doctor['id'],
            'doctor_id' => $doctor['id'],
            'doctor' => $doctor['name'],
            'category' => $doctor['category'],
            'slot_time' => $slotTime,
            'slot_status' => $isOnline ? 'Available' : 'Offline Visit',
            'token_type' => $isOnline ? 'Online' : 'Offline',
            'token_no' => $assignedToken,
            'capacity' => 1,
        ];
        $slotFilter = [
            'provider_type' => 'doctor',
            'slot_time' => $slotTime,
            '$or' => [
                ['provider_id' => $doctor['id']],
                ['doctor_id' => $doctor['id']],
            ],
        ];
        $existing = mongoFindOne('appointment_slots', $slotFilter);
        if ($existing !== null) {
            // Older slot records may not have an application-level id. Add one
            // while preserving an administrator's current availability choice.
            $doc['id'] = $existing['id'] ?? bin2hex(random_bytes(12));
            $doc['slot_status'] = $existing['slot_status'] ?? $doc['slot_status'];
            mongoUpdate('appointment_slots', $slotFilter, ['$set'=>$doc]);
            $updated++;
        } else {
            $doc['id'] = bin2hex(random_bytes(12));
            mongoInsert('appointment_slots', $doc);
            $created++;
        }
    }
}

$totalWindows = count($doctors) * count($slotTimes);
$onlineWindows = count($doctors) * count($onlineTimes);
$offlineWindows = $totalWindows - $onlineWindows;

echo '<h2>Appointment slots setup complete!</h2>';
echo '<p>Doctors: '.count($doctors).'</p>';
echo '<p>Time range: 10:00 AM – 5:00 PM, every 30 minutes</p>';
echo '<p>Time windows per doctor: '.count($slotTimes).'</p>';
echo '<p>Total doctor time windows: '.$totalWindows.'</p>';
echo '<p>Offline visitor windows: '.$offlineWindows.'</p>';
echo '<p><strong>Total online tokens: '.$onlineWindows.'</strong></p>';
echo '<p>New doctor slot documents created: '.$created.'</p>';
echo '<p>Existing doctor slot documents refreshed: '.$updated.'</p>';

// Diagnostic and health-check services use the same 30-minute booking windows.
// These are service appointments, not part of the 100 doctor-consultation tokens.
$services = mongoFind('services', [], ['sort'=>['category'=>1,'name'=>1]]);
$serviceCreated = 0;
foreach ($services as $service) {
    foreach ($slotTimes as $slotTime) {
        $existing = mongoFindOne('appointment_slots', [
            'provider_type' => 'service',
            'provider_id' => $service['id'],
            'slot_time' => $slotTime,
        ]);
        if ($existing !== null) {
            if (empty($existing['id'])) {
                mongoUpdate('appointment_slots', [
                    'provider_type' => 'service',
                    'provider_id' => $service['id'],
                    'slot_time' => $slotTime,
                ], ['$set' => ['id' => bin2hex(random_bytes(12))]]);
            }
            continue;
        }
        mongoInsert('appointment_slots', [
            'id' => bin2hex(random_bytes(12)),
            'provider_type' => 'service',
            'provider_id' => $service['id'],
            'provider_name' => $service['name'],
            'category' => $service['category'],
            'slot_time' => $slotTime,
            'slot_status' => 'Available',
            'token_type' => 'Service',
            'capacity' => 1,
        ]);
        $serviceCreated++;
    }
}

echo '<p>Online token times per doctor: '.implode(', ', $onlineTimes).'</p>';
echo '<p>Online doctor tokens: '.$onlineWindows.' total (reset each day)</p>';
 echo '<p>Online token windows per doctor: '.implode(', ', $onlineTimes).'</p>';
echo '<p><a href="index.php">Return to MediBook</a></p>';
