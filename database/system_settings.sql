-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 02:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ita_assessment`
--

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_category` varchar(50) NOT NULL COMMENT 'หมวดหมู่การตั้งค่า',
  `setting_key` varchar(100) NOT NULL COMMENT 'ชื่อการตั้งค่า',
  `setting_value` text DEFAULT NULL COMMENT 'ค่า',
  `setting_type` enum('string','integer','boolean','json','text') NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_encrypted` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'การเข้ารหัส',
  `is_public` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'แสดงให้ผู้ใช้ทั่วไป',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='การตั้งค่าระบบ';

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_category`, `setting_key`, `setting_value`, `setting_type`, `description`, `is_encrypted`, `is_public`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 'general', 'site_name', 'ระบบจัดการผู้ใช้งาน', 'string', 'ชื่อระบบ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:33:03', 2),
(2, 'general', 'site_description', 'ระบบจัดการผู้ใช้งานด้วย Yii2', 'string', 'คำอธิบายระบบ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:33:03', 2),
(3, 'general', 'admin_email', 'admin@pbri.ac.th', 'string', 'อีเมลผู้ดูแลระบบ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:33:03', 2),
(4, 'general', 'support_email', 'support@pbri.ac.th', 'string', 'อีเมลสำหรับติดต่อ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(5, 'general', 'items_per_page', '20', 'integer', 'จำนวนรายการต่อหน้า', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:33:03', 2),
(6, 'general', 'timezone', 'Asia/Bangkok', 'string', 'เขตเวลา', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:33:03', 2),
(7, 'general', 'date_format', 'd/m/Y', 'string', 'รูปแบบวันที่', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(8, 'general', 'maintenance_mode', '0', 'boolean', 'โหมดปิดปรับปรุง', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(9, 'general', 'allow_registration', '0', 'boolean', 'อนุญาตให้สมัครสมาชิก', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(10, 'security', 'password_min_length', '12', 'integer', 'ความยาวรหัสผ่านขั้นต่ำ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(11, 'security', 'password_require_uppercase', '1', 'boolean', 'ต้องมีตัวพิมพ์ใหญ่', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(12, 'security', 'password_require_lowercase', '1', 'boolean', 'ต้องมีตัวพิมพ์เล็ก', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(13, 'security', 'password_require_number', '1', 'boolean', 'ต้องมีตัวเลข', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(14, 'security', 'password_require_special', '1', 'boolean', 'ต้องมีอักขระพิเศษ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(15, 'security', 'password_history_count', '5', 'integer', 'จำนวนรหัสผ่านที่ห้ามซ้ำ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(16, 'security', 'login_max_attempts', '5', 'integer', 'จำนวนครั้งที่ล็อกอินผิดได้', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(17, 'security', 'login_lockout_duration', '900', 'integer', 'เวลาล็อค (วินาที)', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(18, 'security', 'session_timeout', '3600', 'integer', 'หมดเวลา session (วินาที)', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(19, 'security', 'captcha_threshold', '3', 'integer', 'แสดง CAPTCHA หลังล็อกอินผิดกี่ครั้ง', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(20, 'security', 'password_reset_expire', '3600', 'integer', 'หมดเวลา reset password (วินาที)', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(21, 'security', 'two_factor_enabled', '0', 'boolean', 'เปิดใช้ 2FA', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(22, 'smtp', 'enabled', '0', 'boolean', 'เปิดใช้งาน SMTP', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(23, 'smtp', 'host', 'smtp.example.com', 'string', 'SMTP Server', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(24, 'smtp', 'port', '587', 'integer', 'SMTP Port', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(25, 'smtp', 'encryption', 'tls', 'string', 'Encryption (tls/ssl)', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(26, 'smtp', 'username', '', 'string', 'SMTP Username', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(27, 'smtp', 'password', '', 'string', 'SMTP Password', 1, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(28, 'smtp', 'from_email', 'noreply@pbri.ac.th', 'string', 'From Email', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(29, 'smtp', 'from_name', 'ระบบจัดการผู้ใช้งาน', 'string', 'From Name', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(30, 'oauth_azure', 'enabled', '0', 'boolean', 'เปิดใช้งาน Azure AD', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(31, 'oauth_azure', 'client_id', '', 'string', 'Azure Client ID', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(32, 'oauth_azure', 'client_secret', '', 'string', 'Azure Client Secret', 1, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(33, 'oauth_azure', 'tenant_id', '', 'string', 'Azure Tenant ID', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(34, 'oauth_azure', 'redirect_uri', '', 'string', 'Redirect URI', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(35, 'oauth_google', 'enabled', '0', 'boolean', 'เปิดใช้งาน Google', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(36, 'oauth_google', 'client_id', '', 'string', 'Google Client ID', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(37, 'oauth_google', 'client_secret', '', 'string', 'Google Client Secret', 1, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(38, 'oauth_google', 'redirect_uri', '', 'string', 'Redirect URI', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(39, 'oauth_thaid', 'enabled', '0', 'boolean', 'เปิดใช้งาน ThaID', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(40, 'oauth_thaid', 'client_id', '', 'string', 'ThaID Client ID', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(41, 'oauth_thaid', 'client_secret', '', 'string', 'ThaID Client Secret', 1, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(42, 'oauth_thaid', 'redirect_uri', '', 'string', 'Redirect URI', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(43, 'general', 'maintenance_message', 'ระบบกำลังปิดปรับปรุง กรุณากลับมาใหม่ภายหลัง', 'string', NULL, NULL, 0, '2025-12-14 20:33:03', '2025-12-14 20:33:03', 2),
(44, 'oauth_google', 'google_client_id', '', 'string', NULL, NULL, 0, '2025-12-14 20:33:13', '2025-12-14 20:33:13', 2),
(45, 'oauth_google', 'google_client_secret', '', 'string', NULL, NULL, 0, '2025-12-14 20:33:13', '2025-12-14 20:33:13', 2),
(46, 'oauth_google', 'google_redirect_uri', '', 'string', NULL, NULL, 0, '2025-12-14 20:33:13', '2025-12-14 20:33:13', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_settings_key` (`setting_category`,`setting_key`),
  ADD KEY `idx_settings_category` (`setting_category`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
