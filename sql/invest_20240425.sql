-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-04-25 07:05:44
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
-- 資料表結構 `test`
--

CREATE TABLE `test` (
  `fab_id` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`fab_id`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 資料表結構 `_document`
--

CREATE TABLE `_document` (
  `id` int(10) NOT NULL COMMENT 'aid',
  `uuid` varchar(255) NOT NULL COMMENT '系統uuid',
  `idty` varchar(10) NOT NULL COMMENT '表單狀態',
  `dcc_no` varchar(255) NOT NULL COMMENT '套用dcc編號',
  `fab_id` int(10) NOT NULL COMMENT 'fab_id',
  `local_id` int(10) NOT NULL COMMENT 'local_id',
  `case_title` varchar(255) NOT NULL COMMENT '事件名稱',
  `a_dept` varchar(255) NOT NULL COMMENT '事故單位',
  `meeting_time` datetime NOT NULL COMMENT '會議時間',
  `meeting_local` varchar(255) NOT NULL COMMENT '會議地點',
  `meeting_man_a` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '1.事故當事者(或其委任代理人)' CHECK (json_valid(`meeting_man_a`)),
  `meeting_man_o` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '2.其他與會人員/勞工代表' CHECK (json_valid(`meeting_man_o`)),
  `meeting_man_s` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '3.環安人員' CHECK (json_valid(`meeting_man_s`)),
  `_focus` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '採集重點JSON' CHECK (json_valid(`_focus`)),
  `_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '訪談內容JSON' CHECK (json_valid(`_content`)),
  `confirm_sign` longtext DEFAULT NULL COMMENT '當事人同意上述描述',
  `ruling_sign` longtext DEFAULT NULL COMMENT '非公傷當事人簽認',
  `a_pic` varchar(255) DEFAULT NULL COMMENT '上傳路線圖檔',
  `created_emp_id` varchar(10) NOT NULL COMMENT '開單工號',
  `created_cname` varchar(10) NOT NULL COMMENT '開單姓名',
  `created_at` datetime NOT NULL COMMENT '建檔時間',
  `updated_at` datetime NOT NULL COMMENT '更新時間',
  `updated_cname` varchar(10) NOT NULL COMMENT '更新人員',
  `logs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Logs' CHECK (json_valid(`logs`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_document`
--

INSERT INTO `_document` (`id`, `uuid`, `idty`, `dcc_no`, `fab_id`, `local_id`, `case_title`, `a_dept`, `meeting_time`, `meeting_local`, `meeting_man_a`, `meeting_man_o`, `meeting_man_s`, `_focus`, `_content`, `confirm_sign`, `ruling_sign`, `a_pic`, `created_emp_id`, `created_cname`, `created_at`, `updated_at`, `updated_cname`, `logs`) VALUES
(3, 'fde10d39-0074-11ef-a147-2cfda183ef4f', '1', '13ES100016-F002-V002', 6, 9, '測試事件', 'GAA', '2024-04-22 14:53:00', 'TAC office 1F', '\"{\\\"cname\\\":\\\"\\u9673\\u5efa\\u826f\\\",\\\"emp_id\\\":\\\"22007223\\\"}\"', '\"{\\\"cname\\\":\\\"\\u9673\\u5efa\\u826f\\\",\\\"emp_id\\\":\\\"15111155\\\"}\"', '\"{\\\"cname\\\":\\\"\\u9673\\u5efa\\u826f\\\",\\\"emp_id\\\":\\\"11063892\\\"},{\\\"cname\\\":\\\"\\u9673\\u5efa\\u826f\\\",\\\"emp_id\\\":\\\"10008048\\\"}\"', '[]', '{\"emp_id\":\"22007223\",\"cname\":\"\\u9673\\u5efa\\u826f\",\"cstext\":\"\\u8a2d\\u5099\\u526f\\u5de5\\u7a0b\\u5e2b\",\"hired\":\"2024-04-01\",\"rload\":\"\\u4f30\\u7b97\\u7d04\\uff1a0 \\u5e74 0 \\u500b\\u6708 21 \\u5929\",\"a_day\":\"2024-04-01T08:00\",\"a_work_s\":\"2024-04-01T07:30\",\"a_work_e\":\"2024-04-01T19:30\",\"a_location\":\"test_local\",\"a_description\":\"test session3\",\"s4_combo_1\":[\"\\u5176\\u4ed6\",\"\\u50b7\\u5bb3\\u985e\\u578b-\\u5176\\u4ed6\"],\"s4_combo_2\":[\"\\u662f\"],\"s4_combo_3\":[\"\\u662f\"],\"s4_combo_4\":[\"\\u6709\"],\"s4_combo_5\":[\"\\u5831\\u6848\\u4e09\\u806f\\u55ae\",\"\\u4fdd\\u96aa\\u8b49\\u660e\",\"\\u4ea4\\u901a\\u88c1\\u6c7a\\u66f8\",\"\\u5176\\u4ed6\",\"\\u8209\\u8b49\\u8cc7\\u6599...other\"],\"s5_combo_1\":[\"\\u6709\"],\"s5_combo_2\":[\"\\u7121\"],\"s5_combo_3\":[\"\\u6709\"],\"s5_combo_4\":[\"\\u7121\"],\"s5_combo_5\":[\"\\u6709\"],\"s5_combo_6\":[\"\\u7121\"],\"s5_combo_7\":[\"\\u6709\"],\"s5_combo_8\":[\"\\u7121\"],\"s5_combo_9\":[\"\\u6709\"],\"s5_combo_10\":[\"\\u7121\"],\"s6_combo_1\":[\"\\u5426\"],\"s6_combo_2\":[\"\\u5426\",\"\\u5c31\\u662f\\u5426\"],\"remark\":\"test session7\"}', 'data:image/png;base64,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', 'data:image/png;base64,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', '20240422-145135_reurl_info.jpg', '10008048', '陳建良', '2024-04-22 14:53:14', '2024-04-22 14:53:14', '陳建良', '[{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-04-22 14:53:14\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"test command\"}]'),
(6, '78513518-02ae-11ef-82e5-2cfda183ef4f', '1', '13ES100016-F003-V003', 6, 9, '測試事件', 'G95', '2024-04-25 10:49:44', 'TAC office 1F', '\"\"', '\"{\\\"cname\\\":\\\"\\u99ac\\u5409\\u8b19\\\",\\\"emp_id\\\":\\\"10019391\\\"}\"', '\"{\\\"cname\\\":\\\"\\u9673\\u5efa\\u826f\\\",\\\"emp_id\\\":\\\"10008048\\\"}\"', 'null', '{\"a_day\":\"2024-04-01T08:00\",\"a_location\":\"test_local 1F\",\"anis_day\":\"2024-04-01\",\"esh_dept\":\"\\u6211\\u4e0d\\u77e5\\u9053\\u74b0\\u5b89\\u8981\\u586b\\u8ab0\",\"vendor\":\"\\u6211\\u4e0d\\u77e5\\u9053\\u627f\\u652c\\u5546\\u8981\\u586b\\u8ab0\",\"other_dept\":\"\\u6c92\\u6709\\u5176\\u4ed6\\u4eba\",\"a_description\":\"\\u6709\\u4e00\\u5929\\u5927\\u91ce\\u72fc\\u4e00\\u89ba\\u9192\\u4f86\\uff0c\\u5c31\\u767c\\u73fe\\u81ea\\u5df1\\u5927\\u809a\\u5b50\\u4e86\",\"emp_id\":\"00000000\",\"cname\":\"\\u5927\\u91ce\\u72fc\",\"oftext\":\"G95\",\"cstext\":\"\\u52a9\\u7406\\u6280\\u8853\\u54e1\",\"hired\":\"2023-04-01\",\"rload\":\"\\u4f30\\u7b97\\u7d04\\uff1a1 \\u5e74 0 \\u500b\\u6708 24 \\u5929\",\"s4_combo_1\":[\"1\",\"\",\"2\",\"\",\"3\",\"\"],\"s4_combo_2\":[\"\\u5176\\u4ed6\",\"\\u61f7\\u5b55\"],\"s4_combo_3\":[\"\\u5426\"],\"direct_cause\":\"\\u5927\\u809a\\u5b50\",\"s5_combo_1\":[\"\\u4ee5\\u4e0a\\u7686\\u662f\"],\"indirect_cause_ub\":\"\\u7761\\u89ba\\u4e0d\\u9396\\u9580\",\"indirect_cause_ue\":\"\\u4e00\\u500b\\u4eba\\u4f4f\\u5728\\u8352\\u91ce\\u5c0f\\u5c4b\\r\\n\",\"s5_combo_2\":[\"\\u4f5c\\u696d\\u7a0b\\u5e8f\\u4e0d\\u5b8c\\u6574\\u6216\\u4e0d\\u9069\\u7576\",\"\\u5de5\\u4f5c\\u7d00\\u5f8b\\u4e0d\\u826f\",\"\\u4eba\\u54e1\\u4f5c\\u696d\\u4e0d\\u614e\",\"\\u6559\\u80b2\\u8a13\\u7df4\\u4e0d\\u8db3\",\"\\u4eba\\u56e0\\u5de5\\u7a0b\",\"\\u65bd\\u5de5\\u54c1\\u8cea\\u4e0d\\u826f\",\"\\u96f6\\u4ef6\\u54c1\\u8cea\\u4e0d\\u826f\",\"\\u672a\\u843d\\u5be6\\u81ea\\u52d5\\u6aa2\\u67e5\\u6216\\u7dad\\u8b77\\u4fdd\\u990a\\u8a08\\u756b\",\"\\u8a2d\\u8a08\\u7455\\u75b5\\u6216\\u9632\\u8b77\\u63aa\\u65bd\\u4e0d\\u8db3\",\"\\u4f5c\\u696d\\u5371\\u5bb3\\u9451\\u5225\\u53ca\\u98a8\\u96aa\\u8a55\\u4f30\\u4e0d\\u78ba\\u5be6\\u6216\\u4e0d\\u8db3\"],\"basic_reasons\":\"\\u83ab\\u540d\\u5176\\u5999\\u5927\\u809a\\u5b50\",\"preventive_measures\":\"1.\\u7761\\u89ba\\u8981\\u95dc\\u9580\\r\\n2.\\u8981\\u7a7f\\u8932\\u5b50\",\"id\":\"\"}', NULL, NULL, NULL, '10008048', '陳建良', '2024-04-25 10:49:44', '2024-04-25 10:49:44', '陳建良', '[{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-04-25 10:49:44\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u6e2c\\u8a662\"}]'),
(7, '43f616b7-02b5-11ef-82e5-2cfda183ef4f', '1', '13ES100016-F003-V003', 6, 9, '測試事件x', 'G95', '2024-04-25 11:38:22', 'TAC office 1F', '\"\"', '\"{\\\"cname\\\":\\\"\\u99ac\\u5409\\u8b19\\\",\\\"emp_id\\\":\\\"10019391\\\"}\"', '\"{\\\"cname\\\":\\\"\\u9673\\u5efa\\u826f\\\",\\\"emp_id\\\":\\\"10008048\\\"}\"', 'null', '{\"a_day\":\"2024-04-01T08:00\",\"a_location\":\"test_local\",\"anis_day\":\"2024-04-01\",\"a_belong_dept\":\"G95\",\"esh_dept\":\"\\u6211\\u4e0d\\u77e5\\u9053\\u74b0\\u5b89\\u8981\\u586b\\u8ab0\",\"vendor\":\"\\u6211\\u4e0d\\u77e5\\u9053\\u627f\\u652c\\u5546\\u8981\\u586b\\u8ab0\",\"other_dept\":\"\\u6c92\\u6709\\u5176\\u4ed6\\u4eba\",\"a_description\":\"\\u6709\\u4e00\\u5929\\u5927\\u91ce\\u72fc\\u4e00\\u89ba\\u9192\\u4f86\\uff0c\\u5c31\\u767c\\u73fe\\u81ea\\u5df1\\u5927\\u809a\\u5b50\\u4e86\",\"emp_id\":\"00000000\",\"cname\":\"\\u5927\\u91ce\\u72fc\",\"oftext\":\"G95\",\"cstext\":\"\\u52a9\\u7406\\u6280\\u8853\\u54e1\",\"hired\":\"2024-04-01\",\"rload\":\"\\u4f30\\u7b97\\u7d04\\uff1a0 \\u5e74 0 \\u500b\\u6708 24 \\u5929\",\"s4_combo_1\":[\"1\",\"\\u809a\\u5b50\",\"2\",\"\",\"3\",\"\"],\"s4_combo_2\":[\"\\u5176\\u4ed6\",\"\\u61f7\\u5b55\"],\"s4_combo_3\":[\"\\u5426\"],\"direct_cause\":\"\\u5927\\u809a\\u5b50\",\"s5_combo_1\":[\"\\u4ee5\\u4e0a\\u7686\\u662f\"],\"indirect_cause_ub\":\"\\u7761\\u89ba\\u4e0d\\u9396\\u9580\",\"indirect_cause_ue\":\"\\u4e00\\u500b\\u4eba\\u4f4f\\u5728\\u8352\\u91ce\\u5c0f\\u5c4b\",\"s5_combo_2\":[\"\\u4f5c\\u696d\\u7a0b\\u5e8f\\u4e0d\\u5b8c\\u6574\\u6216\\u4e0d\\u9069\\u7576\",\"\\u5de5\\u4f5c\\u7d00\\u5f8b\\u4e0d\\u826f\",\"\\u4eba\\u54e1\\u4f5c\\u696d\\u4e0d\\u614e\",\"\\u6559\\u80b2\\u8a13\\u7df4\\u4e0d\\u8db3\",\"\\u4eba\\u56e0\\u5de5\\u7a0b\",\"\\u65bd\\u5de5\\u54c1\\u8cea\\u4e0d\\u826f\",\"\\u96f6\\u4ef6\\u54c1\\u8cea\\u4e0d\\u826f\",\"\\u672a\\u843d\\u5be6\\u81ea\\u52d5\\u6aa2\\u67e5\\u6216\\u7dad\\u8b77\\u4fdd\\u990a\\u8a08\\u756b\",\"\\u8a2d\\u8a08\\u7455\\u75b5\\u6216\\u9632\\u8b77\\u63aa\\u65bd\\u4e0d\\u8db3\",\"\\u4f5c\\u696d\\u5371\\u5bb3\\u9451\\u5225\\u53ca\\u98a8\\u96aa\\u8a55\\u4f30\\u4e0d\\u78ba\\u5be6\\u6216\\u4e0d\\u8db3\"],\"basic_reasons\":\"\\u83ab\\u540d\\u5176\\u5999\\u5927\\u809a\\u5b50\",\"preventive_measures\":\"1.\\u7761\\u89ba\\u8981\\u95dc\\u9580\\r\\n2.\\u8981\\u7a7f\\u8932\\u5b50\"}', NULL, NULL, NULL, '10008048', '陳建良', '2024-04-25 11:38:22', '2024-04-25 11:38:22', '陳建良', '[{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-04-25 11:38:22\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u6e2c\\u8a663\"}]');

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
(1, 1, '一般用戶', 'virtual', 'Off', '9T040500', '10008048,陳建良', '2023-07-03 14:30:51', '2024-03-29 11:58:40', '陳建良'),
(2, 1, 'tnESH', '台南環安處', 'Off', '9T040500', '10008048,陳建良,13099963,梁修豪', '2023-07-03 14:30:51', '2024-03-29 11:58:21', '陳建良'),
(3, 1, 'FAB1棟', '一廠', 'On', '9T041501', '', '2023-07-04 15:11:08', '2024-03-29 11:00:58', '陳建良'),
(4, 1, 'FAB2棟', '二廠', 'On', '9T043501', NULL, '2023-07-04 15:11:30', '2023-10-26 13:56:51', '陳建良'),
(5, 1, 'FAB3棟', '三廠', 'On', '9T043501', NULL, '2023-07-04 15:11:59', '2023-10-26 13:56:57', '陳建良'),
(6, 1, 'TAC棟', '四廠', 'On', '9T042501', '', '2023-07-04 15:12:32', '2024-03-29 11:01:03', '陳建良'),
(7, 1, 'FAB5棟', '五廠', 'On', '9T043502', NULL, '2023-07-04 15:12:52', '2023-10-26 13:57:02', '陳建良'),
(8, 1, 'FAB6棟', '六廠', 'On', '9T044501', NULL, '2023-07-04 15:13:11', '2023-10-26 13:54:28', '陳建良'),
(9, 1, 'FAB7棟', '七廠', 'On', '9T041502', '', '2023-07-04 15:13:30', '2024-03-29 11:00:48', '陳建良'),
(10, 1, 'FAB8棟', '八廠', 'On', '9T044502', NULL, '2023-07-04 15:14:07', '2023-10-26 13:54:18', '陳建良'),
(11, 1, 'LCM棟', 'C廠區', 'On', '9T042502', '', '2023-07-04 15:14:29', '2023-12-19 11:14:31', '陳建良'),
(12, 1, 'TOC棟', 'TOC', 'On', '9T042501', '', '2023-07-04 15:15:35', '2024-03-29 11:00:42', '陳建良'),
(13, 1, 'K9棟', '科九', 'On', '9T042501', '', '2023-07-04 15:16:11', '2024-03-29 11:00:31', '陳建良'),
(15, 1, 'T6', 'T6', 'On', '9T044502', '', '2024-01-24 12:48:33', '2024-03-29 11:00:36', '陳建良');

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
(1, '13ES100016-F002', '南廠區交通事故訪談表', '交通事故', '13ES100016-F002-V002', 'fa-solid fa-car-burst', 'On', '2024-02-01 03:24:12', '2024-04-25 11:45:55', '陳建良'),
(2, '13ES100016-F003', '南廠區廠內事故訪談表', '廠內事故', '13ES100016-F003-V003', 'fa-solid fa-house-circle-exclamation', 'On', '2024-02-01 11:02:18', '2024-04-25 11:46:49', '陳建良'),
(6, 'test', 'test', '測試', 'autolog', 'fa-brands fa-facebook', 'Off', '2024-04-12 14:07:01', '2024-04-25 11:46:58', '陳建良');

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
-- 資料表索引 `_formcase`
--
ALTER TABLE `_formcase`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cate_no` (`_type`);

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
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'aid', AUTO_INCREMENT=8;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_fab`
--
ALTER TABLE `_fab`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=16;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_formcase`
--
ALTER TABLE `_formcase`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
