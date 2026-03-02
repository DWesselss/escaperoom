CREATE TABLE riddles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    riddle VARCHAR(255) NOT NULL,
    answer VARCHAR(100) NOT NULL,
    hint VARCHAR(255),
    roomId INT NOT NULL
);

-- Let op, dit is een voorbeeld!
INSERT INTO riddles (riddle, answer, hint, roomId)
VALUES
    ('Welke Pokémon is nummer 25 in de Pokédex?', 'Pikachu', 'Het is de mascotte van Pokémon.', 1),
    ('Wat is het type van Charmander?', 'Vuur', 'Denk aan zijn vlammende staart.', 1),
    ('Hoe heet de evolutie van Bulbasaur?', 'Ivysaur', 'Het zit tussen Bulbasaur en Venusaur.', 1),
    ('Wat gebeurt er als Magikarp level 20 bereikt?', 'Gyarados', 'Van nutteloos naar legendarisch!', 2),
    ('Wat is super effectief tegen een water-type Pokémon?', 'Gras', 'Denk aan elementaire logica: wat groeit in water?', 2),
    ('Welke legendarische vogel hoort bij het element ijs?', 'Articuno', 'Zijn naam begint met "Arti...".', 2),
    ('Wat is de naam van de professor in de eerste Pokémon-games?', 'Professor Oak', 'Hij deelt je eerste Pokémon uit.', 3),
    ('Welke kleur heeft shiny Charizard?', 'Zwart', 'Anders dan zijn originele oranje kleur.', 3),
    ('Wat gebruik je om een wilde Pokémon te vangen?', 'Pokéball', 'Je gooit het naar een Pokémon.', 3);
