-- Pegar locais
select cidade.codigo as codCidade, cidade.nome as nomeCidade, uf.codigo as codUf, uf.nome as nomeUf, pais.codigo as codPais, pais.nome as nomePais from cidade
    join uf on cidade.uf = uf.codigo
    join pais on uf.pais = pais.codigo;

-- pegar assuntos
select * from assunto;

-- pegar pessoas e amigos
select codigo, username, img from perfil where ativo = 1;

-- Sugerir amigos ao usuário U, considerando que, se U e V não são amigos mas possuem no mínimo A assuntos em comum entre os B assuntos mais comentados por cada um nos últimos M meses, V deve ser sugerido como amigo de U

-- U - 7
-- V - any
-- A - 1
-- B - 5
-- M - 3 meses

select tmp1.codigo, tmp1.username, tmp1.img, tmp1.enviado, 
    case
        when solicitacao_amigo.perfil is null then 'false'
        when solicitacao_amigo.perfil is not null then 'true'
    end as recebido
from (
    select perfil.codigo, perfil.username, perfil.img,
        case
            when solicitacao_amigo.amigo is null or solicitacao_amigo.ativo == 0 then 'false'
            when solicitacao_amigo.amigo is not null or solicitacao_amigo.ativo == 1 then 'true'
        end as enviado, 
        1 as camadas from perfil
        join (select perfil.codigo as user, tmp1.assuntoCodigo, tmp1.assuntoNome, tmp1.qtd, count(*) as qtd2 from perfil
                join (select interacao.perfil as perfil, assunto.codigo as assuntoCodigo, assunto.nome as assuntoNome, count(*) as qtd from interacao
                    join INTERACAO_ASSUNTO on interacao.codigo = INTERACAO_ASSUNTO.interacao
                    join assunto on interacao_assunto.assunto = assunto.codigo
                where
                    datetime(interacao.data) between datetime('now','start of month', '-$M months') and datetime('now')
                group by interacao.perfil, assunto.codigo
                having 
                    qtd > $A
                order by qtd desc) as tmp1 on tmp1.perfil = perfil.codigo
            where 
                tmp1.qtd <= $B
            group by perfil.codigo) 
            as tmp2 on perfil.codigo = tmp2.user
        left join solicitacao_amigo on solicitacao_amigo.amigo = perfil.codigo and solicitacao_amigo.perfil = $U
    where
        perfil.codigo != $U
    group by perfil.codigo
    having
        tmp2.assuntoCodigo in (select tmp1.assuntoCodigo from perfil
            join (select interacao.perfil as perfil, assunto.codigo as assuntoCodigo, assunto.nome as assuntoNome, count(*) as qtd from interacao
                join INTERACAO_ASSUNTO on interacao.codigo = INTERACAO_ASSUNTO.interacao
                join assunto on interacao_assunto.assunto = assunto.codigo
            where
                datetime(interacao.data) between datetime('now','start of month', '-$M months') and datetime('now')
            group by interacao.perfil, assunto.codigo
            having 
                qtd > $A
            order by qtd desc) as tmp1 on tmp1.perfil = perfil.codigo
        where
            perfil.codigo = $U
        limit $B)
    union
    select perfil.codigo, perfil.username, perfil.img,
        case
            when solicitacao_amigo.amigo is null or solicitacao_amigo.ativo == 0 then 'false'
            when solicitacao_amigo.amigo is not null or solicitacao_amigo.ativo == 1 then 'true'
        end as enviado, 
        2 as camadas from perfil
        left join solicitacao_amigo on solicitacao_amigo.amigo = perfil.codigo and solicitacao_amigo.perfil = $U
    where 
        perfil.codigo != $U and
        perfil.codigo not in (
        select perfil.codigo from perfil
            join (select perfil.codigo as user, tmp1.assuntoCodigo, tmp1.assuntoNome, tmp1.qtd, count(*) as qtd2 from perfil
                    join (select interacao.perfil as perfil, assunto.codigo as assuntoCodigo, assunto.nome as assuntoNome, count(*) as qtd from interacao
                        join INTERACAO_ASSUNTO on interacao.codigo = INTERACAO_ASSUNTO.interacao
                        join assunto on interacao_assunto.assunto = assunto.codigo
                    where
                        datetime(interacao.data) between datetime('now','start of month', '-$M months') and datetime('now')
                    group by interacao.perfil, assunto.codigo
                    having 
                        qtd > $A
                    order by qtd desc) as tmp1 on tmp1.perfil = perfil.codigo
                where 
                    tmp1.qtd <= $B
                group by perfil.codigo) 
                as tmp2 on perfil.codigo = tmp2.user
        where
            perfil.codigo != $U
        group by perfil.codigo
        having
            tmp2.assuntoCodigo in (select tmp1.assuntoCodigo from perfil
                join (select interacao.perfil as perfil, assunto.codigo as assuntoCodigo, assunto.nome as assuntoNome, count(*) as qtd from interacao
                    join INTERACAO_ASSUNTO on interacao.codigo = INTERACAO_ASSUNTO.interacao
                    join assunto on interacao_assunto.assunto = assunto.codigo
                where
                    datetime(interacao.data) between datetime('now','start of month', '-$M months') and datetime('now')
                group by interacao.perfil, assunto.codigo
                having 
                    qtd > $A
                order by qtd desc) as tmp1 on tmp1.perfil = perfil.codigo
            where
                perfil.codigo = $U           
            limit $B)   
    ) 
    group by perfil.codigo
    order by camadas asc) as tmp1
    left join solicitacao_amigo on solicitacao_amigo.perfil = tmp1.codigo and solicitacao_amigo.amigo = $U
where 
    solicitacao_amigo.perfil is null and
    tmp1.enviado != 'true' and
    tmp1.codigo not in (
        select case
                when amigo.perfil = perfil.codigo then amigo.amigo
                when amigo.amigo = perfil.codigo then amigo.perfil
            end as amigo
        from perfil
            join amigo on perfil.codigo = amigo.perfil or perfil.codigo = amigo.amigo
        where perfil.codigo = $U
    ) and tmp1.codigo not in (select codigo from perfil where ativo = 0)
limit $limit offset $offset;

-- Get solicitacao amizade + amizades
select solicitacao_amigo.perfil, solicitacao_amigo.amigo, solicitacao_amigo.dateEnvio, solicitacao_amigo.ativo, amigo.perfil as otherPerfil, amigo.amigo as otherAmigo, amigo.ativo as otherAtivo from solicitacao_amigo
    left join amigo on 
        (solicitacao_amigo.perfil = amigo.perfil and solicitacao_amigo.amigo = amigo.amigo) or 
        (solicitacao_amigo.amigo = amigo.perfil and solicitacao_amigo.perfil = amigo.amigo)
where 
    solicitacao_amigo.perfil = 7 and
    solicitacao_amigo.amigo not in (
        select codigo from perfil where ativo = 0
    )

-- Get dos PORTOS
select porto.codigo as codigo, porto.nome as nome, porto.descr as descr, porto.img as img, 
    case 
        when porto.perfil = 4 or (porto_participa.perfil = 4 and porto_participa.ativo = 1) then true
        else false
    end as participa,
    case 
        when porto.perfil = 4 then true
        else false
    end as owner
from porto
    left join porto_participa on porto.codigo = porto_participa.porto
where 
    porto.ativo = 1 and
    porto.codigo = 5
group by porto.codigo
order by porto_participa.dataregis desc

-- Get userporto
select count(*) as total from porto
where 
    porto.ativo = 1 and
    porto.perfil = 4;

-- Comandos para entrar e sair do porto
select case
    when porto_participa.ativo = 0 then 'off'
    when porto_participa.ativo = 1 then 'on'
    end as participa
from porto_participa where perfil = 4 and porto = 1;

insert into porto_participa (perfil, porto) values (4, 1);

update porto_participa set ativo = 1, dataregis = CURRENT_TIMESTAMP where perfil = 4 and porto = 1;


-- Get dos POSTS
-- 9) Mostrar um feed com as interações do usuário U, OK
    -- com interações de outros usuários relacionadas a estas interações; OK
    -- as interações de outros usuários que citam o usuário U; ok
    -- as interações dos amigos do usuário U;  ok
    -- as postagens dos grupos que o usuário U participa ok
-- da interação mais recente para a mais antiga, com paginação e no máximo E elementos por página

select
    interacao.codigo as codInteracao, 
    interacao.post as codPost, 
    interacao.isReaction as isReaction, 
    interacao.texto as textoPost, 
    interacao.data as dataPost,
    interacao.isSharing as isSharing, 
    interacao.emote as emote,
    interacao.ativo as ativo,
    porto.codigo as codPorto,
    porto.nome as nomePorto,
    perfil.codigo as codPerfil, 
    perfil.username as nomePerfil,
    perfil.img as iconPerfil
from (
        select
            case
                when interacao.post is null then interacao.codigo
                when interacao.isSharing is not null then interacao.codigo
                else interacao.post
            end as codPost,
            interacao.data
        from interacao
            join perfil on interacao.perfil = perfil.codigo
            left join porto on porto.codigo = interacao.porto
        where
            interacao.ativo = 1 and
            interacao.perfil = $user
        union
        select 
            case
                when interacao.post is null then interacao.codigo
                when interacao.isSharing is not null then interacao.codigo
                else interacao.post
            end as codPost,
            interacao.data
        from interacao 
        where
            post in (select codigo from interacao where perfil = $user)
        union
        select 
            case
                when interacao.post is null then interacao.codigo
                when interacao.isSharing is not null then interacao.codigo
                else interacao.post
            end as codPost,
            interacao.data 
        from interacao
            join citacao on interacao.codigo = citacao.interacao
        where citacao.perfil = $user
        union
        --amigos
        select
            case
                when interacao.post is null then interacao.codigo
                when interacao.isSharing is not null then interacao.codigo
                else interacao.post
            end as codPost,
            interacao.data
        from interacao
            join perfil on interacao.perfil = perfil.codigo
            left join porto on porto.codigo = interacao.porto
        where
            interacao.ativo = 1 and
            interacao.perfil in (
            select
                case
                    when perfil = $user then amigo
                    when amigo = $user then perfil
                end as amigo
            from amigo)
        union
        select 
            case
                when interacao.post is null then interacao.codigo
                when interacao.isSharing is not null then interacao.codigo
                else interacao.post
            end as codPost,
            interacao.data
        from interacao 
        where
            post in (select codigo from interacao where perfil = $user)
        union
        select 
            case
                when interacao.post is null then interacao.codigo
                when interacao.isSharing is not null then interacao.codigo
                else interacao.post
            end as codPost,
            interacao.data 
        from interacao
            join citacao on interacao.codigo = citacao.interacao
        where citacao.perfil in (
            select
                case
                    when perfil = $user then amigo
                    when amigo = $user then perfil
                end as amigo
            from amigo)
        union
        -- grupo
        select
            case
                when interacao.post is null then interacao.codigo
                when interacao.isSharing is not null then interacao.codigo
                else interacao.post
            end as codPost,
            interacao.data 
        from interacao
            join porto on interacao.porto = porto.codigo
            left join porto_participa on porto_participa.porto = porto.codigo
        where porto_participa.perfil = $user or porto.perfil = $user) as tmp1
    join interacao on tmp1.codPost = interacao.codigo
    join perfil on interacao.perfil = perfil.codigo
    left join porto on interacao.porto = porto.codigo
group by codPost
order by tmp1.data desc
limit $limit offset $offset


-- Pegando todos os posts relacionados a um post
select codigo from interacao
where 
    post = 1 or codigo in 
        (select codigo from interacao
        where post = 1 or codigo)


--tem que pegar por fora os assuntos, as citações e intercações

select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
    left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
    left join assunto on interacao_assunto.assunto = assunto.codigo
where
    interacao.ativo = 1

-- Insert interacao
insert into amigo (perfil, amigo, dateAceito) values (4, 1, CURRENT_TIMESTAMP);
insert into amigo (perfil, amigo, dateAceito) values (4, 3, CURRENT_TIMESTAMP);
insert into amigo (perfil, amigo, dateAceito) values (4, 5, CURRENT_TIMESTAMP);

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (5, null, null, null, 'Conta 5 fazendo post normal', null, null, null); 
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (4, null, null, 1, 'Conta 4 comentando no post 1', null, null, null); 
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (1, null, null, 1, 'Conta 1 compartilhando post 1', null, 1, null); 
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (4, null, null, null, 'Conta 4 fazendo post normal', null, null, null);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (3, null, null, 4, 'Conta 3 compartilhando post 4', null, 1, null); 
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (4, null, null, 5, 'Conta 5 compartilhando post 5', null, 1, null); 
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (4, null, null, null, 'Conta 4 fazendo post normal: Batman is a superhero who appears in American comic books published by DC Comics. The character was created by artist Bob Kane and writer Bill Finger...', null, null, null); 
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (5, null, null, 7, 'Conta 5 compartilhando post 7: Spider-Man is a superhero created by writer-editor Stan Lee and writer-artist Steve Ditko. He first appeared in the anthology comic book Amazing Fantasy #15 in the Silver Age of Comic Books...', null, 1, null); 


-- Get dos amigos
select perfil.codigo, case 
        when amigo.perfil = perfil.codigo then amigo.amigo
        when amigo.amigo = perfil.codigo then amigo.perfil
    end as amigoCod,
    amigo.dateAceito,
    tmp1.codigo as codAmigo,
    tmp1.username as nameAmigo,
    tmp1.img as imgAmigo,
    (select count(*) from amigo where amigo = 4 or perfil = 4) as qtdAmigos
from perfil 
    join amigo on perfil.codigo = amigo.perfil or amigo.amigo
    join (select * from perfil) as tmp1 on tmp1.codigo = amigoCod
where perfil.codigo = 4 and amigo.ativo = 1
order by amigo.dateAceito desc;


select 
    interacao.codigo as codInteracao, 
    interacao.post as codPost, 
    interacao.isReaction as isReaction, 
    interacao.texto as textoPost, 
    interacao.data as dataPost, 
    interacao.isSharing as isSharing, 
    interacao.emote as emote,
    interacao.ativo as ativo,
    perfil.codigo as codPerfil, perfil.username as nomePerfil, perfil.img as iconPerfil
from interacao
    join perfil on interacao.perfil = perfil.codigo
where 
    interacao.ativo = 1 and
    (interacao.perfil_posting = 4 or interacao.perfil = 4)
order by interacao.data desc
limit 10 offset 0;

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (5, 4, null, null, 'Conta 5 postando no perfil 4: Spider-Man is a superhero created by writer-editor Stan Lee and writer-artist Steve Ditko. He first appeared in the anthology comic book Amazing Fantasy #15 in the Silver Age of Comic Books...', null, null, null); 

select 
    interacao.codigo as codInteracao, 
    interacao.post as codPost, 
    interacao.isReaction as isReaction, 
    interacao.texto as textoPost, 
    interacao.data as dataPost, 
    interacao.isSharing as isSharing, 
    interacao.emote as emote,
    interacao.ativo as ativo,
    perfil.codigo as codPerfil, perfil.username as nomePerfil, perfil.img as iconPerfil
from interacao
    join porto on interacao.porto = porto.codigo
    join perfil on interacao.perfil = perfil.codigo
where 
    interacao.ativo = 1 and
    porto.codigo = 3
order by interacao.data desc
limit 10 offset 0;

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (5, null, 3, null, 'Conta 5 postando no porto 3: Spider-Man is a superhero created by writer-editor Stan Lee and writer-artist Steve Ditko. He first appeared in the anthology comic book Amazing Fantasy #15 in the Silver Age of Comic Books...', null, null, null); 
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (4, null, 3, null, 'Conta 5 postando no porto 3: Spider-Man is a superhero created by writer-editor Stan Lee and writer-artist Steve Ditko. He first appeared in the anthology comic book Amazing Fantasy #15 in the Silver Age of Comic Books...', null, null, null); 
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (1, null, 3, null, 'Conta 5 postando no porto 3: Spider-Man is a superhero created by writer-editor Stan Lee and writer-artist Steve Ditko. He first appeared in the anthology comic book Amazing Fantasy #15 in the Silver Age of Comic Books...', null, null, null); 

select count(interacao.perfil), pais.nome from interacao join perfil on interacao.perfil= perfil.codigo join cidade on perfil.cidade = cidade.codigo join uf on cidade.uf= uf.codigo join pais on uf.pais = pais.codigo where perfil.genero = "M" and pais.nome="Brazil" and date(interacao.data) between date('now','-2 years') and date('now') and date(perfil.datanasc) between date('now','-20 years') and date('now', '-0 years');

select 
porto.codigo as codPorto,
perfil.username as nomePart,
perfil.img as imgPart
from porto
    left join porto_participa on porto.codigo = porto_participa.porto
    left join perfil on porto_participa.perfil = perfil.codigo
where 
    porto_participa.ativo = 1 and
    porto.codigo = 3

select 
    porto.codigo as codigo, 
    porto.nome as nome, 
    porto.descr as descr, 
    porto.img as img, 
    perfil.codigo as codigoAdm, 
    perfil.username as nomeAdm, 
    perfil.img as imgAdm, 
    case 
        when porto.perfil = $user or (porto_participa.perfil = $user and porto_participa.ativo = 1) then true
        else false
    end as participa,
    case 
        when porto.perfil = $user then true
        else false
    end as owner
from porto
    join perfil on porto.perfil = perfil.codigo
    left join porto_participa on porto.codigo = porto_participa.porto
where 
    porto.ativo = 1 and
    porto.codigo = $porto
group by porto.codigo
order by porto_participa.dataregis desc

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote) values (4, null, 2, null, 'Quero ver terminar esse bangu até domingo...', 1, null, 'sad'); 

select interacao, perfil.codigo, perfil.username from citacao join perfil on perfil.codigo = citacao.perfil;

insert into assunto (nome) values ('Orra banco'), ('Ajuda ai parceria'), ('Foda');
insert into interacao_assunto (assunto, interacao) values (1, 13), (2, 13), (3,13);

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (5, null, null, 13, 'Mammaaaa, just killed a mannnn', 1, null, 'sad', 2);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (5, null, null, 14, 'Whaaat?', null, null, null, null);

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values 
                    (5, null, null, 1, '', 1, null, 'curtir', null),
                    (3, null, null, 1, '', 1, null, 'curtir', null),
                    (4, null, null, 2, '', 1, null, 'curtir', null),
                    (4, null, null, 2, '', 1, null, 'curtir', null),
                    (4, null, null, 5, '', 1, null, 'curtir', null),
                    (3, null, null, 4, '', 1, null, 'curtir', null),
                    (4, null, null, 6, '', 1, null, 'curtir', null),
                    (3, null, null, 7, '', 1, null, 'curtir', null),
                    (2, null, null, 2, '', 1, null, 'curtir', null),
                    (4, null, null, 1, '', 1, null, 'curtir', null),
                    (1, null, null, 2, '', 1, null, 'curtir', null),
                    (1, null, null, 3, '', 1, null, 'curtir', null),
                    (1, null, null, 4, '', 1, null, 'curtir', null),
                    (1, null, null, 5, '', 1, null, 'curtir', null),
                    (4, null, null, 2, '', 1, null, 'curtir', null),
                    (4, null, null, 2, '', 1, null, 'curtir', null),
                    (5, null, null, 1, '', 1, null, 'curtir', null);
//10) Mostrar quantos usuários receberam mais de C curtidas em uma postagem, em menos de H horas após a postagem, no país P nos últimos D dias
select 
perfil.username,
interacao.* 
from perfil 
join interacao on perfil.codigo=interacao.perfil
where 
interacao.isReaction is not null and 
interacao.post is not null
;

select 
    porto.codigo as codigo, 
    porto.nome as nome, 
    porto.descr as descr, 
    porto.img as img, 
    tmp1.participa as participa 
from porto
    left join (
        select case 
                when (porto.perfil = perfil.codigo) or (porto_participa.perfil = perfil.codigo and porto_participa.ativo = 1) then true
                else false
            end as participa,
            porto.codigo as porto
        from porto 
            left join porto_participa on porto.codigo = porto_participa.porto
            left join perfil on (porto.perfil = perfil.codigo) or (porto_participa.perfil = perfil.codigo and porto_participa.ativo = 1)
        where 
            perfil.codigo = 5
    ) as tmp1 on porto.codigo = tmp1.porto
where 
    porto.ativo = 1

--Mostrar quantos usuários receberam mais de C curtidas em uma postagem, em menos de H horas após a postagem, no país P nos últimos D dias
 --ex 2 curtidas

--  f) Qual o nome do usuário que postou a postagem que teve mais curtidas no Brasil nos últimos 60 dias?

-- C = 3 curtidas
-- H = 2 hrs
-- P = Brasil - 31
-- D = 30 dias

select perfil.codigo, perfil.username, perfil.img from perfil
where perfil.codigo in (
    select perfil.codigo from interacao 
        join perfil on interacao.perfil = perfil.codigo
        left join (select post, isReaction, emote, data, local from interacao) as reacoes on interacao.codigo = reacoes.post
        left join cidade on reacoes.local = cidade.codigo --left join pois pode nao ter local
        left join uf on cidade.uf = uf.codigo
        left join pais on uf.pais = pais.codigo
    where 
        reacoes.isReaction = 1 and 
        reacoes.emote = 'curtir' and
        pais.codigo = 31 and
        date(reacoes.data) between date('now', '-60 days', 'localtime') and date('now', 'localtime') and
        date(reacoes.data) between date(interacao.data, 'localtime') and date(interacao.data, '+2 hours', 'localtime')
    group by perfil.codigo
    having count(*) > 3
    order by count(*) desc  
);
    
--11) Mostrar qual faixa etária mais interagiu às postagens do grupo G nos últimos D dias

select case
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) < 18 then '- 18'    
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 18 and 21 then '18-21'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 21 and 25 then '21-25'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 25 and 30 then '25-30'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 30 and 36 then '30-36'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 36 and 43 then '36-43'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 43 and 51 then '43-51'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 51 and 60 then '51-60'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) > 60 then '60-'
end as faixaEtaria, count(*) as qtdReacoes
from interacao
    join perfil on interacao.perfil = perfil.codigo
where 
    date(interacao.data, 'localtime') between date('now', '-60 days', 'localtime') and date('now', 'localtime') and
    interacao.codigo in (
        select interacao.codigo from interacao
            join porto on interacao.porto = porto.codigo
        where porto.codigo = 1
    )
group by faixaEtaria
having qtdReacoes = (
        select qtdReacoes from (
            select case
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) < 18 then  '- 18'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 18 and 21 then '18-21'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 21 and 25 then '21-25'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 25 and 30 then '25-30'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 30 and 36 then '30-36'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 36 and 43 then '36-43'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 43 and 51 then '43-51'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 51 and 60 then '51-60'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) > 60 then '60-'
            end as faixaEtaria, count(*) as qtdReacoes
            from interacao
                join perfil on interacao.perfil = perfil.codigo
            where 
                interacao.codigo in (
                     select interacao.codigo from interacao
            join porto on interacao.porto = porto.codigo
        where porto.codigo = 1
                ) and
                date(interacao.data, 'localtime') between date('now', '-60 days', 'localtime') and date('now', 'localtime')
            group by faixaEtaria
            order by qtdReacoes desc
        )
        limit 1
    )
order by qtdReacoes desc;

--12) Mostrar quais os top T assuntos mais interagidos por mês no país P nos últimos M meses
--13) Mostrar qual assunto permaneceu por mais meses consecutivos entre os top T mais interagidos por mês no país P nos últimos M meses

select 
result.assunto as assunto,count(*) as total
from
(
    select 
    assunto.nome as assunto, 
    case strftime('%m',interacao.data)
    when '01' then 'janeiro' 
    when '02' then 'fevereiro' 
    when '03' then 'março' 
    when '04' then 'abril' 
    when '05' then 'maio' 
    when '06' then 'junho' 
    when '07' then 'julho' 
    when '08' then 'agosto' 
    when '09' then 'setembro' 
    when '10' then 'outubro' 
    when '11' then 'novembro' 
    when '12' then 'dezembro' 
    end as mes,
    count(assunto.nome) as qt
    from interacao 
        join interacao_assunto on interacao.codigo = interacao_assunto.interacao 
        join assunto on interacao_assunto.assunto = assunto.codigo
        join cidade on interacao.local = cidade.codigo
        join uf on cidade.uf = uf.codigo
        join pais on uf.pais = pais.codigo
        where 
        pais.codigo = 31 
        and  date(interacao.data, 'localtime') between date('now', '-3000 months', 'localtime') and date('now', 'localtime')
    group by strftime('%m',interacao.data),assunto.nome
    having count(*) in 
    (
        select
        distinct
        count(assunto.nome) 
        from interacao 
            join interacao_assunto on interacao.codigo = interacao_assunto.interacao 
            join assunto on interacao_assunto.assunto = assunto.codigo
            join cidade on interacao.local = cidade.codigo
            join uf on cidade.uf = uf.codigo
            join pais on uf.pais = pais.codigo
            where 
            pais.codigo = 31 
            and  date(interacao.data, 'localtime') between date('now', '-3000 months', 'localtime') and date('now', 'localtime')
        group by strftime('%m',interacao.data),assunto.nome
        order by count(assunto.nome) desc
        limit 10
    )
    order by count(assunto.nome) desc
) as result
group by result.assunto
having count(*) = 
(
    select 
    count(*) as total
    from
    (
        select 
        assunto.nome as assunto, 
        case strftime('%m',interacao.data)
        when '01' then 'janeiro' 
        when '02' then 'fevereiro' 
        when '03' then 'março' 
        when '04' then 'abril' 
        when '05' then 'maio' 
        when '06' then 'junho' 
        when '07' then 'julho' 
        when '08' then 'agosto' 
        when '09' then 'setembro' 
        when '10' then 'outubro' 
        when '11' then 'novembro' 
        when '12' then 'dezembro' 
        end as mes,
        count(assunto.nome) as qt
        from interacao 
            join interacao_assunto on interacao.codigo = interacao_assunto.interacao 
            join assunto on interacao_assunto.assunto = assunto.codigo
            join cidade on interacao.local = cidade.codigo
            join uf on cidade.uf = uf.codigo
            join pais on uf.pais = pais.codigo
            where 
            pais.codigo = 31 
            and  date(interacao.data, 'localtime') between date('now', '-3000 months', 'localtime') and date('now', 'localtime')
        group by strftime('%m',interacao.data),assunto.nome
        having count(*) in 
        (
            select
            distinct
            count(assunto.nome) 
            from interacao 
                join interacao_assunto on interacao.codigo = interacao_assunto.interacao 
                join assunto on interacao_assunto.assunto = assunto.codigo
                join cidade on interacao.local = cidade.codigo
                join uf on cidade.uf = uf.codigo
                join pais on uf.pais = pais.codigo
                where 
                pais.codigo = 31 
                and  date(interacao.data, 'localtime') between date('now', '-3000 months', 'localtime') and date('now', 'localtime')
            group by strftime('%m',interacao.data),assunto.nome
            order by count(assunto.nome) desc
            limit 10
        )
        order by count(assunto.nome) desc
    ) as result
    group by result.assunto
    limit 1
)
;









select 
    strftime('%Y',interacao.data) as ano,
    case strftime('%m',interacao.data)
        when '01' then 'janeiro'
        when '02' then 'fevereiro'
        when '03' then 'marco'
        when '04' then 'abril'
        when '05' then 'maio'
        when '06' then 'junho'
        when '07' then 'julho'
        when '08' then 'agosto'
        when '09' then 'setembro'
        when '10' then 'outubro'
        when '11' then 'novembro'
        when '12' then 'dezembro'
    end as mes_nome,
    assunto.nome,
    count(*) as por_mes
    from interacao
    join cidade on interacao.local=cidade.codigo
    join uf on uf.codigo=cidade.uf
    join pais on pais.codigo=uf.pais
    join interacao_assunto on interacao.codigo=INTERACAO_ASSUNTO.interacao
    join assunto on assunto.codigo=INTERACAO_ASSUNTO.assunto
    where 
        interacao.data between date('now','-3000 months') and date('now')
    group by 
        strftime('%Y-%m',interacao.data),assunto.nome
    order by strftime('%Y-%m',interacao.data) asc,por_mes desc
;


insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-11-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-09-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-08-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-07-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-10-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-09-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-09-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-05-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-06-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-09-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-12-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-11-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-10-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-03-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-04-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-05-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-06-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-08-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-09-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-12-04 00:00:00');
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (22, null, null, null, 'TESTE', null, null, null,1,'2021-10-04 00:00:00');
select * from assunto;
select codigo,data from interacao where ativo=1 order by data;
insert into interacao_assunto (assunto, interacao) values (1,322);
insert into interacao_assunto (assunto, interacao) values (2,340);
insert into interacao_assunto (assunto, interacao) values (3,341);
insert into interacao_assunto (assunto, interacao) values (4,334);
insert into interacao_assunto (assunto, interacao) values (1,335);
insert into interacao_assunto (assunto, interacao) values (2,342);
insert into interacao_assunto (assunto, interacao) values (3,334);
insert into interacao_assunto (assunto, interacao) values (4,329);
insert into interacao_assunto (assunto, interacao) values (1,330);
insert into interacao_assunto (assunto, interacao) values (2,332);
insert into interacao_assunto (assunto, interacao) values (3,331);
insert into interacao_assunto (assunto, interacao) values (4,339);
insert into interacao_assunto (assunto, interacao) values (1,347);
insert into interacao_assunto (assunto, interacao) values (2,331);
insert into interacao_assunto (assunto, interacao) values (3,345);
insert into interacao_assunto (assunto, interacao) values (4,337);
insert into interacao_assunto (assunto, interacao) values (1,338);
insert into interacao_assunto (assunto, interacao) values (2,2);
insert into interacao_assunto (assunto, interacao) values (3,3);
insert into interacao_assunto (assunto, interacao) values (4,4);
insert into interacao_assunto (assunto, interacao) values (1,4);
insert into interacao_assunto (assunto, interacao) values (2,5);
insert into interacao_assunto (assunto, interacao) values (3,6);
insert into interacao_assunto (assunto, interacao) values (4,7);
insert into interacao_assunto (assunto, interacao) values (1,8);
insert into interacao_assunto (assunto, interacao) values (2,10);
insert into interacao_assunto (assunto, interacao) values (3,11);
insert into interacao_assunto (assunto, interacao) values (4,12);
insert into interacao_assunto (assunto, interacao) values (1,13);

insert into interacao_assunto (assunto, interacao) values (1,322);
insert into interacao_assunto (assunto, interacao) values (2,340);
insert into interacao_assunto (assunto, interacao) values (3,341);
insert into interacao_assunto (assunto, interacao) values (4,334);
insert into interacao_assunto (assunto, interacao) values (1,335);
insert into interacao_assunto (assunto, interacao) values (2,342);
insert into interacao_assunto (assunto, interacao) values (3,334);
insert into interacao_assunto (assunto, interacao) values (4,329);
insert into interacao_assunto (assunto, interacao) values (1,330);
insert into interacao_assunto (assunto, interacao) values (2,332);
insert into interacao_assunto (assunto, interacao) values (3,331);
insert into interacao_assunto (assunto, interacao) values (4,339);
insert into interacao_assunto (assunto, interacao) values (1,347);
insert into interacao_assunto (assunto, interacao) values (2,331);
insert into interacao_assunto (assunto, interacao) values (3,345);
insert into interacao_assunto (assunto, interacao) values (4,337);
insert into interacao_assunto (assunto, interacao) values (1,338);
insert into interacao_assunto (assunto, interacao) values (2,2);
insert into interacao_assunto (assunto, interacao) values (3,3);
insert into interacao_assunto (assunto, interacao) values (4,4);
insert into interacao_assunto (assunto, interacao) values (1,4);
insert into interacao_assunto (assunto, interacao) values (2,5);
insert into interacao_assunto (assunto, interacao) values (3,6);
insert into interacao_assunto (assunto, interacao) values (4,7);
insert into interacao_assunto (assunto, interacao) values (1,8);
insert into interacao_assunto (assunto, interacao) values (2,10);
insert into interacao_assunto (assunto, interacao) values (3,11);
insert into interacao_assunto (assunto, interacao) values (4,12);
insert into interacao_assunto (assunto, interacao) values (1,13);







































--13) Mostrar qual assunto permaneceu por mais meses consecutivos entre os top T mais interagidos por mês no país P nos últimos M meses
-- T = 5
-- P = Brasil 31
-- M = 5 meses

select assunto from (
    select assunto, 
        case
            when qtd2 is null then qtd1
            when qtd2 is not null then qtd1+qtd2
        end as total 
    from (
        select assunto.nome as assunto, qtdnoPost.qtd as qtd1, qtdnoComentario.qtd as qtd2 from assunto
            join (
                select assunto.nome as n, count(*) as qtd from interacao
                    join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                    join assunto on interacao_assunto.assunto = assunto.codigo
                where 
                    strftime('%m', interacao.data, 'localtime') = strftime('%m', 'now', 'localtime') and
                    interacao.local in (
                        select cidade.codigo from cidade join uf on cidade.uf = uf.codigo join pais on uf.pais = pais.codigo 
                        where pais.codigo = 31)
                group by assunto.nome
                order by qtd desc
            ) as qtdnoPost on assunto.nome = qtdnoPost.nome
    ) as tmp1
    where total in (
        select distinct* from (
            select  
                case
                    when qtd2 is null then qtd1
                    when qtd2 is not null then qtd1+qtd2
                end as total from (
                select assunto.nome as assunto, qtdnoPost.qtd as qtd1, qtdnoComentario.qtd as qtd2 from assunto
                    join (
                        select assunto.nome, count(*) as qtd from post
                            join assuntopost on post.codigo = assuntopost.post
                            join assunto on assuntopost.assunto = assunto.codigo
                        where 
                            strftime('%m', post.datadopost, 'localtime') = strftime('%m', 'now', '-1 month', 'localtime') and
                            post.usuario in (
                                select usuario.email from usuario
                                    join cidade on usuario.cidade = cidade.codigo
                                    join estado on cidade.estado = estado.codigodaUF
                                    join pais on estado.pais = pais.codigoISO
                                where pais.nome = 'Brasil'
                            )
                        group by assunto.nome
                        order by qtd desc
                    ) as qtdnoPost on assunto.nome = qtdnoPost.nome
                    left join (
                        select assunto.nome, count(*) as qtd from comentario
                            join assuntocomentario on comentario.codigo = assuntocomentario.comentario
                            join assunto on assuntocomentario.assunto = assunto.codigo
                        where 
                            strftime('%m', comentario.datacomentario, 'localtime') = strftime('%m', 'now', '-1 month', 'localtime') and
                            comentario.usuario in (
                                select usuario.email from usuario
                                    join cidade on usuario.cidade = cidade.codigo
                                    join estado on cidade.estado = estado.codigodaUF
                                    join pais on estado.pais = pais.codigoISO
                                where pais.nome = 'Brasil'
                            )
                        group by assunto.nome
                        order by qtd desc
                    ) as qtdnoComentario on assunto.nome = qtdnoComentario.nome
            )
            order by total desc
        )
        limit 5
    )
    order by total desc
)

select 
    pais.nome as pais,
    perfil.genero as genero,
    case
        when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) < 18 then  '- 18'
        when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 18 and 21 then '18-21'
        when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 21 and 25 then '21-25'
        when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 25 and 30 then '25-30'
        when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 30 and 36 then '30-36'
        when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 36 and 43 then '36-43'
        when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 43 and 51 then '43-51'
        when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 51 and 60 then '51-60'
        when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) > 60 then '60-'
    end as faixaEtaria,
    count(*) as total
from interacao 
        join perfil on perfil.codigo=interacao.perfil
        join cidade on cidade.codigo=interacao.local
        join uf on uf.codigo=cidade.uf
        join pais on uf.pais=pais.codigo
        where 
            date(interacao.data) between date('now','-1 month') and  date('now')
            and pais.codigo=31
    group by perfil.genero,faixaEtaria
    order by faixaEtaria,perfil.genero desc
;



INSERT INTO PERFIL VALUES(6,1,'teste1@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','F','CHPK-1','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','1980-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(7,1,'teste2@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','F','CHPK-2','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','1990-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(8,1,'teste3@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','F','CHPK-3','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','2000-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(9,1,'teste4@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','F','CHPK-4','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','1940-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(10,1,'teste5@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','F','CHPK-5','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','1999-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(11,1,'teste6@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','F','CHPK-6','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','2003-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(12,1,'teste7@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','F','CHPK-29','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','2004-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(13,1,'teste8@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','F','CHPK-12','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','1979-05-09','2021-11-25 21:34:09',1);

INSERT INTO PERFIL VALUES(14,1,'teste9@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','M','CHPK-1','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','1980-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(15,1,'teste10@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','M','CHPK-2','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','1990-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(16,1,'teste11@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','M','CHPK-3','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','2000-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(17,1,'teste12@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','M','CHPK-4','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','1940-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(18,1,'teste13@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','M','CHPK-5','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','1999-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(19,1,'teste14@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','M','CHPK-6','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','2003-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(20,1,'teste15@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','M','CHPK-29','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','2004-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(21,1,'teste16@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','M','CHPK-12','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','1979-05-09','2021-11-25 21:34:09',1);
INSERT INTO PERFIL VALUES(22,1,'teste17@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','M','CHPK-13','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','2004-05-09','2021-11-25 21:34:09',1);


insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (5, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (6, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (7, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (9, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (10, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (11, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (12, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (13, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (6, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (1, null, null, null, 'TESTE', null, null, null,1);

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (14, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (15, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (16, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (17, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (18, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (19, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (21, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (14, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (13, null, null, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (22, null, null, null, 'TESTE', null, null, null,1);


select count(perfil.codigo) from perfil
    where perfil.codigo in (
        select perfil.codigo from interacao 
            join perfil on interacao.perfil = perfil.codigo
            left join (select post, isReaction, emote, data, local from interacao) as reacoes on interacao.codigo = reacoes.post
            left join cidade on reacoes.local = cidade.codigo 
            left join uf on cidade.uf = uf.codigo
            left join pais on uf.pais = pais.codigo
        where 
            reacoes.isReaction = 1 and 
            reacoes.emote = 'curtir' and
            pais.codigo = 31 --and
            --date(reacoes.data) between date('now', '-200 days', 'localtime') and date('now', 'localtime') and
            --date(reacoes.data) between date(interacao.data, 'localtime') and date(interacao.data, '+0 hours', 'localtime')
        group by perfil.codigo
        having count(*) > $likes
        order by count(*) desc  
    )
;

--10)Mostrar quantos usuários receberam mais de C curtidas em uma postagem, em menos de H horas após a postagem,
--no país P nos últimos D dias
select count(*) as qt
from 
(
    select 
    distinct
    posts.perfil
    from
    ( 
        select 
        pais.nome as pais,
        interacao.perfil as perfil,
        interacao.codigo as postagem,
        interacao.data as postagem_timestamp
        from interacao
        join cidade on cidade.codigo=interacao.local
        join uf on cidade.uf=uf.codigo
        join pais on pais.codigo=uf.pais
        where 
            pais.codigo=31
            and interacao.isReaction is null
            and interacao.data between datetime('now','-200 days') and datetime('now') 
    ) as posts
    join interacao on posts.postagem=interacao.post
    where 
        interacao.isReaction is not null
        and interacao.data between datetime(posts.postagem_timestamp) and datetime(posts.postagem_timestamp,'+1 hours')
    group by posts.postagem
    having count(*)>8
)
;

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (14, null, null,1, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (15, null, null,2, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (16, null, null,3, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (17, null, null,4, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (18, null, null,5, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (19, null, null,6, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (21, null, null,7, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (14, null, null,8, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (13, null, null,9, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (22, null, null,10, 'TESTE',1, null,'curtir',1);

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (14, null, null,11, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (15, null, null,12, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (16, null, null,13, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (17, null, null,14, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (18, null, null,15, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (19, null, null,16, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (21, null, null,17, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (14, null, null,18, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (13, null, null,19, 'TESTE',1, null,'curtir',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (22, null, null,20, 'TESTE',1, null,'curtir',1);


select 
case
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) < 18 then '- 18'    
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 18 and 21 then '18-21'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 21 and 25 then '21-25'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 25 and 30 then '25-30'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 30 and 36 then '30-36'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 36 and 43 then '36-43'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 43 and 51 then '43-51'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 51 and 60 then '51-60'
    when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) > 60 then '60-'
end as faixaEtaria, count(*) as qtdReacoes
from interacao
    join perfil on interacao.perfil = perfil.codigo
where 
    date(interacao.data, 'localtime') between date('now', '-200 days', 'localtime') and date('now', 'localtime') and
    interacao.codigo in (
        select interacao.codigo from interacao
            join porto on interacao.porto = porto.codigo
        where porto.codigo = 2
    )
group by faixaEtaria
having qtdReacoes = (
        select qtdReacoes from (
            select case
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) < 18 then  '- 18'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 18 and 21 then '18-21'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 21 and 25 then '21-25'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 25 and 30 then '25-30'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 30 and 36 then '30-36'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 36 and 43 then '36-43'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 43 and 51 then '43-51'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) between 51 and 60 then '51-60'
                when cast((julianday('now', 'localtime')-julianday(perfil.datanasc, 'localtime'))/365.2422 as integer) > 60 then '60-'
            end as faixaEtaria, count(*) as qtdReacoes
            from interacao
                join perfil on interacao.perfil = perfil.codigo
            where 
                interacao.codigo in (
                        select interacao.codigo from interacao
            join porto on interacao.porto = porto.codigo
        where porto.codigo = 2
                ) and
                date(interacao.data, 'localtime') between date('now', '-200 days', 'localtime') and date('now', 'localtime')
            group by faixaEtaria
            order by qtdReacoes desc
        )
        limit 1
    ) 
    order by qtdReacoes desc
;


insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (5, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (6, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (7, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (9, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (10, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (11, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (12, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (13, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (6, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (1, null, 2, null, 'TESTE', null, null, null,1);

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (14, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (15, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (16, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (17, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (18, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (19, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (21, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (14, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (13, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (22, null, 2, null, 'TESTE', null, null, null,1);

insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (5, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (6, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (7, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (9, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (10, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (11, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (12, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (13, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (6, null, 2, null, 'TESTE', null, null, null,1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local) values (1, null, 2, null, 'TESTE', null, null, null,1);


--15 Desativar temporariamente as contas dos usuários do país P que não possuem qualquer interação há mais de A anos
INSERT INTO PERFIL VALUES(23,1,'teste124@gmail.com','$2y$10$vaJf.MBckE0gkUdu0WE3p.c8ZxwWw6OM/sJcZNc3rzV2yt87DVAFy','M','CHPK-112','https://drive.google.com/uc?export=download&id=18Q0QWF1iRWc7wq0nezRxgdLYEQUc2Thz','2000-05-09','2021-11-25 21:34:09',1);
insert into interacao (perfil, perfil_posting, porto, post, texto, isReaction, isSharing, emote, local,data) values (23, null, 2, null, 'TESTE', null, null, null,1,'2000-06-12 00:00:00');

--desativar usuario
update 
    perfil 
    set ativo=0 
    where
        perfil.codigo not in
        (
            select
            distinct 
            perfil.codigo 
            from perfil
                join cidade on perfil.cidade=cidade.codigo
                join uf on cidade.uf=uf.codigo
                join pais on pais.codigo=uf.pais
                join interacao on interacao.perfil=perfil.codigo
            where 
                interacao.data between datetime('now','-5 years') and datetime('now')
                and pais.codigo=31
        )
        and perfil.dataregis between datetime('now','-2 days') and datetime('now')
;
--desativar interacoes do usuario
update interacao set ativo=0 where interacao.perfil in (select codigo from perfil where ativo=0);
--desativar portos do usuario
update porto set ativo=0 where porto.perfil in (select codigo from perfil where ativo=0);
--desativa amizades com esse usuario
update amigo set ativo=0 where amigo.perfil in (select codigo from perfil where ativo=0) or amigo.amigo in (select codigo from perfil where ativo=0);
--desativa citacoes com esse usuario
update citacao set ativo=0 where citacao.perfil in (select codigo from perfil where ativo=0);
--desativa porto_participa desse usuario
update porto_participa set ativo=0 where porto_participa.perfil in (select codigo from perfil where ativo=0);
--desativa solicitaçoes de amizade desse usuario
update solicitacao_amigo set ativo=0 where solicitacao_amigo.perfil in (select codigo from perfil where ativo=0) or solicitacao_amigo.amigo in (select codigo from perfil where ativo=0);







