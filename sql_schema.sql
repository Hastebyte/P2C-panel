-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 25, 2020 at 02:46 AM
-- Server version: 5.7.31
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gateway`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(50) NOT NULL DEFAULT '',
  `admin` tinyint(1) DEFAULT NULL,
  `reseller` tinyint(1) DEFAULT NULL,
  `developer` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `property` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(2048) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `durations`
--

CREATE TABLE IF NOT EXISTS `durations` (
  `durationid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `duration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `gameid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `maintenance` tinyint(1) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `userfile` varchar(255) DEFAULT NULL,
  `kernelfile` varchar(255) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `developerid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `serialid` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `success` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

CREATE TABLE IF NOT EXISTS `pricing` (
  `pricingid` int(11) NOT NULL,
  `accountid` int(11) NOT NULL,
  `gameid` int(11) NOT NULL,
  `durationid` int(11) NOT NULL,
  `price` float(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `serials`
--

CREATE TABLE IF NOT EXISTS `serials` (
  `id` int(11) NOT NULL,
  `serial` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `duration` int(11) NOT NULL,
  `registered` date DEFAULT NULL,
  `created` date NOT NULL,
  `commission` int(11) DEFAULT NULL,
  `comments` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `computerid` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gameid` int(11) NOT NULL,
  `resellerid` int(11) DEFAULT NULL,
  `resetcount` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` int(11) NOT NULL,
  `resellerid` int(11) DEFAULT NULL,
  `username` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `ipaddress` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `level` tinyint(1) DEFAULT NULL,
  `expiry` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `durations`
--
ALTER TABLE `durations`
  ADD PRIMARY KEY (`durationid`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`gameid`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pricing`
--
ALTER TABLE `pricing`
  ADD PRIMARY KEY (`pricingid`);

--
-- Indexes for table `serials`
--
ALTER TABLE `serials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `log`
--
ALTER TABLE `log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `pricingid` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `serials`
--
ALTER TABLE `serials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
