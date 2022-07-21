CREATE TABLE users (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    full_name varchar(36) NOT NULL,
    email varchar(64) NOT NULL,
    password varchar(255) NOT NULL,
    isActive int NOT NULL DEFAULT 1,
    attemptCount int NOT NULL DEFAULT 0
);

CREATE TABLE admin (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    full_name varchar(36) NOT NULL,
    email varchar(64) NOT NULL,
    password varchar(255) NOT NULL,
    attemptCount int NOT NULL DEFAULT 0,
    role varchar(25) NOT NULL,
    sessionHash varchar(255),
);

CREATE TABLE super_admin (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    email varchar(64) NOT NULL,
    role char(6) DEFAULT "SADMIN",
    password varchar(255) NOT NULL
    sessionHash varchar(255),
);

CREATE TABLE feedbacks (
feedback_id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(120) NOT NULL,
filePath VARCHAR(120) NOT NULL,
comment VARCHAR(320) NOT NULL,
date DATE DEFAULT (CURRENT_DATE),
status VARCHAR(50),
user_id int NOT NULL,
FOREIGN KEY(user_id) REFERENCES users(id)
);
DROP table users;