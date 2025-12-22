-- Exam System Database Tables Migration
-- Run this to add exam functionality to CnEduc

-- 1. Exams table - Define exam structure
CREATE TABLE IF NOT EXISTS `exams` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_id` INT,
  `unit_id` INT,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `total_questions` INT NOT NULL,
  `time_limit_minutes` INT NOT NULL DEFAULT 60,
  `passing_percentage` INT DEFAULT 50,
  `max_attempts` INT DEFAULT 3,
  `shuffle_questions` BOOLEAN DEFAULT 1,
  `shuffle_answers` BOOLEAN DEFAULT 0,
  `show_results` BOOLEAN DEFAULT 1,
  `created_by` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`unit_id`) REFERENCES `units`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `exam_unique` (`subject_id`, `unit_id`, `title`)
);

-- 2. Exam questions - Link questions to exams
CREATE TABLE IF NOT EXISTS `exam_questions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `exam_id` INT NOT NULL,
  `question_id` INT NOT NULL,
  `question_order` INT DEFAULT 0,
  `points` INT DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`question_id`) REFERENCES `topic_questions`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `exam_question_unique` (`exam_id`, `question_id`)
);

-- 3. Exam attempts - Track when users take exams
CREATE TABLE IF NOT EXISTS `exam_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `exam_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `attempt_number` INT DEFAULT 1,
  `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `submitted_at` TIMESTAMP NULL,
  `time_taken_seconds` INT,
  `score` INT DEFAULT 0,
  `total_points` INT,
  `percentage` DECIMAL(5,2),
  `status` ENUM('in_progress', 'submitted', 'graded') DEFAULT 'in_progress',
  `tab_switches` INT DEFAULT 0,
  `suspicious_activity` BOOLEAN DEFAULT 0,
  `ip_address` VARCHAR(45),
  `browser_info` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`exam_id`) REFERENCES `exams`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `user_exam_idx` (`user_id`, `exam_id`),
  INDEX `status_idx` (`status`)
);

-- 4. Exam answers - Store user's answers
CREATE TABLE IF NOT EXISTS `exam_answers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `attempt_id` INT NOT NULL,
  `question_id` INT NOT NULL,
  `user_answer` LONGTEXT,
  `is_correct` BOOLEAN DEFAULT NULL,
  `points_earned` INT DEFAULT 0,
  `answered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`attempt_id`) REFERENCES `exam_attempts`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`question_id`) REFERENCES `topic_questions`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `attempt_question_unique` (`attempt_id`, `question_id`)
);

-- 5. Exam tab switches - Track tab switches for cheat detection
CREATE TABLE IF NOT EXISTS `exam_tab_switches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `attempt_id` INT NOT NULL,
  `switch_type` ENUM('left', 'returned', 'blur', 'focus') DEFAULT 'left',
  `logged_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`attempt_id`) REFERENCES `exam_attempts`(`id`) ON DELETE CASCADE,
  INDEX `attempt_idx` (`attempt_id`)
);

-- 6. Update topic_questions table to add marking fields
ALTER TABLE `topic_questions` ADD COLUMN `answer_keywords` VARCHAR(500) AFTER `question_text`;
ALTER TABLE `topic_questions` ADD COLUMN `model_answer` TEXT AFTER `answer_keywords`;
ALTER TABLE `topic_questions` ADD COLUMN `marking_type` ENUM('auto', 'manual', 'ai_assisted') DEFAULT 'manual' AFTER `model_answer`;

-- 7. Create index for better performance
CREATE INDEX `idx_exam_user_attempt` ON `exam_attempts`(`exam_id`, `user_id`, `attempt_number`);
CREATE INDEX `idx_attempt_answers` ON `exam_answers`(`attempt_id`);
