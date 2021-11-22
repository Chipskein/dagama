DROP TABLE IF EXISTS INTERACAO_ASSUNTO ;
DROP TABLE IF EXISTS INTERACAO;
DROP TABLE IF EXISTS CITACAOPERFIL;
DROP TABLE IF EXISTS CITACAO;
DROP TABLE IF EXISTS solicitacao_amigo;
DROP TABLE IF EXISTS AMIGO;
DROP TABLE IF EXISTS SELOUSER;
DROP TABLE IF EXISTS SELO;
DROP TABLE IF EXISTS PORTO_PARTICIPA;
DROP TABLE IF EXISTS PORTO;
DROP TABLE IF EXISTS PERFIL;
DROP TABLE IF EXISTS ASSUNTO;
DROP TABLE IF EXISTS CIDADE;
DROP TABLE IF EXISTS UF;
DROP TABLE IF EXISTS PAIS;

--Alterar todas INTEGER --> SERIAL caso for Postgresql
CREATE TABLE PAIS(
    codigo  INTEGER NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    PRIMARY KEY(codigo)
);
CREATE TABLE UF(
    codigo  INTEGER NOT NULL,
    pais    INTEGER NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    PRIMARY KEY(codigo),
    FOREIGN KEY (pais) REFERENCES PAIS(codigo) 

);
CREATE TABLE CIDADE(
    codigo  INTEGER NOT NULL,
    uf      INTEGER NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    PRIMARY KEY(codigo),
    FOREIGN KEY (uf) REFERENCES UF(codigo) 
);

CREATE TABLE ASSUNTO(
    codigo  INTEGER NOT NULL,
    nome    VARCHAR(250) NOT NULL,
    PRIMARY KEY(codigo)
);
CREATE TABLE PERFIL(
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
    FOREIGN KEY (cidade) REFERENCES cidade(codigo)
);
--https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg
CREATE TABLE PORTO(
    codigo INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    nome VARCHAR(250) NOT NULL,
    descr VARCHAR(250) NOT NULL,
    img VARCHAR(250) NOT NULL DEFAULT 'https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg',
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
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
    codigo INTEGER NOT NULL,
    porto INTEGER NOT NULL,
    texto VARCHAR(250) NOT NULL,
    img VARCHAR(250),
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo),
    FOREIGN KEY (porto) REFERENCES PORTO(codigo)
);
CREATE TABLE SELOUSER(
    selo INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    dateVal DATETIME NOT NULL,
    FOREIGN KEY (selo) REFERENCES SELO(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);
CREATE TABLE AMIGO(
    amigo INTEGER NOT NULL CHECK(amigo!=perfil),
    perfil INTEGER NOT NULL CHECK(amigo!=perfil),
    dateAceito DATETIME NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 0 CHECK(ativo=1 OR ativo=0),
    FOREIGN KEY (amigo) REFERENCES PERFIL(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);
CREATE TABLE solicitacao_amigo(
    amigo INTEGER NOT NULL CHECK(amigo!=perfil),
    perfil INTEGER NOT NULL CHECK(amigo!=perfil),
    dateEnvio DATETIME NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    FOREIGN KEY (amigo) REFERENCES PERFIL(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo)
);
CREATE TABLE INTERACAO(
    codigo INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    perfil_posting INTEGER CHECK((post IS NULL AND porto IS NULL) OR perfil_posting IS NULL),
    porto INTEGER CHECK((post IS NULL AND perfil_posting IS NULL) OR porto IS NULL),
    post INTEGER CHECK((porto IS NULL AND perfil_posting IS NULL) OR post IS NULL),
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    texto VARCHAR(250) NOT NULL,
    isReaction BOOLEAN CHECK((post IS NOT NULL AND isSharing IS NULL) OR isReaction IS NULL),
    isSharing BOOLEAN  CHECK((post IS NOT NULL AND isReaction IS NULL) OR isSharing IS NULL),
    emote VARCHAR(250) CHECK(isReaction IS NOT NULL OR emote IS NULL),
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(codigo),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo),
    FOREIGN KEY (perfil_posting) REFERENCES PERFIL(codigo),
    FOREIGN KEY (porto) REFERENCES PORTO(codigo),
    FOREIGN KEY (post) REFERENCES INTERACAO(codigo)
);
CREATE TABLE CITACAO(
    interacao INTEGER NOT NULL,
    perfil INTEGER NOT NULL,
    ativo BOOLEAN NOT NULL DEFAULT 1 CHECK(ativo=1 OR ativo=0),
    PRIMARY KEY(interacao, perfil),
    FOREIGN KEY (perfil) REFERENCES PERFIL(codigo),
    FOREIGN KEY (interacao) REFERENCES INTERACAO(codigo)
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
INSERT INTO UF(nome,pais) VALUES('RS',1);
INSERT INTO CIDADE(nome,uf) VALUES('Rio Grande',1);
INSERT INTO CIDADE(nome,uf) VALUES('Pelotas',1);

--assuntos
insert into assunto (nome) values ('Banco de dados'), ('Homem aranha sem volta pra casa'), ('Meta'), ('cópia do facebook');

--perfis
INSERT INTO PERFIL(codigo, cidade, email, senha, genero, username, img, datanasc, dataregis, ativo) VALUES 
(1, 1, 'abfn0905@gmail.com','$2y$10$vL5SKzTYBXYzYCHYrxF8P.ZACVQNwWD3n4txiC4CZFgpvWuGRqQ4u', 'M', 'testoman','https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif', '2002-09-05',  '2021-11-12 16:55:11', 1), 
(2, 1, 'abfn@gmail.com', '$2y$10$vL5SKzTYBXYzYCHYrxF8P.ZACVQNwWD3n4txiC4CZFgpvWuGRqQ4u', 'M', 'testoman2', 'https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif', '2002-09-05', '2021-11-12 16:55:11', 0), 
(3, 1, 'bruno.nascimento@aluno.riogrande.ifrs.edu', '$2y$10$uCijiBJPgN0LTo3DgSEKgOFgaVDiydGgfsqhtZZCIUFj11nb6diiu', 'M', 'chipskein', 'https://drive.google.com/uc?id=1oQjH_htgpdohildhLoo0zU5bGXCLhE1e', '2002-09-05', '2021-11-12 16:55:42', 0), 
(4, 1, 'biluteia@gmail.com','$2y$10$feaKLLXbSs0x/8TGxHwwdOmGqRN6OKefvO.EQil5aEhlTWX/CcK1S','M','email de teste','https://drive.google.com/uc?id=1gv9tSM4qCF2iILmsEYeWIcxGKwApgVoW','2009-09-17', '2021-11-12 22:48:26', 0),
(5, 1, 'silvioquintana1@hotmail.com', '$2y$10$cM7wy6iszu32GTEergrkFuUPtcRMMVsx2HCDceP2QLK2UEuVJLgWS', 'M', 'SilMusk', 'https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif','2002-04-26','2021-11-13 01:28:42',1), 
(6, 1, 's@hotmail.com', '$2y$10$n6Tmzm0rK7erTRI3WQkpweJ9OSbqTNpMTRqL8jmYuNHLui/7pks3W', 'M', 'SilMusk1', 'https://drive.google.com/uc?id=1BRztifvvBzsQFGMA7QqG3RqIidf0gJxx', '2002-04-26', '2021-11-13 01:29:26', 0),
(7, 1, 'victortavamaral@gmail.com', '$2y$10$y71PdaTg0ZjjGHLTlDwcAOkw/pwqP9L4zQGYACoj6fpTqiPwrPmr6', 'M', 'vitão', 'https://drive.google.com/uc?id=1LtTnddiIaufdBo0lVCxEufGAtmIK7Igz', '2002-04-22', '2021-11-18 00:11:41', 1),
(8, 1, 'victortavaresjedi150@gmail.com', '$2y$10$0F0ZO3hozWc0SK1BSgmAQOnF23A6G5N5m38/AJKw8zyIhzaqjeppW', 'M', 'bigSmoke2002', 'https://drive.google.com/uc?id=1ZRnJd2aTlnKrzAENQB3MmiRqVHpA9o-l', '2002-04-22', '2021-11-18 15:26:12', 1),
(9, 1, 'victortavaresjedi2500@gmail.com', '$2y$10$x6PdBr36AJdwM8UtG5uy9eOeF0DwL58/3v6r5uwug3.Ja1FAxP9IS', 'F', 'JillValentine0928', 'https://drive.google.com/uc?id=1r-CuVOpdPCDSKQ1dqdVaHhJRqehZg0Fg', '2002-04-22', '2021-11-18 16:12:34', 1),
(10, 1, 'emaildeteste1@gmail.com', '$2y$10$vMHIbdpBEKRk81cahDZeS.5aVhJTdY/nVJhU.u8gs3w7elKzNnfum', 'M', 'Gaunter O Dimm', 'https://drive.google.com/uc?export=download&id=1-C9XOdcdiW4W149HolzKe39-hgxfl5xj', ' 2002-04-22', '2021-11-19 12:33:04', 1);

--porto
INSERT INTO PORTO(perfil,nome,descr) VALUES(1, 'Devs dagama', 'è isso ai parceria');
--INSERT INTO PORTO(perfil,nome,descr) VALUES(1,'PORTO DE TESTE2','è isso ai parceria2');
--INSERT INTO PORTO_PARTICIPA(porto,perfil) VALUES(1,1),(2,1),(1,2);

--amigos
--INSERT INTO solicitacao_amigo(perfil,amigo,dateEnvio,ativo) VALUES(1,2,CURRENT_TIMESTAMP,0);
-- INSERT INTO AMIGO(perfil,amigo,dateAceito,ativo) VALUES(1,2,CURRENT_TIMESTAMP,1);
--UPDATE solicitacao_amigo SET ATIVO=0 WHERE PERFIL=1 AND AMIGO=2;

--selos
--INSERT INTO SELO(porto,texto) VALUES(1,'SELO DE TESTE');
--INSERT INTO SELO(porto,texto) VALUES(1,'SELO DE TESTE2');
--INSERT INTO SELOUSER(perfil,selo,dateVal) VALUES(2,1,CURRENT_TIMESTAMP),(2,2,CURRENT_TIMESTAMP);

--interacao post
INSERT INTO INTERACAO(perfil, texto) VALUES
(7, 'TEstando postar nessa bagaça'),--1
(7, 'Sla mermão'),--2
(7, 'Oh rapaiz'),--3
(8, 'Vishhh');--4

--interacao comentarios
--INSERT INTO INTERACAO(perfil,post,texto) values 
--(1,1,'comentario em no post de teste'),--5
--(1,5,'comentario no comentario do no post de teste')--6
--;
--INSERT INTO INTERACAO(perfil,perfil_posting,texto) values 
--(1,1,'POST NO PERFIL'),--7
--(1,2,'POST NO PERFIL2')--8
--;

--REAÇÕES
--INSERT INTO INTERACAO(perfil,post,texto,isReaction,emote) VALUES
--(1,6,'Like',1,'https://en.wikipedia.org/wiki/Facebook_like_button#/media/File:Facebook_Thumb_icon.svg');
--COMPARTILHAMENTO
--INSERT INTO INTERACAO(perfil,post,texto,isSharing) VALUES(1,2,'compartilhou ',1);

