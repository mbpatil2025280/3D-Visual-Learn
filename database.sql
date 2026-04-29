-- Create the database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS `visual_learning`;
USE `visual_learning`;

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert some dummy user data for testing
INSERT IGNORE INTO `users` (`id`, `email`, `password`) VALUES
(1, 'student@example.com', 'password123'),
(2, 'admin@example.com', 'admin123');

-- --------------------------------------------------------

-- Table structure for table `purchases`
CREATE TABLE IF NOT EXISTS `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `course` varchar(255) NOT NULL,
  `purchase_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_user_id` (`user_id`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert some dummy purchase data
INSERT IGNORE INTO `purchases` (`id`, `user_id`, `course`) VALUES
(1, 1, 'Physics 3D Course');

-- --------------------------------------------------------
