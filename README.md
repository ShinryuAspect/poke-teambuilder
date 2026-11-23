**English** · [Español](README.es.md)

# Pokémon Team Builder

A PHP/MySQL web app to build, save and edit competitive Pokémon teams in a Showdown-style interface, using live data from [PokéAPI](https://pokeapi.co/).

Built as a course project (Full-Stack web development).

## Screenshots

| Login | Dashboard |
|---|---|
| ![Login](docs/readme/login.png) | ![Dashboard](docs/readme/dashboard.png) |

![Team builder](docs/readme/builder.png)

## Features

- Account registration and login (bcrypt-hashed passwords, PDO prepared statements).
- Dashboard listing your saved teams, with create, edit and delete.
- Team builder with six slots. Each slot supports:
  - Pokémon search with autocomplete over the full National Dex, with sprite, gender and shiny toggle.
  - Held item, ability and nature selection.
  - Four moves restricted to those the Pokémon can legally learn in Scarlet/Violet.
  - EV/IV editor (252 per stat, 508 total) with live final-stat calculation.
- PokéAPI lists (Pokémon, items, abilities) are cached in the browser with IndexedDB to avoid repeated requests.
- The interface is in Spanish.

## Status

- The "Analizar con IA" button currently returns a placeholder text. The Gemini integration (`src/services/GeminiService.php`) is not implemented yet.

## Tech stack

PHP 8 · MySQL / MariaDB (PDO) · Vanilla JavaScript · HTML/CSS · PokéAPI

## Getting started

Requirements: PHP 8+ with the `pdo_mysql` and `curl` extensions, and MySQL or MariaDB.

1. Clone the repository.
2. Create the database by importing the schema:
   ```bash
   mysql -u root -p < docs/bd_schema.sql
   ```
3. Set your database credentials in `config/config.php`.
4. Start the development server:
   ```bash
   php -S localhost:8000 -t public
   ```
5. Open `http://localhost:8000`, register an account and create your first team.

## Project structure

```
config/        database connection and settings
docs/          schema, project deliverables and screenshots
public/        entry point, views, CSS and JavaScript
src/models/    Usuario and Equipo (database access)
src/services/  SmogonService (PokéAPI lookup), GeminiService (pending)
```

## Credits

Pokémon data and sprites come from [PokéAPI](https://pokeapi.co/). Pokémon is a trademark of Nintendo, Game Freak and Creatures Inc.; this is a non-commercial fan project with no affiliation.
