-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 13 2025 г., 17:07
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `map`
--

-- --------------------------------------------------------

--
-- Структура таблицы `pictures`
--

CREATE TABLE `pictures` (
  `id` int NOT NULL,
  `object_id` int NOT NULL,
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `pictures`
--

INSERT INTO `pictures` (`id`, `object_id`, `link`) VALUES
(15, 46, '/object_pictures/obj_46_67fbc4dbb83e8.webp'),
(16, 46, '/object_pictures/obj_46_67fbc4dbb875d.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `points`
--

CREATE TABLE `points` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `geo` point NOT NULL,
  `street` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` varchar(10000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `points`
--

INSERT INTO `points` (`id`, `name`, `geo`, `street`, `category`, `description`) VALUES
(11, 'Памятник В.И. Ленину', 0x0000000001010000006ea7ad11c118424024809bc58bdd4940, 'городской округ Курск, Центральный округ, Красная площадь', 'Культурные', '\r\nэхуыдвжлпамшщжывришолывпрмиролываипывп'),
(18, 'Церковь Архангела Михаила', 0x00000000010100000014048f6fef1642400de36e10addd4940, 'Курск, улица Карла Либкнехта, 39', 'Религиозные', ''),
(28, 'Троицкий монастырь', 0x0000000001010000002e3c2f151b1942404dd87e32c6dd4940, 'Курск, улица Горького, 13/2', 'Религиозные', ''),
(29, 'Знаменский собор', 0x0000000001010000001bf67b629d1842406249b9fb1cdd4940, 'Курск, улица Луначарского, 4', 'Религиозные', ''),
(30, 'Памятник А. С. Пушкину', 0x000000000101000000d3838252b418424001158e2095de4940, 'городской округ Курск, Центральный округ, Театральная площадь', 'Культурные', ''),
(32, 'Краеведческий музей', 0x0000000001010000009e9acb0d861842404e42e90b21dd4940, 'Курск, улица Луначарского, 6', 'Музеи', ''),
(34, 'Музей А.Г. Уфимцева и Ф.А. Семенова', 0x000000000101000000f5ba4560ac1742407e384888f2dd4940, 'Курск, Семёновская улица, 14', 'Музеи', 'зхашщзшцурашгоывлдиалдыива'),
(35, 'Литературный музей', 0x000000000101000000d1e97937161842407e6fd39ffdde4940, 'Курск, Садовая улица, 21', 'Музеи', ''),
(36, 'Парк героев гражданской войны', 0x0000000001010000004012f6ed241842408cbe823463df4940, 'городской округ Курск, Центральный округ, площадь Героев Гражданской войны', 'Природные', '[]ewfkpeowigfhiuoewsdfsd'),
(37, 'Парк Боева дача', 0x000000000101000000b98ac56f0a1b42400c94145800df4940, 'городской округ Курск, Центральный округ, парк Боева дача', 'Природные', ''),
(38, 'Новая Боевка', 0x000000000101000000b727486c771b4240191efb592cdf4940, 'городской округ Курск, Железнодорожный округ, пикник-парк Новая Боевка', 'Природные', ''),
(39, 'Курская антоновка', 0x0000000001010000000c3b8c497f15424020d5b0df13e54940, 'Курск, Северный жилой район', 'Архитектурные', ''),
(46, 'Драматический театр им. Пушкина', 0x000000000101000000f3ad0feb8d18424024b726dd96de4940, 'Курск, улица Ленина, 26', 'Культурные', 'Курский государственный драматический театр имени А. С. Пушкина — театр в Курске, один из старейших театров России, основан в 1792 году.');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `pictures`
--
ALTER TABLE `pictures`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `points`
--
ALTER TABLE `points`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `pictures`
--
ALTER TABLE `pictures`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `points`
--
ALTER TABLE `points`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
