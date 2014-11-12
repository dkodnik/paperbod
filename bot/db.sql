-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Мар 06 2014 г., 16:34
-- Версия сервера: 5.5.31
-- Версия PHP: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Структура таблицы `feeds`
--

CREATE TABLE IF NOT EXISTS `feeds` (
  `hash` varchar(64) NOT NULL COMMENT 'Hash URL',
  `idusr` int(10) unsigned NOT NULL COMMENT 'ID аккаунта',
  `idst` int(11) unsigned NOT NULL COMMENT 'ID Sites',
  `addfeed` int(1) NOT NULL DEFAULT '0' COMMENT 'Добавлен на сайт диаспоры? 0-нет, 1-да',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `hash` (`hash`,`idusr`),
  KEY `hash_2` (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Новость';


-- --------------------------------------------------------

--
-- Структура таблицы `flwrs_sites`
--

CREATE TABLE IF NOT EXISTS `flwrs_sites` (
  `address` varchar(256) NOT NULL COMMENT 'POD адрес пользователя',
  `idst` int(11) unsigned NOT NULL COMMENT 'ID Sites',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `address` (`address`,`idst`),
  KEY `address_2` (`address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Читатели сайтов';

-- --------------------------------------------------------

--
-- Структура таблицы `sites`
--

CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID Sites',
  `feed_url` varchar(200) NOT NULL COMMENT 'URL сайта новостей, RSS',
  `feed_type` varchar(10) NOT NULL COMMENT 'Тип потока',
  `idusr` int(10) unsigned NOT NULL COMMENT 'ID аккаунта',
  `pod_url` varchar(200) NOT NULL COMMENT 'POD диаспоры',
  `usrnm` varchar(200) NOT NULL COMMENT 'Имя пользователя на POD диаспоры',
  `pswrd` varchar(200) NOT NULL COMMENT 'Пароль пользователя',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT 'Статус потока: 0-новый, 1-работает, 2-ошибка, 3-удален',
  `rating` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Рейтинг сайта',
  `followers` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Количество читателей',
  `view_url` int(1) unsigned NOT NULL DEFAULT '1' COMMENT 'Показывать строку первоисточника',
  `string_footer` varchar(128) NOT NULL DEFAULT 'Published via [PaperboD*](http://paperbod.com)' COMMENT 'Собственная строка внизу',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `feed_url` (`feed_url`),
  KEY `idusr` (`idusr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Список сайтов новостей' AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tkn_post`
--

CREATE TABLE IF NOT EXISTS `tkn_post` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `idSites` int(11) unsigned NOT NULL,
  `message` varchar(1024) CHARACTER SET utf8 NOT NULL COMMENT 'Текст сообщения для постинга',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT 'Статус поста: 0-не опубликован, 1-опубликован',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Постинг в D* через токен API' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID аккаунта',
  `email` varchar(200) NOT NULL COMMENT 'Почтовый ящик, используется как логин',
  `pswrd` varchar(200) NOT NULL COMMENT 'Пароль на аккаунт',
  `feedlim` int(5) unsigned NOT NULL DEFAULT '20' COMMENT 'Лимит потоков',
  `verified` int(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Почтовый ящик проверен',
  `hash` varchar(32) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Пользователи' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Структура таблицы `verifi`
--

CREATE TABLE IF NOT EXISTS `verifi` (
  `vercode` varchar(32) NOT NULL COMMENT 'Код верификации',
  `idusr` int(11) unsigned NOT NULL COMMENT 'ID пользователя',
  `verified` int(1) NOT NULL DEFAULT '0' COMMENT 'Проверен email?',
  `create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания верификации',
  UNIQUE KEY `vercode` (`vercode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица верификации';


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
