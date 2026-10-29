<?php

require_once __DIR__ . '/db.php';

if (mongoFindOne("users", ["email" => "user@medibook.com"]) === null) {
    mongoInsert("users", [
        "name" => "Demo Patient",
        "email" => "user@medibook.com",
        "password" => password_hash("user123", PASSWORD_DEFAULT),
        "role" => "user"
    ]);
    echo "User account created.<br>";
} else {
    echo "User account already exists.<br>";
}

if (mongoFindOne("users", ["email" => "admin@medibook.com"]) === null) {
    mongoInsert("users", [
        "name" => "Admin",
        "email" => "admin@medibook.com",
        "password" => password_hash("admin123", PASSWORD_DEFAULT),
        "role" => "admin"
    ]);
    echo "Admin account created.<br>";
} else {
    echo "Admin account already exists.<br>";
}

echo "<br>Done.";