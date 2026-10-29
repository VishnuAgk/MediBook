<?php
// MediBook catalog restore script.
// This restores doctors, services and appointment slots in the LOCAL medibook database.
// It does NOT delete users or appointments.

session_start();
require_once __DIR__ . '/db.php';

if (!extension_loaded('mongodb')) {
    die('MongoDB extension is not enabled.');
}

global $mongo;

function clearCollection(string $collection): void {
    global $mongo;
    $cmd = new MongoDB\Driver\Command([
        'delete' => $collection,
        'deletes' => [
            ['q' => [], 'limit' => 0]
        ]
    ]);
    $mongo->executeCommand('medibook', $cmd);
}

function insertMany(string $collection, array $documents): int {
    global $mongo;
    $bulk = new MongoDB\Driver\BulkWrite();
    foreach ($documents as $doc) {
        if (!isset($doc['_id'])) {
            $doc['_id'] = bin2hex(random_bytes(12));
        }
        $bulk->insert($doc);
    }
    $result = $mongo->executeBulkWrite('medibook.' . $collection, $bulk);
    return $result->getInsertedCount();
}

$doctors = [
        ['id'=>'D001','name'=>'Dr. Arun Raj','qualification'=>'MBBS, MD (Cardiology)','specialty'=>'Clinical Cardiology','category'=>'Cardiology'],
        ['id'=>'D002','name'=>'Dr. Meera Nair','qualification'=>'MBBS, MD, DM','specialty'=>'Interventional Cardiology','category'=>'Cardiology'],
        ['id'=>'D003','name'=>'Dr. Vivek Menon','qualification'=>'MBBS, MD (Cardiology)','specialty'=>'Clinical Cardiology','category'=>'Cardiology'],
        ['id'=>'D004','name'=>'Dr. Anjali Kumar','qualification'=>'MBBS, MD (Cardiology)','specialty'=>'Preventive Cardiology','category'=>'Cardiology'],
        ['id'=>'D005','name'=>'Dr. Rahul Das','qualification'=>'MBBS, DM','specialty'=>'Heart & Vascular Care','category'=>'Cardiology'],
        ['id'=>'D006','name'=>'Dr. Suresh Nair','qualification'=>'MBBS, MD (Neurology)','specialty'=>'Stroke & Neuro Care','category'=>'Neurology'],
        ['id'=>'D007','name'=>'Dr. Priya Menon','qualification'=>'MBBS, MD, DM','specialty'=>'Epilepsy & Seizure Care','category'=>'Neurology'],
        ['id'=>'D008','name'=>'Dr. Kiran Thomas','qualification'=>'MBBS, MD','specialty'=>'Movement Disorders','category'=>'Neurology'],
        ['id'=>'D009','name'=>'Dr. Neha Varma','qualification'=>'MBBS, MD','specialty'=>'Headache & Migraine','category'=>'Neurology'],
        ['id'=>'D010','name'=>'Dr. Ajith Kumar','qualification'=>'MBBS, DM','specialty'=>'Neuro Critical Care','category'=>'Neurology'],
        ['id'=>'D011','name'=>'Dr. Ramesh Babu','qualification'=>'MBBS, MS (Orthopaedics)','specialty'=>'Joint Replacement','category'=>'Orthopaedics'],
        ['id'=>'D012','name'=>'Dr. Deepa Krishnan','qualification'=>'MBBS, MS','specialty'=>'Sports Injury & Arthroscopy','category'=>'Orthopaedics'],
        ['id'=>'D013','name'=>'Dr. Nikhil Joseph','qualification'=>'MBBS, DNB','specialty'=>'Spine & Back Care','category'=>'Orthopaedics'],
        ['id'=>'D014','name'=>'Dr. Swathi Menon','qualification'=>'MBBS, MS','specialty'=>'Paediatric Orthopaedics','category'=>'Orthopaedics'],
        ['id'=>'D015','name'=>'Dr. Hari Prasad','qualification'=>'MBBS, MS','specialty'=>'Trauma & Fracture Care','category'=>'Orthopaedics'],
        ['id'=>'D016','name'=>'Dr. Sanjay Rao','qualification'=>'MBBS, MD (Gastroenterology)','specialty'=>'Digestive Disorders','category'=>'Gastroenterology'],
        ['id'=>'D017','name'=>'Dr. Lakshmi Nair','qualification'=>'MBBS, MD, DM','specialty'=>'Liver & Biliary Care','category'=>'Gastroenterology'],
        ['id'=>'D018','name'=>'Dr. Joseph Mathew','qualification'=>'MBBS, MD','specialty'=>'Inflammatory Bowel Disease','category'=>'Gastroenterology'],
        ['id'=>'D019','name'=>'Dr. Aparna Suresh','qualification'=>'MBBS, MD','specialty'=>'Endoscopy & Digestive Care','category'=>'Gastroenterology'],
        ['id'=>'D020','name'=>'Dr. Vinod Kumar','qualification'=>'MBBS, DM','specialty'=>'Pancreas & GI Care','category'=>'Gastroenterology'],
        ['id'=>'D021','name'=>'Dr. Anu Thomas','qualification'=>'MBBS, MD (Dermatology)','specialty'=>'Clinical Dermatology','category'=>'Dermatology'],
        ['id'=>'D022','name'=>'Dr. Rahul Menon','qualification'=>'MBBS, MD','specialty'=>'Acne & Scar Treatment','category'=>'Dermatology'],
        ['id'=>'D023','name'=>'Dr. Fathima Ali','qualification'=>'MBBS, MD','specialty'=>'Hair & Scalp Disorders','category'=>'Dermatology'],
        ['id'=>'D024','name'=>'Dr. Kavya Nair','qualification'=>'MBBS, MD','specialty'=>'Cosmetic Dermatology','category'=>'Dermatology'],
        ['id'=>'D025','name'=>'Dr. Manoj Kumar','qualification'=>'MBBS, MD','specialty'=>'Skin Allergy & Immunology','category'=>'Dermatology'],
        ['id'=>'D026','name'=>'Dr. Rekha Menon','qualification'=>'MBBS, MD (Obstetrics & Gynaecology)','specialty'=>'High-Risk Pregnancy','category'=>'Obstetrics & Gynaecology'],
        ['id'=>'D027','name'=>'Dr. Sneha Raj','qualification'=>'MBBS, MD','specialty'=>'Women\'s Health','category'=>'Obstetrics & Gynaecology'],
        ['id'=>'D028','name'=>'Dr. Latha Nair','qualification'=>'MBBS, DGO','specialty'=>'Infertility & Reproductive Care','category'=>'Obstetrics & Gynaecology'],
        ['id'=>'D029','name'=>'Dr. Divya Thomas','qualification'=>'MBBS, MS','specialty'=>'Gynaecological Surgery','category'=>'Obstetrics & Gynaecology'],
        ['id'=>'D030','name'=>'Dr. Reshma Kumar','qualification'=>'MBBS, MD','specialty'=>'Maternal & Fetal Medicine','category'=>'Obstetrics & Gynaecology'],
        ['id'=>'D031','name'=>'Dr. Arunima Nair','qualification'=>'MBBS, MD (Paediatrics)','specialty'=>'General Paediatrics','category'=>'Paediatrics'],
        ['id'=>'D032','name'=>'Dr. Vivek Thomas','qualification'=>'MBBS, MD','specialty'=>'Paediatric Allergy','category'=>'Paediatrics'],
        ['id'=>'D033','name'=>'Dr. Meenu Joseph','qualification'=>'MBBS, DNB','specialty'=>'Child Nutrition & Growth','category'=>'Paediatrics'],
        ['id'=>'D034','name'=>'Dr. Riya Menon','qualification'=>'MBBS, MD','specialty'=>'Paediatric Respiratory Care','category'=>'Paediatrics'],
        ['id'=>'D035','name'=>'Dr. Abhishek Raj','qualification'=>'MBBS, MD','specialty'=>'Neonatal & Child Care','category'=>'Paediatrics'],
        ['id'=>'D036','name'=>'Dr. Ajay Nair','qualification'=>'MBBS, MS (ENT)','specialty'=>'Ear & Hearing Care','category'=>'ENT'],
        ['id'=>'D037','name'=>'Dr. Shilpa Menon','qualification'=>'MBBS, DLO','specialty'=>'Nose & Sinus Care','category'=>'ENT'],
        ['id'=>'D038','name'=>'Dr. Mathew George','qualification'=>'MBBS, MS','specialty'=>'Throat & Voice Disorders','category'=>'ENT'],
        ['id'=>'D039','name'=>'Dr. Priyanka Das','qualification'=>'MBBS, MS','specialty'=>'Head & Neck Surgery','category'=>'ENT'],
        ['id'=>'D040','name'=>'Dr. Rakesh Kumar','qualification'=>'MBBS, DNB','specialty'=>'Paediatric ENT','category'=>'ENT'],
        ['id'=>'D041','name'=>'Dr. Sandeep Nair','qualification'=>'MBBS, MD (Pulmonology)','specialty'=>'Respiratory Medicine','category'=>'Pulmonology'],
        ['id'=>'D042','name'=>'Dr. Asha Menon','qualification'=>'MBBS, MD','specialty'=>'Asthma & Allergy','category'=>'Pulmonology'],
        ['id'=>'D043','name'=>'Dr. George Thomas','qualification'=>'MBBS, DNB','specialty'=>'Sleep Medicine','category'=>'Pulmonology'],
        ['id'=>'D044','name'=>'Dr. Nandita Raj','qualification'=>'MBBS, MD','specialty'=>'COPD & Lung Care','category'=>'Pulmonology'],
        ['id'=>'D045','name'=>'Dr. Mahesh Kumar','qualification'=>'MBBS, MD','specialty'=>'Critical Respiratory Care','category'=>'Pulmonology'],
        ['id'=>'D046','name'=>'Dr. Naveen Nair','qualification'=>'MBBS, MS (Urology)','specialty'=>'General Urology','category'=>'Urology'],
        ['id'=>'D047','name'=>'Dr. Smitha Menon','qualification'=>'MBBS, MS','specialty'=>'Kidney Stone Care','category'=>'Urology'],
        ['id'=>'D048','name'=>'Dr. Jose Mathew','qualification'=>'MBBS, DNB','specialty'=>'Uro-Oncology','category'=>'Urology'],
        ['id'=>'D049','name'=>'Dr. Aditi Kumar','qualification'=>'MBBS, MS','specialty'=>'Female Urology','category'=>'Urology'],
        ['id'=>'D050','name'=>'Dr. Rajeev Das','qualification'=>'MBBS, MS','specialty'=>'Men\'s Health & Urology','category'=>'Urology']
];

$services = [
        ['id'=>'S001','name'=>'MRI Brain','category'=>'MRI & Imaging','description'=>'Detailed MRI imaging of the brain.','duration'=>'45 minutes'],
        ['id'=>'S002','name'=>'MRI Spine','category'=>'MRI & Imaging','description'=>'MRI imaging for cervical, dorsal and lumbar spine.','duration'=>'60 minutes'],
        ['id'=>'S003','name'=>'CT Scan Brain','category'=>'CT & Imaging','description'=>'CT imaging of the brain for diagnostic evaluation.','duration'=>'20 minutes'],
        ['id'=>'S004','name'=>'CT Scan Chest','category'=>'CT & Imaging','description'=>'CT imaging of the chest and lungs.','duration'=>'20 minutes'],
        ['id'=>'S005','name'=>'Ultrasound Abdomen','category'=>'Ultrasound & X-Ray','description'=>'Ultrasound examination of abdominal organs.','duration'=>'30 minutes'],
        ['id'=>'S006','name'=>'Digital X-Ray','category'=>'Ultrasound & X-Ray','description'=>'Digital X-ray imaging for routine diagnostic needs.','duration'=>'15 minutes'],
        ['id'=>'S007','name'=>'Complete Blood Count (CBC)','category'=>'Blood Tests','description'=>'Routine blood count and cell analysis.','duration'=>'15 minutes'],
        ['id'=>'S008','name'=>'Blood Sugar & HbA1c','category'=>'Blood Tests','description'=>'Blood glucose and long-term sugar assessment.','duration'=>'15 minutes'],
        ['id'=>'S009','name'=>'Lipid Profile','category'=>'Blood Tests','description'=>'Cholesterol and triglyceride assessment.','duration'=>'15 minutes'],
        ['id'=>'S010','name'=>'Liver Function Test (LFT)','category'=>'Organ Function Tests','description'=>'Blood tests to assess liver function.','duration'=>'20 minutes'],
        ['id'=>'S011','name'=>'Kidney Function Test (KFT)','category'=>'Organ Function Tests','description'=>'Blood tests to assess kidney function.','duration'=>'20 minutes'],
        ['id'=>'S012','name'=>'Thyroid Profile','category'=>'Hormone & Vitamin Tests','description'=>'Thyroid hormone assessment.','duration'=>'20 minutes'],
        ['id'=>'S013','name'=>'Vitamin D & B12','category'=>'Hormone & Vitamin Tests','description'=>'Vitamin D and B12 level assessment.','duration'=>'20 minutes'],
        ['id'=>'S014','name'=>'ECG','category'=>'Cardiac Tests','description'=>'Electrocardiogram for heart rhythm assessment.','duration'=>'15 minutes'],
        ['id'=>'S015','name'=>'Full Body Health Checkup','category'=>'Health Checkups','description'=>'Comprehensive preventive health screening package.','duration'=>'120 minutes'],
        ['id'=>'S016','name'=>'Executive Health Checkup','category'=>'Health Checkups','description'=>'Extended preventive health assessment package.','duration'=>'150 minutes']
];

// Replace only catalog/slot data.
// Existing users and appointments are preserved.
clearCollection('doctors');
clearCollection('services');
clearCollection('appointment_slots');

$doctorDocs = [];
foreach ($doctors as $d) {
    $doctorDocs[] = $d;
}

$serviceDocs = [];
foreach ($services as $s) {
    $serviceDocs[] = $s;
}

$doctorCount = insertMany('doctors', $doctorDocs);
$serviceCount = insertMany('services', $serviceDocs);

// Create doctor slots: 10:00 AM to 5:00 PM, every 30 minutes.
// Only 10:00 AM and 2:00 PM are online-token windows.
// All other windows are Offline Visitor.
$times = [];
for ($minutes = 10 * 60; $minutes <= 17 * 60; $minutes += 30) {
    $h = intdiv($minutes, 60);
    $m = $minutes % 60;
    $ampm = $h >= 12 ? 'PM' : 'AM';
    $displayH = $h % 12;
    if ($displayH === 0) $displayH = 12;
    $times[] = sprintf('%02d:%02d %s', $displayH, $m, $ampm);
}

$slots = [];
$token = 1;

foreach ($doctors as $d) {
    foreach ($times as $time) {
        $online = ($time === '10:00 AM' || $time === '02:00 PM');

        $slots[] = [
            '_id' => 'slot_' . $d['id'] . '_' . str_replace([':', ' '], ['', '_'], $time),
            'provider_type' => 'doctor',
            'provider_id' => $d['id'],
            'provider_name' => $d['name'],
            'category' => $d['category'],
            'slot_time' => $time,
            'slot_status' => $online ? 'Available' : 'Offline Visitor',
            'token_no' => $online ? $token++ : null
        ];
    }
}

// Service slots use the same 30-minute schedule.
foreach ($services as $s) {
    foreach ($times as $time) {
        $slots[] = [
            '_id' => 'slot_' . $s['id'] . '_' . str_replace([':', ' '], ['', '_'], $time),
            'provider_type' => 'service',
            'provider_id' => $s['id'],
            'provider_name' => $s['name'],
            'category' => $s['category'],
            'slot_time' => $time,
            'slot_status' => 'Available',
            'token_no' => null
        ];
    }
}

$slotCount = insertMany('appointment_slots', $slots);

echo '<!doctype html><html><head><meta charset="utf-8">';
echo '<title>MediBook Data Restored</title>';
echo '<style>
body{font-family:Arial,sans-serif;background:#eefaf8;padding:40px;color:#16324f}
.card{max-width:650px;margin:auto;background:white;border-radius:22px;padding:35px;box-shadow:0 15px 45px rgba(20,70,80,.12)}
h1{color:#087f78} li{margin:12px 0} .btn{display:inline-block;margin-top:20px;padding:13px 20px;background:#0b9d93;color:white;text-decoration:none;border-radius:12px;font-weight:700}
</style></head><body><div class="card">';
echo '<h1>✓ MediBook data restored</h1>';
echo '<p>The missing catalog data has been added to your local <b>medibook</b> database.</p>';
echo '<ul>';
echo '<li><b>' . $doctorCount . '</b> doctors added</li>';
echo '<li><b>' . count(array_unique(array_column($doctors, 'category'))) . '</b> departments added</li>';
echo '<li><b>' . $serviceCount . '</b> services added</li>';
echo '<li><b>' . $slotCount . '</b> appointment slots added</li>';
echo '<li><b>100</b> online doctor tokens created</li>';
echo '</ul>';
echo '<a class="btn" href="index.php">Open MediBook</a> ';
echo '<a class="btn" href="consult_doctor.php">Consult Doctor</a>';
echo '</div></body></html>';
?>
