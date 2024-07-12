-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-07-12 10:56:19
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
-- 資料表結構 `autolog`
--

CREATE TABLE `autolog` (
  `id` int(10) NOT NULL COMMENT 'aid',
  `thisDay` varchar(10) NOT NULL COMMENT 'Log日期',
  `sys` varchar(50) NOT NULL COMMENT '系統名稱',
  `logs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '記錄事項',
  `t_stamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '記錄時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `omager` varchar(10) NOT NULL COMMENT '主管工號',
  `in_sign` varchar(10) DEFAULT NULL COMMENT '待簽人員ID',
  `in_signName` varchar(10) DEFAULT NULL COMMENT '待簽人員姓名',
  `flow` varchar(255) DEFAULT NULL COMMENT 'approval_steps/步驟名稱',
  `_odd` varchar(255) DEFAULT NULL COMMENT '職災申報',
  `_focus` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '採集重點JSON' CHECK (json_valid(`_focus`)),
  `_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '訪談內容JSON' CHECK (json_valid(`_content`)),
  `confirm_sign` varchar(255) DEFAULT NULL COMMENT '當事人同意上述描述',
  `ruling_sign` longtext DEFAULT NULL COMMENT '上傳自述PDF',
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

INSERT INTO `_document` (`id`, `uuid`, `idty`, `dcc_no`, `fab_id`, `local_id`, `anis_no`, `case_title`, `a_dept`, `meeting_time`, `meeting_local`, `meeting_man_a`, `meeting_man_o`, `meeting_man_s`, `meeting_man_d`, `omager`, `in_sign`, `in_signName`, `flow`, `_odd`, `_focus`, `_content`, `confirm_sign`, `ruling_sign`, `a_pic`, `created_emp_id`, `created_cname`, `created_at`, `updated_at`, `updated_cname`, `logs`, `editions`) VALUES
(1, 'eab987d4-4016-11ef-a0ba-2cfda183ef4f', '10', '13ES100016-F003-V003d', 2, 1, 'ANIS20240712140000000', '測試事件x1', 'GAA1', '2024-07-12 14:07:00', 'office 1F', '\"{\\\"cname\\\":\\\"\\u99ac\\u5409\\u5152\\\",\\\"emp_id\\\":\\\"17005107\\\"}\"', '\"{\\\"cname\\\":\\\"\\u99ac\\u6771\\u5c3c\\\",\\\"emp_id\\\":\\\"23004863\\\"}\"', '\"{\\\"cname\\\":\\\"\\u99ac\\u5409\\u8b19\\\",\\\"emp_id\\\":\\\"10019391\\\"},{\\\"cname\\\":\\\"\\u9673\\u5efa\\u826f\\\",\\\"emp_id\\\":\\\"10008048\\\"}\"', '馬東食', '14124253', NULL, NULL, NULL, '{\"due_day\":\"2024-08-05\",\"od_day\":\"\"}', '{\"a_self_desc\":null,\"a_others_desc\":null}', '{\"emp_id\":\"17005107\",\"cname\":\"\\u99ac\\u5409\\u5152\",\"oftext\":\"9O432505\\/\\u53f0\\u5357MOD\\u88fd\\u9020\\u5ee0\\/\\u88fd\\u9020\\u5de5\\u7a0b\\u4e8c\\u90e8\\/\\u88fd\\u9020\\u8ab2\",\"cstext\":\"\\u52a9\\u7406\\u6280\\u8853\\u54e1\",\"s1_combo_NATIO\":[\"\\u5916\\u7c4d\"],\"s1_combo_GESCH\":[\"\\u5973\\u6027\"],\"hired\":\"2023-04-01\",\"rload\":\"\\u4f30\\u7b97\\u7d04\\uff1a1 \\u5e74 3 \\u500b\\u6708 9 \\u5929\",\"s1_combo_emp_type\":[\"PM\"],\"s1_combo_years_level\":[\"20u-25d\"],\"a_day\":\"2024-07-10T08:00\",\"a_location\":\"test_local\",\"a_work_s\":\"2024-07-10T07:30\",\"a_work_e\":\"2024-07-10T19:30\",\"s2_combo_01\":[\"\\u65e5\\u73ed\"],\"s2_combo_02\":[\"\\u65e5\\u9593\"],\"s2_combo_03\":[\"\\u81ea\\u6454\"],\"s2_combo_04\":[\"\\u662f\"],\"s2_combo_05\":[\"\\u662f\"],\"s2_combo_06\":[\"\\u4eba\\u50b7\"],\"s2_combo_07\":[\"\\u4eba\\u54e1\\u53d7\\u50b7\"],\"s2_combo_08\":\"02.\\u8dcc\\u5012\\u3001\\u6ed1\\u5012\",\"a_description\":\"test session3 \\u4e8b\\u6545\\u6558\\u8ff0\",\"s4_combo_01\":[\"\\u5426\"],\"s4_combo_02\":[\"\\u66ab\\u6642\\u5168\\u5931\\u80fd\"],\"s4_combo_03\":[\"\\u640d\\u5de5\",\"2\"],\"s4_text_a\":\"\\u7834\\u76ae\",\"s5a_combo_01\":[\"\\u5426\"],\"s5a_combo_02\":[\"\\u5426\"],\"s5a_combo_03\":[\"\\u5426\"],\"s5a_combo_07\":[\"\\u5426\"],\"s5a_combo_08\":[\"\\u5426\"],\"s5a_combo_09\":[\"\\u662f\"],\"s5a_combo_10\":[\"\\u5426\"],\"s5a_combo_11\":[\"\\u5426\"],\"s5a_text_other\":\"\",\"s5b_combo_01\":[\"\\u5426\"],\"s5b_combo_02\":[\"\\u5426\"],\"s5b_combo_03\":[\"\\u5426\"],\"s5b_combo_04\":[\"\\u5426\"],\"s6a_combo_01\":[\"\\u5426\"],\"s6a_combo_02\":[\"\\u5426\"],\"s6a_combo_03\":[\"\\u5426\"],\"s6a_combo_04\":[\"\\u5426\"],\"s6a_combo_05\":[\"\\u5426\"],\"s6a_text_other\":\"\",\"s6b_combo_01\":[\"\\u5426\"],\"s6b_combo_02\":[\"\\u5426\"],\"s6b_combo_03\":[\"\\u5426\"],\"s6b_combo_04\":[\"\\u5426\"],\"s6c_combo_01\":[\"\\u5426\"],\"s6c_combo_02\":[\"\\u5426\"],\"s6c_combo_03\":[\"\\u5426\"],\"s6c_combo_10\":[\"\\u5426\"],\"s6c_text_other\":\"\",\"s7b_combo_05\":[\"\\u5426\"],\"s7b_combo_06\":[\"\\u5426\"],\"s7b_text_other\":\"\",\"s8_direct_cause\":\"\\u5617\\u8a66\\u8dcc\\u5012\",\"s8_combo_02\":[\"\\u4e0d\\u5b89\\u5168\\u884c\\u70ba\",\"UD_\\u7f3a\\u4e4f\\u6ce8\\u610f\\u529b\\u548c\\u8a8d\\u77e5(\\u63cf\\u8ff0\\u4e8b\\u4ef6\\u3001\\u884c\\u70ba)\"],\"s8_basic_reasons_remark\":\"\\u57fa\\u672c\\u4e0a\\u662f\\u8dcc\\u5012\",\"s8_preventive_measures\":\"\\u5750\\u8f4e\\u5b50\",\"s8_combo_01\":[\"\\u662f\"],\"s8_basic_reasons_combo\":[\"\\u672a\\u843d\\u5be6\\u81ea\\u52d5\\u6aa2\\u67e5\\u6216\\u7dad\\u8b77\\u4fdd\\u990a\\u8a08\\u756b\"]}', '4700165366_201711_請購筆電.pdf', NULL, 'P-052z.jpg', '90000001', 'susu', '2024-07-12 14:21:04', '2024-07-12 15:12:33', '陳建良', '[{\"step\":\"1\",\"cname\":\"susu (90000001)\",\"datetime\":\"2024-07-12 14:21:04\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u6e2c\\u8a66\\u7acb\\u6848\"},{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-07-12 14:50:11\",\"action\":\"\\u66ab\\u5b58 (Save)\",\"remark\":\"\"},{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-07-12 14:50:59\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-07-12 14:51:39\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-07-12 14:59:54\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-07-12 15:12:13\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"},{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-07-12 15:12:33\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"}]', '[{\"updated_at\":\"2024-07-12 14:50:11\",\"updated_cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"update_document\":{\"idty\":\"1 => 6\",\"_content\":{\"s8_basic_reasons\":\"[\\\"\\u672a\\u843d\\u5be6\\u81ea\\u52d5\\u6aa2\\u67e5\\u6216\\u7dad\\u8b77\\u4fdd\\u990a\\u8a08\\u756b\\\"] => \"}}},{\"updated_at\":\"2024-07-12 14:50:59\",\"updated_cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"update_document\":{\"idty\":\"6 => 1\",\"_content\":{\"s8_combo_02\":\"[\\\"\\u4e0d\\u5b89\\u5168\\u884c\\u70ba\\\",\\\"UD_\\u7f3a\\u4e4f\\u6ce8\\u610f\\u529b\\u548c\\u8a8d\\u77e5(\\u63cf\\u8ff0\\u4e8b\\u4ef6\\u3001\\u884c\\u70ba)\\\"] => [\\\"\\u4e0d\\u5b89\\u5168\\u884c\\u70ba\\\",\\\"UD_\\u7f3a\\u4e4f\\u6ce8\\u610f\\u529b\\u548c\\u8a8d\\u77e5(\\u63cf\\u8ff0\\u4e8b\\u4ef6\\u3001\\u884c\\u70ba)\\\",\\\"\\u4e0d\\u5b89\\u5168\\u74b0\\u5883\\\",\\\"\\\",\\\"\\u4ee5\\u4e0a\\u7686\\u662f\\\"]\",\"s8_basic_reasons\":\" => [\\\"\\u672a\\u843d\\u5be6\\u81ea\\u52d5\\u6aa2\\u67e5\\u6216\\u7dad\\u8b77\\u4fdd\\u990a\\u8a08\\u756b\\\"]\"}}},{\"updated_at\":\"2024-07-12 14:51:39\",\"updated_cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"update_document\":{\"_content\":{\"s8_combo_02\":\"[\\\"\\u4e0d\\u5b89\\u5168\\u884c\\u70ba\\\",\\\"UD_\\u7f3a\\u4e4f\\u6ce8\\u610f\\u529b\\u548c\\u8a8d\\u77e5(\\u63cf\\u8ff0\\u4e8b\\u4ef6\\u3001\\u884c\\u70ba)\\\",\\\"\\u4e0d\\u5b89\\u5168\\u74b0\\u5883\\\",\\\"\\\",\\\"\\u4ee5\\u4e0a\\u7686\\u662f\\\"] => [\\\"\\u4e0d\\u5b89\\u5168\\u884c\\u70ba\\\",\\\"UD_\\u7f3a\\u4e4f\\u6ce8\\u610f\\u529b\\u548c\\u8a8d\\u77e5(\\u63cf\\u8ff0\\u4e8b\\u4ef6\\u3001\\u884c\\u70ba)\\\"]\"}}},{\"updated_at\":\"2024-07-12 15:12:13\",\"updated_cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"update_document\":{\"_content\":{\"s8_basic_reasons\":\"[\\\"\\u672a\\u843d\\u5be6\\u81ea\\u52d5\\u6aa2\\u67e5\\u6216\\u7dad\\u8b77\\u4fdd\\u990a\\u8a08\\u756b\\\"] => \"}}},{\"updated_at\":\"2024-07-12 15:12:33\",\"updated_cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"update_document\":{\"_content\":{\"s8_basic_reasons_combo\":\" => [\\\"\\u672a\\u843d\\u5be6\\u81ea\\u52d5\\u6aa2\\u67e5\\u6216\\u7dad\\u8b77\\u4fdd\\u990a\\u8a08\\u756b\\\"]\"}}}]'),
(2, '4198bd9a-4027-11ef-a0ba-2cfda183ef4f', '1', '13ES100016-F003-V003e', 3, 12, 'ANIS20240712163055555', '測試事件x2', 'GAA2', '2024-07-12 16:15:00', 'office 1F', '\"\"', '\"{\\\"cname\\\":\\\"\\u99ac\\u6771\\u5c3c\\\",\\\"emp_id\\\":\\\"23004863\\\"}\"', '\"{\\\"cname\\\":\\\"\\u99ac\\u5409\\u8b19\\\",\\\"emp_id\\\":\\\"10019391\\\"},{\\\"cname\\\":\\\"\\u9673\\u5efa\\u826f\\\",\\\"emp_id\\\":\\\"10008048\\\"}\"', '馬東食', '', NULL, NULL, NULL, '[]', '{\"a_self_desc\":\"\\u5357\\u5ee0\\u5340\\u4ea4\\u901a\\u4e8b\\u6545\\u8a2a\\u8ac7\\u8868_ANIS20240603114700123(empty).pdf\",\"a_others_desc\":\"\\u5357\\u5ee0\\u5340\\u4ea4\\u901a\\u4e8b\\u6545\\u8a2a\\u8ac7\\u8868_ANIS20240603114700123.pdf\"}', '{\"emp_id\":\"\",\"cname\":\"\",\"oftext\":\"\",\"cstext\":\"\",\"hired\":\"\",\"rload\":\"(\\u586b\\u5b8csession_2 \\u4e8b\\u6545\\u6642\\u9593\\uff0c\\u6b64\\u6b04\\u81ea\\u52d5\\u66f4\\u65b0)\",\"a_day\":\"2024-07-12T10:00\",\"a_location\":\"test_local\",\"s2_combo_01\":[\"\\u65e5\\u73ed\"],\"s2_combo_05\":[\"\\u662f\"],\"s2_combo_06\":[\"\\u8a2d\\u5099\"],\"s2_combo_07\":[\"\\u751f\\u7522\\u4e2d\\u65b7\"],\"s2_combo_08\":\"01.\\u7121\\u5875\\u5ba4\\u5168\\u54e1\\u758f\\u6563\\u6216\\u88fd\\u9020\\u5ee0\\u7522\\u7dda\\u505c\\u6b62\\u751f\\u7522\",\"a_description\":\"test session3 \\u4e8b\\u6545\\u6558\\u8ff0\",\"s8_direct_cause\":\"\\u6e2c\\u8a66\\u4e8b\\u6545\\u76f4\\u63a5\\u539f\\u56e0\",\"s8_combo_02\":[\"\\u4e0d\\u5b89\\u5168\\u74b0\\u5883\",\"IA_\\u8b66\\u793a\\u9632\\u8b77\\u8a2d\\u65bd(\\u74b0\\u5883\\u4e0a\\u7684\\u8b66\\u793a)\"],\"s8_basic_reasons_combo\":[\"\\u4f5c\\u696d\\u5371\\u5bb3\\u9451\\u5225\\u53ca\\u98a8\\u96aa\\u8a55\\u4f30\\u4e0d\\u78ba\\u5be6\\u6216\\u4e0d\\u8db3\"],\"s8_basic_reasons_remark\":\"\\u6e2c\\u8a66\\u4e8b\\u6545\\u57fa\\u672c\\u539f\\u56e0\",\"s8_preventive_measures\":\"\\u6e2c\\u8a66\\u95dc\\u6a5f\"}', NULL, NULL, NULL, '90000001', 'susu', '2024-07-12 16:18:02', '2024-07-12 16:20:37', 'susu', '[{\"step\":\"1\",\"cname\":\"susu (90000001)\",\"datetime\":\"2024-07-12 16:18:02\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\\u6e2c\\u8a66\\u7acb\\u6848\"},{\"step\":\"1\",\"cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"datetime\":\"2024-07-12 16:18:55\",\"action\":\"\\u66ab\\u5b58 (Save)\",\"remark\":\"\"},{\"step\":\"1\",\"cname\":\"susu (90000001)\",\"datetime\":\"2024-07-12 16:20:37\",\"action\":\"\\u9001\\u51fa (Submit)\",\"remark\":\"\"}]', '[{\"updated_at\":\"2024-07-12 16:18:55\",\"updated_cname\":\"\\u9673\\u5efa\\u826f (10008048)\",\"update_document\":{\"idty\":\"1 => 6\"}},{\"updated_at\":\"2024-07-12 16:20:37\",\"updated_cname\":\"susu (90000001)\",\"update_document\":{\"idty\":\"6 => 1\",\"meeting_man_d\":\" => \\u99ac\\u6771\\u98df\",\"meeting_man_o\":\" => {\\\"\\u99ac\\u6771\\u5c3c\\\":\\\"23004863\\\"}\",\"meeting_man_s\":\" => {\\\"\\u99ac\\u5409\\u8b19\\\":\\\"10019391\\\"},{\\\"\\u9673\\u5efa\\u826f\\\":\\\"10008048\\\"}\",\"a_self_desc\":\" => \\u5357\\u5ee0\\u5340\\u4ea4\\u901a\\u4e8b\\u6545\\u8a2a\\u8ac7\\u8868_ANIS20240603114700123(empty).pdf\",\"a_others_desc\":\" => \\u5357\\u5ee0\\u5340\\u4ea4\\u901a\\u4e8b\\u6545\\u8a2a\\u8ac7\\u8868_ANIS20240603114700123.pdf\"}}]'),
(3, '1d47bae0-402c-11ef-a0ba-2cfda183ef4f', '6', '13ES100016-F003-V003c', 4, 23, 'ANIS20240712165055555', '測試事件x3', 'GAA3', '2024-07-12 16:52:00', 'office 1F', '\"\"', '\"\"', '\"\"', '', '', NULL, NULL, NULL, '[]', '{\"a_self_desc\":null,\"a_others_desc\":null}', '{\"emp_id\":\"\",\"cname\":\"\",\"oftext\":\"\",\"cstext\":\"\",\"hired\":\"\",\"rload\":\"\",\"a_day\":\"\",\"a_location\":\"\",\"s2_combo_06\":[\"\\u4eba\\u50b7\"],\"s2_combo_07\":[\"\\u5176\\u4ed6\"],\"s2_combo_08\":\"09.\\u751f\\u6d3b\\u6027\\u5de5\\u50b7\",\"a_description\":\"\",\"s4_text_a\":\"\",\"s5a_text_other\":\"\",\"s6a_text_other\":\"\",\"s6c_text_other\":\"\",\"s7b_text_other\":\"\",\"s8_direct_cause\":\"\",\"s8_basic_reasons_remark\":\"\",\"s8_preventive_measures\":\"\"}', NULL, NULL, NULL, '90000001', 'susu', '2024-07-12 16:52:48', '2024-07-12 16:52:48', 'susu', '[{\"step\":\"1\",\"cname\":\"susu (90000001)\",\"datetime\":\"2024-07-12 16:52:48\",\"action\":\"\\u66ab\\u5b58 (Save)\",\"remark\":\"\"}]', NULL);

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
(12, 1, 'K9棟', '科九', 'On', '9T042501', '', '2023-07-04 15:16:11', '2024-03-29 11:00:31', '陳建良'),
(16, 2, 'HQ棟', '企業總部', 'Off', '8N051501', '21001775,陳明維', '2024-06-21 15:31:18', '2024-06-21 15:38:32', '陳建良'),
(17, 2, 'JOC棟', '企業總部', 'Off', '8N051501', '11083897,侯志郎', '2024-06-21 15:33:09', '2024-06-21 15:38:21', '陳建良'),
(18, 2, 'T3棟', '企業總部', 'Off', '8N051501', '10042943,張簡旭成', '2024-06-21 15:34:38', '2024-06-21 15:38:01', '陳建良');

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
  `_type` varchar(20) NOT NULL COMMENT 'DCC_no無版次',
  `title` varchar(255) NOT NULL COMMENT '表單名稱',
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
(1, '13ES100016-F002b', '南廠區交通事故訪談表', '廠外交通事故', '13ES100016-F002-V002b', 'fa-solid fa-motorcycle', 'On', '2024-06-13 16:38:59', '2024-06-13 16:54:10', '陳建良'),
(2, '13ES100016-F003b', '南廠區廠內事故訪談表', '廠內交通事故', '13ES100016-F003-V003b', 'fa-solid fa-car-burst', 'On', '2024-06-13 16:41:19', '2024-06-13 16:53:07', '陳建良'),
(3, '13ES100016-F003c', '南廠區廠內事故訪談表', '生活性工傷', '13ES100016-F003-V003c', 'fa-solid fa-utensils', 'On', '2024-06-13 16:44:17', '2024-06-27 12:15:29', '陳建良'),
(5, '13ES100016-F003d', '南廠區廠內事故訪談表', '人傷事故', '13ES100016-F003-V003d', 'fa-solid fa-person-falling-burst', 'On', '2024-06-27 12:13:20', '2024-06-27 12:13:35', '陳建良'),
(6, '13ES100016-F003e', '南廠區廠內事故訪談表', '設備／其他事故', '13ES100016-F003-V003e', 'fa-solid fa-fire-burner', 'On', '2024-06-27 12:17:12', '2024-07-05 09:34:16', '陳建良');

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
(1, 'TN', '南科廠區', 'On', '2023-07-03 14:22:27', '2024-07-01 08:42:35', '陳建良'),
(2, 'JN', '竹南廠區', 'Off', '2023-07-04 15:10:00', '2024-07-01 08:42:43', '陳建良');

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
(1, '90000001', 'susu', 'invest測試1', '9T041500', '2022-09-01 12:46:49', '2', '1'),
(3, '90000002', 'micro', 'invest測試2', '9T040500', '2022-12-07 15:14:40', '1', '1'),
(8, '13085117', 'DORISE.CHENG', '鄭羽淳', '9T042501', '2023-07-12 16:53:57', '3', '1'),
(10, '10009261', 'YC.SHIH', '施昱丞', '9T042501', '2023-10-17 11:20:21', '3', '1'),
(13, '10119798', 'CHIEH.SHEN', '沈旻頡', '9T041501', '2023-12-11 17:11:13', '3', '1'),
(14, '10008048', 'LEONG.CHEN', '陳建良', '9T041500', '2023-12-28 17:15:02', '1', '1'),
(15, '11046016', 'YA12.HSU', '徐慈雅', '6', '2023-12-29 08:43:48', '3', '1'),
(17, '10010721', 'ASKA.CHEN', '陳飛良', '9T041500', '2024-03-25 11:48:58', '2', '3');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `autolog`
--
ALTER TABLE `autolog`
  ADD PRIMARY KEY (`id`);

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
-- 使用資料表自動遞增(AUTO_INCREMENT) `autolog`
--
ALTER TABLE `autolog`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'aid';

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_document`
--
ALTER TABLE `_document`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'aid', AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_fab`
--
ALTER TABLE `_fab`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=19;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_flow`
--
ALTER TABLE `_flow`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_formcase`
--
ALTER TABLE `_formcase`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
