<?php
require_once __DIR__ . '/db.php';

// MediBook master data: multiple doctors in each department + diagnostic/health-check services.
$doctors = [
    ['id'=>'D001','name'=>'Dr. Arun Raj','qualification'=>'MBBS, MD (Cardiology)','specialty'=>'Cardiology','category'=>'Cardiology'],
    ['id'=>'D002','name'=>'Dr. Meera Nair','qualification'=>'MBBS, MD, DM (Cardiology)','specialty'=>'Interventional Cardiology','category'=>'Cardiology'],
    ['id'=>'D003','name'=>'Dr. Vivek Menon','qualification'=>'MBBS, MD (Cardiology)','specialty'=>'Clinical Cardiology','category'=>'Cardiology'],
    ['id'=>'D004','name'=>'Dr. Anjali Kumar','qualification'=>'MBBS, MD (Cardiology)','specialty'=>'Preventive Cardiology','category'=>'Cardiology'],
    ['id'=>'D005','name'=>'Dr. Rahul Das','qualification'=>'MBBS, DM (Cardiology)','specialty'=>'Heart & Vascular Care','category'=>'Cardiology'],
    ['id'=>'D006','name'=>'Dr. Vivek Menon','qualification'=>'MBBS, MD, DM (Neurology)','specialty'=>'Neurology','category'=>'Neurology'],
    ['id'=>'D007','name'=>'Dr. Anjali Kumar','qualification'=>'MBBS, MD, DM (Neurology)','specialty'=>'Clinical Neurology','category'=>'Neurology'],
    ['id'=>'D008','name'=>'Dr. Nikhil Varma','qualification'=>'MBBS, MD (Neurology)','specialty'=>'Stroke & Neurology','category'=>'Neurology'],
    ['id'=>'D009','name'=>'Dr. Riya George','qualification'=>'MBBS, MD (Neurology)','specialty'=>'Movement Disorders','category'=>'Neurology'],
    ['id'=>'D010','name'=>'Dr. Sanjay Kumar','qualification'=>'MBBS, DM (Neurology)','specialty'=>'Epilepsy & Neurology','category'=>'Neurology'],
    ['id'=>'D011','name'=>'Dr. Rahul Das','qualification'=>'MBBS, MS (Orthopaedics)','specialty'=>'Orthopaedics','category'=>'Orthopaedics'],
    ['id'=>'D012','name'=>'Dr. Sneha Joseph','qualification'=>'MBBS, MS, DNB (Orthopaedics)','specialty'=>'Joint & Sports Medicine','category'=>'Orthopaedics'],
    ['id'=>'D013','name'=>'Dr. Kiran Babu','qualification'=>'MBBS, MS (Orthopaedics)','specialty'=>'Trauma & Joint Care','category'=>'Orthopaedics'],
    ['id'=>'D014','name'=>'Dr. Manoj Krishnan','qualification'=>'MBBS, MS (Orthopaedics)','specialty'=>'Spine & Orthopaedics','category'=>'Orthopaedics'],
    ['id'=>'D015','name'=>'Dr. Divya Suresh','qualification'=>'MBBS, DNB (Orthopaedics)','specialty'=>'Sports Injury & Rehabilitation','category'=>'Orthopaedics'],
    ['id'=>'D016','name'=>'Dr. Javed P','qualification'=>'MBBS, MD, DM (Gastroenterology)','specialty'=>'Gastroenterology','category'=>'Gastroenterology'],
    ['id'=>'D017','name'=>'Dr. Farah Ali','qualification'=>'MBBS, MD, DM (Gastroenterology)','specialty'=>'Hepatology & Gastroenterology','category'=>'Gastroenterology'],
    ['id'=>'D018','name'=>'Dr. Priya Menon','qualification'=>'MBBS, MD, DM (Gastroenterology)','specialty'=>'Digestive Diseases','category'=>'Gastroenterology'],
    ['id'=>'D019','name'=>'Dr. Fathima Salim','qualification'=>'MBBS, MD, DM (Gastroenterology)','specialty'=>'Liver & Gastrointestinal Care','category'=>'Gastroenterology'],
    ['id'=>'D020','name'=>'Dr. Arjun Nair','qualification'=>'MBBS, MD, DM (Gastroenterology)','specialty'=>'Advanced Gastroenterology','category'=>'Gastroenterology'],
    ['id'=>'D021','name'=>'Dr. Anju V S','qualification'=>'MBBS, MD (Dermatology & Venereology), DNB','specialty'=>'Dermatology','category'=>'Dermatology'],
    ['id'=>'D022','name'=>'Dr. Neha Thomas','qualification'=>'MBBS, MD (Dermatology)','specialty'=>'Clinical Dermatology','category'=>'Dermatology'],
    ['id'=>'D023','name'=>'Dr. Asha Mathew','qualification'=>'MBBS, MD (Dermatology)','specialty'=>'Cosmetic Dermatology','category'=>'Dermatology'],
    ['id'=>'D024','name'=>'Dr. Lakshmi Nair','qualification'=>'MBBS, MD (Dermatology)','specialty'=>'Skin & Hair Disorders','category'=>'Dermatology'],
    ['id'=>'D025','name'=>'Dr. Fathima Rahman','qualification'=>'MBBS, DNB (Dermatology)','specialty'=>'Paediatric & Clinical Dermatology','category'=>'Dermatology'],
    ['id'=>'D026','name'=>'Dr. Priya Menon','qualification'=>'MBBS, MD (Obstetrics & Gynaecology)','specialty'=>'Obstetrics & Gynaecology','category'=>'Obstetrics & Gynaecology'],
    ['id'=>'D027','name'=>'Dr. Fathima Salim','qualification'=>'MBBS, MS (Obstetrics & Gynaecology)','specialty'=>'Women’s Health','category'=>'Obstetrics & Gynaecology'],
    ['id'=>'D028','name'=>'Dr. Anjana Nair','qualification'=>'MBBS, MD (Obstetrics & Gynaecology)','specialty'=>'High-Risk Pregnancy','category'=>'Obstetrics & Gynaecology'],
    ['id'=>'D029','name'=>'Dr. Maya Joseph','qualification'=>'MBBS, DNB (Obstetrics & Gynaecology)','specialty'=>'Gynaecology','category'=>'Obstetrics & Gynaecology'],
    ['id'=>'D030','name'=>'Dr. Reshma Varma','qualification'=>'MBBS, MD (Obstetrics & Gynaecology)','specialty'=>'Maternal Health','category'=>'Obstetrics & Gynaecology'],
    ['id'=>'D031','name'=>'Dr. Kiran Babu','qualification'=>'MBBS, MD (Paediatrics)','specialty'=>'Paediatrics','category'=>'Paediatrics'],
    ['id'=>'D032','name'=>'Dr. Asha Mathew','qualification'=>'MBBS, DNB (Paediatrics)','specialty'=>'Child Health','category'=>'Paediatrics'],
    ['id'=>'D033','name'=>'Dr. Neeraj Kumar','qualification'=>'MBBS, MD (Paediatrics)','specialty'=>'Paediatric Medicine','category'=>'Paediatrics'],
    ['id'=>'D034','name'=>'Dr. Maria Thomas','qualification'=>'MBBS, DNB (Paediatrics)','specialty'=>'Child Development','category'=>'Paediatrics'],
    ['id'=>'D035','name'=>'Dr. Adithya Menon','qualification'=>'MBBS, MD (Paediatrics)','specialty'=>'Neonatal & Child Care','category'=>'Paediatrics'],
    ['id'=>'D036','name'=>'Dr. Nikhil Varma','qualification'=>'MBBS, MS (ENT)','specialty'=>'ENT & Head-Neck','category'=>'ENT'],
    ['id'=>'D037','name'=>'Dr. Riya George','qualification'=>'MBBS, DLO, DNB (ENT)','specialty'=>'ENT & Audiology','category'=>'ENT'],
    ['id'=>'D038','name'=>'Dr. Arun Joseph','qualification'=>'MBBS, MS (ENT)','specialty'=>'Otology','category'=>'ENT'],
    ['id'=>'D039','name'=>'Dr. Meera Thomas','qualification'=>'MBBS, DNB (ENT)','specialty'=>'Rhinology & Sinus Care','category'=>'ENT'],
    ['id'=>'D040','name'=>'Dr. Kevin Mathew','qualification'=>'MBBS, MS (ENT)','specialty'=>'Head & Neck ENT','category'=>'ENT'],
    ['id'=>'D041','name'=>'Dr. Sanjay Kumar','qualification'=>'MBBS, MD (Pulmonary Medicine)','specialty'=>'Pulmonology','category'=>'Pulmonology'],
    ['id'=>'D042','name'=>'Dr. Lakshmi Nair','qualification'=>'MBBS, DNB (Respiratory Medicine)','specialty'=>'Respiratory Medicine','category'=>'Pulmonology'],
    ['id'=>'D043','name'=>'Dr. Joseph Paul','qualification'=>'MBBS, MD (Pulmonary Medicine)','specialty'=>'Asthma & Allergy','category'=>'Pulmonology'],
    ['id'=>'D044','name'=>'Dr. Anoop Menon','qualification'=>'MBBS, DNB (Respiratory Medicine)','specialty'=>'Sleep & Respiratory Care','category'=>'Pulmonology'],
    ['id'=>'D045','name'=>'Dr. Sreeram Nair','qualification'=>'MBBS, MD (Pulmonary Medicine)','specialty'=>'Critical Respiratory Care','category'=>'Pulmonology'],
    ['id'=>'D046','name'=>'Dr. Manoj Krishnan','qualification'=>'MBBS, MS, DNB (Urology)','specialty'=>'Urology','category'=>'Urology'],
    ['id'=>'D047','name'=>'Dr. Divya Suresh','qualification'=>'MBBS, MS (Urology)','specialty'=>'Urology & Andrology','category'=>'Urology'],
    ['id'=>'D048','name'=>'Dr. Ajay Varma','qualification'=>'MBBS, MS, MCh (Urology)','specialty'=>'Endourology','category'=>'Urology'],
    ['id'=>'D049','name'=>'Dr. Harish Nair','qualification'=>'MBBS, MS (Urology)','specialty'=>'Kidney & Urinary Care','category'=>'Urology'],
    ['id'=>'D050','name'=>'Dr. Nandana Joseph','qualification'=>'MBBS, DNB (Urology)','specialty'=>'Urology & Women’s Urology','category'=>'Urology'],
    ['id'=>'D051','name'=>'Dr. Aarya Menon','qualification'=>'MBBS, MD (Cardiology)','specialty'=>'Heart Failure Care','category'=>'Cardiology'],
    ['id'=>'D052','name'=>'Dr. Nitin Varghese','qualification'=>'MBBS, DM (Cardiology)','specialty'=>'Cardiac Electrophysiology','category'=>'Cardiology'],
    ['id'=>'D053','name'=>'Dr. Devika Rao','qualification'=>'MBBS, MD, DM (Neurology)','specialty'=>'Neurovascular Care','category'=>'Neurology'],
    ['id'=>'D054','name'=>'Dr. Alan Mathew','qualification'=>'MBBS, MD (Neurology)','specialty'=>'Memory & Cognitive Disorders','category'=>'Neurology'],
    ['id'=>'D055','name'=>'Dr. Sreeja Nair','qualification'=>'MBBS, MS (Orthopaedics)','specialty'=>'Joint Replacement','category'=>'Orthopaedics'],
    ['id'=>'D056','name'=>'Dr. Vivek Thomas','qualification'=>'MBBS, MS (Orthopaedics)','specialty'=>'Spine Surgery','category'=>'Orthopaedics'],
    ['id'=>'D057','name'=>'Dr. Nisha Krishnan','qualification'=>'MBBS, MD, DM (Gastroenterology)','specialty'=>'Advanced Endoscopy','category'=>'Gastroenterology'],
    ['id'=>'D058','name'=>'Dr. Rahul Varma','qualification'=>'MBBS, MD (Gastroenterology)','specialty'=>'Pancreatic & Biliary Care','category'=>'Gastroenterology'],
    ['id'=>'D059','name'=>'Dr. Meera Das','qualification'=>'MBBS, MD (Dermatology)','specialty'=>'Psoriasis & Clinical Dermatology','category'=>'Dermatology'],
    ['id'=>'D060','name'=>'Dr. Arunima Paul','qualification'=>'MBBS, MD (Dermatology)','specialty'=>'Dermatologic Surgery','category'=>'Dermatology'],
    ['id'=>'D061','name'=>'Dr. Lakshmi Mohan','qualification'=>'MBBS, MS (Obstetrics & Gynaecology)','specialty'=>'Maternal & Fetal Medicine','category'=>'Obstetrics & Gynaecology'],
    ['id'=>'D062','name'=>'Dr. Reena Joseph','qualification'=>'MBBS, MD (Obstetrics & Gynaecology)','specialty'=>'Gynaecological Endoscopy','category'=>'Obstetrics & Gynaecology'],
    ['id'=>'D063','name'=>'Dr. Karthik Nair','qualification'=>'MBBS, MD (Paediatrics)','specialty'=>'Paediatric Cardiology','category'=>'Paediatrics'],
    ['id'=>'D064','name'=>'Dr. Anjali Thomas','qualification'=>'MBBS, MD (Paediatrics)','specialty'=>'Paediatric Neurology','category'=>'Paediatrics'],
    ['id'=>'D065','name'=>'Dr. Joseph Mathew','qualification'=>'MBBS, MS (ENT)','specialty'=>'Otology & Hearing','category'=>'ENT'],
    ['id'=>'D066','name'=>'Dr. Nandita Menon','qualification'=>'MBBS, MS (ENT)','specialty'=>'Rhinology & Sinus Surgery','category'=>'ENT'],
    ['id'=>'D067','name'=>'Dr. Sandeep Kumar','qualification'=>'MBBS, MD (Pulmonary Medicine)','specialty'=>'Interventional Pulmonology','category'=>'Pulmonology'],
    ['id'=>'D068','name'=>'Dr. Fathima Nair','qualification'=>'MBBS, MD (Pulmonary Medicine)','specialty'=>'Sleep & Respiratory Medicine','category'=>'Pulmonology'],
    ['id'=>'D069','name'=>'Dr. Suresh Balan','qualification'=>'MBBS, MS (Urology)','specialty'=>'Robotic & Laparoscopic Urology','category'=>'Urology'],
    ['id'=>'D070','name'=>'Dr. Anitha Raj','qualification'=>'MBBS, MS (Urology)','specialty'=>'Female & Reconstructive Urology','category'=>'Urology'],
];

$services = [
    ['id'=>'S001','category'=>'MRI & Imaging','name'=>'MRI Brain','description'=>'Magnetic resonance imaging of the brain.','duration'=>'30 min','equipment'=>'1.5T MRI scanner'],
    ['id'=>'S002','category'=>'MRI & Imaging','name'=>'MRI Spine','description'=>'MRI examination of the spine.','duration'=>'30 min','equipment'=>'1.5T MRI scanner'],
    ['id'=>'S003','category'=>'CT & Imaging','name'=>'CT Scan Brain','description'=>'Computed tomography scan of the brain.','duration'=>'30 min','equipment'=>'128-slice CT scanner'],
    ['id'=>'S004','category'=>'CT & Imaging','name'=>'CT Scan Chest','description'=>'Computed tomography scan of the chest.','duration'=>'30 min','equipment'=>'128-slice CT scanner'],
    ['id'=>'S005','category'=>'Ultrasound & X-Ray','name'=>'Ultrasound Abdomen','description'=>'Ultrasound examination of the abdomen.','duration'=>'30 min','equipment'=>'Digital ultrasound system'],
    ['id'=>'S006','category'=>'Ultrasound & X-Ray','name'=>'Digital X-Ray','description'=>'Digital radiography appointment.','duration'=>'30 min','equipment'=>'Digital radiography unit'],
    ['id'=>'S007','category'=>'Blood Tests','name'=>'Complete Blood Count (CBC)','description'=>'Routine blood count test.','duration'=>'30 min'],
    ['id'=>'S008','category'=>'Blood Tests','name'=>'Blood Sugar & HbA1c','description'=>'Blood glucose and long-term sugar assessment.','duration'=>'30 min'],
    ['id'=>'S009','category'=>'Blood Tests','name'=>'Lipid Profile','description'=>'Cholesterol and triglyceride testing.','duration'=>'30 min'],
    ['id'=>'S010','category'=>'Organ Function Tests','name'=>'Liver Function Test (LFT)','description'=>'Blood tests used to assess liver function.','duration'=>'30 min'],
    ['id'=>'S011','category'=>'Organ Function Tests','name'=>'Kidney Function Test (KFT)','description'=>'Blood tests used to assess kidney function.','duration'=>'30 min'],
    ['id'=>'S012','category'=>'Hormone & Vitamin Tests','name'=>'Thyroid Profile','description'=>'TSH and thyroid-related blood testing.','duration'=>'30 min'],
    ['id'=>'S013','category'=>'Hormone & Vitamin Tests','name'=>'Vitamin D & B12','description'=>'Vitamin D and B12 testing.','duration'=>'30 min'],
    ['id'=>'S014','category'=>'Cardiac Tests','name'=>'ECG','description'=>'Electrocardiogram appointment.','duration'=>'30 min'],
    ['id'=>'S015','category'=>'Health Checkups','name'=>'Full Body Health Checkup','description'=>'Comprehensive preventive health screening package.','duration'=>'60 min'],
    ['id'=>'S016','category'=>'Health Checkups','name'=>'Executive Health Checkup','description'=>'Preventive screening package for adults.','duration'=>'60 min'],
    ['id'=>'S017','category'=>'MRI & Imaging','name'=>'MRI Knee','description'=>'Detailed magnetic resonance imaging of the knee joint.','duration'=>'30 min','equipment'=>'1.5T MRI scanner'],
    ['id'=>'S018','category'=>'MRI & Imaging','name'=>'MRI Shoulder','description'=>'Magnetic resonance imaging of the shoulder and surrounding soft tissues.','duration'=>'30 min','equipment'=>'1.5T MRI scanner'],
    ['id'=>'S019','category'=>'MRI & Imaging','name'=>'MRI Abdomen','description'=>'Cross-sectional magnetic resonance imaging of abdominal organs.','duration'=>'45 min','equipment'=>'1.5T MRI scanner'],
    ['id'=>'S020','category'=>'CT & Imaging','name'=>'CT Scan Sinuses','description'=>'Computed tomography imaging of the paranasal sinuses.','duration'=>'20 min','equipment'=>'128-slice CT scanner'],
    ['id'=>'S021','category'=>'CT & Imaging','name'=>'CT Scan Abdomen & Pelvis','description'=>'Computed tomography imaging of the abdomen and pelvis.','duration'=>'30 min','equipment'=>'128-slice CT scanner'],
    ['id'=>'S022','category'=>'CT & Imaging','name'=>'CT Pulmonary Angiogram','description'=>'Contrast-enhanced CT imaging of pulmonary blood vessels.','duration'=>'30 min','equipment'=>'128-slice CT scanner'],
    ['id'=>'S023','category'=>'Ultrasound & X-Ray','name'=>'Ultrasound Pelvis','description'=>'Ultrasound examination of pelvic organs.','duration'=>'30 min','equipment'=>'Digital ultrasound system'],
    ['id'=>'S024','category'=>'Ultrasound & X-Ray','name'=>'Color Doppler Ultrasound','description'=>'Doppler ultrasound assessment of blood flow.','duration'=>'30 min','equipment'=>'Color Doppler ultrasound system'],
    ['id'=>'S025','category'=>'Ultrasound & X-Ray','name'=>'Digital X-Ray Chest','description'=>'Digital chest radiograph for diagnostic assessment.','duration'=>'15 min','equipment'=>'DR digital X-ray system'],
    ['id'=>'S026','category'=>'Ultrasound & X-Ray','name'=>'Mammography','description'=>'Digital breast imaging for diagnostic assessment.','duration'=>'30 min','equipment'=>'Full-field digital mammography unit'],
    ['id'=>'S027','category'=>'Blood Tests','name'=>'Fasting Blood Sugar (FBS)','description'=>'Blood glucose measurement after fasting, as directed by your clinician.','duration'=>'15 min'],
    ['id'=>'S028','category'=>'Blood Tests','name'=>'Random Blood Sugar (RBS)','description'=>'Blood glucose measurement at the time of collection.','duration'=>'15 min'],
    ['id'=>'S029','category'=>'Blood Tests','name'=>'Post-Prandial Blood Sugar (PPBS)','description'=>'Blood glucose measurement after a meal, as directed by your clinician.','duration'=>'15 min'],
    ['id'=>'S030','category'=>'Blood Tests','name'=>'HbA1c','description'=>'Laboratory measurement used to assess average blood glucose over time.','duration'=>'15 min'],
    ['id'=>'S031','category'=>'Blood Tests','name'=>'Complete Blood Count (CBC) with ESR','description'=>'Blood cell count with erythrocyte sedimentation rate.','duration'=>'20 min'],
    ['id'=>'S032','category'=>'Blood Tests','name'=>'Iron Studies','description'=>'Iron, ferritin and related measurements as ordered.','duration'=>'20 min'],
    ['id'=>'S033','category'=>'Organ Function Tests','name'=>'Serum Creatinine','description'=>'Blood test used to assess kidney filtration.','duration'=>'15 min'],
    ['id'=>'S034','category'=>'Organ Function Tests','name'=>'Blood Urea','description'=>'Blood urea measurement as part of kidney function assessment.','duration'=>'15 min'],
    ['id'=>'S035','category'=>'Organ Function Tests','name'=>'Renal Function Panel','description'=>'Combined kidney function measurements, including creatinine and urea.','duration'=>'20 min'],
    ['id'=>'S036','category'=>'Organ Function Tests','name'=>'Serum Electrolytes','description'=>'Measurement of sodium, potassium and chloride levels.','duration'=>'20 min'],
    ['id'=>'S037','category'=>'Organ Function Tests','name'=>'Liver Enzymes (SGOT/SGPT)','description'=>'Blood measurements of commonly assessed liver enzymes.','duration'=>'15 min'],
    ['id'=>'S038','category'=>'Organ Function Tests','name'=>'Serum Uric Acid','description'=>'Blood test measuring uric acid levels.','duration'=>'15 min'],
    ['id'=>'S039','category'=>'Hormone & Vitamin Tests','name'=>'TSH','description'=>'Thyroid-stimulating hormone measurement.','duration'=>'15 min'],
    ['id'=>'S040','category'=>'Hormone & Vitamin Tests','name'=>'Vitamin B12','description'=>'Blood test measuring vitamin B12 level.','duration'=>'15 min'],
    ['id'=>'S041','category'=>'Hormone & Vitamin Tests','name'=>'Serum Ferritin','description'=>'Blood test measuring stored iron.','duration'=>'15 min'],
    ['id'=>'S042','category'=>'Cardiac Tests','name'=>'12-Lead ECG','description'=>'Resting electrocardiogram recording.','duration'=>'15 min'],
    ['id'=>'S043','category'=>'Cardiac Tests','name'=>'Echocardiogram','description'=>'Ultrasound assessment of heart structure and function.','duration'=>'30 min'],
    ['id'=>'S044','category'=>'Cardiac Tests','name'=>'Treadmill Stress Test','description'=>'Supervised exercise-based cardiac assessment when clinically appropriate.','duration'=>'45 min'],
    ['id'=>'S045','category'=>'Health Checkups','name'=>'Diabetes Screening Panel','description'=>'Preventive screening panel including blood glucose and related tests.','duration'=>'30 min'],
    ['id'=>'S046','category'=>'Health Checkups','name'=>'Kidney Health Screening','description'=>'Preventive kidney screening panel; tests are performed as ordered.','duration'=>'30 min'],
];

$doctorCreated = 0;
$serviceCreated = 0;
$femaleDoctorIds = ['D002','D004','D007','D009','D012','D015','D017','D018','D019','D021','D022','D023','D024','D025','D026','D027','D028','D029','D030','D032','D034','D037','D039','D042','D047','D050','D051','D053','D055','D057','D059','D060','D061','D062','D064','D066','D068','D070'];
foreach ($doctors as &$doctor) {
    $doctor['gender'] = in_array($doctor['id'], $femaleDoctorIds, true) ? 'female' : 'male';
}
unset($doctor);
foreach ($doctors as $doctor) {
    if (mongoFindOne('doctors', ['id'=>$doctor['id']])) {
        mongoUpdate('doctors', ['id'=>$doctor['id']], ['$set'=>$doctor]);
    } else {
        mongoInsert('doctors', $doctor);
        $doctorCreated++;
    }
}
foreach ($services as $service) {
    if (mongoFindOne('services', ['id'=>$service['id']])) {
        mongoUpdate('services', ['id'=>$service['id']], ['$set'=>$service]);
    } else {
        mongoInsert('services', $service);
        $serviceCreated++;
    }
}

echo '<h2>MediBook catalog setup complete</h2>';
echo '<p>Doctors configured: '.count($doctors).' across '.count(array_unique(array_column($doctors, 'category'))).' departments</p>';
echo '<p>New doctor documents created: '.$doctorCreated.'</p>';
echo '<p>Services configured: '.count($services).' ('.$serviceCreated.' new)</p>';
echo '<p>Collections: doctors, services</p>';
echo '<p><a href="index.php">Return to MediBook</a></p>';
