-- --  /database/seed.sql
-- ================================================
-- DONNÉES DE TEST - IMDB PROJECT
-- FAIT PAR IA SIMPLEMENT POUR TESTER LA BASE
-- A SUPPRIMER EN PRODUCTION
-- ================================================

USE imdb_project;
-- pour refaire les tests de zero
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE movie_actor;
TRUNCATE TABLE movie_category;
TRUNCATE TABLE categories;
TRUNCATE TABLE directors;
TRUNCATE TABLE movies;
TRUNCATE TABLE actors;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;
-- fin des test

-- Catégories
INSERT INTO categories (name, slug) VALUES
('Action', 'action'),
('Drama', 'drama');

-- Réalisateurs
INSERT INTO directors (name, bio) VALUES
('Christopher Nolan', 'Réalisateur britannique connu pour Inception, Interstellar'),
('Quentin Tarantino', 'Réalisateur américain de Pulp Fiction'),
('Denis Villeneuve', 'Réalisateur canadien de Dune et Blade Runner 2049');

-- Films
INSERT INTO movies (title, description, price, release_year, duration, director_id, poster_url) VALUES
('Inception', 'Un voleur utilise la technologie pour s\'infiltrer dans les rêves.', 9.99, 2010, 148, 1, 'https://image.tmdb.org/t/p/w500/edv5CZvWj09upOsy2Y6IwDhK8bt.jpg'),
('Interstellar', 'Des astronautes voyagent à travers un trou de ver.', 12.99, 2014, 169, 1, 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg'),
('Pulp Fiction', 'Histoires croisées de criminels à Los Angeles.', 8.99, 1994, 154, 2, 'https://image.tmdb.org/t/p/w500/d5iIlFn5s0ImszYzBPb8JPIfbXD.jpg'),
('Dune', 'L\'histoire de Paul Atréides sur la planète Arrakis.', 14.99, 2021, 155, 3, 'https://image.tmdb.org/t/p/w500/d5NXSklXo0qyIYkgV94XAgMIckC.jpg');

-- Associer films et catégories

INSERT INTO movie_category (movie_id, category_id) VALUES
(1, 1), -- Inception → Action
(2, 1), -- Interstellar → Action
(2, 2), -- Interstellar → Drama
(3, 2), -- Pulp Fiction → Drama
(4, 1); -- Dune → Action

-- Acteurs

INSERT INTO actors (name) VALUES
('Leonardo DiCaprio'),
('Matthew McConaughey'),
('John Travolta'),
('Timothée Chalamet');

-- Associer acteurs et films
INSERT INTO movie_actor (movie_id, actor_id, role) VALUES
(1, 1, 'Dom Cobb'),
(2, 2, 'Cooper'),
(3, 3, 'Vincent Vega'),
(4, 4, 'Paul Atréides');

-- Utilisateur de test
INSERT INTO users (username, email, password_hash) VALUES
('admin', 'admin@imdb.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); 
-- Mot de passe : "password" hashé avec bcrypt