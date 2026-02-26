<?php
// Shared database bootstrap using MysqliDb.
require_once __DIR__ . '/MysqliDb.php';

$dbHost = '192.168.0.149';
$dbUser = 'neeJou';
$dbPass = 'Bobo@20100129';
$dbName = 'neejou';
$dbPort = 3306;

try {
    $db = new MysqliDb($dbHost, $dbUser, $dbPass, $dbName, $dbPort, 'utf8mb4');
} catch (Exception $e) {
    http_response_code(500);
    exit('Database connection failed.');
}
