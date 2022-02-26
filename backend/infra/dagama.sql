SET FOREIGN_KEY_CHECKS = 0;
    DROP TABLE IF EXISTS `interacao_assunto`;
    DROP TABLE IF EXISTS `interacao`;
    DROP TABLE IF EXISTS `citacao`;
    DROP TABLE IF EXISTS `solicita_amigo`;
    DROP TABLE IF EXISTS `amigo`;
    DROP TABLE IF EXISTS `porto_participa`;
    DROP TABLE IF EXISTS `porto`;
    DROP TABLE IF EXISTS `perfil`;
    DROP TABLE IF EXISTS `assunto`;
    DROP TABLE IF EXISTS `cidade`;
    DROP TABLE IF EXISTS `uf`;
    DROP TABLE IF EXISTS `pais`;
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE `pais`(
    codigo  INTEGER NOT NULL AUTO_INCREMENT,
    nome    VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo)
);
CREATE TABLE `uf`(
    codigo  INTEGER NOT NULL AUTO_INCREMENT,
    pais    INTEGER NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo),
    FOREIGN KEY (pais) REFERENCES `pais`(codigo) 

);
CREATE TABLE `cidade`(
    codigo  INTEGER NOT NULL AUTO_INCREMENT,
    uf      INTEGER NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo),
    FOREIGN KEY (uf) REFERENCES `uf`(codigo) 
);

CREATE TABLE `assunto`(
    codigo  INTEGER NOT NULL AUTO_INCREMENT,
    nome    VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo)
);
CREATE TABLE `perfil`(
    codigo INTEGER NOT NULL AUTO_INCREMENT,
    cidade INTEGER NOT NULL,
    email VARCHAR(250) NOT NULL UNIQUE,
    senha VARCHAR(250) NOT NULL,
    genero CHAR(1) CHECK(genero='M' OR genero='F' OR genero is null),
    username VARCHAR(250) NOT NULL,
    img VARCHAR(250) NOT NULL DEFAULT 'https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif',
    datanasc DATETIME NOT NULL,
    dataregis DATETIME DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN NOT NULL DEFAULT 0 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo),
    FOREIGN KEY (cidade) REFERENCES `cidade`(codigo)
);
CREATE TABLE `porto`(
    codigo INTEGER NOT NULL AUTO_INCREMENT,
    perfil INTEGER NOT NULL,
    nome VARCHAR(250) NOT NULL,
    descr VARCHAR(250) NOT NULL,
    img VARCHAR(250) NOT NULL DEFAULT 'https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg',
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    dataRegis DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(codigo),
    FOREIGN KEY (perfil) REFERENCES `perfil`(codigo) 
);
CREATE TABLE `porto_participa`(
    porto INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    dataregis DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (porto) REFERENCES `porto`(codigo),
    FOREIGN KEY (perfil) REFERENCES `perfil`(codigo)
);
CREATE TABLE `amigo`(
    amigo INTEGER NOT NULL CHECK(amigo!=perfil),
    perfil INTEGER NOT NULL CHECK(amigo!=perfil),
    dateAceito DATETIME NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    FOREIGN KEY (amigo) REFERENCES `perfil`(codigo),
    FOREIGN KEY (perfil) REFERENCES `perfil`(codigo)
);
CREATE TABLE `solicita_amigo`(
    amigo INTEGER NOT NULL CHECK(amigo!=perfil),
    perfil INTEGER NOT NULL CHECK(amigo!=perfil),
    dateEnvio DATETIME NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    FOREIGN KEY (amigo) REFERENCES `perfil`(codigo),
    FOREIGN KEY (perfil) REFERENCES `perfil`(codigo)
);

CREATE TABLE `interacao`(
    codigo INTEGER NOT NULL AUTO_INCREMENT,
    perfil INTEGER NOT NULL,
    perfil_posting INTEGER,
    porto INTEGER,
    post INTEGER,
    postPai INTEGER,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    texto VARCHAR(250) NOT NULL,
    isReaction BOOLEAN,
    isSharing BOOLEAN,
    local integer,
    emote VARCHAR(250) CHECK(isReaction IS NOT NULL OR emote IS NULL),
    -- Opções: curtir, kkk, amei, grr, wow, sad
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo),
    CHECK (
        (post IS NOT NULL AND perfil_posting IS NULL AND porto IS NULL) OR
        (post IS NULL AND perfil_posting IS NOT NULL AND porto IS NULL) OR
        (post IS NOT NULL AND perfil_posting IS NULL AND porto IS NOT NULL) OR
        (post IS NULL AND perfil_posting IS NULL AND porto IS NOT NULL) OR
        (post IS NULL AND perfil_posting IS NULL AND porto IS NULL)
    ),
    FOREIGN KEY (perfil) REFERENCES `perfil`(codigo),
    FOREIGN KEY (perfil_posting) REFERENCES `perfil`(codigo),
    FOREIGN KEY (porto) REFERENCES `porto`(codigo),
    FOREIGN KEY (post) REFERENCES `interacao`(codigo),
    FOREIGN KEY (local) REFERENCES `cidade`(codigo)
);
CREATE TABLE `citacao`(
    interacao INTEGER NOT NULL AUTO_INCREMENT,
    perfil INTEGER NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(interacao, perfil),
    FOREIGN KEY (perfil) REFERENCES `perfil`(codigo),
    FOREIGN KEY (interacao) REFERENCES `interacao`(codigo)
);

CREATE TABLE `interacao_assunto`(
    assunto INTEGER NOT NULL,
    interacao INTEGER NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    FOREIGN KEY (assunto) REFERENCES `assunto`(codigo),
    FOREIGN KEY (interacao) REFERENCES `interacao`(codigo)
);

insert into `pais`(nome,ativo) values('ue',1);
insert into `pais`(nome,ativo) values('ue2',1);
insert into `uf`(nome,ativo,pais) values('ue',1,1);
insert into `cidade`(nome,ativo,uf) values('ue',1,1);
