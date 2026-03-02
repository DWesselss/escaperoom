# Escape Room Project

Welkom bij het Avontuurlijke Escape Room Project!

Jullie worden een klein beetje geholpen door wat starterscode te geven. 
Let op, dit is alleen om een opzet te geven. Daarna gaan jullie verder bouwen.

## Bestanden

- **index.php**: Dit wordt de homepagina
- **room_1**: Bevat de HTML-structuur en laadt de vragen uit de database.
- **app.js**: Bevat de JavaScript-code voor de interactie met de vragen en antwoorden.
- **style.css**: Bevat de styling van de pagina.
- **dbcon.php**: Verbindt met de database en verzorgt de databaseverbinding.
- **riddles.sql**: Bevat de database-structuur en voorbeeldvragen.
- **overige**: In de overige bestanden dien je de opdracht te maken.

## Uitleg van de code

### app.js
App.js is voorzien van comments in de code. Zorg dat je het goed leest en stel vragen aan de docent als je het niet begrijpt.


### dbcon.php
Tijdens de les zal de docent uitleg geven over hoe je verbinding maakt met de database

### riddles.sql
Wijzig de vragen in het sql-bestand met de vragen voor jullie Escape Room.
Importeer dit bestand daarna in jullie database.

### room_1.php, room_2.php & room_3.php

#### Uitleg over dataset

In de onderstaande HTML- en PHP-code hebben we een interactieve vraag-en-antwoordinterface. Hier wordt gebruikgemaakt van de **dataset-eigenschap** in JavaScript, en het is goed om te begrijpen wat dit precies doet.

``` html
<div class="container">
  <?php foreach ($riddles as $index => $riddle) : ?>
  <div class="box" onclick="openModal(<?php echo $index; ?>)" data-index="<?php echo $index; ?>"
    data-riddle="<?php echo htmlspecialchars($riddle['riddle']); ?>"
    data-answer="<?php echo htmlspecialchars($riddle['answer']); ?>">
    Box <?php echo $index + 1; ?>
  </div>
  <?php endforeach; ?>
</div>

```
##### Lijst van Vragen (PHP)
- De `foreach` loop in PHP doorloopt een array van vragen en antwoorden uit de database (`$riddles`). Voor elke vraag wordt er een `<div>`-element gemaakt met de class `box`.
- Binnen de `div` worden er ook zogenaamde **data-attributen** toegevoegd:
  - `data-index`: Dit is een unieke index voor elke vraag.
  - `data-riddle`: Dit is de vraag zelf (dit komt uit de database).
  - `data-answer`: Dit is het juiste antwoord op de vraag (dit komt uit de database).

#### Het Gebruik van `dataset` in JavaScript

- **Waarom `dataset` belangrijk is:** In JavaScript kunnen we de data-attributen die we in de HTML hebben toegevoegd ophalen met behulp van de `dataset`-eigenschap. Dit maakt het mogelijk om extra gegevens (zoals de vraag en het juiste antwoord) op te slaan in de HTML zonder ze zichtbaar te maken voor de gebruiker.

- **Voorbeeld:** Als we kijken naar de `div` met de class `box`, zien we de data-attributen `data-index`, `data-riddle` en `data-answer`. Deze worden als volgt gebruikt:
```javascript
let box = document.querySelector(`.box[data-index='${index}']`); // Haalt de juiste box op (box 1, 2, 3 of 4)
let riddleText = box.dataset.riddle;  // Haalt de vraag op uit het data-riddle attribuut
let correctAnswer = box.dataset.answer;  // Haalt het antwoord op uit het data-answer attribuut
```

#### Voorbeeld van `box.dataset.riddle`:

Stel dat de vraag **"Wat is de hoofdstad van Nederland?"** is en deze is opgeslagen in het `data-riddle` attribuut. Door `box.dataset.riddle` aan te roepen, krijg je de waarde van dat attribuut: `"Wat is de hoofdstad van Nederland?"`.

Dit is handig omdat je zonder extra complexe DOM-manipulatie gegevens kunt koppelen aan elementen en deze gegevens later kunt ophalen met JavaScript, zoals bij het openen van de modal om een vraag te tonen.
