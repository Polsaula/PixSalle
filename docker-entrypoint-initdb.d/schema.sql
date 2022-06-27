SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS pixsalle;
USE pixsalle;

DROP TABLE IF EXISTS users;
CREATE TABLE users(
    id INT NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    password VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    phoneNumber VARCHAR(255) NOT NULL,
    profilePic VARCHAR(255) NOT NULL,
    membership INT DEFAULT 0,
    wallet FLOAT NOT NULL DEFAULT 0,
    createdAt DATETIME NOT NULL,
    updatedAt DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS portfolio;
CREATE TABLE portfolio(
    id INT AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS album;
CREATE TABLE album(
    id INT AUTO_INCREMENT,
    portfolio_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (portfolio_id) REFERENCES portfolio(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE album AUTO_INCREMENT=1501;

DROP TABLE IF EXISTS photo;
CREATE TABLE photo(
    id INT AUTO_INCREMENT,
    album_id INT NOT NULL,
    link VARCHAR(1000) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (album_id) REFERENCES album(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;