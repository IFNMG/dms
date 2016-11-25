-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 22, 2016 at 05:50 PM
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `droidbox`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicationimages`
--

CREATE TABLE IF NOT EXISTS `applicationimages` (
  `Id` int(8) NOT NULL AUTO_INCREMENT,
  `ApplicationId` int(8) NOT NULL,
  `Url` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `CreatedBy` int(6) NOT NULL,
  `CreatedOn` datetime NOT NULL,
  `ModifedBy` int(6) DEFAULT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ApplicationId` (`ApplicationId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=50 ;

--
-- Dumping data for table `applicationimages`
--

INSERT INTO `applicationimages` (`Id`, `ApplicationId`, `Url`, `Description`, `IsActive`, `CreatedBy`, `CreatedOn`, `ModifedBy`, `ModifiedOn`) VALUES
(1, 122, 'IpiIYBGf621457429257.png', NULL, 1, 1, '2016-03-08 09:27:37', NULL, NULL),
(10, 153, '8gsu0KX71z1460522997.png', NULL, 1, 1, '2016-04-13 04:49:57', NULL, NULL),
(11, 153, 'yh98CtztWi1460523001.png', NULL, 1, 1, '2016-04-13 04:50:01', NULL, NULL),
(14, 151, '1zJoVTBWeT1460523031.png', NULL, 1, 1, '2016-04-13 04:50:31', NULL, NULL),
(15, 151, 'ZVEMGJI6oi1460523035.jpeg', NULL, 1, 1, '2016-04-13 04:50:35', NULL, NULL),
(17, 149, 'tRBjrjsMMS1460523059.png', NULL, 1, 1, '2016-04-13 04:50:59', NULL, NULL),
(18, 148, 'Wqb1lY4DwS1460523083.jpg', NULL, 1, 1, '2016-04-13 04:51:23', NULL, NULL),
(23, 154, 'yAqaTBBmKZ1460624008.jpg', NULL, 1, 1, '2016-04-14 08:53:28', NULL, NULL),
(24, 154, '3TIOBo2RPh1460624015.jpg', NULL, 1, 1, '2016-04-14 08:53:35', NULL, NULL),
(25, 154, 'SsbygMiknQ1460624020.jpg', NULL, 1, 1, '2016-04-14 08:53:40', NULL, NULL),
(26, 154, 'o3O9hbOm4H1460635471.jpg', NULL, 1, 1, '2016-04-14 12:04:31', NULL, NULL),
(27, 154, '3AvJsHYWL31461160309.png', NULL, 1, 1, '2016-04-20 13:51:49', NULL, NULL),
(31, 158, 'ARE6HvoQ1P1461160967.png', NULL, 1, 1, '2016-04-20 14:02:47', NULL, NULL),
(32, 159, 'o9fQHgwCa41461161012.png', NULL, 1, 1, '2016-04-20 14:03:32', NULL, NULL),
(33, 159, 'z0rSS9IlHQ1461161017.jpg', NULL, 1, 1, '2016-04-20 14:03:37', NULL, NULL),
(37, 159, 'ASckgBDxTo1461161063.png', NULL, 1, 1, '2016-04-20 14:04:23', NULL, NULL),
(41, 159, 'DCLfIKJcCJ1461230437.png', NULL, 1, 1, '2016-04-21 09:20:37', NULL, NULL),
(42, 160, 'JNgRHySTuZ1461241434.png', NULL, 1, 1, '2016-04-21 12:23:54', NULL, NULL),
(43, 160, 'fUiFjazmoN1461241438.jpg', NULL, 1, 1, '2016-04-21 12:23:58', NULL, NULL),
(44, 161, 'Eyq7pudBYj1461241509.jpg', NULL, 1, 1, '2016-04-21 12:25:09', NULL, NULL),
(45, 161, 'GQ8gZPMF7L1461241514.jpg', NULL, 1, 1, '2016-04-21 12:25:14', NULL, NULL),
(46, 161, 'B1JhN3Q9Fm1461241520.jpg', NULL, 1, 1, '2016-04-21 12:25:20', NULL, NULL),
(47, 162, 'KNb3ulsNsS1461241656.png', NULL, 1, 1, '2016-04-21 12:27:36', NULL, NULL),
(48, 162, 'BY78952sEw1461241660.jpg', NULL, 1, 1, '2016-04-21 12:27:40', NULL, NULL),
(49, 163, 'H3GVGv5zND1461242942.jpg', NULL, 1, 1, '2016-04-21 12:49:02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `applicationkeywords`
--

CREATE TABLE IF NOT EXISTS `applicationkeywords` (
  `Id` int(8) NOT NULL AUTO_INCREMENT,
  `ApplicationId` int(8) NOT NULL,
  `Keyword` varchar(255) NOT NULL,
  `IsActive` tinyint(4) NOT NULL DEFAULT '1',
  `CreatedBy` int(6) NOT NULL,
  `CreatedOn` datetime NOT NULL,
  `ModifiedBy` int(6) DEFAULT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ApplicationId` (`ApplicationId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `applicationkeywords`
--

INSERT INTO `applicationkeywords` (`Id`, `ApplicationId`, `Keyword`, `IsActive`, `CreatedBy`, `CreatedOn`, `ModifiedBy`, `ModifiedOn`) VALUES
(1, 122, '3434df', 1, 1, '2016-03-08 09:27:43', NULL, NULL),
(8, 154, 'Keyword16', 1, 1, '2016-04-13 04:49:43', NULL, NULL),
(9, 153, 'Bike', 1, 1, '2016-04-13 04:50:06', NULL, NULL),
(10, 153, 'Race', 1, 1, '2016-04-13 04:50:10', NULL, NULL),
(12, 151, 'Games', 1, 1, '2016-04-13 04:50:38', NULL, NULL),
(13, 151, 'Sounds', 1, 1, '2016-04-13 04:50:42', NULL, NULL),
(15, 149, 'RACE', 1, 1, '2016-04-13 04:51:02', NULL, NULL),
(16, 149, 'quiz', 1, 1, '2016-04-13 04:51:06', NULL, NULL),
(17, 148, 'games', 1, 1, '2016-04-13 04:51:26', NULL, NULL),
(23, 154, 'Candy Crush', 1, 1, '2016-04-14 08:53:48', NULL, NULL),
(24, 154, 'My Game', 1, 1, '2016-04-14 08:53:55', NULL, NULL),
(25, 154, 'Test', 1, 1, '2016-04-14 12:03:20', NULL, NULL),
(26, 154, 'Candy Crush', 1, 1, '2016-04-14 12:03:28', NULL, NULL),
(27, 154, 'My Game111', 1, 1, '2016-04-14 12:03:43', NULL, NULL),
(28, 154, 'Whatsapp', 1, 1, '2016-04-14 12:04:00', NULL, NULL),
(29, 154, 'Facebook', 1, 1, '2016-04-14 12:04:05', NULL, NULL),
(30, 154, 'GooglePlus', 1, 1, '2016-04-14 12:04:11', NULL, NULL),
(31, 154, 'Linkedin', 1, 1, '2016-04-14 12:04:16', NULL, NULL),
(32, 154, 'Tech Support', 1, 1, '2016-04-14 12:04:21', NULL, NULL),
(34, 159, 'droidbox', 1, 1, '2016-04-21 04:46:02', NULL, NULL),
(35, 159, 'SAP', 1, 1, '2016-04-21 11:29:37', NULL, NULL),
(36, 159, 'Java', 1, 1, '2016-04-21 11:29:43', NULL, NULL),
(37, 159, 'php', 1, 1, '2016-04-21 11:29:51', NULL, NULL),
(38, 159, 'informatica', 1, 1, '2016-04-21 11:30:10', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `applicationlanguages`
--

CREATE TABLE IF NOT EXISTS `applicationlanguages` (
  `Id` int(6) NOT NULL AUTO_INCREMENT,
  `AppId` int(6) NOT NULL,
  `LangId` int(6) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `CreatedOn` datetime NOT NULL,
  `CreatedBy` int(6) NOT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  `ModifiedBy` int(6) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `AppId` (`AppId`),
  KEY `LangId` (`LangId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE IF NOT EXISTS `applications` (
  `Id` int(8) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Icon` varchar(255) DEFAULT NULL,
  `CategoryId` int(6) NOT NULL,
  `LanguageId` int(10) NOT NULL,
  `SubCategoryId` int(6) NOT NULL,
  `Description` text NOT NULL,
  `Size` double DEFAULT NULL,
  `Unit` int(6) DEFAULT NULL,
  `Version` varchar(255) DEFAULT NULL,
  `Score` double DEFAULT NULL,
  `Developer` varchar(255) DEFAULT NULL,
  `Url` varchar(255) DEFAULT NULL,
  `Apk` varchar(255) DEFAULT NULL,
  `IsEditorChoice` tinyint(1) DEFAULT '0',
  `IsInternal` tinyint(1) DEFAULT '1',
  `DownloadCount` int(8) DEFAULT '0',
  `AverageRating` float DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `CreatedBy` int(6) NOT NULL,
  `CreatedOn` datetime NOT NULL,
  `ModifiedBy` int(6) DEFAULT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `CategoryId` (`CategoryId`),
  KEY `SubCategoryId` (`SubCategoryId`),
  KEY `Unit` (`Unit`),
  KEY `LanguageId` (`LanguageId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=164 ;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`Id`, `Name`, `Icon`, `CategoryId`, `LanguageId`, `SubCategoryId`, `Description`, `Size`, `Unit`, `Version`, `Score`, `Developer`, `Url`, `Apk`, `IsEditorChoice`, `IsInternal`, `DownloadCount`, `AverageRating`, `IsActive`, `CreatedBy`, `CreatedOn`, `ModifiedBy`, `ModifiedOn`) VALUES
(114, 'Facebook Application', '', 22005, 22033, 22007, 'errere', NULL, NULL, '', NULL, '', '', '', 0, 1, 0, NULL, 1, 1, '2016-03-08 05:17:55', 1, '2016-04-14 07:24:53'),
(115, 'M1 App', 'icon.png', 22005, 22034, 22007, 'u5665u6u5', 83280, 21002, '1.0', NULL, '', 'http://play.mob.org/android/mqpVFf8mlHX7z_nlLJDgcg/1456946724/hellgate_london_fps/1_hellgate_london_fps.apk', '', 0, 2, 0, NULL, 1, 1, '2016-03-08 05:56:03', 1, '2016-04-14 07:24:48'),
(117, 'Facebook Application', 'icon.png', 22005, 22035, 22008, 'ewewew', 83280, NULL, '1.0', NULL, NULL, '', 'EBHS.apk', 0, 1, 0, NULL, 1, 1, '2016-03-08 06:33:26', 1, '2016-04-14 07:24:42'),
(118, 'uyyuyuyu', 'icon.png', 22005, 22034, 22007, 'www', 83280, NULL, '1.0', NULL, NULL, '', 'EBHS.apk', 0, 1, 0, NULL, 1, 1, '2016-03-08 06:45:45', 1, '2016-04-14 07:24:38'),
(119, 'Facebook Application', 'apk-icon.png', 22005, 22034, 22007, 'refefrerre', 34, 21002, '1.23889', NULL, '', 'http://play.mob.org/android/mqpVFf8mlHX7z_nlLJDgcg/1456946724/hellgate_london_fps/1_hellgate_london_fps.apk', '', 0, 2, 0, NULL, 1, 1, '2016-03-08 06:56:23', 1, '2016-04-14 07:24:33'),
(120, 'NFS11111', 'icon.png', 22005, 22034, 22007, 'rrerere', 1482549, NULL, '1.0', NULL, NULL, 'http://play.mob.org/android/mqpVFf8mlHX7z_nlLJDgcg/1456946724/hellgate_london_fps/1_hellgate_london_fps.apk', 'Chess Update apkfiles.com.apk', 0, 3, 0, NULL, 1, 1, '2016-03-08 06:58:26', 1, '2016-04-14 07:24:27'),
(121, 'Editor45', 'icon.png', 22005, 22033, 22008, '454545', 83280, NULL, '1.0', NULL, NULL, '', 'EBHS.apk', 0, 1, 0, NULL, 1, 1, '2016-03-08 07:01:31', 1, '2016-04-14 07:24:23'),
(122, 'Editor322', 'apk-icon.png', 22005, 22035, 22007, '111', 20.5011, 21003, '111', NULL, '', 'http://play.mob.org/android/mqpVFf8mlHX7z_nlLJDgcg/1456946724/hellgate_london_fps/1_hellgate_london_fps.apk', '', 1, 2, 0, NULL, 1, 1, '2016-03-08 07:02:33', 1, '2016-04-14 07:24:09'),
(123, 'M1 App2', 'icon.png', 22005, 22033, 22007, '223', 1417520, NULL, '1.6', NULL, NULL, 'http://play.mob.org/android/mqpVFf8mlHX7z_nlLJDgcg/1456946724/hellgate_london_fps/1_hellgate_london_fps.apk', 'XPOSED%20IMEI%20Changer_1.6_apk-dl.com.apk', 0, 3, 0, NULL, 1, 1, '2016-03-08 07:21:39', 1, '2016-04-14 07:24:03'),
(124, 'FB', 'icon.png', 22005, 22035, 22006, '1', 83280, 21003, '1.0', NULL, '', '', 'EBHS.apk', 0, 1, 145, NULL, 1, 1, '2016-03-08 08:40:21', 1, '2016-04-14 07:23:59'),
(125, 'Name', 'app v.png', 22005, 22033, 22007, 'yhi', 1.45, 21002, '1.23889', NULL, '', 'http://play.mob.org/android/mqpVFf8mlHX7z_nlLJDgcg/1456946724/hellgate_london_fps/1_hellgate_london_fps.apk', '', 0, 2, 0, NULL, 1, 1, '2016-03-09 04:57:09', 1, '2016-04-14 07:23:55'),
(126, 'First', 'icon.png', 22005, 22033, 22006, '1111', 1482549, 21002, '78789', NULL, '', 'http://play.mob.org/android/mqpVFf8mlHX7z_nlLJDgcg/1456946724/hellgate_london_fps/1_hellgate_london_fps.apk', '', 1, 2, 23, NULL, 1, 1, '2016-03-09 05:02:28', 1, '2016-04-14 07:23:46'),
(127, 'Second', '', 22005, 22034, 22007, '112311', 1.45, 21001, '1.9', NULL, NULL, '', 'Facebook Lite_apkpure.com.apk', 1, 1, 0, NULL, 1, 1, '2016-03-09 05:02:57', 1, '2016-04-14 07:23:41'),
(128, 'Third', 'icon.png', 22005, 22033, 22008, '2332', 83280, NULL, '1.0', NULL, NULL, '', '', 1, 1, 0, NULL, 1, 1, '2016-03-09 05:03:18', 1, '2016-04-14 07:23:33'),
(129, 'Kotak', 'images.jpeg', 22005, 22034, 22007, '2', 12321, 21002, '1.61', NULL, '', 'http://play.mob.org/android/mqpVFf8mlHX7z_nlLJDgcg/1456946724/hellgate_london_fps/1_hellgate_london_fps.apk', '', 0, 2, 0, NULL, 1, 1, '2016-03-09 05:10:56', 1, '2016-04-14 07:23:28'),
(131, 'NFC', 'icon.png', 22005, 22034, 22006, '1', 1417520, NULL, '1.6', NULL, NULL, '', 'XPOSED%20IMEI%20Changer_1.6_apk-dl.com.apk', 0, 1, 0, NULL, 1, 1, '2016-03-09 05:14:34', 1, '2016-04-14 07:23:18'),
(132, 'FB1', '', 22005, 22034, 22007, '1', NULL, NULL, '', NULL, NULL, '', 'Facebook Lite_apkpure.com.apk', 0, 1, 0, NULL, 1, 1, '2016-03-09 05:14:50', 1, '2016-04-14 07:23:00'),
(133, 'Facebook Application89', 'icon.png', 22005, 22034, 22007, 'uiuiui', 1417520, NULL, '1.6', NULL, NULL, '', 'XPOSED%20IMEI%20Changer_1.6_apk-dl.com.apk', 0, 1, 0, NULL, 1, 1, '2016-03-09 05:15:15', 1, '2016-04-14 07:22:54'),
(135, 'Facebook', 'apk-icon.png', 22005, 22034, 22008, 'rt', 12, 21003, '1.23889', NULL, '', 'http://play.mob.org/android/mqpVFf8mlHX7z_nlLJDgcg/1456946724/hellgate_london_fps/1_hellgate_london_fps.apk', '', 0, 2, 0, NULL, 1, 1, '2016-03-09 05:17:11', 1, '2016-04-14 07:22:48'),
(137, 'M1 App234', '', 22005, 22034, 22008, 'refre', 1417520, NULL, '1.6', NULL, NULL, '', '', 0, 1, 0, NULL, 1, 1, '2016-03-09 05:27:55', 1, '2016-04-14 07:22:39'),
(139, 'Bth', 'icon.png', 22005, 22033, 22008, '23', 1417520, NULL, '1.6', NULL, NULL, '', 'XPOSED%20IMEI%20Changer_1.6_apk-dl.com.apk', 1, 1, 3, 3.5, 1, 1, '2016-03-09 05:49:03', 1, '2016-04-14 07:22:30'),
(143, 'Shadow Fight', '', 22005, 22034, 22006, 'q\r\nq\r\nq\r\nq', NULL, NULL, '', NULL, NULL, '', 'WAsteManagement.apk', 0, 1, 0, NULL, 1, 1, '2016-04-08 04:50:10', 1, '2016-04-21 06:41:28'),
(148, 'Event high', '', 22019, 22035, 22020, '3', NULL, NULL, '', NULL, NULL, '', 'Facebook Lite_apkpure.com.apk', 0, 1, 0, NULL, 1, 1, '2016-04-11 10:38:00', 1, '2016-04-14 07:21:43'),
(149, 'True Caller', 'icon.png', 22019, 22035, 22020, 'Test', 83280, NULL, '1.0', NULL, NULL, '', 'EBHS.apk', 0, 1, 0, NULL, 1, 1, '2016-04-11 10:39:00', 1, '2016-04-14 07:21:37'),
(151, 'Samsung India', 'icon.png', 22005, 22034, 22007, '2', 83280, NULL, '1.0', NULL, NULL, '', 'EBHS.apk', 0, 1, 0, NULL, 1, 1, '2016-04-11 10:39:30', 1, '2016-04-14 07:21:28'),
(153, 'Linux', 'icon.png', 22062, 22034, 22063, 'we', 83280, NULL, '1.0', NULL, NULL, '', 'EBHS.apk', 1, 1, 0, 4, 1, 1, '2016-04-11 10:39:56', 1, '2016-04-21 12:31:17'),
(154, 'Candy Crush', 'icon.png', 22053, 22034, 22056, 'Write an O(n)-time non-recursive procedure that, given an n-node binary tree, prints out the key of each node. Use no more than constant extra space outside of the tree itself and do not modify the tree, even temporarily, during the procedure.\r\n\r\nEach node has left, child and parent pointers.\r\n\r\n\r\nMaintain two pointers current and last, to keep track of current node visited and last node visited respectively. Here, I am traversing the list as (parent, left, right) order. Then, depending on the relationship between current node and last node, whether current node is left child or right child or parent, update the current and last pointers accordingly.', 83280, NULL, '1.0', NULL, 'Ankit Joshi', '', 'EBHS.apk', 1, 1, 0, 4.5, 1, 1, '2016-04-11 10:40:14', 1, '2016-04-21 12:31:03'),
(158, 'rest', '', 22005, 22033, 22006, 'qqq', NULL, NULL, '', NULL, '', '', 'Facebook Lite_apkpure.com.apk', 0, 1, 0, NULL, 1, 1, '2016-04-20 14:02:39', NULL, NULL),
(159, 'Raj', 'icon.png', 22062, 22034, 22063, 'Description', 1417520, NULL, '1.6', NULL, '', '', 'XPOSED%20IMEI%20Changer_1.6_apk-dl.com.apk', 0, 1, 0, NULL, 1, 1, '2016-04-20 14:03:22', 1, '2016-04-21 10:32:21'),
(160, 'Polaris', '', 22053, 22034, 22056, 'Test', NULL, NULL, '', NULL, '', '', 'Facebook Lite_apkpure.com.apk', 0, 1, 0, NULL, 1, 1, '2016-04-21 12:23:45', NULL, NULL),
(161, 'Playfish', 'icon.png', 22034, 22034, 22058, 'Playfish', 1417520, NULL, '1.6', NULL, '', '', 'XPOSED%20IMEI%20Changer_1.6_apk-dl.com.apk', 0, 1, 0, NULL, 1, 1, '2016-04-21 12:24:57', NULL, NULL),
(162, 'cricbuzz', '', 22057, 22034, 22064, 'Cricbuzz', NULL, NULL, '', NULL, '', '', 'Facebook Lite_apkpure.com.apk', 1, 1, 0, NULL, 1, 1, '2016-04-21 12:27:18', NULL, NULL),
(163, 'Shashwat App', 'icon.png', 22065, 22034, 22066, 'mty app', 83280, NULL, '1.0', NULL, '', '', 'EBHS.apk', 1, 1, 0, 4, 1, 1, '2016-04-21 12:48:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `Id` int(6) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(1000) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `CreatedBy` int(6) NOT NULL,
  `CreatedOn` datetime NOT NULL,
  `ModifiedBy` int(6) DEFAULT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`Id`, `Name`, `Description`, `IsActive`, `CreatedBy`, `CreatedOn`, `ModifiedBy`, `ModifiedOn`) VALUES
(3, 'Games', 'android games', 1, 1, '2016-02-02 11:12:26', 1, '2016-02-20 06:10:30'),
(10, 'Messenger', 'Facaebook Messenger', 1, 1, '2016-02-18 09:25:26', NULL, NULL),
(14, 'Whatsapp', 'category Functional Specification', 1, 1, '2016-02-18 09:39:24', NULL, NULL),
(16, 'Mysql Java Php', 'Android IOS', 0, 1, '2016-02-18 11:05:51', NULL, NULL),
(17, 'My App', 'M', 1, 1, '2016-02-18 09:42:27', NULL, NULL),
(18, 'Petfunday', 'Petfunday apps', 1, 1, '2016-02-18 09:44:05', NULL, NULL),
(20, 'Myspace', 'My space test\r\n', 1, 1, '2016-02-18 11:09:24', 1, '2016-02-18 11:28:03'),
(23, 'Pininterest', 'Add New Category', 1, 1, '2016-02-18 11:13:05', NULL, NULL),
(24, 'Facebook', 'Facebook', 0, 1, '2016-02-18 12:11:43', 1, '2016-02-20 05:28:47'),
(25, 'My Data', 'abcdef', 1, 1, '2016-02-19 06:30:31', NULL, NULL),
(26, 'Test', 'Test ', 1, 1, '2016-02-19 08:50:42', NULL, NULL),
(27, 'Infosys', 'New Version', 1, 1, '2016-02-19 09:03:42', 1, '2016-02-20 06:10:28'),
(28, 'TCS', 'OS', 1, 1, '2016-02-19 09:04:50', 1, '2016-02-19 11:08:05'),
(30, 'Thank You', 'Thank You', 1, 1, '2016-02-19 12:36:19', NULL, NULL),
(31, 'App Data', 'Appppppsss', 1, 1, '2016-02-20 06:30:35', NULL, NULL),
(32, 'Games 1', 'Test', 1, 1, '2016-02-23 04:49:35', 1, '2016-02-25 06:13:55'),
(33, 'Filezilla', 'w', 1, 1, '2016-02-25 06:18:23', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `emailhistory`
--

CREATE TABLE IF NOT EXISTS `emailhistory` (
  `Id` int(6) NOT NULL AUTO_INCREMENT,
  `EmailId` varchar(255) NOT NULL,
  `Status` int(6) NOT NULL,
  `Subject` varchar(500) NOT NULL,
  `Body` text NOT NULL,
  `Attempts` int(2) NOT NULL,
  `SenderEmail` varchar(255) NOT NULL,
  `SentOn` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Status` (`Status`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `emailhistory`
--

INSERT INTO `emailhistory` (`Id`, `EmailId`, `Status`, `Subject`, `Body`, `Attempts`, `SenderEmail`, `SentOn`) VALUES
(5, 'siddharth.k@spdynaics.net', 20002, 'Forgot password mail', 'Your new password is q&lj,^', 1, 'siddharth.k@spdynaics.net', '2016-03-02 01:07:33'),
(6, 'abc@gmail.com', 20002, 'Forgot password mail', 'Your new password is K=F%xe', 1, 'siddharth.k@spdynaics.net', '2016-03-15 09:30:42'),
(7, 'abc@gmail.com', 20002, 'Forgot password mail', 'Your new password is KYIs2%', 1, 'siddharth.k@spdynaics.net', '2016-03-15 09:39:52'),
(8, 'r@g.com', 20002, 'Forgot password mail', 'Your new password is Jb:Py_', 1, 'siddharth.k@spdynaics.net', '2016-04-21 11:37:01'),
(9, 'r@g.com', 20002, 'Forgot password mail', 'Your new password is Wtk#Tz', 1, 'siddharth.k@spdynaics.net', '2016-04-21 11:37:03');

-- --------------------------------------------------------

--
-- Table structure for table `lookups`
--

CREATE TABLE IF NOT EXISTS `lookups` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) DEFAULT NULL,
  `ImageUrl` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `LookupTypeId` int(11) NOT NULL,
  `ParentLookupId` int(6) DEFAULT '0',
  `IsSeedData` tinyint(1) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT '1',
  `CreatedBy` int(6) NOT NULL,
  `CreatedOn` datetime NOT NULL,
  `ModifiedBy` int(6) DEFAULT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `LookupTypeId` (`LookupTypeId`),
  KEY `ParentLookupId` (`ParentLookupId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22067 ;

--
-- Dumping data for table `lookups`
--

INSERT INTO `lookups` (`Id`, `Name`, `ImageUrl`, `Description`, `LookupTypeId`, `ParentLookupId`, `IsSeedData`, `IsActive`, `CreatedBy`, `CreatedOn`, `ModifiedBy`, `ModifiedOn`) VALUES
(20001, 'Pending', '', 'Pending', 2, NULL, NULL, 1, 1, '2016-03-01 13:06:42', 1, '2016-03-02 08:40:37'),
(20002, 'Sent', '', 'Sent', 2, NULL, NULL, 1, 1, '2016-03-01 13:06:42', 1, '2016-03-02 08:40:37'),
(20003, 'Failed', '', 'Failed', 2, NULL, NULL, 1, 1, '2016-03-01 13:06:42', 1, '2016-03-02 08:40:37'),
(21001, 'Bytes', '', 'File Size Unit', 3, NULL, NULL, 1, 1, '2016-03-01 13:06:42', 1, '2016-03-02 08:40:37'),
(21002, 'KB', '', 'File Size Unit', 3, NULL, NULL, 1, 1, '2016-03-01 13:06:42', 1, '2016-03-02 08:40:37'),
(21003, 'MB', '', 'File Size Unit', 3, NULL, NULL, 1, 1, '2016-03-01 13:06:42', 1, '2016-03-02 08:40:37'),
(21004, 'GB', '', 'File Size Unit', 3, NULL, NULL, 0, 1, '2016-03-01 13:06:42', 1, '2016-03-02 08:40:37'),
(22004, 'Games', 'social-media-mobile-apps-ss-1920.jpg', '1', 1, NULL, NULL, 0, 1, '2016-03-07 10:43:52', 1, '2016-04-07 10:21:06'),
(22005, 'Media', 'images.jpeg', '', 1, NULL, NULL, 1, 1, '2016-03-07 10:45:37', 1, '2016-03-18 10:50:01'),
(22006, 'M11234', '', '', 1, 22005, NULL, 1, 1, '2016-03-07 10:45:51', 1, '2016-04-20 13:49:26'),
(22007, 'M2', '', '', 1, 22005, NULL, 1, 1, '2016-03-07 10:45:57', NULL, NULL),
(22008, 'M3', '', '', 1, 22005, NULL, 1, 1, '2016-03-07 10:46:03', NULL, NULL),
(22009, 'G1', '', '', 1, 22004, NULL, 1, 1, '2016-03-07 10:46:12', NULL, NULL),
(22010, 'G2', '', '', 1, 22004, NULL, 1, 1, '2016-03-07 10:46:16', NULL, NULL),
(22011, 'Quiz Games', 'apk-icon.png', '', 1, 22004, NULL, 1, 1, '2016-03-11 09:18:43', NULL, NULL),
(22012, 'Racing Games', 'Art_deco_club_chair.jpg', '', 1, NULL, NULL, 0, 1, '2016-03-16 05:12:00', 1, '2016-03-18 10:49:54'),
(22013, 'Arcade', '', '', 1, 22012, NULL, 1, 1, '2016-03-16 05:12:16', NULL, NULL),
(22014, 'NFS', '', '', 1, 22012, NULL, 1, 1, '2016-03-16 05:12:25', NULL, NULL),
(22015, 'Hello', 'apk-icon.png', 'Hello', 1, NULL, NULL, 0, 1, '2016-03-16 11:34:16', 1, '2016-04-08 04:55:09'),
(22016, 'PNG', 'Google_Chrome_icon_(2011).png', '', 1, NULL, NULL, 1, 1, '2016-03-18 11:20:33', 1, '2016-04-21 05:53:27'),
(22017, 'Chrome', 'Art_deco_club_chair.jpg', 'eee', 1, NULL, NULL, 0, 1, '2016-03-18 12:13:33', 1, '2016-04-21 06:09:43'),
(22018, 'Hi1', '', 'H1', 1, 22015, NULL, 1, 1, '2016-04-07 06:56:35', NULL, NULL),
(22019, 'TimeTac', 'timetac-mobile-966373-l-140x140.png', 'Online time tracking and attendance monitoring software for all working environments and industries', 1, NULL, NULL, 1, 1, '2016-04-07 10:14:29', 1, '2016-04-08 04:49:05'),
(22020, 'tac', 'PetFunday_Issues_30March(1).xlsx', 'tac time', 1, 22019, NULL, 1, 1, '2016-04-07 10:15:09', 1, '2016-04-07 10:19:03'),
(22029, 'baseball', '', 'ggggghgkljyhuihkjhuih', 1, NULL, NULL, 1, 1, '2016-04-07 12:18:29', 1, '2016-04-19 11:27:30'),
(22030, 'Test76868768', '', '', 1, NULL, NULL, 1, 1, '2016-04-12 09:19:36', NULL, NULL),
(22031, 'Linkedin', 'Dawson-Red-Chair-A.jpg', 'e', 1, NULL, NULL, 1, 1, '2016-04-14 07:12:18', 1, '2016-04-21 05:50:41'),
(22033, 'German', 'Google_Chrome_icon_(2011).png', 'German Language', 4, NULL, NULL, 1, 1, '2016-04-14 07:13:41', NULL, NULL),
(22034, 'English', '0105948_PE253720_S5.JPG', 'e', 4, NULL, NULL, 1, 1, '2016-04-14 07:13:53', NULL, NULL),
(22035, 'Chinese', 'mobile-apps-pile-ss-1920.jpg', 'rrrr', 4, NULL, NULL, 1, 1, '2016-04-14 07:14:16', NULL, NULL),
(22037, 'My Sub Category190', 'Art_deco_club_chair.jpg', '2', 1, 22031, NULL, 1, 1, '2016-04-21 05:47:28', 1, '2016-04-21 09:59:45'),
(22038, 'Claritus', 'apk-1.png', '', 1, 22031, NULL, 1, 1, '2016-04-21 06:10:52', NULL, NULL),
(22039, 'LookupTy', '', '1', 1, NULL, NULL, 1, 1, '2016-04-21 07:25:33', 1, '2016-04-21 08:57:42'),
(22049, 'Infosys India', 'apk-1.png', '', 1, NULL, NULL, 1, 1, '2016-04-21 09:01:38', 1, '2016-04-21 09:05:34'),
(22053, 'ABB', '', '', 1, NULL, NULL, 1, 1, '2016-04-21 09:05:26', NULL, NULL),
(22054, 'Claritus Consulting', 'app v.png', '', 1, NULL, NULL, 1, 1, '2016-04-21 09:06:15', 1, '2016-04-21 10:07:39'),
(22055, 'SAP', 'Google_Chrome_icon_(2011).png', '', 1, NULL, NULL, 1, 1, '2016-04-21 09:27:42', 1, '2016-04-21 10:07:53'),
(22056, 'Polaris', '', '', 1, 22053, NULL, 1, 1, '2016-04-21 09:40:44', NULL, NULL),
(22057, 'Logitech', '', '', 1, NULL, NULL, 1, 1, '2016-04-21 09:49:41', 1, '2016-04-21 09:50:23'),
(22058, 'Microsoft', 'cat.png', 'MIcrosoft', 1, 22031, NULL, 1, 1, '2016-04-21 09:51:23', 1, '2016-04-21 12:26:35'),
(22059, 'ABB57', '', '', 1, 22049, NULL, 1, 1, '2016-04-21 09:53:33', NULL, NULL),
(22061, 'Entertainment', 'Dawson-Red-Chair-A.jpg', 'Test', 1, NULL, NULL, 1, 1, '2016-04-21 09:55:42', NULL, NULL),
(22062, 'Quatrro', 'Dawson-Red-Chair-A.jpg', '', 1, NULL, NULL, 1, 1, '2016-04-21 09:57:16', NULL, NULL),
(22063, 'Mortgage', 'Art_deco_club_chair.jpg', 'w', 1, 22062, NULL, 1, 1, '2016-04-21 09:57:44', NULL, NULL),
(22064, 'Computer', 'Art_deco_club_chair.jpg', '', 1, 22057, NULL, 1, 1, '2016-04-21 10:15:06', 1, '2016-04-21 10:15:22'),
(22065, 'Shashwat', 'Dawson-Red-Chair-A.jpg', '', 1, NULL, NULL, 1, 1, '2016-04-21 12:47:41', NULL, NULL),
(22066, 'shashwat1', 'apk-icon.png', '', 1, 22065, NULL, 1, 1, '2016-04-21 12:48:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lookuptypes`
--

CREATE TABLE IF NOT EXISTS `lookuptypes` (
  `Id` int(6) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `CreateBy` int(6) NOT NULL,
  `CreatedOn` datetime NOT NULL,
  `ModifiedBy` int(6) DEFAULT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `lookuptypes`
--

INSERT INTO `lookuptypes` (`Id`, `Name`, `Description`, `IsActive`, `CreateBy`, `CreatedOn`, `ModifiedBy`, `ModifiedOn`) VALUES
(1, 'ApplicationCategory', 'Application type categories', 1, 1, '2016-02-25 00:00:00', NULL, NULL),
(2, 'EmailStatus', 'Status for emails', 1, 1, '2016-02-25 00:00:00', NULL, NULL),
(3, 'FileSizeUnit', 'File Size Unit', 1, 1, '2016-02-25 00:00:00', NULL, NULL),
(4, 'Language', 'Language', 1, 1, '2016-02-25 00:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `Id` int(8) NOT NULL AUTO_INCREMENT,
  `ApplicationId` int(8) NOT NULL,
  `UserId` int(6) NOT NULL,
  `Rating` float NOT NULL,
  `IsActive` tinyint(1) NOT NULL,
  `CreatedBy` int(6) NOT NULL,
  `CreatedOn` datetime NOT NULL,
  `ModifiedBy` int(6) DEFAULT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ApplicationId` (`ApplicationId`),
  KEY `UserId` (`UserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`Id`, `ApplicationId`, `UserId`, `Rating`, `IsActive`, `CreatedBy`, `CreatedOn`, `ModifiedBy`, `ModifiedOn`) VALUES
(1, 139, 1, 3.5, 1, 1, '2016-03-09 05:52:07', 1, '2016-03-09 05:52:14'),
(2, 139, 55, 3.5, 1, 55, '2016-03-11 12:28:54', NULL, NULL),
(3, 153, 64, 4, 1, 64, '2016-04-14 06:49:32', 64, '2016-04-21 12:45:10'),
(4, 154, 64, 4.5, 1, 64, '2016-04-14 07:10:48', 64, '2016-04-20 13:16:16'),
(5, 163, 64, 4, 1, 64, '2016-04-21 12:52:41', 64, '2016-04-21 12:55:12');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE IF NOT EXISTS `reviews` (
  `Id` int(8) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `ApplicationId` int(8) NOT NULL,
  `UserId` int(6) NOT NULL,
  `Review` varchar(1000) DEFAULT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `CreatedBy` int(6) NOT NULL,
  `CreatedOn` datetime NOT NULL,
  `ModifiedBy` int(6) DEFAULT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ApplicationId` (`ApplicationId`),
  KEY `UserId` (`UserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`Id`, `Name`, `ApplicationId`, `UserId`, `Review`, `IsActive`, `CreatedBy`, `CreatedOn`, `ModifiedBy`, `ModifiedOn`) VALUES
(1, 'Waseem Khan', 154, 1, 'The code has been generated successfully. The code has been generated successfully. The code has been generated successfully.', 1, 1, '2016-03-09 05:51:33', NULL, NULL),
(2, '222', 154, 65, 'Myspace.com is a social networking website offering an interactive, user-submitted network of friends, personal profiles, blogs, groups, photos, music, and videos. It is headquartered in Beverly Hills, California.', 1, 64, '2016-04-14 07:25:57', 64, '2016-04-20 13:16:28'),
(3, 'Anjan Dutta', 154, 53, 'Review 1', 1, 1, '2016-03-09 05:51:33', NULL, NULL),
(4, 'raj', 153, 70, 'sting', 1, 70, '2016-04-21 11:54:01', NULL, NULL),
(5, 'Shashank', 154, 62, 'Review 1', 1, 1, '2016-03-09 05:51:33', NULL, NULL),
(6, 'Waseem', 154, 46, 'Myspace.com is a social networking website offering an interactive, user-submitted network of friends, personal profiles, blogs, groups, photos, music, and videos. It is headquartered in Beverly Hills, California.', 1, 1, '2016-03-09 05:51:33', NULL, NULL),
(7, 'Raj Kumar', 154, 57, 'Myspace.com is a social networking website offering an interactive, user-submitted network of friends, personal profiles, blogs, groups, photos, music, and videos. It is headquartered in Beverly Hills, California.', 1, 64, '2016-04-14 07:25:57', 64, '2016-04-20 13:16:28'),
(8, 'Nishant', 154, 68, 'ately 1,600 employees.[3][16] In June 2011, Specific Media Group and Justin Timberlake jointly purchased the company for approximately $35 million.[17] Under new ownership, the company had undergone several rounds of layoffs and by June 2011, Myspace had reduced its staff ', 1, 1, '2016-03-09 05:51:33', NULL, NULL),
(9, 'Nishant', 154, 63, 'ately 1,600 employees.[3][16] In June 2011, Specific Media Group and Justin Timberlake jointly purchased the company for approximately $35 million.[17] Under new ownership, the company had undergone several rounds of layoffs and by June 2011, Myspace had reduced its staff ', 1, 1, '2016-03-09 05:51:33', NULL, NULL),
(10, 'Anjan Dutta', 154, 59, 'Review 1', 1, 1, '2016-03-09 05:51:33', NULL, NULL),
(11, 'Raj Kumar', 154, 50, ' networking website offering an interactive, user-submitted network of friends, personal profiles, blogs, groups, photos, music, and videos. It is headquartered in Beverly Hills, California.', 1, 64, '2016-04-14 07:25:57', 64, '2016-04-20 13:16:28'),
(12, 'Yogesh', 154, 66, 'Since then, the number of Myspace users has declined steadily in spite of several redesigns.[12] As of May 2014, Myspace was ranked 982 by total web traffic, and 392 in the United States. As of April 2016 the ranks were 1985 and 1747, corresponding', 1, 1, '2016-03-09 05:51:33', NULL, NULL),
(13, 'ASDF', 153, 71, 'ASDASD', 1, 71, '2016-04-21 12:45:43', NULL, NULL),
(14, 'sd', 163, 64, 'agasd', 1, 64, '2016-04-21 12:54:13', NULL, NULL),
(15, 'asda', 163, 71, 'asdas', 1, 71, '2016-04-21 12:55:17', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `Id` int(6) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) DEFAULT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `IsActive` tinyint(1) NOT NULL DEFAULT '1',
  `IsAdmin` tinyint(1) DEFAULT '0',
  `IsVerified` tinyint(1) NOT NULL DEFAULT '1',
  `CreatedBy` int(6) NOT NULL,
  `CreatedOn` datetime NOT NULL,
  `ModifiedBy` int(6) DEFAULT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=72 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Id`, `FirstName`, `LastName`, `Email`, `Password`, `IsActive`, `IsAdmin`, `IsVerified`, `CreatedBy`, `CreatedOn`, `ModifiedBy`, `ModifiedOn`) VALUES
(1, 'Eugene', 'admin', 'admin@droidbox.com', '$2y$13$ig6gmi7KCU9M/7MVQkEjj.YX/kWB5g4ho.VL3.CmAFi5iZJd8Cf8i', 1, 1, 1, 1, '2016-02-18 12:53:27', NULL, NULL),
(46, 'Waseem', 'khan', 'waseem@venire.com', '$2y$13$tpVRITQ86OF6CscWg6im4./b7XEzPJSCNBotF9cpFxzVvzoaTiK76', 1, 0, 1, 1, '2016-02-29 07:19:36', NULL, NULL),
(47, 'w', 'w', 'w@y.com', '$2y$13$mcnccdwkiPW13HzdvVAgWuYN8Sufo974ufP0BUg/pCwi5Uc3ww/.a', 1, 0, 1, 1, '2016-02-29 13:00:47', NULL, NULL),
(49, 'ankit', 'ojshi', 'ankit@yahoo.com', '$2y$13$R90.E6ZwpPNC6o8g0S1fxOcyYbsDCuQjPt54q6UZloLqhRgR06xuu', 1, 0, 1, 1, '2016-03-01 07:05:23', NULL, NULL),
(50, 'abc', '', 'abc@yahoo.com', '$2y$13$VSGZLBzslMYMF7DC8pqZGup9Hr3E17oDLU.k9jzflTNJXObmNunJq', 1, 0, 1, 1, '2016-03-01 07:17:48', NULL, NULL),
(52, 'Test', 'etst', 'siddharth.k@spdynaics.net', '$2y$13$RTXMyo8gpKSX2DoGoIlr5OiQyR7a8bBb1DjwI8/ho/iGfV9IL91Iy', 1, 0, 1, 1, '2016-03-02 12:24:37', NULL, NULL),
(53, 'waseem', 'khan', 'waseem@yahoo.com', '$2y$13$SROgjHsRr38/p5PkWc/jUeOEZ.An45xJdAshSWKLIfTq2QFoaDdHi', 1, 0, 1, 1, '2016-03-10 06:56:55', NULL, NULL),
(54, 'anjan', 'dutta', 'anjan@claritus.com', '$2y$13$JzZGFbm5xWQLR3xro7TDIeZyrFU9HB7nOcrMaikT9vNP4susw9a4q', 1, 0, 1, 1, '2016-03-10 08:55:00', NULL, NULL),
(55, 'abc', 'def', 'abc@gmail.com', '$2y$13$HLkLR/fvg0ayAG0So4t/euoFXolJkhYWFFrAny1bmsVBgiEJfL36.', 1, 0, 1, 1, '2016-03-11 06:20:02', NULL, NULL),
(56, 'waseem', 'khan', 'khan@yahoo.com', '$2y$13$yX6GKReb6jcBZYZju5Rfi./bshhook2Ury26Eaz0587yVZE1ziTRO', 1, 0, 1, 1, '2016-03-11 06:47:07', NULL, NULL),
(57, 'Test', 'TEst', 'tetstst@yahoo.com', '$2y$13$c9QfWjSeawkvceK2cQaA2ezvHv0/OAiuypuCd.TAqasN0pJMJQnNa', 1, 0, 1, 1, '2016-03-11 07:17:59', NULL, NULL),
(58, 'aas', 'asfd', 'as@gmail.com', '$2y$13$R4JK0.x6QJH1rbrRw70Qv.RDOkgN6TxQwTIM3IZ44bm038u5T.ihy', 1, 0, 1, 1, '2016-03-11 07:28:46', NULL, NULL),
(59, 'Yogesh', NULL, 'yogesh@gmail.com', '$2y$13$bL6MLMyQ6S.3QKuEDhHg9OzcXj4g8BVOwbnwPfaCdaCFBT3Mm.M7m', 1, 0, 1, 1, '2016-03-11 08:42:12', NULL, NULL),
(60, 'shashwat', 'shukla', 'az@gmail.com', '$2y$13$/H0Ew3/t0MZSvpbwcVRfBujX09VTFFQeHgF8lRZ91yV0RQnJXgJQ6', 1, 0, 1, 1, '2016-03-15 05:29:57', NULL, NULL),
(61, 'asas', 'sdfsd', 'sxy@gmail.com', '$2y$13$upBQZ2KU11Z47QPIJ6R7uONnG9HLKG2E3WGhgRPOoLIMYxb7ECJdO', 1, 0, 1, 1, '2016-03-15 05:30:39', NULL, NULL),
(62, 'shashwat', 'shukla', 'at@gmail.com', '$2y$13$KjxsmoV64DNntfhKQ5fYTuTgeep59gBf1A.nrcTDMNZeYYwlrKZt.', 1, 0, 1, 1, '2016-03-15 06:08:54', NULL, NULL),
(63, 'ss', 'ss', 's@gmail.com', '$2y$13$fx.HUfZSyWDYRcw3LdNzDe7yJUndKK/22hvYQsxXuA3P6M/fb4XpK', 1, 0, 1, 1, '2016-03-15 11:45:59', NULL, NULL),
(64, 'abc', 'def', 'sas@gmail.com', '$2y$13$SKlI8Hr/bqMPaFF.GESYLOakPY.NHuUUxPBKkwpxZhjRzNo9FEc/O', 1, 0, 1, 1, '2016-03-16 05:37:04', NULL, NULL),
(65, 'as', 'sa', 'sa@gmail.com', '$2y$13$jRFgHDJ/vOTL87jvPVSwROq7pjGa01gWoSw0H8UXYDCyGlyrbut4W', 1, 0, 1, 1, '2016-03-18 13:05:35', NULL, NULL),
(66, 'asd', 'asd', 'a@gmail.com', '$2y$13$uGIr2pzikUmDUn492yi4benM0ORVlhtVz8tJwYSdhXj5ojH3MCHB2', 1, 0, 1, 1, '2016-03-18 13:21:11', NULL, NULL),
(67, 'Waseem', NULL, 'waseem.khan@yahoo.com', '$2y$13$eoxTV0sQxmOJ.WRmkQYGxOy74hVtPSI/kn1lCxufnv1DF0UUfdqNS', 1, 0, 1, 1, '2016-04-19 04:45:50', NULL, NULL),
(68, 'ads', 'asd', 'asd@gmail.com', '$2y$13$bSwn39fYizyXYQzKdvdTnObEGMYb.RXd.VwTYE9zW/fue2SwS0xFW', 1, 0, 1, 1, '2016-04-21 09:40:41', NULL, NULL),
(69, 'rr', 'tt', 'r@g.com', '$2y$13$loQDrsX604TCs6QBoTdnNeJYuLMwzDReHEuVfrsqe25eT/szfBPby', 1, 0, 1, 1, '2016-04-21 11:23:21', NULL, NULL),
(70, 'www', 'www', 's@g.com', '$2y$13$dPwEVrC2k2O8NNW0kZMkH.AA7dQQEDovDTfmsAB8vad4pcFhH3hF2', 1, 0, 1, 1, '2016-04-21 11:44:03', NULL, NULL),
(71, 'asda', 'asdasd', 'a@g.com', '$2y$13$6kKUnAV9weIdvqFNNf/CSeBKpAGP26HLAX8pk/a2EwNMGOH5UurF2', 1, 0, 1, 1, '2016-04-21 12:43:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `usertokens`
--

CREATE TABLE IF NOT EXISTS `usertokens` (
  `Id` int(6) NOT NULL AUTO_INCREMENT,
  `UserId` int(6) NOT NULL,
  `Token1` varchar(500) DEFAULT NULL,
  `Token2` varchar(500) DEFAULT NULL,
  `CreatedBy` int(6) DEFAULT NULL,
  `CreatedOn` datetime DEFAULT NULL,
  `ModifiedBy` int(6) DEFAULT NULL,
  `ModifiedOn` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `UserId` (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicationimages`
--
ALTER TABLE `applicationimages`
  ADD CONSTRAINT `applicationimages_ibfk_1` FOREIGN KEY (`ApplicationId`) REFERENCES `applications` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `applicationkeywords`
--
ALTER TABLE `applicationkeywords`
  ADD CONSTRAINT `applicationkeywords_ibfk_1` FOREIGN KEY (`ApplicationId`) REFERENCES `applications` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `applicationlanguages`
--
ALTER TABLE `applicationlanguages`
  ADD CONSTRAINT `applicationlanguages_ibfk_1` FOREIGN KEY (`AppId`) REFERENCES `applications` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applicationlanguages_ibfk_2` FOREIGN KEY (`LangId`) REFERENCES `lookups` (`Id`);

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`CategoryId`) REFERENCES `lookups` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`SubCategoryId`) REFERENCES `lookups` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `applications_ibfk_3` FOREIGN KEY (`Unit`) REFERENCES `lookups` (`Id`),
  ADD CONSTRAINT `applications_ibfk_4` FOREIGN KEY (`LanguageId`) REFERENCES `lookups` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `emailhistory`
--
ALTER TABLE `emailhistory`
  ADD CONSTRAINT `emailhistory_ibfk_1` FOREIGN KEY (`Status`) REFERENCES `lookups` (`Id`);

--
-- Constraints for table `lookups`
--
ALTER TABLE `lookups`
  ADD CONSTRAINT `lookups_ibfk_1` FOREIGN KEY (`LookupTypeId`) REFERENCES `lookuptypes` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `lookups_ibfk_2` FOREIGN KEY (`ParentLookupId`) REFERENCES `lookups` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`ApplicationId`) REFERENCES `applications` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `user` (`Id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`ApplicationId`) REFERENCES `applications` (`Id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `user` (`Id`);

--
-- Constraints for table `usertokens`
--
ALTER TABLE `usertokens`
  ADD CONSTRAINT `usertokens_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `user` (`Id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
