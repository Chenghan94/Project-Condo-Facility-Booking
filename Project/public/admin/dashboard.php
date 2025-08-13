<?php
require_once __DIR__ . '/../../src/bootstrap.php';
require_once __DIR__ . '/../../src/Facility.php';
require_once __DIR__ . '/../../src/Booking.php';
Auth::require_login();

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$facilities = Facility::allActive();
$facility_id = (int)($_GET['facility_id'] ?? ($facilities ? $facilities[0]['facility_id'] : 0));
$from = $_GET['from'] ?? date('Y-m-d');
$to   = $_GET['to']   ?? date('Y-m-d', strtotime('+6 days'));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $del = (int)$_POST['delete_id'];
    $pdo = get_pdo();
    $stmt = $pdo->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->execute([$del]);
    header("Location: /Project/public/admin/dashboard.php?facility_id={$facility_id}&from={$from}&to={$to}");
    exit;
}

$rows = [];
if ($facility_id) {
    $rows = Booking::listByDateRange($facility_id, $from, $to);
}
?>
<?php require_once __DIR__ . '/../../views/partials/header.php'; ?>
<h2>Dashboard</h2>
<p>Welcome, <?php echo h($_SESSION['user_name']); ?> | <a href="/Project/public/admin/logout.php" style="color:#0d0c0c;">Logout</a></p>

<form method="get" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
  <div>
    <label>Facility</label>
    <select name="facility_id">
      <?php foreach ($facilities as $f): ?>
      <option value="<?php echo (int)$f['facility_id']; ?>" <?php echo $facility_id==(int)$f['facility_id']?'selected':''; ?>>
        <?php echo h($f['name']); ?>
      </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label>From</label>
    <input type="date" name="from" value="<?php echo h($from); ?>">
  </div>
  <div>
    <label>To</label>
    <input type="date" name="to" value="<?php echo h($to); ?>">
  </div>
  <div><input type="submit" value="Filter"></div>
</form>

<p>
  <a href="/Project/public/admin/new-booking.php" 
     style="background:#0f766e;color:#fff;padding:8px 12px;border-radius:5px;text-decoration:none;">
    + Create New Booking
  </a>
</p>

<?php if ($rows): ?>
<table class="table" style="width:100%;border-collapse:collapse;margin-top:12px;">
  <tr>
    <th style="border:1px solid #e5e7eb;padding:8px;">ID</th>
    <th style="border:1px solid #e5e7eb;padding:8px;">Date</th>
    <th style="border:1px solid #e5e7eb;padding:8px;">Start</th>
    <th style="border:1px solid #e5e7eb;padding:8px;">End</th>
    <th style="border:1px solid #e5e7eb;padding:8px;">Unit</th>
    <th style="border:1px solid #e5e7eb;padding:8px;">Name</th>
    <th style="border:1px solid #e5e7eb;padding:8px;">Contact</th>
    <th style="border:1px solid #e5e7eb;padding:8px;">Status</th>
    <th style="border:1px solid #e5e7eb;padding:8px;">Action</th>
  </tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo (int)$r['booking_id']; ?></td>
    <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h($r['booking_date']); ?></td>
    <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h(substr($r['slot_start'],0,5)); ?></td>
    <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h(substr($r['slot_end'],0,5)); ?></td>
    <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h($r['unit']); ?></td>
    <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h($r['name']); ?></td>
    <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h($r['contact']); ?></td>
    <td style="border:1px solid #e5e7eb;padding:8px;"><?php echo h($r['status']); ?></td>
    <td style="border:1px solid #e5e7eb;padding:8px;">
        <a href="/Project/public/admin/edit-booking.php?id=<?php echo (int)$r['booking_id']; ?>"
        style="margin-right:6px;">Edit</a>
      <form method="post" onsubmit="return confirm('Cancel this booking?');">
        <input type="hidden" name="delete_id" value="<?php echo (int)$r['booking_id']; ?>">
        <input type="submit" value="Cancel">
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table>
<?php else: ?>
<p>No bookings in the selected range.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
