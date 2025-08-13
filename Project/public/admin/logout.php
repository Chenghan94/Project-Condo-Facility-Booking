<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::logout();
header('Location: /Project/public/admin/login.php?msg=Logged+out');
