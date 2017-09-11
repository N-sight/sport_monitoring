-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Хост: localhost:3306
-- Время создания: Янв 23 2017 г., 02:30
-- Версия сервера: 5.5.52-cll-lve
-- Версия PHP: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `c18372_hr_helper`
--

-- --------------------------------------------------------

--
-- Структура таблицы `hr_human`
--

CREATE TABLE IF NOT EXISTS `hr_human` (
  `id_human` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `last_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sur_name` varchar(255) NOT NULL,
  `b_day` date NOT NULL,
  `sex` tinyint(4) unsigned zerofill NOT NULL,
  `city_id` int(11) NOT NULL,
  `is_complete` tinyint(4) unsigned zerofill DEFAULT NULL,
  PRIMARY KEY (`id_human`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `hr_human`
--

INSERT INTO `hr_human` (`id_human`, `last_name`, `name`, `sur_name`, `b_day`, `sex`, `city_id`, `is_complete`) VALUES
(1, 'Иванов', 'Иван', 'Иванович', '1980-12-31', 0001, 1, NULL);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
