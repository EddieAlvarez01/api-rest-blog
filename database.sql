CREATE DATABASE IF NOT EXISTS db_blog;
USE db_blog;

CREATE TABLE Role(
	id INT UNSIGNED AUTO_INCREMENT,
    name VARCHAR(15) NOT NULL,
    CONSTRAINT pk_role PRIMARY KEY(id)
)ENGINE=InnoDB;

CREATE TABLE Category(
	id INT UNSIGNED AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    CONSTRAINT pk_id PRIMARY KEY(id)
)ENGINE=InnoDB;

CREATE TABLE User(
	id INT UNSIGNED AUTO_INCREMENT,
    role_id INT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    surname VARCHAR(200) NOT NULL,
    email VARCHAR(300) NOT NULL,
    password VARCHAR(1000) NOT NULL,
    description TEXT,
    image VARCHAR(500),
    created_at DATETIME,
    updated_at DATETIME,
    remember_token VARCHAR(500),
    CONSTRAINT pk_user PRIMARY KEY(id),
    CONSTRAINT uq_user_email UNIQUE(email),
    CONSTRAINT fk_user_role FOREIGN KEY(role_id) REFERENCES Role(id)
)ENGINE=InnoDB;

CREATE TABLE Post(
	id INT UNSIGNED AUTO_INCREMENT,
    user_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    title VARCHAR(300) NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(500),
    created_at DATETIME,
    updated_at DATETIME,
    CONSTRAINT pk_post PRIMARY KEY(id),
    CONSTRAINT fk_post_user FOREIGN KEY(user_id) REFERENCES User(id),
    CONSTRAINT fk_post_category FOREIGN KEY(category_id) REFERENCES Category(id)
)ENGINE=InnoDB;

#INSERCIÓN DE ROLES
INSERT INTO Role(name)
VALUES('administrador');
INSERT INTO Role(name)
VALUES('usuario');

#ADMIN
INSERT INTO User(role_id, name, surname, email, password, description, image, created_at, updated_at, remember_token)
SELECT id, 'admin', 'admin', 'admin@admin.com', 'admin', 'administrador del sistema', '', NOW(), NOW(), NULL
FROM Role
WHERE name = 'administrador';
