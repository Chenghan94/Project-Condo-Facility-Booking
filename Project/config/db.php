<?php
// config/db.php
// Edit DB_USER / DB_PASS if your MySQL has a password.
$DB_HOST = '127.0.0.1';
$DB_NAME = 'condo_booking';
$DB_USER = 'root';
$DB_PASS = '';

function get_pdo(): PDO {
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    return new PDO($dsn, $DB_USER, $DB_PASS, $options);
}
