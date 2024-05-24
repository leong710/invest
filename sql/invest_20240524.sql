-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-05-24 10:00:22
-- 伺服器版本： 10.4.24-MariaDB
-- PHP 版本： 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `invest`
--

-- --------------------------------------------------------

--
-- 資料表結構 `_document`
--

CREATE TABLE `_document` (
  `id` int(10) NOT NULL COMMENT 'aid',
  `uuid` varchar(50) NOT NULL COMMENT '系統uuid',
  `idty` varchar(10) NOT NULL COMMENT '表單狀態',
  `dcc_no` varchar(25) NOT NULL COMMENT '套用dcc編號',
  `fab_id` int(10) NOT NULL COMMENT 'fab_id',
  `local_id` int(10) NOT NULL COMMENT 'local_id',
  `anis_no` varchar(25) NOT NULL COMMENT 'ANIS表單編號',
  `case_title` varchar(100) NOT NULL COMMENT '事件名稱',
  `a_dept` varchar(100) NOT NULL COMMENT '事故單位',
  `meeting_time` datetime NOT NULL COMMENT '會議時間',
  `meeting_local` varchar(100) NOT NULL COMMENT '會議地點',
  `meeting_man_a` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '1.事故當事者(或其委任代理人)' CHECK (json_valid(`meeting_man_a`)),
  `meeting_man_o` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '2.其他與會人員/勞工代表' CHECK (json_valid(`meeting_man_o`)),
  `meeting_man_s` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '3.環安人員' CHECK (json_valid(`meeting_man_s`)),
  `meeting_man_d` varchar(255) NOT NULL COMMENT '其他非INX與會人員',
  `_focus` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '採集重點JSON' CHECK (json_valid(`_focus`)),
  `_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '訪談內容JSON' CHECK (json_valid(`_content`)),
  `confirm_sign` longtext DEFAULT NULL COMMENT '當事人同意上述描述',
  `ruling_sign` longtext DEFAULT NULL COMMENT '非公傷當事人簽認',
  `a_pic` varchar(50) DEFAULT NULL COMMENT '上傳路線圖檔',
  `created_emp_id` varchar(10) NOT NULL COMMENT '開單工號',
  `created_cname` varchar(10) NOT NULL COMMENT '開單姓名',
  `created_at` datetime NOT NULL COMMENT '建檔時間',
  `updated_at` datetime NOT NULL COMMENT '更新時間',
  `updated_cname` varchar(10) NOT NULL COMMENT '更新人員',
  `logs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Logs' CHECK (json_valid(`logs`)),
  `editions` longtext DEFAULT NULL COMMENT 'Edition記錄'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_document`
--

INSERT INTO `_document` (`id`, `uuid`, `idty`, `dcc_no`, `fab_id`, `local_id`, `anis_no`, `case_title`, `a_dept`, `meeting_time`, `meeting_local`, `meeting_man_a`, `meeting_man_o`, `meeting_man_s`, `meeting_man_d`, `_focus`, `_content`, `confirm_sign`, `ruling_sign`, `a_pic`, `created_emp_id`, `created_cname`, `created_at`, `updated_at`, `updated_cname`, `logs`, `editions`) VALUES
(2, '0d3886e7-1990-11ef-83dc-2cfda183ef4f', '6', '13ES100016-F002-V002b', 5, 38, 'ANIS20240524133922222', '測試事件x', 'G95', '2024-05-24 13:39:00', 'office 1F', '\"\"', '\"\"', '\"\"', '', 'null', '{\"emp_id\":\"17005107\",\"cname\":\"\\u99ac\\u5409\\u5152\",\"cstext\":\"\\u52a9\\u7406\\u6280\\u8853\\u54e1\",\"a_day\":\"2024-04-01 08:00\",\"hired\":\"2023-04-01\",\"rload\":\"\\u4f30\\u7b97\\u7d04\\uff1a1 \\u5e74 0 \\u500b\\u6708 0 \\u5929\",\"a_work_s\":\"2024-04-01 07:30\",\"a_work_e\":\"2024-04-01 19:30\",\"a_location\":\"test_local\",\"a_description\":\"\",\"s4_combo_1\":[\"\"],\"s4_combo_4\":[\"\"],\"s4_combo_5\":[\"\",\"\"],\"s6_combo_2\":[\"\"],\"remark\":\"\",\"s8_combo_6\":[\"\"],\"s8_combo_7\":[\"\"],\"s8_text_8a\":\"\",\"s8_text_8b\":\"\",\"s8_textarea_a\":\"\"}', NULL, NULL, NULL, '10008048', '陳建良', '2024-05-24 13:39:56', '2024-05-24 13:42:47', '陳建良', '[{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-05-24 13:39:56\",\"action\":\"\\u66ab\\u5b58 (Save)\",\"remark\":\"\"},{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-05-24 13:42:47\",\"action\":\"\\u66ab\\u5b58 (Save)\",\"remark\":\"\"}]', '[{\"updated_at\":\"2024-05-24 13:42:47\",\"updated_cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"update_document\":{\"_content\":{\"emp_id\":\" => 17005107\",\"cname\":\" => \\u99ac\\u5409\\u5152\",\"cstext\":\" => \\u52a9\\u7406\\u6280\\u8853\\u54e1\",\"a_day\":\" => 2024-04-01 08:00\",\"hired\":\" => 2023-04-01\",\"rload\":\" => \\u4f30\\u7b97\\u7d04\\uff1a1 \\u5e74 0 \\u500b\\u6708 0 \\u5929\",\"a_work_s\":\" => 2024-04-01 07:30\",\"a_work_e\":\" => 2024-04-01 19:30\",\"a_location\":\" => test_local\"}}}]');

-- --------------------------------------------------------

--
-- 資料表結構 `_fab`
--

CREATE TABLE `_fab` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `site_id` int(10) NOT NULL COMMENT '歸屬site',
  `fab_title` varchar(20) NOT NULL COMMENT 'fab名稱',
  `fab_remark` varchar(255) NOT NULL COMMENT 'fab備註',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `sign_code` varchar(10) NOT NULL COMMENT '管理權責(部課)',
  `pm_emp_id` varchar(255) DEFAULT NULL COMMENT '轄區管理員',
  `created_at` datetime NOT NULL COMMENT '創建日期',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_fab`
--

INSERT INTO `_fab` (`id`, `site_id`, `fab_title`, `fab_remark`, `flag`, `sign_code`, `pm_emp_id`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 1, 'tnESH', '台南環安處', 'Off', '9T040500', '10008048,陳建良,13099963,梁修豪', '2023-07-03 14:30:51', '2024-03-29 11:58:21', '陳建良'),
(2, 1, 'FAB1棟', '一廠', 'On', '9T041501', '', '2023-07-04 15:11:08', '2024-03-29 11:00:58', '陳建良'),
(3, 1, 'FAB2棟', '二廠', 'On', '9T043501', NULL, '2023-07-04 15:11:30', '2023-10-26 13:56:51', '陳建良'),
(4, 1, 'FAB3棟', '三廠', 'On', '9T043501', NULL, '2023-07-04 15:11:59', '2023-10-26 13:56:57', '陳建良'),
(5, 1, 'TAC棟', '四廠', 'On', '9T042501', '', '2023-07-04 15:12:32', '2024-03-29 11:01:03', '陳建良'),
(6, 1, 'FAB5棟', '五廠', 'On', '9T043502', NULL, '2023-07-04 15:12:52', '2023-10-26 13:57:02', '陳建良'),
(7, 1, 'FAB6棟', '六廠', 'On', '9T044501', NULL, '2023-07-04 15:13:11', '2023-10-26 13:54:28', '陳建良'),
(8, 1, 'FAB7棟', '七廠', 'On', '9T041502', '', '2023-07-04 15:13:30', '2024-03-29 11:00:48', '陳建良'),
(9, 1, 'FAB8棟', '八廠', 'On', '9T044502', NULL, '2023-07-04 15:14:07', '2023-10-26 13:54:18', '陳建良'),
(10, 1, 'LCM棟', 'C廠區', 'On', '9T042502', '', '2023-07-04 15:14:29', '2023-12-19 11:14:31', '陳建良'),
(11, 1, 'TOC棟', 'TOC', 'On', '9T042501', '', '2023-07-04 15:15:35', '2024-03-29 11:00:42', '陳建良'),
(12, 1, 'K9棟', '科九', 'On', '9T042501', '', '2023-07-04 15:16:11', '2024-03-29 11:00:31', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_flow`
--

CREATE TABLE `_flow` (
  `id` int(10) NOT NULL,
  `doc_uuid` varchar(255) NOT NULL COMMENT 'doc_uuid',
  `in_signEmpid` varchar(10) DEFAULT NULL COMMENT '待簽工號emp_id',
  `in_signCname` varchar(20) DEFAULT NULL COMMENT '待簽姓名cname',
  `step_flow` longtext NOT NULL COMMENT '簽核動作',
  `updated_at` datetime NOT NULL COMMENT '更新時間',
  `updated_user` varchar(20) NOT NULL COMMENT '更新人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 資料表結構 `_formcase`
--

CREATE TABLE `_formcase` (
  `id` int(10) UNSIGNED NOT NULL,
  `_type` varchar(20) NOT NULL COMMENT '表單名稱',
  `title` varchar(255) NOT NULL COMMENT '分類註解',
  `short_name` varchar(20) NOT NULL COMMENT '簡稱',
  `dcc_no` varchar(30) NOT NULL COMMENT 'dcc編號',
  `_icon` varchar(100) NOT NULL COMMENT 'icon',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_formcase`
--

INSERT INTO `_formcase` (`id`, `_type`, `title`, `short_name`, `dcc_no`, `_icon`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, '13ES100016-F002', '南廠區交通事故訪談表', '交通事故', '13ES100016-F002-V002', 'fa-solid fa-car-burst', 'Off', '2024-02-01 03:24:12', '2024-04-25 11:45:55', '陳建良'),
(2, '13ES100016-F003', '南廠區廠內事故訪談表', '廠內事故', '13ES100016-F003-V003', 'fa-solid fa-house-circle-exclamation', 'Off', '2024-02-01 11:02:18', '2024-04-25 11:46:49', '陳建良'),
(3, '13ES100016-F002a', '南廠區交通事故訪談表', '交通事故', '13ES100016-F002-V002a', 'fa-solid fa-person-falling-burst', 'On', '2024-02-01 03:24:12', '2024-05-07 11:08:07', '陳建良'),
(4, '13ES100016-F003a', '南廠區廠內事故訪談表', '廠內事故', '13ES100016-F003-V003a', 'fa-solid fa-building-circle-exclamation', 'On', '2024-02-01 11:02:18', '2024-05-07 11:08:55', '陳建良'),
(6, 'test', 'test', '測試', 'autolog', 'fa-brands fa-facebook', 'Off', '2024-04-12 14:07:01', '2024-04-25 11:46:58', '陳建良'),
(20, '13ES100016-F002b', '南廠區交通事故訪談表', '交通事故', '13ES100016-F002-V002b', 'fa-solid fa-ghost', 'On', '2024-05-17 14:40:01', '2024-05-21 15:44:13', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_local`
--

CREATE TABLE `_local` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `fab_id` int(10) UNSIGNED NOT NULL COMMENT '歸屬Fab',
  `local_title` varchar(50) NOT NULL COMMENT '子區域名稱',
  `local_remark` varchar(50) NOT NULL COMMENT '子區域註解',
  `low_level` varchar(50) DEFAULT NULL COMMENT '安全水位',
  `flag` varchar(5) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL COMMENT '創建時間',
  `updated_at` datetime NOT NULL COMMENT '更新時間',
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_local`
--

INSERT INTO `_local` (`id`, `fab_id`, `local_title`, `local_remark`, `low_level`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 2, 'Array-1', 'Array-1', NULL, 'On', '2024-05-06 09:24:28', '2024-05-06 09:24:28', '陳建良'),
(2, 2, 'CF-1', 'CF-1', NULL, 'On', '2024-05-06 09:24:52', '2024-05-06 09:24:52', '陳建良'),
(3, 2, 'Cell(LCD)-1', 'Cell(LCD)-1', NULL, 'On', '2024-05-06 09:27:47', '2024-05-06 09:27:47', '陳建良'),
(4, 2, 'TS1廠', 'TS1廠', NULL, 'On', '2024-05-06 09:28:21', '2024-05-06 09:28:21', '陳建良'),
(5, 2, '儲運管理WH-1', '儲運管理WH-1', NULL, 'On', '2024-05-06 09:29:09', '2024-05-06 09:29:09', '陳建良'),
(6, 2, '其他Other-1', '其他Other-1', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(7, 2, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 09:30:18', '2024-05-06 09:30:18', '陳建良'),
(8, 2, '整合INT-1', '整合INT-1', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(9, 2, '環安衛ESH', '環安衛ESH', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 13:38:50', '陳建良'),
(10, 2, '總務GA', '總務GA', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(11, 2, '自動化AT(MAHS)-1', '自動化AT(MAHS)-1', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(12, 3, 'Array-2', 'Array-2', NULL, 'On', '2024-05-06 09:56:49', '2024-05-06 09:56:49', '陳建良'),
(13, 3, 'CF/Cell(LCD)-2', 'CF/Cell(LCD)-2', NULL, 'On', '2024-05-06 09:57:17', '2024-05-06 09:57:17', '陳建良'),
(14, 3, 'TN-XRay廠', 'TN-XRay廠', NULL, 'On', '2024-05-06 10:50:38', '2024-05-06 10:50:38', '陳建良'),
(15, 3, 'TN-XR其他Other', 'TN-XR其他Other', NULL, 'On', '2024-05-06 10:55:48', '2024-05-06 10:55:48', '陳建良'),
(16, 3, '儲運管理WH-2', '儲運管理WH-2', NULL, 'On', '2024-05-06 11:00:30', '2024-05-06 11:00:30', '陳建良'),
(17, 3, '其他Other-2', '其他Other-2', NULL, 'On', '2024-05-06 11:12:25', '2024-05-06 11:12:25', '陳建良'),
(18, 3, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 11:12:52', '2024-05-06 11:12:52', '陳建良'),
(19, 3, '整合INT-2', '整合INT-2', NULL, 'On', '2024-05-06 11:13:12', '2024-05-06 11:13:12', '陳建良'),
(20, 3, '環安衛ESH', '環安衛ESH', NULL, 'On', '2024-05-06 11:13:29', '2024-05-06 11:13:29', '陳建良'),
(21, 3, '總務GA', '總務GA', NULL, 'On', '2024-05-06 11:13:55', '2024-05-06 11:13:55', '陳建良'),
(22, 3, '自動化AT(MAHS)-2', '自動化AT(MAHS)-2', NULL, 'On', '2024-05-06 11:14:16', '2024-05-06 11:14:16', '陳建良'),
(23, 4, 'Array-3', 'Array-3', NULL, 'On', '2024-05-06 09:24:28', '2024-05-06 09:24:28', '陳建良'),
(24, 4, 'CF-3', 'CF-3', NULL, 'On', '2024-05-06 09:24:52', '2024-05-06 09:24:52', '陳建良'),
(25, 4, 'Cell(LCD)-3', 'Cell(LCD)-3', NULL, 'On', '2024-05-06 09:27:47', '2024-05-06 09:27:47', '陳建良'),
(26, 4, '儲運管理WH-3', '儲運管理WH-3', NULL, 'On', '2024-05-06 09:29:09', '2024-05-06 09:29:09', '陳建良'),
(27, 4, '其他Other-3', '其他Other-3', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(28, 4, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 09:30:18', '2024-05-06 09:30:18', '陳建良'),
(29, 4, '整合INT-3', '整合INT-3', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(30, 4, '環安衛ESH', '環安衛ESH', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(31, 4, '總務GA', '總務GA', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(32, 4, '自動化AT(MAHS)-3', '自動化AT(MAHS)-3', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(33, 5, 'HR人資', 'HR人資', NULL, 'On', '2024-05-06 09:28:21', '2024-05-06 09:28:21', '陳建良'),
(34, 5, '儲運管理WH-4', '儲運管理WH-4', NULL, 'On', '2024-05-06 09:29:09', '2024-05-06 09:29:09', '陳建良'),
(35, 5, '其他Other-4', '其他Other-4', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(36, 5, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 09:30:18', '2024-05-06 09:30:18', '陳建良'),
(37, 5, '環安衛ESH', '環安衛ESH', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(38, 5, '總務GA', '總務GA', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(39, 6, 'Array-5', 'Array-5', NULL, 'On', '2024-05-06 09:24:28', '2024-05-06 09:24:28', '陳建良'),
(40, 6, 'CF-5', 'CF-5', NULL, 'On', '2024-05-06 09:24:52', '2024-05-06 09:24:52', '陳建良'),
(41, 6, 'Cell(LCD)-5', 'Cell(LCD)-5', NULL, 'On', '2024-05-06 09:27:47', '2024-05-06 09:27:47', '陳建良'),
(42, 6, '儲運管理WH-5', '儲運管理WH-5', NULL, 'On', '2024-05-06 09:29:09', '2024-05-06 09:29:09', '陳建良'),
(43, 6, '其他Other-5', '其他Other-5', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(44, 6, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 09:30:18', '2024-05-06 09:30:18', '陳建良'),
(45, 6, '整合INT-5', '整合INT-5', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(46, 6, '環安衛ESH', '環安衛ESH', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(47, 6, '總務GA', '總務GA', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(48, 6, '自動化AT(MAHS)-5', '自動化AT(MAHS)-5', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(49, 7, 'Array-6', 'Array-6', NULL, 'On', '2024-05-06 09:24:28', '2024-05-06 09:24:28', '陳建良'),
(50, 7, 'CF-6', 'CF-6', NULL, 'On', '2024-05-06 09:24:52', '2024-05-06 09:24:52', '陳建良'),
(51, 7, 'Cell(LCD)-6', 'Cell(LCD)-6', NULL, 'On', '2024-05-06 09:27:47', '2024-05-06 09:27:47', '陳建良'),
(52, 7, '儲運管理WH-6', '儲運管理WH-6', NULL, 'On', '2024-05-06 09:29:09', '2024-05-06 09:29:09', '陳建良'),
(53, 7, '其他Other-6', '其他Other-6', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(54, 7, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 09:30:18', '2024-05-06 09:30:18', '陳建良'),
(55, 7, '整合INT-6', '整合INT-6', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(56, 7, '環安衛ESH', '環安衛ESH', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(57, 7, '總務GA', '總務GA', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(58, 7, '自動化AT(MAHS)-6', '自動化AT(MAHS)-6', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(59, 8, 'Array/CF-7', 'Array/CF-7', NULL, 'On', '2024-05-06 09:24:52', '2024-05-06 09:24:52', '陳建良'),
(60, 8, 'Cell(LCD)-7', 'Cell(LCD)-7', NULL, 'On', '2024-05-06 09:27:47', '2024-05-06 09:27:47', '陳建良'),
(61, 8, '儲運管理WH-7', '儲運管理WH-7', NULL, 'On', '2024-05-06 09:29:09', '2024-05-06 09:29:09', '陳建良'),
(62, 8, '其他Other-7', '其他Other-7', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(63, 8, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 09:30:18', '2024-05-06 09:30:18', '陳建良'),
(64, 8, '整合INT-7', '整合INT-7', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(65, 8, '環安衛ESH', '環安衛ESH', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(66, 8, '總務GA', '總務GA', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(67, 8, '自動化AT(MAHS)-7', '自動化AT(MAHS)-7', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(68, 9, 'Array-8A', 'Array-8A', NULL, 'On', '2024-05-06 09:24:28', '2024-05-06 09:24:28', '陳建良'),
(69, 9, 'CF-8', 'CF-8', NULL, 'On', '2024-05-06 09:24:52', '2024-05-06 09:24:52', '陳建良'),
(70, 9, 'Cell(LCD)-8', 'Cell(LCD)-8', NULL, 'On', '2024-05-06 09:27:47', '2024-05-06 09:27:47', '陳建良'),
(71, 9, 'T6-Array/CF', 'T6-Array/CF', NULL, 'On', '2024-05-06 09:24:52', '2024-05-06 09:24:52', '陳建良'),
(72, 9, 'T6-Cell(LCD)', 'T6-Cell(LCD)', NULL, 'On', '2024-05-06 09:27:47', '2024-05-06 09:27:47', '陳建良'),
(73, 9, 'T6-其他Other', 'T6-其他Other', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(74, 9, 'T6-整合INT', 'T6-整合INT', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(75, 9, 'T6-自動化AT(MAHS)', 'T6-自動化AT(MAHS)', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(76, 9, '儲運管理WH-8', '儲運管理WH-8', NULL, 'On', '2024-05-06 09:29:09', '2024-05-06 09:29:09', '陳建良'),
(77, 9, '其他Other-8', '其他Other-8', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(78, 9, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 09:30:18', '2024-05-06 09:30:18', '陳建良'),
(79, 9, '整合INT-8', '整合INT-8', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(80, 9, '環安衛ESH', '環安衛ESH', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(81, 9, '總務GA', '總務GA', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(82, 9, '自動化AT(MAHS)-8', '自動化AT(MAHS)-8', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(83, 12, 'K9-MOD廠', 'K9-MOD廠', NULL, 'On', '2024-05-06 09:29:09', '2024-05-06 09:29:09', '陳建良'),
(84, 12, 'K9-其他Other', 'K9-其他Other', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(85, 12, 'K9-自動化AT(MAHS)', 'K9-自動化AT(MAHS)', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(86, 12, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 09:30:18', '2024-05-06 09:30:18', '陳建良'),
(87, 12, '總務GA', '總務GA', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(88, 10, 'CG-其他Other', 'CG-其他Other', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(89, 10, 'CG-自動化AT(MAHS)', 'CG-自動化AT(MAHS)', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(90, 10, 'CG-製造廠', 'CG-製造廠', NULL, 'On', '2024-05-06 09:29:09', '2024-05-06 09:29:09', '陳建良'),
(91, 10, 'LCDU', 'LCDU', NULL, 'On', '2024-05-06 09:24:52', '2024-05-06 09:24:52', '陳建良'),
(92, 10, 'MOD3', 'MOD3', NULL, 'On', '2024-05-06 09:27:47', '2024-05-06 09:27:47', '陳建良'),
(93, 10, 'TN-CarMOD廠', 'TN-CarMOD廠', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(94, 10, 'TN-Car其他Other', 'TN-Car其他Other', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(95, 10, '其他Other-C', '其他Other-C', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(96, 10, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 09:30:18', '2024-05-06 09:30:18', '陳建良'),
(97, 10, '整合INT-C', '整合INT-C', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(98, 10, '環安衛ESH', '環安衛ESH', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(99, 10, '總務GA', '總務GA', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(100, 10, '自動化AT(MAHS)-C', '自動化AT(MAHS)-C', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(101, 11, 'BLC', 'BLC', NULL, 'On', '2024-05-06 09:24:52', '2024-05-06 09:24:52', '陳建良'),
(102, 11, 'Cell(LCD)-6', 'Cell(LCD)-6', NULL, 'On', '2024-05-06 09:27:47', '2024-05-06 09:27:47', '陳建良'),
(103, 11, 'MOD-儲運管理WH', 'MOD-儲運管理WH', NULL, 'On', '2024-05-06 09:29:09', '2024-05-06 09:29:09', '陳建良'),
(104, 11, 'MOD-其他Other', 'MOD-其他Other', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(105, 11, 'MOD-整合INT', 'MOD-整合INT', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(106, 11, 'MOD1', 'MOD1', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(107, 11, 'MOD3', 'MOD3', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(108, 11, 'SGCux-辦公室Office', 'SGCux-辦公室Office', NULL, 'On', '2024-05-06 09:29:56', '2024-05-06 09:29:56', '陳建良'),
(109, 11, 'TV-Car品質管理QS', 'TV-Car品質管理QS', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(110, 11, 'TV-辦公室Office', 'TV-辦公室Office', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(111, 11, '品管-辦公室Office', '品管-辦公室Office', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(112, 11, '廠務FAC(CE)', '廠務FAC(CE)', NULL, 'On', '2024-05-06 09:30:18', '2024-05-06 09:30:18', '陳建良'),
(113, 11, '整合-辦公室Office', '整合-辦公室Office', NULL, 'On', '2024-05-06 09:30:46', '2024-05-06 09:30:46', '陳建良'),
(114, 11, '環安衛ESH', '環安衛ESH', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(115, 11, '研發-辦公室Office', '研發-辦公室Office', NULL, 'On', '2024-05-06 09:31:12', '2024-05-06 09:31:12', '陳建良'),
(116, 11, '總務GA', '總務GA', NULL, 'On', '2024-05-06 09:31:33', '2024-05-06 09:31:33', '陳建良'),
(117, 11, '自動化-辦公室Office', '自動化-辦公室Office', NULL, 'On', '2024-05-06 09:32:47', '2024-05-06 09:32:47', '陳建良'),
(118, 11, '自動化AT(MAHS)-6', '自動化AT(MAHS)-6', NULL, 'On', '2024-05-06 09:24:28', '2024-05-06 09:24:28', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_site`
--

CREATE TABLE `_site` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ai',
  `site_title` varchar(20) NOT NULL COMMENT 'site名稱',
  `site_remark` varchar(255) NOT NULL COMMENT 'site註解',
  `flag` varchar(3) NOT NULL COMMENT '開關',
  `created_at` datetime NOT NULL COMMENT '創建日期',
  `updated_at` datetime NOT NULL COMMENT '更新日期',
  `updated_user` varchar(10) NOT NULL COMMENT '建檔人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_site`
--

INSERT INTO `_site` (`id`, `site_title`, `site_remark`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 'tnSite', '南科廠區', 'On', '2023-07-03 14:22:27', '2024-04-19 09:10:13', '陳建良'),
(2, 'jnSite', '竹南廠區', 'Off', '2023-07-04 15:10:00', '2024-04-19 09:10:04', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_users`
--

CREATE TABLE `_users` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT 'ai',
  `emp_id` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '工號',
  `user` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '帳號',
  `cname` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `sign_code` varchar(20) CHARACTER SET utf8mb4 NOT NULL COMMENT '部門代號',
  `created_at` datetime DEFAULT NULL COMMENT '創建日期',
  `role` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT '1' COMMENT '權限',
  `idty` varchar(2) CHARACTER SET utf8mb4 DEFAULT '1' COMMENT '身份'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `_users`
--

INSERT INTO `_users` (`id`, `emp_id`, `user`, `cname`, `sign_code`, `created_at`, `role`, `idty`) VALUES
(0, '90000000', 'admin', 'invest管理員', '9T040500', '2022-08-17 09:39:31', '0', '1'),
(1, '90000001', 'susu', 'invest測試1', '9T041500', '2022-09-01 12:46:49', '1', '1'),
(3, '90000002', 'micro', 'invest測試2', '9T040500', '2022-12-07 15:14:40', '1', '1'),
(8, '13085117', 'DORISE.CHENG', '鄭羽淳', '9T042501', '2023-07-12 16:53:57', '3', '1'),
(10, '10009261', 'YC.SHIH', '施昱丞', '9T042501', '2023-10-17 11:20:21', '3', '1'),
(13, '10119798', 'CHIEH.SHEN', '沈旻頡', '9T041501', '2023-12-11 17:11:13', '3', '1'),
(14, '10008048', 'LEONG.CHEN', '陳建良', '9T041500', '2023-12-28 17:15:02', '0', '1'),
(15, '11046016', 'YA12.HSU', '徐慈雅', '6', '2023-12-29 08:43:48', '3', '1'),
(17, '10010721', 'ASKA.CHEN', '陳飛良', '9T041500', '2024-03-25 11:48:58', '2', '3');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `_document`
--
ALTER TABLE `_document`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_fab`
--
ALTER TABLE `_fab`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_flow`
--
ALTER TABLE `_flow`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_formcase`
--
ALTER TABLE `_formcase`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cate_no` (`_type`);

--
-- 資料表索引 `_local`
--
ALTER TABLE `_local`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_site`
--
ALTER TABLE `_site`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `_users`
--
ALTER TABLE `_users`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_document`
--
ALTER TABLE `_document`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'aid', AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_fab`
--
ALTER TABLE `_fab`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=16;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_flow`
--
ALTER TABLE `_flow`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_formcase`
--
ALTER TABLE `_formcase`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_local`
--
ALTER TABLE `_local`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=119;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_site`
--
ALTER TABLE `_site`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_users`
--
ALTER TABLE `_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
