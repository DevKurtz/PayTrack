<?php
require_once __DIR__ . '/../config/database.php';

class FeeCategory
{
    public static function all(): array
    {
        $db = Database::getInstance();
        return $db->query('SELECT * FROM fee_categories ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public static function allActive(): array
    {
        $db = Database::getInstance();
        return $db->query('SELECT * FROM fee_categories WHERE is_active = 1 ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT * FROM fee_categories WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getVariableCategory(): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->query('SELECT * FROM fee_categories WHERE is_variable = 1 AND is_active = 1 LIMIT 1');
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getFixedTotal(): float
    {
        $db = Database::getInstance();
        $row = $db->query('SELECT COALESCE(SUM(default_amount), 0) AS total FROM fee_categories WHERE is_variable = 0 AND is_active = 1')->fetch();
        return (float) ($row['total'] ?? 0);
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $isVar = !empty($data['is_variable']) ? 1 : 0;
        if ($isVar) {
            $db->query('UPDATE fee_categories SET is_variable = 0');
        }
        $stmt = $db->prepare(
            'INSERT INTO fee_categories (name, code, default_amount, is_variable, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'],
            $data['code'] ?? strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $data['name'])),
            $isVar ? 0.00 : (float) ($data['default_amount'] ?? 0),
            $isVar,
            (int) ($data['sort_order'] ?? 99),
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getInstance();
        $isVar = !empty($data['is_variable']) ? 1 : 0;
        if ($isVar) {
            $db->prepare('UPDATE fee_categories SET is_variable = 0 WHERE id != ?')->execute([$id]);
        }
        $stmt = $db->prepare(
            'UPDATE fee_categories SET name = ?, default_amount = ?, is_variable = ?, sort_order = ?, is_active = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['name'],
            $isVar ? 0.00 : (float) ($data['default_amount'] ?? 0),
            $isVar,
            (int) ($data['sort_order'] ?? 99),
            isset($data['is_active']) ? (int) $data['is_active'] : 1,
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare('DELETE FROM fee_categories WHERE id = ?');
        $stmt->execute([$id]);
    }
}
