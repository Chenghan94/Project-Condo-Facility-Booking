<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::require_login();
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$facilities = Facility::allActive();
$err = $msg = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$booking = $id ? Booking::get($id) : null;
if (!$booking) {
    die('Booking not found.');
}

$facility_id = (int)$booking['facility_id'];
$date        = $booking['booking_date'];
$slot_start  = $booking['slot_start'];
$slot_end    = $booking['slot_end'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $facility_id = (int)($_POST['facility_id'] ?? $facility_id);
    $date        = $_POST['date'] ?? $date;
    $slot        = $_POST['slot'] ?? ( $slot_start . '|' . $slot_end );

    if (!$facility_id || !$date || !$slot) {
        $err = 'Please fill in all fields.';
    } else {
        [$slot_start, $slot_end] = explode('|', $slot);
        $facility = Facility::find($facility_id);
        if (!$facility) {
            $err = 'Invalid facility.';
        } else {
            $res = Booking::update($id, $facility_id, $date, $slot_start, $slot_end);
            if ($res['ok']) {
                $msg = 'Booking updated.';
                // reload current data
                $booking = Booking::get($id);
            } else {
                $err = $res['error'];
            }
        }
    }
}
?>
<?php require_once __DIR__ . '/../../views/partials/header.php'; ?>
<h2>Edit Booking #<?php echo (int)$id; ?></h2>

<?php if ($msg): ?><div style="background:#ecfeff;border:1px solid #a5f3fc;padding:10px;border-radius:8px;"><?php echo h($msg); ?></div><?php endif; ?>
<?php if ($err): ?><div style="background:#fee2e2;border:1px solid #fecaca;padding:10px;border-radius:8px;color:#7f1d1d;"><?php echo h($err); ?></div><?php endif; ?>

<p><strong>Resident:</strong> <?php echo h($booking['unit'] . ' | ' . $booking['name'] . ' | ' . $booking['contact']); ?></p>

<form method="post">
  <fieldset>
    <legend>Booking Details</legend>

    <label>Facility</label>
    <select name="facility_id" id="facility" required>
      <?php foreach ($facilities as $f): ?>
        <option value="<?php echo (int)$f['facility_id']; ?>" <?php echo $facility_id==(int)$f['facility_id']?'selected':''; ?>>
          <?php echo h($f['name']); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Date</label>
    <input type="date" name="date" required value="<?php echo h($date); ?>">

    <label>Time Slot</label>
    <select name="slot" id="slot-select" required>
      <!-- will be filled by JS; preselect current -->
    </select>
  </fieldset>

  <input type="submit" value="Save Changes">
  <button type="button" onclick="history.back()">← Back</button>
</form>

<script>
const facilities = <?php echo json_encode($facilities); ?>;
const current = { start: "<?php echo h($slot_start); ?>", end: "<?php echo h($slot_end); ?>" };

function timeToMinutes(t){ const [h,m,s] = t.split(':').map(Number); return h*60+m; }
function minutesToTime(min){ const h=String(Math.floor(min/60)).padStart(2,'0'); const m=String(min%60).padStart(2,'0'); return h+':'+m+':00'; }

function buildSlots(facility) {
  const openM = timeToMinutes(facility.open_time);
  const closeM = timeToMinutes(facility.close_time);
  const step = parseInt(facility.slot_minutes,10);
  const slots = [];
  for (let m=openM; m+step<=closeM; m+=step) {
    const start = minutesToTime(m);
    const end = minutesToTime(m+step);
    slots.push([start, end]);
  }
  return slots;
}

function refreshSlots() {
  const selFacility = document.getElementById('facility');
  const slotSel = document.getElementById('slot-select');
  slotSel.innerHTML = '';
  const id = parseInt(selFacility.value,10);
  const facility = facilities.find(f => parseInt(f.facility_id,10) === id);
  if (!facility) return;
  const slots = buildSlots(facility);
  slots.forEach(s => {
    const opt = document.createElement('option');
    opt.value = s[0] + '|' + s[1];
    opt.textContent = s[0].slice(0,5) + ' - ' + s[1].slice(0,5);
    if (s[0] === current.start && s[1] === current.end) opt.selected = true;
    slotSel.appendChild(opt);
  });
}
document.getElementById('facility').addEventListener('change', refreshSlots);
window.addEventListener('DOMContentLoaded', refreshSlots);
</script>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
