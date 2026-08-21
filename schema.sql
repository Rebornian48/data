-- ============================================================================
-- JKT48 Database Schema (MySQL / MariaDB)
-- ============================================================================
-- Designed based on the JKT48 Member Database Excel structure.
-- Normalized into related tables with proper foreign keys.
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `member_singles`;
DROP TABLE IF EXISTS `captains`;
DROP TABLE IF EXISTS `members`;
DROP TABLE IF EXISTS `singles`;
DROP TABLE IF EXISTS `generations`;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- Table: generations
-- Master data for member generations (1-13, V1, V2, Kaigai 1/2, Transfer)
-- ----------------------------------------------------------------------------
CREATE TABLE `generations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE COMMENT 'Generation code: 1-13, V1, V2, Kaigai 1, etc',
    `name` VARCHAR(100) NOT NULL COMMENT 'Display name: Generasi 1, Vocal 1, etc',
    `announcement_date` DATE NULL COMMENT 'When this generation was announced',
    `join_date` DATE NULL COMMENT 'Official first day',
    `description` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_generations_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: singles
-- Master data for JKT48 singles (S1 - S26+)
-- ----------------------------------------------------------------------------
CREATE TABLE `singles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(20) NOT NULL UNIQUE COMMENT 'S1, S2, S19 (EK), etc',
    `title` VARCHAR(255) NOT NULL COMMENT 'Song title',
    `release_date` DATE NULL,
    `sequence` INT NOT NULL COMMENT 'Order for sorting',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_singles_sequence` (`sequence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: members
-- Core member data with all biographical and career milestone fields
-- ----------------------------------------------------------------------------
CREATE TABLE `members` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL COMMENT 'Full name',
    `nickname` VARCHAR(100) NULL,
    `birth_place` VARCHAR(150) NULL,
    `birth_date` DATE NULL,
    `generation_id` INT UNSIGNED NOT NULL,

    -- Career timeline
    `join_date` DATE NULL COMMENT 'Date joined JKT48',
    `cancelled_date` DATE NULL COMMENT 'Date cancelled (for gen 10 kenkyuusei)',
    `rejoin_date` DATE NULL COMMENT 'Date rejoined',
    `promotion_date` DATE NULL COMMENT 'Promotion to core team',
    `graduation_announce_date` DATE NULL,
    `graduation_announce_event` VARCHAR(255) NULL,
    `graduation_date` DATE NULL,

    -- Status
    `status` ENUM('Aktif','Lulus') NOT NULL DEFAULT 'Aktif',
    `restructure_status` VARCHAR(100) NULL COMMENT 'Kategori terkait restrukturisasi 2021',

    -- Media
    `photo_url` VARCHAR(500) NULL,
    `bio` TEXT NULL,

    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (`generation_id`) REFERENCES `generations`(`id`) ON DELETE RESTRICT,
    INDEX `idx_members_name` (`name`),
    INDEX `idx_members_status` (`status`),
    INDEX `idx_members_generation` (`generation_id`),
    INDEX `idx_members_join_date` (`join_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: member_singles
-- Pivot: which members appeared in which singles (senbatsu) + role
-- ----------------------------------------------------------------------------
CREATE TABLE `member_singles` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id` INT UNSIGNED NOT NULL,
    `single_id` INT UNSIGNED NOT NULL,
    `role` ENUM('member','center') NOT NULL DEFAULT 'member',
    `position` INT NULL COMMENT 'Position in formation (optional)',
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (`member_id`) REFERENCES `members`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`single_id`) REFERENCES `singles`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `uq_member_single` (`member_id`, `single_id`),
    INDEX `idx_ms_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table: captains
-- Historical record of captains (JKT48, Team J, Team KIII, Vice Captain)
-- ----------------------------------------------------------------------------
CREATE TABLE `captains` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `member_id` INT UNSIGNED NOT NULL,
    `position` ENUM(
        'Kapten JKT48',
        'Wakil Kapten JKT48',
        'Kapten Tim J',
        'Kapten Tim KIII',
        'Kapten Tim T'
    ) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NULL COMMENT 'NULL = still active',
    `notes` TEXT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (`member_id`) REFERENCES `members`(`id`) ON DELETE CASCADE,
    INDEX `idx_captains_position` (`position`),
    INDEX `idx_captains_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Useful Views for Dashboard
-- ============================================================================

DROP VIEW IF EXISTS `v_member_stats`;
CREATE VIEW `v_member_stats` AS
SELECT
    m.id,
    m.name,
    m.nickname,
    m.status,
    g.code AS generation_code,
    g.name AS generation_name,
    m.birth_date,
    m.join_date,
    m.graduation_date,
    TIMESTAMPDIFF(YEAR, m.birth_date, CURDATE()) AS age_current,
    TIMESTAMPDIFF(YEAR, m.birth_date, m.join_date) AS age_at_join,
    CASE
        WHEN m.graduation_date IS NULL THEN DATEDIFF(CURDATE(), m.join_date)
        ELSE DATEDIFF(m.graduation_date, m.join_date)
    END AS days_in_jkt48,
    (SELECT COUNT(*) FROM member_singles ms WHERE ms.member_id = m.id) AS total_senbatsu,
    (SELECT COUNT(*) FROM member_singles ms WHERE ms.member_id = m.id AND ms.role = 'center') AS total_center
FROM members m
JOIN generations g ON g.id = m.generation_id;

DROP VIEW IF EXISTS `v_generation_summary`;
CREATE VIEW `v_generation_summary` AS
SELECT
    g.id,
    g.code,
    g.name,
    g.join_date,
    COUNT(m.id) AS total_members,
    SUM(CASE WHEN m.status = 'Aktif' THEN 1 ELSE 0 END) AS active_members,
    SUM(CASE WHEN m.status = 'Lulus' THEN 1 ELSE 0 END) AS graduated_members,
    MIN(m.birth_date) AS oldest_member_birth,
    MAX(m.birth_date) AS youngest_member_birth
FROM generations g
LEFT JOIN members m ON m.generation_id = g.id
GROUP BY g.id, g.code, g.name, g.join_date;
