-- cneduc_schema.sql
-- Database schema for CnEduc - Uganda school curriculum (Primary P1-P7, Secondary S1-S6, University)

CREATE DATABASE IF NOT EXISTS `cneduc` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cneduc`;

-- Levels (Primary, Secondary, University)
CREATE TABLE IF NOT EXISTS `levels` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `slug` VARCHAR(100) NOT NULL UNIQUE
);

-- Classes (P1-P7 for Primary, S1-S6 for Secondary)
CREATE TABLE IF NOT EXISTS `classes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `level_id` INT NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `position` INT DEFAULT 0,
  FOREIGN KEY (`level_id`) REFERENCES `levels`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `level_class` (`level_id`, `name`)
);

-- Subjects per class (e.g., P1 has Math, English, Science, Social Studies)
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `class_id` INT NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `position` INT DEFAULT 0,
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE
);

-- Topics for each subject (e.g., P1 Math has topics like Numbers, Addition, Subtraction)
CREATE TABLE IF NOT EXISTS `topics` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `subject_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT,
  `position` INT DEFAULT 0,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE
);

-- University courses
CREATE TABLE IF NOT EXISTS `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT
);

-- Units (course units) for university courses
CREATE TABLE IF NOT EXISTS `units` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `course_id` INT NOT NULL,
  `code` VARCHAR(50),
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT,
  `position` INT DEFAULT 0,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
);

-- User accounts for student login
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(100),
  `last_name` VARCHAR(100),
  `is_active` BOOLEAN DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`email`)
);

-- Admin users for secure login
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Video tutorials attached to topics
CREATE TABLE IF NOT EXISTS `topic_videos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `topic_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `video_url` VARCHAR(500) NOT NULL,
  `video_provider` ENUM('youtube', 'vimeo', 'local') DEFAULT 'youtube',
  `duration_seconds` INT,
  `position` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`topic_id`) REFERENCES `topics`(`id`) ON DELETE CASCADE
);

-- PDF resources attached to topics
CREATE TABLE IF NOT EXISTS `topic_resources` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `topic_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `file_path` VARCHAR(500) NOT NULL,
  `file_size` INT,
  `file_type` VARCHAR(20),
  `position` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`topic_id`) REFERENCES `topics`(`id`) ON DELETE CASCADE
);

-- Unit resources (videos and PDFs for university units)
CREATE TABLE IF NOT EXISTS `unit_videos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `unit_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `video_url` VARCHAR(500) NOT NULL,
  `video_provider` ENUM('youtube', 'vimeo', 'local') DEFAULT 'youtube',
  `duration_seconds` INT,
  `position` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`unit_id`) REFERENCES `units`(`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `unit_resources` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `unit_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `file_path` VARCHAR(500) NOT NULL,
  `file_size` INT,
  `file_type` VARCHAR(20),
  `position` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`unit_id`) REFERENCES `units`(`id`) ON DELETE CASCADE
);

-- User progress tracking for classes/subjects
CREATE TABLE IF NOT EXISTS `user_progress_subjects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `class_id` INT NOT NULL,
  `subject_id` INT NOT NULL,
  `current_topic_id` INT,
  `topics_completed` INT DEFAULT 0,
  `total_topics` INT DEFAULT 0,
  `progress_percentage` INT DEFAULT 0,
  `last_accessed` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`class_id`) REFERENCES `classes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`subject_id`) REFERENCES `subjects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`current_topic_id`) REFERENCES `topics`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `user_subject` (`user_id`, `subject_id`)
);

-- User progress tracking for university courses/units
CREATE TABLE IF NOT EXISTS `user_progress_courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `course_id` INT NOT NULL,
  `current_unit_id` INT,
  `units_completed` INT DEFAULT 0,
  `total_units` INT DEFAULT 0,
  `progress_percentage` INT DEFAULT 0,
  `last_accessed` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`current_unit_id`) REFERENCES `units`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `user_course` (`user_id`, `course_id`)
);

-- Track completed topics per user
CREATE TABLE IF NOT EXISTS `user_topic_completion` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `topic_id` INT NOT NULL,
  `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`topic_id`) REFERENCES `topics`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `user_topic` (`user_id`, `topic_id`)
);

-- Track completed units per user
CREATE TABLE IF NOT EXISTS `user_unit_completion` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `unit_id` INT NOT NULL,
  `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`unit_id`) REFERENCES `units`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `user_unit` (`user_id`, `unit_id`)
);

-- User learning sessions (track time spent)
CREATE TABLE IF NOT EXISTS `user_learning_sessions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `topic_id` INT,
  `unit_id` INT,
  `started_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `ended_at` TIMESTAMP NULL,
  `duration_seconds` INT,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`topic_id`) REFERENCES `topics`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`unit_id`) REFERENCES `units`(`id`) ON DELETE SET NULL
);

-- Sample seed data
INSERT INTO `levels` (`name`, `description`, `slug`) VALUES
('Primary', 'Primary school level (P1 - P7)', 'primary'),
('Secondary', 'Secondary school level (S1 - S6)', 'secondary'),
('University', 'Tertiary / University level', 'university');

-- Primary classes (P1-P7)
INSERT INTO `classes` (`level_id`, `name`, `description`, `position`) VALUES
((SELECT id FROM levels WHERE slug='primary'), 'P1', 'Primary One', 1),
((SELECT id FROM levels WHERE slug='primary'), 'P2', 'Primary Two', 2),
((SELECT id FROM levels WHERE slug='primary'), 'P3', 'Primary Three', 3),
((SELECT id FROM levels WHERE slug='primary'), 'P4', 'Primary Four', 4),
((SELECT id FROM levels WHERE slug='primary'), 'P5', 'Primary Five', 5),
((SELECT id FROM levels WHERE slug='primary'), 'P6', 'Primary Six', 6),
((SELECT id FROM levels WHERE slug='primary'), 'P7', 'Primary Seven', 7);

-- Secondary classes (S1-S6)
INSERT INTO `classes` (`level_id`, `name`, `description`, `position`) VALUES
((SELECT id FROM levels WHERE slug='secondary'), 'S1', 'Secondary One', 1),
((SELECT id FROM levels WHERE slug='secondary'), 'S2', 'Secondary Two', 2),
((SELECT id FROM levels WHERE slug='secondary'), 'S3', 'Secondary Three', 3),
((SELECT id FROM levels WHERE slug='secondary'), 'S4', 'Secondary Four', 4),
((SELECT id FROM levels WHERE slug='secondary'), 'S5', 'Secondary Five', 5),
((SELECT id FROM levels WHERE slug='secondary'), 'S6', 'Secondary Six', 6);

-- Subjects for P1 (Mathematics, English, Science, Social Studies)
INSERT INTO `subjects` (`class_id`, `name`, `description`, `position`) VALUES
((SELECT id FROM classes WHERE name='P1' AND level_id=(SELECT id FROM levels WHERE slug='primary')), 'Mathematics', 'P1 Mathematics', 1),
((SELECT id FROM classes WHERE name='P1' AND level_id=(SELECT id FROM levels WHERE slug='primary')), 'English', 'P1 English', 2),
((SELECT id FROM classes WHERE name='P1' AND level_id=(SELECT id FROM levels WHERE slug='primary')), 'Science', 'P1 Science', 3),
((SELECT id FROM classes WHERE name='P1' AND level_id=(SELECT id FROM levels WHERE slug='primary')), 'Social Studies', 'P1 Social Studies', 4);

-- Subjects for P2
INSERT INTO `subjects` (`class_id`, `name`, `description`, `position`) VALUES
((SELECT id FROM classes WHERE name='P2' AND level_id=(SELECT id FROM levels WHERE slug='primary')), 'Mathematics', 'P2 Mathematics', 1),
((SELECT id FROM classes WHERE name='P2' AND level_id=(SELECT id FROM levels WHERE slug='primary')), 'English', 'P2 English', 2),
((SELECT id FROM classes WHERE name='P2' AND level_id=(SELECT id FROM levels WHERE slug='primary')), 'Science', 'P2 Science', 3),
((SELECT id FROM classes WHERE name='P2' AND level_id=(SELECT id FROM levels WHERE slug='primary')), 'Social Studies', 'P2 Social Studies', 4);

-- Sample topics for P1 Mathematics
INSERT INTO `topics` (`subject_id`, `title`, `content`, `position`) VALUES
((SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1' AND level_id=(SELECT id FROM levels WHERE slug='primary')) AND name='Mathematics'), 'Numbers 1-10', 'Learning to count and recognize numbers from 1 to 10.', 1),
((SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1' AND level_id=(SELECT id FROM levels WHERE slug='primary')) AND name='Mathematics'), 'Addition Basics', 'Introduction to simple addition with small numbers.', 2),
((SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1' AND level_id=(SELECT id FROM levels WHERE slug='primary')) AND name='Mathematics'), 'Subtraction Basics', 'Introduction to simple subtraction.', 3);

-- Sample topics for P1 English
INSERT INTO `topics` (`subject_id`, `title`, `content`, `position`) VALUES
((SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1' AND level_id=(SELECT id FROM levels WHERE slug='primary')) AND name='English'), 'Letter Recognition', 'Learning uppercase and lowercase letters.', 1),
((SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1' AND level_id=(SELECT id FROM levels WHERE slug='primary')) AND name='English'), 'Simple Words', 'Reading and writing simple 3-letter words.', 2),
((SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1' AND level_id=(SELECT id FROM levels WHERE slug='primary')) AND name='English'), 'Sight Words', 'Common sight words for early readers.', 3);

-- Subjects for S1 (Secondary One)
INSERT INTO `subjects` (`class_id`, `name`, `description`, `position`) VALUES
((SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')), 'Mathematics', 'S1 Mathematics', 1),
((SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')), 'English', 'S1 English', 2),
((SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')), 'Biology', 'S1 Biology', 3),
((SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')), 'Chemistry', 'S1 Chemistry', 4),
((SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')), 'Physics', 'S1 Physics', 5),
((SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')), 'History', 'S1 History', 6),
((SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')), 'Geography', 'S1 Geography', 7),
((SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')), 'Social Studies', 'S1 Social Studies', 8);

-- Sample topics for S1 Biology
INSERT INTO `topics` (`subject_id`, `title`, `content`, `position`) VALUES
((SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')) AND name='Biology'), 'Cell Structure', 'Understanding prokaryotic and eukaryotic cells.', 1),
((SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')) AND name='Biology'), 'Classification of Organisms', 'Kingdom, phylum, class, order, family, genus, species.', 2),
((SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='S1' AND level_id=(SELECT id FROM levels WHERE slug='secondary')) AND name='Biology'), 'Nutrition and Digestion', 'How organisms get and use energy.', 3);

-- University courses
INSERT INTO `courses` (`name`, `description`) VALUES
('BSc Computer Science', 'Bachelor of Science in Computer Science'),
('BA Education', 'Bachelor of Arts in Education');

-- University units
INSERT INTO `units` (`course_id`, `code`, `title`, `content`, `position`) VALUES
((SELECT id FROM courses WHERE name='BSc Computer Science'), 'CS101', 'Introduction to Programming', 'Content: programming basics (variables, loops, functions).', 1),
((SELECT id FROM courses WHERE name='BSc Computer Science'), 'CS102', 'Data Structures', 'Content: arrays, linked lists, trees.', 2),
((SELECT id FROM courses WHERE name='BA Education'), 'EDU101', 'Foundations of Education', 'Content: history & philosophy of education.', 1);

-- Default admin user: username=admin, password=password (CHANGE THIS!)
-- Password hash generated by password_hash('password', PASSWORD_BCRYPT)
INSERT INTO `admin_users` (`username`, `password_hash`) VALUES
('admin', '$2y$10$Y0M/QdFr7b5HCCIwKVvKFebLRDNWl8dO/h/Gk/MG8hMdQhLQYLFfq');

-- Sample student users
INSERT INTO `users` (`email`, `password_hash`, `first_name`, `last_name`) VALUES
('student1@example.com', '$2y$10$Y0M/QdFr7b5HCCIwKVvKFebLRDNWl8dO/h/Gk/MG8hMdQhLQYLFfq', 'John', 'Doe'),
('student2@example.com', '$2y$10$Y0M/QdFr7b5HCCIwKVvKFebLRDNWl8dO/h/Gk/MG8hMdQhLQYLFfq', 'Jane', 'Smith');

-- Sample topic videos for P1 Mathematics
INSERT INTO `topic_videos` (`topic_id`, `title`, `description`, `video_url`, `video_provider`, `duration_seconds`, `position`) VALUES
((SELECT id FROM topics WHERE title='Numbers 1-10' AND subject_id=(SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1') AND name='Mathematics')), 'Counting 1 to 10', 'Learn to count numbers from 1 to 10 with visual aids', 'dQw4w9WgXcQ', 'youtube', 300, 1),
((SELECT id FROM topics WHERE title='Addition Basics' AND subject_id=(SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1') AND name='Mathematics')), 'Simple Addition', 'Introduction to adding small numbers', 'jNQXAC9IVRw', 'youtube', 420, 1);

-- Sample topic resources (PDFs) for P1 Mathematics
INSERT INTO `topic_resources` (`topic_id`, `title`, `description`, `file_path`, `file_size`, `file_type`, `position`) VALUES
((SELECT id FROM topics WHERE title='Numbers 1-10' AND subject_id=(SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1') AND name='Mathematics')), 'Numbers Worksheet', 'Practice sheet for numbers 1-10', '/uploads/resources/p1_math_numbers.pdf', 1024000, 'pdf', 1),
((SELECT id FROM topics WHERE title='Addition Basics' AND subject_id=(SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1') AND name='Mathematics')), 'Addition Practice', 'Worksheet with addition problems', '/uploads/resources/p1_math_addition.pdf', 1024000, 'pdf', 1);

-- Sample unit videos for university courses
INSERT INTO `unit_videos` (`unit_id`, `title`, `description`, `video_url`, `video_provider`, `duration_seconds`, `position`) VALUES
((SELECT id FROM units WHERE code='CS101'), 'Programming Basics', 'Introduction to programming concepts and variables', 'PrAw-gHWnSM', 'youtube', 900, 1),
((SELECT id FROM units WHERE code='CS102'), 'Arrays Explained', 'Understanding arrays and their operations', 'TSC9mS16R7w', 'youtube', 1200, 1);

-- Sample unit resources (PDFs)
INSERT INTO `unit_resources` (`unit_id`, `title`, `description`, `file_path`, `file_size`, `file_type`, `position`) VALUES
((SELECT id FROM units WHERE code='CS101'), 'Programming Guide', 'Complete guide to programming basics', '/uploads/resources/cs101_guide.pdf', 2048000, 'pdf', 1),
((SELECT id FROM units WHERE code='CS102'), 'Data Structures Reference', 'Reference manual for data structures', '/uploads/resources/cs102_reference.pdf', 3048000, 'pdf', 1);

-- Sample user progress for subjects
INSERT INTO `user_progress_subjects` (`user_id`, `class_id`, `subject_id`, `current_topic_id`, `topics_completed`, `total_topics`, `progress_percentage`) VALUES
((SELECT id FROM users WHERE email='student1@example.com'), (SELECT id FROM classes WHERE name='P1'), (SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1') AND name='Mathematics'), (SELECT id FROM topics WHERE title='Numbers 1-10' LIMIT 1), 1, 3, 33),
((SELECT id FROM users WHERE email='student1@example.com'), (SELECT id FROM classes WHERE name='P1'), (SELECT id FROM subjects WHERE class_id=(SELECT id FROM classes WHERE name='P1') AND name='English'), (SELECT id FROM topics WHERE title='Letter Recognition' LIMIT 1), 0, 3, 0);

-- Sample user progress for courses
INSERT INTO `user_progress_courses` (`user_id`, `course_id`, `current_unit_id`, `units_completed`, `total_units`, `progress_percentage`) VALUES
((SELECT id FROM users WHERE email='student2@example.com'), (SELECT id FROM courses WHERE name='BSc Computer Science'), (SELECT id FROM units WHERE code='CS101'), 0, 2, 0);

-- Sample completed topics
INSERT INTO `user_topic_completion` (`user_id`, `topic_id`) VALUES
((SELECT id FROM users WHERE email='student1@example.com'), (SELECT id FROM topics WHERE title='Numbers 1-10' LIMIT 1));

-- Questions and Answers for topics
CREATE TABLE IF NOT EXISTS `topic_questions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `topic_id` INT NOT NULL,
  `user_name` VARCHAR(100),
  `user_email` VARCHAR(255),
  `question_title` VARCHAR(255) NOT NULL,
  `question_text` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_approved` TINYINT(1) DEFAULT 1,
  FOREIGN KEY (`topic_id`) REFERENCES `topics`(`id`) ON DELETE CASCADE,
  INDEX `idx_topic_created` (`topic_id`, `created_at` DESC)
);

CREATE TABLE IF NOT EXISTS `topic_question_answers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question_id` INT NOT NULL,
  `user_name` VARCHAR(100),
  `user_email` VARCHAR(255),
  `answer_text` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_approved` TINYINT(1) DEFAULT 1,
  `helpful_count` INT DEFAULT 0,
  FOREIGN KEY (`question_id`) REFERENCES `topic_questions`(`id`) ON DELETE CASCADE,
  INDEX `idx_question_created` (`question_id`, `created_at` DESC)
);

-- End of file
