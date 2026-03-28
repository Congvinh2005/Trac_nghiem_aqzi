--
-- Database: `vinhzota`
-- Hệ thống quản lý bài tập trực tuyến
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Database creation
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `vinhzota` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vinhzota`;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------

CREATE TABLE `users` (
  `ma_user` VARCHAR(20) NOT NULL,
  `ten_user` VARCHAR(55) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(55) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `school` VARCHAR(255) DEFAULT NULL,
  `avatar` TEXT DEFAULT NULL,
  `phan_quyen` ENUM('teacher', 'student') NOT NULL,
  `ngay_tao` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ma_user`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `de_thi`
-- --------------------------------------------------------

CREATE TABLE `de_thi` (
  `ma_de` VARCHAR(20) NOT NULL,
  `ma_giao_vien` VARCHAR(20) NOT NULL,
  `ten_de` VARCHAR(255) NOT NULL,
  `ma_code` VARCHAR(10) NOT NULL,
  `thoi_gian_nap` DATETIME DEFAULT NULL,
  `cho_xem_ket_qua` TINYINT(1) DEFAULT 1,
  `yeu_cau_dang_nhap` TINYINT(1) DEFAULT 0,
  `trang_thai` ENUM('active', 'inactive') DEFAULT 'active',
  `ngay_tao` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ma_de`),
  UNIQUE KEY `ma_code` (`ma_code`),
  KEY `ma_giao_vien` (`ma_giao_vien`),
  CONSTRAINT `de_thi_ibfk_1` FOREIGN KEY (`ma_giao_vien`) REFERENCES `users` (`ma_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `cau_hoi`
-- --------------------------------------------------------

CREATE TABLE `cau_hoi` (
  `ma_cau_hoi` INT(11) NOT NULL AUTO_INCREMENT,
  `ma_de` VARCHAR(20) NOT NULL,
  `noi_dung` TEXT NOT NULL,
  `hinh_anh` TEXT DEFAULT NULL,
  `thu_tu` INT(11) DEFAULT 0,
  PRIMARY KEY (`ma_cau_hoi`),
  KEY `ma_de` (`ma_de`),
  CONSTRAINT `cau_hoi_ibfk_1` FOREIGN KEY (`ma_de`) REFERENCES `de_thi` (`ma_de`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `dap_an`
-- --------------------------------------------------------

CREATE TABLE `dap_an` (
  `ma_dap_an` INT(11) NOT NULL AUTO_INCREMENT,
  `ma_cau_hoi` INT(11) NOT NULL,
  `noi_dung` TEXT NOT NULL,
  `ky_tu` CHAR(1) NOT NULL,
  `la_dung` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`ma_dap_an`),
  KEY `ma_cau_hoi` (`ma_cau_hoi`),
  CONSTRAINT `dap_an_ibfk_1` FOREIGN KEY (`ma_cau_hoi`) REFERENCES `cau_hoi` (`ma_cau_hoi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for table `bai_lam`
-- --------------------------------------------------------

CREATE TABLE `bai_lam` (
  `ma_bai_lam` VARCHAR(20) NOT NULL,
  `ma_de` VARCHAR(20) NOT NULL,
  `ma_hoc_sinh` VARCHAR(20) DEFAULT NULL,
  `ten_hoc_sinh` VARCHAR(255) DEFAULT NULL,
  `email` VARCHAR(55) DEFAULT NULL,
  `danh_sach_dap_an` TEXT DEFAULT NULL,
  `diem` DECIMAL(5,2) DEFAULT 0.00,
  `thoi_gian_nop` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ma_bai_lam`),
  KEY `ma_de` (`ma_de`),
  KEY `ma_hoc_sinh` (`ma_hoc_sinh`),
  CONSTRAINT `bai_lam_ibfk_1` FOREIGN KEY (`ma_de`) REFERENCES `de_thi` (`ma_de`) ON DELETE CASCADE,
  CONSTRAINT `bai_lam_ibfk_2` FOREIGN KEY (`ma_hoc_sinh`) REFERENCES `users` (`ma_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Sample data for table `users`
-- --------------------------------------------------------

INSERT INTO `users` (`ma_user`, `ten_user`, `password`, `full_name`, `email`, `phone`, `school`, `phan_quyen`) VALUES
('GV001', 'giaovien', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Giáo Viên Demo', 'giaovien@vinhzota.edu.vn', '0901111111', 'Trường THPT Chuyên Vinh', 'teacher'),
('HS001', 'hocsinh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh Demo', 'hocsinh@vinhzota.edu.vn', '0902222222', 'Trường THPT Chuyên Vinh', 'student');

-- Password for both users: 'password123'
-- Hash generated using PHP password_hash('password123', PASSWORD_DEFAULT)

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
