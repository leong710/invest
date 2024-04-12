-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-04-12 08:29:27
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

INSERT INTO `_formcase` (`id`, `_type`, `title`, `dcc_no`, `_icon`, `flag`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, '13ES100016-F002', '南廠區交通事故訪談表', '13ES100016-F002-V002', '<i class=\"fa-solid fa-car-burst fa-10x\"></i>', 'On', '2024-02-01 03:24:12', '2024-04-12 12:57:06', '陳建良'),
(2, '13ES100016-F003', '南廠區廠內事故訪談表', '13ES100016-F003-V003', '<i class=\"fa-solid fa-house-circle-exclamation fa-10x\"></i>', 'On', '2024-02-01 11:02:18', '2024-04-12 12:58:24', '陳建良'),
(6, 'test', 'test', 'autolog', '?', 'Off', '2024-04-12 14:07:01', '2024-04-12 14:07:01', '陳建良');

-- --------------------------------------------------------

--
-- 資料表結構 `_formplan`
--

CREATE TABLE `_formplan` (
  `id` int(10) NOT NULL COMMENT 'aid',
  `_type` varchar(20) NOT NULL COMMENT '表單類別',
  `remark` varchar(255) NOT NULL COMMENT '說明',
  `flag` varchar(3) NOT NULL COMMENT '計劃開關',
  `start_time` datetime NOT NULL COMMENT '起始時間',
  `end_time` datetime NOT NULL COMMENT '結束時間',
  `_inplan` varchar(10) NOT NULL COMMENT '啟動的起始值',
  `created_at` datetime NOT NULL COMMENT '建檔時間',
  `updated_at` datetime NOT NULL COMMENT '更新時間',
  `updated_user` varchar(10) NOT NULL COMMENT '更新人員'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `_formplan`
--

INSERT INTO `_formplan` (`id`, `_type`, `remark`, `flag`, `start_time`, `end_time`, `_inplan`, `created_at`, `updated_at`, `updated_user`) VALUES
(1, 'issue', '上半年請購時間', 'On', '2024-02-01 08:00:00', '2024-03-01 17:00:00', 'On', '2024-01-18 14:48:33', '2024-02-06 12:31:08', '陳建良'),
(2, 'issue', '下半年請購時間', 'On', '2024-08-01 08:00:00', '2024-09-01 17:00:00', 'On', '2024-01-18 15:16:43', '2024-02-06 12:31:23', '陳建良'),
(4, 'ptstock', '除汙劑庫存上半年點檢時間', 'On', '2023-01-01 08:00:00', '2023-05-01 17:00:00', 'On', '2024-01-18 15:16:43', '2024-04-09 12:15:21', '陳建良'),
(5, 'stock', 'PPE庫存上半年點檢時間', 'On', '2024-01-01 08:00:00', '2024-05-01 17:00:00', 'On', '2024-02-01 12:32:48', '2024-04-09 12:15:26', '陳建良'),
(6, 'ptstock', '除汙劑庫存下半年點檢時間', 'On', '2024-07-01 08:00:00', '2024-08-01 17:00:00', 'On', '2024-02-06 12:34:06', '2024-02-06 12:34:58', '陳建良'),
(7, 'stock', 'PE庫存下半年點檢時間', 'On', '2024-07-01 08:00:00', '2024-08-01 17:00:00', 'On', '2024-02-06 12:34:46', '2024-02-06 12:34:46', '陳建良');

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
(1, 'tnSite', '南科', 'On', '2023-07-03 14:22:27', '2023-11-07 10:21:31', '陳建良'),
(2, 'tempStorage', '暫存區', 'Off', '2023-07-04 15:10:00', '2023-11-27 10:27:13', '陳建良');

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
-- 資料表索引 `_formplan`
--
ALTER TABLE `_formplan`
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
-- 使用資料表自動遞增(AUTO_INCREMENT) `_fab`
--
ALTER TABLE `_fab`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ai', AUTO_INCREMENT=16;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_formcase`
--
ALTER TABLE `_formcase`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `_formplan`
--
ALTER TABLE `_formplan`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'aid', AUTO_INCREMENT=8;

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
