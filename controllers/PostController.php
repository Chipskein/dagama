<?php
    use Dagama\Database;
    class PostController{
        function getPosts($user, $offset,$limit,$order){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $postsArray=[];
                $postsOriginais = mysqli_query($con,"
                select
                    interacao.codigo as codInteracao, 
                    interacao.post as codPost,
                    interacao.postPai as codPostPai,
                    case 
                        when tmpQtd.qtd is null then 0
                        else tmpQtd.qtd
                    end as qtdInteracao,
                    interacao.isReaction as isReaction, 
                    interacao.texto as textoPost, 
                    interacao.data as dataPost,
                    interacao.isSharing as isSharing, 
                    interacao.emote as emote,
                    interacao.ativo as ativo,
                    pais.nome as nomePais,
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
                            interacao.post in (select codigo from interacao where perfil = $user and ativo = 1) or
                            interacao.codigo in (select postPai from interacao where perfil = $user and ativo = 1 group by postPai)
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
                    left join (select postPai, count(*) as qtd from interacao where postPai is not null and interacao.ativo=1 group by postPai) as tmpQtd on interacao.codigo = tmpQtd.postPai
                    left join pais on interacao.local = pais.codigo
                    left join porto on interacao.porto = porto.codigo
                where interacao.ativo = 1 and interacao.postPai is null
                group by codPost
                order by $order
                limit $limit 
                offset $offset
                ");

                if($postsOriginais){              
                    while($row = mysqli_fetch_array($postsOriginais)){
                        $postsArray[$row['codInteracao']] = $row;

                        $resCitacoesParent = mysqli_query($con,"select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                        from citacao join perfil on perfil.codigo = citacao.perfil 
                        where 
                            citacao.ativo = 1 and 
                            citacao.interacao = $row[codInteracao]");
                        $citacoes = [];
                        while ($row2 = mysqli_fetch_array($resCitacoesParent)) {
                            $citacoes[] = $row2;
                        }
                        $postsArray[$row['codInteracao']]['citacoes'] = $citacoes;
                        
                        $resAssuntosParent = mysqli_query($con,"
                        select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                            left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                            left join assunto on interacao_assunto.assunto = assunto.codigo
                        where
                            interacao_assunto.ativo = 1 and
                            interacao.codigo = $row[codInteracao]");
                        $assuntos = [];
                        while ($row2 = mysqli_fetch_array($resAssuntosParent)) {
                            $assuntos[] = $row2;
                        }
                        $postsArray[$row['codInteracao']]['assuntos'] = $assuntos;
                        $temInteracoes = mysqli_query($con,"
                        select
                            interacao.codigo as codInteracao, 
                            interacao.post as codPost, 
                            interacao.isReaction as isReaction, 
                            interacao.texto as textoPost, 
                            interacao.data as dataPost,
                            interacao.isSharing as isSharing, 
                            interacao.emote as emote,
                            interacao.ativo as ativo,
                            case 
                                when tmpQtd.qtd is null then 0
                                else tmpQtd.qtd
                            end as qtdInteracao,
                            pais.nome as nomePais,
                            perfil.codigo as codPerfil, 
                            perfil.username as nomePerfil,
                            perfil.img as iconPerfil
                        from interacao
                            join perfil on interacao.perfil = perfil.codigo
                            left join pais on interacao.local = pais.codigo
                            left join (select post, count(*) as qtd from interacao where interacao.postPai is not null and interacao.post is not null and interacao.ativo = 1 group by interacao.post) as tmpQtd on interacao.codigo = tmpQtd.post
                        where
                            interacao.ativo = 1 and 
                            interacao.isSharing is null 
                            and interacao.postPai = $row[codInteracao] 
                            and interacao.post = $row[codInteracao]");
                        $childInteracoes = [];
                        if($temInteracoes){
                            while($row3 = mysqli_fetch_array($temInteracoes)){
                                $childInteracoes[$row3['codInteracao']] = $row3;
                                $resCitacoesChild = mysqli_query($con,"select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                                from citacao 
                                    join perfil on perfil.codigo = citacao.perfil 
                                where 
                                    citacao.ativo = 1 and 
                                    citacao.interacao = $row3[codInteracao]");
                                $citacoes = [];
                                while ($row4 = mysqli_fetch_array($resCitacoesChild)) {
                                    $citacoes[] = $row4;
                                }
                                $childInteracoes[$row3['codInteracao']]['citacoes'] = $citacoes;
                                $resAssuntosChild = mysqli_query($con,"
                                select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                                    left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                                    left join assunto on interacao_assunto.assunto = assunto.codigo
                                where
                                    interacao_assunto.ativo = 1 and
                                    interacao.codigo = $row3[codInteracao]");
                                $assuntos = [];
                                while ($row5 = mysqli_fetch_array($resAssuntosChild)) {
                                    $assuntos[] = $row5;
                                }
                                $childInteracoes[$row3['codInteracao']]['assuntos'] = $assuntos;

                                $temInnerInteracoes = mysqli_query($con,"
                                select
                                    interacao.codigo as codInteracao, 
                                    interacao.post as codPost, 
                                    interacao.isReaction as isReaction, 
                                    interacao.texto as textoPost, 
                                    interacao.data as dataPost,
                                    interacao.isSharing as isSharing, 
                                    interacao.emote as emote,
                                    interacao.ativo as ativo,
                                    case 
                                        when tmpQtd.qtd is null then 0
                                        else tmpQtd.qtd
                                    end as qtdInteracao,
                                    pais.nome as nomePais,
                                    perfil.codigo as codPerfil, 
                                    perfil.username as nomePerfil,
                                    perfil.img as iconPerfil
                                from interacao
                                    join perfil on interacao.perfil = perfil.codigo
                                    left join pais on interacao.local = pais.codigo
                                    left join (select post, count(*) as qtd from interacao where interacao.postPai is not null and interacao.post is not null and interacao.ativo = 1 group by interacao.post) as tmpQtd on interacao.codigo = tmpQtd.post
                                where 
                                    interacao.ativo = 1 and 
                                    interacao.isSharing is null and 
                                    interacao.postPai = $row[codInteracao] and 
                                    interacao.post = $row3[codInteracao]");
                                    
                                $grandChildInteracoes = [];
                                $childInteracoes[$row3['codInteracao']]['respostas'] = [];
                                if($temInnerInteracoes){
                                    while ($row6 = mysqli_fetch_array($temInnerInteracoes)) {
                                        $grandChildInteracoes[$row6['codInteracao']] = $row6;
                                        $resCitacoesGrandChild = mysqli_query($con,"
                                        select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                                            join perfil on perfil.codigo = citacao.perfil 
                                        where 
                                            citacao.ativo = 1 and 
                                            citacao.interacao = $row6[codInteracao]");
                                        $citacoes = [];
                                        while ($row7 = mysqli_fetch_array($resCitacoesGrandChild)) {
                                            $citacoes[] = $row7;
                                        }
                                        $grandChildInteracoes[$row6['codInteracao']]['citacoes'] = $citacoes;
                                        
                                        $resAssuntosGrandChild = mysqli_query($con,"
                                        select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                                            left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                                            left join assunto on interacao_assunto.assunto = assunto.codigo
                                        where
                                            interacao_assunto.ativo = 1 and
                                            interacao.codigo = ".$row6['codInteracao']);
                                        $assuntos = [];
                                        while ($row8 = mysqli_fetch_array($resAssuntosGrandChild)) {
                                            $assuntos[] = $row8;
                                        }
                                        $grandChildInteracoes[$row6['codInteracao']]['assuntos'] = $assuntos;
                                        $childInteracoes[$row3['codInteracao']]['respostas'][$row6['codInteracao']] = $grandChildInteracoes[$row6['codInteracao']];
                                    }                                
                                }
                                
                            }
                        }
                        $postsArray[$row['codInteracao']]['comentarios'] = $childInteracoes;
                    }
                }
                $db->close();
                return $postsArray;
                
            }
            else exit;
        }
        function getAllPosts($user){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $postsArray=[];
                $postsOriginais = mysqli_query($con,"
                select
                    interacao.codigo as codInteracao, 
                    interacao.post as codPost,
                    interacao.postPai as codPostPai,
                    case 
                        when tmpQtd.qtd is null then 0
                        else tmpQtd.qtd
                    end as qtdInteracao,
                    interacao.isReaction as isReaction, 
                    interacao.texto as textoPost, 
                    interacao.data as dataPost,
                    interacao.isSharing as isSharing, 
                    interacao.emote as emote,
                    interacao.ativo as ativo,
                    pais.nome as nomePais,
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
                            interacao.post in (select codigo from interacao where perfil = $user and ativo = 1) or
                            interacao.codigo in (select postPai from interacao where perfil = $user and ativo = 1 group by postPai)
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
                    left join (select postPai, count(*) as qtd from interacao where postPai is not null and interacao.ativo=1 group by postPai) as tmpQtd on interacao.codigo = tmpQtd.postPai
                    left join pais on interacao.local = pais.codigo
                    left join porto on interacao.porto = porto.codigo
                where interacao.ativo = 1 and interacao.postPai is null
                group by codPost
                ");
                if($postsOriginais){       
                    while($row = mysqli_fetch_array($postsOriginais)){
                        $postsArray[$row['codInteracao']] = $row;

                        $resCitacoesParent = mysqli_query($con,"select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                        from citacao join perfil on perfil.codigo = citacao.perfil 
                        where 
                            citacao.ativo = 1 and 
                            citacao.interacao = $row[codInteracao]");
                        $citacoes = [];
                        while ($row2 = mysqli_fetch_array($resCitacoesParent)) {
                            $citacoes[] = $row2;
                        }
                        $postsArray[$row['codInteracao']]['citacoes'] = $citacoes;
                        
                        $resAssuntosParent = mysqli_query($con,"
                        select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                            left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                            left join assunto on interacao_assunto.assunto = assunto.codigo
                        where
                            interacao_assunto.ativo = 1 and
                            interacao.codigo = $row[codInteracao]");
                        $assuntos = [];
                        while ($row2 = mysqli_fetch_array($resAssuntosParent)) {
                            $assuntos[] = $row2;
                        }
                        $postsArray[$row['codInteracao']]['assuntos'] = $assuntos;
                        $temInteracoes = mysqli_query($con,"
                        select
                            interacao.codigo as codInteracao, 
                            interacao.post as codPost, 
                            interacao.isReaction as isReaction, 
                            interacao.texto as textoPost, 
                            interacao.data as dataPost,
                            interacao.isSharing as isSharing, 
                            interacao.emote as emote,
                            interacao.ativo as ativo,
                            case 
                                when tmpQtd.qtd is null then 0
                                else tmpQtd.qtd
                            end as qtdInteracao,
                            pais.nome as nomePais,
                            perfil.codigo as codPerfil, 
                            perfil.username as nomePerfil,
                            perfil.img as iconPerfil
                        from interacao
                            join perfil on interacao.perfil = perfil.codigo
                            left join pais on interacao.local = pais.codigo
                            left join (select post, count(*) as qtd from interacao where interacao.postPai is not null and interacao.post is not null and interacao.ativo = 1 group by interacao.post) as tmpQtd on interacao.codigo = tmpQtd.post
                        where
                            interacao.ativo = 1 and 
                            interacao.isSharing is null 
                            and interacao.postPai = $row[codInteracao] 
                            and interacao.post = $row[codInteracao]");
                        $childInteracoes = [];
                        if($temInteracoes){
                            while($row3 = mysqli_fetch_array($temInteracoes)){
                                $childInteracoes[$row3['codInteracao']] = $row3;
                                $resCitacoesChild = mysqli_query($con,"select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                                from citacao 
                                    join perfil on perfil.codigo = citacao.perfil 
                                where 
                                    citacao.ativo = 1 and 
                                    citacao.interacao = $row3[codInteracao]");
                                $citacoes = [];
                                while ($row4 = mysqli_fetch_array($resCitacoesChild)) {
                                    $citacoes[] = $row4;
                                }
                                $childInteracoes[$row3['codInteracao']]['citacoes'] = $citacoes;
                                $resAssuntosChild = mysqli_query($con,"
                                select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                                    left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                                    left join assunto on interacao_assunto.assunto = assunto.codigo
                                where
                                    interacao_assunto.ativo = 1 and
                                    interacao.codigo = $row3[codInteracao]");
                                $assuntos = [];
                                while ($row5 = mysqli_fetch_array($resAssuntosChild)) {
                                    $assuntos[] = $row5;
                                }
                                $childInteracoes[$row3['codInteracao']]['assuntos'] = $assuntos;

                                $temInnerInteracoes = mysqli_query($con,"
                                select
                                    interacao.codigo as codInteracao, 
                                    interacao.post as codPost, 
                                    interacao.isReaction as isReaction, 
                                    interacao.texto as textoPost, 
                                    interacao.data as dataPost,
                                    interacao.isSharing as isSharing, 
                                    interacao.emote as emote,
                                    interacao.ativo as ativo,
                                    case 
                                        when tmpQtd.qtd is null then 0
                                        else tmpQtd.qtd
                                    end as qtdInteracao,
                                    pais.nome as nomePais,
                                    perfil.codigo as codPerfil, 
                                    perfil.username as nomePerfil,
                                    perfil.img as iconPerfil
                                from interacao
                                    join perfil on interacao.perfil = perfil.codigo
                                    left join pais on interacao.local= pais.codigo
                                    left join (select post, count(*) as qtd from interacao where interacao.postPai is not null and interacao.post is not null and interacao.ativo = 1 group by interacao.post) as tmpQtd on interacao.codigo = tmpQtd.post
                                where 
                                    interacao.ativo = 1 and 
                                    interacao.isSharing is null and 
                                    interacao.postPai = $row[codInteracao] and 
                                    interacao.post = $row3[codInteracao]");
                                    
                                $grandChildInteracoes = [];
                                $childInteracoes[$row3['codInteracao']]['respostas'] = [];
                                if($temInnerInteracoes){
                                    while ($row6 = mysqli_fetch_array($temInnerInteracoes)) {
                                        $grandChildInteracoes[$row6['codInteracao']] = $row6;
                                        $resCitacoesGrandChild = mysqli_query($con,"
                                        select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                                            join perfil on perfil.codigo = citacao.perfil 
                                        where 
                                            citacao.ativo = 1 and 
                                            citacao.interacao = $row6[codInteracao]");
                                        $citacoes = [];
                                        while ($row7 = mysqli_fetch_array($resCitacoesGrandChild)) {
                                            $citacoes[] = $row7;
                                        }
                                        $grandChildInteracoes[$row6['codInteracao']]['citacoes'] = $citacoes;
                                        
                                        $resAssuntosGrandChild = mysqli_query($con,"
                                        select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                                            left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                                            left join assunto on interacao_assunto.assunto = assunto.codigo
                                        where
                                            interacao_assunto.ativo = 1 and
                                            interacao.codigo = ".$row6['codInteracao']);
                                        $assuntos = [];
                                        while ($row8 = mysqli_fetch_array($resAssuntosGrandChild)) {
                                            $assuntos[] = $row8;
                                        }
                                        $grandChildInteracoes[$row6['codInteracao']]['assuntos'] = $assuntos;
                                        $childInteracoes[$row3['codInteracao']]['respostas'][$row6['codInteracao']] = $grandChildInteracoes[$row6['codInteracao']];
                                    }                                
                                }
                                
                            }
                        }
                        $postsArray[$row['codInteracao']]['comentarios'] = $childInteracoes;
                    }
                }
                $db->close();
                return $postsArray;

            }
            else exit;
        } 
        function getOriginalPost($post){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $result = mysqli_query($con,"
                    select
                        interacao.codigo as codInteracao, 
                        interacao.post as codPost, 
                        interacao.isReaction as isReaction, 
                        interacao.texto as textoPost, 
                        interacao.data as dataPost,
                        interacao.isSharing as isSharing, 
                        interacao.emote as emote,
                        interacao.ativo as ativo,
                        pais.nome as nomePais,
                        pais.codigo as codPais,
                        porto.codigo as codPorto,
                        porto.nome as nomePorto,
                        perfil.codigo as codPerfil, 
                        perfil.username as nomePerfil,
                        perfil.img as iconPerfil,
                        interacao.postPai as postPai
                    from interacao
                        join perfil on interacao.perfil = perfil.codigo
                        left join porto on interacao.porto = porto.codigo
                        left join pais on interacao.local = pais.codigo
                    where interacao.codigo = $post");
                
                $results2 = mysqli_query($con,"
                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                        join perfil on perfil.codigo = citacao.perfil 
                    where 
                        citacao.ativo = 1 and citacao.interacao = $post");
                $citacoes = [];
                while ($row = mysqli_fetch_array($results2)) {
                    $citacoes[$row['codPerfil']] = $row;
                }
                
                $results3 = mysqli_query($con,"
                select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                    join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                    join assunto on interacao_assunto.assunto = assunto.codigo
                where
                    interacao.ativo = 1 and interacao.codigo = $post");
                $assuntos = [];
                while ($row = mysqli_fetch_array($results3)) {
                    $assuntos[$row['codAssunto']] = $row;
                }

                
                $response = mysqli_fetch_array($result);
                $response['assuntos'] = $assuntos;
                $response['citacoes'] = $citacoes;
                
                $db->close();
                return $response;
                
            }
            else exit;
        }
        function getOriginalPostOnPorto($post, $porto){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $result = mysqli_query($con,"
                    select
                        interacao.codigo as codInteracao, 
                        interacao.post as codPost, 
                        interacao.isReaction as isReaction, 
                        interacao.texto as textoPost, 
                        interacao.data as dataPost,
                        interacao.isSharing as isSharing, 
                        interacao.emote as emote,
                        interacao.ativo as ativo,
                        pais.nome as nomePais,
                        pais.codigo as codPais,
                        porto.codigo as codPorto,
                        porto.nome as nomePorto,
                        perfil.codigo as codPerfil, 
                        perfil.username as nomePerfil,
                        perfil.img as iconPerfil,
                        interacao.postPai as postPai
                    from interacao
                        join perfil on interacao.perfil = perfil.codigo
                        left join porto on interacao.porto = porto.codigo
                        left join pais on interacao.local = pais.codigo
                    where interacao.codigo = $post");
                
                $results2 = mysqli_query($con,"
                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                        join perfil on perfil.codigo = citacao.perfil 
                    where 
                        citacao.ativo = 1 and citacao.interacao = $post");
                $citacoes = [];
                while ($row = mysqli_fetch_array($results2)) {
                    $citacoes[$row['codPerfil']] = $row;
                }
                
                $results3 = mysqli_query($con,"
                select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                    join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                    join assunto on interacao_assunto.assunto = assunto.codigo
                where
                    interacao.ativo = 1 and interacao.codigo = $post");
                $assuntos = [];
                while ($row = mysqli_fetch_array($results3)) {
                    $assuntos[$row['codAssunto']] = $row;
                }

                
                $response = mysqli_fetch_array($result);
                $response['assuntos'] = $assuntos;
                $response['citacoes'] = $citacoes;
                
                $db->close();
                return $response;
                
            }
            else exit;
        }
        function addInteracao($perfil, $texto, $perfil_posting, $porto, $isSharing, $post, $postPai, $isReaction, $emote, $local){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                
                $response = mysqli_query($con,"insert into interacao (perfil, texto, perfil_posting, porto, isSharing, post, postPai, isReaction, emote, local) values 
                ($perfil, 
                '".($texto ? $texto : '')."', 
                ".($perfil_posting ? $perfil_posting : 'null').", 
                ".($porto ? $porto : 'null').", 
                ".($isSharing ? $isSharing : 'null').", 
                ".($post ? $post : 'null').", 
                ".($postPai ? $postPai : 'null').", 
                ".($isReaction ? $isReaction : 'null').", 
                ".($emote ? "'".$emote."'" : 'null').", 
                ".($local ? $local : 'null').")");
                if($response){
                    $res = $db->insert_id;
                    $db->close();
                    return $res;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        function delInteracao($post){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"update interacao set ativo = 0 where codigo = $post");
                if($response) {
                    $response2 = mysqli_query($con,"update citacao set ativo = 0 where interacao = $post");
                    $response3 = mysqli_query($con,"update interacao_assunto set ativo = 0 where interacao = $post");
                    $db->close();
                    return $response;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        function ediInteracao($interacao, $texto, $isReaction, $emote, $local){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $txt = [($texto ? "texto = '$texto'" : "texto = ''"),
                ($isReaction ? "isReaction = ".$isReaction : "isReaction = null"),
                ($emote ? "emote = '".$emote."'" : 'emote = null'),
                ($local ? "local = ".$local : 'local = null'),
                "data = CURRENT_TIMESTAMP"];
                $txt = implode(', ',$txt);
                $response = mysqli_query($con,"update interacao set $txt where codigo = $interacao");
                if($response) {
                    $db->close();
                    return true;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        function addCitacaoInteracao($user, $post){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $check = mysqli_query($con,"select * from citacao where perfil = $user and interacao = $post");
                $check = mysqli_fetch_array($check);
                if($check){
                    $response = mysqli_query($con,"update citacao set ativo = 1 where perfil = $user and interacao = $post");
                    if($response) {
                        $db->close();
                        return $response;
                    } else {
                        $db->close();
                        return false;
                    }
                } else {
                    $response = mysqli_query($con,"insert into citacao (perfil, interacao) values ($user, $post)");
                    if($response) {
                        $db->close();
                        return $response;
                    } else {
                        $db->close();
                        return false;
                    }
                }
            }
            else exit;
        }
        function delCitacao($post, $pessoa){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"update citacao set ativo = 0 where interacao = $post and perfil = $pessoa");
                if($response) {
                    $db->close();
                    return $response;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        function addAssuntoInteracao($post, $assunto){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"insert into interacao_assunto (interacao, assunto) values ($post, $assunto)");
                if($response) {
                    $db->close();
                    return $response;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        function delAssuntoInteracao($post, $assunto){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"update interacao_assunto set ativo = 0 where interacao = $post and assunto = $assunto");
                if($response) {
                    $db->close();
                    return $response;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
    }
?>