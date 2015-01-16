-- phpMyAdmin SQL Dump
-- version 4.0.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 27, 2014 at 01:47 AM
-- Server version: 5.5.35-MariaDB
-- PHP Version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tracking`
--

-- --------------------------------------------------------

--
-- Table structure for table `tracking_visits`
--

CREATE TABLE IF NOT EXISTS `tracking_visits` (
  `tracking_id` int(11) NOT NULL AUTO_INCREMENT,
  `tracking_url` text NOT NULL,
  `tracking_ip` char(32) NOT NULL,
  `tracking_uid` char(32) NOT NULL,
  `tracking_ua` text NOT NULL,
  `tracking_browser` char(32) NOT NULL,
  `tracking_browser_version` char(32) NOT NULL,
  `tracking_os` char(32) NOT NULL,
  `tracking_width` int(11) NOT NULL,
  `tracking_height` int(11) NOT NULL,
  `tracking_viewport_size` text NOT NULL,
  `tracking_flash` text NOT NULL,
  `tracking_java` text NOT NULL,
  `tracking_title` text NOT NULL,
  `tracking_description` text NOT NULL,
  `tracking_encoding` text NOT NULL,
  `tracking_user_language` text NOT NULL,
  `tracking_utm_source` text NOT NULL,
  `tracking_utm_medium` text NOT NULL,
  `tracking_utm_campaign` text NOT NULL,
  `tracking_utm_content` text NOT NULL,
  `tracking_utm_term` text NOT NULL,
  `tracking_utm_id` int(11) NOT NULL,
  `tracking_gclid` text NOT NULL,
  `tracking_caching` int(11) NOT NULL,
  `tracking_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tracking_id`),
  KEY `wp_visits_cookie` (`tracking_uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
