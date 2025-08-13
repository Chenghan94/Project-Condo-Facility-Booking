<?php
// Project/src/Facility.php
require_once __DIR__ . '/../config/db.php';

class Facility {
    public static function allActive(): array {
        $pdo = get_pdo();
        $stmt = $pdo->query("SELECT * FROM facilities WHERE is_active = 1 ORDER BY name");
        return $stmt->fetchAll();
    }

    public static function find(int $id) {
        $pdo = get_pdo();
        $stmt = $pdo->prepare("SELECT * FROM facilities WHERE facility_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
