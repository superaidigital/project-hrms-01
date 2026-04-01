-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2026 at 08:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hrms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `dept_type` enum('central','school','health') NOT NULL DEFAULT 'central' COMMENT 'ประเภท: ส่วนกลาง, โรงเรียน, รพ.สต.',
  `amphoe` varchar(100) DEFAULT NULL COMMENT 'ชื่ออำเภอ (สำหรับ รพ.สต.)',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'สถานะการใช้งาน',
  `dept_code` varchar(50) DEFAULT NULL COMMENT 'รหัสหน่วยงาน',
  `short_name` varchar(100) DEFAULT NULL COMMENT 'ชื่อย่อ',
  `phone` varchar(50) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `email` varchar(100) DEFAULT NULL COMMENT 'อีเมล',
  `address` text DEFAULT NULL COMMENT 'ที่อยู่หน่วยงาน',
  `latitude` varchar(50) DEFAULT NULL COMMENT 'ละติจูด',
  `longitude` varchar(50) DEFAULT NULL COMMENT 'ลองจิจูด'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `type`, `created_at`, `dept_type`, `amphoe`, `status`, `dept_code`, `short_name`, `phone`, `email`, `address`, `latitude`, `longitude`) VALUES
(1, 'สำนักปลัดองค์การบริหารส่วนจังหวัด', 'ส่วนราชการส่วนกลาง', '2026-03-02 10:36:16', 'central', NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'กองการเจ้าหน้าที่', 'ส่วนราชการส่วนกลาง', '2026-03-02 10:36:16', 'central', NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'กองช่าง', 'ส่วนราชการส่วนกลาง', '2026-03-02 10:36:16', 'central', NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'กองการศึกษา ศาสนา และวัฒนธรรม', 'ส่วนราชการส่วนกลาง', '2026-03-02 10:36:16', 'central', NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'โรงเรียนพอกพิทยาคม รัชมังคลาภิเษก', 'สถานศึกษาในสังกัด', '2026-03-02 10:36:16', 'central', NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'โรงเรียนไพรบึงวิทยาคม', 'สถานศึกษาในสังกัด', '2026-03-02 10:36:16', 'central', NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'โรงเรียนราษีไศล', 'สถานศึกษาในสังกัด', '2026-03-02 10:36:16', 'central', NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'รพ.สต. โพนข่า', 'หน่วยบริการสาธารณสุข', '2026-03-02 10:36:16', 'central', NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'รพ.สต. น้ำคำ', 'หน่วยบริการสาธารณสุข', '2026-03-02 10:36:16', '', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'กองคลัง', 'ส่วนราชการส่วนกลาง', '2026-03-02 16:43:33', 'central', NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `emp_code` varchar(100) DEFAULT NULL,
  `national_id` varchar(13) NOT NULL,
  `prefix` varchar(20) NOT NULL,
  `gender` varchar(20) DEFAULT 'ชาย',
  `dob` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'ปฏิบัติงาน',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `emp_code`, `national_id`, `prefix`, `gender`, `dob`, `first_name`, `last_name`, `phone`, `email`, `status`, `is_active`, `avatar`, `created_at`) VALUES
(1, '', '1339900123456', 'นาย', 'ชาย', '', 'สมชาย', 'รักชาติ', '', '', 'ปฏิบัติงาน', 1, NULL, '2026-03-02 02:39:06'),
(2, NULL, '3339900987654', 'นางสาว', 'ชาย', '', 'วิไลวรรณ', 'ใจดี', '', '', 'ปฏิบัติงาน', 1, NULL, '2026-03-02 02:39:06'),
(3, NULL, '1119900112233', 'นาย', 'ชาย', '', 'สมศักดิ์', 'มั่นคง', '', '', 'ปฏิบัติงาน', 1, NULL, '2026-03-02 02:39:06'),
(4, 'CEN-001', '1339900112233', 'นาย', 'ชาย', NULL, 'สมศักดิ์', 'รักชาติ', NULL, NULL, 'ปฏิบัติงาน', 1, NULL, '2026-03-18 07:58:00'),
(5, 'CEN-002', '1339900223344', 'นาง', 'ชาย', NULL, 'ใจดี', 'มีสุข', NULL, NULL, 'ช่วยราชการ', 1, NULL, '2026-03-18 07:58:00'),
(6, 'CEN-004', '1339900334455', 'นาย', 'ชาย', NULL, 'วิศวะ', 'สร้างสรรค์', NULL, NULL, 'ปฏิบัติงาน', 1, NULL, '2026-03-18 07:58:00'),
(7, 'CEN-005', '1339900445566', 'นาย', 'ชาย', NULL, 'แข็งขัน', 'ขยันยิ่ง', NULL, NULL, 'ปฏิบัติงาน', 1, NULL, '2026-03-18 07:58:00'),
(8, 'SCH-001', '1339900556677', 'นาย', 'ชาย', NULL, 'เรียนดี', 'มีวิชา', NULL, NULL, 'ปฏิบัติงาน', 1, NULL, '2026-03-18 07:58:00'),
(9, 'SCH-002', '1339900667788', 'นางสาว', 'ชาย', NULL, 'สอนดี', 'ศรีสะเกษ', NULL, NULL, 'ลาศึกษาต่อ', 1, NULL, '2026-03-18 07:58:00'),
(10, 'SCH-004', '1339900778899', 'นาย', 'ชาย', NULL, 'วิชาการ', 'ก้าวไกล', NULL, NULL, 'โอนย้าย', 1, NULL, '2026-03-18 07:58:00'),
(11, 'HOS-M01', '1339900889900', 'นาง', 'ชาย', NULL, 'สมหญิง', 'รักษาดี', NULL, NULL, 'ปฏิบัติงาน', 1, NULL, '2026-03-18 07:58:00'),
(12, 'HOS-M02', '1339900990011', 'นางสาว', 'ชาย', NULL, 'พยาบาล', 'ใจเย็น', NULL, NULL, 'ปฏิบัติงาน', 1, NULL, '2026-03-18 07:58:00'),
(13, 'HOS-M04', '1339900001122', 'นาย', 'ชาย', NULL, 'หมอดี', 'มีชัย', NULL, NULL, 'เกษียณอายุ', 1, NULL, '2026-03-18 07:58:00'),
(14, 'HOS-K01', '1339900112200', 'นาง', 'ชาย', NULL, 'ปราณี', 'รักเมือง', NULL, NULL, 'ลาออก', 1, NULL, '2026-03-18 07:58:00'),
(15, 'HOS-K02', '1339900223311', 'นางสาว', 'ชาย', NULL, 'เมตตา', 'อารีย์', NULL, NULL, 'ปฏิบัติงาน', 1, NULL, '2026-03-18 07:58:00'),
(16, 'HOS-K03', '1339900334422', 'นาย', 'ชาย', NULL, 'อนามัย', 'ปลอดภัย', NULL, NULL, 'ถูกพักราชการ', 1, NULL, '2026-03-18 07:58:00'),
(17, 'HOS-W01', '1339900445533', 'นาง', 'ชาย', NULL, 'สุขภาพ', 'แข็งแรง', NULL, NULL, 'ปฏิบัติงาน', 1, NULL, '2026-03-18 07:58:00'),
(18, 'HOS-W02', '1339900556644', 'นาย', 'ชาย', '07/12/2537', 'ฉีดยา', 'เบามือ', '', '', 'ปฏิบัติงาน', 1, NULL, '2026-03-18 07:58:00');

-- --------------------------------------------------------

--
-- Table structure for table `emp_acting`
--

CREATE TABLE `emp_acting` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `acting_position` varchar(255) NOT NULL,
  `order_number` varchar(100) DEFAULT NULL,
  `start_date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emp_decoration`
--

CREATE TABLE `emp_decoration` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `decor_name` varchar(255) NOT NULL,
  `received_year` varchar(4) DEFAULT NULL,
  `gazette_info` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_decoration`
--

INSERT INTO `emp_decoration` (`id`, `employee_id`, `decor_name`, `received_year`, `gazette_info`) VALUES
(7, 1, 'ตริตาภรณ์ช้างเผือก (ต.ช.)', '2560', 'เล่ม 134 ตอนที่ 52ข หน้า 15'),
(8, 1, 'ทวีติยาภรณ์มงกุฎไทย (ท.ม.)', '2565', 'เล่ม 139 ตอนที่ 14ข หน้า 20');

-- --------------------------------------------------------

--
-- Table structure for table `emp_disciplinary`
--

CREATE TABLE `emp_disciplinary` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `incident_date` varchar(50) DEFAULT NULL,
  `punishment_type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_disciplinary`
--

INSERT INTO `emp_disciplinary` (`id`, `employee_id`, `incident_date`, `punishment_type`, `description`) VALUES
(1, 3, '15/02/2567', 'ภาคทัณฑ์', 'มาปฏิบัติราชการสายเกินกำหนดเวลาติดต่อกัน');

-- --------------------------------------------------------

--
-- Table structure for table `emp_education`
--

CREATE TABLE `emp_education` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `degree_level` varchar(100) NOT NULL,
  `major` varchar(255) NOT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `graduation_year` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_education`
--

INSERT INTO `emp_education` (`id`, `employee_id`, `degree_level`, `major`, `institution`, `graduation_year`) VALUES
(7, 1, 'ปริญญาตรี', 'รัฐประศาสนศาสตร์', 'มหาวิทยาลัยขอนแก่น', '2547'),
(8, 1, 'ปริญญาโท', 'การบริหารทรัพยากรมนุษย์', 'สถาบันบัณฑิตพัฒนบริหารศาสตร์ (NIDA)', '2553');

-- --------------------------------------------------------

--
-- Table structure for table `emp_evaluation`
--

CREATE TABLE `emp_evaluation` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `eval_year` varchar(4) NOT NULL,
  `eval_round` varchar(50) DEFAULT NULL,
  `score_percent` decimal(5,2) DEFAULT NULL,
  `result_level` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_evaluation`
--

INSERT INTO `emp_evaluation` (`id`, `employee_id`, `eval_year`, `eval_round`, `score_percent`, `result_level`) VALUES
(7, 1, '2565', 'รอบที่ 1', 92.50, 'ดีเด่น'),
(8, 1, '2565', 'รอบที่ 2', 95.00, 'ดีเด่น');

-- --------------------------------------------------------

--
-- Table structure for table `emp_family`
--

CREATE TABLE `emp_family` (
  `employee_id` int(11) NOT NULL,
  `father_name` varchar(255) DEFAULT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `spouse_name` varchar(255) DEFAULT NULL,
  `children_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_family`
--

INSERT INTO `emp_family` (`employee_id`, `father_name`, `mother_name`, `spouse_name`, `children_count`) VALUES
(1, 'นาย สมหวัง รักความยุติธรรม', 'นาง สมใจ รักความยุติธรรม', 'นาง มาลี รักความยุติธรรม', 2),
(2, '', '', '', 0),
(3, '', '', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `emp_leave`
--

CREATE TABLE `emp_leave` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `leave_year` varchar(4) NOT NULL,
  `sick_leave` int(11) DEFAULT 0,
  `personal_leave` int(11) DEFAULT 0,
  `vacation_leave` int(11) DEFAULT 0,
  `late_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_leave`
--

INSERT INTO `emp_leave` (`id`, `employee_id`, `leave_year`, `sick_leave`, `personal_leave`, `vacation_leave`, `late_count`) VALUES
(7, 1, '2565', 2, 0, 5, 0),
(8, 1, '2566', 0, 1, 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `emp_license`
--

CREATE TABLE `emp_license` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `license_name` varchar(255) NOT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `issue_date` varchar(50) DEFAULT NULL,
  `expiry_date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emp_training`
--

CREATE TABLE `emp_training` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `course_name` varchar(255) NOT NULL,
  `institution` varchar(255) DEFAULT NULL,
  `training_year` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_training`
--

INSERT INTO `emp_training` (`id`, `employee_id`, `course_name`, `institution`, `training_year`) VALUES
(7, 1, 'นักบริหารงานท้องถิ่นระดับต้น รุ่นที่ 15', 'สถาบันพัฒนาบุคลากรท้องถิ่น', '2561'),
(8, 1, 'หลักสูตรนักทรัพยากรบุคคลมืออาชีพ', 'สำนักงาน ก.พ.', '2564');

-- --------------------------------------------------------

--
-- Table structure for table `emp_work_history`
--

CREATE TABLE `emp_work_history` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `start_date` varchar(50) NOT NULL,
  `order_number` varchar(100) DEFAULT NULL,
  `position_name` varchar(255) NOT NULL,
  `position_number` varchar(100) DEFAULT NULL,
  `level` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `agency` varchar(255) DEFAULT 'อบจ.ศรีสะเกษ',
  `department` varchar(255) DEFAULT NULL,
  `division` varchar(255) DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `emp_work_history`
--

INSERT INTO `emp_work_history` (`id`, `employee_id`, `start_date`, `order_number`, `position_name`, `position_number`, `level`, `salary`, `agency`, `department`, `division`) VALUES
(17, 1, '01/10/2555', 'อบจ.ศก. 123/2555', 'นักทรัพยากรบุคคล', '01-01-01', 'ปฏิบัติการ', 15000.00, '', 'กองการเจ้าหน้าที่', '-'),
(18, 1, '01/10/2560', 'อบจ.ศก. 456/2560', 'นักทรัพยากรบุคคล', '01-01-01', 'ชำนาญการ', 25000.00, '', 'กองการเจ้าหน้าที่', '-'),
(19, 1, '01/10/2565', 'อบจ.ศก. 789/2565', 'นักทรัพยากรบุคคล', '01-01-01', 'ชำนาญการพิเศษ', 35000.00, '', 'กองการเจ้าหน้าที่', '-'),
(20, 2, '01/10/2540', 'อบจ.ศก. 001/2540', 'นักวิชาการเงินและบัญชี', '03-01-01', 'ชำนาญการ', 45000.00, '', 'กองคลัง', '-'),
(22, 3, '01/05/2566', 'อบจ.ศก. 002/2566', 'วิศวกรโยธา', '02-01-05', 'ปฏิบัติการ', 17500.00, '', 'กองช่าง', '-');

-- --------------------------------------------------------

--
-- Table structure for table `manpower`
--

CREATE TABLE `manpower` (
  `id` int(11) NOT NULL,
  `department` varchar(255) NOT NULL,
  `division` varchar(255) DEFAULT '-',
  `employee_type` varchar(100) DEFAULT 'ข้าราชการ อบจ.',
  `position_number` varchar(100) NOT NULL,
  `position_name` varchar(255) NOT NULL,
  `level` varchar(100) DEFAULT NULL,
  `status` enum('occupied','vacant') DEFAULT 'vacant',
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manpower`
--

INSERT INTO `manpower` (`id`, `department`, `division`, `employee_type`, `position_number`, `position_name`, `level`, `status`, `remark`, `created_at`, `sort_order`) VALUES
(71, 'รพ.สต. น้ำคำ', '-', 'ข้าราชการ อบจ.', '52111113', 'ผู้อำนวยการ รพ.สต.', 'ชำนาญการ', 'vacant', '', '2026-04-01 02:58:26', 1),
(72, 'โรงเรียนราษีไศล', '-', 'ข้าราชการ อบจ.', '52111113', 'ผู้อำนวยการ รร.', 'กลาง', 'vacant', '', '2026-04-01 03:00:31', 1),
(73, 'รพ.สต. น้ำคำ', '-', 'ข้าราชการ อบจ.', '52111444454', 'พยาบาล', 'เชี่ยวชาญ', 'vacant', '', '2026-04-01 03:03:01', 2);

-- --------------------------------------------------------

--
-- Table structure for table `position_levels`
--

CREATE TABLE `position_levels` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(100) DEFAULT 'ทั่วไป',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `position_levels`
--

INSERT INTO `position_levels` (`id`, `name`, `type`, `created_at`) VALUES
(1, 'ปฏิบัติงาน', 'ประเภททั่วไป', '2026-03-02 10:54:44'),
(2, 'ชำนาญงาน', 'ประเภททั่วไป', '2026-03-02 10:54:44'),
(3, 'อาวุโส', 'ประเภททั่วไป', '2026-03-02 10:54:44'),
(4, 'ทักษะเฉพาะ', 'ประเภททั่วไป', '2026-03-02 10:54:44'),
(5, 'ปฏิบัติการ', 'ประเภทวิชาการ', '2026-03-02 10:54:44'),
(6, 'ชำนาญการ', 'ประเภทวิชาการ', '2026-03-02 10:54:44'),
(7, 'ชำนาญการพิเศษ', 'ประเภทวิชาการ', '2026-03-02 10:54:44'),
(8, 'เชี่ยวชาญ', 'ประเภทวิชาการ', '2026-03-02 10:54:44'),
(9, 'ต้น', 'ประเภทอำนวยการท้องถิ่น', '2026-03-02 10:54:44'),
(10, 'กลาง', 'ประเภทอำนวยการท้องถิ่น', '2026-03-02 10:54:44'),
(11, 'สูง', 'ประเภทอำนวยการท้องถิ่น', '2026-03-02 10:54:44'),
(12, 'ไม่มีระดับ / ไม่ระบุ', 'อื่นๆ', '2026-03-02 10:54:44');

-- --------------------------------------------------------

--
-- Table structure for table `system_menus`
--

CREATE TABLE `system_menus` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT 0,
  `menu_name` varchar(100) NOT NULL,
  `action_name` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-circle',
  `role_access` varchar(20) DEFAULT 'all',
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_menus`
--

INSERT INTO `system_menus` (`id`, `parent_id`, `menu_name`, `action_name`, `icon`, `role_access`, `is_active`, `sort_order`) VALUES
(1, 0, 'แดชบอร์ด', 'dashboard', 'fa-chart-pie', 'all', 1, 1),
(2, 0, 'ทะเบียนประวัติ', 'employees', 'fa-users', 'all', 1, 2),
(3, 0, 'กรอบอัตรากำลัง', 'manpower', 'fa-sitemap', 'all', 1, 3),
(4, 0, 'รายงานและสถิติ', 'summary_report', 'fa-chart-bar', 'all', 1, 4),
(5, 0, 'จัดการหน่วยงาน', 'departments', 'fa-building', 'admin', 1, 5),
(6, 0, 'ระดับและสายงาน', 'position_levels', 'fa-layer-group', 'admin', 1, 6),
(7, 0, 'จัดการผู้ใช้งาน', 'users', 'fa-user-shield', 'admin', 1, 7),
(8, 0, 'ตั้งค่าระบบหลัก', 'settings', 'fa-cogs', 'admin', 1, 8);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `description`) VALUES
('maintenance_mode', 'off', 'สถานะปิดปรับปรุงระบบ (on/off)');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','hr','user') DEFAULT 'user',
  `is_active` enum('0','1') DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `is_active`, `created_at`) VALUES
(1, 'admin', '$2y$10$IePjUIJn87wXbAEw.bFZv.actec.L4kmV.tVxzs0I2h3QQalRMMA.', 'ผู้ดูแลระบบสูงสุด', 'admin', '1', '2026-03-05 07:34:54'),
(2, 'root', '$2y$10$99Eox8u.nWXCgbXbE.68.OxaI91mMNN/0wdnt8Nwodr.D9lgzCwI2', 'คุณสมหญิง ใจดี', 'user', '1', '2026-03-05 07:39:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD UNIQUE KEY `emp_code` (`emp_code`);

--
-- Indexes for table `emp_acting`
--
ALTER TABLE `emp_acting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `emp_decoration`
--
ALTER TABLE `emp_decoration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `emp_disciplinary`
--
ALTER TABLE `emp_disciplinary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `emp_education`
--
ALTER TABLE `emp_education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `emp_evaluation`
--
ALTER TABLE `emp_evaluation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `emp_family`
--
ALTER TABLE `emp_family`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `emp_leave`
--
ALTER TABLE `emp_leave`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `emp_license`
--
ALTER TABLE `emp_license`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `emp_training`
--
ALTER TABLE `emp_training`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `emp_work_history`
--
ALTER TABLE `emp_work_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `manpower`
--
ALTER TABLE `manpower`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `position_levels`
--
ALTER TABLE `position_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_menus`
--
ALTER TABLE `system_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `emp_acting`
--
ALTER TABLE `emp_acting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_decoration`
--
ALTER TABLE `emp_decoration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `emp_disciplinary`
--
ALTER TABLE `emp_disciplinary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `emp_education`
--
ALTER TABLE `emp_education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `emp_evaluation`
--
ALTER TABLE `emp_evaluation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `emp_leave`
--
ALTER TABLE `emp_leave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `emp_license`
--
ALTER TABLE `emp_license`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_training`
--
ALTER TABLE `emp_training`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `emp_work_history`
--
ALTER TABLE `emp_work_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `manpower`
--
ALTER TABLE `manpower`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `position_levels`
--
ALTER TABLE `position_levels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `system_menus`
--
ALTER TABLE `system_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emp_acting`
--
ALTER TABLE `emp_acting`
  ADD CONSTRAINT `emp_acting_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_decoration`
--
ALTER TABLE `emp_decoration`
  ADD CONSTRAINT `emp_decoration_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_disciplinary`
--
ALTER TABLE `emp_disciplinary`
  ADD CONSTRAINT `emp_disciplinary_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_education`
--
ALTER TABLE `emp_education`
  ADD CONSTRAINT `emp_education_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_evaluation`
--
ALTER TABLE `emp_evaluation`
  ADD CONSTRAINT `emp_evaluation_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_family`
--
ALTER TABLE `emp_family`
  ADD CONSTRAINT `emp_family_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_leave`
--
ALTER TABLE `emp_leave`
  ADD CONSTRAINT `emp_leave_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_license`
--
ALTER TABLE `emp_license`
  ADD CONSTRAINT `emp_license_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_training`
--
ALTER TABLE `emp_training`
  ADD CONSTRAINT `emp_training_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_work_history`
--
ALTER TABLE `emp_work_history`
  ADD CONSTRAINT `emp_work_history_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
