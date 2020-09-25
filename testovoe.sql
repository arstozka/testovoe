-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 192.168.1.165:3306
-- Время создания: Сен 25 2020 г., 13:27
-- Версия сервера: 5.7.20
-- Версия PHP: 7.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `testovoe`
--

-- --------------------------------------------------------

--
-- Структура таблицы `ankets`
--

CREATE TABLE `ankets` (
  `id` int(10) NOT NULL,
  `GENDER` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LASTNAME` char(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NAME` char(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FATHERNAME` char(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `BIRTHDAY` date DEFAULT NULL,
  `COLOR` char(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PERSONALITY` text COLLATE utf8mb4_unicode_ci,
  `AVATAR` int(11) DEFAULT NULL,
  `USIDCHOVOST` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `OPRYATNOST` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SAMOOBUCHAEMOST` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TRUDOLUBIE` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `ankets`
--

INSERT INTO `ankets` (`id`, `GENDER`, `LASTNAME`, `NAME`, `FATHERNAME`, `BIRTHDAY`, `COLOR`, `PERSONALITY`, `AVATAR`, `USIDCHOVOST`, `OPRYATNOST`, `SAMOOBUCHAEMOST`, `TRUDOLUBIE`) VALUES
(1, 'Мужской', 'Иванов', 'Иван', 'Петрович', '2020-09-16', '#fff', 'ывыфв', NULL, NULL, NULL, NULL, NULL),
(2, 'Женский', 'Сидорова', 'Сука', 'Александровна', '1999-12-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `ankets_images`
--

CREATE TABLE `ankets_images` (
  `anketa_id` int(10) NOT NULL,
  `image_id` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `images`
--

CREATE TABLE `images` (
  `image_type` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
  `image` blob NOT NULL,
  `image_size` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
  `image_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
  `image_id` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `login` char(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` char(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`login`, `password`) VALUES
('admin', 'qwerty123');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `ankets`
--
ALTER TABLE `ankets`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `ankets_images`
--
ALTER TABLE `ankets_images`
  ADD KEY `anketa_id` (`anketa_id`),
  ADD KEY `image_id` (`image_id`);

--
-- Индексы таблицы `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`image_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `ankets`
--
ALTER TABLE `ankets`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `images`
--
ALTER TABLE `images`
  MODIFY `image_id` tinyint(4) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `ankets_images`
--
ALTER TABLE `ankets_images`
  ADD CONSTRAINT `ankets_images_ibfk_1` FOREIGN KEY (`anketa_id`) REFERENCES `ankets` (`id`),
  ADD CONSTRAINT `ankets_images_ibfk_2` FOREIGN KEY (`image_id`) REFERENCES `images` (`image_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
