-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3307
-- Время создания: Июн 23 2024 г., 13:18
-- Версия сервера: 10.3.22-MariaDB
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `game_curs`
--

-- --------------------------------------------------------

--
-- Структура таблицы `battle`
--

CREATE TABLE `battle` (
  `id` int(11) NOT NULL,
  `status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'found',
  `nowRound` int(11) NOT NULL DEFAULT 0,
  `nextChange` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `camera`
--

CREATE TABLE `camera` (
  `player` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idRoom` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `cardImage` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(240) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Nona',
  `hp` int(11) NOT NULL DEFAULT 1,
  `mp` int(11) NOT NULL DEFAULT 1,
  `atk` int(11) NOT NULL DEFAULT 1,
  `price` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `cards`
--

INSERT INTO `cards` (`id`, `cardImage`, `name`, `hp`, `mp`, `atk`, `price`) VALUES
(1, 'https://i.pinimg.com/originals/a3/ed/67/a3ed677bc0d4ae3a8f32059147298d34.jpg', 'Зомби', 20, 5, 3, 100),
(2, 'https://cdna.artstation.com/p/assets/covers/images/000/941/134/large/philippe-steven-go-4-pyromancer.jpg', 'Пиромант', 15, 10, 7, 150),
(3, 'https://i.pinimg.com/originals/9b/19/8f/9b198fd3b17dfe7cfbab576570295217.jpg', 'Заклинатель', 17, 7, 5, 120),
(4, 'https://i.pinimg.com/originals/a6/79/e6/a679e631da4fd9ea7c0303a6f374f1f8.jpg', 'Вампир', 15, 8, 7, 120),
(5, 'https://celes.club/uploads/posts/2022-05/1652498126_61-celes-club-p-paren-mag-art-krasivo-68.png', 'Маг', 10, 10, 12, 130),
(6, 'https://images-ext-2.discordapp.net/external/hTx6ybObD2aDaj7nbpI9GgnNeFup-6YUTVhKB17tyEc/https/i.pinimg.com/564x/eb/67/a3/eb67a3d0da71645d5a3d12a208292e63.jpg?width=385&height=541', 'Мудрец', 15, 4, 12, 62),
(7, 'https://images-ext-2.discordapp.net/external/NiB2gvYrY2Jvf3lgNtU73uvfZvETj7M4oeA8h6cN2IY/https/i.pinimg.com/564x/59/f2/6f/59f26f112ddf7b4fa74cb1b7158c6c03.jpg?width=307&height=541', 'Аристократ', 12, 2, 5, 38),
(8, 'https://images-ext-1.discordapp.net/external/h0bvS89HQJ-z04jKd83Z4zoD-1mwG9TVNZEMCDJeGeQ/https/i.pinimg.com/564x/c5/51/3f/c5513ffe6bbbe9fd7e016a9efb94a109.jpg?width=361&height=541', 'Капитан Легиона', 25, 8, 15, 96),
(9, 'https://images-ext-1.discordapp.net/external/uWkBu2Nk7l66R0QUmt4kaxPYExx70exfDb2RBQMGDq8/https/i.pinimg.com/564x/fa/33/8a/fa338acf60604c965d657e5638bedcf6.jpg?width=304&height=541', 'Хранитель знаний', 15, 3, 12, 60),
(10, 'https://images-ext-1.discordapp.net/external/VU8mBUCVzceW5y0bwfE9Sr9otkQ9T2xCJrvf1sAhQoY/https/i.pinimg.com/564x/84/34/eb/8434eb31703851bb75f956aa6c4a8e2d.jpg', 'Убийца', 8, 4, 20, 64),
(11, 'https://images-ext-2.discordapp.net/external/188--haGDoE7lGvSFAvJJlaqA7V4bnBqPGmhFHrRDok/https/i.pinimg.com/564x/a2/84/27/a28427e3fdd23789a6f1b655dd3fa686.jpg?width=391&height=541', 'Мастер рун', 8, 5, 15, 56),
(12, 'https://images-ext-1.discordapp.net/external/SWzFdogfuA52ZhftWJqx3w7nb7jVo2V-IeWoL97zle4/https/i.pinimg.com/564x/0f/7a/1f/0f7a1f09d5b54e8e70c01dfa71e77840.jpg?width=442&height=541', 'Зимний страж', 21, 7, 15, 86),
(13, 'https://i.pinimg.com/564x/56/ca/3c/56ca3c9233ba84a50af8a11ac0ca4a24.jpg', 'Маг света', 13, 8, 15, 72),
(14, 'https://images-ext-2.discordapp.net/external/GqSvBKrbnAiFlGW-A1p_AOORt6mPlrT1RLe2O7VgFrA/https/i.pinimg.com/564x/22/02/be/2202be81a16398d926f12c395bd2bede.jpg?width=379&height=541', 'Рыцарь странник', 15, 4, 10, 58),
(15, 'https://images-ext-1.discordapp.net/external/H8dTMUGjVlZtmfHAUjo4Mk5bKSG68dkwD07y3mNNupc/https/i.pinimg.com/564x/57/fe/fb/57fefb9c9a247d0d4c32f6048b217f85.jpg?width=293&height=541', 'Ночной страж', 12, 3, 9, 38);

-- --------------------------------------------------------

--
-- Структура таблицы `cardsinbox`
--

CREATE TABLE `cardsinbox` (
  `id` int(11) NOT NULL,
  `users` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idCards` int(11) NOT NULL,
  `count` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `cardsinbox`
--

INSERT INTO `cardsinbox` (`id`, `users`, `idCards`, `count`) VALUES
(1, 'Monster', 2, 1),
(2, 'Monster', 5, 1),
(3, 'Morgana', 3, 1),
(7, 'Morgana', 12, 1),
(8, 'Morgana', 10, 1),
(9, 'Morgana', 1, 1),
(10, 'Monster', 12, 1),
(11, 'Monster', 7, 1),
(12, 'Monster', 13, 1),
(13, 'Monster', 11, 1),
(14, 'Monster', 4, 1),
(15, 'Morgana', 5, 1),
(16, 'Morgana', 9, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `who` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `whom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `what` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `whenDate` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `post`
--

INSERT INTO `post` (`id`, `who`, `whom`, `what`, `whenDate`) VALUES
(3, 'Monster', 'WORLD', 'hello again', '2022-12-21 16:19:50'),
(4, 'Monster', 'WORLD', 'test', '2022-12-21 16:21:00'),
(5, 'Monster', 'WORLD', 'test again', '2022-12-22 17:09:43'),
(6, 'Morgana', 'WORLD', 'опять тест', '2022-12-22 17:11:17'),
(7, 'Morgana', 'WORLD', 'а сейчас?', '2022-12-22 17:11:41'),
(8, 'Morgana', 'WORLD', 'ну вот сейчас', '2022-12-22 17:15:29');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `login` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `passwd` varchar(240) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'любой пароль = !логин10',
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `cash` int(11) NOT NULL DEFAULT 400,
  `dateRegistration` datetime DEFAULT current_timestamp(),
  `win` int(100) NOT NULL DEFAULT 0,
  `loose` int(100) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`login`, `email`, `passwd`, `status`, `cash`, `dateRegistration`, `win`, `loose`) VALUES
('Monster', 'monster@gmail.com', '6c8982c3fde99d1c7ff65c196e8e3e9f', 'user', 28, '2022-12-14 13:58:28', 0, 0),
('Morgana', 'morgana@gmail.com', '2080c622e6dd97a7cdb2fdedeec0f3ba', 'user', 46, '2022-12-14 14:11:30', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `usersinbattle`
--

CREATE TABLE `usersinbattle` (
  `id` int(11) NOT NULL,
  `login` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'P1',
  `hp` int(11) NOT NULL DEFAULT 40,
  `mp` int(11) NOT NULL DEFAULT 10,
  `cardsInHand` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none|none|none|none',
  `cardsInBattle` text COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none|none|none|none'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `battle`
--
ALTER TABLE `battle`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `camera`
--
ALTER TABLE `camera`
  ADD KEY `player` (`player`),
  ADD KEY `idRoom` (`idRoom`);

--
-- Индексы таблицы `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `cardsinbox`
--
ALTER TABLE `cardsinbox`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users` (`users`),
  ADD KEY `idCards` (`idCards`);

--
-- Индексы таблицы `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `who` (`who`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `usersinbattle`
--
ALTER TABLE `usersinbattle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login` (`login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `cardsinbox`
--
ALTER TABLE `cardsinbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `usersinbattle`
--
ALTER TABLE `usersinbattle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `camera`
--
ALTER TABLE `camera`
  ADD CONSTRAINT `camera_ibfk_1` FOREIGN KEY (`player`) REFERENCES `users` (`login`),
  ADD CONSTRAINT `camera_ibfk_2` FOREIGN KEY (`idRoom`) REFERENCES `battle` (`id`);

--
-- Ограничения внешнего ключа таблицы `cardsinbox`
--
ALTER TABLE `cardsinbox`
  ADD CONSTRAINT `cardsinbox_ibfk_1` FOREIGN KEY (`users`) REFERENCES `users` (`login`),
  ADD CONSTRAINT `cardsinbox_ibfk_2` FOREIGN KEY (`idCards`) REFERENCES `cards` (`id`);

--
-- Ограничения внешнего ключа таблицы `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`who`) REFERENCES `users` (`login`);

--
-- Ограничения внешнего ключа таблицы `usersinbattle`
--
ALTER TABLE `usersinbattle`
  ADD CONSTRAINT `usersinbattle_ibfk_1` FOREIGN KEY (`login`) REFERENCES `users` (`login`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
