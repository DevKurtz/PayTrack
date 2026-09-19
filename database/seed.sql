-- ============================================================
--  PAYTRACK v2 — Seed Data (Development / Testing)
--  Import AFTER schema.sql
-- ============================================================

USE `paytrack`;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `email_logs`;
TRUNCATE TABLE `payments`;
TRUNCATE TABLE `tuition_fee_items`;
TRUNCATE TABLE `tuition_fees`;
TRUNCATE TABLE `students`;
TRUNCATE TABLE `fee_categories`;
TRUNCATE TABLE `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- Admin account (password: admin123)
-- ------------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `is_first_login`) VALUES
(1, 'admin', '$2y$10$jJBca9GtHWFBVFnZkqsIduuOLLnX.4fX2ZcTTijQ4ljIiNHmtVauW', 'admin', 0);

-- ------------------------------------------------------------
-- Sample student user (format: 2023-53512 / password: DELACRUZ)
-- ------------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `is_first_login`) VALUES
(2, '2023-53512', '$2y$10$9kCOdWJNylQsuxeo.VBb9OxBN7TCfgsF03uXEtq3xSWgJFihb1GpW', 'student', 1);

-- ------------------------------------------------------------
-- Default Fee Categories
-- Fixed fees: Registration (650), LMS (500), Lab (1200), Library (450), Medical (300), Athletic (350), Misc (1500) = 4,950
-- Variable fee: Subject Units (variable remainder)
-- ------------------------------------------------------------
INSERT INTO `fee_categories` (`id`, `name`, `code`, `default_amount`, `is_variable`, `sort_order`, `is_active`) VALUES
(1, 'Registration & Matriculation Fee', 'reg', 650.00, 0, 1, 1),
(2, 'LMS & E-Learning Platform Fee', 'lms', 500.00, 0, 2, 1),
(3, 'Laboratory & Computer Lab Fee', 'lab', 1200.00, 0, 3, 1),
(4, 'Library & Learning Media Fee', 'lib', 450.00, 0, 4, 1),
(5, 'Medical & Dental Clinic Fee', 'med', 300.00, 0, 5, 1),
(6, 'Student Activities & Athletic Fee', 'ath', 350.00, 0, 6, 1),
(7, 'Miscellaneous & Development Fee', 'misc', 1500.00, 0, 7, 1),
(8, 'Subject Units & Academic Tuition', 'subject', 0.00, 1, 8, 1);

-- ------------------------------------------------------------
-- Sample student profile (student_id: 2023-53512)
-- ------------------------------------------------------------
INSERT INTO `students`
  (`id`, `user_id`, `student_id`, `first_name`, `last_name`, `middle_name`,
   `email`, `grade_level`, `school_year`,
   `parent_name`, `parent_email`, `contact_number`)
VALUES
  (1, 2, '2023-53512', 'Juan', 'Dela Cruz', 'Santos',
   'juan.delacruz@email.com', 'Grade 11 - STEM', '2024-2025',
   'Maria Dela Cruz', 'maria.delacruz@email.com', '09171234567');

-- ------------------------------------------------------------
-- Sample tuition fee record (Total ₱21,000 | Paid ₱5,000 | Balance ₱16,000)
-- ------------------------------------------------------------
INSERT INTO `tuition_fees`
  (`id`, `student_id`, `school_year`, `semester`, `description`, `total_amount`, `amount_paid`, `due_date`, `status`)
VALUES
  (1, 1, '2024-2025', '1st Semester', 'S.Y. 2024-2025 - 1st Semester Tuition', 21000.00, 5000.00, '2024-10-31', 'partial');

-- ------------------------------------------------------------
-- Tuition breakdown items (Display-only aspects)
-- Total fixed: ₱4,950.00 | Subject Units: ₱16,050.00 | Total: ₱21,000.00
-- ------------------------------------------------------------
INSERT INTO `tuition_fee_items` (`tuition_fee_id`, `fee_category_id`, `category_name`, `amount`) VALUES
(1, 1, 'Registration & Matriculation Fee', 650.00),
(1, 2, 'LMS & E-Learning Platform Fee', 500.00),
(1, 3, 'Laboratory & Computer Lab Fee', 1200.00),
(1, 4, 'Library & Learning Media Fee', 450.00),
(1, 5, 'Medical & Dental Clinic Fee', 300.00),
(1, 6, 'Student Activities & Athletic Fee', 350.00),
(1, 7, 'Miscellaneous & Development Fee', 1500.00),
(1, 8, 'Subject Units & Academic Tuition', 16050.00);

-- ------------------------------------------------------------
-- Sample payment record
-- ------------------------------------------------------------
INSERT INTO `payments`
  (`id`, `tuition_fee_id`, `student_id`, `or_number`, `amount`, `payment_method`, `notes`, `paid_at`)
VALUES
  (1, 1, 1, 'OR-2024-00001', 5000.00, 'online', 'First installment payment via portal', '2024-09-01 10:30:00');
