-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2024 at 05:01 PM
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

CREATE Database `outpass`;

use outpass;
--

-- --------------------------------------------------------

--
-- Table structure for table `facultymaster`
--

CREATE TABLE `facultymaster` (
  `FacultyId` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Branch` varchar(255) NOT NULL,
  `Stream` varchar(255) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  `FacultyType` enum('Care Taker','Warden','Student Welfare','Director') NOT NULL,
  `InchargeFor` enum('Male','Female') DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `outpassrequests`
--

CREATE TABLE `outpassrequests` (
  `RequestID` int(11) NOT NULL,
  `StudentID` varchar(20) DEFAULT NULL,
  `Reason` varchar(255) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `InTime` datetime DEFAULT NULL,
  `OutTime` datetime DEFAULT NULL,
  `ColorCode` varchar(50) DEFAULT NULL,
  `IconName` varchar(50) DEFAULT NULL,
  `CurrentLevel` enum('Care Taker','Warden','Student Welfare','Director') DEFAULT 'Care Taker',
  `RequestStatus` enum('Pending','Rejected','Approved') DEFAULT 'Pending',
  `EscalationStatus` enum('No','Yes') DEFAULT 'No',
  `RequestDate` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `passwordreset`
--

CREATE TABLE `passwordreset` (
  `UserID` int(11) NOT NULL,
  `Token` varchar(64) NOT NULL,
  `ExpiryTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `studentmaster`
--

CREATE TABLE `studentmaster` (
  `Id` int(11) NOT NULL,
  `StudentID` varchar(20) DEFAULT NULL,
  `Email` varchar(255) NOT NULL,
  `Branch` varchar(100) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Stream` varchar(100) NOT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `Phone` varchar(20) NOT NULL,
  `StudentAddress` text NOT NULL,
  `Guardian` varchar(100) NOT NULL,
  `GuardianName` varchar(100) NOT NULL,
  `GuardianContact` varchar(20) NOT NULL,
  `GuardianAddress` text NOT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- --------------------------------------------------------

--
-- Table structure for table `usermaster`
--

CREATE TABLE `usermaster` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) DEFAULT '$2y$10$saC3lxrDlSVfSLPLdoJdUe7GVw2jenMQztfW/hCumOeA.2o2ahpeW',
  `UserType` enum('Student','Faculty') NOT NULL,
  `UserRole` enum('Admin','Student','Faculty') DEFAULT NULL,
  `Branch` varchar(1000) DEFAULT NULL,
  `IsFirstLogin` tinyint(1) DEFAULT 1,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usermaster`
--

INSERT INTO `usermaster` (`UserID`, `Username`, `Email`, `Password`, `UserType`, `UserRole`, `Branch`, `IsFirstLogin`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Admin', 'r190306@rguktrkv.ac.in', '$2y$10$saC3lxrDlSVfSLPLdoJdUe7GVw2jenMQztfW/hCumOeA.2o2ahpeW', 'Faculty', 'Admin', NULL, 0, 1, '2024-11-12 20:25:44', '2024-11-19 23:12:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `facultymaster`
--
ALTER TABLE `facultymaster`
  ADD PRIMARY KEY (`FacultyId`);

--
-- Indexes for table `outpassrequests`
--
ALTER TABLE `outpassrequests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `StudentID` (`StudentID`);

--
-- Indexes for table `passwordreset`
--
ALTER TABLE `passwordreset`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `studentmaster`
--
ALTER TABLE `studentmaster`
  ADD PRIMARY KEY (`Id`),
  ADD UNIQUE KEY `StudentID` (`StudentID`);

--
-- Indexes for table `usermaster`
--
ALTER TABLE `usermaster`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `facultymaster`
--
ALTER TABLE `facultymaster`
  MODIFY `FacultyId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `outpassrequests`
--
ALTER TABLE `outpassrequests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=408;

--
-- AUTO_INCREMENT for table `studentmaster`
--
ALTER TABLE `studentmaster`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `usermaster`
--
ALTER TABLE `usermaster`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `outpassrequests`
--
ALTER TABLE `outpassrequests`
  ADD CONSTRAINT `outpassrequests_ibfk_1` FOREIGN KEY (`StudentID`) REFERENCES `studentmaster` (`StudentID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
