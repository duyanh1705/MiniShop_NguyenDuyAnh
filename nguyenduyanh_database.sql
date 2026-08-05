-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th8 05, 2026 lúc 09:59 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `nguyenduyanh_database`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `brandname` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `brands`
--

INSERT INTO `brands` (`id`, `brandname`, `slug`, `image`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Logitech', 'logitech', NULL, NULL, 1, '2026-08-05 14:55:09', '2026-08-05 14:55:09'),
(2, 'Razer', 'razer', NULL, NULL, 1, '2026-08-05 14:55:09', '2026-08-05 14:55:09'),
(3, 'Corsair', 'corsair', NULL, NULL, 1, '2026-08-05 14:55:09', '2026-08-05 14:55:09'),
(4, 'ASUS', 'asus', NULL, NULL, 1, '2026-08-05 14:55:09', '2026-08-05 14:55:09'),
(5, 'SteelSeries', 'steelseries', NULL, NULL, 1, '2026-08-05 14:55:09', '2026-08-05 14:55:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `catename` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `catename`, `slug`, `image`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Chuột Gaming', 'chuot-gaming', NULL, NULL, 1, '2026-08-05 14:54:43', '2026-08-05 14:54:43'),
(2, 'Bàn phím Cơ', 'ban-phim-co', NULL, NULL, 1, '2026-08-05 14:54:43', '2026-08-05 14:54:43'),
(3, 'Tai nghe', 'tai-nghe', NULL, NULL, 1, '2026-08-05 14:54:43', '2026-08-05 14:54:43'),
(4, 'Màn hình', 'man-hinh', NULL, NULL, 1, '2026-08-05 14:54:43', '2026-08-05 14:54:43'),
(5, 'Lót chuột', 'lot-chuot', NULL, NULL, 1, '2026-08-05 14:54:43', '2026-08-05 14:54:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `customers`
--

INSERT INTO `customers` (`id`, `fullname`, `phone`, `email`, `address`, `note`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn A', '0901234567', 'nguyenvana@gmail.com', 'Hà Nội', NULL, 1, '2026-08-05 14:56:29', '2026-08-05 14:56:29'),
(2, 'Trần Thị B', '0912345678', 'tranthib@gmail.com', 'TP.HCM', NULL, 1, '2026-08-05 14:56:29', '2026-08-05 14:56:29'),
(3, 'Lê Văn C', '0923456789', 'levanc@gmail.com', 'Đà Nẵng', NULL, 1, '2026-08-05 14:56:29', '2026-08-05 14:56:29'),
(4, 'Phạm Thị D', '0934567890', 'phamthid@gmail.com', 'Cần Thơ', NULL, 1, '2026-08-05 14:56:29', '2026-08-05 14:56:29'),
(5, 'Hoàng Văn E', '0945678901', 'hoangvane@gmail.com', 'Hải Phòng', NULL, 1, '2026-08-05 14:56:29', '2026-08-05 14:56:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_code` varchar(30) NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `note` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `user_id`, `order_code`, `total_amount`, `note`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'DH001', 350000.00, NULL, 0, '2026-08-05 14:56:51', '2026-08-05 14:56:51'),
(2, 2, 2, 'DH002', 2100000.00, NULL, 1, '2026-08-05 14:56:51', '2026-08-05 14:56:51'),
(3, 3, 2, 'DH003', 950000.00, NULL, 1, '2026-08-05 14:56:51', '2026-08-05 14:56:51'),
(4, 4, 3, 'DH004', 4100000.00, NULL, 2, '2026-08-05 14:56:51', '2026-08-05 14:56:51'),
(5, 5, 1, 'DH005', 300000.00, NULL, 0, '2026-08-05 14:56:51', '2026-08-05 14:56:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`, `created_at`) VALUES
(1, 1, 1, 1, 350000.00, 350000.00, '2026-08-05 14:57:11'),
(2, 2, 2, 1, 2100000.00, 2100000.00, '2026-08-05 14:57:11'),
(3, 3, 3, 1, 950000.00, 950000.00, '2026-08-05 14:57:11'),
(4, 4, 4, 1, 4100000.00, 4100000.00, '2026-08-05 14:57:11'),
(5, 5, 5, 1, 300000.00, 300000.00, '2026-08-05 14:57:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `proname` varchar(200) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `discount_price` decimal(10,0) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `proname`, `slug`, `price`, `discount_price`, `quantity`, `image`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Chuột Logitech G102', 'chuot-logitech-g102', 400000, 350000, 50, NULL, NULL, 1, '2026-08-05 14:55:39', '2026-08-05 14:55:39'),
(2, 2, 2, 'Bàn phím Razer BlackWidow', 'ban-phim-razer-blackwidow', 2500000, 2100000, 20, NULL, NULL, 1, '2026-08-05 14:55:39', '2026-08-05 14:55:39'),
(3, 3, 3, 'Tai nghe Corsair HS35', 'tai-nghe-corsair-hs35', 1100000, 950000, 30, NULL, NULL, 1, '2026-08-05 14:55:39', '2026-08-05 14:55:39'),
(4, 4, 4, 'Màn hình ASUS TUF VG249Q', 'man-hinh-asus-tuf-vg249q', 4500000, 4100000, 15, NULL, NULL, 1, '2026-08-05 14:55:39', '2026-08-05 14:55:39'),
(5, 5, 5, 'Lót chuột SteelSeries QCK', 'lot-chuot-steelseries-qck', 350000, 300000, 100, NULL, NULL, 1, '2026-08-05 14:55:39', '2026-08-05 14:55:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`, `email`, `phone`, `address`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn Admin', 'admin', '123456', 'admin@gmail.com', NULL, NULL, 1, 1, '2026-08-05 14:56:06', '2026-08-05 14:56:06'),
(2, 'Trần Văn Nhân Viên', 'staff1', '123456', 'staff1@gmail.com', NULL, NULL, 0, 1, '2026-08-05 14:56:06', '2026-08-05 14:56:06'),
(3, 'Lê Thị Thu Ngân', 'staff2', '123456', 'staff2@gmail.com', NULL, NULL, 0, 1, '2026-08-05 14:56:06', '2026-08-05 14:56:06'),
(4, 'Phạm Hoàng Kho', 'staff3', '123456', 'staff3@gmail.com', NULL, NULL, 0, 1, '2026-08-05 14:56:06', '2026-08-05 14:56:06'),
(5, 'Đặng Văn Quản Lý', 'manager', '123456', 'manager@gmail.com', NULL, NULL, 1, 1, '2026-08-05 14:56:06', '2026-08-05 14:56:06');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
