<?php
  use Dagama\Database;
    class FriendController{
        /*
        public static function suggestFriends($user, $limit, $offset) {
            $db_connection=db_connection();
            $db=$db_connection['db'];
            $db_type=$db_connection['db_type'];
            if($db){
                // Variáveis M - meses, A - min assuntos em comum, $B - top x assuntos, $U - usuario
                $M = 3; $A = 1; $B = 5; $U = $user;
                if($db_type == 'mysql'){
                    $results=[];
                    $result = mysqli_query($db,"
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
                            where perfil.codigo = $U and amigo.ativo = 1
                        ) and
                        tmp1.codigo not in (select codigo from perfil where ativo = 0)
                    limit $limit offset $offset");
                    if($result){
                        while ($row = mysqli_fetch_array($result)) {
                            array_push($results, $row);
                        }
                    }
                    mysqli_close($db);
                    return $results;
                }
            }
            else exit;
        }
        */
        public static function sendFriendRequest($user, $friend) {
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $hasRequest = mysqli_query($con,"select * from SOLICITACAO_AMIGO where (perfil = $user and amigo = $friend) or (amigo = $user and perfil = $friend)");
                $hasRequest = mysqli_fetch_array($hasRequest);
                if($hasRequest){
                    if($hasRequest['ativo'] == 0) {
                        $friendRequest = mysqli_query($con,"update SOLICITACAO_AMIGO set ativo = 1 where (amigo = $user and perfil = $friend) or (perfil = $user and amigo = $friend)");    
                        if($friendRequest)
                        {
                            $db->close();
                            return $friendRequest;
                        }
                        else
                        {
                            $db->close();
                            return false;
                        }
                    }
                } else {
                    $friendRequest = mysqli_query($con,"insert into SOLICITACAO_AMIGO (perfil, amigo, dateEnvio) values ($user, $friend, CURRENT_TIMESTAMP)");
                    if($friendRequest) {
                        $db->close();
                        return $friendRequest;
                    } else {
                        $db->close();
                        return false;
                    }
                }
            }
            else exit;
        }
        public static function unsendFriendRequest($user, $friend) {
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $friendRequest = mysqli_query($con,"update SOLICITACAO_AMIGO set ativo = 0 where perfil = $user and amigo = $friend");
                if($friendRequest) 
                {
                    $db->close();
                    return $friendRequest;
                } 
                else 
                {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        public static function confirmFriendRequest($user, $friend) {
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $friendRequest = mysqli_query($con,"update SOLICITACAO_AMIGO set ativo = 0 where amigo = $user and perfil = $friend");
                $friendAdd = mysqli_query($con,"insert into amigo (amigo, perfil, dateAceito) values ($user, $friend, CURRENT_TIMESTAMP)");
                if($friendAdd) {
                    $db->close();
                    return $friendAdd;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        public static function declineFriendRequest($user, $friend) {
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $friendRequest = mysqli_query($con,"update SOLICITACAO_AMIGO set ativo = 0 where amigo = $user and perfil = $friend");
                if($friendRequest) {
                    $db->close();
                    return $friendRequest;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        /*
        public static function getRequestAndFriends($user, $isOwner){
            $con_connection=db_connection();
            $con=$con_connection['db'];
            $db_type=$db_connection['db_type'];
            if($db){
                if($db_type == 'mysql'){
                    $results=[];
                    $result = mysqli_query($db,"
                    select perfil.username as nome, perfil.img as img, solicitacao_amigo.dateEnvio as data, solicitacao_amigo.perfil as amigocod, solicitacao_amigo.amigo, amigo.perfil as otherPerfil, amigo.amigo as otherAmigo from solicitacao_amigo, perfil
                        left join amigo on 
                            (solicitacao_amigo.perfil = amigo.perfil and solicitacao_amigo.amigo = amigo.amigo) or 
                            (solicitacao_amigo.amigo = amigo.perfil and solicitacao_amigo.perfil = amigo.amigo)
                    where 
                        perfil.codigo = solicitacao_amigo.perfil and
                        solicitacao_amigo.perfil not in (
                            select codigo from perfil where ativo = 0
                        ) and
                        perfil.ativo = true and
                        solicitacao_amigo.amigo = $user and
                        solicitacao_amigo.ativo = 1");
                    if($result){
                        while ($row = mysqli_fetch_array($result)) {
                            array_push($results, $row);
                        }
                    }
                    mysqli_close($db);
                    return $results;
                }
            }
            else exit;
        }
        */
        public static function delFriend($user, $friend){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $delFriend = mysqli_query($con,"update amigo set ativo = 0 where (perfil = $user and amigo = $friend) or (amigo = $user and perfil = $friend)");
                if($delFriend) {
                    $db->close();
                    return $delFriend;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        /*
        public static function getFriends($user, $offset, $limit, $where){
            $db_connection=db_connection();
            $db=$db_connection['db'];
            $db_type=$db_connection['db_type'];
            if($db){
                if($db_type == 'mysql'){
                    $results=[];
                    if($where !== ''){
                        $result = mysqli_query($db,"
                        select perfil.codigo, case 
                                when amigo.perfil = perfil.codigo then amigo.amigo
                                when amigo.amigo = perfil.codigo then amigo.perfil
                            end as amigoCod,
                            amigo.dateAceito,
                            tmp1.codigo as codAmigo,
                            tmp1.username as nameAmigo,
                            tmp1.img as imgAmigo,
                            (select count(*) from amigo where amigo = $user or perfil = $user and amigo.ativo = 1) as qtdAmigos
                        from perfil 
                            join amigo on perfil.codigo = amigo.perfil or amigo.amigo
                            join (select * from perfil) as tmp1 on tmp1.codigo = amigoCod
                        where 
                        nameAmigo like '%$where%' and
                            perfil.codigo = $user and 
                            amigo.ativo = 1
                        order by amigo.dateAceito desc
                        limit $limit offset $offset");
                    }else{
                        $result = mysqli_query($db,"
                        select perfil.codigo, case 
                                when amigo.perfil = perfil.codigo then amigo.amigo
                                when amigo.amigo = perfil.codigo then amigo.perfil
                            end as amigoCod,
                            amigo.dateAceito,
                            tmp1.codigo as codAmigo,
                            tmp1.username as nameAmigo,
                            tmp1.img as imgAmigo,
                            (select count(*) from amigo where amigo = $user or perfil = $user and amigo.ativo = 1) as qtdAmigos
                        from perfil 
                            join amigo on perfil.codigo = amigo.perfil or amigo.amigo
                            join (select * from perfil) as tmp1 on tmp1.codigo = amigoCod
                        where 
                            perfil.codigo = $user and 
                            amigo.ativo = 1
                        order by amigo.dateAceito desc
                        limit $limit offset $offset");
    
                    }
                    if($result){
                        while ($row = mysqli_fetch_array($result)) {
                            array_push($results, $row);
                        }
                    }
                    mysqli_close($db);
                    return $results;
                }
            }
            else exit;
        }
        */
    }
?>