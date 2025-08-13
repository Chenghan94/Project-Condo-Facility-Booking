<?php
// Project/src/Booking.php
require_once __DIR__ . '/../config/db.php';

class Booking {
    public static function create(int $resident_id, int $facility_id, string $date, string $slot_start, string $slot_end): array {
        $pdo = get_pdo();
        $stmt = $pdo->prepare("INSERT INTO bookings (resident_id, facility_id, booking_date, slot_start, slot_end) VALUES (?,?,?,?,?)");
        try {
            $stmt->execute([$resident_id, $facility_id, $date, $slot_start, $slot_end]);
            return ['ok'=>true, 'booking_id'=>$pdo->lastInsertId()];
        } catch (PDOException $e) {
            // Catches duplicate slot (unique key uk_active_slot) or other DB errors
            if (strpos($e->getMessage(), 'uk_active_slot') !== false) {
                return ['ok'=>false, 'error'=>'This slot is already taken. Please pick another time.'];
            }
            return ['ok'=>false, 'error'=>'Database error: ' . $e->getMessage()];

        }
    }
    public static function findByResident(string $unit, ?string $email, ?string $contact): array {
    $pdo = get_pdo();
    $sql = "SELECT b.booking_id, f.name AS facility, b.booking_date, b.slot_start, b.slot_end, b.status
            FROM bookings b
            JOIN facilities f ON f.facility_id = b.facility_id
            JOIN residents r  ON r.resident_id = b.resident_id
            WHERE r.unit = ? AND (r.email = ? OR r.contact = ?)
            ORDER BY b.booking_date, b.slot_start";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$unit, $email, $contact]);
    return $stmt->fetchAll();
}
    public static function listByDateRange(int $facility_id, string $from, string $to): array {
    $pdo = get_pdo();
    $sql = "SELECT b.booking_id, b.booking_date, b.slot_start, b.slot_end, b.status,
                   r.unit, r.name, r.contact
            FROM bookings b
            JOIN residents r ON r.resident_id = b.resident_id
            WHERE b.facility_id = ?
              AND b.booking_date BETWEEN ? AND ?
            ORDER BY b.booking_date, b.slot_start";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$facility_id, $from, $to]);
    return $stmt->fetchAll();
}
    public static function get(int $booking_id): ?array {
    $pdo = get_pdo();
    $sql = "SELECT b.*, r.unit, r.name, r.email, r.contact
            FROM bookings b
            JOIN residents r ON r.resident_id = b.resident_id
            WHERE b.booking_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$booking_id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

public static function update(int $booking_id, int $facility_id, string $date, string $slot_start, string $slot_end): array {
    $pdo = get_pdo();
    $stmt = $pdo->prepare("
        UPDATE bookings
        SET facility_id = ?, booking_date = ?, slot_start = ?, slot_end = ?
        WHERE booking_id = ?
        LIMIT 1
    ");
    try {
        $stmt->execute([$facility_id, $date, $slot_start, $slot_end, $booking_id]);
        return ['ok'=>true];
    } catch (PDOException $e) {
        // Unique key on (facility_id, booking_date, slot_start) will throw if another booking already uses that slot
        if (strpos($e->getMessage(), 'uk_active_slot') !== false) {
            return ['ok'=>false, 'error'=>'This slot is already taken by another booking.'];
        }
        return ['ok'=>false, 'error'=>'Database error: ' . $e->getMessage()];
    }
}

}
