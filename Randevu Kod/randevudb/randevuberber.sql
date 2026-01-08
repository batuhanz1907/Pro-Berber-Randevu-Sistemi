-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 22 Ağu 2025, 11:34:54
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `randevuberber`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ayarlar`
--

CREATE TABLE `ayarlar` (
  `id` int(11) NOT NULL,
  `randevu_acik` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `ayarlar`
--

INSERT INTO `ayarlar` (`id`, `randevu_acik`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `mesajlar`
--

CREATE TABLE `mesajlar` (
  `id` int(11) NOT NULL,
  `adsoyad` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mesaj` text NOT NULL,
  `tarih` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `mesajlar`
--

INSERT INTO `mesajlar` (`id`, `adsoyad`, `email`, `mesaj`, `tarih`) VALUES
(1, 'sdasda asfasf', 'sdasfasfasf@30.com', 'sdaglksdagl', '2025-04-20 17:12:51'),
(2, 'gdsag ', '124214@30.com', 'dfsgdsfhsfdhsdfh', '2025-04-23 14:13:39');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `randevular`
--

CREATE TABLE `randevular` (
  `id` int(11) NOT NULL,
  `adsoyad` varchar(100) NOT NULL COMMENT 'Adı soyadı',
  `telefon` varchar(20) NOT NULL COMMENT 'telefon numarası',
  `randevu_saati` time NOT NULL,
  `alindigi_tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `geldi` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `randevular`
--

INSERT INTO `randevular` (`id`, `adsoyad`, `telefon`, `randevu_saati`, `alindigi_tarih`, `geldi`) VALUES
(16, 'dfh hfds', '1235125', '15:20:00', '2025-08-19 11:45:20', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `randevusistem`
--

CREATE TABLE `randevusistem` (
  `id` int(11) NOT NULL,
  `sistem_durum` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `randevu_gecmis`
--

CREATE TABLE `randevu_gecmis` (
  `id` int(11) NOT NULL,
  `adsoyad` varchar(100) NOT NULL,
  `telefon` varchar(20) NOT NULL,
  `randevu_saati` datetime NOT NULL,
  `alindigi_tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `geldi` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `randevu_gecmis`
--

INSERT INTO `randevu_gecmis` (`id`, `adsoyad`, `telefon`, `randevu_saati`, `alindigi_tarih`, `geldi`) VALUES
(2, 'fg fdh', '463346', '2025-04-28 17:20:00', '2025-04-28 11:01:10', 0),
(3, 'hdsf dfh', '436346', '2025-04-28 16:40:00', '2025-04-28 11:02:01', 0),
(4, 'dsga gsda', '463346', '2025-04-29 17:20:00', '2025-04-29 12:19:34', 1),
(5, 'fdsg dfsh', '57584', '2025-04-29 18:00:00', '2025-04-29 12:19:38', 1),
(6, 'dfsagasdg adsfgasdgh', '57457', '2025-05-02 12:40:00', '2025-05-02 08:00:05', 0),
(7, 'gasdf asdg', '346347', '2025-05-14 18:00:00', '2025-05-14 10:51:39', 0),
(8, 'beyko hjfgj ', '35235', '2025-05-15 18:40:00', '2025-05-15 13:45:53', 0);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `ayarlar`
--
ALTER TABLE `ayarlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `mesajlar`
--
ALTER TABLE `mesajlar`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `randevular`
--
ALTER TABLE `randevular`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `randevusistem`
--
ALTER TABLE `randevusistem`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `randevu_gecmis`
--
ALTER TABLE `randevu_gecmis`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `ayarlar`
--
ALTER TABLE `ayarlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `mesajlar`
--
ALTER TABLE `mesajlar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `randevular`
--
ALTER TABLE `randevular`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tablo için AUTO_INCREMENT değeri `randevusistem`
--
ALTER TABLE `randevusistem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `randevu_gecmis`
--
ALTER TABLE `randevu_gecmis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
