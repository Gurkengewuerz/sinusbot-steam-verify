SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `steam_teamspeak` (
  `steam_id` varchar(20) NOT NULL,
  `ts_uid` varchar(50) NOT NULL,
  `added` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `steam_id` varchar(20) NOT NULL,
  `realname` varchar(50) NOT NULL,
  `first_login` int(11) NOT NULL,
  `verify_code` varchar(8) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE `steam_teamspeak`
  ADD PRIMARY KEY (`steam_id`,`ts_uid`),
  ADD KEY `steam_id` (`steam_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`steam_id`),
  ADD UNIQUE KEY `steam_id` (`steam_id`);


ALTER TABLE `steam_teamspeak`
  ADD CONSTRAINT `steam_teamspeak_ibfk_1` FOREIGN KEY (`steam_id`) REFERENCES `users` (`steam_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
