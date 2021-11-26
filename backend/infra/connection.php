<?php
    include $_SERVER['DOCUMENT_ROOT'].'/backend/mailer/mailer.php';
    include $_SERVER['DOCUMENT_ROOT'].'/backend/gdrive/driver.php';
    function db_connection(){
        $db=false;
        $db_type=false;
        if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
            //echo "Connect with sqlite<br>";
            $db=new SQLite3("$_SERVER[DOCUMENT_ROOT]/backend/infra/dagama.db");
            $test=$db->exec("PRAGMA FOREIGN_KEYS=ON");
            if(!$test){
                echo "<br>Um erro de conexão com banco ocorreu<br>"; 
                exit;
            }
            $db_type='sqlite';
        }
        if(preg_match("/dagama.herokuapp/","$_SERVER[HTTP_HOST]")){
            $db=pg_connect(getenv("DATABASE_URL"));
            if (!$db) {
                echo "<br>Um erro de conexão com banco ocorreu<br>"; 
                exit;
            }
            $db_type='postgresql';
        }
        return ['db'=>$db,'db_type'=>$db_type];
    }

    /* FEED */
    function getFeed($offset,$limit=10){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                $result = $db->query("select * from interacao where isReaction is null and isSharing is null and ativo = 1 limit $limit offset $offset");
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db, "select * from interacao where isReaction is null and isSharing is null and ativo = true limit $limit offset $offset"));
                return $result;
            }
        }
        else exit;
    }
    function getPosts($offset,$limit=10){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                $result = $db->query("
                select 
                    interacao.codigo as codInteracao, 
                    interacao.post as codPost, 
                    interacao.isReaction as isReaction, 
                    interacao.texto as textoPost, 
                    interacao.data as dataPost, 
                    interacao.isSharing as isSharing, 
                    interacao.emote as emote,
                    interacao.ativo as ativo,
                    porto.codigo as codPorto, porto.nome as nomePorto, 
                    perfil.codigo as codPerfil, perfil.username as nomePerfil, perfil.img as iconPerfil
                from interacao
                    join perfil on interacao.perfil = perfil.codigo
                    left join porto on porto.codigo = interacao.porto
                where
                    interacao.ativo = 1 and
                    interacao.isReaction is null and
                    (interacao.post is null or interacao.isSharing is not null)
                limit $limit offset $offset");
                
                $results2 = $db->query("
                select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                    left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                    left join assunto on interacao_assunto.assunto = assunto.codigo
                where
                    interacao.ativo = 1");
                $assuntos = [];
                while ($row = $results2->fetchArray()) {
                    $assuntos[$row['interacao']][$row['codAssunto']] = $row;
                }

                $results3 = $db->query("
                select 
                    interacao.codigo as codInteracao, 
                    interacao.post as codPost, 
                    interacao.isReaction as isReaction, 
                    interacao.texto as textoPost, 
                    interacao.data as dataPost, 
                    interacao.isSharing as isSharing, 
                    interacao.emote as emote,
                    interacao.ativo as ativo,
                    porto.codigo as codPorto, porto.nome as nomePorto, 
                    perfil.codigo as codPerfil, perfil.username as nomePerfil, perfil.img as iconPerfil
                from interacao
                    join perfil on interacao.perfil = perfil.codigo
                    left join porto on porto.codigo = interacao.porto
                where
                    interacao.ativo = 1 and
                    interacao.isReaction is null and
                    interacao.post is not null and 
                    interacao.isSharing is null");
                $interacoes = [];
                while ($row = $results3->fetchArray()) {
                    $interacoes[$row['codPost']][$row['codInteracao']] = $row;
                }

                while ($row = $result->fetchArray()) {
                    $row['assuntos'] = $assuntos[$row['codInteracao']];
                    if(in_array($row['codInteracao'], array_keys($interacoes))){
                        $row['comentarios'] = $interacoes[$row['codInteracao']];
                    } else {
                        $row['comentarios'] = [];
                    }
                    // echo $row['codInteracao']."<br>";
                    // echo "<br>";
                    // print_r($row['comentarios']);
                    array_push($results, $row);
                }
                // array[0] = post 1, vitão, asdasdasdsa
                // array[0]['assuntos'] = 'assuntos'
                // array[0]['interacoes'][] = 'interacao'
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db, "
                select 
                    interacao.codigo as codInteracao, 
                    interacao.post as codPost, 
                    interacao.isReaction as isReaction, 
                    interacao.texto as textoPost, 
                    interacao.data as dataPost, 
                    interacao.isSharing as isSharing, 
                    interacao.emote as emote,
                    interacao.ativo as ativo,
                    porto.codigo as codPorto, porto.nome as nomePorto, 
                    perfil.codigo as codPerfil, perfil.username as nomePerfil, perfil.img as iconPerfil
                from interacao
                    join perfil on interacao.perfil = perfil.codigo
                    left join porto on porto.codigo = interacao.porto
                where
                    interacao.ativo = 1 and
                    interacao.isReaction is null and
                    (interacao.post is null or interacao.isSharing is not null)
                limit $limit offset $offset"));
                return $result;
            }
        }
        else exit;
    }
    /* -------- */
    getPosts(0,10);

    function getAllPorto($offset,$limit=10){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                $result=$db->query("select * from porto where ativo=1 limit $limit offset $offset");
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db,"select * from porto where ativo = true limit $limit offset $offset"));
                return $result;
            }
        }
        else exit;
    }
    function Login($email,$password){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $verify=$db->query("select codigo,senha as pass,ativo,img,username from perfil where perfil.email='$email'")->fetchArray();
                if(password_verify($password,$verify['pass'])) return $verify;
                else return false;
            }
            if($db_type=='postgresql'){
                $verify=pg_fetch_array(pg_query($db,"select codigo,senha as pass,ativo,img,username from perfil where perfil.email='$email'"));
                if(password_verify($password,$verify['pass'])) return $verify;
                else return false;
            }
        }
        else exit;
    }
    function Login2($email,$password){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $verify=$db->query("select codigo,senha as pass,ativo,img,username from perfil where perfil.email='$email'")->fetchArray();
                if("$password"=="$verify[pass]") return $verify;
                else return false;
            }
            if($db_type=='postgresql'){
                $verify=pg_fetch_array(pg_query($db,"select codigo,senha as pass,ativo,img,username from perfil where perfil.email='$email'"));
                if("$password"=="$verify[pass]") return $verify;
                else return false;
            }
        }
        else exit;
    }
    function Register($email, $password, $bdate, $username, $genero, $cidade,$photo){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
        $link='https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif';
        if($photo){
            $type=$photo['type'];
            $server_path=$photo['tmp_name'];
            $link="https://drive.google.com/uc?export=download&id=".insertFile("$type","$server_path","$FOLDERS[avatares]","avatar");
        }
        if($db){
            if($db_type == 'sqlite'){
                $verify = $db->exec("insert into perfil (cidade, email, senha, genero, username, datanasc,img) values ('".$cidade."', '".$email."', '".$password."', '".$genero."', '".$username."', '".$bdate."', '".$link."'".")");
                if($verify) return $verify;
                else return false;
            }
            if($db_type == 'postgresql'){
                $preparing = pg_prepare($db, "Register", "insert into perfil (cidade, email, senha, genero, username, datanasc,img) values ($1,$2,$3,$4,$5,$6,$7)");
                if($preparing){
                    $verify = pg_execute($db, "Register", array("$cidade","$email","$password","$genero","$username","$bdate","$link"));
                    if($verify) return $verify;
                    else return false;
                }
                else return false;
            }
        }
        else exit;  
    };

    /* FRIENDS */
    function suggestFriends($user, $limit, $offset) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            // Variáveis M - meses, A - min assuntos em comum, $B - top x assuntos, $U - usuario
            $M = 3; $A = 1; $B = 5; $U = $user;
            if($db_type=='sqlite'){
                $results=[];
                $result = $db->query("
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
                        tmp1.enviado = 'false' and
                        tmp1.codigo not in (
                            select case
                                    when amigo.perfil = perfil.codigo then amigo.amigo
                                    when amigo.amigo = perfil.codigo then amigo.perfil
                                end as amigo
                            from perfil
                                join amigo on perfil.codigo = amigo.perfil or perfil.codigo = amigo.amigo
                            where perfil.codigo = $U
                        ) and tmp1.codigo not in (select codigo from perfil where ativo = 0)
                limit $limit offset $offset");
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db, "
                select tmp1.codigo, tmp1.username, tmp1.img, tmp1.enviado, 
                    case
                        when solicitacao_amigo.amigo is null or solicitacao_amigo.ativo == false then 'false'
                        when solicitacao_amigo.amigo is not null or solicitacao_amigo.ativo == true then 'true'
                    end as enviado, 
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
                            when solicitacao_amigo.amigo is null or solicitacao_amigo.ativo == false then 'false'
                            when solicitacao_amigo.amigo is not null or solicitacao_amigo.ativo == true then 'true'
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
                        tmp1.enviado = 'false' and
                        tmp1.codigo not in (
                            select case
                                    when amigo.perfil = perfil.codigo then amigo.amigo
                                    when amigo.amigo = perfil.codigo then amigo.perfil
                                end as amigo
                            from perfil
                                join amigo on perfil.codigo = amigo.perfil or perfil.codigo = amigo.amigo
                            where perfil.codigo = $U
                        ) and tmp1.codigo not in (select codigo from perfil where ativo = false)
                limit $limit offset $offset"));
                return $result;
            }
        }
        else exit;
    }
    function sendFriendRequest($user, $friend) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $friendRequest = $db->exec("insert into SOLICITACAO_AMIGO (perfil, amigo, dateEnvio) values ($user, $friend, CURRENT_TIMESTAMP)");
            if($friendRequest) return $friendRequest;
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "sendFriendRequest", "insert into SOLICITACAO_AMIGO (perfil, amigo, dateEnvio) values ($1, $2, CURRENT_TIMESTAMP)");
            if($preparing){
                $friendRequest = pg_execute($db, "sendFriendRequest", array("$user","$friend"));
                if($friendRequest) return $friendRequest;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function unsendFriendRequest($user, $friend) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $friendRequest = $db->exec("update SOLICITACAO_AMIGO set ativo = 0 where perfil = $user and amigo = $friend");
            if($friendRequest) return $friendRequest;
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "unsendFriendRequest", "update SOLICITACAO_AMIGO set ativo = false where perfil = $1 and amigo = $2");
            if($preparing){
                $friendRequest = pg_execute($db, "unsendFriendRequest", array("$user","$friend"));
                if($friendRequest) return $friendRequest;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function confirmFriendRequest($user, $friend) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $friendRequest = $db->exec("update SOLICITACAO_AMIGO set ativo = 0 where perfil = $user and amigo = $friend");
            $friendAdd = $db->exec("insert into amigo (amigo, perfil) ($user, $friend)");
            if($friendAdd) return $friendAdd;
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "unsendFriendRequest", "update SOLICITACAO_AMIGO set ativo = false where perfil = $user and amigo = $friend");
            if($preparing){
                $friendRequest = pg_execute($db, "unsendFriendRequest", array("$user","$friend"));
                if(!$friendRequest) return false;
                $preparing2 = pg_prepare($db, "addFriend", "insert into amigo (amigo, perfil) values ($1, $2)");
                if($preparing2){
                    $friendAdd = pg_execute($db, "addFriend", array("$user","$friend"));
                    if($friendAdd) return $friendAdd;
                    else return false;
                } else return false;
            }
            else return false;
        }
        else exit;
    }
    function declineFriendRequest($user, $friend) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $friendRequest = $db->exec("update SOLICITACAO_AMIGO set ativo = 0 where amigo = $user and perfil = $friend");
            if($friendRequest) return $friendRequest;
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "declineFriendRequest", "update SOLICITACAO_AMIGO set ativo = 0 where amigo = $1 and perfil = $2");
            if($preparing){
                $friendRequest = pg_execute($db, "declineFriendRequest", array("$user","$friend"));
                if($friendRequest) return $friendRequest;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function getRequestAndFriends($user, $isOwner){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                $result = $db->query("select solicitacao_amigo.perfil, solicitacao_amigo.amigo, solicitacao_amigo.dateEnvio, solicitacao_amigo.ativo, amigo.perfil as otherPerfil, amigo.amigo as otherAmigo, amigo.ativo as otherAtivo from solicitacao_amigo
                    left join amigo on 
                        (solicitacao_amigo.perfil = amigo.perfil and solicitacao_amigo.amigo = amigo.amigo) or 
                        (solicitacao_amigo.amigo = amigo.perfil and solicitacao_amigo.perfil = amigo.amigo)
                where 
                    ".($isOwner ? "solicitacao_amigo.perfil = $user and
                    solicitacao_amigo.amigo" : "solicitacao_amigo.amigo = $user and
                    solicitacao_amigo.perfil")." not in (
                        select codigo from perfil where ativo = 0
                    )");
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db, "select solicitacao_amigo.perfil, solicitacao_amigo.amigo, solicitacao_amigo.dateEnvio, solicitacao_amigo.ativo, amigo.perfil as otherPerfil, amigo.amigo as otherAmigo, amigo.ativo as otherAtivo from solicitacao_amigo
                    left join amigo on 
                        (solicitacao_amigo.perfil = amigo.perfil and solicitacao_amigo.amigo = amigo.amigo) or 
                        (solicitacao_amigo.amigo = amigo.perfil and solicitacao_amigo.perfil = amigo.amigo)
                where 
                    ".($isOwner ? "solicitacao_amigo.perfil = $user and
                    solicitacao_amigo.amigo" : "solicitacao_amigo.amigo = $user and
                    solicitacao_amigo.perfil")." not in (
                        select codigo from perfil where ativo = 0
                    )"));
                return $result;
            }
        }
        else exit;
    }
    /* ---------------------------------------*/

    // BASICS
    function getPaises(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $results=[];
                $result = $db->query("select codigo,nome from pais");
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                return $results;
            }
        }
        else exit;
    };
    function getStates(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $results=[];
                $result = $db->query("select * from uf");
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                return $results;
            }
        }
        else exit;
    };
    function getCities(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $results=[];
                $result = $db->query("select * from cidade");
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                return $results;
            }
        }
        else exit;
    };
    function getLocais(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            $results=[];
            if($db_type == 'sqlite'){
                $response = $db->query("select cidade.codigo as codCidade, cidade.nome as nomeCidade, uf.codigo as codUf, uf.nome as nomeUf, pais.codigo as codPais, pais.nome as nomePais from cidade
                    join uf on cidade.uf = uf.codigo
                    join pais on uf.pais = pais.codigo
                group by cidade.codigo");
                if($response){
                    while ($row = $response->fetchArray()) {
                        array_push($results, $row);
                    }
                    return $results; 
                }
                else return false;
            }
            if($db_type == 'postgresql'){
                $response = pg_query($db, "select cidade.codigo as codCidade, cidade.nome as nomeCidade, uf.codigo as codUf, uf.nome as nomeUf, pais.codigo as codPais, pais.nome as nomePais from cidade
                    join uf on cidade.uf = uf.codigo
                    join pais on uf.pais = pais.codigo
                group by cidade.codigo");
                if($response){
                    while ($row = pg_fetch_array($response)) {
                        array_push($results, $row);
                    }
                    return $results; 
                }
                else return false;
            }
        }
        else exit;
    };
    function getAssuntos(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            $results=[];
            if($db_type == 'sqlite'){
                $response = $db->query("select * from assunto");
                if($response){
                    while ($row = $response->fetchArray()) {
                        array_push($results, $row);
                    }
                    return $results; 
                }
                else return false;
            }
            if($db_type == 'postgresql'){
                $response = pg_query($db, "select * from assunto");
                if($response){
                    while ($row = pg_fetch_array($response)) {
                        array_push($results, $row);
                    }
                    return $results; 
                }
                else return false;
            }
        }
        else exit;
    };
    function getPessoas(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            $results=[];
            if($db_type == 'sqlite'){
                $response = $db->query("select codigo, username, img from perfil where ativo = 1");
                if($response){
                    while ($row = $response->fetchArray()) {
                        array_push($results, $row);
                    }
                    return $results; 
                }
                else return false;
            }
            if($db_type == 'postgresql'){
                $response = pg_query($db, "select codigo, username, img from perfil where ativo = true");
                if($response){
                    while ($row = pg_fetch_array($response)) {
                        array_push($results, $row);
                    }
                    return $results; 
                }
                else return false;
            }
        }
        else exit;
    }
    /*---------------------------------------------------------*/

    //remover
    function getEmails(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select email from perfil")->fetchArray();
                if($response) return $response;
                else return false;
            }
            if($db_type == 'postgresql'){
                $response = pg_fetch_array(pg_query($db, "select email from perfil"));
                if($response) return $response;
                else return false;
            }
        }
        else exit;
    };
    //substitui getEmails na validação
    function emailExists($email){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select email from perfil where email='$email'");
                if($response) return $response->fetchArray();
                else return false;
            }
            if($db_type == 'postgresql'){
                $response = pg_query($db,"select email from perfil where email='$email'");
                if($response) return pg_fetch_array($response);
                else return false;
            }
        }
        else exit;
    };
    function getUserInfo($id){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select codigo, email, ativo, img, username from perfil where codigo='$id'");
                if($response) return $response->fetchArray();
                else return false;
            }
            if($db_type == 'postgresql'){
                if($db_type == 'postgresql'){
                    $response = pg_query($db,"select codigo, email, ativo, img, username from perfil where codigo='$id'");
                    if($response) return pg_fetch_array($response);
                    else return false;
                }
            }
        }
        else exit;
    };
    function getIdbyEmail($email){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select codigo from perfil where email='$email'");
                if($response) return $response->fetchArray()['codigo'];
                else return false;
            }
            if($db_type == 'postgresql'){
                if($db_type == 'postgresql'){
                    $response = pg_query($db,"select codigo from perfil where email='$email'");
                    if($response) return pg_fetch_array($response)['codigo'];
                    else return false;
                }
            }
        }
        else exit;
    };
    function activateUser($id){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->exec("update perfil set ativo='1' where codigo=$id");
                if($response) {
                    $res=$db->query("select email,senha as password from perfil where codigo='$id'");
                    if($res) return $res->fetchArray();
                    else return false;
                }
                else return false;
            }
            if($db_type == 'postgresql'){
                $preparing = pg_prepare($db, "ActivateUser","update perfil set ativo=true where codigo=$1");
                if($preparing){
                    $verify = pg_execute($db, "ActivateUser", array("$id"));
                    if($verify){
                        $response = pg_query($db,"select email,senha as password from perfil where codigo=$id");
                        if($response) return pg_fetch_array($response);
                        else return false;
                    } 
                    else return false;
                }
                else return false;
            }
        }
        else exit;
    }
    function addPorto($perfil,$nome,$descr,$img){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
        $link='https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif';
        if($img){
            $type=$img['type'];
            $server_path=$img['tmp_name'];
            $link="https://drive.google.com/uc?export=download&id=".insertFile("$type","$server_path","$FOLDERS[portos]","porto-avatar");
        }
        if($db){
            if($db_type == 'sqlite'){
                $verify = $db->exec("insert into porto (perfil,nome,descr,img) values ('".$perfil."', '".$nome."', '".$descr."', '".$link."'".")");
                $portoId = $db->lastInsertRowID();
                if($portoId) return $portoId;
                else return false;
            }
            if($db_type == 'postgresql'){
                $preparing = pg_prepare($db, "addPorto", "insert into porto (perfil,nome,descr,img) values ($1,$2,$3,$4)");
                if($preparing){
                    $verify = pg_execute($db, "addPorto", array("$perfil","$nome","$descr","$link"));
                    if($verify) return $verify;
                    else return false;
                }
                else return false;
            }
        }
        else exit; 
    }
    function getTotalPorto(){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $result=$db->query("select count(*) as total from porto where ativo=1");
                if($result){
                    return $result->fetchArray()['total'];
                }
                return false;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_array(pg_query($db,"select count(*) as total from porto where ativo=true"));
                return $result['total'];
            }
        }
        else exit;
    }
    function getPortInfo($porto){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select nome,descr,img from porto where codigo='$porto' and ativo=1");
                if($response) return $response->fetchArray();
                else return false;
            }
            if($db_type == 'postgresql'){
                if($db_type == 'postgresql'){
                    $response = pg_query($db,"select nome,descr,img from porto where codigo='$porto' and ativo=true");
                    if($response) return pg_fetch_array($response);
                    else return false;
                }
            }
        }
        else exit;
    }
?>