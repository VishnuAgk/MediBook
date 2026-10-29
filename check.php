<?php

require_once __DIR__ . '/db.php';

echo "DB loaded<br>";
echo "mongoFindOne exists: ";
var_dump(function_exists('mongoFindOne'));

echo "<br><br>Loaded DB file:<br>";
print_r(get_included_files());
