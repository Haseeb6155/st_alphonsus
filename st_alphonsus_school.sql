-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2025 at 06:57 PM
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
-- Database: `st_alphonsus_school`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `pupil_id` int(11) DEFAULT NULL,
  `attendance_date` date DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `pupil_id`, `attendance_date`, `status`, `notes`, `created_at`) VALUES
(1, 6, '2025-11-30', 'Present', '', '2025-12-07 16:38:29'),
(2, 4, '2025-11-30', 'Absent', 'Sick', '2025-12-07 16:38:29'),
(3, 1, '2025-11-30', 'Present', '', '2025-12-07 16:38:29'),
(4, 3, '2025-11-30', 'Late', 'Rain', '2025-12-07 16:38:29'),
(5, 5, '2025-11-30', 'Absent', 'Cough', '2025-12-07 16:38:29'),
(6, 7, '2025-11-30', 'Present', '', '2025-12-07 16:38:29'),
(7, 2, '2025-11-30', 'Absent', 'No Reason', '2025-12-07 16:38:29'),
(8, 6, '2025-12-02', 'Absent', 'Blob', '2025-12-07 16:38:29'),
(9, 1, '2025-12-03', 'Absent', '', '2025-12-07 16:38:29'),
(12, 12, '2025-12-06', 'Present', NULL, '2025-12-07 16:38:29'),
(15, 7, '2025-12-06', 'Present', NULL, '2025-12-07 16:38:29'),
(16, 6, '2025-12-06', 'Late', 'Car problem', '2025-12-07 16:38:29'),
(18, 4, '2025-12-01', 'Absent', '', '2025-12-07 16:38:29'),
(19, 3, '2025-12-01', 'Late', '', '2025-12-07 16:38:29'),
(21, 2, '2025-12-03', 'Present', '', '2025-12-07 16:38:29'),
(22, 1, '2025-12-07', 'Present', '', '2025-12-07 17:10:52'),
(23, 2, '2025-12-07', 'Present', '', '2025-12-07 17:10:52');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `class_name` varchar(100) NOT NULL,
  `capacity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `teacher_id`, `class_name`, `capacity`) VALUES
(1, 1, 'Reception', 30),
(2, 2, 'Year One', 18),
(3, 3, 'Year Two', 15),
(4, 7, 'Year Three', 25);

-- --------------------------------------------------------

--
-- Table structure for table `library_books`
--

CREATE TABLE `library_books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `year_published` int(11) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `library_books`
--

INSERT INTO `library_books` (`book_id`, `title`, `author`, `year_published`, `available`) VALUES
(1, 'The Gruffalo', 'Julia Donaldson', 1999, 1),
(2, 'Fantastic Mr Fox', 'Roald Dahl', 1970, 1),
(3, 'Harry Potter and the Philosophers Stone', 'J.K. Rowling', 1995, 0),
(7, 'Harry Potter', 'J.K. Rowling', 1997, 1),
(8, 'Subar', 'James Bonds', 2002, 1);

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `parent_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`parent_id`, `full_name`, `address`, `email`, `phone`, `user_id`) VALUES
(9, 'Sarah Jenkins', '14 Maple Drive, Manchester, M1 2AB', 'sarah.j@example.com', '07700 900101', 29),
(10, 'Michael Chang', '42 High Street, Liverpool, L3 4XY', 'm.chang@example.com', '07700 900202', 30),
(11, 'Emily O\'Connor', '7 Oak Avenue, Birmingham, B2 5CD', 'emily.oconnor@example.com', '07700 900303', 31),
(12, 'David Okonjo', 'Flat 5, Riverside Apts, London, SE1 7TH', 'd.okonjo@example.com', '07700 900404', 32);

-- --------------------------------------------------------

--
-- Table structure for table `pupils`
--

CREATE TABLE `pupils` (
  `pupil_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `medical_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pupils`
--

INSERT INTO `pupils` (`pupil_id`, `class_id`, `full_name`, `address`, `medical_info`, `created_at`) VALUES
(1, 1, 'Harry Potter', '4 Privet Drive, Surrey', 'Needs glasses, scar pain', '2025-12-07 16:38:29'),
(2, 1, 'Ron Weasley', '123 Test Lane', 'Fear of spiders', '2025-12-07 16:38:29'),
(3, 2, 'Hermione Granger', '10 Heathgate, Hampstead', 'None', '2025-12-07 16:38:29'),
(4, 2, 'Draco Malfoy', 'Malfoy Manor, Wiltshire', 'General allergies', '2025-12-07 16:38:29'),
(5, 3, 'Luna Lovegood', 'The Rookery, Ottery', 'None', '2025-12-07 16:38:29'),
(6, 3, 'Cedric Diggory', 'White House, Near Ottery', 'None', '2025-12-07 16:38:29'),
(7, 4, 'Neville Longbottom', 'Longbottom Manor', 'Memory issues', '2025-12-07 16:38:29'),
(12, 4, 'Neil Armstone', 'America', 'Healthy Kid', '2025-12-07 16:38:29');

-- --------------------------------------------------------

--
-- Table structure for table `pupil_parent`
--

CREATE TABLE `pupil_parent` (
  `pupil_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pupil_parent`
--

INSERT INTO `pupil_parent` (`pupil_id`, `parent_id`) VALUES
(1, 11),
(2, 9),
(3, 11),
(4, 12),
(5, 10),
(6, 12),
(7, 9),
(12, 10);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `annual_salary` decimal(10,2) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `full_name`, `address`, `phone`, `annual_salary`, `user_id`) VALUES
(1, 'Rubeus Hagrid', '12 Maple Dr', '07700900123', 45000.00, 20),
(2, 'Mrs. Sarah Jones', '45 Oak Ln', '07700900456', 38500.00, 17),
(3, 'Miss Emily Davis', '89 Pine St', '07700900789', 32000.00, 21),
(7, 'Severus Katie', 'Spinner\'s End, Cokeworth', '07700 900102', 52000.00, 22);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','parent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`) VALUES
(16, 'admin', '$2y$10$DyVJeqlHUT9/cy78Ui2udep2XBaApOOMux4xDSOhxm6OiXZNbDREC', 'admin'),
(17, 'teacher', '$2y$10$9rr31A8m8FGp9U6zkn.DqOfA05HJ.Bre2P96CpB6UrYinvNU30NKm', 'teacher'),
(18, 'parent', '$2y$10$l3grguVDbntd4nd/NiYg7.Kl1QxtJgw5zIdcl6MMY6cj46wqYqCFW', 'parent'),
(20, 'hagrid', '$2y$10$9rr31A8m8FGp9U6zkn.DqOfA05HJ.Bre2P96CpB6UrYinvNU30NKm', 'teacher'),
(21, 'emily', '$2y$10$9rr31A8m8FGp9U6zkn.DqOfA05HJ.Bre2P96CpB6UrYinvNU30NKm', 'teacher'),
(22, 'severus', '$2y$10$9rr31A8m8FGp9U6zkn.DqOfA05HJ.Bre2P96CpB6UrYinvNU30NKm', 'teacher'),
(29, 'sjenkins', '$2y$10$ZRKw3igoB3GsgcB0joC.O.CIA/nVUt3Hp/Q94cRyQS5LXM0Neqi7a', 'parent'),
(30, 'mchang88', '$2y$10$8BxHPBpSUXOwp6X2v9IeYusfhEfHcJdkikqKLa9meZIAiXSIOXuxG', 'parent'),
(31, 'emily_oc', '$2y$10$IeaprioxViT9NBYh7cSJT.Fx2pTNh58AxdD8jM9PFH0r8rn4fi4g.', 'parent'),
(32, 'david_o', '$2y$10$Qy9MmpFJiMmx7r3GZZev2OtiVQLfidbC7/QR1218ZFxRVg.RL9RGS', 'parent');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `unique_attendance` (`pupil_id`,`attendance_date`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD UNIQUE KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `library_books`
--
ALTER TABLE `library_books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`parent_id`),
  ADD KEY `fk_parent_user` (`user_id`);

--
-- Indexes for table `pupils`
--
ALTER TABLE `pupils`
  ADD PRIMARY KEY (`pupil_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `pupil_parent`
--
ALTER TABLE `pupil_parent`
  ADD PRIMARY KEY (`pupil_id`,`parent_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD KEY `fk_teacher_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `library_books`
--
ALTER TABLE `library_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pupils`
--
ALTER TABLE `pupils`
  MODIFY `pupil_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`pupil_id`) REFERENCES `pupils` (`pupil_id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`);

--
-- Constraints for table `parents`
--
ALTER TABLE `parents`
  ADD CONSTRAINT `fk_parent_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `pupils`
--
ALTER TABLE `pupils`
  ADD CONSTRAINT `pupils_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Constraints for table `pupil_parent`
--
ALTER TABLE `pupil_parent`
  ADD CONSTRAINT `pupil_parent_ibfk_1` FOREIGN KEY (`pupil_id`) REFERENCES `pupils` (`pupil_id`),
  ADD CONSTRAINT `pupil_parent_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`parent_id`);

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `fk_teacher_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
