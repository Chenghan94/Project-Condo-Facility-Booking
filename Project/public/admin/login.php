<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::start();

if (!class_exists('Auth')) { die('Auth class not loaded'); }
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    if (Auth::login($email, $password)) {
        header('Location: /Project/public/admin/dashboard.php');
        exit;
    } else {
        $msg = 'Invalid credentials.';
    }
}
?>
<?php require_once __DIR__ . '/../../views/partials/header.php'; ?>
<h2>Management Login</h2>
<?php if ($msg): ?><div style="background:#fee2e2;border:1px solid #fecaca;padding:10px;border-radius:8px;color:#7f1d1d;"><?php echo htmlspecialchars($msg,ENT_QUOTES,'UTF-8'); ?></div><?php endif; ?>
<form method="post">
  <label>Email</label>
  <input name="email" type="email" required>
  <label>Password</label>
  <input name="password" type="password" required>
  <input type="submit" value="Login">
</form>
<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
