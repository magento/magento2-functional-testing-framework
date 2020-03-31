<?php
$test = $_GET['test'] ?? "NO_TEST_SPECIFIED";
file_put_contents('CURRENT_TEST', $test);
echo 'SET CURRENT TEST TO ' . $test;