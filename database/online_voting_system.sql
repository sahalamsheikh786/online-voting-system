-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2026 at 08:24 AM
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
-- Database: `online_voting_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_profiles`
--

CREATE TABLE `admin_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `age` int(10) UNSIGNED NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_profiles`
--

INSERT INTO `admin_profiles` (`id`, `user_id`, `age`, `contact_number`, `created_at`, `updated_at`) VALUES
(1, 1, 35, '9800000000', '2026-05-26 18:40:22', '2026-05-26 18:40:22');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `district_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `logged_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `district_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `party` varchar(255) DEFAULT NULL,
  `age` int(10) UNSIGNED NOT NULL,
  `position` varchar(255) NOT NULL DEFAULT 'District Representative',
  `image_path` varchar(255) DEFAULT NULL,
  `vision_path` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `district_id`, `name`, `party`, `age`, `position`, `image_path`, `vision_path`, `email`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Alexys Makwanpur', 'Independent', 47, 'President', NULL, NULL, 'candidate10@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(2, 1, 'Edgardo Makwanpur', 'Unity Party', 67, 'President', NULL, NULL, 'candidate20@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(3, 1, 'Bartholome Makwanpur', 'Independent', 60, 'Vice President', NULL, NULL, 'candidate30@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(4, 1, 'Kamryn Makwanpur', 'Forward Nepal', 53, 'Vice President', NULL, NULL, 'candidate40@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(5, 2, 'Alexandro Kathmandu', 'Citizen Forum', 36, 'President', NULL, NULL, 'candidate11@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(6, 2, 'Hudson Kathmandu', 'Citizen Forum', 58, 'President', NULL, NULL, 'candidate21@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(7, 2, 'Annette Kathmandu', 'Citizen Forum', 39, 'Vice President', NULL, NULL, 'candidate31@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(8, 2, 'Johnathon Kathmandu', 'Unity Party', 35, 'Vice President', NULL, NULL, 'candidate41@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(9, 3, 'Rollin Lalitpur', 'Unity Party', 63, 'President', NULL, NULL, 'candidate12@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(10, 3, 'Lilyan Lalitpur', 'Citizen Forum', 56, 'President', NULL, NULL, 'candidate22@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(11, 3, 'Murphy Lalitpur', 'Unity Party', 45, 'Vice President', NULL, NULL, 'candidate32@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(12, 3, 'Jerry Lalitpur', 'Forward Nepal', 48, 'Vice President', NULL, NULL, 'candidate42@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(13, 4, 'Alba Bhaktapur', 'Citizen Forum', 67, 'President', NULL, NULL, 'candidate13@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(14, 4, 'Hilbert Bhaktapur', 'Independent', 42, 'President', NULL, NULL, 'candidate23@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(15, 4, 'Lottie Bhaktapur', 'Independent', 47, 'Vice President', NULL, NULL, 'candidate33@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(16, 4, 'Lavon Bhaktapur', 'Forward Nepal', 35, 'Vice President', NULL, NULL, 'candidate43@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(17, 5, 'Merle Chitwan', 'Citizen Forum', 49, 'President', NULL, NULL, 'candidate14@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(18, 5, 'Eladio Chitwan', 'Unity Party', 46, 'President', NULL, NULL, 'candidate24@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(19, 5, 'Luther Chitwan', 'Citizen Forum', 44, 'Vice President', NULL, NULL, 'candidate34@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(20, 5, 'Keven Chitwan', 'Independent', 37, 'Vice President', NULL, NULL, 'candidate44@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(21, 6, 'Lukas Pokhara', 'Forward Nepal', 68, 'President', NULL, NULL, 'candidate15@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(22, 6, 'Lance Pokhara', 'Independent', 43, 'President', NULL, NULL, 'candidate25@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(23, 6, 'Bethel Pokhara', 'Independent', 43, 'Vice President', NULL, NULL, 'candidate35@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(24, 6, 'Sallie Pokhara', 'Citizen Forum', 37, 'Vice President', NULL, NULL, 'candidate45@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(25, 7, 'Geo Biratnagar', 'Citizen Forum', 53, 'President', NULL, NULL, 'candidate16@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(26, 7, 'Ocie Biratnagar', 'Unity Party', 59, 'President', NULL, NULL, 'candidate26@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(27, 7, 'May Biratnagar', 'Citizen Forum', 51, 'Vice President', NULL, NULL, 'candidate36@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(28, 7, 'Dewayne Biratnagar', 'Forward Nepal', 47, 'Vice President', NULL, NULL, 'candidate46@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(29, 8, 'Maci Dharan', 'Independent', 57, 'President', NULL, NULL, 'candidate17@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(30, 8, 'Lilly Dharan', 'Forward Nepal', 59, 'President', NULL, NULL, 'candidate27@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(31, 8, 'Katelin Dharan', 'Citizen Forum', 62, 'Vice President', NULL, NULL, 'candidate37@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(32, 8, 'Rudolph Dharan', 'Unity Party', 63, 'Vice President', NULL, NULL, 'candidate47@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(33, 9, 'Reed Hetauda', 'Independent', 41, 'President', NULL, NULL, 'candidate18@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(34, 9, 'Filiberto Hetauda', 'Forward Nepal', 57, 'President', NULL, NULL, 'candidate28@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(35, 9, 'Sydnee Hetauda', 'Citizen Forum', 51, 'Vice President', NULL, NULL, 'candidate38@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(36, 9, 'Drew Hetauda', 'Forward Nepal', 56, 'Vice President', NULL, NULL, 'candidate48@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(37, 10, 'Neal Butwal', 'Citizen Forum', 65, 'President', NULL, NULL, 'candidate19@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(38, 10, 'Jaquelin Butwal', 'Independent', 46, 'President', NULL, NULL, 'candidate29@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(39, 10, 'Alexandre Butwal', 'Unity Party', 39, 'Vice President', NULL, NULL, 'candidate39@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(40, 10, 'Jamil Butwal', 'Unity Party', 61, 'Vice President', NULL, NULL, 'candidate49@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(41, 11, 'Madeline Nepalgunj', 'Citizen Forum', 67, 'President', NULL, NULL, 'candidate110@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(42, 11, 'Mellie Nepalgunj', 'Unity Party', 44, 'President', NULL, NULL, 'candidate210@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(43, 11, 'Prince Nepalgunj', 'Citizen Forum', 57, 'Vice President', NULL, NULL, 'candidate310@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(44, 11, 'Deon Nepalgunj', 'Independent', 68, 'Vice President', NULL, NULL, 'candidate410@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(45, 12, 'Alex Dhangadhi', 'Independent', 39, 'President', NULL, NULL, 'candidate111@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(46, 12, 'Richie Dhangadhi', 'Citizen Forum', 49, 'President', NULL, NULL, 'candidate211@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(47, 12, 'Casimir Dhangadhi', 'Forward Nepal', 39, 'Vice President', NULL, NULL, 'candidate311@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(48, 12, 'Macie Dhangadhi', 'Independent', 42, 'Vice President', NULL, NULL, 'candidate411@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(49, 13, 'Fredrick Janakpur', 'Citizen Forum', 67, 'President', NULL, NULL, 'candidate112@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(50, 13, 'Emmanuel Janakpur', 'Citizen Forum', 45, 'President', NULL, NULL, 'candidate212@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(51, 13, 'Norberto Janakpur', 'Forward Nepal', 60, 'Vice President', NULL, NULL, 'candidate312@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(52, 13, 'Victoria Janakpur', 'Unity Party', 36, 'Vice President', NULL, NULL, 'candidate412@example.com', 1, '2026-05-26 18:40:23', '2026-05-26 18:40:23');

-- --------------------------------------------------------

--
-- Table structure for table `deleted_candidates`
--

CREATE TABLE `deleted_candidates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `election_archive_id` bigint(20) UNSIGNED DEFAULT NULL,
  `original_candidate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `district_name` varchar(255) NOT NULL,
  `candidate_name` varchar(255) NOT NULL,
  `party` varchar(255) DEFAULT NULL,
  `age` int(10) UNSIGNED DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `vision_path` varchar(255) DEFAULT NULL,
  `vote_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `deleted_reason` varchar(255) NOT NULL DEFAULT 'candidate_deleted',
  `election_started_at` timestamp NULL DEFAULT NULL,
  `election_ended_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `restored_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `name`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Makwanpur', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(2, 'Kathmandu', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(3, 'Lalitpur', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(4, 'Bhaktapur', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(5, 'Chitwan', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(6, 'Pokhara', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(7, 'Biratnagar', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(8, 'Dharan', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(9, 'Hetauda', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(10, 'Butwal', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(11, 'Nepalgunj', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(12, 'Dhangadhi', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(13, 'Janakpur', 1, '2026-05-26 18:40:22', '2026-05-26 18:40:22');

-- --------------------------------------------------------

--
-- Table structure for table `election_archives`
--

CREATE TABLE `election_archives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `district_name` varchar(255) NOT NULL,
  `election_title` varchar(255) DEFAULT NULL,
  `archive_reason` varchar(255) NOT NULL DEFAULT 'deleted',
  `candidate_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_votes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `election_started_at` timestamp NULL DEFAULT NULL,
  `election_ended_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `restored_at` timestamp NULL DEFAULT NULL,
  `winners` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`winners`)),
  `position_results` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`position_results`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `election_settings`
--

CREATE TABLE `election_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `district_id` bigint(20) UNSIGNED DEFAULT NULL,
  `election_title` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `started_at` timestamp NULL DEFAULT NULL,
  `paused_at` timestamp NULL DEFAULT NULL,
  `remaining_seconds` int(10) UNSIGNED DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `election_settings`
--

INSERT INTO `election_settings` (`id`, `district_id`, `election_title`, `is_active`, `started_at`, `paused_at`, `remaining_seconds`, `ends_at`, `ended_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(2, 2, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(3, 3, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(4, 4, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(5, 5, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(6, 6, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(7, 7, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(8, 8, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(9, 9, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(10, 10, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(11, 11, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(12, 12, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40'),
(13, 13, NULL, 0, NULL, NULL, NULL, '2026-05-28 18:40:24', NULL, '2026-05-26 18:40:24', '2026-05-26 18:42:40');

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
(4, '2026_05_11_115751_create_districts_table', 1),
(5, '2026_05_11_115752_create_candidates_table', 1),
(6, '2026_05_11_115753_create_admin_profiles_table', 1),
(7, '2026_05_11_115754_create_election_settings_table', 1),
(8, '2026_05_11_115754_create_votes_table', 1),
(9, '2026_05_11_123000_add_pattern_lock_to_users_table', 1),
(10, '2026_05_12_090000_update_votes_for_multi_position_ballot', 1),
(11, '2026_05_14_090000_add_district_id_to_election_settings_table', 1),
(12, '2026_05_15_000000_upgrade_election_lifecycle_and_archives', 1),
(13, '2026_05_15_010000_add_pause_fields_to_election_settings', 1),
(14, '2026_05_15_020000_add_restored_at_to_archives', 1),
(15, '2026_05_15_030000_add_last_known_district_to_users', 1),
(16, '2026_05_21_000001_create_audit_logs_table', 1),
(17, '2026_05_21_000002_add_party_to_candidates_and_deleted_candidates', 1),
(18, '2026_05_22_000001_add_unique_index_to_users_citizenship_number', 1),
(19, '2026_05_23_000000_add_election_title_to_election_settings_and_archives', 1);

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
('dJ225UANt3Wpd9KszsMvVPFbxOmt8wjrokqyXZZj', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.121.0 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQTBwc0JCWlVtOUxxM1F4MFNIYTQyZmRIRUNESlprMGVmRzRvRHpMciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779820936),
('QQs4nE5ZFvRCSGN5m8bNOT7UDOvNHhpPdCzwKAou', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUk9Zbk5zMDhPSGR2UDh0UGFQWHJZTWU5ZlM5ek5hT0wzd2M5aUY1NiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jYW5kaWRhdGVzP3BhZ2U9NSI7czo1OiJyb3V0ZSI7czoxNjoiY2FuZGlkYXRlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1779820972),
('RMPFCCjpWX67EfNLPuyBwmEHhjS0foYYwx0LKtcv', NULL, '::1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.7462', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWDgwOWlZeVZwYTNtZnhDRGRvaGZFakV6SkxoSDRUZGlFYWpVUkpnYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDg6Imh0dHA6Ly9sb2NhbGhvc3QvT25saW5lJTIwVm90aW5nJTIwU3lzdGVtL3B1YmxpYyI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1779820837);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pattern_lock` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `date_of_birth` date DEFAULT NULL,
  `district_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_known_district_name` varchar(255) DEFAULT NULL,
  `citizenship_number` varchar(255) DEFAULT NULL,
  `voter_id_number` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `rejection_message` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `has_voted_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `contact_number`, `password`, `pattern_lock`, `role`, `status`, `date_of_birth`, `district_id`, `last_known_district_name`, `citizenship_number`, `voter_id_number`, `image_path`, `rejection_message`, `approved_at`, `has_voted_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'System Admin', '9800000000', '$2y$12$AsgfsqDBEvxAjnho0UAaJu9jsNZK8lgkpl0awJKe52ouM2FaBiNIS', '$2y$12$ixzw.ZjEQ4.bTB7YckHB0.eDH0sTzU0i4eLLEAnQCGD.Jy41gtu6C', 'admin', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-26 18:40:22', NULL, NULL, '2026-05-26 18:40:22', '2026-05-26 18:40:22'),
(2, 'Approved User', '9811111111', '$2y$12$LzR6UbWXybF1/aWpuI/VVeQ4IWbB.giAVzhp2P.WCXoySb2dRMC16', '$2y$12$c19xsKYiDi65Quhae3d4V.TXTWJrddLAfoHxV8dCnZ0bJa3xyUUii', 'user', 'approved', '1998-04-17', 1, NULL, '12345-67890', '100200300', NULL, NULL, '2026-05-26 18:40:23', NULL, NULL, '2026-05-26 18:40:23', '2026-05-26 18:40:23'),
(3, 'Pending User', '9822222222', '$2y$12$tLuVV7qrcW5D1RJRv32NbO/sweZnznNVMWMGThzqYjOnktKupIAU6', '$2y$12$OF5K6MiOzZ44P..qPcw16.Kj4FtHrwZfJ008u1VZZ/b5JIPWJ.2Lq', 'user', 'pending', '2000-08-10', 2, NULL, '55555/22222', '100200301', NULL, NULL, NULL, NULL, NULL, '2026-05-26 18:40:24', '2026-05-26 18:40:24'),
(4, 'Rejected User', '9833333333', '$2y$12$OwZYLKc9o05ZOrXcQfWGruRfK8dXQ/iNW1t/0dMjMfsr9Ln4pmMAa', '$2y$12$bb2wOfbebFptXMMdCWdoauoIc3elgOFUjYJZwJ0Y8kst1sWvc6xDq', 'user', 'rejected', '2001-01-19', 3, NULL, '99999-11111', '100200302', NULL, 'You can try once again', NULL, NULL, NULL, '2026-05-26 18:40:24', '2026-05-26 18:40:24');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `district_id` bigint(20) UNSIGNED NOT NULL,
  `candidate_id` bigint(20) UNSIGNED NOT NULL,
  `position` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_profiles`
--
ALTER TABLE `admin_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`),
  ADD KEY `audit_logs_district_id_foreign` (`district_id`),
  ADD KEY `audit_logs_logged_at_index` (`logged_at`);

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
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `candidates_email_unique` (`email`),
  ADD KEY `candidates_district_id_foreign` (`district_id`);

--
-- Indexes for table `deleted_candidates`
--
ALTER TABLE `deleted_candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deleted_candidates_election_archive_id_foreign` (`election_archive_id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `districts_name_unique` (`name`);

--
-- Indexes for table `election_archives`
--
ALTER TABLE `election_archives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `election_settings`
--
ALTER TABLE `election_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_settings_district_id_foreign` (`district_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_contact_number_unique` (`contact_number`),
  ADD UNIQUE KEY `users_voter_id_number_unique` (`voter_id_number`),
  ADD UNIQUE KEY `users_citizenship_number_unique` (`citizenship_number`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `votes_user_id_position_unique` (`user_id`,`position`),
  ADD KEY `votes_district_id_foreign` (`district_id`),
  ADD KEY `votes_candidate_id_foreign` (`candidate_id`),
  ADD KEY `votes_user_id_index` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_profiles`
--
ALTER TABLE `admin_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `deleted_candidates`
--
ALTER TABLE `deleted_candidates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `election_archives`
--
ALTER TABLE `election_archives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `election_settings`
--
ALTER TABLE `election_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_profiles`
--
ALTER TABLE `admin_profiles`
  ADD CONSTRAINT `admin_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deleted_candidates`
--
ALTER TABLE `deleted_candidates`
  ADD CONSTRAINT `deleted_candidates_election_archive_id_foreign` FOREIGN KEY (`election_archive_id`) REFERENCES `election_archives` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `election_settings`
--
ALTER TABLE `election_settings`
  ADD CONSTRAINT `election_settings_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_district_id_foreign` FOREIGN KEY (`district_id`) REFERENCES `districts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
