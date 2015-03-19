-- phpMyAdmin SQL Dump
-- version 4.2.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Mar 19, 2015 at 09:27 AM
-- Server version: 5.5.38
-- PHP Version: 5.6.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+01:00";

--
-- Database: `PJ2100`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
`bookingID` int(5) NOT NULL,
  `brukerID` int(5) NOT NULL,
  `romNr` int(4) NOT NULL,
  `dato` date NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bookingTimer`
--

CREATE TABLE `bookingTimer` (
  `bookingID` int(5) NOT NULL,
  `timeID` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `brukere`
--

CREATE TABLE `brukere` (
`brukerID` int(5) NOT NULL,
  `brukernavn` varchar(10) NOT NULL,
  `passord` varchar(32) NOT NULL,
  `rettigheter` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `brukere`
--

INSERT INTO `brukere` (`brukerID`, `brukernavn`, `passord`, `rettigheter`) VALUES
(1, 'admin', '239ab7c514067548cf9b3c897caa1403', 1),
(2, 'bruker1', '56f491c56340a6fa5c158863c6bfb39f', 0),
(3, 'bruker2', 'e10adc3949ba59abbe56e057f20f883e', 0),
(4, 'testbruker', '56f491c56340a6fa5c158863c6bfb39f', 0);

-- --------------------------------------------------------

--
-- Table structure for table `rom`
--

CREATE TABLE `rom` (
  `romNr` int(4) NOT NULL,
  `kapasitet` int(1) NOT NULL,
  `projektor` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `rom`
--

INSERT INTO `rom` (`romNr`, `kapasitet`, `projektor`) VALUES
(101, 2, 0),
(102, 2, 1),
(103, 3, 0),
(104, 3, 1),
(105, 3, 1),
(106, 4, 0),
(107, 4, 0),
(108, 4, 1),
(109, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `timer`
--

CREATE TABLE `timer` (
  `timeID` int(1) NOT NULL,
  `fraTid` time NOT NULL,
  `tilTid` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `timer`
--

INSERT INTO `timer` (`timeID`, `fraTid`, `tilTid`) VALUES
(1, '08:00:00', '09:00:00'),
(2, '09:00:00', '10:00:00'),
(3, '10:00:00', '11:00:00'),
(4, '11:00:00', '12:00:00'),
(5, '12:00:00', '13:00:00'),
(6, '13:00:00', '14:00:00'),
(7, '14:00:00', '15:00:00'),
(8, '15:00:00', '16:00:00'),
(9, '16:00:00', '17:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
 ADD PRIMARY KEY (`bookingID`), ADD KEY `brukerID` (`brukerID`), ADD KEY `romNr` (`romNr`);

--
-- Indexes for table `bookingTimer`
--
ALTER TABLE `bookingTimer`
 ADD PRIMARY KEY (`bookingID`,`timeID`), ADD KEY `timeID` (`timeID`);

--
-- Indexes for table `brukere`
--
ALTER TABLE `brukere`
 ADD PRIMARY KEY (`brukerID`), ADD UNIQUE KEY `brukernavn` (`brukernavn`);

--
-- Indexes for table `rom`
--
ALTER TABLE `rom`
 ADD PRIMARY KEY (`romNr`), ADD UNIQUE KEY `romNr` (`romNr`);

--
-- Indexes for table `timer`
--
ALTER TABLE `timer`
 ADD PRIMARY KEY (`timeID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
MODIFY `bookingID` int(5) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `brukere`
--
ALTER TABLE `brukere`
MODIFY `brukerID` int(5) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`brukerID`) REFERENCES `brukere` (`brukerID`),
ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`romNr`) REFERENCES `rom` (`romNr`);

--
-- Constraints for table `bookingTimer`
--
ALTER TABLE `bookingTimer`
ADD CONSTRAINT `bookingtimer_ibfk_1` FOREIGN KEY (`bookingID`) REFERENCES `booking` (`bookingID`),
ADD CONSTRAINT `bookingtimer_ibfk_2` FOREIGN KEY (`timeID`) REFERENCES `timer` (`timeID`);