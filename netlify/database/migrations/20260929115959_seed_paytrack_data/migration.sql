-- Seed data imported from the PayTrack MySQL dump (paytrack.sql)

INSERT INTO "users" ("id", "username", "name", "email", "password_hash", "role", "is_first_login", "status", "last_login_at", "last_active_at", "created_at", "updated_at") VALUES
(1, 'admin', 'System Administrator', 'admin@paytrack.edu.ph', '$2y$10$jJBca9GtHWFBVFnZkqsIduuOLLnX.4fX2ZcTTijQ4ljIiNHmtVauW', 'admin', false, 'active', '2026-09-27 17:35:47', '2026-09-27 17:37:30', '2026-09-21 21:02:14', '2026-09-27 17:37:30'),
(3, '2023-53512', 'Juan Dela Cruz', 'juan.delacruz@email.com', '$2y$10$9kCOdWJNylQsuxeo.VBb9OxBN7TCfgsF03uXEtq3xSWgJFihb1GpW', 'student', true, 'active', '2026-09-23 23:03:02', '2026-09-23 23:03:33', '2026-09-21 21:02:14', '2026-09-23 23:03:33'),
(4, 'accounting', 'Jane Santos', 'zackary.22010@gmail.com', '$2y$10$7d9jx3kb1Cfu0H8BG2wcquzgHFklViCXYvDAMeQ2WWpN3tsGhRuHi', 'accounting', false, 'active', '2026-09-27 17:30:06', '2026-09-27 17:31:11', '2026-09-21 21:20:20', '2026-09-27 17:31:11'),
(6, '2002-09090', 'mark flores', 'obias6569@gmail.com', '$2y$10$ecLqEbWqfmhFNLb3eF5pq.B1gKpljLGS29oKjDsBgN6ocYPuh0nSO', 'student', true, 'active', '2026-09-27 17:31:22', '2026-09-27 17:35:30', '2026-09-22 22:57:40', '2026-09-27 17:35:30');
--> statement-breakpoint

INSERT INTO "students" ("id", "user_id", "student_id", "first_name", "last_name", "middle_name", "email", "grade_level", "school_year", "parent_name", "parent_email", "contact_number", "address", "created_at", "updated_at") VALUES
(1, 3, '2023-53512', 'Juan', 'Dela Cruz', 'Santos', 'juan.delacruz@email.com', 'Grade 11 - STEM', '2024-2025', 'Maria Dela Cruz', 'maria.delacruz@email.com', '09171234567', NULL, '2026-09-21 21:02:14', '2026-09-21 21:02:14'),
(3, 6, '2002-09090', 'mark', 'flores', '', 'obias6569@gmail.com', 'BSCS 11A1', '2026-2027', 'may', 'marks000211@gmail.com', '', NULL, '2026-09-22 22:57:40', '2026-09-22 22:57:40');
--> statement-breakpoint

INSERT INTO "fee_categories" ("id", "name", "code", "default_amount", "is_variable", "sort_order", "is_active", "created_at") VALUES
(1, 'Registration & Matriculation Fee', 'reg', 650.00, false, 1, true, '2026-09-21 21:02:14'),
(2, 'LMS & E-Learning Platform Fee', 'lms', 500.00, false, 2, true, '2026-09-21 21:02:14'),
(3, 'Laboratory & Computer Lab Fee', 'lab', 1200.00, false, 3, true, '2026-09-21 21:02:14'),
(4, 'Library & Learning Media Fee', 'lib', 450.00, false, 4, true, '2026-09-21 21:02:14'),
(5, 'Medical & Dental Clinic Fee', 'med', 300.00, false, 5, true, '2026-09-21 21:02:14'),
(6, 'Student Activities & Athletic Fee', 'ath', 350.00, false, 6, true, '2026-09-21 21:02:14'),
(7, 'Miscellaneous & Development Fee', 'misc', 1500.00, false, 7, true, '2026-09-21 21:02:14'),
(8, 'Subject Units & Academic Tuition', 'subject', 0.00, true, 8, true, '2026-09-21 21:02:14');
--> statement-breakpoint

INSERT INTO "tuition_fees" ("id", "student_id", "school_year", "semester", "description", "total_amount", "amount_paid", "due_date", "status", "created_at", "updated_at") VALUES
(1, 1, '2024-2025', '1st Semester', 'S.Y. 2024-2025 - 1st Semester Tuition', 21000.00, 15000.00, '2024-10-31', 'partial', '2026-09-21 21:02:14', '2026-09-23 22:52:50'),
(2, 3, '2026-2027', '1st Semester', 'S.Y. 2026-2027 - 1st Semester Tuition', 4950.00, 0.00, '2026-10-22', 'unpaid', '2026-09-22 23:08:38', '2026-09-22 23:08:38'),
(3, 3, '2026-2027', '1st Semester', 'S.Y. 2026-2027 - 1st Semester Tuition', 4950.00, 0.00, '2026-10-22', 'unpaid', '2026-09-22 23:08:48', '2026-09-22 23:08:48'),
(4, 3, '2026-2027', '1st Semester', 'S.Y. 2026-2027 - 1st Semester Tuition', 4950.00, 0.00, '2026-10-22', 'unpaid', '2026-09-22 23:08:59', '2026-09-22 23:08:59'),
(5, 3, '2026-2027', '1st Semester', 'S.Y. 2026-2027 - 1st Semester Tuition', 4950.00, 0.00, '2026-10-22', 'unpaid', '2026-09-22 23:09:11', '2026-09-22 23:09:11'),
(6, 3, '2026-2027', '1st Semester', 'S.Y. 2026-2027 - 1st Semester Tuition', 9950.00, 8000.00, '2026-10-22', 'partial', '2026-09-22 23:09:21', '2026-09-23 22:58:30');
--> statement-breakpoint

INSERT INTO "tuition_fee_items" ("id", "tuition_fee_id", "fee_category_id", "category_name", "amount") VALUES
(1, 1, 1, 'Registration & Matriculation Fee', 650.00),
(2, 1, 2, 'LMS & E-Learning Platform Fee', 500.00),
(3, 1, 3, 'Laboratory & Computer Lab Fee', 1200.00),
(4, 1, 4, 'Library & Learning Media Fee', 450.00),
(5, 1, 5, 'Medical & Dental Clinic Fee', 300.00),
(6, 1, 6, 'Student Activities & Athletic Fee', 350.00),
(7, 1, 7, 'Miscellaneous & Development Fee', 1500.00),
(8, 1, 8, 'Subject Units & Academic Tuition', 16050.00),
(9, 2, 1, 'Registration & Matriculation Fee', 650.00),
(10, 2, 2, 'LMS & E-Learning Platform Fee', 500.00),
(11, 2, 3, 'Laboratory & Computer Lab Fee', 1200.00),
(12, 2, 4, 'Library & Learning Media Fee', 450.00),
(13, 2, 5, 'Medical & Dental Clinic Fee', 300.00),
(14, 2, 6, 'Student Activities & Athletic Fee', 350.00),
(15, 2, 7, 'Miscellaneous & Development Fee', 1500.00),
(16, 2, 8, 'Subject Units & Academic Tuition', 0.00),
(17, 3, 1, 'Registration & Matriculation Fee', 650.00),
(18, 3, 2, 'LMS & E-Learning Platform Fee', 500.00),
(19, 3, 3, 'Laboratory & Computer Lab Fee', 1200.00),
(20, 3, 4, 'Library & Learning Media Fee', 450.00),
(21, 3, 5, 'Medical & Dental Clinic Fee', 300.00),
(22, 3, 6, 'Student Activities & Athletic Fee', 350.00),
(23, 3, 7, 'Miscellaneous & Development Fee', 1500.00),
(24, 3, 8, 'Subject Units & Academic Tuition', 0.00),
(25, 4, 1, 'Registration & Matriculation Fee', 650.00),
(26, 4, 2, 'LMS & E-Learning Platform Fee', 500.00),
(27, 4, 3, 'Laboratory & Computer Lab Fee', 1200.00),
(28, 4, 4, 'Library & Learning Media Fee', 450.00),
(29, 4, 5, 'Medical & Dental Clinic Fee', 300.00),
(30, 4, 6, 'Student Activities & Athletic Fee', 350.00),
(31, 4, 7, 'Miscellaneous & Development Fee', 1500.00),
(32, 4, 8, 'Subject Units & Academic Tuition', 0.00),
(33, 5, 1, 'Registration & Matriculation Fee', 650.00),
(34, 5, 2, 'LMS & E-Learning Platform Fee', 500.00),
(35, 5, 3, 'Laboratory & Computer Lab Fee', 1200.00),
(36, 5, 4, 'Library & Learning Media Fee', 450.00),
(37, 5, 5, 'Medical & Dental Clinic Fee', 300.00),
(38, 5, 6, 'Student Activities & Athletic Fee', 350.00),
(39, 5, 7, 'Miscellaneous & Development Fee', 1500.00),
(40, 5, 8, 'Subject Units & Academic Tuition', 0.00),
(57, 6, 1, 'Registration & Matriculation Fee', 650.00),
(58, 6, 2, 'LMS & E-Learning Platform Fee', 500.00),
(59, 6, 3, 'Laboratory & Computer Lab Fee', 1200.00),
(60, 6, 4, 'Library & Learning Media Fee', 450.00),
(61, 6, 5, 'Medical & Dental Clinic Fee', 300.00),
(62, 6, 6, 'Student Activities & Athletic Fee', 350.00),
(63, 6, 7, 'Miscellaneous & Development Fee', 1500.00),
(64, 6, 8, 'Subject Units & Academic Tuition', 5000.00);
--> statement-breakpoint

INSERT INTO "payments" ("id", "tuition_fee_id", "student_id", "or_number", "amount", "payment_method", "notes", "paid_at", "created_at") VALUES
(1, 1, 1, 'OR-2024-00001', 5000.00, 'online', 'First installment payment via portal', '2024-09-01 10:30:00', '2026-09-21 21:02:14'),
(2, 1, 1, 'OR-2026-56852', 5000.00, 'online', 'Payment via Student Portal', '2026-09-22 23:19:35', '2026-09-22 23:19:35'),
(3, 6, 3, 'OR-2026-36685', 1000.00, 'online', 'Payment via Student Portal', '2026-09-22 23:20:08', '2026-09-22 23:20:08'),
(4, 6, 3, 'OR-2026-24758', 1000.00, 'online', 'Payment via Student Portal', '2026-09-22 23:21:06', '2026-09-22 23:21:06'),
(5, 6, 3, 'OR-2026-35795', 1000.00, 'online', 'Payment via Student Portal', '2026-09-22 23:21:43', '2026-09-22 23:21:43'),
(6, 1, 1, 'OR-2026-54970', 5000.00, 'online', 'Payment via Student Portal', '2026-09-23 22:52:50', '2026-09-23 22:52:50'),
(7, 6, 3, 'OR-2026-19166', 5000.00, 'online', 'Payment via Student Portal', '2026-09-23 22:58:30', '2026-09-23 22:58:30');
--> statement-breakpoint

INSERT INTO "email_logs" ("id", "recipient_email", "subject", "type", "related_id", "status", "error_message", "sent_at") VALUES
(1, 'zackary.22010@gmail.com', 'PayTrack — Accounting Staff Account Created', 'accounting_account_created', NULL, 'sent', NULL, '2026-09-21 21:20:25'),
(2, 'kurtxz74@gmail.com', 'PayTrack — Student Portal Account Created (2022-51498)', 'account_created', 2, 'sent', NULL, '2026-09-22 22:52:08'),
(3, 'demesakurtdaryl@gmail.com', 'PayTrack — Account Access for kurt de mesa', 'account_created', 2, 'sent', NULL, '2026-09-22 22:52:19'),
(4, 'zackary.22010@gmail.com', 'PayTrack Notice: New Student Enrolled (2022-51498) — Assessment Required', 'new_student_accounting_alert', 2, 'sent', NULL, '2026-09-22 22:52:24'),
(5, 'obias6569@gmail.com', 'PayTrack — Student Portal Account Created (2002-09090)', 'account_created', 3, 'sent', NULL, '2026-09-22 22:57:44'),
(6, 'marks000211@gmail.com', 'PayTrack — Account Access for mark flores', 'account_created', 3, 'sent', NULL, '2026-09-22 22:57:49'),
(7, 'zackary.22010@gmail.com', 'PayTrack Notice: New Student Enrolled (2002-09090) — Assessment Required', 'new_student_accounting_alert', 3, 'sent', NULL, '2026-09-22 22:57:54'),
(8, 'obias6569@gmail.com', 'PayTrack — Tuition Assessment: S.Y. 2026-2027 - 1st Semester Tuition', 'tuition_assessed', 2, 'sent', NULL, '2026-09-22 23:08:44'),
(9, 'marks000211@gmail.com', 'PayTrack — Tuition Assessment for mark flores', 'tuition_assessed', 2, 'sent', NULL, '2026-09-22 23:08:48'),
(10, 'obias6569@gmail.com', 'PayTrack — Tuition Assessment: S.Y. 2026-2027 - 1st Semester Tuition', 'tuition_assessed', 3, 'sent', NULL, '2026-09-22 23:08:53'),
(11, 'marks000211@gmail.com', 'PayTrack — Tuition Assessment for mark flores', 'tuition_assessed', 3, 'sent', NULL, '2026-09-22 23:08:59'),
(12, 'obias6569@gmail.com', 'PayTrack — Tuition Assessment: S.Y. 2026-2027 - 1st Semester Tuition', 'tuition_assessed', 4, 'sent', NULL, '2026-09-22 23:09:04'),
(13, 'marks000211@gmail.com', 'PayTrack — Tuition Assessment for mark flores', 'tuition_assessed', 4, 'sent', NULL, '2026-09-22 23:09:11'),
(14, 'obias6569@gmail.com', 'PayTrack — Tuition Assessment: S.Y. 2026-2027 - 1st Semester Tuition', 'tuition_assessed', 5, 'sent', NULL, '2026-09-22 23:09:16'),
(15, 'marks000211@gmail.com', 'PayTrack — Tuition Assessment for mark flores', 'tuition_assessed', 5, 'sent', NULL, '2026-09-22 23:09:21'),
(16, 'obias6569@gmail.com', 'PayTrack — Tuition Assessment: S.Y. 2026-2027 - 1st Semester Tuition', 'tuition_assessed', 6, 'sent', NULL, '2026-09-22 23:09:28'),
(17, 'marks000211@gmail.com', 'PayTrack — Tuition Assessment for mark flores', 'tuition_assessed', 6, 'sent', NULL, '2026-09-22 23:09:34'),
(18, 'juan.delacruz@email.com', 'Payment Receipt: OR-2026-56852', 'payment_confirmation', 1, 'sent', NULL, '2026-09-22 23:19:39'),
(19, 'maria.delacruz@email.com', 'Payment Confirmation: OR-2026-56852', 'payment_confirmation', 1, 'sent', NULL, '2026-09-22 23:19:44'),
(20, 'obias6569@gmail.com', 'Payment Receipt: OR-2026-36685', 'payment_confirmation', 6, 'sent', NULL, '2026-09-22 23:20:13'),
(21, 'marks000211@gmail.com', 'Payment Confirmation: OR-2026-36685', 'payment_confirmation', 6, 'sent', NULL, '2026-09-22 23:20:17'),
(22, 'obias6569@gmail.com', 'Payment Receipt: OR-2026-24758', 'payment_confirmation', 6, 'sent', NULL, '2026-09-22 23:21:11'),
(23, 'marks000211@gmail.com', 'Payment Confirmation: OR-2026-24758', 'payment_confirmation', 6, 'sent', NULL, '2026-09-22 23:21:16'),
(24, 'obias6569@gmail.com', 'Payment Receipt: OR-2026-35795', 'payment_confirmation', 6, 'sent', NULL, '2026-09-22 23:21:47'),
(25, 'marks000211@gmail.com', 'Payment Confirmation: OR-2026-35795', 'payment_confirmation', 6, 'sent', NULL, '2026-09-22 23:21:52'),
(26, 'juan.delacruz@email.com', 'Payment Receipt: OR-2026-54970', 'payment_confirmation', 1, 'sent', NULL, '2026-09-23 22:52:57'),
(27, 'maria.delacruz@email.com', 'Payment Confirmation: OR-2026-54970', 'payment_confirmation', 1, 'sent', NULL, '2026-09-23 22:52:59'),
(28, 'obias6569@gmail.com', 'PayTrack — Tuition Assessment: S.Y. 2026-2027 - 1st Semester Tuition', 'tuition_assessed', 6, 'sent', NULL, '2026-09-23 22:55:24'),
(29, 'marks000211@gmail.com', 'PayTrack — Tuition Assessment for mark flores', 'tuition_assessed', 6, 'sent', NULL, '2026-09-23 22:55:27'),
(30, 'obias6569@gmail.com', 'PayTrack — Tuition Assessment: S.Y. 2026-2027 - 1st Semester Tuition', 'tuition_assessed', 6, 'sent', NULL, '2026-09-23 22:57:34'),
(31, 'marks000211@gmail.com', 'PayTrack — Tuition Assessment for mark flores', 'tuition_assessed', 6, 'sent', NULL, '2026-09-23 22:57:36'),
(32, 'obias6569@gmail.com', 'Payment Receipt: OR-2026-19166', 'payment_confirmation', 6, 'sent', NULL, '2026-09-23 22:58:34'),
(33, 'marks000211@gmail.com', 'Payment Confirmation: OR-2026-19166', 'payment_confirmation', 6, 'sent', NULL, '2026-09-23 22:58:36');
--> statement-breakpoint

SELECT setval(pg_get_serial_sequence('"users"', 'id'), COALESCE((SELECT MAX("id") FROM "users"), 1));
--> statement-breakpoint
SELECT setval(pg_get_serial_sequence('"students"', 'id'), COALESCE((SELECT MAX("id") FROM "students"), 1));
--> statement-breakpoint
SELECT setval(pg_get_serial_sequence('"fee_categories"', 'id'), COALESCE((SELECT MAX("id") FROM "fee_categories"), 1));
--> statement-breakpoint
SELECT setval(pg_get_serial_sequence('"tuition_fees"', 'id'), COALESCE((SELECT MAX("id") FROM "tuition_fees"), 1));
--> statement-breakpoint
SELECT setval(pg_get_serial_sequence('"tuition_fee_items"', 'id'), COALESCE((SELECT MAX("id") FROM "tuition_fee_items"), 1));
--> statement-breakpoint
SELECT setval(pg_get_serial_sequence('"payments"', 'id'), COALESCE((SELECT MAX("id") FROM "payments"), 1));
--> statement-breakpoint
SELECT setval(pg_get_serial_sequence('"email_logs"', 'id'), COALESCE((SELECT MAX("id") FROM "email_logs"), 1));
