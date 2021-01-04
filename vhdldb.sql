-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

CREATE TABLE `files` (
  `fid` int(11) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `dir` varchar(255) DEFAULT NULL,
  `username` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

CREATE TABLE `users` (
  `username` varchar(64) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users` (`username`, `email`, `pass`) VALUES
('guest', 'guest@tempmail.com', '084e0343a0486ff05530df6c705c8bb4');

ALTER TABLE `files`
  ADD PRIMARY KEY (`fid`),
  ADD UNIQUE KEY `filename` (`filename`),
  ADD UNIQUE KEY `dir` (`dir`),
  ADD KEY `username` (`username`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`username`);

ALTER TABLE `files`
  MODIFY `fid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
