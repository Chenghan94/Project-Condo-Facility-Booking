<?php require_once __DIR__ . '/../views/partials/header.php'; ?>
<h2>Welcome</h2>
<p>This is the landing page for residents and management.</p>
<div style="background:#ecfeff; padding:10px; border-radius:8px; border:1px solid #a5f3fc;">
  Residents can make bookings and view their current reservations.<br>
  Management can login to view, create, update, or cancel bookings.
</div>
<p>
  <a href="/Project/public/booking.php">Make a Booking</a> |
  <a href="/Project/public/my-bookings.php">View My Bookings</a>
</p>
<?php require_once __DIR__ . '/../views/partials/footer.php'; ?>
