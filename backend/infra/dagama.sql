SET FOREIGN_KEY_CHECKS = 0;
    DROP TABLE IF EXISTS `INTERACAO_ASSUNTO`;
    DROP TABLE IF EXISTS `INTERACAO`;
    DROP TABLE IF EXISTS `CITACAOPERFIL`;
    DROP TABLE IF EXISTS `CITACAO`;
    DROP TABLE IF EXISTS `SOLICITACAO_AMIGO`;
    DROP TABLE IF EXISTS `AMIGO`;
    DROP TABLE IF EXISTS `SELOUSER`;
    DROP TABLE IF EXISTS `SELO`;
    DROP TABLE IF EXISTS `PORTO_PARTICIPA`;
    DROP TABLE IF EXISTS `PORTO`;
    DROP TABLE IF EXISTS `PERFIL`;
    DROP TABLE IF EXISTS `ASSUNTO`;
    DROP TABLE IF EXISTS `CIDADE`;
    DROP TABLE IF EXISTS `UF`;
    DROP TABLE IF EXISTS `PAIS`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `PAIS`(
    codigo  INTEGER NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo)
);
CREATE TABLE `UF`(
    codigo  INTEGER NOT NULL,
    pais    INTEGER NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo),
    FOREIGN KEY (pais) REFERENCES PAIS(codigo) 

);
CREATE TABLE `CIDADE`(
    codigo  INTEGER NOT NULL,
    uf      INTEGER NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo),
    FOREIGN KEY (uf) REFERENCES UF(codigo) 
);

CREATE TABLE `ASSUNTO`(
    codigo  INTEGER NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo)
);
CREATE TABLE `PERFIL`(
    codigo INTEGER NOT NULL,
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
    FOREIGN KEY (cidade) REFERENCES CIDADE(codigo)
);
CREATE TABLE `PORTO`(
    codigo INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    nome VARCHAR(250) NOT NULL,
    descr VARCHAR(250) NOT NULL,
    img VARCHAR(250) NOT NULL DEFAULT 'https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg',
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    dataRegis DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo) 
);
CREATE TABLE `PORTO_PARTICIPA`(
    porto INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    dataregis DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (porto) REFERENCES PORTO(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);
CREATE TABLE `SELO`(
    codigo INTEGER NOT NULL,
    texto VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo)
);

CREATE TABLE `SELOUSER`(
    selo INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    porto INTEGER NOT NULL,
    dateVal DATETIME NOT NULL,
    FOREIGN KEY (selo) REFERENCES SELO(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo),
    FOREIGN KEY (porto) REFERENCES PORTO(codigo),
    PRIMARY KEY(perfil,porto)
);

CREATE TABLE `AMIGO`(
    amigo INTEGER NOT NULL CHECK(amigo!=perfil),
    perfil INTEGER NOT NULL CHECK(amigo!=perfil),
    dateAceito DATETIME NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    FOREIGN KEY (amigo) REFERENCES PERFIL(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);
CREATE TABLE `SOLICITACAO_AMIGO`(
    amigo INTEGER NOT NULL CHECK(amigo!=perfil),
    perfil INTEGER NOT NULL CHECK(amigo!=perfil),
    dateEnvio DATETIME NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    FOREIGN KEY (amigo) REFERENCES PERFIL(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);

CREATE TABLE `INTERACAO`(
    codigo INTEGER NOT NULL,
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
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo),
    FOREIGN KEY (perfil_posting) REFERENCES PERFIL(codigo),
    FOREIGN KEY (porto) REFERENCES PORTO(codigo),
    FOREIGN KEY (post) REFERENCES INTERACAO(codigo),
    FOREIGN KEY (local) REFERENCES CIDADE(codigo)
);
CREATE TABLE `CITACAO`(
    interacao INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(interacao, perfil),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo),
    FOREIGN KEY (interacao) REFERENCES INTERACAO(codigo)
);

CREATE TABLE `INTERACAO_ASSUNTO`(
    assunto INTEGER NOT NULL,
    interacao INTEGER NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    FOREIGN KEY (assunto) REFERENCES ASSUNTO(codigo),
    FOREIGN KEY (interacao) REFERENCES INTERACAO(codigo)
);


