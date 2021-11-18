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
limit $limit offset $offset;