SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `riddles` (
  `id` int NOT NULL,
  `riddle` varchar(255) NOT NULL,
  `answer` varchar(100) NOT NULL,
  `hint` varchar(255) DEFAULT NULL,
  `roomId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `riddles` (`id`, `riddle`, `answer`, `hint`, `roomId`) VALUES
(1, 'Ik wijs altijd naar het noorden, zuiden, oosten en westen. Wat ben ik?', 'Kompas', 'Handig als je verdwaald bent op een eiland.', 1),
(2, 'Ik ben vol zand maar geen strand. Je draait mij om om tijd te meten. Wat ben ik?', 'Zandloper', 'Denk aan tijd.', 1),
(3, 'Zonder mij kom je moeilijk van een eiland af over zee. Wat ben ik?', 'Boot', 'Je vaart ermee.', 1),
(4, 'Ik geef licht in het donker en warmte bij overleving. Wat ben ik?', 'Vuur', 'Je maakt mij met hout of een aansteker.', 2),
(5, 'Ik bescherm je tegen regen en zon, gemaakt van doek. Wat ben ik?', 'Tent', 'Handig om in te slapen buiten.', 2),
(6, 'Ik open een slot maar ben zelf geen deur. Wat ben ik?', 'Sleutel', 'Zonder mij blijf je opgesloten.', 2);

CREATE TABLE `teams` (
  `id` int NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `player_one` varchar(100) NOT NULL,
  `player_two` varchar(100) NOT NULL,
  `score` int NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `reviews` (
  `id` int NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `rating` int NOT NULL,
  `difficulty` varchar(50) NOT NULL,
  `review_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE `riddles`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `riddles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `teams`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `reviews`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;
