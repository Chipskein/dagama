SET FOREIGN_KEY_CHECKS = 0;
    DROP TABLE IF EXISTS `interacao_assunto`;
    DROP TABLE IF EXISTS `interacao`;
    DROP TABLE IF EXISTS `citacao`;
    DROP TABLE IF EXISTS `solicitacao_amigo`;
    DROP TABLE IF EXISTS `amigo`;
    DROP TABLE IF EXISTS `porto_participa`;
    DROP TABLE IF EXISTS `porto`;
    DROP TABLE IF EXISTS `perfil`;
    DROP TABLE IF EXISTS `assunto`;
    DROP TABLE IF EXISTS `pais`;
SET FOREIGN_KEY_CHECKS = 1;
CREATE TABLE `pais`(
    codigo  INTEGER NOT NULL AUTO_INCREMENT,
    nome    VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo)
);

CREATE TABLE `assunto`(
    codigo  INTEGER NOT NULL AUTO_INCREMENT,
    nome    VARCHAR(250) NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo)
);
CREATE TABLE `perfil`(
    codigo INTEGER NOT NULL AUTO_INCREMENT,
    email VARCHAR(250) NOT NULL UNIQUE,
    senha VARCHAR(250) NOT NULL,
    genero CHAR(1) CHECK(genero='M' OR genero='F' OR genero is null),
    pais INTEGER NOT NULL,
    username VARCHAR(250) NOT NULL,
    img VARCHAR(250) NOT NULL DEFAULT 'https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif',
    datanasc DATETIME NOT NULL,
    dataregis DATETIME DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN NOT NULL DEFAULT 0 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo),
    FOREIGN KEY (pais) REFERENCES `pais`(codigo)
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
CREATE TABLE `solicitacao_amigo`(
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
    FOREIGN KEY (local) REFERENCES `pais`(codigo),
    FOREIGN KEY (post) REFERENCES `interacao`(codigo)
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

INSERT INTO `pais` (nome) VALUES('Afghanistan');  
INSERT INTO `pais` (nome) VALUES('Albania');  
INSERT INTO `pais` (nome) VALUES('Algeria');  
INSERT INTO `pais` (nome) VALUES('Andorra');  
INSERT INTO `pais` (nome) VALUES('Angola');  
INSERT INTO `pais` (nome) VALUES('Anguilla');  
INSERT INTO `pais` (nome) VALUES('Antigua & Barbuda');  
INSERT INTO `pais` (nome) VALUES('Argentina');  
INSERT INTO `pais` (nome) VALUES('Armenia');  
INSERT INTO `pais` (nome) VALUES('Australia');  
INSERT INTO `pais` (nome) VALUES('Austria');  
INSERT INTO `pais` (nome) VALUES('Azerbaijan');  
INSERT INTO `pais` (nome) VALUES('Bahamas');  
INSERT INTO `pais` (nome) VALUES('Bahrain');  
INSERT INTO `pais` (nome) VALUES('Bangladesh');  
INSERT INTO `pais` (nome) VALUES('Barbados');  
INSERT INTO `pais` (nome) VALUES('Belarus');  
INSERT INTO `pais` (nome) VALUES('Belgium');  
INSERT INTO `pais` (nome) VALUES('Belize');  
INSERT INTO `pais` (nome) VALUES('Benin');  
INSERT INTO `pais` (nome) VALUES('Bermuda');  
INSERT INTO `pais` (nome) VALUES('Bhutan');  
INSERT INTO `pais` (nome) VALUES('Bolivia');  
INSERT INTO `pais` (nome) VALUES('Bosnia & Herzegovina');  
INSERT INTO `pais` (nome) VALUES('Botswana');  
INSERT INTO `pais` (nome) VALUES('Brazil');  
INSERT INTO `pais` (nome) VALUES('Brunei Darussalam');  
INSERT INTO `pais` (nome) VALUES('Bulgaria');  
INSERT INTO `pais` (nome) VALUES('Burkina Faso');  
INSERT INTO `pais` (nome) VALUES('Myanmar/Burma');  
INSERT INTO `pais` (nome) VALUES('Burundi');  
INSERT INTO `pais` (nome) VALUES('Cambodia');  
INSERT INTO `pais` (nome) VALUES('Cameroon');  
INSERT INTO `pais` (nome) VALUES('Canada');  
INSERT INTO `pais` (nome) VALUES('Cape Verde');  
INSERT INTO `pais` (nome) VALUES('Cayman Islands');  
INSERT INTO `pais` (nome) VALUES('Central African Republic');  
INSERT INTO `pais` (nome) VALUES('Chad');  
INSERT INTO `pais` (nome) VALUES('Chile');  
INSERT INTO `pais` (nome) VALUES('China');  
INSERT INTO `pais` (nome) VALUES('Colombia');  
INSERT INTO `pais` (nome) VALUES('Comoros');  
INSERT INTO `pais` (nome) VALUES('Congo');  
INSERT INTO `pais` (nome) VALUES('Costa Rica');  
INSERT INTO `pais` (nome) VALUES('Croatia');  
INSERT INTO `pais` (nome) VALUES('Cuba');  
INSERT INTO `pais` (nome) VALUES('Cyprus');  
INSERT INTO `pais` (nome) VALUES('Czech Republic');  
INSERT INTO `pais` (nome) VALUES('Democratic Republic of the Congo');  
INSERT INTO `pais` (nome) VALUES('Denmark');  
INSERT INTO `pais` (nome) VALUES('Djibouti');  
INSERT INTO `pais` (nome) VALUES('Dominican Republic');  
INSERT INTO `pais` (nome) VALUES('Dominica');  
INSERT INTO `pais` (nome) VALUES('Ecuador');  
INSERT INTO `pais` (nome) VALUES('Egypt');  
INSERT INTO `pais` (nome) VALUES('El Salvador');  
INSERT INTO `pais` (nome) VALUES('Equatorial Guinea');  
INSERT INTO `pais` (nome) VALUES('Eritrea');  
INSERT INTO `pais` (nome) VALUES('Estonia');  
INSERT INTO `pais` (nome) VALUES('Ethiopia');  
INSERT INTO `pais` (nome) VALUES('Fiji');  
INSERT INTO `pais` (nome) VALUES('Finland');  
INSERT INTO `pais` (nome) VALUES('France');  
INSERT INTO `pais` (nome) VALUES('French Guiana');  
INSERT INTO `pais` (nome) VALUES('Gabon');  
INSERT INTO `pais` (nome) VALUES('Gambia');  
INSERT INTO `pais` (nome) VALUES('Georgia');  
INSERT INTO `pais` (nome) VALUES('Germany');  
INSERT INTO `pais` (nome) VALUES('Ghana');  
INSERT INTO `pais` (nome) VALUES('Great Britain');  
INSERT INTO `pais` (nome) VALUES('Greece');  
INSERT INTO `pais` (nome) VALUES('Grenada');  
INSERT INTO `pais` (nome) VALUES('Guadeloupe');  
INSERT INTO `pais` (nome) VALUES('Guatemala');  
INSERT INTO `pais` (nome) VALUES('Guinea');  
INSERT INTO `pais` (nome) VALUES('Guinea-Bissau');  
INSERT INTO `pais` (nome) VALUES('Guyana');  
INSERT INTO `pais` (nome) VALUES('Haiti');  
INSERT INTO `pais` (nome) VALUES('Honduras');  
INSERT INTO `pais` (nome) VALUES('Hungary');  
INSERT INTO `pais` (nome) VALUES('Iceland');  
INSERT INTO `pais` (nome) VALUES('India');  
INSERT INTO `pais` (nome) VALUES('Indonesia');  
INSERT INTO `pais` (nome) VALUES('Iran');  
INSERT INTO `pais` (nome) VALUES('Iraq');  
INSERT INTO `pais` (nome) VALUES('Israel and the Occupied Territories');  
INSERT INTO `pais` (nome) VALUES('Italy');  
INSERT INTO `pais` (nome) VALUES('Ivory Coast (Cote d''Ivoire)');  
INSERT INTO `pais` (nome) VALUES('Jamaica');  
INSERT INTO `pais` (nome) VALUES('Japan');  
INSERT INTO `pais` (nome) VALUES('Jordan');  
INSERT INTO `pais` (nome) VALUES('Kazakhstan');  
INSERT INTO `pais` (nome) VALUES('Kenya');  
INSERT INTO `pais` (nome) VALUES('Kosovo');  
INSERT INTO `pais` (nome) VALUES('Kuwait');  
INSERT INTO `pais` (nome) VALUES('Kyrgyz Republic (Kyrgyzstan)');  
INSERT INTO `pais` (nome) VALUES('Laos');  
INSERT INTO `pais` (nome) VALUES('Latvia');  
INSERT INTO `pais` (nome) VALUES('Lebanon');  
INSERT INTO `pais` (nome) VALUES('Lesotho');  
INSERT INTO `pais` (nome) VALUES('Liberia');  
INSERT INTO `pais` (nome) VALUES('Libya');  
INSERT INTO `pais` (nome) VALUES('Liechtenstein');  
INSERT INTO `pais` (nome) VALUES('Lithuania');  
INSERT INTO `pais` (nome) VALUES('Luxembourg');  
INSERT INTO `pais` (nome) VALUES('Republic of Macedonia');  
INSERT INTO `pais` (nome) VALUES('Madagascar');  
INSERT INTO `pais` (nome) VALUES('Malawi');  
INSERT INTO `pais` (nome) VALUES('Malaysia');  
INSERT INTO `pais` (nome) VALUES('Maldives');  
INSERT INTO `pais` (nome) VALUES('Mali');  
INSERT INTO `pais` (nome) VALUES('Malta');  
INSERT INTO `pais` (nome) VALUES('Martinique');  
INSERT INTO `pais` (nome) VALUES('Mauritania');  
INSERT INTO `pais` (nome) VALUES('Mauritius');  
INSERT INTO `pais` (nome) VALUES('Mayotte');  
INSERT INTO `pais` (nome) VALUES('Mexico');  
INSERT INTO `pais` (nome) VALUES('Moldova, Republic of');  
INSERT INTO `pais` (nome) VALUES('Monaco');  
INSERT INTO `pais` (nome) VALUES('Mongolia');  
INSERT INTO `pais` (nome) VALUES('Montenegro');  
INSERT INTO `pais` (nome) VALUES('Montserrat');  
INSERT INTO `pais` (nome) VALUES('Morocco');  
INSERT INTO `pais` (nome) VALUES('Mozambique');  
INSERT INTO `pais` (nome) VALUES('Namibia');  
INSERT INTO `pais` (nome) VALUES('Nepal');  
INSERT INTO `pais` (nome) VALUES('Netherlands');  
INSERT INTO `pais` (nome) VALUES('New Zealand');  
INSERT INTO `pais` (nome) VALUES('Nicaragua');  
INSERT INTO `pais` (nome) VALUES('Niger');  
INSERT INTO `pais` (nome) VALUES('Nigeria');  
INSERT INTO `pais` (nome) VALUES('Korea, Democratic Republic of (North Korea)');  
INSERT INTO `pais` (nome) VALUES('Norway');  
INSERT INTO `pais` (nome) VALUES('Oman');  
INSERT INTO `pais` (nome) VALUES('Pacific Islands');  
INSERT INTO `pais` (nome) VALUES('Pakistan');  
INSERT INTO `pais` (nome) VALUES('Panama');  
INSERT INTO `pais` (nome) VALUES('Papua New Guinea');  
INSERT INTO `pais` (nome) VALUES('Paraguay');  
INSERT INTO `pais` (nome) VALUES('Peru');  
INSERT INTO `pais` (nome) VALUES('Philippines');  
INSERT INTO `pais` (nome) VALUES('Poland');  
INSERT INTO `pais` (nome) VALUES('Portugal');  
INSERT INTO `pais` (nome) VALUES('Puerto Rico');  
INSERT INTO `pais` (nome) VALUES('Qatar');  
INSERT INTO `pais` (nome) VALUES('Reunion');  
INSERT INTO `pais` (nome) VALUES('Romania');  
INSERT INTO `pais` (nome) VALUES('Russian Federation');  
INSERT INTO `pais` (nome) VALUES('Rwanda');  
INSERT INTO `pais` (nome) VALUES('Saint Kitts and Nevis');  
INSERT INTO `pais` (nome) VALUES('Saint Lucia');  
INSERT INTO `pais` (nome) VALUES('Saint Vincent''s & Grenadines');  
INSERT INTO `pais` (nome) VALUES('Samoa');  
INSERT INTO `pais` (nome) VALUES('Sao Tome and Principe');  
INSERT INTO `pais` (nome) VALUES('Saudi Arabia');  
INSERT INTO `pais` (nome) VALUES('Senegal');  
INSERT INTO `pais` (nome) VALUES('Serbia');  
INSERT INTO `pais` (nome) VALUES('Seychelles');  
INSERT INTO `pais` (nome) VALUES('Sierra Leone');  
INSERT INTO `pais` (nome) VALUES('Singapore');  
INSERT INTO `pais` (nome) VALUES('Slovak Republic (Slovakia)');  
INSERT INTO `pais` (nome) VALUES('Slovenia');  
INSERT INTO `pais` (nome) VALUES('Solomon Islands');  
INSERT INTO `pais` (nome) VALUES('Somalia');  
INSERT INTO `pais` (nome) VALUES('South Africa');  
INSERT INTO `pais` (nome) VALUES('Korea, Republic of (South Korea)');  
INSERT INTO `pais` (nome) VALUES('South Sudan');  
INSERT INTO `pais` (nome) VALUES('Spain');  
INSERT INTO `pais` (nome) VALUES('Sri Lanka');  
INSERT INTO `pais` (nome) VALUES('Sudan');  
INSERT INTO `pais` (nome) VALUES('Suriname');  
INSERT INTO `pais` (nome) VALUES('Swaziland');  
INSERT INTO `pais` (nome) VALUES('Sweden');  
INSERT INTO `pais` (nome) VALUES('Switzerland');  
INSERT INTO `pais` (nome) VALUES('Syria');  
INSERT INTO `pais` (nome) VALUES('Tajikistan');  
INSERT INTO `pais` (nome) VALUES('Tanzania');  
INSERT INTO `pais` (nome) VALUES('Thailand');  
INSERT INTO `pais` (nome) VALUES('Timor Leste');  
INSERT INTO `pais` (nome) VALUES('Togo');  
INSERT INTO `pais` (nome) VALUES('Trinidad & Tobago');  
INSERT INTO `pais` (nome) VALUES('Tunisia');  
INSERT INTO `pais` (nome) VALUES('Turkey');  
INSERT INTO `pais` (nome) VALUES('Turkmenistan');  
INSERT INTO `pais` (nome) VALUES('Turks & Caicos Islands');  
INSERT INTO `pais` (nome) VALUES('Uganda');  
INSERT INTO `pais` (nome) VALUES('Ukraine');  
INSERT INTO `pais` (nome) VALUES('United Arab Emirates');  
INSERT INTO `pais` (nome) VALUES('United States of America (USA)');  
INSERT INTO `pais` (nome) VALUES('Uruguay');  
INSERT INTO `pais` (nome) VALUES('Uzbekistan');  
INSERT INTO `pais` (nome) VALUES('Venezuela');  
INSERT INTO `pais` (nome) VALUES('Vietnam');  
INSERT INTO `pais` (nome) VALUES('Virgin Islands (UK)');  
INSERT INTO `pais` (nome) VALUES('Virgin Islands (US)');  
INSERT INTO `pais` (nome) VALUES('Yemen');  
INSERT INTO `pais` (nome) VALUES('Zambia');  
INSERT INTO `pais` (nome) VALUES('Zimbabwe');  
