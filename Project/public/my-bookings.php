<?php
require_once __DIR__ . '/../src/bootstrap.php';

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$results = [];
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unit    = trim($_POST['unit'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    if (!$unit || (!$email && !$contact)) {
        $err = 'Enter your Unit and either Email or Contact.';
    } else {
        $results = Booking::findByResident($unit, $email ?: null, $contact ?: null);
        if (!$results) $err = 'No bookings found.';
    }
}
?>
<?php require_once __DIR__ . '/../views/partials/header.php'; ?>
<h2>My Bookings</h2>

<form method="post">
  <label>Unit</label>
  <input name="unit" required value="<?php echo h($_POST['unit'] ?? ''); ?>">

  <label>Email (or Contact)</label>
  <input type="email" name="email" value="<?php echo h($_POST['email'] ?? ''); ?>">
  <label>Contact</label>
  <input name="contact" value="<?php echo h($_POST['contact'] ?? ''); ?>">

  <p><small>Tip: you can leave Email empty if you enter Contact (or vice‑versa).</small></p>
  <input type="submit" value="View">
</form>

<?php if ($err): ?>
  <div style="background:#fee2e2;border:1px solid #fecaca;padding:10px;border-radius:8px;color:#7f1d1d;"><?php echo h($err); ?></div>
<?php endif; ?>

<?php if ($results): ?>
  <table class="table" style="width:100%;border-collapse:collapse;margin-top:12px;">
    <tr>
      <th style="border:1px solid #e5e7eb;padding:8px;">ID</th>
      <th style="border:1px solid #e5e7eb;padding:8px;">Facility</th>
      <th style="border:1px solid #e5e7eb;padding:8px;">Date</th>
      <th style="border:1px solid #e5e7eb;padding:8px;">Start</th>
      <th style="border:1px solid #e5e7eb;padding:8px;">End</th>
      <th style="border:1px solid #e5e7eb;padding:8px;">Status</th>
    </tr>
    <?php foreach ($results as $r): ?>
    <tr>
      <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo (int)$r['booking_id']; ?></td>
      <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h($r['facility']); ?></td>
      <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h($r['booking_date']); ?></td>
      <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h(substr($r['slot_start'],0,5)); ?></td>
      <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h(substr($r['slot_end'],0,5)); ?></td>
      <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h($r['status']); ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>
