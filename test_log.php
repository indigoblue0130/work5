<?php

$log_directory = '/home/xs300844/triple3.online/log';
$log_file = $log_directory . "/test.txt";

file_put_contents($log_file, "test\n", FILE_APPEND);

echo "done";

?>