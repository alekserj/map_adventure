-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 24 2025 г., 18:47
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
-- Структура таблицы `favorite_points`
--

CREATE TABLE `favorite_points` (
  `id` int NOT NULL,
  `point_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `favorite_routes`
--

CREATE TABLE `favorite_routes` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `route` linestring NOT NULL,
  `route_type` text NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(16, 46, '/object_pictures/obj_46_67fbc4dbb875d.jpg'),
(20, 36, '/object_pictures/obj_36_67fe795add89d.jpg'),
(21, 36, '/object_pictures/obj_36_67fe795addd8d.jpg'),
(22, 36, '/object_pictures/obj_36_67fe795ade3cf.jpg'),
(23, 36, '/object_pictures/obj_36_67fe795ade8c4.jpg'),
(24, 35, '/object_pictures/obj_35_67fe7a26288e5.jpg'),
(25, 30, '/object_pictures/obj_30_67fe7a74d4edf.jpg'),
(26, 30, '/object_pictures/obj_30_67fe7a74d5e04.jpg'),
(27, 34, '/object_pictures/obj_34_67fe7b58994fe.jpg'),
(28, 34, '/object_pictures/obj_34_67fe7b58998f5.png'),
(29, 28, '/object_pictures/obj_28_67fe7be1164f6.jpg'),
(30, 28, '/object_pictures/obj_28_67fe7be116835.jpg'),
(31, 28, '/object_pictures/obj_28_67fe7be116a93.jpg'),
(32, 28, '/object_pictures/obj_28_67fe7be116c6a.jpg'),
(33, 18, '/object_pictures/obj_18_67fe7c2d15520.jpg'),
(34, 11, '/object_pictures/obj_11_67fe7c898f7c3.jpg'),
(35, 37, '/object_pictures/obj_37_67fe7ceb6cb3c.jpg'),
(36, 37, '/object_pictures/obj_37_67fe7ceb6ce14.jpg'),
(37, 37, '/object_pictures/obj_37_67fe7ceb6d128.jpg'),
(38, 38, '/object_pictures/obj_38_67fe7d2cab587.webp'),
(39, 38, '/object_pictures/obj_38_67fe7d2cab8f8.webp'),
(40, 38, '/object_pictures/obj_38_67fe7d2cabc04.webp'),
(41, 32, '/object_pictures/obj_32_67fe7d96ce9f4.jpg'),
(42, 32, '/object_pictures/obj_32_67fe7d96cf6d0.webp'),
(43, 32, '/object_pictures/obj_32_67fe7d96cfca7.jpg'),
(44, 39, '/object_pictures/obj_39_67fe7de72028a.webp'),
(45, 39, '/object_pictures/obj_39_67fe7de72058c.webp'),
(46, 39, '/object_pictures/obj_39_67fe7de7207f7.webp'),
(47, 29, '/object_pictures/obj_29_67fe81a2ea651.jpg'),
(48, 29, '/object_pictures/obj_29_67fe81a2ea97e.jpg');

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
(11, 'Памятник В.И. Ленину', 0x0000000001010000006ea7ad11c118424024809bc58bdd4940, 'городской округ Курск, Центральный округ, Красная площадь', 'Культурные', 'Памятник Владимиру Ильичу Ленину был открыт 1 сентября 1933 года на Красной площади города Курска.'),
(18, 'Церковь Архангела Михаила', 0x00000000010100000014048f6fef1642400de36e10addd4940, 'Курск, улица Карла Либкнехта, 39', 'Религиозные', 'Храм Арха́нгела Михаи́ла — православный храм в Центральном округе города Курска. Входит в состав 2-го благочиния города Курска Курской епархии Русской православной церкви. Памятник архитектуры республиканского значения.'),
(28, 'Троицкий монастырь', 0x0000000001010000002e3c2f151b1942404dd87e32c6dd4940, 'Курск, улица Горького, 13/2', 'Религиозные', 'Курский Свято-Троицкий женский монастырь расположен в центральной части города. В XII веке это место было северной частью примыкающей к Курской крепости городской постройки. Тогда здесь стояла церковь.'),
(29, 'Знаменский собор', 0x0000000001010000001bf67b629d1842406249b9fb1cdd4940, 'Курск, улица Луначарского, 4', 'Религиозные', 'Собо́р ико́ны Бо́жией Ма́тери «Зна́мение» — православный храм в Курске, кафедральный собор Курской митрополии и епархии Русской православной церкви. Расположен в историческом центре города, на территории Знаменского монастыря. Построен в 1816—1826 годы в честь победы в Отечественной войне 1812 года.'),
(30, 'Памятник А. С. Пушкину', 0x000000000101000000d3838252b418424001158e2095de4940, 'городской округ Курск, Центральный округ, Театральная площадь', 'Культурные', 'Памятник А. С. Пушкину — это скульптура, расположенная на Театральной площади города Курска.'),
(32, 'Краеведческий музей', 0x0000000001010000009e9acb0d861842404e42e90b21dd4940, 'Курск, улица Луначарского, 6', 'Музеи', 'Курский областной краеведческий музей (официально — областное бюджетное учреждение культуры «Курский областной краеведческий музей», КОКМ) — историко-краеведческий музей, расположенный в городе Курск; был создан 6 мая 1903 года по инициативе курского губернатора, тайного советника Николая Гордеева — под названием «Историко-археологический и кустарный музей в память посещения города Курска императором Николаем II в 1902 году». Музей открылся 31 января 1905 года; к 1915 году располагал коллекцией из 10 тысяч экспонатов и библиотекой из 1000 единиц хранения. По данным на 2019 год, в фондах музея хранились свыше 181 тысячи экспонатов, включая коллекции бисера, фарфора, оружия, нумизматики, живописи, редкой книги, мебели и костюма. Музей — расположенный в здании, являющемся памятником архитектуры XIX века — разделён на три основных отдела: отдел природы, отдел истории дореволюционного периода, а также — отдел истории советского общества; сотрудничает с образовательными учреждениями Курска.'),
(34, 'Музей А.Г. Уфимцева и Ф.А. Семенова', 0x000000000101000000f5ba4560ac1742407e384888f2dd4940, 'Курск, Семёновская улица, 14', 'Музеи', 'Дом-музей посвящён жизни и работе знаменитого учёного-астронома Фёдора Семенова и его внука, изобретателя Анатолия Уфимцева. Временно закрыт с момента пожара в марте 2019 года.'),
(35, 'Литературный музей', 0x000000000101000000d1e97937161842407e6fd39ffdde4940, 'Курск, Садовая улица, 21', 'Музеи', 'Литературный музей открыт на основании Постановления губернатора Курской области А.Н. Михайлова как филиал Курского областного краеведческого музея. Музей располагается в старинном здании, с конца ХIX в. по 1914 г. владельцем которого был крестьянин Ф.С. Трофимов.\r\n\r\nВ экспозиции музея, размещенной в восьми залах общей площадью 228 кв. м., отражена литературная жизнь Курского края, начиная с древнейших времен и заканчивая современностью. Сотрудниками музея собран материал о 220 курских поэтах и писателях.'),
(36, 'Парк героев гражданской войны', 0x0000000001010000004012f6ed241842408cbe823463df4940, 'городской округ Курск, Центральный округ, площадь Героев Гражданской войны', 'Природные', 'Парк Героев Гражданской войны в народе чаще называют «парк Бородино». В глубокую старину это место было городской окраиной, где находились соляные амбары курского купца Бырдина – благодаря ему парк и получил второе имя.'),
(37, 'Парк Боева дача', 0x000000000101000000b98ac56f0a1b42400c94145800df4940, 'городской округ Курск, Центральный округ, парк Боева дача', 'Природные', '«Боева дача», или просто «Боевка», – одно из любимых мест отдыха у жителей и гостей Курска. Парковую зону называют «зеленые легкие Курска», «уголок дикой природы посреди города», «зеленое кольцо».'),
(38, 'Новая Боевка', 0x000000000101000000b727486c771b4240191efb592cdf4940, 'городской округ Курск, Железнодорожный округ, пикник-парк Новая Боевка', 'Природные', 'Парк культуры и отдыха «Новая Боевка» — это идеальное место для семейного отдыха и прогулок на свежем воздухе. Он расположен на берегу реки Тускарь, что добавляет особую атмосферу спокойствия и умиротворения. Территория парка ухожена, здесь много зелени и цветущих растений.'),
(39, 'Курская антоновка', 0x0000000001010000000c3b8c497f15424020d5b0df13e54940, 'Курск, Северный жилой район', 'Архитектурные', 'Опора ЛЭП «Курская антоновка» — это арт-объект, который выглядит как гигантское дерево и имеет ночную подсветку.'),
(46, 'Драматический театр им. Пушкина', 0x000000000101000000f3ad0feb8d18424024b726dd96de4940, 'Курск, улица Ленина, 26', 'Культурные', 'Курский государственный драматический театр имени А. С. Пушкина — театр в Курске, один из старейших театров России, основан в 1792 году.'),
(50, 'ваяпва', 0x0000000001010000000a2e56d460184240705d3123bcdd4940, 'Курск, улица Радищева, 17', 'Архитектурные', '[]eoprthg0-9rijkiohbdfghdfg');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `nickname`, `email`, `password`) VALUES
(1, 'qw', 'er@ty.com', '$2y$10$6lvBbyX5AwNRuYjJQ19h1eRs98k8KxU6YbaEWlA.ROlgxtbf24yvC');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `favorite_points`
--
ALTER TABLE `favorite_points`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `favorite_routes`
--
ALTER TABLE `favorite_routes`
  ADD PRIMARY KEY (`id`);

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
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nickname` (`nickname`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `favorite_points`
--
ALTER TABLE `favorite_points`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT для таблицы `favorite_routes`
--
ALTER TABLE `favorite_routes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `pictures`
--
ALTER TABLE `pictures`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT для таблицы `points`
--
ALTER TABLE `points`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
