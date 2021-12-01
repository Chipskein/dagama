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