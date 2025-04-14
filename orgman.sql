-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 14, 2025 at 02:27 PM
-- Server version: 8.4.3
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `orgman`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(10) NOT NULL,
  `org_id` varchar(20) NOT NULL,
  `category` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`announcement_id`, `title`, `content`, `created_at`, `created_by`, `org_id`, `category`) VALUES
(1, 'Coke chaka JD daw sa IT night', 'Be happy', '2025-04-14 20:41:45', '2022300133', 'curr_bits', 'Reminder'),
(2, 'CHAT GPT', 'hi guys chat gpt babagsak na ko kailangan ko ng chat gpt', '2025-04-14 20:58:34', '2022300133', 'curr_bits', 'Update');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `audit_id` int NOT NULL,
  `task_id` int NOT NULL,
  `changes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `org_id` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(20) NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `related_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `org_id`, `message`, `type`, `is_read`, `created_at`, `related_id`) VALUES
(1, '2022300133', 'curr_bits', 'New announcement: CHAT GPT', 'announcement', 1, '2025-04-14 20:59:07', 0),
(2, '2022300359', 'curr_bits', 'New announcement: CHAT GPT', 'announcement', 1, '2025-04-14 20:59:07', 1),
(3, '2022300133', 'curr_bits', 'Jade Amber B. Bautista updated task status: Ayusin Aircon is now Not Started', 'task_update', 1, '2025-04-14 21:08:38', 3),
(4, '2022300133', 'curr_bits', 'Jade Amber B. Bautista updated task status: let\'s grind jade is now In Progress', 'task_update', 1, '2025-04-14 21:09:02', 2),
(5, '2022300133', 'curr_bits', 'Jade Amber B. Bautista updated task status: Ayusin Aircon is now In Progress', 'task_update', 1, '2025-04-14 21:54:41', 3),
(6, '2022300359', 'curr_bits', 'New task assigned: BARBARINO BARBARINO', 'task', 1, '2025-04-14 21:59:44', 5),
(7, '2022300359', 'curr_bits', 'New task assigned: BARBARINO BARBARINO', 'task', 1, '2025-04-14 22:00:16', 6);

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `org_id` varchar(20) NOT NULL,
  `org_name` varchar(50) NOT NULL,
  `classification` varchar(20) NOT NULL,
  `departments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`org_id`, `org_name`, `classification`, `departments`) VALUES
('curr_bits', 'Current Bits', 'Technology', 'IT, Software Development, QA'),
('exc_jbes', 'Excellence JBES', 'Education', 'Teaching, Research, HR');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int NOT NULL,
  `task_title` varchar(30) NOT NULL,
  `member_id` int NOT NULL,
  `deadline` datetime NOT NULL,
  `priority` varchar(10) NOT NULL,
  `status` varchar(50) NOT NULL,
  `task_details` text NOT NULL,
  `link` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `task_title`, `member_id`, `deadline`, `priority`, `status`, `task_details`, `link`) VALUES
(1, 'Update database', 2022300133, '2025-03-29 12:45:40', 'High', 'Not Started', 'Simulan mo na tapusin lahat pukengina mo', ''),
(2, 'let\'s grind jade', 2022300359, '2025-03-26 00:00:00', 'High', 'In Progress', 'yay', ''),
(3, 'Ayusin Aircon', 2022300359, '2025-04-17 00:00:00', 'High', 'In Progress', 'Paki gawa pls\r\n', 'https://www.youtube.com/'),
(4, 'Manuod ng balita', 2022300359, '2025-04-15 00:00:00', 'Normal', 'Completed', 'hala ka', 'https://www.youtube.com/'),
(5, 'BARBARINO BARBARINO', 2022300359, '2025-04-17 00:00:00', 'Medium', 'Not Started', 'SOME LONG FOR????????\r\nBEFORE FADING TO?????', 'yotube'),
(6, 'BARBARINO BARBARINO', 2022300359, '2025-04-15 00:00:00', 'High', 'Not Started', 'BOMBARDINO', 'youutbb');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(10) NOT NULL,
  `password` varchar(30) NOT NULL,
  `org_id` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `position` varchar(20) NOT NULL,
  `department` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`username`, `password`, `org_id`, `name`, `position`, `department`, `created_at`) VALUES
('2022300133', 'admin', 'curr_bits', 'Yuri Andrei B. Morrison', 'President', 'Presidents', '2025-04-14 21:51:01'),
('2022300178', 'admin', 'exc_jbes', 'Kylee Superficial', 'President', 'Presidents', '2025-04-14 21:51:01'),
('2022300359', 'admin', 'curr_bits', 'Jade Amber B. Bautista', 'Ganda lang', 'Logistics', '2025-04-14 21:51:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `fk_announcements_user` (`created_by`,`org_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`audit_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notifications_user` (`user_id`,`org_id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`org_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`,`org_id`),
  ADD KEY `fk_users_org` (`org_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `audit_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_announcements_user` FOREIGN KEY (`created_by`,`org_id`) REFERENCES `users` (`username`, `org_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`,`org_id`) REFERENCES `users` (`username`, `org_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_org` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`org_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
