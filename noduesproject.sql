-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2024 at 06:06 AM
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
-- Database: `noduesproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `deptId` varchar(50) NOT NULL,
  `deptName` varchar(100) DEFAULT NULL,
  `deptPassword` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`deptId`, `deptName`, `deptPassword`) VALUES
('D01', 'Fee', '$2y$10$eHJGQjfmpeE9C5IZLK9ktuVdKAuqPgFc74mSdCZ4QTioP0UQXVFj.'),
('D02', 'Library', '$2y$10$U1P4.dNUgJXD9iFQJB2ot.Jfjuwik2UY0ET6jHS4raHj/rpEO/Be2'),
('D03', 'Computer Center', '$2y$10$K.ZWjDHfnW1udkg5mpIScOf.Lk11OG.023b1UigG.SeTJLx7RBx1O'),
('D04', 'Office', '$2y$10$OvFsyPVza.Oj.EabWyUKEuZB42NWstw6uBgM6iwnmOg7LumbiD2rG');

-- --------------------------------------------------------

--
-- Table structure for table `nodues`
--

CREATE TABLE `nodues` (
  `noDueId` int(11) NOT NULL,
  `requestId` varchar(20) DEFAULT NULL,
  `deptId` varchar(50) DEFAULT NULL,
  `noDueApproval` varchar(10) DEFAULT 'no',
  `noDueComment` varchar(255) DEFAULT NULL,
  `approvalDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nodues`
--

INSERT INTO `nodues` (`noDueId`, `requestId`, `deptId`, `noDueApproval`, `noDueComment`, `approvalDate`) VALUES
(1, 'REQmca01', 'D01', 'Yes', 'cleared', '2024-08-22 15:16:59'),
(2, 'REQmca01', 'D02', 'Yes', 'dues cleared', '2024-08-23 00:38:13'),
(3, 'REQmca01', 'D03', 'Yes', 'approved', '2024-08-23 16:41:55'),
(4, 'REQmca01', 'D04', 'Yes', 'approved', '2024-08-23 16:38:34'),
(5, 'REQbba74', 'D01', 'Yes', ' clear', '2024-08-22 22:57:45'),
(6, 'REQbba74', 'D02', 'Yes', 'approved', '2024-08-23 17:17:44'),
(7, 'REQbba74', 'D03', 'Yes', 'approved', '2024-08-23 17:18:52'),
(8, 'REQbba74', 'D04', 'Yes', 'dues cleared', '2024-08-24 00:01:18'),
(9, 'REQbca55', 'D01', 'Yes', 'all fee deposited', '2024-09-21 09:18:51'),
(10, 'REQbca55', 'D02', 'Yes', 'books deposited', '2024-08-30 11:54:33'),
(11, 'REQbca55', 'D03', 'Yes', 'approved', '2024-08-25 22:00:28'),
(12, 'REQbca55', 'D04', 'Yes', 'all documents  submitted', '2024-09-21 09:19:35'),
(13, 'REQmba06', 'D01', 'Yes', 'fee all cleared', '2024-09-19 01:14:12'),
(14, 'REQmba06', 'D02', 'Yes', 'all book submitted', '2024-09-23 00:41:47'),
(15, 'REQmba06', 'D03', 'Yes', 'nodues approved', '2024-09-23 00:43:58'),
(16, 'REQmba06', 'D04', 'Yes', 'nodues cleared', '2024-09-23 00:44:33'),
(17, 'REQmca71', 'D01', 'Yes', 'dues cleared', '2024-11-04 01:20:27'),
(18, 'REQmca71', 'D02', 'No', 'card not submitted', '2024-09-23 00:41:26'),
(19, 'REQmca71', 'D03', 'No', NULL, NULL),
(20, 'REQmca71', 'D04', 'No', NULL, NULL),
(21, 'REQmca10', 'D01', 'Yes', 'approved', '2024-08-24 09:56:27'),
(22, 'REQmca10', 'D02', 'Yes', 'dues cleared', '2024-08-24 09:56:55'),
(23, 'REQmca10', 'D03', 'Yes', 'no dues', '2024-08-24 09:57:20'),
(24, 'REQmca10', 'D04', 'Yes', 'approved', '2024-08-24 09:57:46'),
(25, 'REQbcom07', 'D01', 'Yes', 'all fee cleared', '2024-08-25 21:30:35'),
(26, 'REQbcom07', 'D02', 'Yes', 'dues cleared', '2024-08-25 17:54:47'),
(27, 'REQbcom07', 'D03', 'Yes', 'dues cleared', '2024-08-25 21:31:18'),
(28, 'REQbcom07', 'D04', 'Yes', 'approved', '2024-08-25 17:54:05'),
(29, 'REQmba04', 'D01', 'Yes', 'fee all cleared', '2024-08-28 11:52:56'),
(30, 'REQmba04', 'D02', 'Yes', 'all dues cleared', '2024-08-28 11:54:00'),
(31, 'REQmba04', 'D03', 'Yes', 'dues are cleared', '2024-08-28 11:57:31'),
(32, 'REQmba04', 'D04', 'Yes', 'all documents are submitted', '2024-08-28 12:02:44'),
(33, 'REQmca20', 'D01', 'No', 'not cleared', '2024-10-16 02:20:55'),
(34, 'REQmca20', 'D02', 'No', NULL, NULL),
(35, 'REQmca20', 'D03', 'No', 'tc not submitted', '2024-10-07 22:32:19'),
(36, 'REQmca20', 'D04', 'No', 'some documents not submitted', '2024-09-23 10:15:40'),
(37, 'REQbca03', 'D01', 'Yes', 'all dues cleared', '2024-10-16 02:17:55'),
(38, 'REQbca03', 'D02', 'No', 'book not submitted', '2024-10-08 03:16:15'),
(39, 'REQbca03', 'D03', 'No', 'dues not clear', '2024-09-23 10:14:56'),
(40, 'REQbca03', 'D04', 'Yes', 'dues all cleared', '2024-11-04 01:18:13'),
(41, 'REQbba014', 'D01', 'Yes', 'no dues approved', '2024-09-23 10:14:00'),
(42, 'REQbba014', 'D02', 'Yes', 'cleared all no dues', '2024-09-23 10:14:31'),
(43, 'REQbba014', 'D03', 'Yes', 'dues clear', '2024-09-23 10:15:15'),
(44, 'REQbba014', 'D04', 'Yes', 'no dues approved', '2024-09-23 10:15:50'),
(45, 'REQmca061', 'D01', 'Yes', 'cleared all dues', '2024-10-16 02:19:37'),
(46, 'REQmca061', 'D02', 'Yes', 'dues are approved', '2024-10-07 22:31:37'),
(47, 'REQmca061', 'D03', 'Yes', 'all dues cleared', '2024-10-08 03:10:01'),
(48, 'REQmca061', 'D04', 'Yes', 'all documents cleared', '2024-10-16 02:21:43'),
(49, 'REQBA1124', 'D01', 'No', NULL, NULL),
(50, 'REQBA1124', 'D02', 'No', NULL, NULL),
(51, 'REQBA1124', 'D03', 'No', NULL, NULL),
(52, 'REQBA1124', 'D04', 'No', NULL, NULL),
(53, 'REQBBA1123', 'D01', 'No', NULL, NULL),
(54, 'REQBBA1123', 'D02', 'No', NULL, NULL),
(55, 'REQBBA1123', 'D03', 'No', NULL, NULL),
(56, 'REQBBA1123', 'D04', 'No', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `refundrequest`
--

CREATE TABLE `refundrequest` (
  `requestId` varchar(20) NOT NULL,
  `rollNo` varchar(20) DEFAULT NULL,
  `requestDate` datetime DEFAULT current_timestamp(),
  `refundDate` datetime DEFAULT NULL,
  `refundDescription` varchar(255) DEFAULT NULL,
  `refundStatus` varchar(10) DEFAULT 'No',
  `verifyDetails` varchar(255) DEFAULT 'selected',
  `verifyReason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refundrequest`
--

INSERT INTO `refundrequest` (`requestId`, `rollNo`, `requestDate`, `refundDate`, `refundDescription`, `refundStatus`, `verifyDetails`, `verifyReason`) VALUES
('REQBA1124', 'BA1124', '2024-10-15 01:34:22', NULL, NULL, 'No', 'Not Verified', 'account number not matching'),
('REQbba014', 'bba014', '2024-09-23 09:50:14', '2024-11-03 18:06:59', 'refund initiated ', 'Yes', 'Verified', ''),
('REQBBA1123', 'BBA1123', '2024-10-16 01:56:34', NULL, NULL, 'No', 'selected', ''),
('REQbba74', 'bba74', '2024-08-22 12:37:10', '2024-08-24 21:09:10', 'refund to be processed in your bank account in 3,4 days.', 'Yes', 'Verified', ''),
('REQbca03', 'bca03', '2024-09-22 17:39:59', NULL, NULL, 'No', '', ''),
('REQbca55', 'bca55', '2024-08-22 22:58:56', '2024-09-21 09:36:28', 'security amount initiated', 'Yes', 'Verified', ''),
('REQbcom07', 'bcom07', '2024-08-25 17:21:36', '2024-09-20 07:09:29', 'refund initiated ', 'Yes', 'Verified', ''),
('REQmba04', 'mba04', '2024-08-27 10:08:23', '2024-09-22 14:05:32', 'refund is initiated', 'Yes', 'Verified', ''),
('REQmba06', 'mba06', '2024-08-22 23:37:14', '2024-09-23 00:53:27', 'refund initiated', 'Yes', 'Verified', ''),
('REQmca01', 'mca01', '2024-08-22 12:36:19', '2024-08-23 20:46:37', 'Your refund is initiated and amount will be transferred in 4-5 days.', 'Yes', 'Verified', ''),
('REQmca061', 'mca061', '2024-09-23 10:12:01', NULL, NULL, 'No', 'Verified', ''),
('REQmca10', 'mca10', '2024-08-24 09:55:52', '2024-08-24 10:14:20', 'refund initiated and amount be transferred in 4,5 days.', 'Yes', 'Verified', ''),
('REQmca20', 'mca20', '2024-09-19 01:03:57', NULL, NULL, 'No', 'Verified', ''),
('REQmca71', 'mca71', '2024-08-23 23:42:40', NULL, NULL, 'No', 'Verified', '');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `batchSession` varchar(20) DEFAULT NULL,
  `enrollmentNo` varchar(20) DEFAULT NULL,
  `rollNo` varchar(20) NOT NULL,
  `Course` varchar(50) DEFAULT NULL,
  `Name` varchar(100) DEFAULT NULL,
  `fatherName` varchar(100) DEFAULT NULL,
  `motherName` varchar(100) DEFAULT NULL,
  `Contact` varchar(15) DEFAULT NULL,
  `Dob` date DEFAULT NULL,
  `securityAmount` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`batchSession`, `enrollmentNo`, `rollNo`, `Course`, `Name`, `fatherName`, `motherName`, `Contact`, `Dob`, `securityAmount`) VALUES
('2020-2023', '114', 'BA1124', 'B A', 'Sakshi', 'T K', 'Deepa', '8796789000', '2002-04-04', 8000),
('2022-2025', 'ENR0105', 'bba014', 'BBA', 'Tiya Rai', 'Aman Rai', 'Radha', '4545454545', '2003-11-09', 8000),
('2022-2025', '113', 'BBA1123', 'BBA', 'Pallavi', 'Pramod Rai', 'Sima Rai', '9801108340', '2004-05-23', 8000),
('2022-2025', 'ENR015', 'bba14', 'BBA', 'Tiya Rai', 'Deep Rai', 'Radha', '4545454545', '2003-11-09', 8000),
('2023-2026', 'ENR008', 'bba23', 'BBA', 'Kiran', 'Rohan Kumar', 'Sunita', '2324514554', '2001-07-01', 8000),
('2022-2025', 'ENR005', 'bba74', 'BBA', 'Disha', 'Manoj', 'Radha', '3344514554', '2003-10-05', 8000),
('2021-2024', 'ENR0104', 'bca003', 'BCA', 'Teena Sharma', 'Amit Sharma', 'Neeta Sharma', '9874514554', '2002-10-21', 8000),
('2021-2024', 'ENR014', 'bca03', 'BCA', 'Teena Sharma', 'Amit Sharma', 'Neeta Sharma', '9874514554', '2002-10-21', 8000),
('2022-2025', '116', 'BCA1126', 'BCA', 'Chintu', 'Mintu', 'Chinti', '8676787888', '2002-08-30', 8000),
('2021-2024', 'ENR004', 'bca23', 'BCA', 'Amit Sharma', 'Arvind Sharma', 'Neeta Sharma', '3134514554', '2003-10-11', 8000),
('2019-2022', 'ENR010', 'bca55', 'BCA', 'Happy Singh', 'Vijay Singh', 'Suman Singh', '4455667788', '2003-09-03', 8000),
('2023-2026', 'ENR007', 'bcom07', 'BCOM', 'Aman Patel', 'Sunil Patel', 'Meena Patel', '1114514554', '2000-12-01', 5000),
('2021-2024', '112', 'BCOM1234', 'B COM', 'Muskan', 'Pramod Rai', 'Sima Rai', '8102627458', '2003-12-17', 8000),
('2020-2022', 'ENR002', 'mba04', 'MBA', 'Bhanu', 'Anil', 'Aarti', '3141341341', '2002-11-01', 5000),
('2022-2024', 'ENR006', 'mba06', 'MBA', 'Trisha', 'Pradeep', 'Suman Singh', '3130004554', '2004-02-01', 5000),
('2020-2022', 'ENR012', 'mba104', 'MBA', 'Karan', 'Daddy', 'Mansi', '5641341341', '2002-01-01', 5000),
('2020-2022', 'ENR0102', 'mba14', 'MBA', 'Karan', 'Daddy', 'Mansi', '5641341341', '2002-01-01', 5000),
('2020-2022', 'ENR001', 'mca01', 'MCA', 'Varsha Singh', 'Yogesh Singh', 'Madhu Singh', '1234567890', '2000-12-01', 5000),
('2021-2023', 'ENR0103', 'mca020', 'MCA', 'Sanjay Kumar', 'Rakesh Kumar', 'Poonam', '2436675635', '2003-12-05', 5000),
('2020-2022', 'ENR011', 'mca06', 'MCA', 'Twinkle Singh', 'Y K Singh', 'Meeta Singh', '5432167890', '2001-12-01', 5000),
('2020-2022', 'ENR0101', 'mca061', 'MCA', 'Twinkle Singh', 'Y K Singh', 'Meeta Singh', '5432167890', '2001-12-01', 5000),
('2021-2023', 'ENR003', 'mca10', 'MCA', 'Chetan Kumar', 'Rakesh Kumar', 'Pooja', '2436245635', '2000-12-05', 5000),
('2021-2023', 'ENR013', 'mca20', 'MCA', 'Sanjay Kumar', 'Rakesh Kumar', 'Poonam', '2436675635', '2003-12-05', 5000),
('2019-2021', 'ENR009', 'mca71', 'MCA', 'Tanvi', 'Sunil', 'Anita', '2233445566', '2004-07-02', 5000),
('2021-2023', '115', 'MCOM1125', 'M COM', 'Chinki', 'Minku', 'Meeta', '7865657889', '2000-10-13', 5000),
('2024-2026', '111', 'MCOM12345', 'M COM', 'Varsha', 'Y K Singh', 'Madhuri Singh', '9989898990', '2002-11-04', 5000);

-- --------------------------------------------------------

--
-- Table structure for table `uploadcheque`
--

CREATE TABLE `uploadcheque` (
  `uploadId` int(11) NOT NULL,
  `rollNo` varchar(20) DEFAULT NULL,
  `filePath` varchar(255) DEFAULT NULL,
  `accHolderName` varchar(100) DEFAULT NULL,
  `bankName` varchar(100) DEFAULT NULL,
  `accountNo` bigint(30) DEFAULT NULL,
  `ifscCode` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploadcheque`
--

INSERT INTO `uploadcheque` (`uploadId`, `rollNo`, `filePath`, `accHolderName`, `bankName`, `accountNo`, `ifscCode`) VALUES
(1, 'mca01', '../admin/uploadFile/mca01_form.pdf', 'my name', 'My Bank', 467882345557, 'Bank34355'),
(2, 'bba74', '../admin/uploadFile/bba74_form.pdf', 'my name', 'My Bank', 467882345557, 'Bank34355'),
(3, 'bca55', '../admin/uploadFile/bca55_form.pdf', 'my name', 'My Bank', 467882345557, 'Bank34355'),
(4, 'mba06', '../admin/uploadFile/mba06_form.pdf', 'my name', 'My Bank', 467882345557, 'Bank34355'),
(5, 'mca71', '../admin/uploadFile/mca71_form.pdf', 'my name', 'My Bank', 467882345557, 'Bank34355'),
(6, 'mca10', '../admin/uploadFile/mca10_tc.pdf', 'my name', 'My Bank', 467882345557, 'Bank34355'),
(7, 'bcom07', '../admin/uploadFile/bcom07_mca 1.pdf', 'My Name', 'My Bank', 6534882345557, 'Bank00121'),
(8, 'mba04', '../admin/uploadFile/mba04_5th Semester Result.pdf', 'my name', 'My Bank', 9999900234, 'Bank34355'),
(9, 'mca20', '../admin/uploadFile/mca20_mca 1.pdf', 'sanjay kumar', 'SBI ', 409890344759, 'sbi4353'),
(10, 'bca03', '../admin/uploadFile/bca03_DBMS_LAB.pdf', 'Teena Sharma', 'Axis', 66952450009, 'axis00012'),
(11, 'bba014', '../admin/uploadFile/bba014_mca 1.pdf', 'Tiya Rai', 'Axis', 9999900234, 'axis0012'),
(12, 'mca061', '../admin/uploadFile/mca061_2 sem bca.pdf', 'Twinkle Singh', 'SBI ', 66952454545, 'sbi4352'),
(13, 'BA1124', '../admin/uploadFile/BA1124_NOTICE - HR CONCLAVE.pdf', 'sakshi', 'SBI', 9999900234, 'Bank00121'),
(14, 'BBA1123', '../admin/uploadFile/BBA1123_CV for III Sem._Notice.pdf', 'pallavi', 'Axis', 1111882345557, 'axis0012');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`deptId`);

--
-- Indexes for table `nodues`
--
ALTER TABLE `nodues`
  ADD PRIMARY KEY (`noDueId`),
  ADD KEY `requestId` (`requestId`),
  ADD KEY `deptId` (`deptId`);

--
-- Indexes for table `refundrequest`
--
ALTER TABLE `refundrequest`
  ADD PRIMARY KEY (`requestId`),
  ADD KEY `rollNo` (`rollNo`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`rollNo`),
  ADD UNIQUE KEY `enrollmentNo` (`enrollmentNo`);

--
-- Indexes for table `uploadcheque`
--
ALTER TABLE `uploadcheque`
  ADD PRIMARY KEY (`uploadId`),
  ADD KEY `Roll_No` (`rollNo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nodues`
--
ALTER TABLE `nodues`
  MODIFY `noDueId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `uploadcheque`
--
ALTER TABLE `uploadcheque`
  MODIFY `uploadId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `nodues`
--
ALTER TABLE `nodues`
  ADD CONSTRAINT `nodues_ibfk_1` FOREIGN KEY (`requestId`) REFERENCES `refundrequest` (`requestId`),
  ADD CONSTRAINT `nodues_ibfk_2` FOREIGN KEY (`deptId`) REFERENCES `department` (`deptId`);

--
-- Constraints for table `refundrequest`
--
ALTER TABLE `refundrequest`
  ADD CONSTRAINT `refundrequest_ibfk_1` FOREIGN KEY (`rollNo`) REFERENCES `student` (`rollNo`);

--
-- Constraints for table `uploadcheque`
--
ALTER TABLE `uploadcheque`
  ADD CONSTRAINT `uploadcheque_ibfk_1` FOREIGN KEY (`rollNo`) REFERENCES `student` (`rollNo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
