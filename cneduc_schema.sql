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

-- Admin users for secure login
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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

-- End of file
