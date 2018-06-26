CREATE DATABASE inventory;

USE inventory;

CREATE TABLE items (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_categories INT NOT NULL,
	id_files INT NOT NULL DEFAULT 0,
	id_users INT NOT NULL,
	title TINYTEXT NOT NULL,
	description TEXT NOT NULL,
	batteries_aa INT NOT NULL DEFAULT 0,
	batteries_aaa INT NOT NULL DEFAULT 0,
	batteries_c INT NOT NULL DEFAULT 0,
	batteries_d INT NOT NULL DEFAULT 0,
	batteries_e INT NOT NULL DEFAULT 0,
	batteries_3r12 INT NOT NULL DEFAULT 0,
	materials tinytext not null,
	watt_max float,
	weight bigint not null default 0,
	price FLOAT NOT NULL,
	source TINYTEXT NOT NULL,
	location TINYTEXT NOT NULL,
	status INT NOT NULL DEFAULT 1,
	inuse INT NOT NULL,
	acquired DATETIME NOT NULL,
	disposed DATETIME NOT NULL,
	created DATETIME NOT NULL,
	updated DATETIME NOT NULL
);

CREATE TABLE categories (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_users INT NOT NULL,
	title TINYTEXT NOT NULL
);

CREATE TABLE packlists (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_users INT NOT NULL,
	title TINYTEXT NOT NULL,
	`from` DATETIME NOT NULL,
	`to` DATETIME NOT NULL,
	updated DATETIME NOT NULL,	notes TEXT NOT NULL,
	created DATETIME NOT NULL
);

CREATE TABLE relations_packlists_items (
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	id_packlists INT NOT NULL,
	id_items INT NOT NULL,
	id_users INT NOT NULL,
	comment tinytext not null,
	inuse int not null,
	packed INT NOT NULL DEFAULT 0,
	created DATETIME NOT NULL
);

CREATE TABLE packlist_items (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_packlists INT NOT NULL,
	id_users INT NOT NULL,
	inuse int not null,
	packed INT NOT NULL DEFAULT 0,
	title TINYTEXT NOT NULL,
	weight INT NOT NULL DEFAULT 0,
	updated DATETIME NOT NULL,
	created DATETIME NOT NULL
);

CREATE TABLE users(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_users INT NOT NULL,
	id_visum INT NOT NULL UNIQUE,
	nickname VARCHAR(16) NOT NULL,
	gender enum('0','1','2') NOT NULL,
	birth DATETIME NOT NULL,
	username TINYTEXT NOT NULL,
	password TINYTEXT NOT NULL,
	updated DATETIME NOT NULL,
	created DATETIME NOT NULL
);

CREATE TABLE relations_items_locations(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_items INT NOT NULL,
	id_locations INT NOT NULL,
	id_users INT NOT NULL
);

CREATE TABLE locations(
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_files INT NOT NULL DEFAULT 0,
	id_users INT NOT NULL,
	title TINYTEXT NOT NULL
);

CREATE TABLE files (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_users INT NOT NULL,
	mime TINYTEXT NOT NULL,
	created DATETIME NOT NULL
);

CREATE TABLE criterias (
	id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_users INT NOT NULL,
	title TINYTEXT NOT NULL,
	interval_days INT NOT NULL,
	add_to_new_packlists INT NOT NULL,
	updated DATETIME NOT NULL,
	created DATETIME NOT NULL
);

CREATE TABLE relations_criterias_items (
	id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_criterias BIGINT NOT NULL,
	id_items BIGINT NOT NULL,
	id_users INT NOT NULL,
	created DATETIME NOT NULL
);

CREATE TABLE relations_criterias_packlists (
	id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_criterias BIGINT NOT NULL,
	id_packlists BIGINT NOT NULL,
	id_users INT NOT NULL,
	created DATETIME NOT NULL
);

CREATE TABLE location_history (
	id BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	id_items BIGINT NOT NULL,
	id_users INT NOT NULL,
	title TINYTEXT NOT NULL,
	created DATETIME NOT NULL
);
