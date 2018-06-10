CREATE SCHEMA `project_test` DEFAULT CHARACTER SET utf8 ;

USE project_test;

CREATE TABLE `book` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`)) DEFAULT CHARSET=utf8;

CREATE TABLE `author` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`));

CREATE TABLE `book_author` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `book_id` INT UNSIGNED NOT NULL,
  `author_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_book_id_idx` (`book_id` ASC),
  INDEX `fk_author_id_idx` (`author_id` ASC),
  CONSTRAINT `fkba_book_id`
    FOREIGN KEY (`book_id`)
    REFERENCES `book` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fkba_author_id`
    FOREIGN KEY (`author_id`)
    REFERENCES `author` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);

CREATE TABLE `category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `is_adult` INT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`));

CREATE TABLE `book_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `book_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_category_id_idx` (`category_id` ASC),
  INDEX `fk_book_id_idx` (`book_id` ASC),
  CONSTRAINT `fk_book_id`
    FOREIGN KEY (`book_id`)
    REFERENCES `book` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_category_id`
    FOREIGN KEY (`category_id`)
    REFERENCES `category` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE);