-- ============================================================
--  PAYTRACK v2 — Student Tuition Fee Payment System
--  Database: MariaDB (XAMPP) | Charset: utf8mb4
-- ============================================================

CREATE DATABASE IF NOT EXISTS `paytrack`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `paytrack`;

-- Safeguard: Drop existing tables in reverse dependency order
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `email_logs`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `tuition_fee_items`;
DROP TABLE IF EXISTS `tuition_fees`;
DROP TABLE IF EXISTS `fee_categories`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- 1. USERS (admin + student login accounts)
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `username`         VARCHAR(100)     NOT NULL UNIQUE,
  `password_hash`    VARCHAR(255)     NOT NULL,
  `role`             ENUM('admin','student') NOT NULL DEFAULT 'student',
  `is_first_login`   TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. STUDENTS (student profile details)
-- ------------------------------------------------------------
CREATE TABLE `students` (
  `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `user_id`          INT UNSIGNED     NOT NULL,
  `student_id`       VARCHAR(30)      NOT NULL UNIQUE,
  `first_name`       VARCHAR(80)      NOT NULL,
  `last_name`        VARCHAR(80)      NOT NULL,
  `middle_name`      VARCHAR(80)      NULL,
  `email`            VARCHAR(150)     NOT NULL,
  `grade_level`      VARCHAR(50)      NULL,
  `school_year`      VARCHAR(20)      NULL,
  `parent_name`      VARCHAR(160)     NULL,
  `parent_email`     VARCHAR(150)     NULL,
  `contact_number`   VARCHAR(20)      NULL,
  `address`          TEXT             NULL,
  `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_students_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. FEE CATEGORIES (fee aspects configured by admin)
--    is_variable = 1 means this aspect receives the remainder
-- ------------------------------------------------------------
CREATE TABLE `fee_categories` (
  `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(100)     NOT NULL,
  `code`             VARCHAR(50)      NOT NULL UNIQUE,
  `default_amount`   DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
  `is_variable`      TINYINT(1)       NOT NULL DEFAULT 0,
  `sort_order`       INT              NOT NULL DEFAULT 99,
  `is_active`        TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. TUITION FEES (assigned tuition record per student per term)
-- ------------------------------------------------------------
CREATE TABLE `tuition_fees` (
  `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `student_id`       INT UNSIGNED     NOT NULL,
  `school_year`      VARCHAR(20)      NOT NULL,
  `semester`         VARCHAR(30)      NOT NULL DEFAULT '1st Semester',
  `description`      VARCHAR(200)     NOT NULL,
  `total_amount`     DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
  `amount_paid`      DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
  `due_date`         DATE             NULL,
  `status`           ENUM('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_fees_student`
    FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. TUITION FEE ITEMS (display breakdown aspects for student & admin)
-- ------------------------------------------------------------
CREATE TABLE `tuition_fee_items` (
  `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `tuition_fee_id`   INT UNSIGNED     NOT NULL,
  `fee_category_id`  INT UNSIGNED     NOT NULL,
  `category_name`    VARCHAR(100)     NOT NULL,
  `amount`           DECIMAL(12,2)    NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_items_fee`
    FOREIGN KEY (`tuition_fee_id`) REFERENCES `tuition_fees` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_items_category`
    FOREIGN KEY (`fee_category_id`) REFERENCES `fee_categories` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. PAYMENTS (official receipt transactions)
-- ------------------------------------------------------------
CREATE TABLE `payments` (
  `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `tuition_fee_id`   INT UNSIGNED     NOT NULL,
  `student_id`       INT UNSIGNED     NOT NULL,
  `or_number`        VARCHAR(50)      NOT NULL UNIQUE,
  `amount`           DECIMAL(12,2)    NOT NULL,
  `payment_method`   ENUM('cash','gcash','maya','bank_transfer','online','card','other') NOT NULL DEFAULT 'online',
  `notes`            TEXT             NULL,
  `paid_at`          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_payments_fee`
    FOREIGN KEY (`tuition_fee_id`) REFERENCES `tuition_fees` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_payments_student`
    FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. EMAIL LOGS (audit trail)
-- ------------------------------------------------------------
CREATE TABLE `email_logs` (
  `id`               INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `recipient_email`  VARCHAR(150)     NOT NULL,
  `subject`          VARCHAR(255)     NOT NULL,
  `type`             ENUM('account_created','payment_confirmation','payment_reminder','other') NOT NULL,
  `related_id`       INT UNSIGNED     NULL,
  `status`           ENUM('sent','failed') NOT NULL DEFAULT 'sent',
  `error_message`    TEXT             NULL,
  `sent_at`          DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes
CREATE INDEX `idx_students_student_id`  ON `students`          (`student_id`);
CREATE INDEX `idx_fees_student_id`      ON `tuition_fees`      (`student_id`);
CREATE INDEX `idx_items_fee_id`         ON `tuition_fee_items` (`tuition_fee_id`);
CREATE INDEX `idx_payments_student_id`  ON `payments`          (`student_id`);
CREATE INDEX `idx_payments_or`          ON `payments`          (`or_number`);
