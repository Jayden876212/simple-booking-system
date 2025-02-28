-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2025 at 12:31 AM
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
-- Database: `simple_booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `timeslot_start_time` time NOT NULL,
  `username` varchar(255) NOT NULL,
  `booking_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `timeslot_start_time`, `username`, `booking_date`) VALUES
(102, '09:00:00', 'jayden', '2025-02-24'),
(103, '09:00:00', 'bob', '2025-02-24');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_name` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_name`, `description`, `price`) VALUES
('Hamburger', 'A hamburger, or simply a burger, is a dish consisting of fillings—usually a patty of ground meat, typically beef—placed inside a sliced bun or bread roll. The patties are often served with cheese, lettuce, tomato, onion, pickles, bacon, or chilis with condiments such as ketchup, mustard, mayonnaise, relish or a \"special sauce\", often a variation of Thousand Island dressing, and are frequently placed on sesame seed buns. A hamburger patty topped with cheese is called a cheeseburger.[1] Under some definitions, and in some cultures, a burger is considered a sandwich.', 20),
('Pasta', 'Pasta (UK: /ˈpæstə/, US: /ˈpɑːstə/; Italian: [ˈpasta]) is a type of food typically made from an unleavened dough of wheat flour mixed with water or eggs, and formed into sheets or other shapes, then cooked by boiling or baking. Pasta was originally only made with durum, although the definition has been expanded to include alternatives for a gluten-free diet, such as rice flour, or legumes such as beans or lentils. Pasta is believed to have developed independently in Italy and is a staple food of Italian cuisine,[1][2] with evidence of Etruscans making pasta as early as 400 BCE in Italy.', 50),
('Rice', 'Rice is a cereal grain and in its domesticated form is the staple food of over half of the world\'s population, particularly in Asia and Africa. Rice is the seed of the grass species Oryza sativa (Asian rice)—or, much less commonly, Oryza glaberrima (African rice). Asian rice was domesticated in China some 13,500 to 8,200 years ago; African rice was domesticated in Africa about 3,000 years ago.', 15);

-- --------------------------------------------------------

--
-- Table structure for table `item_orders`
--

CREATE TABLE `item_orders` (
  `item_order_id` int(11) NOT NULL,
  `item_name` varchar(20) NOT NULL,
  `order_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_orders`
--

INSERT INTO `item_orders` (`item_order_id`, `item_name`, `order_id`, `quantity`) VALUES
(54, 'Hamburger', 27, 0),
(55, 'Pasta', 27, 0),
(56, 'Rice', 27, 0),
(57, 'Hamburger', 33, 1),
(58, 'Pasta', 33, 0),
(59, 'Rice', 33, 0),
(60, 'Hamburger', 34, 0),
(61, 'Pasta', 34, 0),
(62, 'Rice', 34, 0),
(63, 'Hamburger', 35, 1),
(64, 'Pasta', 35, 0),
(65, 'Rice', 35, 0),
(66, 'Hamburger', 36, 1),
(67, 'Pasta', 36, 0),
(68, 'Rice', 36, 0),
(69, 'Hamburger', 37, 0),
(70, 'Pasta', 37, 2),
(71, 'Rice', 37, 1),
(72, 'Hamburger', 38, 0),
(73, 'Pasta', 38, 0),
(74, 'Rice', 38, 1),
(75, 'Hamburger', 39, 1),
(76, 'Pasta', 39, 0),
(77, 'Rice', 39, 0),
(78, 'Hamburger', 40, 1),
(79, 'Hamburger', 41, 2),
(80, 'Pasta', 41, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `datetime_ordered` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `booking_id`, `datetime_ordered`) VALUES
(27, 102, '2025-02-23 20:12:16'),
(33, 102, '2025-02-23 20:15:22'),
(34, 102, '2025-02-23 21:06:54'),
(35, 102, '2025-02-23 21:26:19'),
(36, 102, '2025-02-23 21:27:10'),
(37, 102, '2025-02-23 22:21:44'),
(38, 102, '2025-02-23 22:21:51'),
(39, 102, '2025-02-23 22:50:27'),
(40, 102, '2025-02-23 22:51:56'),
(41, 102, '2025-02-23 22:53:37');

-- --------------------------------------------------------

--
-- Table structure for table `timeslots`
--

CREATE TABLE `timeslots` (
  `timeslot_start_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timeslots`
--

INSERT INTO `timeslots` (`timeslot_start_time`) VALUES
('09:00:00'),
('10:00:00'),
('11:00:00'),
('12:00:00'),
('13:00:00'),
('14:00:00'),
('15:00:00'),
('16:00:00'),
('17:00:00');

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `booking_username` (`username`),
  ADD KEY `booking_timeslot` (`timeslot_start_time`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_name`);

--
-- Indexes for table `item_orders`
--
ALTER TABLE `item_orders`
  ADD PRIMARY KEY (`item_order_id`),
  ADD KEY `on_item` (`item_name`),
  ADD KEY `on_order` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `order_for_booking` (`booking_id`);

--
-- Indexes for table `timeslots`
--
ALTER TABLE `timeslots`
  ADD PRIMARY KEY (`timeslot_start_time`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `item_orders`
--
ALTER TABLE `item_orders`
  MODIFY `item_order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `booking_timeslot` FOREIGN KEY (`timeslot_start_time`) REFERENCES `timeslots` (`timeslot_start_time`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `booking_username` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `item_orders`
--
ALTER TABLE `item_orders`
  ADD CONSTRAINT `on_item` FOREIGN KEY (`item_name`) REFERENCES `items` (`item_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `on_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `order_for_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
