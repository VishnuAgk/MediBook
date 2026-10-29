<?php

require_once __DIR__ . '/db.php';

$user = mongoFindOne("users", [
    "email" => "user@medibook.com"
]);

echo "<pre>";

if ($user) {
    echo "USER FOUND\n\n";
    print_r($user);

    echo "\nPassword test: ";
    var_dump(password_verify("user123", $user["password"]));
} else {
    echo "USER NOT FOUND";
}

echo "</pre>";