-- Schéma de base de données pour DEAL.BZH
-- MySQL/MariaDB

CREATE DATABASE IF NOT EXISTS `deal_bzh` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `deal_bzh`;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) DEFAULT NULL,
    `last_name` VARCHAR(100) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `role` ENUM('user', 'moderator', 'admin') NOT NULL DEFAULT 'user',
    `rating` DECIMAL(3,2) DEFAULT 0.00,
    `rating_count` INT(11) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    KEY `role` (`role`),
    KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des catégories
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `parent_id` INT(11) UNSIGNED DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `parent_id` (`parent_id`),
    KEY `is_active` (`is_active`),
    FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des annonces (deals)
CREATE TABLE IF NOT EXISTS `deals` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `category_id` INT(11) UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `type` ENUM('exchange', 'swap', 'free_service', 'sale') NOT NULL DEFAULT 'exchange',
    `status` ENUM('draft', 'published', 'moderated', 'archived', 'rejected') NOT NULL DEFAULT 'draft',
    `location` VARCHAR(255) DEFAULT NULL,
    `price` DECIMAL(10,2) DEFAULT NULL,
    `is_negotiable` TINYINT(1) DEFAULT 0,
    `views` INT(11) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `published_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `category_id` (`category_id`),
    KEY `status` (`status`),
    KEY `type` (`type`),
    KEY `published_at` (`published_at`),
    FULLTEXT KEY `search` (`title`, `description`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des images des annonces
CREATE TABLE IF NOT EXISTS `deal_images` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `deal_id` INT(11) UNSIGNED NOT NULL,
    `filename` VARCHAR(255) NOT NULL,
    `original_filename` VARCHAR(255) DEFAULT NULL,
    `file_size` INT(11) DEFAULT NULL,
    `mime_type` VARCHAR(100) DEFAULT NULL,
    `order` INT(11) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `deal_id` (`deal_id`),
    KEY `order` (`order`),
    FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des messages
CREATE TABLE IF NOT EXISTS `messages` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `from_user_id` INT(11) UNSIGNED NOT NULL,
    `to_user_id` INT(11) UNSIGNED NOT NULL,
    `deal_id` INT(11) UNSIGNED DEFAULT NULL,
    `subject` VARCHAR(255) DEFAULT NULL,
    `content` TEXT NOT NULL,
    `status` ENUM('unread', 'read', 'archived', 'moderated', 'deleted') NOT NULL DEFAULT 'unread',
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` TIMESTAMP NULL DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `from_user_id` (`from_user_id`),
    KEY `to_user_id` (`to_user_id`),
    KEY `deal_id` (`deal_id`),
    KEY `status` (`status`),
    KEY `is_read` (`is_read`),
    FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des favoris
CREATE TABLE IF NOT EXISTS `favorites` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `deal_id` INT(11) UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_deal` (`user_id`, `deal_id`),
    KEY `user_id` (`user_id`),
    KEY `deal_id` (`deal_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des notations
CREATE TABLE IF NOT EXISTS `ratings` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `rater_id` INT(11) UNSIGNED NOT NULL,
    `rated_user_id` INT(11) UNSIGNED NOT NULL,
    `deal_id` INT(11) UNSIGNED DEFAULT NULL,
    `score` TINYINT(1) NOT NULL CHECK (`score` >= 1 AND `score` <= 5),
    `comment` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `rater_id` (`rater_id`),
    KEY `rated_user_id` (`rated_user_id`),
    KEY `deal_id` (`deal_id`),
    KEY `score` (`score`),
    FOREIGN KEY (`rater_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`rated_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`deal_id`) REFERENCES `deals` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des catégories de base
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Sonorisation', 'sonorisation', 'Matériel de sonorisation, enceintes, amplis, micros, etc.'),
('Vidéo', 'video', 'Matériel vidéo, caméras, projecteurs, écrans, etc.'),
('Informatique Old School', 'informatique-old-school', 'Ordinateurs rétro, périphériques vintage, etc.'),
('Éclairage', 'eclairage', 'Matériel d''éclairage scénique, projecteurs, etc.'),
('Accessoires', 'accessoires', 'Câbles, supports, accessoires divers');

