-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mariadb
-- Generation Time: May 05, 2026 at 11:53 AM
-- Server version: 10.6.20-MariaDB-ubu2004
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `power_house_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_pk` char(50) NOT NULL,
  `item_lat` varchar(50) NOT NULL,
  `item_lon` varchar(50) NOT NULL,
  `item_price` int(10) UNSIGNED NOT NULL,
  `item_price_per_meter` int(5) NOT NULL,
  `item_monthly_expenses` int(5) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `item_zip_code` varchar(20) NOT NULL,
  `item_city_name` varchar(30) NOT NULL,
  `item_road_name` varchar(100) NOT NULL,
  `item_house_number` varchar(10) NOT NULL,
  `item_number_of_rooms` varchar(3) NOT NULL,
  `item_year_built` int(4) NOT NULL,
  `item_main_image_path` varchar(500) NOT NULL,
  `item_floor_plan_path` varchar(500) NOT NULL,
  `item_floor_square_meters` int(4) NOT NULL,
  `item_area_square_meters` int(10) NOT NULL,
  `item_energy_label` varchar(5) NOT NULL,
  `item_days_listed` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_pk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
