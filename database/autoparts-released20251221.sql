-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 21, 2025 at 01:56 PM
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
-- Database: `autoparts`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `model_class` varchar(100) DEFAULT NULL,
  `model_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `customer_code` varchar(20) NOT NULL COMMENT 'รหัสลูกค้า',
  `customer_type` varchar(20) NOT NULL DEFAULT 'retail' COMMENT 'ประเภทลูกค้า',
  `full_name` varchar(255) NOT NULL COMMENT 'ชื่อ-นามสกุล',
  `company_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อบริษัท/ร้าน',
  `tax_id` varchar(20) DEFAULT NULL COMMENT 'เลขประจำตัวผู้เสียภาษี',
  `phone` varchar(20) NOT NULL COMMENT 'เบอร์โทรศัพท์',
  `phone2` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรสำรอง',
  `email` varchar(255) DEFAULT NULL COMMENT 'อีเมล',
  `line_id` varchar(100) DEFAULT NULL COMMENT 'Line ID',
  `facebook` varchar(255) NOT NULL COMMENT 'Facebook',
  `address` text DEFAULT NULL COMMENT 'address',
  `province` varchar(100) DEFAULT NULL COMMENT 'จังหวัด',
  `district` varchar(100) DEFAULT NULL COMMENT 'อำเภอ/เขต',
  `postal_code` varchar(10) DEFAULT NULL COMMENT 'รหัสไปรษณีย์',
  `shipping_address` text DEFAULT NULL COMMENT 'ที่อยู่จัดส่ง',
  `notes` text DEFAULT NULL COMMENT 'หมายเหตุ',
  `credit_limit` decimal(12,2) DEFAULT 0.00 COMMENT 'วงเงินเครดิต',
  `discount_percent` decimal(12,1) NOT NULL COMMENT 'ส่วนลด',
  `total_purchases` decimal(12,2) DEFAULT 0.00 COMMENT 'ยอดซื้อสะสม',
  `total_orders` int(11) DEFAULT 0 COMMENT 'จำนวนออเดอร์',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'สถานะ',
  `created_at` int(11) NOT NULL COMMENT 'วันที่สร้าง',
  `updated_at` int(11) NOT NULL COMMENT 'วันที่แก้ไข'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_vehicle`
--

CREATE TABLE `customer_vehicle` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `model_id` int(11) DEFAULT NULL,
  `engine_type_id` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `vin` varchar(17) DEFAULT NULL,
  `license_plate` varchar(20) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `mileage` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `engine_type`
--

CREATE TABLE `engine_type` (
  `id` int(11) NOT NULL,
  `model_id` int(11) NOT NULL,
  `engine_code` varchar(50) NOT NULL,
  `displacement` decimal(3,1) DEFAULT NULL,
  `fuel_type` varchar(20) DEFAULT NULL,
  `power_hp` int(11) DEFAULT NULL,
  `torque_nm` int(11) DEFAULT NULL,
  `year_start` int(11) DEFAULT NULL,
  `year_end` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry`
--

CREATE TABLE `inquiry` (
  `id` int(11) NOT NULL,
  `inquiry_number` varchar(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_line_id` varchar(100) DEFAULT NULL,
  `channel` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'open',
  `priority` varchar(10) DEFAULT 'normal',
  `subject` varchar(255) DEFAULT NULL,
  `vehicle_info` text DEFAULT NULL,
  `requested_parts` text DEFAULT NULL,
  `quoted_amount` decimal(12,2) DEFAULT NULL,
  `converted_order_id` int(11) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `closed_at` int(11) DEFAULT NULL,
  `closed_reason` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_message`
--

CREATE TABLE `inquiry_message` (
  `id` int(11) NOT NULL,
  `inquiry_id` int(11) NOT NULL,
  `sender_type` varchar(20) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `attachments` text DEFAULT NULL,
  `is_auto_reply` tinyint(1) DEFAULT 0,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1766130719),
('m130524_201442_init', 1766130722),
('m190124_110200_add_verification_token_column_to_user_table', 1766130722),
('m241218_000001_create_autoparts_tables', 1766131059),
('m241218_000002_seed_data', 1766291906);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `order_number` varchar(20) NOT NULL COMMENT 'เลขที่ออเดอร์',
  `customer_id` int(11) NOT NULL COMMENT 'ลูกค้า',
  `order_date` date NOT NULL COMMENT 'วันที่สั่งซื้อ',
  `order_status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'สถานะออเดอร์',
  `payment_status` varchar(20) NOT NULL DEFAULT 'unpaid' COMMENT 'สถานะชำระเงิน',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'วิธีชำระเงิน',
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'ยอดรวมสินค้า',
  `discount_amount` decimal(12,2) DEFAULT 0.00 COMMENT 'ส่วนลด',
  `discount_reason` varchar(255) DEFAULT NULL COMMENT 'เหตุผลส่วนลด',
  `shipping_cost` decimal(12,2) DEFAULT 0.00 COMMENT 'ค่าจัดส่ง',
  `grand_total` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'ยอดรวมทั้งหมด',
  `shipping_method` varchar(50) DEFAULT NULL COMMENT 'วิธีจัดส่ง KEX, Flash, EMS, pickup',
  `tracking_number` varchar(100) DEFAULT NULL COMMENT 'เลขพัสดุ',
  `shipping_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อผู้รับ',
  `shipping_phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทรผู้รับ',
  `shipping_address` text DEFAULT NULL COMMENT 'ที่อยู่จัดส่ง',
  `customer_notes` text DEFAULT NULL COMMENT 'หมายเหตุจากลูกค้า',
  `internal_notes` text DEFAULT NULL COMMENT 'หมายเหตุภายใน',
  `shipped_at` int(11) DEFAULT NULL COMMENT 'วันที่จัดส่ง',
  `delivered_at` int(11) DEFAULT NULL COMMENT 'วันที่ได้รับ',
  `cancelled_at` int(11) DEFAULT NULL COMMENT 'วันที่ยกเลิก',
  `cancel_reason` varchar(255) DEFAULT NULL COMMENT 'เหตุผลยกเลิก',
  `created_by` int(11) DEFAULT NULL COMMENT 'สร้างโดย',
  `updated_by` int(11) DEFAULT NULL COMMENT 'แก้ไขโดย',
  `created_at` int(11) NOT NULL COMMENT 'วันที่สร้าง',
  `updated_at` int(11) NOT NULL COMMENT 'วันที่แก้ไข'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `part_name` varchar(255) NOT NULL,
  `part_sku` varchar(50) NOT NULL,
  `part_type` varchar(20) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `discount_percent` decimal(5,2) DEFAULT 0.00,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `line_total` decimal(12,2) NOT NULL,
  `warranty_days` int(11) DEFAULT 0,
  `warranty_expires_at` date DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `part`
--

CREATE TABLE `part` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `sku` varchar(50) NOT NULL COMMENT 'รหัสสินค้า (SKU)',
  `oem_number` varchar(50) DEFAULT NULL COMMENT 'เลขอะไหล่แท้ (OEM)',
  `name_th` varchar(255) NOT NULL COMMENT 'ชื่อสินค้า (TH)',
  `name_en` varchar(255) NOT NULL COMMENT 'ชื่อสินค้า (EN)',
  `category_id` int(11) NOT NULL COMMENT 'หมวดหมู่',
  `brand_manufacturer` varchar(100) DEFAULT NULL COMMENT 'ยี่ห้อผู้ผลิต',
  `part_type` varchar(20) NOT NULL COMMENT 'ประเภทสินค้า',
  `condition_grade` varchar(10) DEFAULT NULL,
  `origin_country` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `specifications` text DEFAULT NULL,
  `weight_kg` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `cost_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(12,2) NOT NULL,
  `discount_price` decimal(12,2) DEFAULT NULL,
  `warranty_days` int(11) DEFAULT 0,
  `warranty_description` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 1 COMMENT 'จำนวนขั้นต่ำ',
  `location` varchar(50) DEFAULT NULL COMMENT 'ตำแหน่งในคลัง',
  `supplier_id` int(11) DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `images` text DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `sold_count` int(11) DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `part_category`
--

CREATE TABLE `part_category` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name_th` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `part_category`
--

INSERT INTO `part_category` (`id`, `parent_id`, `name_th`, `name_en`, `slug`, `icon`, `description`, `image`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Engine', 'ระบบเครื่องยนต์', 'engine', 'fa-cogs', NULL, NULL, 1, 1, 1766131059, 1766131059),
(2, NULL, 'Air Conditioning', 'ระบบปรับอากาศ', 'air-conditioning', 'fa-snowflake', NULL, NULL, 1, 2, 1766131059, 1766131059),
(3, NULL, 'Suspension', 'ช่วงล่าง', 'suspension', 'fa-car', NULL, NULL, 1, 3, 1766131059, 1766131059),
(4, NULL, 'Brake System', 'ระบบเบรก', 'brake-system', 'fa-compact-disc', NULL, NULL, 1, 4, 1766131059, 1766131059),
(5, NULL, 'Electrical', 'ระบบไฟฟ้า', 'electrical', 'fa-bolt', NULL, NULL, 1, 5, 1766131059, 1766131059),
(6, NULL, 'Body Parts', 'ตัวถังและชิ้นส่วนภายนอก', 'body-parts', 'fa-door-closed', NULL, NULL, 1, 6, 1766131059, 1766131059),
(7, NULL, 'Interior', 'ชิ้นส่วนภายใน', 'interior', 'fa-couch', NULL, NULL, 1, 7, 1766131059, 1766131059),
(8, NULL, 'Cooling System', 'ระบบหล่อเย็น', 'cooling-system', 'fa-temperature-low', NULL, NULL, 1, 8, 1766131059, 1766131059),
(9, NULL, 'Fuel System', 'ระบบน้ำมันเชื้อเพลิง', 'fuel-system', 'fa-gas-pump', NULL, NULL, 1, 9, 1766131059, 1766131059),
(10, NULL, 'Transmission', 'ระบบส่งกำลัง', 'transmission', 'fa-exchange-alt', NULL, NULL, 1, 10, 1766131059, 1766131059);

-- --------------------------------------------------------

--
-- Table structure for table `part_vehicle`
--

CREATE TABLE `part_vehicle` (
  `id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `model_id` int(11) DEFAULT NULL,
  `engine_type_id` int(11) DEFAULT NULL,
  `year_start` int(11) DEFAULT NULL,
  `year_end` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `slip_image` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` int(11) DEFAULT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(20) DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `category`, `key`, `value`, `type`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'shop', 'name', 'Dune\'s Auto Parts', 'string', 'ชื่อร้าน', NULL, 1766131059),
(2, 'shop', 'name_th', 'ดูน ออโต้ พาร์ท', 'string', 'ชื่อร้านภาษาไทย', NULL, 1766131059),
(3, 'shop', 'phone', '02-XXX-XXXX', 'string', 'เบอร์โทรศัพท์', NULL, 1766131059),
(4, 'shop', 'line_id', '@dunesautoparts', 'string', 'Line ID', NULL, 1766131059),
(5, 'shop', 'email', 'info@dunesautoparts.com', 'string', 'อีเมล', NULL, 1766131059),
(6, 'shop', 'open_time', '08:30', 'string', 'เวลาเปิดร้าน', NULL, 1766131059),
(7, 'shop', 'close_time', '17:30', 'string', 'เวลาปิดร้าน', NULL, 1766131059),
(8, 'shipping', 'cutoff_time', '14:00', 'string', 'เวลาตัดรอบส่งสินค้า', NULL, 1766131059),
(9, 'shipping', 'methods', '[\"Kerry\",\"Flash\",\"EMS\",\"J&T\"]', 'json', 'ช่องทางจัดส่ง', NULL, 1766131059),
(10, 'shipping', 'cod_available', '1', 'boolean', 'รับเก็บเงินปลายทาง', NULL, 1766131059),
(11, 'warranty', 'new_parts_days', '180', 'integer', 'ประกันอะไหล่ใหม่ (วัน)', NULL, 1766131059),
(12, 'warranty', 'used_parts_days', '7', 'integer', 'ประกันอะไหล่มือสอง (วัน)', NULL, 1766131059);

-- --------------------------------------------------------

--
-- Table structure for table `stock_movement`
--

CREATE TABLE `stock_movement` (
  `id` int(11) NOT NULL,
  `part_id` int(11) NOT NULL,
  `movement_type` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `quantity_before` int(11) NOT NULL,
  `quantity_after` int(11) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `unit_cost` decimal(12,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL COMMENT 'รหัสซัพพลายเออร์',
  `name` varchar(255) NOT NULL COMMENT 'ชื่อ',
  `contact_name` varchar(100) DEFAULT NULL COMMENT 'ผู้ติดต่อ',
  `phone` varchar(20) DEFAULT NULL COMMENT 'เบอร์โทร',
  `email` varchar(255) DEFAULT NULL COMMENT 'อีเมล',
  `address` text DEFAULT NULL COMMENT 'ที่อยู่',
  `country` varchar(50) DEFAULT NULL COMMENT 'ประเทศ',
  `supplier_type` varchar(20) NOT NULL COMMENT 'ประเภท',
  `payment_terms` varchar(100) DEFAULT NULL COMMENT 'เงื่อนไขการชำระเงิน',
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'เปิดใช้งาน',
  `created_at` int(11) NOT NULL COMMENT 'สร้างเมื่อ',
  `updated_at` int(11) NOT NULL COMMENT 'แก้ไขเมื่อ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id`, `code`, `name`, `contact_name`, `phone`, `email`, `address`, `country`, `supplier_type`, `payment_terms`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'SUP-001', 'บริษัท ไทยออโต้พาร์ท จำกัด', 'คุณสมชาย ใจดี', '02-123-4567', 'sales@thaiautopartz.co.th', '123/45 ถนนพระราม 2 แขวงแสมดำ เขตบางขุนเทียน กรุงเทพฯ 10150', 'Thailand', 'local', 'เครดิต 30 วัน', 'ตัวแทนจำหน่ายอะไหล่ Toyota, Honda', 1, 1766291906, 1766291906),
(2, 'SUP-002', 'ห้างหุ้นส่วนจำกัด เจริญยนต์', 'คุณวิชัย เจริญกิจ', '081-234-5678', 'charoenyont@gmail.com', '88 ซอยลาดพร้าว 101 แขวงคลองจั่น เขตบางกะปิ กรุงเทพฯ 10240', 'Thailand', 'local', 'เงินสด / เครดิต 15 วัน', 'อะไหล่เครื่องยนต์ทุกยี่ห้อ ราคาส่ง', 1, 1766291906, 1766291906),
(3, 'SUP-003', 'อู่ช่างเล็ก ออโต้พาร์ท', 'คุณเล็ก', '089-876-5432', NULL, '55/3 หมู่ 5 ต.บางพลีใหญ่ อ.บางพลี จ.สมุทรปราการ 10540', 'Thailand', 'local', 'เงินสด', 'อะไหล่มือสองคัดเกรด ราคาถูก', 1, 1766291906, 1766291906),
(4, 'SUP-004', 'บริษัท ศูนย์รวมอะไหล่ จำกัด', 'คุณนภา ศรีสุข', '02-987-6543', 'info@partscenter.co.th', '199/88 ถนนบางนา-ตราด กม.3 แขวงบางนา เขตบางนา กรุงเทพฯ 10260', 'Thailand', 'local', 'เครดิต 45 วัน', 'ศูนย์รวมอะไหล่แท้ทุกยี่ห้อ Denso, Aisin, NTN', 1, 1766291906, 1766291906),
(5, 'SUP-005', 'Japan Auto Parts Co., Ltd.', 'Mr. Tanaka', '+81-3-1234-5678', 'export@japautoparts.jp', '1-2-3 Shinagawa, Tokyo 140-0001, Japan', 'Japan', 'japan', 'T/T 30 days', 'อะไหล่มือสองนำเข้าจากญี่ปุ่น เกรด A+ ทุกชิ้น', 1, 1766291906, 1766291906),
(6, 'SUP-006', 'Osaka Trading Corporation', 'Mr. Yamamoto', '+81-6-9876-5432', 'parts@osakatrading.co.jp', '5-6-7 Namba, Osaka 556-0011, Japan', 'Japan', 'japan', 'L/C 60 days', 'เครื่องยนต์และเกียร์มือสอง สภาพดี ไมล์น้อย', 1, 1766291906, 1766291906),
(7, 'SUP-007', 'Nagoya Parts Export Inc.', 'Ms. Suzuki', '+81-52-111-2222', 'suzuki@nagoyaparts.jp', '8-9-10 Sakae, Nagoya 460-0008, Japan', 'Japan', 'japan', 'T/T 50% deposit', 'อะไหล่ตัวถังและไฟฟ้า นำเข้าตรงจากโรงงาน', 1, 1766291906, 1766291906),
(8, 'SUP-008', 'Euro Parts GmbH', 'Mr. Schmidt', '+49-30-1234567', 'order@europarts.de', 'Berliner Str. 123, 10115 Berlin, Germany', 'Germany', 'europe', 'Net 30', 'อะไหล่ Mercedes-Benz, BMW, Audi แท้และเทียบ', 1, 1766291906, 1766291906),
(9, 'SUP-009', 'Autoparts Europe BV', 'Mr. Van Der Berg', '+31-20-5551234', 'sales@autopartseurope.nl', 'Amstelweg 45, 1012 AB Amsterdam, Netherlands', 'Netherlands', 'europe', 'Net 45', 'อะไหล่ Volvo, VW แท้ ส่งตรงจากยุโรป', 1, 1766291906, 1766291906),
(10, 'SUP-010', 'UK Motor Spares Ltd.', 'Mr. Williams', '+44-20-7946-0958', 'exports@ukmotorspares.co.uk', '15 Industrial Estate, Birmingham B1 2AB, UK', 'UK', 'europe', 'Net 30', 'อะไหล่รถอังกฤษ MG, Land Rover, Jaguar', 1, 1766291906, 1766291906);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `setting_group` varchar(50) NOT NULL COMMENT 'กลุ่มการตั้งค่า',
  `setting_key` varchar(100) NOT NULL COMMENT 'ชื่อการตั้งค่า',
  `setting_label` varchar(255) NOT NULL COMMENT 'ชื่อที่แสดง',
  `setting_value` text DEFAULT NULL COMMENT 'ค่า',
  `setting_type` enum('string','integer','boolean','json','text') NOT NULL DEFAULT 'string' COMMENT 'ประเภท',
  `setting_description` text DEFAULT NULL COMMENT 'คำอธิบาย',
  `sort_order` tinyint(3) UNSIGNED NOT NULL COMMENT 'ลำดับ',
  `is_system` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'การตั้งค่าระบบ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'สร้างเมื่อ',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'แก้ไขเมื่อ',
  `updated_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'แก้ไขโดย'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='การตั้งค่าระบบ';

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_group`, `setting_key`, `setting_label`, `setting_value`, `setting_type`, `setting_description`, `sort_order`, `is_system`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 'general', 'site_name', '', 'ระบบจัดการผู้ใช้งาน', 'string', 'ชื่อระบบ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:33:03', 2),
(2, 'general', 'site_description', '', 'ระบบจัดการผู้ใช้งานด้วย Yii2', 'string', 'คำอธิบายระบบ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:33:03', 2),
(3, 'general', 'admin_email', '', 'admin@pbri.ac.th', 'string', 'อีเมลผู้ดูแลระบบ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:33:03', 2),
(4, 'general', 'support_email', '', 'support@pbri.ac.th', 'string', 'อีเมลสำหรับติดต่อ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(5, 'general', 'items_per_page', '', '20', 'integer', 'จำนวนรายการต่อหน้า', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:33:03', 2),
(6, 'general', 'timezone', '', 'Asia/Bangkok', 'string', 'เขตเวลา', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:33:03', 2),
(7, 'general', 'date_format', '', 'd/m/Y', 'string', 'รูปแบบวันที่', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(8, 'general', 'maintenance_mode', '', '0', 'boolean', 'โหมดปิดปรับปรุง', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(9, 'general', 'allow_registration', '', '0', 'boolean', 'อนุญาตให้สมัครสมาชิก', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(10, 'security', 'password_min_length', '', '12', 'integer', 'ความยาวรหัสผ่านขั้นต่ำ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(11, 'security', 'password_require_uppercase', '', '1', 'boolean', 'ต้องมีตัวพิมพ์ใหญ่', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(12, 'security', 'password_require_lowercase', '', '1', 'boolean', 'ต้องมีตัวพิมพ์เล็ก', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(13, 'security', 'password_require_number', '', '1', 'boolean', 'ต้องมีตัวเลข', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(14, 'security', 'password_require_special', '', '1', 'boolean', 'ต้องมีอักขระพิเศษ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(15, 'security', 'password_history_count', '', '5', 'integer', 'จำนวนรหัสผ่านที่ห้ามซ้ำ', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(16, 'security', 'login_max_attempts', '', '5', 'integer', 'จำนวนครั้งที่ล็อกอินผิดได้', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(17, 'security', 'login_lockout_duration', '', '900', 'integer', 'เวลาล็อค (วินาที)', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(18, 'security', 'session_timeout', '', '3600', 'integer', 'หมดเวลา session (วินาที)', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(19, 'security', 'captcha_threshold', '', '3', 'integer', 'แสดง CAPTCHA หลังล็อกอินผิดกี่ครั้ง', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(20, 'security', 'password_reset_expire', '', '3600', 'integer', 'หมดเวลา reset password (วินาที)', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(21, 'security', 'two_factor_enabled', '', '0', 'boolean', 'เปิดใช้ 2FA', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(22, 'smtp', 'enabled', '', '0', 'boolean', 'เปิดใช้งาน SMTP', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(23, 'smtp', 'host', '', 'smtp.example.com', 'string', 'SMTP Server', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(24, 'smtp', 'port', '', '587', 'integer', 'SMTP Port', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(25, 'smtp', 'encryption', '', 'tls', 'string', 'Encryption (tls/ssl)', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(26, 'smtp', 'username', '', '', 'string', 'SMTP Username', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(27, 'smtp', 'password', '', '', 'string', 'SMTP Password', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(28, 'smtp', 'from_email', '', 'noreply@pbri.ac.th', 'string', 'From Email', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(29, 'smtp', 'from_name', '', 'ระบบจัดการผู้ใช้งาน', 'string', 'From Name', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(30, 'oauth_azure', 'enabled', '', '0', 'boolean', 'เปิดใช้งาน Azure AD', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(31, 'oauth_azure', 'client_id', '', '', 'string', 'Azure Client ID', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(32, 'oauth_azure', 'client_secret', '', '', 'string', 'Azure Client Secret', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(33, 'oauth_azure', 'tenant_id', '', '', 'string', 'Azure Tenant ID', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(34, 'oauth_azure', 'redirect_uri', '', '', 'string', 'Redirect URI', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(35, 'oauth_google', 'enabled', '', '0', 'boolean', 'เปิดใช้งาน Google', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(36, 'oauth_google', 'client_id', '', '', 'string', 'Google Client ID', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(37, 'oauth_google', 'client_secret', '', '', 'string', 'Google Client Secret', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(38, 'oauth_google', 'redirect_uri', '', '', 'string', 'Redirect URI', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(39, 'oauth_thaid', 'enabled', '', '0', 'boolean', 'เปิดใช้งาน ThaID', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(40, 'oauth_thaid', 'client_id', '', '', 'string', 'ThaID Client ID', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(41, 'oauth_thaid', 'client_secret', '', '', 'string', 'ThaID Client Secret', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(42, 'oauth_thaid', 'redirect_uri', '', '', 'string', 'Redirect URI', 0, 0, '2025-12-14 20:07:14', '2025-12-14 20:07:14', NULL),
(43, 'general', 'maintenance_message', '', 'ระบบกำลังปิดปรับปรุง กรุณากลับมาใหม่ภายหลัง', 'string', NULL, 0, 0, '2025-12-14 20:33:03', '2025-12-14 20:33:03', 2),
(44, 'oauth_google', 'google_client_id', '', '', 'string', NULL, 0, 0, '2025-12-14 20:33:13', '2025-12-14 20:33:13', 2),
(45, 'oauth_google', 'google_client_secret', '', '', 'string', NULL, 0, 0, '2025-12-14 20:33:13', '2025-12-14 20:33:13', 2),
(46, 'oauth_google', 'google_redirect_uri', '', '', 'string', NULL, 0, 0, '2025-12-14 20:33:13', '2025-12-14 20:33:13', 2),
(47, 'general', 'company_name', '', 'ดูน ออโต้ พาร์ท', 'text', 'ชื่อบริษัท', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(48, 'general', 'company_phone', '', '02-XXX-XXXX', 'text', 'เบอร์โทรศัพท์', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(49, 'general', 'company_email', '', 'info@dunesautoparts.com', 'text', 'อีเมล', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(50, 'general', 'company_address', '', 'กรุงเทพมหานคร', '', 'ที่อยู่', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(51, 'general', 'company_tax_id', '', '', 'text', 'เลขประจำตัวผู้เสียภาษี', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(52, 'business', 'business_hours_open', '', '08:30', 'text', 'เวลาเปิดทำการ', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(53, 'business', 'business_hours_close', '', '17:30', 'text', 'เวลาปิดทำการ', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(54, 'business', 'shipping_cutoff', '', '14:00', 'text', 'เวลาตัดรอบจัดส่ง', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(55, 'inventory', 'low_stock_threshold', '', '5', '', 'จำนวนสินค้าน้อย (แจ้งเตือน)', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(56, 'inventory', 'default_warranty_days', '', '7', '', 'วันรับประกันสินค้ามือสอง (วัน)', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(57, 'order', 'order_prefix', '', 'SO', 'text', 'รหัสนำหน้าเลขที่คำสั่งซื้อ', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(58, 'customer', 'customer_prefix', '', 'CUST', 'text', 'รหัสนำหน้าเลขที่ลูกค้า', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(59, 'chatbot', 'chatbot_enabled', '', '1', 'boolean', 'เปิดใช้งาน AI Chatbot', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL),
(60, 'chatbot', 'chatbot_welcome_message', '', 'สวัสดีครับ! ยินดีให้บริการครับ มีอะไรให้ช่วยครับ?', '', 'ข้อความต้อนรับ Chatbot', 0, 0, '2025-12-21 04:38:26', '2025-12-21 04:38:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'User ID',
  `azure_object_id` varchar(255) DEFAULT NULL COMMENT 'Microsoft Entra ID (Object ID)',
  `oauth_provider` varchar(50) DEFAULT NULL COMMENT 'OAuth Provider',
  `azure_upn` varchar(100) DEFAULT NULL COMMENT 'Azure UPN',
  `azure_synced_at` datetime DEFAULT NULL COMMENT 'Azure Sync Time',
  `username` varchar(100) NOT NULL COMMENT 'ชื่อผู้ใช้งาน',
  `email_address` varchar(255) NOT NULL COMMENT 'อีเมล',
  `password_hash` varchar(255) DEFAULT NULL COMMENT 'รหัสผ่าน (สำหรับ local auth)',
  `auth_key` varchar(64) DEFAULT NULL COMMENT 'Authentication key',
  `access_token` varchar(255) DEFAULT NULL COMMENT 'API access token',
  `password_reset_token` varchar(255) DEFAULT NULL COMMENT 'Token สำหรับ reset password',
  `full_name` varchar(255) NOT NULL COMMENT 'ชื่อ-นามสกุล',
  `title_name` varchar(50) DEFAULT NULL COMMENT 'คำนำหน้า',
  `first_name` varchar(100) NOT NULL COMMENT 'ชื่อ',
  `last_name` varchar(100) NOT NULL COMMENT 'นามสกุล',
  `first_name_en` varchar(100) DEFAULT NULL COMMENT 'ชื่อภาษาอังกฤษ',
  `last_name_en` varchar(100) DEFAULT NULL COMMENT 'นามสกุลภาษาอังกฤษ',
  `organization_id` int(10) UNSIGNED NOT NULL COMMENT 'หน่วยงาน',
  `phone_number` varchar(50) DEFAULT NULL COMMENT 'โทรศัพท์',
  `line_id` varchar(100) NOT NULL COMMENT 'Line ID',
  `position_name` varchar(255) DEFAULT NULL COMMENT 'ตำแหน่ง',
  `department` varchar(100) NOT NULL COMMENT 'แผนก/ฝ่าย',
  `avatar_file_path` varchar(500) DEFAULT NULL COMMENT 'path รูปโปรไฟล์',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT 'เข้าสู่ระบบล่าสุด',
  `last_login_ip` varchar(45) DEFAULT NULL COMMENT 'IP ล่าสุด',
  `failed_login_attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'จำนวนครั้งที่ login ผิด',
  `locked_until` timestamp NULL DEFAULT NULL COMMENT 'ล็อคจนถึงเวลา',
  `must_change_password` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ต้องเปลี่ยนรหัสผ่าน',
  `role` varchar(10) NOT NULL COMMENT 'บทบาท',
  `notes` text NOT NULL COMMENT 'หมายเหตุเพิ่มเติม',
  `user_status` tinyint(4) NOT NULL DEFAULT 10 COMMENT 'สถานะ: 0=ลบ, 8=ปิดใช้งาน, 9=ไม่ใช้งาน, 10=ใช้งาน',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'สร้างเมื่อ',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'ปรับปรุงล่าสุด',
  `created_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'สร้างโดย',
  `updated_by` int(10) UNSIGNED DEFAULT NULL COMMENT 'ปรับปรุงโดย'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ผู้ใช้งานระบบ';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `azure_object_id`, `oauth_provider`, `azure_upn`, `azure_synced_at`, `username`, `email_address`, `password_hash`, `auth_key`, `access_token`, `password_reset_token`, `full_name`, `title_name`, `first_name`, `last_name`, `first_name_en`, `last_name_en`, `organization_id`, `phone_number`, `line_id`, `position_name`, `department`, `avatar_file_path`, `last_login_at`, `last_login_ip`, `failed_login_attempts`, `locked_until`, `must_change_password`, `role`, `notes`, `user_status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, NULL, NULL, NULL, NULL, 'superadmin', 'superadmin@pi.ac.th', '$2y$12$3yRDiPnnaFQyvgG8aMJYa.j.fmg4PfEqBBaHvNTelZHLVq0b4jHyS', 'super-admin-auth-key-001', NULL, NULL, '', '', 'Super', 'Admin', '', '', 0, '02-590-1001', '', 'ผู้ดูแลระบบสูงสุด', '', NULL, '2025-12-19 01:00:07', '127.0.0.1', 0, NULL, 0, '', '', 10, '2025-12-16 22:56:33', '2025-12-19 01:00:07', NULL, 2),
(2, NULL, NULL, NULL, NULL, 'admin', 'admin@pi.ac.th', '$2y$12$g8au7wiZkX0wqR9tB06Pde8A/ZVC5rMxF31q0DHmk48nCFAesWsm2', 'admin-auth-key-002', NULL, NULL, '', 'นาย', 'แอดมิน', 'ระบบ', NULL, NULL, 0, '02-590-1002', '', 'ผู้ดูแลระบบ', '', 'avatar_2_1766312427.jpg', '2025-12-19 00:59:14', '127.0.0.1', 0, NULL, 0, 'staff', '', 10, '2025-12-16 22:56:33', '0000-00-00 00:00:00', NULL, NULL),
(3, NULL, NULL, NULL, NULL, 'ita_manager', 'ita.manager@pi.ac.th', '$2y$12$xrBlUnQdyvZfgZrvz9iXMuv8sQVpMw4R9UcbtAbSQOGfnd2Aba6DO', 'manager-auth-key-003', NULL, NULL, '', 'นางสาว', 'วิไลวรรณ', 'จัดการ', '', '', 0, '02-590-1003', '', 'หัวหน้างาน ITA', '', NULL, '2025-12-19 01:15:49', '127.0.0.1', 0, NULL, 0, '', '', 10, '2025-12-16 22:56:33', '2025-12-19 01:15:49', NULL, 1),
(4, NULL, NULL, NULL, NULL, 'ita_staff', 'ita.staff@pi.ac.th', '$2y$12$bqXp27KCK2k7Rtnl8WWH0e6J6dckH5VO78ry6tihrO2A9ZDrqB.He', 'staff-auth-key-004', NULL, NULL, '', 'นาย', 'สมชาย', 'พนักงาน', '', '', 0, '02-590-1004', '', 'เจ้าหน้าที่ ITA', '', NULL, '2025-12-19 01:17:44', '127.0.0.1', 0, NULL, 0, '', '', 10, '2025-12-16 22:56:33', '2025-12-19 01:17:44', NULL, 1),
(5, NULL, NULL, NULL, NULL, 'assessor1', 'assessor1@pi.ac.th', '$2y$12$V5Dmb/AWBW8NWtaWDdnWDedHGZebW9DF9Duu8uVNNWMxZ7F3qw/lW', 'assessor-auth-key-005', NULL, NULL, '', 'รศ.ดร.', 'ประเมิน', 'ให้คะแนน', '', '', 0, '081-111-1111', '', 'กรรมการประเมิน', '', NULL, '2025-12-19 01:02:18', '127.0.0.1', 0, NULL, 0, '', '', 10, '2025-12-16 22:56:33', '2025-12-19 01:02:18', NULL, 1),
(6, NULL, NULL, NULL, NULL, 'assessor2', 'assessor2@pi.ac.th', '$2y$12$AOl8L9jUdL/cZ/U4MB95BuY3TSUr8o766vrzeXv1M//K7mo35IPpS', 'assessor-auth-key-006', NULL, NULL, '', 'ผศ.ดร.', 'วิเคราะห์', 'ตรวจสอบ', '', '', 0, '081-222-2222', '', 'กรรมการประเมิน', '', NULL, NULL, NULL, 0, NULL, 0, '', '', 10, '2025-12-16 22:56:33', '2025-12-19 01:01:36', NULL, 1),
(7, NULL, NULL, NULL, NULL, 'assessor3', 'assessor3@pi.ac.th', '$2y$12$OSj40St9t1Gqm0meMErbfuCS4IdCFEtl8/6w9Dymm4pfuiJDCZk5y', 'assessor-auth-key-007', NULL, NULL, '', 'ดร.', 'สมศรี', 'พิจารณา', '', '', 0, '081-333-3333', '', 'กรรมการประเมิน', '', NULL, NULL, NULL, 0, NULL, 0, '', '', 10, '2025-12-16 22:56:33', '2025-12-19 01:01:50', NULL, 1),
(8, NULL, NULL, NULL, NULL, 'appeal_chair', 'appeal.chair@pi.ac.th', '$2y$12$wLogs5GSALbWwngJiUvr9u48TA9hZjdyVep83chgnq2MGbGtrzPX6', 'appeal-auth-key-008', NULL, NULL, '', 'ศ.ดร.', 'อุทธรณ์', 'ประธาน', '', '', 0, '081-444-4444', '', 'ประธานคณะกรรมการอุทธรณ์', '', NULL, '2025-12-19 01:13:49', '127.0.0.1', 0, NULL, 0, 'staff', '', 10, '2025-12-16 22:56:33', '0000-00-00 00:00:00', NULL, 1),
(10, NULL, NULL, NULL, NULL, 'coord_dtai', 'coordinator.dtai@pi.ac.th', '$2y$12$cs/XBC9O7vaSfn0Gv1xZ5e0e80jz9AUjmeLOc8Vt/nXghrvsVxNbq', 'coord-auth-key-010', NULL, NULL, '', 'นาย', 'ดิจิทัล', 'เทคโนโลยี', '', '', 0, '02-590-2001', '', 'ผู้ประสานงาน กทป.', '', NULL, NULL, NULL, 0, NULL, 0, '', '', 10, '2025-12-16 22:56:33', '2025-12-19 01:05:31', NULL, 1),
(11, NULL, NULL, NULL, NULL, 'coord_bcnnon', 'coordinator.bcnnon@pi.ac.th', '$2y$12$wyiGSk4KzfP7K5IbJUB.n.HvSJ0m./zlafNZlDO9i1iVJ9FETNxFq', 'coord-auth-key-011', NULL, NULL, '', 'นางสาว', 'พยาบาล', 'นนทบุรี', '', '', 0, '02-591-1001', '', 'ผู้ประสานงาน วพบ.นนทบุรี', '', NULL, NULL, NULL, 0, NULL, 0, '', '', 10, '2025-12-16 22:56:33', '2025-12-19 01:04:44', NULL, 1),
(12, NULL, NULL, NULL, NULL, 'coord_bcnbkk', 'coordinator.bcnbkk@pi.ac.th', '$2y$12$D/mh19unK8ax7o2Z5/aziuvqFd8M5tDDwXswH3b2Hk8ZURCxZXzOu', 'coord-auth-key-012', NULL, NULL, '', 'นาง', 'พยาบาล', 'กรุงเทพ', '', '', 0, '02-591-1002', '', 'ผู้ประสานงาน วพบ.กรุงเทพ', '', NULL, NULL, NULL, 0, NULL, 0, '', '', 10, '2025-12-16 22:56:33', '2025-12-19 01:05:07', NULL, 1),
(13, NULL, NULL, NULL, NULL, 'coord_scphkk', 'coordinator.scphkk@pi.ac.th', '$2y$12$UOXyuK7qKsLmNmJDTBXPROdPGlF0hhdW2zU6CEp8QZQyidUdKBVE.', 'coord-auth-key-013', NULL, NULL, '', 'นาย', 'สาธารณสุข', 'ขอนแก่น', '', '', 0, '043-221-001', '', 'ผู้ประสานงาน วสส.ขอนแก่น', '', NULL, NULL, NULL, 0, NULL, 0, '', '', 10, '2025-12-16 22:56:33', '2025-12-19 01:04:55', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_original`
--

CREATE TABLE `user_original` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'staff',
  `status` smallint(6) NOT NULL DEFAULT 10,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_original`
--

INSERT INTO `user_original` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `full_name`, `phone`, `avatar`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'inuW2t9B5xGleTR0eUBT4h-kS1WnmR4H', '$2y$13$SEviwqa7dw2ltcaSmx7eW.M1VEP4zx53RfuN.A2IGSLOizUJfaWRW', NULL, 'admin@dunesautoparts.com', 'System Administrator', NULL, NULL, 'admin', 10, 1766131059, 1766131059);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_brand`
--

CREATE TABLE `vehicle_brand` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `name_th` varchar(100) NOT NULL COMMENT 'ชื่อยี่ห้อ (TH)',
  `name_en` varchar(100) DEFAULT NULL COMMENT 'ชื่อยี่ห้อ (EN)',
  `logo` varchar(255) DEFAULT NULL COMMENT 'โลโก้',
  `country` varchar(50) DEFAULT NULL COMMENT 'ประเทศ',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'เปิดใช้งาน',
  `sort_order` int(11) DEFAULT 0 COMMENT 'ลำดับ',
  `created_at` int(11) NOT NULL COMMENT 'สร้างเมื่อ',
  `updated_at` int(11) NOT NULL COMMENT 'แก้ไขเมื่อ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vehicle_brand`
--

INSERT INTO `vehicle_brand` (`id`, `name_th`, `name_en`, `logo`, `country`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Honda', 'ฮอนด้า', NULL, 'Japan', 1, 1, 1766131059, 1766131059),
(2, 'Toyota', 'โตโยต้า', NULL, 'Japan', 1, 2, 1766131059, 1766131059),
(3, 'Mercedes-Benz', 'เมอร์เซเดส-เบนซ์', NULL, 'Germany', 1, 3, 1766131059, 1766131059),
(4, 'BMW', 'บีเอ็มดับเบิลยู', NULL, 'Germany', 1, 4, 1766131059, 1766131059),
(5, 'Nissan', 'นิสสัน', NULL, 'Japan', 1, 5, 1766131059, 1766131059),
(6, 'Mazda', 'มาสด้า', NULL, 'Japan', 1, 6, 1766131059, 1766131059),
(7, 'Mitsubishi', 'มิตซูบิชิ', NULL, 'Japan', 1, 7, 1766131059, 1766131059),
(8, 'Isuzu', 'อีซูซุ', NULL, 'Japan', 1, 8, 1766131059, 1766131059),
(9, 'Ford', 'ฟอร์ด', NULL, 'USA', 1, 9, 1766131059, 1766131059),
(10, 'Chevrolet', 'เชฟโรเลต', NULL, 'USA', 1, 10, 1766131059, 1766131059);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_model`
--

CREATE TABLE `vehicle_model` (
  `id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `name_th` varchar(100) NOT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `generation` varchar(50) DEFAULT NULL,
  `year_start` int(11) DEFAULT NULL,
  `year_end` int(11) DEFAULT NULL,
  `body_type` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-activity_log-user_id` (`user_id`),
  ADD KEY `idx-activity_log-model` (`model_class`,`model_id`),
  ADD KEY `idx-activity_log-created_at` (`created_at`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customer_code` (`customer_code`),
  ADD KEY `idx-customer-customer_type` (`customer_type`),
  ADD KEY `idx-customer-phone` (`phone`);

--
-- Indexes for table `customer_vehicle`
--
ALTER TABLE `customer_vehicle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-customer_vehicle-customer_id` (`customer_id`),
  ADD KEY `fk-customer_vehicle-brand_id` (`brand_id`),
  ADD KEY `fk-customer_vehicle-model_id` (`model_id`),
  ADD KEY `fk-customer_vehicle-engine_type_id` (`engine_type_id`);

--
-- Indexes for table `engine_type`
--
ALTER TABLE `engine_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-engine_type-model_id` (`model_id`);

--
-- Indexes for table `inquiry`
--
ALTER TABLE `inquiry`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `inquiry_number` (`inquiry_number`),
  ADD KEY `fk-inquiry-customer_id` (`customer_id`),
  ADD KEY `fk-inquiry-converted_order_id` (`converted_order_id`),
  ADD KEY `fk-inquiry-assigned_to` (`assigned_to`),
  ADD KEY `idx-inquiry-status` (`status`),
  ADD KEY `idx-inquiry-channel` (`channel`);

--
-- Indexes for table `inquiry_message`
--
ALTER TABLE `inquiry_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-inquiry_message-inquiry_id` (`inquiry_id`),
  ADD KEY `fk-inquiry_message-sender_id` (`sender_id`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `fk-order-customer_id` (`customer_id`),
  ADD KEY `fk-order-created_by` (`created_by`),
  ADD KEY `fk-order-updated_by` (`updated_by`),
  ADD KEY `idx-order-status` (`order_status`),
  ADD KEY `idx-order-payment_status` (`payment_status`),
  ADD KEY `idx-order-order_date` (`order_date`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-order_item-order_id` (`order_id`),
  ADD KEY `fk-order_item-part_id` (`part_id`);

--
-- Indexes for table `part`
--
ALTER TABLE `part`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk-part-category_id` (`category_id`),
  ADD KEY `fk-part-supplier_id` (`supplier_id`),
  ADD KEY `fk-part-created_by` (`created_by`),
  ADD KEY `fk-part-updated_by` (`updated_by`),
  ADD KEY `idx-part-part_type` (`part_type`),
  ADD KEY `idx-part-is_active` (`is_active`),
  ADD KEY `idx-part-is_featured` (`is_featured`),
  ADD KEY `idx-part-oem_number` (`oem_number`);

--
-- Indexes for table `part_category`
--
ALTER TABLE `part_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk-part_category-parent_id` (`parent_id`);

--
-- Indexes for table `part_vehicle`
--
ALTER TABLE `part_vehicle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-part_vehicle-part_id` (`part_id`),
  ADD KEY `fk-part_vehicle-brand_id` (`brand_id`),
  ADD KEY `fk-part_vehicle-model_id` (`model_id`),
  ADD KEY `fk-part_vehicle-engine_type_id` (`engine_type_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-payment-order_id` (`order_id`),
  ADD KEY `fk-payment-verified_by` (`verified_by`),
  ADD KEY `fk-payment-created_by` (`created_by`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx-setting-category_key` (`category`,`key`);

--
-- Indexes for table `stock_movement`
--
ALTER TABLE `stock_movement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-stock_movement-part_id` (`part_id`),
  ADD KEY `fk-stock_movement-created_by` (`created_by`),
  ADD KEY `idx-stock_movement-movement_type` (`movement_type`),
  ADD KEY `idx-stock_movement-created_at` (`created_at`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_settings_key` (`setting_group`,`setting_key`),
  ADD KEY `idx_settings_category` (`setting_group`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_users_username` (`username`),
  ADD UNIQUE KEY `uk_users_email` (`email_address`),
  ADD UNIQUE KEY `uk_users_azure_id` (`azure_object_id`),
  ADD KEY `idx_users_status` (`user_status`),
  ADD KEY `idx_users_name` (`first_name`,`last_name`);

--
-- Indexes for table `user_original`
--
ALTER TABLE `user_original`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`),
  ADD KEY `idx-user-status` (`status`),
  ADD KEY `idx-user-role` (`role`);

--
-- Indexes for table `vehicle_brand`
--
ALTER TABLE `vehicle_brand`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-vehicle_brand-is_active` (`is_active`);

--
-- Indexes for table `vehicle_model`
--
ALTER TABLE `vehicle_model`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk-vehicle_model-brand_id` (`brand_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `customer_vehicle`
--
ALTER TABLE `customer_vehicle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `engine_type`
--
ALTER TABLE `engine_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiry`
--
ALTER TABLE `inquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inquiry_message`
--
ALTER TABLE `inquiry_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `part`
--
ALTER TABLE `part`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID';

--
-- AUTO_INCREMENT for table `part_category`
--
ALTER TABLE `part_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `part_vehicle`
--
ALTER TABLE `part_vehicle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stock_movement`
--
ALTER TABLE `stock_movement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'User ID', AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_original`
--
ALTER TABLE `user_original`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vehicle_brand`
--
ALTER TABLE `vehicle_brand`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vehicle_model`
--
ALTER TABLE `vehicle_model`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `fk-activity_log-user_id` FOREIGN KEY (`user_id`) REFERENCES `user_original` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `customer_vehicle`
--
ALTER TABLE `customer_vehicle`
  ADD CONSTRAINT `fk-customer_vehicle-brand_id` FOREIGN KEY (`brand_id`) REFERENCES `vehicle_brand` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-customer_vehicle-customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-customer_vehicle-engine_type_id` FOREIGN KEY (`engine_type_id`) REFERENCES `engine_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-customer_vehicle-model_id` FOREIGN KEY (`model_id`) REFERENCES `vehicle_model` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `engine_type`
--
ALTER TABLE `engine_type`
  ADD CONSTRAINT `fk-engine_type-model_id` FOREIGN KEY (`model_id`) REFERENCES `vehicle_model` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inquiry`
--
ALTER TABLE `inquiry`
  ADD CONSTRAINT `fk-inquiry-assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `user_original` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-inquiry-converted_order_id` FOREIGN KEY (`converted_order_id`) REFERENCES `order` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-inquiry-customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `inquiry_message`
--
ALTER TABLE `inquiry_message`
  ADD CONSTRAINT `fk-inquiry_message-inquiry_id` FOREIGN KEY (`inquiry_id`) REFERENCES `inquiry` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-inquiry_message-sender_id` FOREIGN KEY (`sender_id`) REFERENCES `user_original` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk-order-created_by` FOREIGN KEY (`created_by`) REFERENCES `user_original` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-order-customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-order-updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_original` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `order_item`
--
ALTER TABLE `order_item`
  ADD CONSTRAINT `fk-order_item-order_id` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-order_item-part_id` FOREIGN KEY (`part_id`) REFERENCES `part` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `part`
--
ALTER TABLE `part`
  ADD CONSTRAINT `fk-part-category_id` FOREIGN KEY (`category_id`) REFERENCES `part_category` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-part-created_by` FOREIGN KEY (`created_by`) REFERENCES `user_original` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-part-supplier_id` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-part-updated_by` FOREIGN KEY (`updated_by`) REFERENCES `user_original` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `part_category`
--
ALTER TABLE `part_category`
  ADD CONSTRAINT `fk-part_category-parent_id` FOREIGN KEY (`parent_id`) REFERENCES `part_category` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `part_vehicle`
--
ALTER TABLE `part_vehicle`
  ADD CONSTRAINT `fk-part_vehicle-brand_id` FOREIGN KEY (`brand_id`) REFERENCES `vehicle_brand` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-part_vehicle-engine_type_id` FOREIGN KEY (`engine_type_id`) REFERENCES `engine_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-part_vehicle-model_id` FOREIGN KEY (`model_id`) REFERENCES `vehicle_model` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-part_vehicle-part_id` FOREIGN KEY (`part_id`) REFERENCES `part` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk-payment-created_by` FOREIGN KEY (`created_by`) REFERENCES `user_original` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-payment-order_id` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-payment-verified_by` FOREIGN KEY (`verified_by`) REFERENCES `user_original` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `stock_movement`
--
ALTER TABLE `stock_movement`
  ADD CONSTRAINT `fk-stock_movement-created_by` FOREIGN KEY (`created_by`) REFERENCES `user_original` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-stock_movement-part_id` FOREIGN KEY (`part_id`) REFERENCES `part` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `vehicle_model`
--
ALTER TABLE `vehicle_model`
  ADD CONSTRAINT `fk-vehicle_model-brand_id` FOREIGN KEY (`brand_id`) REFERENCES `vehicle_brand` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
