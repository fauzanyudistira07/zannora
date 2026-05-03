-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2026 at 09:09 PM
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
-- Database: `zannora_airline`
--

-- --------------------------------------------------------

--
-- Table structure for table `airlines`
--

CREATE TABLE `airlines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `airlines`
--

INSERT INTO `airlines` (`id`, `name`, `code`, `logo`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Zannora Air', 'ZNA', NULL, 'Maskapai demo untuk sistem reservasi Zannora.', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(2, 'Archipelago Sky', 'ASK', NULL, 'Rute domestik dan regional Indonesia.', '2026-04-01 00:21:07', '2026-04-01 00:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `airplanes`
--

CREATE TABLE `airplanes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `airline_id` bigint(20) UNSIGNED NOT NULL,
  `model` varchar(255) NOT NULL,
  `registration_number` varchar(255) NOT NULL,
  `capacity` int(10) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `airplanes`
--

INSERT INTO `airplanes` (`id`, `airline_id`, `model`, `registration_number`, `capacity`, `description`, `photo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Airbus A320', 'PK-ZNA-01', 24, 'Armada regional untuk rute domestik.', NULL, '2026-04-01 00:21:07', '2026-05-03 06:15:41'),
(2, 1, 'Boeing 737-800', 'PK-ZNA-02', 30, 'Armada menengah dengan kabin fleksibel.', NULL, '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(3, 1, 'Airbus A321neo', 'PK-ZNA-03', 36, 'Armada efisien untuk rute populer.', NULL, '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(4, 1, 'Boeing 737 MAX 8', 'PK-ZNA-04', 42, 'Armada high-demand untuk jadwal padat.', NULL, '2026-05-03 06:15:41', '2026-05-03 06:15:41');

-- --------------------------------------------------------

--
-- Table structure for table `airports`
--

CREATE TABLE `airports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `airports`
--

INSERT INTO `airports` (`id`, `code`, `name`, `city`, `country`, `created_at`, `updated_at`) VALUES
(1, 'CGK', 'Soekarno Hatta', 'Jakarta', 'Indonesia', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(2, 'DPS', 'Ngurah Rai', 'Denpasar', 'Indonesia', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(3, 'SUB', 'Juanda', 'Surabaya', 'Indonesia', '2026-04-01 00:21:07', '2026-04-01 00:21:07');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `flight_id` bigint(20) UNSIGNED NOT NULL,
  `booking_code` varchar(255) NOT NULL,
  `total_passengers` int(10) UNSIGNED NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `expired_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `flight_id`, `booking_code`, `total_passengers`, `total_price`, `status`, `expired_at`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'BK-20260401092958-295', 1, 850000.00, 'confirmed', '2026-04-02 09:29:58', '2026-04-01 02:29:58', '2026-05-02 00:11:47'),
(2, 2, 1, 'BK-20260503131500-440', 1, 850000.00, 'cancelled', '2026-05-03 13:24:10', '2026-05-03 06:15:00', '2026-05-03 06:24:10'),
(3, 2, 3, 'BK-20260503132430-518', 1, 980000.00, 'confirmed', '2026-05-04 13:24:30', '2026-05-03 06:24:30', '2026-05-03 06:47:04'),
(4, 6, 4, 'BK-20260503135404-138', 1, 1050000.00, 'pending', '2026-05-04 13:54:04', '2026-05-03 06:54:04', '2026-05-03 06:54:04'),
(5, 2, 4, 'BK-20260503140003-432', 1, 1050000.00, 'confirmed', '2026-05-04 14:00:03', '2026-05-03 07:00:03', '2026-05-03 07:15:32'),
(6, 2, 1, 'BK-20260503141548-900', 1, 850000.00, 'confirmed', '2026-05-04 14:15:48', '2026-05-03 07:15:48', '2026-05-03 07:16:20'),
(7, 2, 3, 'BK-20260503142648-836', 1, 980000.00, 'cancelled', '2026-05-03 14:58:55', '2026-05-03 07:26:48', '2026-05-03 07:58:55'),
(8, 2, 1, 'BK-20260503143615-385', 1, 850000.00, 'cancelled', '2026-05-03 14:58:46', '2026-05-03 07:36:15', '2026-05-03 07:58:46'),
(9, 2, 1, 'BK-20260503145905-541', 1, 850000.00, 'confirmed', '2026-05-04 14:59:05', '2026-05-03 07:59:05', '2026-05-03 07:59:57');

-- --------------------------------------------------------

--
-- Table structure for table `booking_details`
--

CREATE TABLE `booking_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `passenger_id` bigint(20) UNSIGNED NOT NULL,
  `seat_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `ticket_number` varchar(255) DEFAULT NULL,
  `boarding_status` enum('not_checked_in','checked_in','boarded') NOT NULL DEFAULT 'not_checked_in',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_details`
--

INSERT INTO `booking_details` (`id`, `booking_id`, `passenger_id`, `seat_id`, `price`, `ticket_number`, `boarding_status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 3, 850000.00, 'TK-BK20260401092958295-0001', 'not_checked_in', '2026-04-01 02:29:58', '2026-05-02 00:11:47'),
(2, 2, 3, 4, 850000.00, NULL, 'not_checked_in', '2026-05-03 06:15:00', '2026-05-03 06:15:00'),
(3, 3, 3, 102, 980000.00, 'TK-BK20260503132430518-0003', 'not_checked_in', '2026-05-03 06:24:30', '2026-05-03 06:47:04'),
(4, 4, 4, 108, 1050000.00, NULL, 'not_checked_in', '2026-05-03 06:54:04', '2026-05-03 06:54:04'),
(5, 5, 3, 114, 1050000.00, 'TK-BK20260503140003432-0005', 'not_checked_in', '2026-05-03 07:00:03', '2026-05-03 07:15:32'),
(6, 6, 3, 15, 850000.00, 'TK-BK20260503141548900-0006', 'not_checked_in', '2026-05-03 07:15:48', '2026-05-03 07:16:20'),
(7, 7, 3, 67, 980000.00, NULL, 'not_checked_in', '2026-05-03 07:26:48', '2026-05-03 07:26:48'),
(8, 8, 3, 5, 850000.00, NULL, 'not_checked_in', '2026-05-03 07:36:15', '2026-05-03 07:36:15'),
(9, 9, 3, 14, 850000.00, 'TK-BK20260503145905541-0009', 'not_checked_in', '2026-05-03 07:59:05', '2026-05-03 07:59:57');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flights`
--

CREATE TABLE `flights` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `airline_id` bigint(20) UNSIGNED NOT NULL,
  `airplane_id` bigint(20) UNSIGNED NOT NULL,
  `departure_airport_id` bigint(20) UNSIGNED NOT NULL,
  `arrival_airport_id` bigint(20) UNSIGNED NOT NULL,
  `flight_number` varchar(255) NOT NULL,
  `departure_time` datetime NOT NULL,
  `arrival_time` datetime NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `status` enum('scheduled','delayed','cancelled','completed') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `flights`
--

INSERT INTO `flights` (`id`, `airline_id`, `airplane_id`, `departure_airport_id`, `arrival_airport_id`, `flight_number`, `departure_time`, `arrival_time`, `price`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 2, 'ZN1001', '2026-05-05 08:00:00', '2026-05-05 10:00:00', 850000.00, 'scheduled', '2026-04-01 00:21:08', '2026-05-03 06:15:41'),
(2, 1, 2, 1, 2, 'ZN1002', '2026-05-05 13:30:00', '2026-05-05 15:30:00', 920000.00, 'scheduled', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(3, 1, 3, 1, 2, 'ZN1003', '2026-05-06 07:45:00', '2026-05-06 09:45:00', 980000.00, 'scheduled', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(4, 1, 4, 1, 2, 'ZN1004', '2026-05-06 19:00:00', '2026-05-06 21:00:00', 1050000.00, 'scheduled', '2026-05-03 06:15:41', '2026-05-03 06:15:41');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_01_063518_create_personal_access_tokens_table', 1),
(5, '2026_04_01_063538_create_airlines_table', 1),
(6, '2026_04_01_063538_create_airports_table', 1),
(7, '2026_04_01_063538_create_passengers_table', 1),
(8, '2026_04_01_063539_create_airplanes_table', 1),
(9, '2026_04_01_063539_create_flights_table', 1),
(10, '2026_04_01_063539_create_seats_table', 1),
(11, '2026_04_01_063540_create_bookings_table', 1),
(12, '2026_04_01_063541_create_payments_table', 1),
(13, '2026_04_01_063542_create_booking_details_table', 1),
(14, '2026_04_01_063543_create_tickets_table', 1),
(15, '2026_05_02_133500_add_midtrans_fields_to_payments_table', 2),
(16, '2026_05_03_000000_expand_user_roles_and_profile_fields', 3);

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `birth_date` date NOT NULL,
  `passport_number` varchar(255) DEFAULT NULL,
  `identity_number` varchar(255) DEFAULT NULL,
  `nationality` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `passengers`
--

INSERT INTO `passengers` (`id`, `user_id`, `full_name`, `gender`, `birth_date`, `passport_number`, `identity_number`, `nationality`, `created_at`, `updated_at`) VALUES
(2, 3, 'Fauzan Yudistira', 'male', '2026-04-01', '1235532112', '1231344664332', 'WNI', '2026-04-01 02:19:42', '2026-04-01 02:19:42'),
(3, 2, 'Fauzan Yudistira', 'male', '2026-05-03', '02092828', '7272732782', 'Indonesia', '2026-05-03 06:14:44', '2026-05-03 06:14:44'),
(4, 6, 'Ahmad kasim', 'male', '2026-05-03', '929282727', '2872737328', 'Indonesia', '2026-05-03 06:53:39', '2026-05-03 06:53:39');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `transaction_code` varchar(255) DEFAULT NULL,
  `midtrans_order_id` varchar(255) DEFAULT NULL,
  `midtrans_transaction_id` varchar(255) DEFAULT NULL,
  `midtrans_snap_token` text DEFAULT NULL,
  `midtrans_redirect_url` text DEFAULT NULL,
  `midtrans_payment_type` varchar(50) DEFAULT NULL,
  `midtrans_status_code` varchar(20) DEFAULT NULL,
  `midtrans_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`midtrans_payload`)),
  `paid_at` datetime DEFAULT NULL,
  `proof_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `payment_method`, `amount`, `payment_status`, `transaction_code`, `midtrans_order_id`, `midtrans_transaction_id`, `midtrans_snap_token`, `midtrans_redirect_url`, `midtrans_payment_type`, `midtrans_status_code`, `midtrans_payload`, `paid_at`, `proof_file`, `created_at`, `updated_at`) VALUES
(1, 1, 'bank_transfer', 850000.00, 'failed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'payments/WrAlHWW0oEywDSU3zwK51fiB635XThM6RjR9FBVp.png', '2026-04-01 02:29:58', '2026-05-02 00:11:33'),
(2, 1, 'midtrans_snap', 850000.00, 'paid', 'MID-1-2-20260502071133', 'MID-1-2-20260502071133', 'sim-zannora-1777705907', '905d340c-f6e3-4050-a25f-591a7b8b283e', 'https://app.sandbox.midtrans.com/snap/v4/redirection/905d340c-f6e3-4050-a25f-591a7b8b283e', 'qris', '200', '{\"order_id\":\"MID-1-2-20260502071133\",\"transaction_id\":\"sim-zannora-1777705907\",\"payment_type\":\"qris\",\"status_code\":\"200\",\"gross_amount\":\"850000\",\"transaction_status\":\"settlement\",\"fraud_status\":\"accept\",\"signature_key\":\"1b3804349b608d1d9fc4ec79284e8c5225849d57365a5dbec31533db5cd7f66db96abddf10d937ebf385fb63e00c3199c2d965722f8dda3365df1831b4ff79a1\"}', '2026-05-02 07:11:47', NULL, '2026-05-02 00:11:33', '2026-05-02 00:11:47'),
(3, 2, 'unassigned', 850000.00, 'failed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 06:15:00', '2026-05-03 06:15:03'),
(4, 2, 'midtrans_snap', 850000.00, 'failed', 'MID-2-4-20260503131503', 'MID-2-4-20260503131503', '', 'fd2d7341-51e9-49d1-b07f-4ca88a56ff39', 'https://app.sandbox.midtrans.com/snap/v4/redirection/fd2d7341-51e9-49d1-b07f-4ca88a56ff39', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"af6445ac-bdf9-4ff2-b6e2-216468cbdb54\"}', NULL, NULL, '2026-05-03 06:15:03', '2026-05-03 06:24:10'),
(5, 3, 'unassigned', 980000.00, 'failed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 06:24:30', '2026-05-03 06:24:33'),
(6, 3, 'midtrans_snap', 980000.00, 'failed', 'MID-3-6-20260503132433', 'MID-3-6-20260503132433', '', '965f076c-624a-4971-aaed-37b5228725b5', 'https://app.sandbox.midtrans.com/snap/v4/redirection/965f076c-624a-4971-aaed-37b5228725b5', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"a446dcc4-f792-4596-a455-ffe511a770a3\"}', NULL, NULL, '2026-05-03 06:24:33', '2026-05-03 06:30:19'),
(7, 3, 'midtrans_snap', 980000.00, 'failed', 'MID-3-7-20260503133019', 'MID-3-7-20260503133019', '', '3814b82e-0550-4862-88c1-0111b5fff9c7', 'https://app.sandbox.midtrans.com/snap/v4/redirection/3814b82e-0550-4862-88c1-0111b5fff9c7', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"5f500d20-1669-4ef6-94bf-6637e965d1ca\"}', NULL, NULL, '2026-05-03 06:30:19', '2026-05-03 06:35:40'),
(8, 3, 'midtrans_snap', 980000.00, 'failed', 'MID-3-8-20260503133626', 'MID-3-8-20260503133626', '', 'd0bba1d8-50f1-4ff7-ba7e-91b84773cbb5', 'https://app.sandbox.midtrans.com/snap/v4/redirection/d0bba1d8-50f1-4ff7-ba7e-91b84773cbb5', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"514c7bea-1267-4764-b4d7-c45c3a940517\"}', NULL, NULL, '2026-05-03 06:36:26', '2026-05-03 06:42:01'),
(9, 3, 'midtrans_snap', 980000.00, 'failed', 'MID-3-9-20260503134334', 'MID-3-9-20260503134334', '', '91aaf16a-d84e-405c-ad67-14a91963814e', 'https://app.sandbox.midtrans.com/snap/v4/redirection/91aaf16a-d84e-405c-ad67-14a91963814e', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"f8faf18e-6437-4375-bf03-f974f48db0cd\"}', NULL, NULL, '2026-05-03 06:43:34', '2026-05-03 06:46:47'),
(10, 3, 'midtrans_snap', 980000.00, 'paid', 'MID-3-10-20260503134651', 'MID-3-10-20260503134651', 'dbb67719-1dfd-4081-922c-2e845d2dfca2', '42090568-cb42-43d8-a542-cab9f1090a6e', 'https://app.sandbox.midtrans.com/snap/v4/redirection/42090568-cb42-43d8-a542-cab9f1090a6e', 'gopay', '200', '{\"status_code\":\"200\",\"transaction_id\":\"dbb67719-1dfd-4081-922c-2e845d2dfca2\",\"gross_amount\":\"980000.00\",\"currency\":\"IDR\",\"order_id\":\"MID-3-10-20260503134651\",\"payment_type\":\"gopay\",\"signature_key\":\"29b465f094139eb9738d3aad397a0d70e513c35684045a2e773b987fb6222b1a98f4e924df37b519e8f0d1e1788288465d71b0c89d032cf00a8b27c2cfb2ad66\",\"transaction_status\":\"settlement\",\"fraud_status\":\"accept\",\"status_message\":\"Success, transaction is found\",\"merchant_id\":\"M837738569\",\"transaction_time\":\"2026-05-03 20:45:41\",\"settlement_time\":\"2026-05-03 20:45:43\",\"expiry_time\":\"2026-05-03 21:00:41\"}', '2026-05-03 13:47:04', NULL, '2026-05-03 06:46:51', '2026-05-03 06:47:04'),
(11, 4, 'unassigned', 1050000.00, 'failed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 06:54:04', '2026-05-03 06:54:07'),
(12, 4, 'midtrans_snap', 1050000.00, 'failed', 'MID-4-12-20260503135407', 'MID-4-12-20260503135407', NULL, '473712ac-aad3-4d7a-b393-d146655768f6', 'https://app.sandbox.midtrans.com/snap/v4/redirection/473712ac-aad3-4d7a-b393-d146655768f6', NULL, '201', '{\"token\":\"473712ac-aad3-4d7a-b393-d146655768f6\",\"redirect_url\":\"https:\\/\\/app.sandbox.midtrans.com\\/snap\\/v4\\/redirection\\/473712ac-aad3-4d7a-b393-d146655768f6\"}', NULL, NULL, '2026-05-03 06:54:07', '2026-05-03 06:54:27'),
(13, 4, 'midtrans_snap', 1050000.00, 'failed', 'MID-4-13-20260503135427', 'MID-4-13-20260503135427', '', '0f31d703-bb15-44c3-8cde-75883ceda823', 'https://app.sandbox.midtrans.com/snap/v4/redirection/0f31d703-bb15-44c3-8cde-75883ceda823', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"5017a47d-f814-4cc6-88be-d3e9024a4272\"}', NULL, NULL, '2026-05-03 06:54:27', '2026-05-03 06:55:25'),
(14, 4, 'midtrans_snap', 1050000.00, 'pending', 'MID-4-14-20260503135525', 'MID-4-14-20260503135525', '', '93ec8d28-9d56-40bf-b595-c56d5090de98', 'https://app.sandbox.midtrans.com/snap/v4/redirection/93ec8d28-9d56-40bf-b595-c56d5090de98', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"0ee085c6-5f98-488e-8f35-081aa6fa1702\"}', NULL, NULL, '2026-05-03 06:55:25', '2026-05-03 06:55:37'),
(15, 5, 'unassigned', 1050000.00, 'failed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 07:00:03', '2026-05-03 07:00:04'),
(16, 5, 'midtrans_snap', 1050000.00, 'failed', 'MID-5-16-20260503140004', 'MID-5-16-20260503140004', '', '9ac05fd2-6b2b-4fd9-8eb3-18feec32b89b', 'https://app.sandbox.midtrans.com/snap/v4/redirection/9ac05fd2-6b2b-4fd9-8eb3-18feec32b89b', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"8e1dc7d1-b46e-4c8d-bebb-9ca4c5633840\"}', NULL, NULL, '2026-05-03 07:00:04', '2026-05-03 07:08:57'),
(17, 5, 'midtrans_snap', 1050000.00, 'paid', 'MID-5-17-20260503140857', 'MID-5-17-20260503140857', '', '2a97aace-483a-4ce8-85ae-f1008e94f6ff', 'https://app.sandbox.midtrans.com/snap/v4/redirection/2a97aace-483a-4ce8-85ae-f1008e94f6ff', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"8ee9a302-fb7d-440e-a8b2-98b11b861172\"}', '2026-05-03 14:15:32', NULL, '2026-05-03 07:08:57', '2026-05-03 07:21:22'),
(18, 6, 'unassigned', 850000.00, 'failed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 07:15:48', '2026-05-03 07:15:50'),
(19, 6, 'midtrans_snap', 850000.00, 'paid', 'MID-6-19-20260503141550', 'MID-6-19-20260503141550', '', 'e2f3004c-03d3-4cd9-8677-1338a6c2e4ea', 'https://app.sandbox.midtrans.com/snap/v4/redirection/e2f3004c-03d3-4cd9-8677-1338a6c2e4ea', '', '200', '{\"order_id\":\"MID-6-19-20260503141550\",\"status_code\":\"200\",\"transaction_status\":\"settlement\",\"fraud_status\":\"\",\"transaction_id\":\"\",\"payment_type\":\"\"}', '2026-05-03 14:16:20', NULL, '2026-05-03 07:15:50', '2026-05-03 07:16:20'),
(20, 7, 'unassigned', 980000.00, 'failed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 07:26:48', '2026-05-03 07:26:50'),
(21, 7, 'midtrans_snap', 980000.00, 'failed', 'MID-7-21-20260503142650', 'MID-7-21-20260503142650', '', '1eaae8e5-b88a-410f-af88-4e4538b32356', 'https://app.sandbox.midtrans.com/snap/v4/redirection/1eaae8e5-b88a-410f-af88-4e4538b32356', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"483f4f0d-8d8b-47c3-8a50-fd906b2cdc3e\"}', NULL, NULL, '2026-05-03 07:26:50', '2026-05-03 07:58:55'),
(22, 8, 'unassigned', 850000.00, 'failed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 07:36:15', '2026-05-03 07:36:16'),
(23, 8, 'midtrans_snap', 850000.00, 'failed', 'MID-8-23-20260503143616', 'MID-8-23-20260503143616', '', '07ef80c7-ed80-4666-b737-07fb496e951f', 'https://app.sandbox.midtrans.com/snap/v4/redirection/07ef80c7-ed80-4666-b737-07fb496e951f', '', '404', '{\"status_code\":\"404\",\"status_message\":\"Transaction doesn\'t exist.\",\"id\":\"207d090c-335b-4d30-a0e1-03ca3d2c599e\"}', NULL, NULL, '2026-05-03 07:36:16', '2026-05-03 07:58:46'),
(24, 9, 'unassigned', 850000.00, 'failed', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-03 07:59:05', '2026-05-03 07:59:09'),
(25, 9, 'midtrans_snap', 850000.00, 'paid', 'MID-9-25-20260503145909', 'MID-9-25-20260503145909', '', '3b7c5623-ed43-485d-86a5-8fbb573a56ba', 'https://app.sandbox.midtrans.com/snap/v4/redirection/3b7c5623-ed43-485d-86a5-8fbb573a56ba', '', '200', '{\"order_id\":\"MID-9-25-20260503145909\",\"status_code\":\"200\",\"transaction_status\":\"settlement\",\"fraud_status\":\"\",\"transaction_id\":\"\",\"payment_type\":\"\"}', '2026-05-03 14:59:57', NULL, '2026-05-03 07:59:09', '2026-05-03 07:59:57');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(15, 'App\\Models\\User', 6, 'web-portal', 'bbf1a49a4f4eae230165b763252fbc07b9ebe8c463e6b5f05799f98070e00169', '[\"*\"]', NULL, NULL, '2026-05-03 06:53:08', '2026-05-03 06:53:08'),
(19, 'App\\Models\\User', 1, 'web-portal', '6eac93b03335700dadfd71aba45d627b4da2d5074f881f98fe0ec5606574476c', '[\"*\"]', NULL, NULL, '2026-05-03 08:26:52', '2026-05-03 08:26:52');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `airplane_id` bigint(20) UNSIGNED NOT NULL,
  `seat_number` varchar(255) NOT NULL,
  `class` enum('economy','business','first') NOT NULL DEFAULT 'economy',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `airplane_id`, `seat_number`, `class`, `created_at`, `updated_at`) VALUES
(1, 1, 'A1', 'business', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(2, 1, 'A2', 'business', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(3, 1, 'A3', 'economy', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(4, 1, 'A4', 'economy', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(5, 1, 'A5', 'economy', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(6, 1, 'A6', 'economy', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(7, 1, 'A7', 'economy', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(8, 1, 'A8', 'economy', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(9, 1, 'A9', 'economy', '2026-04-01 00:21:07', '2026-04-01 00:21:07'),
(10, 1, 'A10', 'economy', '2026-04-01 00:21:08', '2026-04-01 00:21:08'),
(11, 1, 'A11', 'economy', '2026-04-01 00:21:08', '2026-04-01 00:21:08'),
(12, 1, 'A12', 'economy', '2026-04-01 00:21:08', '2026-04-01 00:21:08'),
(13, 1, '1A', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(14, 1, '1B', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(15, 1, '1C', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(16, 1, '1D', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(17, 1, '1E', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(18, 1, '1F', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(19, 1, '2A', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(20, 1, '2B', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(21, 1, '2C', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(22, 1, '2D', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(23, 1, '2E', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(24, 1, '2F', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(25, 1, '3A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(26, 1, '3B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(27, 1, '3C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(28, 1, '3D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(29, 1, '3E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(30, 1, '3F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(31, 1, '4A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(32, 1, '4B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(33, 1, '4C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(34, 1, '4D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(35, 1, '4E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(36, 1, '4F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(37, 2, '1A', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(38, 2, '1B', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(39, 2, '1C', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(40, 2, '1D', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(41, 2, '1E', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(42, 2, '1F', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(43, 2, '2A', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(44, 2, '2B', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(45, 2, '2C', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(46, 2, '2D', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(47, 2, '2E', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(48, 2, '2F', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(49, 2, '3A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(50, 2, '3B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(51, 2, '3C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(52, 2, '3D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(53, 2, '3E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(54, 2, '3F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(55, 2, '4A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(56, 2, '4B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(57, 2, '4C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(58, 2, '4D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(59, 2, '4E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(60, 2, '4F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(61, 2, '5A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(62, 2, '5B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(63, 2, '5C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(64, 2, '5D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(65, 2, '5E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(66, 2, '5F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(67, 3, '1A', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(68, 3, '1B', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(69, 3, '1C', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(70, 3, '1D', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(71, 3, '1E', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(72, 3, '1F', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(73, 3, '2A', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(74, 3, '2B', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(75, 3, '2C', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(76, 3, '2D', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(77, 3, '2E', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(78, 3, '2F', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(79, 3, '3A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(80, 3, '3B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(81, 3, '3C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(82, 3, '3D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(83, 3, '3E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(84, 3, '3F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(85, 3, '4A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(86, 3, '4B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(87, 3, '4C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(88, 3, '4D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(89, 3, '4E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(90, 3, '4F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(91, 3, '5A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(92, 3, '5B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(93, 3, '5C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(94, 3, '5D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(95, 3, '5E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(96, 3, '5F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(97, 3, '6A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(98, 3, '6B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(99, 3, '6C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(100, 3, '6D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(101, 3, '6E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(102, 3, '6F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(103, 4, '1A', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(104, 4, '1B', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(105, 4, '1C', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(106, 4, '1D', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(107, 4, '1E', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(108, 4, '1F', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(109, 4, '2A', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(110, 4, '2B', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(111, 4, '2C', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(112, 4, '2D', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(113, 4, '2E', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(114, 4, '2F', 'business', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(115, 4, '3A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(116, 4, '3B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(117, 4, '3C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(118, 4, '3D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(119, 4, '3E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(120, 4, '3F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(121, 4, '4A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(122, 4, '4B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(123, 4, '4C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(124, 4, '4D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(125, 4, '4E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(126, 4, '4F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(127, 4, '5A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(128, 4, '5B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(129, 4, '5C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(130, 4, '5D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(131, 4, '5E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(132, 4, '5F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(133, 4, '6A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(134, 4, '6B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(135, 4, '6C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(136, 4, '6D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(137, 4, '6E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(138, 4, '6F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(139, 4, '7A', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(140, 4, '7B', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(141, 4, '7C', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(142, 4, '7D', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(143, 4, '7E', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41'),
(144, 4, '7F', 'economy', '2026-05-03 06:15:41', '2026-05-03 06:15:41');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4NYvEDQVFt8nBNQABEu6aEVJIRhiPg6dsPHpwcYC', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiIzMHBmNW56aUY5amFwUVZKY0VPVVlnSjF4MDN4OGJoRTd0anNsZFRLIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluXC9haXJwbGFuZXMiLCJyb3V0ZSI6ImFkbWluLmFpcnBsYW5lcy5pbmRleCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MSwiYXBpX3Rva2VuIjoiMTl8VGNFUzZ3QTJ4U0Y2dloxa0doZjUyb1p4YWhpUnA1Y1VJM0FjeDRSejlhMWJjZGUzIn0=', 1777822029),
('NR5QFNZRKRAX0Nx9ROzJkL26M7mwGIK0dfb54fOU', 6, '192.168.18.26', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', 'eyJfdG9rZW4iOiJ2MENrdjRNZW1xdEJFbFI4cXFuZnZVWDk2dzFsMXhuSFd5am1Na1dUIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTkyLjE2OC4xOC4yOTo4MDAwXC9teS1ib29raW5ncyIsInJvdXRlIjoibXktYm9va2luZ3MuaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjYsImFwaV90b2tlbiI6IjE1fHZ4MlBCeW9DNER5ZXBSYml3QlRKclVoRE9PbWdYWkxOS1cwWHFYb1gyOTY0ZGY4ZiJ9', 1777816537);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_detail_id` bigint(20) UNSIGNED NOT NULL,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `issued_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`id`, `booking_detail_id`, `qr_code_path`, `pdf_path`, `issued_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'tickets/qr/tk-bk20260401092958295-0001.png', NULL, '2026-05-03 14:23:47', '2026-05-02 00:11:47', '2026-05-03 07:23:48'),
(2, 3, 'tickets/qr/tk-bk20260503132430518-0003.png', NULL, '2026-05-03 14:23:48', '2026-05-03 06:47:04', '2026-05-03 07:23:49'),
(3, 5, 'tickets/qr/tk-bk20260503140003432-0005.png', NULL, '2026-05-03 14:23:49', '2026-05-03 07:15:32', '2026-05-03 07:23:49'),
(4, 6, 'tickets/qr/tk-bk20260503141548900-0006.png', NULL, '2026-05-03 14:23:49', '2026-05-03 07:16:20', '2026-05-03 07:23:50'),
(5, 9, 'tickets/qr/tk-bk20260503145905541-0009.png', NULL, '2026-05-03 14:59:57', '2026-05-03 07:59:57', '2026-05-03 07:59:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','customer','manager') NOT NULL DEFAULT 'customer',
  `employee_id` varchar(50) DEFAULT NULL,
  `department` varchar(120) DEFAULT NULL,
  `job_title` varchar(120) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `phone`, `password`, `role`, `employee_id`, `department`, `job_title`, `last_login_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin@zannora.com', NULL, '08123456789', '$2y$12$ZKBrUZ9jW4zajNqk4QPWve4qbIbVdyJy229XDPN0scRb8RsMSBYhq', 'admin', NULL, NULL, NULL, '2026-05-03 08:26:52', NULL, '2026-04-01 00:21:07', '2026-05-03 08:26:52'),
(2, 'Demo User', 'user@zannora.com', NULL, '08111111111', '$2y$12$7U1V7/LF4tQWiLNAkiwhNeOn9ccyWNyfJaGvLSSfF6gDltT7Gg7ui', 'customer', NULL, NULL, NULL, '2026-05-03 06:58:29', 'Hx59nNNaIDvZxvaBQ7sVfHZLPp1SJF5ZX0bOGibuboyRhOvTyGNKXZPTJ4yx', '2026-04-01 00:21:07', '2026-05-03 06:58:29'),
(3, 'Fauzan Yudistira', 'user01@zannora.com', NULL, '08882017549', '$2y$12$T9izrkfT1oY/JUgMLdNNBOg.arsdGkrTRrr90SB/PdyeZEIgoUrby', 'customer', NULL, NULL, NULL, NULL, 'cY4Tk6DwjT4pnykT85axGtekfUe3G5GWgsK5wXd6k1jTlF0DIJpNhZxZ6bXH', '2026-04-01 01:07:20', '2026-04-01 01:07:20'),
(4, 'Operations Staff', 'staff@zannora.com', NULL, '08123456780', '$2y$12$i1xFXPabjY1kEHODX.JYC.O5R/QcTkTQXfesVs/VIT/E3Cfkb98q2', 'staff', NULL, NULL, NULL, '2026-05-03 08:11:13', 'e31RVOUgQEIt8mcRudt7ydLEXSkOGOSWNXdBPRkfE6ZchuufIafogwEiULXI', '2026-05-03 06:06:54', '2026-05-03 08:11:13'),
(5, 'Operations Manager', 'manager@zannora.com', NULL, '08123456781', '$2y$12$5bLqzieKiMhqoH/KMsP1DeimsQ/b7qkmE16KB8H7sxR.9pbs1nx3.', 'manager', NULL, NULL, NULL, '2026-05-03 08:26:07', NULL, '2026-05-03 06:06:55', '2026-05-03 08:26:07'),
(6, 'Ahmad Sobri', 'fauzanyudistira10@gmail.com', NULL, '08882017549', '$2y$12$TY2MesQnsSxlhJr50jUrN.TuJT1VL2VHUMjwbUHXt8w7khQS9hNLe', 'customer', NULL, NULL, NULL, '2026-05-03 06:53:08', 'Z9S7uldlQUcZWNTYoMgVb0EyHDR3cIofrMYDlBO0chXzgPbEcbreWRAW9uCR', '2026-05-03 06:52:46', '2026-05-03 06:53:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `airlines`
--
ALTER TABLE `airlines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `airlines_code_unique` (`code`);

--
-- Indexes for table `airplanes`
--
ALTER TABLE `airplanes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `airplanes_registration_number_unique` (`registration_number`),
  ADD KEY `airplanes_airline_id_foreign` (`airline_id`);

--
-- Indexes for table `airports`
--
ALTER TABLE `airports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `airports_code_unique` (`code`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookings_booking_code_unique` (`booking_code`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_flight_id_foreign` (`flight_id`);

--
-- Indexes for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_details_booking_id_passenger_id_unique` (`booking_id`,`passenger_id`),
  ADD KEY `booking_details_passenger_id_foreign` (`passenger_id`),
  ADD KEY `booking_details_seat_id_foreign` (`seat_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `flights`
--
ALTER TABLE `flights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `flights_flight_number_unique` (`flight_number`),
  ADD KEY `flights_airline_id_foreign` (`airline_id`),
  ADD KEY `flights_airplane_id_foreign` (`airplane_id`),
  ADD KEY `flights_departure_airport_id_foreign` (`departure_airport_id`),
  ADD KEY `flights_arrival_airport_id_foreign` (`arrival_airport_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `passengers_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_booking_id_foreign` (`booking_id`),
  ADD KEY `payments_midtrans_order_id_index` (`midtrans_order_id`),
  ADD KEY `payments_midtrans_transaction_id_index` (`midtrans_transaction_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `seats_airplane_id_seat_number_unique` (`airplane_id`,`seat_number`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tickets_booking_detail_id_unique` (`booking_detail_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_employee_id_unique` (`employee_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `airlines`
--
ALTER TABLE `airlines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `airplanes`
--
ALTER TABLE `airplanes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `airports`
--
ALTER TABLE `airports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flights`
--
ALTER TABLE `flights`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `airplanes`
--
ALTER TABLE `airplanes`
  ADD CONSTRAINT `airplanes_airline_id_foreign` FOREIGN KEY (`airline_id`) REFERENCES `airlines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_flight_id_foreign` FOREIGN KEY (`flight_id`) REFERENCES `flights` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_details_passenger_id_foreign` FOREIGN KEY (`passenger_id`) REFERENCES `passengers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_details_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `flights`
--
ALTER TABLE `flights`
  ADD CONSTRAINT `flights_airline_id_foreign` FOREIGN KEY (`airline_id`) REFERENCES `airlines` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `flights_airplane_id_foreign` FOREIGN KEY (`airplane_id`) REFERENCES `airplanes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `flights_arrival_airport_id_foreign` FOREIGN KEY (`arrival_airport_id`) REFERENCES `airports` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `flights_departure_airport_id_foreign` FOREIGN KEY (`departure_airport_id`) REFERENCES `airports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `passengers`
--
ALTER TABLE `passengers`
  ADD CONSTRAINT `passengers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_airplane_id_foreign` FOREIGN KEY (`airplane_id`) REFERENCES `airplanes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_booking_detail_id_foreign` FOREIGN KEY (`booking_detail_id`) REFERENCES `booking_details` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
