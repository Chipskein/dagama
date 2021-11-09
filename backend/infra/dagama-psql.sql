DROP TABLE IF EXISTS INTERACAO_ASSUNTO;
DROP TABLE IF EXISTS INTERACAO;
DROP TABLE IF EXISTS CITACAOPERFIL;
DROP TABLE IF EXISTS CITACAO;
DROP TABLE IF EXISTS SOLICITACAO_AMIGO;
DROP TABLE IF EXISTS AMIGO;
DROP TABLE IF EXISTS SELOUSER;
DROP TABLE IF EXISTS SELO;
DROP TABLE IF EXISTS PORTO_PARTICIPA;
DROP TABLE IF EXISTS PORTO;
DROP TABLE IF EXISTS PERFIL;
DROP TABLE IF EXISTS ASSUNTO;
DROP TABLE IF EXISTS PAIS;

CREATE TABLE PAIS(
    codigo  SERIAL NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    PRIMARY KEY(codigo)
);
CREATE TABLE ASSUNTO(
    codigo  SERIAL NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    PRIMARY KEY(codigo)
);
CREATE TABLE PERFIL(
    codigo SERIAL NOT NULL,
    pais INTEGER NOT NULL,
    email VARCHAR(250) NOT NULL UNIQUE,
    senha VARCHAR(250) NOT NULL,
    genero CHAR(1) CHECK(genero='M' OR genero='F' OR genero is null),
    username VARCHAR(250) NOT NULL,
    img VARCHAR(250) NOT NULL DEFAULT 'https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif',
    datanasc TIMESTAMP NOT NULL,
    dataregis TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN NOT NULL DEFAULT false CHECK(ativo=true OR ativo=false),
    PRIMARY KEY(codigo),
    FOREIGN KEY (pais) REFERENCES PAIS(codigo)
);
--https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg
CREATE TABLE PORTO(
    codigo SERIAL NOT NULL,
    perfil INTEGER NOT NULL,
    nome VARCHAR(250) NOT NULL,
    descr VARCHAR(250) NOT NULL,
    img VARCHAR(250) NOT NULL DEFAULT 'https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg',
    ativo BOOLEAN NOT NULL DEFAULT true CHECK(ativo=true OR ativo=false),
    PRIMARY KEY(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo) 
);
CREATE TABLE PORTO_PARTICIPA(
    porto INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    FOREIGN KEY (porto) REFERENCES PORTO(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);
CREATE TABLE SELO(
    codigo SERIAL NOT NULL,
    porto INTEGER NOT NULL,
    texto VARCHAR(250) NOT NULL,
    img VARCHAR(250),
    ativo BOOLEAN NOT NULL DEFAULT true CHECK(ativo=true OR ativo=false),
    PRIMARY KEY(codigo),
    FOREIGN KEY (porto) REFERENCES PORTO(codigo)
);
CREATE TABLE SELOUSER(
    selo INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    dateVal TIMESTAMP NOT NULL,
    FOREIGN KEY (selo) REFERENCES SELO(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);
CREATE TABLE AMIGO(
    amigo INTEGER NOT NULL CHECK(amigo!=perfil),
    perfil INTEGER NOT NULL CHECK(amigo!=perfil),
    dateAceito TIMESTAMP NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT false CHECK(ativo=true OR ativo=false),
    FOREIGN KEY (amigo) REFERENCES PERFIL(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);
CREATE TABLE SOLICITACAO_AMIGO(
    amigo INTEGER NOT NULL CHECK(amigo!=perfil),
    perfil INTEGER NOT NULL CHECK(amigo!=perfil),
    dateEnvio TIMESTAMP NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT true CHECK(ativo=true OR ativo=false),
    FOREIGN KEY (amigo) REFERENCES PERFIL(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);
CREATE TABLE INTERACAO(
    codigo SERIAL NOT NULL,
    perfil INTEGER NOT NULL,
    perfil_posting INTEGER CHECK((post IS NULL AND porto IS NULL) OR perfil_posting IS NULL),
    porto INTEGER CHECK((post IS NULL AND perfil_posting IS NULL) OR porto IS NULL),
    post INTEGER CHECK((porto IS NULL AND perfil_posting IS NULL) OR post IS NULL),
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    texto VARCHAR(250) NOT NULL,
    isReaction BOOLEAN CHECK((post IS NOT NULL AND isSharing IS NULL) OR isReaction IS NULL),
    isSharing BOOLEAN  CHECK((post IS NOT NULL AND isReaction IS NULL) OR isSharing IS NULL),
    emote VARCHAR(250) CHECK(isReaction IS NOT NULL OR emote IS NULL),
    ativo BOOLEAN NOT NULL DEFAULT true CHECK(ativo=true OR ativo=false),
    PRIMARY KEY(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo),
    FOREIGN KEY (perfil_posting) REFERENCES PERFIL(codigo),
    FOREIGN KEY (porto) REFERENCES PORTO(codigo),
    FOREIGN KEY (post) REFERENCES INTERACAO(codigo)
);
CREATE TABLE CITACAO(
    codigo SERIAL NOT NULL,
    interacao INTEGER NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT true CHECK(ativo=true OR ativo=false),
    PRIMARY KEY(codigo),
    FOREIGN KEY (interacao) REFERENCES INTERACAO(codigo)
);
CREATE TABLE CITACAOPERFIL(
    citacao INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    FOREIGN KEY (citacao) REFERENCES CITACAO(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);
CREATE TABLE INTERACAO_ASSUNTO(
    assunto INTEGER NOT NULL,
    interacao INTEGER NOT NULL,
    FOREIGN KEY (assunto) REFERENCES ASSUNTO(codigo),
    FOREIGN KEY (interacao) REFERENCES INTERACAO(codigo)
);
--TESTES
--pais
INSERT INTO PAIS(nome) VALUES('Brasil');
--perfil
INSERT INTO PERFIL(pais,email,username,senha,genero,datanasc) VALUES(1,'abfn0905@gmail.com','testoman','$2y$10$vL5SKzTYBXYzYCHYrxF8P.ZACVQNwWD3n4txiC4CZFgpvWuGRqQ4u','M','2002-09-05');
INSERT INTO PERFIL(pais,email,username,senha,genero,datanasc) VALUES(1,'abfn@gmail.com','testoman2','$2y$10$vL5SKzTYBXYzYCHYrxF8P.ZACVQNwWD3n4txiC4CZFgpvWuGRqQ4u','M','2002-09-05');
--porto
INSERT INTO PORTO(perfil,nome,descr) VALUES(1,'PORTO DE TESTE','è isso ai parceria');
INSERT INTO PORTO(perfil,nome,descr) VALUES(1,'PORTO DE TESTE2','è isso ai parceria2');
INSERT INTO PORTO_PARTICIPA(porto,perfil) VALUES(1,1),(2,1),(1,2);
--amigos
INSERT INTO SOLICITACAO_AMIGO(perfil,amigo,dateEnvio,ativo) VALUES(1,2,CURRENT_TIMESTAMP,false);
INSERT INTO AMIGO(perfil,amigo,dateAceito,ativo) VALUES(1,2,CURRENT_TIMESTAMP,true);
UPDATE SOLICITACAO_AMIGO SET ATIVO=false WHERE PERFIL=1 AND AMIGO=2;
--selos
INSERT INTO SELO(porto,texto) VALUES(1,'SELO DE TESTE');
INSERT INTO SELO(porto,texto) VALUES(1,'SELO DE TESTE2');
INSERT INTO SELOUSER(perfil,selo,dateVal) VALUES(2,1,CURRENT_TIMESTAMP),(2,2,CURRENT_TIMESTAMP);
--interacao post
INSERT INTO INTERACAO(perfil,porto,texto) VALUES
(1,1,'POST DE TESTE EM PORTO DE TESTE'),--1
(2,1,'POST DE TESTE EM PORTO DE TESTE'),--2
(1,2,'POST DE TESTE2.1 EM PORTO DE TESTE2'),--3
(2,2,'POST DE TESTE2.2 EM PORTO DE TESTE2');--4
--interacao comentarios
INSERT INTO INTERACAO(perfil,post,texto) values 
(1,1,'comentario em no post de teste'),--5
(1,5,'comentario no comentario do no post de teste')--6
;
INSERT INTO INTERACAO(perfil,perfil_posting,texto) values 
(1,1,'POST NO PERFIL'),--7
(1,2,'POST NO PERFIL2')--8
;
--REAÇÕES
INSERT INTO INTERACAO(perfil,post,texto,isReaction,emote) VALUES
(1,6,'Like',true,'https://en.wikipedia.org/wiki/Facebook_like_button#/media/File:Facebook_Thumb_icon.svg');
--COMPARTILHAMENTO
INSERT INTO INTERACAO(perfil,post,texto,isSharing) VALUES(1,2,'compartilhou ',true);












