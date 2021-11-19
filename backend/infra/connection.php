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
    function suggestFriends($user, $limit, $offset) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                // Variáveis M - meses, A - min assuntos em comum, $B - top x assuntos, $U - usuario
                $M = 3; $A = 1; $B = 5; $U = $user;
                $result = $db->query("
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
                        when solicitacao_amigo.amigo is null then 'false'
                        when solicitacao_amigo.amigo is not null then 'true'
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
                order by camadas asc
                limit $limit offset $offset");
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db, "
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
                        when solicitacao_amigo.amigo is null then 'false'
                        when solicitacao_amigo.amigo is not null then 'true'
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
                order by camadas asc
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
            $preparing = pg_prepare($db, "Register", "insert into SOLICITACAO_AMIGO (perfil, amigo, dateEnvio) values ($1, $2, CURRENT_TIMESTAMP)");
            if($preparing){
                $friendRequest = pg_execute($db, "Register", array("$user","$friend"));
                if($friendRequest) return $friendRequest;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    /* -------- */
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
    function Register($email, $password, $bdate, $username, $genero, $pais,$photo){
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
                $verify = $db->exec("insert into perfil (pais, email, senha, genero, username, datanasc,img) values ('".$pais."', '".$email."', '".$password."', '".$genero."', '".$username."', '".$bdate."', '".$link."'".")");
                if($verify) return $verify;
                else return false;
            }
            if($db_type == 'postgresql'){
                $preparing = pg_prepare($db, "Register", "insert into perfil (pais, email, senha, genero, username, datanasc,img) values ($1,$2,$3,$4,$5,$6,$7)");
                if($preparing){
                    $verify = pg_execute($db, "Register", array("$pais","$email","$password","$genero","$username","$bdate","$link"));
                    if($verify) return $verify;
                    else return false;
                }
                else return false;
            }
        }
        else exit;  
    };

    // BASICS
    function getPaises(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select codigo from pais")->fetchArray();
                if($response) return $response;
                else return false;
            }
            if($db_type == 'postgresql'){
                $response = pg_fetch_array(pg_query($db, "select codigo from pais"));
                if($response) return $response;
                else return false;
            }
        }
        else exit;
    };
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
                if($verify) return $verify;
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