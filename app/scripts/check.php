<?php

require_once dirname(__DIR__, 2) . '/db.php';

echo "DB loaded<br>";
echo "mongoFindOne exists: ";
var_dump(function_exists('mongoFindOne'));

echo "<br><br>Loaded DB file:<br>";
print_r(get_included_files());
