<?php
    include $_SERVER['DOCUMENT_ROOT'].'/backend/mailer/mailer.php';
    include $_SERVER['DOCUMENT_ROOT'].'/backend/gdrive/driver.php';
    function db_connection(){
        $db=false;
        $db_type=false;
        if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
            //echo "Connect with sqlite<br>";
            $db=new SQLite3("$_SERVER[DOCUMENT_ROOT]/backend/infra/dagama.db");
            // $sql = file_get_contents("$_SERVER[DOCUMENT_ROOT]/backend/infra/dagama.sql");
            // $qr = $db->exec($sql);
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

    /* BASICS */
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
    function getGrupos(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            $results=[];
            if($db_type == 'sqlite'){
                $response = $db->query("select codigo, nome from porto where ativo = 1");
                if($response){
                    $results=[];
                    while ($row = $response->fetchArray()) {
                        array_push($results, $row);
                    }
                    return $results;
                }                
                else return false;
            }
            if($db_type == 'postgresql'){
                $response = pg_query($db, "select codigo, nome from porto where ativo = true");
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
    function getAllUserInfo($offset,$limit=10){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            $results=[];
            if($db_type == 'sqlite'){
                $response = $db->query("select * from perfil limit $limit offset $offset");
                if($response){
                    while ($row = $response->fetchArray()) {
                        array_push($results, $row);
                    }
                    return $results; 
                }
                else return false;
            }
        }
        else exit;
    }
    function countAllUsers(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            $results=[];
            if($db_type == 'sqlite'){
                $response = $db->query("select count(*)as total from perfil");
                if($response) return $response->fetchArray()['total'];
                else return false;
            }
        }
        else exit;
    }
    /*---------------------------------------------------------*/

    /* LOCAIS */
    function getPaises(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $results = [];
                $result = $db->query("select * from pais where ativo = 1");
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                return $results;
            }
            if($db_type == 'postgresql'){
                $response = pg_query($db, "select * from pais where ativo = true");
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
    function addPais($nome){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("insert into pais (nome) values ('$nome')");
            if($response) return $db->lastInsertRowID();
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "pais", "insert into pais (nome) values ($1) returning codigo");
            if($preparing){
                $response = pg_execute($db, "pais", array("$nome"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function delPais($pais){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update pais set ativo = 0 where codigo = $pais");
            if($response) return $response;
            else return false;
        }
        if($db_type == 'postgresql'){
            $response = pg_prepare($db, "delPais", "update pais set ativo = false where codigo = $1");
            if($response){
                $response = pg_execute($db, "delPais", array("$pais"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function getStates(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $results=[];
                $result = $db->query("select * from uf where ativo = 1");
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                return $results;
            }
            if($db_type == 'postgresql'){
                $response = pg_query($db, "select * from uf where ativo = true");
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
    function addEstado($nome, $pais){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("insert into uf (nome, pais) values ('$nome', $pais)");
            if($response) return $db->lastInsertRowID();
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "estado", "insert into uf (nome, pais) values ($1, $2) returning codigo");
            if($preparing){
                $response = pg_execute($db, "estado", array("$nome", "$pais"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function delEstado($estado){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update uf set ativo = 0 where codigo = $estado");
            if($response) return $response;
            else return false;
        }
        if($db_type == 'postgresql'){
            $response = pg_prepare($db, "delEstado", "update uf set ativo = false where codigo = $1");
            if($response){
                $response = pg_execute($db, "delEstado", array("$estado"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function getCities(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $results=[];
                $result = $db->query("select * from cidade where ativo = 1");
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                return $results;
            }
            if($db_type == 'postgresql'){
                $response = pg_query($db, "select * from cidade where ativo = true");
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
    function addCidade($nome, $estado){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("insert into cidade (nome, uf) values ('$nome', $estado)");
            if($response) return $db->lastInsertRowID();
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "cidade", "insert into cidade (nome, uf) values ($1, $2) returning codigo");
            if($preparing){
                $response = pg_execute($db, "cidade", array("$nome", "$estado"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function delCidade($cidade){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update cidade set ativo = 0 where codigo = $cidade");
            if($response) return $response;
            else return false;
        }
        if($db_type == 'postgresql'){
            $response = pg_prepare($db, "delCidade", "update cidade set ativo = false where codigo = $1");
            if($response){
                $response = pg_execute($db, "delCidade", array("$cidade"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
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
    /*---------------------------------------------------------*/
    
    /* ASSUNTOS */
    function addAssunto($nome){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("insert into assunto (nome) values ('$nome')");
            if($response) return $response;
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "assunto", "insert into assunto (nome) values ($1)");
            if($preparing){
                $response = pg_execute($db, "assunto", array("$nome"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function delAssunto($assunto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update assunto set ativo = 0 where codigo = $assunto");
            if($response) return $response;
            else return false;
        }
        if($db_type == 'postgresql'){
            $response = pg_prepare($db, "delAssunto", "update assunto set ativo = false where codigo = $1");
            if($response){
                $response = pg_execute($db, "delAssunto", array("$assunto"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    /*---------------------------------------------------------*/

    /* LOGIN/REGISTER */
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
    function getEmails(){ //remover
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
    function emailExists($email){ //substitui getEmails na validação
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
    /*-----------------------------------------*/

    /* USER */
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
    function deactivateUser($user){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->exec("update perfil set ativo='0' where codigo=$user");
                return true;
            }
            if($db_type == 'postgresql'){
                $response = pg_query($db,"update perfil set ativo=false where codigo=$user");
                return pg_fetch_array($response);
            } 
        }
        else exit;
    }
    function changeUserName($id,$name){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->exec("update perfil set username='$name' where codigo=$id");
                return true;
            }
            if($db_type == 'postgresql'){
                        $response = pg_query($db,"update perfil set nome=$name where codigo=$id");
                        return pg_fetch_array($response);
                    } 
        }
        else exit;
    }
    function changeUserEmail($id,$email){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->exec("update perfil set email='$email' where codigo=$id");
                return true;
            }
            if($db_type == 'postgresql'){
                        $response = pg_query($db,"update perfil set email='$email' where codigo=$id");
                        return pg_fetch_array($response);
                    } 
        }
        else exit;
    }
    function changeUserSenha($id,$senha){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->exec("update perfil set\\ senha='$senha' where codigo=$id");
                return true;
            }
            if($db_type=='postgresql'){ // FIXME: tem que deixar igual ao de cima
                $result=pg_fetch_all(pg_query($db, ""));
                return $result;
            }
        }
    }
    /*----------------------------------------*/

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
    function getPosts($user, $offset,$limit=10){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $postsArray=[];
                $postsOriginais = $db->query("
                select
                    interacao.codigo as codInteracao, 
                    interacao.post as codPost, 
                    interacao.isReaction as isReaction, 
                    interacao.texto as textoPost, 
                    interacao.data as dataPost,
                    interacao.isSharing as isSharing, 
                    interacao.emote as emote,
                    interacao.ativo as ativo,
                    cidade.nome as nomeCidade,
                    uf.nome as nomeUF,
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
                    left join cidade on interacao.local = cidade.codigo
                    left join uf on cidade.uf = uf.codigo
                    left join pais on uf.pais = pais.codigo
                    left join porto on interacao.porto = porto.codigo
                where interacao.ativo = 1
                group by codPost
                order by tmp1.data desc
                limit $limit offset $offset");

                while($row = $postsOriginais->fetchArray()){
                    $postsArray[$row['codInteracao']] = $row;

                    $resCitacoesParent = $db->query("select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao join perfil on perfil.codigo = citacao.perfil where citacao.ativo = 1 and citacao.interacao = $row[codInteracao]");
                    $citacoes = [];
                    while ($row2 = $resCitacoesParent->fetchArray()) {
                        $citacoes[] = $row2;
                    }
                    $postsArray[$row['codInteracao']]['citacoes'] = $citacoes;
                    
                    $resAssuntosParent = $db->query("
                    select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                        left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                        left join assunto on interacao_assunto.assunto = assunto.codigo
                    where
                        interacao.ativo = 1");
                    $assuntos = [];
                    while ($row2 = $resAssuntosParent->fetchArray()) {
                        $assuntos[] = $row2;
                    }
                    $postsArray[$row['codInteracao']]['assuntos'] = $assuntos;
                    $temInteracoes = $db->query("
                    select
                        interacao.codigo as codInteracao, 
                        interacao.post as codPost, 
                        interacao.isReaction as isReaction, 
                        interacao.texto as textoPost, 
                        interacao.data as dataPost,
                        interacao.isSharing as isSharing, 
                        interacao.emote as emote,
                        interacao.ativo as ativo,
                        cidade.nome as nomeCidade,
                        uf.nome as nomeUF,
                        pais.nome as nomePais,
                        perfil.codigo as codPerfil, 
                        perfil.username as nomePerfil,
                        perfil.img as iconPerfil
                    from interacao
                        join perfil on interacao.perfil = perfil.codigo
                        left join cidade on cidade.codigo = interacao.local
                        left join uf on cidade.uf = uf.codigo
                        left join pais on uf.pais = pais.codigo
                    where interacao.isSharing is null and interacao.post = ".$row['codInteracao']);
                    $childInteracoes = [];
                    if($temInteracoes){
                        while($row3 = $temInteracoes->fetchArray()){
                            $childInteracoes[$row3['codInteracao']] = $row3;
                            $resCitacoesChild = $db->query("select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao join perfil on perfil.codigo = citacao.perfil where citacao.ativo = 1 and citacao.interacao = $row3[codInteracao]");
                            $citacoes = [];
                            while ($row4 = $resCitacoesChild->fetchArray()) {
                                $citacoes[] = $row4;
                            }
                            $childInteracoes[$row3['codInteracao']]['citacoes'] = $citacoes;
                            
                            $resAssuntosChild = $db->query("
                            select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                                left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                                left join assunto on interacao_assunto.assunto = assunto.codigo
                            where
                                interacao.ativo = 1");
                            $assuntos = [];
                            while ($row5 = $resAssuntosChild->fetchArray()) {
                                $assuntos[] = $row5;
                            }
                            $childInteracoes[$row3['codInteracao']]['assuntos'] = $assuntos;
                        }
                    }
                    $postsArray[$row['codInteracao']]['comentarios'] = $childInteracoes;
                }
                return $postsArray;

            }
            if($db_type=='postgresql'){ // FIXME: tem que deixar igual ao de cima
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
    function getOriginalPost($post){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
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
                        cidade.nome as nomeCidade,
                        uf.nome as nomeUF,
                        pais.nome as nomePais,
                        porto.codigo as codPorto,
                        porto.nome as nomePorto,
                        perfil.codigo as codPerfil, 
                        perfil.username as nomePerfil,
                        perfil.img as iconPerfil
                    from interacao
                        join perfil on interacao.perfil = perfil.codigo
                        left join porto on interacao.porto = porto.codigo
                        left join cidade on interacao.local = cidade.codigo
                        left join uf on cidade.uf = uf.codigo
                        left join pais on uf.pais = pais.codigo
                    where interacao.codigo = $post");
                
                $results2 = $db->query("
                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao join perfil on perfil.codigo = citacao.perfil where citacao.ativo = 1
                ");
                $citacoes = [];
                while ($row = $results2->fetchArray()) {
                    $citacoes[$row['interacao']][$row['codPerfil']] = $row;
                }
                
                $results3 = $db->query("
                select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                    left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                    left join assunto on interacao_assunto.assunto = assunto.codigo
                where
                    interacao.ativo = 1");
                $assuntos = [];
                while ($row = $results3->fetchArray()) {
                    $assuntos[$row['interacao']][$row['codAssunto']] = $row;
                }

                
                $response = $result->fetchArray();
                if(in_array($response['codInteracao'], array_keys($assuntos))){
                    $response['assuntos'] = $assuntos[$response['codInteracao']];
                } else {
                    $response['assuntos'] = [];
                }
                if(in_array($response['codInteracao'], array_keys($citacoes))){
                    $response['citacoes'] = $citacoes[$response['codInteracao']];
                } else {
                    $response['citacoes'] = [];
                }
                return $response;
            }
            if($db_type=='postgresql'){ // FIXME: tem que deixar igual ao de cima
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
                    porto.codigo as codPorto,
                    porto.nome as nomePorto,
                    perfil.codigo as codPerfil, 
                    perfil.username as nomePerfil,
                    perfil.img as iconPerfil
                from interacao
                    join perfil on interacao.perfil = perfil.codigo
                    left join porto on interacao.porto = porto.codigo
                where interacao.codigo = $post"));
                if($result) return $result;
                else return false;
            }
        }
        else exit;
    }
    /*-----------------------------------------*/    

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
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db, ""));
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
            $hasRequest = $db->query("select * from SOLICITACAO_AMIGO where (perfil = $user and amigo = $friend) or (amigo = $user and perfil = $friend)");
            $hasRequest = $hasRequest->fetchArray();
            if($hasRequest){
                if($hasRequest['ativo'] == 0) {
                    $friendRequest = $db->exec("update SOLICITACAO_AMIGO set ativo = 1 where (amigo = $user and perfil = $friend) or (perfil = $user and amigo = $friend)");    
                    if($friendRequest) return $friendRequest;
                    else return false;
                }
            } else {
                $friendRequest = $db->exec("insert into SOLICITACAO_AMIGO (perfil, amigo, dateEnvio) values ($user, $friend, CURRENT_TIMESTAMP)");
                if($friendRequest) return $friendRequest;
                else return false;
            }
        }
        if($db_type == 'postgresql'){ // FIXME: deixar igual ao de cima
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
            $friendRequest = $db->exec("update SOLICITACAO_AMIGO set ativo = 0 where amigo = $user and perfil = $friend");
            $friendAdd = $db->exec("insert into amigo (amigo, perfil, dateAceito) values ($user, $friend, CURRENT_TIMESTAMP)");
            if($friendAdd) return $friendAdd;
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "unsendFriendRequest", "update SOLICITACAO_AMIGO set ativo = false where amigo = $user and perfil = $friend");
            if($preparing){
                $friendRequest = pg_execute($db, "unsendFriendRequest", array("$user","$friend"));
                if(!$friendRequest) return false;
                $preparing2 = pg_prepare($db, "addFriend", "insert into amigo (amigo, perfil, dateAceito) values ($1, $2, CURRENT_TIMESTAMP)");
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
                $result = $db->query("
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
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db, "select perfil.username as nome, perfil.img as img, solicitacao_amigo.dateEnvio as data, solicitacao_amigo.perfil, solicitacao_amigo.amigo as amigocod, amigo.perfil as otherPerfil, amigo.amigo as otherAmigo from solicitacao_amigo, perfil
                left join amigo on 
                    (solicitacao_amigo.perfil = amigo.perfil and solicitacao_amigo.amigo = amigo.amigo) or 
                    (solicitacao_amigo.amigo = amigo.perfil and solicitacao_amigo.perfil = amigo.amigo)
            where 
                perfil.codigo = solicitacao_amigo.perfil and
                solicitacao_amigo.perfil not in (
                    select codigo from perfil where ativo = false
                ) and
                perfil.ativo = true and
                solicitacao_amigo.amigo = $user and
                solicitacao_amigo.ativo = true"));
                return $result;
            }
        }
        else exit;
    }
    function delFriend($user, $friend){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $delFriend = $db->exec("update amigo set ativo = 0 where (perfil = $user and amigo = $friend) or (amigo = $user and perfil = $friend)");
            if($delFriend) return $delFriend;
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "delAmigo", "update amigo set ativo = 0 where (perfil = $1 and amigo = $2) or (amigo = $1 and perfil = $2)");
            if($preparing){
                $delFriend = pg_execute($db, "delAmigo", array("$user","$friend"));
                if($delFriend) return $delFriend;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function getFriends($user, $offset, $limit=10){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                $result = $db->query("
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
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db, "
                select perfil.codigo, case 
                        when amigo.perfil = perfil.codigo then amigo.amigo
                        when amigo.amigo = perfil.codigo then amigo.perfil
                    end as amigoCod,
                    amigo.dateAceito,
                    tmp1.codigo as codAmigo,
                    tmp1.username as nameAmigo,
                    tmp1.img as imgAmigo,
                    (select count(*) from amigo where amigo = $user or perfil = $user and amigo.ativo = true) as qtdAmigos
                from perfil 
                    join amigo on perfil.codigo = amigo.perfil or amigo.amigo
                    join (select * from perfil) as tmp1 on tmp1.codigo = amigoCod
                where 
                    perfil.codigo = $user and 
                    amigo.ativo = true
                order by amigo.dateAceito desc
                limit $limit offset $offset"));
                return $result;
            }
        }
        else exit;
    }
    /* ---------------------------------------*/

    /* PORTO */
    function getAllPorto($user, $isOwner, $offset, $limit=10){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                $result=$db->query("
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
                            perfil.codigo = $user
                    ) as tmp1 on porto.codigo = tmp1.porto
                where 
                    porto.ativo = 1
                    ".($isOwner ? " and participa = true" : "")."
                limit $limit offset $offset");
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db, "
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
                            perfil.codigo = $user
                    ) as tmp1 on porto.codigo = tmp1.porto
                where 
                    porto.ativo = 1
                    ".($isOwner ? " and participa = true" : "")."
                limit $limit offset $offset"));
                return $result;
            }
        }
        else exit;
    }
    function getUserPorto($user, $offset, $limit=10){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $result=$db->query("
                select * from porto
                where 
                    porto.ativo = 1 and
                    porto.perfil = $user
                limit $limit offset $offset");
                if($result) {
                    $results = [];
                    while ($row = $result->fetchArray()) {
                        array_push($results, $row);
                    }
                    return $results;
                }
                else return false;
            }
            if($db_type=='postgresql'){
                $result = pg_query($db, "
                select * from porto
                where 
                    porto.ativo = 1 and
                    porto.perfil = $user
                limit $limit offset $offset");
                if($result) {
                    $results = [];
                    while ($row = $result->fetchArray()) {
                        array_push($results, $row);
                    }
                    return $results;
                }
                else return false;
            }
        }
        else exit;
    }
    function getUserPortoQtd($user){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $result=$db->query("
                select count(*) as total from porto
                where 
                    porto.ativo = 1 and
                    porto.perfil = $user");
                if($result) return $result->fetchArray()['total'];
                else return false;
            }
            if($db_type=='postgresql'){
                $result = pg_fetch_all(pg_query($db, "
                select count(*) as total from porto
                where 
                    porto.ativo = 1 and
                    porto.perfil = $user"));
                if($result) return $result['total'];
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
    function getPortInfo($porto, $user){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("
                select 
                    porto.codigo as codigo, 
                    porto.nome as nome, 
                    porto.descr as descr, 
                    porto.img as img, 
                    perfil.codigo as codAdm, 
                    perfil.username as nomeAdm, 
                    perfil.img as imgAdm, 
                    (select case 
                        when porto.perfil = perfil.codigo or (porto_participa.perfil = perfil.codigo and porto_participa.ativo = 1) then true
                        else false
                    end as participa
                    from perfil 
                        join porto_participa on perfil.codigo = porto_participa.perfil
                        join porto on porto_participa.porto = porto.codigo
                    where 
                        porto_participa.ativo = 1 and
                        perfil.codigo = $user and
                        porto.codigo = $porto) as participa,
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
                order by porto_participa.dataregis desc");
                if($response) return $response->fetchArray();
                else return false;
            }
            if($db_type == 'postgresql'){
                if($db_type == 'postgresql'){
                    $response = pg_query($db,"
                    select 
                    porto.codigo as codigo, 
                    porto.nome as nome, 
                    porto.descr as descr, 
                    porto.img as img, 
                    perfil.codigo as codAdm, 
                    perfil.username as nomeAdm, 
                    perfil.img as imgAdm, 
                    (select case 
                        when porto.perfil = perfil.codigo or (porto_participa.perfil = perfil.codigo and porto_participa.ativo = 1) then true
                        else false
                    end as participa
                    from perfil 
                        join porto_participa on perfil.codigo = porto_participa.perfil
                        join porto on porto_participa.porto = porto.codigo
                    where 
                        porto_participa.ativo = 1 and
                        perfil.codigo = $user) as participa,
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
                    order by porto_participa.dataregis desc");
                    if($response) return pg_fetch_array($response);
                    else return false;
                }
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
    function delPorto($porto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        // $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
        // $link='https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif';
        // if($img){
        //     $type=$img['type'];
        //     $server_path=$img['tmp_name'];
        //     $link="https://drive.google.com/uc?export=download&id=".insertFile("$type","$server_path","$FOLDERS[portos]","porto-avatar");
        // }
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->exec("update porto set ativo = 0 where codigo = $porto");
                if($response) return $response;
                else return false;
            }
            if($db_type == 'postgresql'){
                $preparing = pg_prepare($db, "delPorto", "update porto set ativo = 0 where codigo = $1");
                if($preparing){
                    $response = pg_execute($db, "delPorto", array("$porto"));
                    if($response) return $response;
                    else return false;
                }
                else return false;
            }
        }
        else exit; 
    }
    function entrarPorto($user, $porto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->query("select case 
                when porto_participa.ativo = 0 then 'off' 
                when porto_participa.ativo = 1 then 'on' 
            end as participa from porto_participa 
            where perfil = $user and porto = $porto");
            $response = $response->fetchArray();
            if($response['participa'] == 'off') {
                $response2 = $db->exec("update porto_participa set ativo = 1, dataregis = CURRENT_TIMESTAMP where perfil = $user and porto = $porto");
                if($response2) return $response2;
                else return $response2;
            } else {
                $response2 = $db->exec("insert into porto_participa (perfil, porto) values ($user, $porto)");
                if($response2) return $response2;
                else return $response2;
            }

        }
        if($db_type == 'postgresql'){
            $response = pg_query($db, "select case 
                when porto_participa.ativo = false then 'off' 
                when porto_participa.ativo = true then 'on' 
            end as participa from porto_participa 
            where perfil = $user and porto = $porto");
            $response = pg_fetch_array($response);
            if($response['participa'] == 'off') {
                $response2 = pg_prepare($db, "entrarPorto", "update porto_participa set ativo = true, dataregis = CURRENT_TIMESTAMP where perfil = $1 and porto = $2)");                
                $response2 = pg_execute($db, "entrarPorto", array("$user","$porto"));
                if($response2) return $response2;
                else return false;
            } else {
                $response2 = pg_prepare($db, "entrarPorto", "insert into porto_participa (perfil, porto) values ($1, $2)");
                $response2 = pg_execute($db, "entrarPorto", array("$user","$porto"));
                if($response2) return $response2;
                else return false;
            }
            
        }
        else exit;
    }
    function sairPorto($user, $porto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update porto_participa set ativo = 0 where perfil = $user and porto = $porto");
            if($response) return $response;
            else return false;
        }
        if($db_type == 'postgresql'){
            $response = pg_prepare($db, "sairPorto", "update porto_participa set ativo = false where perfil = $1 and porto = $2");
            if($response){
                $response = pg_execute($db, "sairPorto", array("$user","$porto"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function editarPorto($porto,$newname,$newdescr,$newimg,$oldimgid){
        echo "<pre>";
        var_dump($porto);
        var_dump($newname);
        var_dump($newdescr);
        var_dump($newimg);
        echo "</pre>";
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
        $link = null;
        if($newimg){
            $type=$newimg['type'];
            $server_path=$newimg['tmp_name'];
            $link="https://drive.google.com/uc?export=download&id=".insertFile("$type","$server_path","$FOLDERS[portos]","porto-avatar");
            if($oldimgid){
                rmFile($oldimgid);
            }
        }
        if($db){
            if($db_type == 'sqlite'){
                $verify = $db->exec("update porto set nome='$newname',descr='$newdescr' ".($link ? ",img='$link'" : " ")." where codigo=$porto and ativo=1");
                if($verify) return $verify;
                else return false;
            }
        }
        else exit; 
    }
    function getPostsOnPorto($porto, $offset, $limit=10){
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
                    cidade.nome as nomeCidade,
                    uf.nome as nomeUF,
                    pais.nome as nomePais,
                    perfil.codigo as codPerfil, perfil.username as nomePerfil, perfil.img as iconPerfil
                from interacao
                    join porto on interacao.porto = porto.codigo
                    join perfil on interacao.perfil = perfil.codigo
                    left join cidade on interacao.local = cidade.codigo
                    left join uf on cidade.uf = uf.codigo
                    left join pais on uf.pais = pais.codigo
                where 
                    interacao.ativo = 1 and
                    porto.codigo = $porto
                order by interacao.data desc
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
                    interacao.post is not null and
                    interacao.isSharing is null");
                $interacoes = [];
                while ($row = $results3->fetchArray()) {
                    $interacoes[$row['codPost']][$row['codInteracao']] = $row;
                }

                $results4 = $db->query("
                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao join perfil on perfil.codigo = citacao.perfil where citacao.ativo = 1
                ");
                $citacoes = [];
                while ($row = $results4->fetchArray()) {
                    $citacoes[$row['interacao']][$row['codPerfil']] = $row;
                }

                while ($row = $result->fetchArray()) {
                    $row['assuntos'] = $assuntos[$row['codInteracao']];
                    if(in_array($row['codInteracao'], array_keys($interacoes))){
                        $row['comentarios'] = $interacoes[$row['codInteracao']];
                    } else {
                        $row['comentarios'] = [];
                    }
                    if(in_array($row['codInteracao'], array_keys($citacoes))){
                        $row['citacoes'] = $citacoes[$row['codInteracao']];
                    } else {
                        $row['citacoes'] = [];
                    }
                    array_push($results, $row);
                }
                return $results;
            }
            if($db_type=='postgresql'){ // FIXME: tem que deixar igual ao de cima
                $result=pg_fetch_all(pg_query($db, ""));
                return $result;
            }
        }
        else exit;
    }
    function getPortoParticipants($porto, $offset, $limit=10){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                $result = $db->query("                
                select 
                porto.codigo as codPorto,
                porto_participa.dataregis as dataRegis,
                perfil.codigo as codPart,
                perfil.username as nomePart,
                perfil.img as imgPart
                from porto
                    left join porto_participa on porto.codigo = porto_participa.porto
                    left join perfil on porto_participa.perfil = perfil.codigo
                where 
                    porto_participa.ativo = 1 and
                    porto.codigo = $porto
                limit $limit offset $offset");
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                return $results;
            }
            if($db_type=='postgresql'){
                $result=pg_fetch_all(pg_query($db, "                
                select 
                porto.codigo as codPorto,
                porto_participa.dataregis as dataRegis,
                perfil.codigo as codPart,
                perfil.username as nomePart,
                perfil.img as imgPart
                from porto
                    left join porto_participa on porto.codigo = porto_participa.porto
                    left join perfil on porto_participa.perfil = perfil.codigo
                where 
                    porto_participa.ativo = true and
                    porto.codigo = $porto
                limit $limit offset $offset"));
                return $result;
            }
        }
        else exit;
    }
    function upsertSelo($perfil,$porto){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response=$db->query("select * from selouser where perfil=$perfil and porto=$porto");
                $response2=$db->query("
                select 
                tmp1.perfil,reaction_porcent,comments_porce
                from 
                (
                    select 
                        tmp.perfil as perfil,cast(tmp.reaction_qt*100/tmp.total as real) as reaction_porcent
                    from
                    (
                        select 
                        reactions.perfil as perfil,reactions.qt as reaction_qt,(
                                select 
                                count(*) as total
                                from 
                                interacao 
                                    join porto on porto.codigo=interacao.porto
                                where 
                                porto.codigo=$porto and 
                                interacao.post is null and interacao.ativo=1 and datetime(interacao.data) between  datetime(date('now','weekday 0','-14 days')) and datetime(date('now','weekday 0','-7 days'))
                            ) as total
                        from(
                        select 
                            interacao.perfil as perfil,count(*) as qt
                            from 
                            interacao 
                            where
                            interacao.isReaction is not null and interacao.ativo=1
                            and datetime(interacao.data) between  datetime(date('now','weekday 0','-14 days')) and datetime(date('now','weekday 0','-7 days'))
                            and interacao.post in(
                                select 
                                interacao.codigo
                                from 
                                interacao 
                                    join porto on porto.codigo=interacao.porto
                                where 
                                porto.codigo=$porto and 
                                interacao.post is null and interacao.ativo=1
                            and datetime(interacao.data) between  datetime(date('now','weekday 0','-14 days')) and datetime(date('now','weekday 0','-7 days'))
                            )
                            group by interacao.perfil
                        ) as reactions
                    )as tmp
                ) as tmp1
                ,(
                    select 
                    tmp2.perfil as perfil,cast (tmp2.qt*100/tmp2.total as real) as comments_porce
                    from
                    (
                        select 
                        comments.perfil as perfil,comments.qt as qt
                        ,(
                            select 
                            interacao.codigo
                            from 
                            interacao 
                                join porto on porto.codigo=interacao.porto
                            where 
                            porto.codigo=$porto and 
                            interacao.post is null and interacao.ativo=1
                            and datetime(interacao.data) between  datetime(date('now','weekday 0','-14 days')) and datetime(date('now','weekday 0','-7 days'))
                        ) as total
                        from(
                            select 
                            interacao.perfil as perfil,count(*) as qt
                            from 
                            interacao 
                            where
                            interacao.isReaction is null and
                            interacao.isSharing is null and interacao.ativo=1
                            and datetime(interacao.data) between  datetime(date('now','weekday 0','-14 days')) and datetime(date('now','weekday 0','-7 days'))
                            and interacao.post in(
                                select 
                                interacao.codigo
                                from 
                                interacao 
                                    join porto on porto.codigo=interacao.porto
                                where 
                                porto.codigo=$porto and 
                                interacao.post is null and interacao.ativo=1
                            and datetime(interacao.data) between  datetime(date('now','weekday 0','-14 days')) and datetime(date('now','weekday 0','-7 days'))
                            )
                            group by interacao.perfil
                        ) as comments
                    ) as tmp2
                ) as tmp2
                where 
                    tmp1.perfil=tmp2.perfil and 
                    tmp1.perfil=$perfil
                ");
                if($response2){
                    $response2=$response2->fetchArray();
                    $selo='nenhum';
                    $reaction_p=$response2["reaction_porcent"];
                    $comment_p=$response2["comments_porce"];
                    if(($reaction_p>=25)&&($comment_p>=10)){
                        $selo='fa';
                    }
                    if(($reaction_p>=50)&&($comment_p>=20)){
                        $selo='super-fa';
                    }
                    if($reaction_p>=75&&$comment_p>=30){
                        $selo='ultra-fa';
                    }
                        
                    echo "reaÃ§ao:$reaction_p<br>";
                    echo "comment:$comment_p<br>";
                    echo "selo:$selo<br>";
                    if($response->fetchArray()){
                        switch($selo){
                            case "fa":
                                $response=$db->exec("update SELOUSER set selo=3,dateVal=datetime(date('now'),'weekday 0','+7 days') where porto=$porto and perfil=$perfil");
                                break;
                            case "super-fa":
                                $response=$db->exec("update SELOUSER set selo=2,dateVal=datetime(date('now'),'weekday 0','+7 days') where porto=$porto and perfil=$perfil");
                                break;
                            case "ultra-fa":
                                $response=$db->exec("update SELOUSER set selo=1,dateVal=datetime(date('now'),'weekday 0','+7 days') where porto=$porto and perfil=$perfil");
                                break;
                        }
                    }
                    else{
                        switch($selo){
                            case "nenhum":
                                $response=$db->exec("delete from selouser where perfil=$perfil and porto=$porto");
                                break;
                            case "fa":
                                $response=$db->exec("insert into SELOUSER(perfil,selo,porto,dateVal) VALUES($perfil,3,$porto,datetime(date('now'),'weekday 0','+7 days'))");
                                break;
                            case "super-fa":
                                $response=$db->exec("insert into SELOUSER(perfil,selo,porto,dateVal) VALUES($perfil,2,$porto,datetime(date('now'),'weekday 0','+7 days'))");
                                break;
                            case "ultra-fa":
                                $response=$db->exec("insert into SELOUSER(perfil,selo,porto,dateVal) VALUES($perfil,1,$porto,datetime(date('now'),'weekday 0','+7 days'))");
                                break;
                        }
                    }
                }
                else return false;
            }
        }
        else exit;
    }
    /*-----------------------------------*/
    
    /* INTERAÇÕES */
    function addInteracao($perfil, $texto, $perfil_posting, $porto, $isSharing, $post, $isReaction, $emote, $local){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            // echo $perfil_posting;
            $response = $db->exec("insert into interacao (perfil, texto, perfil_posting, porto, isSharing, post, isReaction, emote, local) values 
            ($perfil, 
            '".($texto ? $texto : '')."', 
            ".($perfil_posting ? $perfil_posting : 'null').", 
            ".($porto ? $porto : 'null').", 
            ".($isSharing ? $isSharing : 'null').", 
            ".($post ? $post : 'null').", 
            ".($isReaction ? $isReaction : 'null').", 
            ".($emote ? "'".$emote."'" : 'null').", 
            ".($local ? $local : 'null').")");
            if($response) return $db->lastInsertRowID();
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "interacao", "insert into interacao (perfil, texto, perfil_posting, porto, isSharing, post, isReaction, emote) values ($1, $2, $3, $4, $5, $6, $7, $8)");
            if($preparing){
                $response = pg_execute($db, "interacao", array($perfil, "$texto", $perfil_posting, $porto, $isSharing, $post, $isReaction, "$emote"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function delInteracao($post){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update interacao set ativo = 0 where codigo = $post");
            if($response) {
                // $response2 = $db->exec("update interacao set ativo = 0 where post in (select codigo from interacao where ativo = 0)");                
                return $response;
            }
            else return false;
        }
        if($db_type == 'postgresql'){
            // $response = pg_prepare($db, "delInteracao", "update interacao set ativo = false where codigo = $1");
            // if($response){
            //     $response = pg_execute($db, "delInteracao", array("$post"));
            //     if($response) return $response;
            //     else return false;
            // }
            // else return false;
        }
        else exit;
    }
    function addCitacaoInteracao($user, $post){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("insert into citacao (perfil, interacao) values ($user, $post)");
            if($response) return $response;
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "citacao", "insert into citacao (perfil, interacao) values ($1, $2)");
            if($preparing){
                $response = pg_execute($db, "citacao", array("$user", "$post"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function delCitacao($post, $pessoa){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update citacao set ativo = 0 where interacao = $post and perfil = $pessoa");
            if($response) return $response;
            else return false;
        }
        if($db_type == 'postgresql'){
            $response = pg_prepare($db, "delCitacao", "update citacao set ativo = false where interacao = $1 and perfil = $2");
            if($response){
                $response = pg_execute($db, "delCitacao", array("$post", "$pessoa"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function addAssuntoInteracao($post, $assunto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("insert into interacao_assunto (interacao, assunto) values ($post, $assunto)");
            if($response) return $response;
            else return false;
        }
        if($db_type == 'postgresql'){
            $preparing = pg_prepare($db, "interacao_assunto", "insert into interacao_assunto (interacao, assunto) values ($1, $2)");
            if($preparing){
                $response = pg_execute($db, "interacao_assunto", array("$post", "$assunto"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    function delAssuntoInteracao($post, $assunto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update interacao_assunto set ativo = 0 where interacao = $post and assunto = $assunto");
            if($response) return $response;
            else return false;
        }
        if($db_type == 'postgresql'){
            $response = pg_prepare($db, "delAssuntoInteracao", "update interacao_assunto set ativo = false where interacao = $1 and assunto = $2");
            if($response){
                $response = pg_execute($db, "delAssuntoInteracao", array("$post", "$assunto"));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    }
    /*-----------------------------------*/

    /*----ESTATISTICAS-------------*/
    function numerosGraficoMasc($faixamin, $faixamax, $pais, $mes){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $interacaoMasc = $db->query("select count(interacao.perfil) as total, pais.nome as pais from interacao join perfil on interacao.perfil= perfil.codigo join cidade on perfil.cidade = cidade.codigo join uf on cidade.uf= uf.codigo join pais on uf.pais = pais.codigo where perfil.genero = \"M\" and pais.nome=\"$pais\" and date(interacao.data) between date('now','-$mes months') and date('now') and date(perfil.datanasc) between date('now','-$faixamax years') and date('now', '-$faixamin years')");
            $results=[];    
                if($interacaoMasc){
                    while ($row = $interacaoMasc->fetchArray()) {
                        array_push($results, $row);
                    }
                    return $results; 
                }
        
        }   
    }
    function numerosGraficoFem($faixamin, $faixamax, $pais, $mes){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $interacaoFem = $db->query("select count(interacao.perfil) as total, pais.nome as pais from interacao join perfil on interacao.perfil= perfil.codigo join cidade on perfil.cidade = cidade.codigo join uf on cidade.uf= uf.codigo join pais on uf.pais = pais.codigo where perfil.genero = \"F\" and pais.nome=\"$pais\" and date(interacao.data) between date('now','-$mes months') and date('now') and date(perfil.datanasc) between date('now','-$faixamax years') and date('now', '-$faixamin years')");
          //  $response = $interacaoFem->fetchArray();
            $results=[];
            
                if($interacaoFem){
                    while ($row = $interacaoFem->fetchArray()) {
                        array_push($results, $row);
                    }
                    return $results; 
                }
        }   
    }

    //10)
    function countLikesbyCountry($pais,$dias,$hora,$likes){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->query("select count(perfil.codigo) from perfil
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
                    pais.codigo = $pais and
                    date(reacoes.data) between date('now', '- $dias', 'localtime') and date('now', 'localtime') and
                    date(reacoes.data) between date(interacao.data, 'localtime') and date(interacao.data, '+$hora hours', 'localtime')
                group by perfil.codigo
                having count(*) > $likes
                order by count(*) desc  
            );");
            if($response) {
                $response = $response->fetchArray();
                return $response;
            }
            else return false;
        }
        if($db_type == 'postgresql'){
            $response = pg_prepare($db, "", "");
            if($response){
                $response = pg_execute($db, "", array(""));
                if($response) return $response;
                else return false;
            }
            else return false;
        }
        else exit;
    };

    //11)
    function getFaixaEtaria($grupo,$dia){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
        $response= $db ->query("select case
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
        date(interacao.data, 'localtime') between date('now', '-$dia days', 'localtime') and date('now', 'localtime') and
        interacao.codigo in (
            select interacao.codigo from interacao
                join porto on interacao.porto = porto.codigo
            where porto.codigo = $grupo
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
            where porto.codigo = $grupo
                    ) and
                    date(interacao.data, 'localtime') between date('now', '-$dia days', 'localtime') and date('now', 'localtime')
                group by faixaEtaria
                order by qtdReacoes desc
            )
            limit 1
        ) order by qtdReacoes desc;");
         
    };
};

//12)

function getTop(){
     $db_connection=db_connection();
    $db=$db_connection['db'];
    $db_type=$db_connection['db_type'];
    if($db_type == 'sqlite'){
$response= $db ->query("select assunto.nome, strftime('%m', interacao.data) as mes from interacao 
join interacao_assunto on interacao.codigo = interacao_assunto.interacao 
join assunto on interacao_assunto.assunto = assunto.codigo
left join cidade on interacao.local = cidade.codigo --left join pois pode nao ter local
    left join uf on cidade.uf = uf.codigo
    left join pais on uf.pais = pais.codigo
where pais.codigo =$pais and  date(interacao.data, 'localtime') between date('now', '-$mes months', 'localtime') and date('now', 'localtime')
group by assunto.nome, mes
    order by count(assunto.nome) desc
    limit $top");
    
}
}
    /*-----------------------------------*/
  ?>