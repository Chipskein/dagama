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

select perfil.codigo as user, tmp1.assuntoCodigo, tmp1.assuntoNome, tmp1.qtd from perfil
    join (select interacao.perfil as perfil, assunto.codigo as assuntoCodigo, assunto.nome as assuntoNome, count(*) as qtd from interacao
        join INTERACAO_ASSUNTO on interacao.codigo = INTERACAO_ASSUNTO.interacao
        join assunto on interacao_assunto.assunto = assunto.codigo
    where
        -- datetime(interacao.data) between datetime('now','start of month', '-3 months') and datetime('now')
        datetime(interacao.data) between datetime('now','start of month','-3 months') and datetime('now')
    group by interacao.perfil, assunto.codigo
    having 
        -- qtd > 1
        qtd > 1
    order by qtd desc) as tmp1 on tmp1.perfil = perfil.codigo
where
    -- perfil.codigo = 7
    perfil.codigo = 8
-- limit 5
limit 5

select tmp1.codigo, tmp1.username, tmp1.img, tmp1.enviado, 
     case
        when solicitacao_amigo.perfil is null then 'false'
        when solicitacao_amigo.perfil is not null then 'true'
    end as recebido
    from (
    select perfil.codigo, perfil.username, perfil.img,
        case
            when solicitacao_amigo.amigo is null then 'false'
            when solicitacao_amigo.amigo is not null then 'true'
        end as enviado, 
        1 as camadas from perfil
        join (select perfil.codigo as user, tmp1.assuntoCodigo, tmp1.assuntoNome, tmp1.qtd, count(*) as qtd2 from perfil
                join (select interacao.perfil as perfil, assunto.codigo as assuntoCodigo, assunto.nome as assuntoNome, count(*) as qtd from interacao
                    join INTERACAO_ASSUNTO on interacao.codigo = INTERACAO_ASSUNTO.interacao
                    join assunto on interacao_assunto.assunto = assunto.codigo
                where
                    datetime(interacao.data) between datetime('now','start of month', '-3 months') and datetime('now')
                group by interacao.perfil, assunto.codigo
                having 
                    qtd > 1
                order by qtd desc) as tmp1 on tmp1.perfil = perfil.codigo
            where 
                tmp1.qtd <= 5
            group by perfil.codigo) 
            as tmp2 on perfil.codigo = tmp2.user
        left join solicitacao_amigo on solicitacao_amigo.amigo = perfil.codigo and solicitacao_amigo.perfil = 7
    where
        perfil.codigo != 7
    group by perfil.codigo
    having
        tmp2.assuntoCodigo in (select tmp1.assuntoCodigo from perfil
            join (select interacao.perfil as perfil, assunto.codigo as assuntoCodigo, assunto.nome as assuntoNome, count(*) as qtd from interacao
                join INTERACAO_ASSUNTO on interacao.codigo = INTERACAO_ASSUNTO.interacao
                join assunto on interacao_assunto.assunto = assunto.codigo
            where
                datetime(interacao.data) between datetime('now','start of month', '-3 months') and datetime('now')
            group by interacao.perfil, assunto.codigo
            having 
                qtd > 1
            order by qtd desc) as tmp1 on tmp1.perfil = perfil.codigo
        where
            perfil.codigo = 7
        limit 5)
    union
    select perfil.codigo, perfil.username, perfil.img,
        case
            when solicitacao_amigo.amigo is null then 'false'
            when solicitacao_amigo.amigo is not null then 'true'
        end as enviado, 
        2 as camadas from perfil
        left join solicitacao_amigo on solicitacao_amigo.amigo = perfil.codigo and solicitacao_amigo.perfil = 7
    where 
        perfil.codigo != 7 and
        perfil.codigo not in (
        select perfil.codigo from perfil
            join (select perfil.codigo as user, tmp1.assuntoCodigo, tmp1.assuntoNome, tmp1.qtd, count(*) as qtd2 from perfil
                    join (select interacao.perfil as perfil, assunto.codigo as assuntoCodigo, assunto.nome as assuntoNome, count(*) as qtd from interacao
                        join INTERACAO_ASSUNTO on interacao.codigo = INTERACAO_ASSUNTO.interacao
                        join assunto on interacao_assunto.assunto = assunto.codigo
                    where
                        datetime(interacao.data) between datetime('now','start of month', '-3 months') and datetime('now')
                    group by interacao.perfil, assunto.codigo
                    having 
                        qtd > 1
                    order by qtd desc) as tmp1 on tmp1.perfil = perfil.codigo
                where 
                    tmp1.qtd <= 5
                group by perfil.codigo) 
                as tmp2 on perfil.codigo = tmp2.user
        where
            perfil.codigo != 7
        group by perfil.codigo
        having
            tmp2.assuntoCodigo in (select tmp1.assuntoCodigo from perfil
                join (select interacao.perfil as perfil, assunto.codigo as assuntoCodigo, assunto.nome as assuntoNome, count(*) as qtd from interacao
                    join INTERACAO_ASSUNTO on interacao.codigo = INTERACAO_ASSUNTO.interacao
                    join assunto on interacao_assunto.assunto = assunto.codigo
                where
                    datetime(interacao.data) between datetime('now','start of month', '-3 months') and datetime('now')
                group by interacao.perfil, assunto.codigo
                having 
                    qtd > 1
                order by qtd desc) as tmp1 on tmp1.perfil = perfil.codigo
            where
                perfil.codigo = 7
            limit 5)   
    ) 
    group by perfil.codigo
    order by camadas asc
) as tmp1
    left join solicitacao_amigo on solicitacao_amigo.perfil = tmp1.codigo and solicitacao_amigo.amigo = 7
where
    solicitacao_amigo.perfil is null and
    tmp1.enviado = 'false' and
    tmp1.codigo not in (
        select case
                when amigo.perfil = perfil.codigo then amigo.amigo
                when amigo.amigo = perfil.codigo then amigo.perfil
            end as amigo
        from perfil
            join amigo on perfil.codigo = amigo.perfil or perfil.codigo = amigo.amigo
        where perfil.codigo = 7
    ) and tmp1.codigo not in (select codigo from perfil where ativo = 0);

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