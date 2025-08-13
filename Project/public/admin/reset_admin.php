<?php
require_once __DIR__ . '/../../src/bootstrap.php';

$pdo = get_pdo();
$email = 'admin@condo.sg';
$pass  = 'Admin@123'; // change after you login successfully

$hash = password_hash($pass, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("
    INSERT INTO management_users (email, user_name, password_hash, role, is_active)
    VALUES (?, 'Admin', ?, 'ADMIN', 1)
    ON DUPLICATE KEY UPDATE
      user_name=VALUES(user_name),
      role=VALUES(role),
      is_active=VALUES(is_active),
      password_hash=VALUES(password_hash)
");
$stmt->execute([$email, $hash]);

echo 'Done. Try logging in now. (Delete this file!)';
