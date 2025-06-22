CREATE DATABASE IF NOT EXISTS vpn_db;
USE vpn_db;

CREATE TABLE usuarios (
    id          INT AUTO_INCREMENT,
    email       VARCHAR(255) NOT NULL UNIQUE,
    senha       VARCHAR(255) NOT NULL,
    nome        VARCHAR(255),
    ativo       TINYINT,
    telefone    VARCHAR(11),
    PRIMARY KEY (id)
);


CREATE TABLE certificados (
    id          VARCHAR(10) PRIMARY KEY,
    data        DATETIME NOT NULL,
    validade    DATE
);
