CREATE DATABASE project;
Use project;
CREATE TABLE `admin` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);
INSERT INTO `admin` (`id`, `LastName`, `FirstName`, `Email`, `Username`, `Password`) VALUES
(1, 'Smith', 'John', 'username@domain.com', 'admin', '1234');
CREATE TABLE `hashes` (
  `hash` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL
);
CREATE TABLE `movies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255),
  `poster` varchar(255),
  `summary` longtext,
  `rating` varchar(255),
  `year` varchar(255),
  `language` varchar(255),
  `genres` text,
  `movie_url` varchar(255),
  `quality` varchar(255),
  `imdb_code` varchar(255),
  PRIMARY KEY (`id`)
);

