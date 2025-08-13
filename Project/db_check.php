<?php
require_once __DIR__ . '/config/db.php';

try {
    $pdo = get_pdo();
    $tables = $pdo->query("SHOW TABLES")->fetchAll();
    echo "<h3>✅ Database connected!</h3>";
    echo "<pre>" . print_r($tables, true) . "</pre>";
} catch (Throwable $e) {
    echo "<h3>❌ Connection failed</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</pre>";
}
