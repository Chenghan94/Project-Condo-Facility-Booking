<?php
require_once __DIR__ . '/../../src/bootstrap.php';
Auth::require_login();

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$facilities = Facility::allActive();
$msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unit = trim($_POST['unit'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    $facility_id = (int)($_POST['facility_id'] ?? 0);
    $date = $_POST['date'] ?? '';
    $slot = $_POST['slot'] ?? '';

    if (!$unit || !$name || !$email || !$contact || !$facility_id || !$date || !$slot) {
        $err = 'Please fill in all fields.';
    } else {
        $facility = Facility::find($facility_id);
        if (!$facility) {
            $err = 'Invalid facility selected.';
        } else {
            [$slot_start, $slot_end] = explode('|', $slot);
            $resident = Resident::findOrCreate($unit, $name, $email, $contact);
            $res = Booking::create((int)$resident['resident_id'], $facility_id, $date, $slot_start, $slot_end);
            if ($res['ok']) {
                $msg = 'Booking created successfully!';
            } else {
                $err = $res['error'];
            }
        }
    }
}
?>
<?php require_once __DIR__ . '/../../views/partials/header.php'; ?>
<h2>Create New Booking (Admin)</h2>
<?php if ($msg): ?><div style="background:#ecfeff;padding:10px;border:1px solid #a5f3fc;border-radius:8px;"><?php echo h($msg); ?></div><?php endif; ?>
<?php if ($err): ?><div style="background:#fee2e2;padding:10px;border:1px solid #fecaca;border-radius:8px;color:#7f1d1d;"><?php echo h($err); ?></div><?php endif; ?>

<form method="post">
  <fieldset>
    <legend>Resident Details</legend>
    <label>Unit</label>
    <input name="unit" required value="<?php echo h($_POST['unit'] ?? ''); ?>">
    <label>Name</label>
    <input name="name" required value="<?php echo h($_POST['name'] ?? ''); ?>">
    <label>Email</label>
    <input type="email" name="email" required value="<?php echo h($_POST['email'] ?? ''); ?>">
    <label>Contact</label>
    <input name="contact" required value="<?php echo h($_POST['contact'] ?? ''); ?>">
  </fieldset>

  <fieldset>
    <legend>Booking Details</legend>
    <label>Facility</label>
    <select name="facility_id" id="facility" required>
      <option value="">-- Select --</option>
      <?php foreach ($facilities as $f): ?>
        <option value="<?php echo (int)$f['facility_id']; ?>" <?php echo (($_POST['facility_id'] ?? '')==(string)$f['facility_id'])?'selected':''; ?>>
          <?php echo h($f['name']); ?>
        </option>
      <?php endforeach; ?>
    </select>

    <label>Date</label>
    <input type="date" name="date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo h($_POST['date'] ?? ''); ?>">

    <label>Time Slot</label>
    <select name="slot" id="slot-select" required>
      <option value="">-- Select facility first --</option>
    </select>
  </fieldset>

  <input type="submit" value="Create Booking"
       style="background:#0f766e;color:#fff;padding:6px 12px;border-radius:4px;border:none;cursor:pointer;">
<button type="button" onclick="history.back()" 
        style="margin-left:8px;background:#0f766e;color:#fff;padding:6px 12px;border-radius:4px;border:none;cursor:pointer;">
  ← Back
</button>

</button>

</form>

<script>
const facilities = <?php echo json_encode($facilities); ?>;
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
  const selectFacility = document.getElementById('facility');
  const slotSel = document.getElementById('slot-select');
  slotSel.innerHTML = '<option value="">-- Select --</option>';
  const id = parseInt(selectFacility.value,10);
  const facility = facilities.find(f => parseInt(f.facility_id,10) === id);
  if (!facility) return;
  const slots = buildSlots(facility);
  slots.forEach(s => {
    const opt = document.createElement('option');
    opt.value = s[0] + '|' + s[1];
    opt.textContent = s[0].slice(0,5) + ' - ' + s[1].slice(0,5);
    slotSel.appendChild(opt);
  });
}
document.getElementById('facility').addEventListener('change', refreshSlots);
</script>

<?php require_once __DIR__ . '/../../views/partials/footer.php'; ?>
