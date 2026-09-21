<?php
require_once __DIR__ . '/../config/database.php';

class Student
{
    public static function all(): array
    {
        $db = Database::getInstance();
        $sql = "SELECT s.*, u.username, u.is_first_login,
                       COALESCE(SUM(tf.total_amount), 0) as total_fee,
                       COALESCE(SUM(tf.amount_paid), 0) as total_paid
                FROM students s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN tuition_fees tf ON s.id = tf.student_id
                GROUP BY s.id
                ORDER BY s.id DESC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT s.*, u.username FROM students s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByUserId(int $userId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM students WHERE user_id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO students (user_id, student_id, first_name, last_name, middle_name, email, grade_level, school_year, parent_name, parent_email, contact_number)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['user_id'],
            $data['student_id'],
            $data['first_name'],
            $data['last_name'],
            $data['middle_name'] ?? null,
            $data['email'],
            $data['grade_level'] ?? 'Class A',
            $data['school_year'] ?? date('Y') . '-' . (date('Y') + 1),
            $data['parent_name'] ?? null,
            $data['parent_email'] ?? null,
            $data['contact_number'] ?? null,
        ]);
        return (int) $db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "UPDATE students 
             SET first_name = ?, last_name = ?, email = ?, grade_level = ?, parent_name = ?, parent_email = ?, contact_number = ?
             WHERE id = ?"
        );
        $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['grade_level'],
            $data['parent_name'],
            $data['parent_email'],
            $data['contact_number'],
            $id
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getInstance();
        $student = self::findById($id);
        if ($student) {
            // Delete associated user account which cascades
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$student['user_id']]);
        }
    }

    public static function updateClassDetails(int $id, string $gradeLevel, string $schoolYear): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE students SET grade_level = ?, school_year = ? WHERE id = ?");
        $stmt->execute([$gradeLevel, $schoolYear, $id]);
    }
}
