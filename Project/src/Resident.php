<?php
// C:\xampp\htdocs\Project\src\Resident.php
require_once __DIR__ . '/../config/db.php';

class Resident {
    public static function findOrCreate(string $unit, string $name, string $email, string $contact): array {
        $pdo = get_pdo();
        // Try find existing
        $stmt = $pdo->prepare("SELECT * FROM residents WHERE unit=? AND email=? AND contact=?");
        $stmt->execute([$unit, $email, $contact]);
        $row = $stmt->fetch();
        if ($row) return $row;

        // Insert new
        $stmt = $pdo->prepare("INSERT INTO residents (unit, name, email, contact) VALUES (?,?,?,?)");
        $stmt->execute([$unit, $name, $email, $contact]);
        $id = (int)$pdo->lastInsertId();
        return ['resident_id'=>$id, 'unit'=>$unit, 'name'=>$name, 'email'=>$email, 'contact'=>$contact];
    }
}
