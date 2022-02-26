<?php
    include '/app/backend/mailer/mailer.php';
    include '/app/backend/gdrive/driver.php';
    
    $dotenv_dir='/app/backend/';

    $dotenv = Dotenv\Dotenv::createImmutable($dotenv_dir, '.env');
    $dotenv->load();

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
                    $db->close();
                    return $results; 
                }
                else { 
                    $db->close();
                    return false;
                }
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
                    $db->close();
                    return $results;
                }                
                else {
                    return false;
                }
            }
        }
        else exit;
    }
    function getAllUserInfo($offset,$limit,$where=''){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            $results=[];
            if($db_type == 'sqlite'){
                if($where !== ''){
                    echo $where;
                    $response = $db->query("select * from perfil where username like '%$where%' limit $limit offset $offset");
                }else {
                    $response = $db->query("select * from perfil limit $limit offset $offset");
                }
                if($response){
                    while ($row = $response->fetchArray()) {
                        array_push($results, $row);
                    }
                    $db->close();
                    return $results; 
                }
                else {
                    $db->close();
                    return false;
                }
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
                if($response) {
                    $response = $response->fetchArray()['total'];
                    $db->close();
                    return $response;
                }
                else {
                    $db->close();
                    return false;
                }
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
                $db->close();
                return $results;
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
            if($response) {
                $res = $db->lastInsertRowID();
                $db->close();
                return $res;
            } else {
                $db->close();
                return false;
            }
        }
        else exit;
    }
    function delPais($pais){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $cidades = $db->query("select cidade.codigo from cidade
                    join uf on uf.codigo = cidade.uf
                where uf.pais = $pais");
            $results = [];
            if($cidades){
                while ($row = $cidades->fetchArray()) {
                    array_push($results, $row['codigo']);
                }
            }
            $response = $db->exec("update pais set ativo = 0 where codigo = $pais");
            $response2 = $db->exec("update uf set ativo = 0 where pais = $pais");
            if(count($results) > 0) {
                $results = implode($results, ', ');
                $response3 = $db->exec("update cidade set ativo = 0 where codigo in ($results)");
            }
            if($response) {
                $db->close();
                return $response;
            }
            else {
                $db->close();
                return false;
            }
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
                $db->close();
                return $results;
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
            if($response) {
                $res = $db->lastInsertRowID();
                $db->close();
                return $res;
            }
            else {
                $db->close();
                return false;
            }
        }
        else exit;
    }
    function delEstado($estado){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update uf set ativo = 0 where codigo = $estado");
            $response2 = $db->exec("update cidade set ativo = 0 where uf = $estado");
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
                $db->close();
                return $results;
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
            if($response) {
                $res = $db->lastInsertRowID();
                $db->close();
                return $res;
            } else {
                $db->close();
                return false;
            }
        }
        else exit;
    }
    function delCidade($cidade){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update cidade set ativo = 0 where codigo = $cidade");
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
                    $db->close();
                    return $results; 
                }
                else {
                    $db->close();
                    return false;
                }
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
            if($response) {
                $res = $db->lastInsertRowID();
                $db->close();
                return $res;
            } else {
                $db->close();
                return false;
            }
        }
        else exit;
    }
    function delAssunto($assunto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update assunto set ativo = 0 where codigo = $assunto");
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
    /*---------------------------------------------------------*/

    /* LOGIN/REGISTER */
    function Login($email,$password){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $verify=$db->query("select codigo,senha as pass,ativo,img,username from perfil where perfil.email='$email'")->fetchArray();
                if(password_verify($password,$verify['pass'])) {
                    $db->close();
                    return $verify;
                }
                else {
                    $db->close();
                    return false;
                }
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
                if("$password"=="$verify[pass]") {
                    $db->close();
                    return $verify;
                } else {
                    $db->close();
                    return false;
                }
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
                if($verify) {
                    $db->close();
                    return $verify;
                } else {
                    $db->close();
                    return false;
                }
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
    };
    function emailExists($email){ //substitui getEmails na validação
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select email from perfil where email='$email'");
                if($response) {
                    $response = $response->fetchArray();
                    $db->close();
                    return $response;
                } else {
                    $db->close();
                    return false;
                }
            }
        }
        else exit;
    };
    /*-----------------------------------------*/

    /* USER */
    function getUserInfoRegister($id){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            if($db_type == 'sqlite'){
                $response = $db->query("select codigo, email, ativo, img, username, cidade from perfil where codigo=$id");
                if($response) return $response->fetchArray();
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
                $response = $db->query("select codigo, email, ativo, img, username, cidade from perfil where codigo='$id' and ativo=1 ");
                if($response) return $response->fetchArray();
                else return false;
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
                if($response) {
                    $response = $response->fetchArray()['codigo'];
                    $db->close();
                    return $response;
                } else {
                    $db->close();
                    return false;
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
                    if($res) {
                        $res = $res->fetchArray();
                        $db->close();
                        return $res;
                    } else {
                        $db->close();
                        return false;
                    }
                } else {
                    $db->close();
                    return false;
                }
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
                $db->close();
                return true;
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
                $db->close();
                return true;
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
                $db->close();
                return true;
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
                $db->close();
                return true;
            }
        }
    }
    function updateImg($id,$img,$oldimgid){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
        if($db){
            if($db_type == 'sqlite'){
                if($img){
                    $type=$img['type'];
                    $server_path=$img['tmp_name'];
                    $link="https://drive.google.com/uc?export=download&id=".insertFile("$type","$server_path","$FOLDERS[avatares]","avatar");
                    rmFile($oldimgid);
                    $response = $db->exec("update perfil set img='$link' where codigo=$id");
                    if($response){
                        $response2=$db->query("select img from perfil where codigo=$id")->fetchArray()['img'];
                        if($response2) {
                            $db->close();
                            return $response2;
                        } else {
                            $db->close();
                            return false;
                        }
                    } else {
                        $db->close();
                        return false;
                    }
                } else {
                    $db->close();
                    return false;
                }                
            }
        }
        else exit;
    }
    /*----------------------------------------*/

    /* FEED */
    function getPosts($user, $offset,$limit,$order){
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
                    left join (select postPai, count(*) as qtd from interacao where postPai is not null and interacao.ativo=1 group by postPai) as tmpQtd on interacao.codigo = tmpQtd.postPai
                    left join cidade on interacao.local = cidade.codigo
                    left join uf on cidade.uf = uf.codigo
                    left join pais on uf.pais = pais.codigo
                    left join porto on interacao.porto = porto.codigo
                where interacao.ativo = 1 and interacao.postPai is null
                group by codPost
                order by $order
                limit $limit 
                offset $offset
                ");


                while($row = $postsOriginais->fetchArray()){
                    $postsArray[$row['codInteracao']] = $row;

                    $resCitacoesParent = $db->query("select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                    from citacao join perfil on perfil.codigo = citacao.perfil 
                    where 
                        citacao.ativo = 1 and 
                        citacao.interacao = $row[codInteracao]");
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
                        interacao_assunto.ativo = 1 and
                        interacao.codigo = $row[codInteracao]");
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
                        case 
                            when tmpQtd.qtd is null then 0
                            else tmpQtd.qtd
                        end as qtdInteracao,
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
                        left join (select post, count(*) as qtd from interacao where interacao.postPai is not null and interacao.post is not null and interacao.ativo = 1 group by interacao.post) as tmpQtd on interacao.codigo = tmpQtd.post
                    where
                        interacao.ativo = 1 and 
                        interacao.isSharing is null 
                        and interacao.postPai = $row[codInteracao] 
                        and interacao.post = $row[codInteracao]");
                    $childInteracoes = [];
                    if($temInteracoes){
                        while($row3 = $temInteracoes->fetchArray()){
                            $childInteracoes[$row3['codInteracao']] = $row3;
                            $resCitacoesChild = $db->query("select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                            from citacao 
                                join perfil on perfil.codigo = citacao.perfil 
                            where 
                                citacao.ativo = 1 and 
                                citacao.interacao = $row3[codInteracao]");
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
                                interacao_assunto.ativo = 1 and
                                interacao.codigo = $row3[codInteracao]");
                            $assuntos = [];
                            while ($row5 = $resAssuntosChild->fetchArray()) {
                                $assuntos[] = $row5;
                            }
                            $childInteracoes[$row3['codInteracao']]['assuntos'] = $assuntos;

                            $temInnerInteracoes = $db->query("
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
                                left join (select post, count(*) as qtd from interacao where interacao.postPai is not null and interacao.post is not null and interacao.ativo = 1 group by interacao.post) as tmpQtd on interacao.codigo = tmpQtd.post
                            where 
                                interacao.ativo = 1 and 
                                interacao.isSharing is null and 
                                interacao.postPai = $row[codInteracao] and 
                                interacao.post = $row3[codInteracao]");
                                
                            $grandChildInteracoes = [];
                            $childInteracoes[$row3['codInteracao']]['respostas'] = [];
                            if($temInnerInteracoes){
                                while ($row6 = $temInnerInteracoes->fetchArray()) {
                                    $grandChildInteracoes[$row6['codInteracao']] = $row6;
                                    $resCitacoesGrandChild = $db->query("
                                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                                        join perfil on perfil.codigo = citacao.perfil 
                                    where 
                                        citacao.ativo = 1 and 
                                        citacao.interacao = $row6[codInteracao]");
                                    $citacoes = [];
                                    while ($row7 = $resCitacoesGrandChild->fetchArray()) {
                                        $citacoes[] = $row7;
                                    }
                                    $grandChildInteracoes[$row6['codInteracao']]['citacoes'] = $citacoes;
                                    
                                    $resAssuntosGrandChild = $db->query("
                                    select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                                        left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                                        left join assunto on interacao_assunto.assunto = assunto.codigo
                                    where
                                        interacao_assunto.ativo = 1 and
                                        interacao.codigo = ".$row6['codInteracao']);
                                    $assuntos = [];
                                    while ($row8 = $resAssuntosGrandChild->fetchArray()) {
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
                $db->close();
                return $postsArray;
            }
        }
        else exit;
    }
    function getAllPosts($user){
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
                    left join (select postPai, count(*) as qtd from interacao where postPai is not null and interacao.ativo=1 group by postPai) as tmpQtd on interacao.codigo = tmpQtd.postPai
                    left join cidade on interacao.local = cidade.codigo
                    left join uf on cidade.uf = uf.codigo
                    left join pais on uf.pais = pais.codigo
                    left join porto on interacao.porto = porto.codigo
                where interacao.ativo = 1 and interacao.postPai is null
                group by codPost
                ");

                while($row = $postsOriginais->fetchArray()){
                    $postsArray[$row['codInteracao']] = $row;

                    $resCitacoesParent = $db->query("select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                    from citacao join perfil on perfil.codigo = citacao.perfil 
                    where 
                        citacao.ativo = 1 and 
                        citacao.interacao = $row[codInteracao]");
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
                        interacao_assunto.ativo = 1 and
                        interacao.codigo = $row[codInteracao]");
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
                        case 
                            when tmpQtd.qtd is null then 0
                            else tmpQtd.qtd
                        end as qtdInteracao,
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
                        left join (select post, count(*) as qtd from interacao where interacao.postPai is not null and interacao.post is not null and interacao.ativo = 1 group by interacao.post) as tmpQtd on interacao.codigo = tmpQtd.post
                    where
                        interacao.ativo = 1 and 
                        interacao.isSharing is null 
                        and interacao.postPai = $row[codInteracao] 
                        and interacao.post = $row[codInteracao]");
                    $childInteracoes = [];
                    if($temInteracoes){
                        while($row3 = $temInteracoes->fetchArray()){
                            $childInteracoes[$row3['codInteracao']] = $row3;
                            $resCitacoesChild = $db->query("select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                            from citacao 
                                join perfil on perfil.codigo = citacao.perfil 
                            where 
                                citacao.ativo = 1 and 
                                citacao.interacao = $row3[codInteracao]");
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
                                interacao_assunto.ativo = 1 and
                                interacao.codigo = $row3[codInteracao]");
                            $assuntos = [];
                            while ($row5 = $resAssuntosChild->fetchArray()) {
                                $assuntos[] = $row5;
                            }
                            $childInteracoes[$row3['codInteracao']]['assuntos'] = $assuntos;

                            $temInnerInteracoes = $db->query("
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
                                left join (select post, count(*) as qtd from interacao where interacao.postPai is not null and interacao.post is not null and interacao.ativo = 1 group by interacao.post) as tmpQtd on interacao.codigo = tmpQtd.post
                            where 
                                interacao.ativo = 1 and 
                                interacao.isSharing is null and 
                                interacao.postPai = $row[codInteracao] and 
                                interacao.post = $row3[codInteracao]");
                                
                            $grandChildInteracoes = [];
                            $childInteracoes[$row3['codInteracao']]['respostas'] = [];
                            if($temInnerInteracoes){
                                while ($row6 = $temInnerInteracoes->fetchArray()) {
                                    $grandChildInteracoes[$row6['codInteracao']] = $row6;
                                    $resCitacoesGrandChild = $db->query("
                                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                                        join perfil on perfil.codigo = citacao.perfil 
                                    where 
                                        citacao.ativo = 1 and 
                                        citacao.interacao = $row6[codInteracao]");
                                    $citacoes = [];
                                    while ($row7 = $resCitacoesGrandChild->fetchArray()) {
                                        $citacoes[] = $row7;
                                    }
                                    $grandChildInteracoes[$row6['codInteracao']]['citacoes'] = $citacoes;
                                    
                                    $resAssuntosGrandChild = $db->query("
                                    select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                                        left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                                        left join assunto on interacao_assunto.assunto = assunto.codigo
                                    where
                                        interacao_assunto.ativo = 1 and
                                        interacao.codigo = ".$row6['codInteracao']);
                                    $assuntos = [];
                                    while ($row8 = $resAssuntosGrandChild->fetchArray()) {
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
                $db->close();
                return $postsArray;

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
                        cidade.codigo as codCidade,
                        uf.nome as nomeUF,
                        uf.codigo as codUF,
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
                        left join cidade on interacao.local = cidade.codigo
                        left join uf on cidade.uf = uf.codigo
                        left join pais on uf.pais = pais.codigo
                    where interacao.codigo = $post");
                
                $results2 = $db->query("
                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                        join perfil on perfil.codigo = citacao.perfil 
                    where 
                        citacao.ativo = 1 and citacao.interacao = $post");
                $citacoes = [];
                while ($row = $results2->fetchArray()) {
                    $citacoes[$row['codPerfil']] = $row;
                }
                
                $results3 = $db->query("
                select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                    join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                    join assunto on interacao_assunto.assunto = assunto.codigo
                where
                    interacao.ativo = 1 and interacao.codigo = $post");
                $assuntos = [];
                while ($row = $results3->fetchArray()) {
                    $assuntos[$row['codAssunto']] = $row;
                }

                
                $response = $result->fetchArray();
                $response['assuntos'] = $assuntos;
                $response['citacoes'] = $citacoes;
                
                $db->close();
                return $response;
            }
        }
        else exit;
    }
    function getOriginalPostOnPorto($post, $porto){
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
                        cidade.codigo as codCidade,
                        uf.nome as nomeUF,
                        uf.codigo as codUF,
                        pais.nome as nomePais,
                        pais.codigo as codPais,
                        porto.codigo as codPorto,
                        porto.nome as nomePorto,
                        perfil.codigo as codPerfil, 
                        perfil.username as nomePerfil,
                        perfil.img as iconPerfil,
                        interacao.postPai as postPai,
                        selo.codigo as codSelo,
                        selo.texto as nomeSelo
                    from interacao
                        join perfil on interacao.perfil = perfil.codigo
                        left join seloUser on perfil.codigo = seloUser.perfil and seloUser.porto = $porto
                        left join selo on seloUser.selo = selo.codigo
                        left join porto on interacao.porto = porto.codigo
                        left join cidade on interacao.local = cidade.codigo
                        left join uf on cidade.uf = uf.codigo
                        left join pais on uf.pais = pais.codigo
                    where interacao.codigo = $post");
                
                $results2 = $db->query("
                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                        join perfil on perfil.codigo = citacao.perfil 
                    where 
                        citacao.ativo = 1 and citacao.interacao = $post");
                $citacoes = [];
                while ($row = $results2->fetchArray()) {
                    $citacoes[$row['codPerfil']] = $row;
                }
                
                $results3 = $db->query("
                select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                    join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                    join assunto on interacao_assunto.assunto = assunto.codigo
                where
                    interacao.ativo = 1 and interacao.codigo = $post");
                $assuntos = [];
                while ($row = $results3->fetchArray()) {
                    $assuntos[$row['codAssunto']] = $row;
                }

                
                $response = $result->fetchArray();
                $response['assuntos'] = $assuntos;
                $response['citacoes'] = $citacoes;
                
                $db->close();
                return $response;
            }
        }
        else exit;
    }
    function OndasDoMomento($top,$cidade){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $response=$db->query("
                select 
                assunto.nome as nome, count(*) as total, cidade.nome as nomeCidade
                from interacao 
                join cidade on interacao.local=cidade.codigo
                join uf on cidade.uf=uf.codigo
                join pais on pais.codigo=uf.pais
                join INTERACAO_ASSUNTO on interacao.codigo=INTERACAO_ASSUNTO.interacao
                join assunto on INTERACAO_ASSUNTO.assunto=assunto.codigo
                where 
                    interacao.local= $cidade
                group by assunto.codigo
                having count(*) in (
                    select 
                    distinct
                    count(*) as total_per_assunto
                    from interacao 
                    join cidade on interacao.local=cidade.codigo
                    join uf on cidade.uf=uf.codigo
                    join pais on pais.codigo=uf.pais
                    join INTERACAO_ASSUNTO on interacao.codigo=INTERACAO_ASSUNTO.interacao
                    join assunto on INTERACAO_ASSUNTO.assunto=assunto.codigo
                    where 
                        interacao.local= $cidade
                    group by assunto.codigo
                    order by total_per_assunto desc
                    limit $top)
                order by total desc");
                if($response){
                    $results=[];
                    while($row = $response->fetchArray()){
                        array_push($results,$row);
                    }
                    $db->close();
                    return $results;
                } else {
                    $db->close();
                    return false;
                }
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
                $db->close();
                return $results;
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
                    if($friendRequest) {$db->close();return $friendRequest;}
                    else {$db->close();return false;}
                }
            } else {
                $friendRequest = $db->exec("insert into SOLICITACAO_AMIGO (perfil, amigo, dateEnvio) values ($user, $friend, CURRENT_TIMESTAMP)");
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
    function unsendFriendRequest($user, $friend) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $friendRequest = $db->exec("update SOLICITACAO_AMIGO set ativo = 0 where perfil = $user and amigo = $friend");
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
    function confirmFriendRequest($user, $friend) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $friendRequest = $db->exec("update SOLICITACAO_AMIGO set ativo = 0 where amigo = $user and perfil = $friend");
            $friendAdd = $db->exec("insert into amigo (amigo, perfil, dateAceito) values ($user, $friend, CURRENT_TIMESTAMP)");
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
    function declineFriendRequest($user, $friend) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $friendRequest = $db->exec("update SOLICITACAO_AMIGO set ativo = 0 where amigo = $user and perfil = $friend");
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
                $db->close();
                return $results;
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

    function getFriends($user, $offset, $limit, $where){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                if($where !== ''){
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
                    nameAmigo like '%$where%' and
                        perfil.codigo = $user and 
                        amigo.ativo = 1
                    order by amigo.dateAceito desc
                    limit $limit offset $offset");
                }else{
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

                }
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                $db->close();
                return $results;
            }
        }
        else exit;
    }
    /* ---------------------------------------*/

    /* PORTO */
    function getAllPorto($user, $isOwner, $offset, $limit, $order){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                if($limit){
                    $result=$db->query("
                    select 
                        porto.codigo as codigo, 
                        porto.nome as nome, 
                        porto.descr as descr, 
                        porto.img as img,
                        porto.dataRegis as data,
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
                        ".($order ? "order by $order" : "")."
                        ".($isOwner ? " and participa = true" : "")."
                    ".($limit > 0 ? " limit $limit offset $offset" : " "));
                } else{
                    $result=$db->query("
                    select 
                        porto.codigo as codigo, 
                        porto.nome as nome, 
                        porto.descr as descr, 
                        porto.img as img,
                        porto.dataRegis as data,
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
                        ".($order ? "order by $order" : "")."
                        ".($isOwner ? " and participa = true" : "")."
                    ");
                }
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                $db->close();
                return $results;
            }
        }
        else exit;
    }
    function getAllPortos($offset, $limit=10, $order, $where){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                $results=[];
                if($where != ''){
                    $result=$db->query("
                    select *
                    from porto
                    where 
                    nome like '%$where%' and
                        ativo = 1
                        ".($order ? "order by $order" : "")."
                    ".($limit > 0 ? " limit $limit offset $offset" : " "));
                }else {
                    $result=$db->query("
                    select 
                        porto.codigo as codigo, 
                        porto.nome as nome, 
                        porto.descr as descr, 
                        porto.img as img,
                        porto.dataRegis as data,
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
                        ".($order ? "order by $order" : "")."
                        ".($isOwner ? " and participa = true" : "")."
                    ".($limit > 0 ? " limit $limit offset $offset" : " "));
                }
                while ($row = $result->fetchArray()) {
                    array_push($results,$row);
                }
                return $results;
            }
        }
        else exit;
    }
    function getUserPorto($user, $offset, $limit){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type=='sqlite'){
                if($offset && $limit){
                    $result=$db->query("
                    select * from porto
                    where 
                        porto.ativo = 1 and
                        porto.perfil = $user
                    limit $limit offset $offset");
                }else{
                    $result=$db->query("
                    select * from porto
                    where 
                        porto.ativo = 1 and
                        porto.perfil = $user");
                }
                if($result) {
                    $results = [];
                    while ($row = $result->fetchArray()) {
                        array_push($results, $row);
                    }
                    $db->close();
                    return $results;
                } else {
                    $db->close();
                    return false;
                }
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
                if($result) {
                    $result = $result->fetchArray()['total'];
                    $db->close();
                    return $result;
                } else {
                    $db->close();
                    return false;
                }
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
                    $result = $result->fetchArray()['total'];
                    $db->close();
                    return $result;
                } else {
                    $db->close();
                    return false;
                }
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
                if($response) {
                    $response = $response->fetchArray();
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
                if($portoId) {
                    $db->close();
                    return $portoId;
                } else {
                    $db->close();
                    return false;
                }
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
                if($response2) {
                    $db->close();
                    return $response2;
                } else {
                    $db->close();
                    return $response2;
                }
            } else {
                $response2 = $db->exec("insert into porto_participa (perfil, porto) values ($user, $porto)");
                if($response2) {
                    $db->close();
                    return $response2;
                } else {
                    $db->close();
                    return $response2;
                }
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
                if($verify) {
                    $db->close();
                    return $verify;
                } else {
                    $db->close();
                    return false;
                }
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
                $postsArray=[];
                $postsOriginais = $db->query("
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
                    cidade.nome as nomeCidade,
                    uf.nome as nomeUF,
                    pais.nome as nomePais,
                    porto.codigo as codPorto,
                    porto.nome as nomePorto,
                    perfil.codigo as codPerfil, 
                    perfil.username as nomePerfil,
                    perfil.img as iconPerfil,
                    selo.codigo as codSelo,
                    selo.texto as nomeSelo
                from interacao
                    join porto on interacao.porto = porto.codigo
                    join perfil on interacao.perfil = perfil.codigo
                    left join (select postPai, count(*) as qtd from interacao where postPai is not null and interacao.ativo=1 group by postPai) as tmpQtd on interacao.codigo = tmpQtd.postPai
                    left join seloUser on perfil.codigo = seloUser.perfil and seloUser.porto = $porto
                    left join selo on seloUser.selo = selo.codigo
                    left join cidade on interacao.local = cidade.codigo
                    left join uf on cidade.uf = uf.codigo
                    left join pais on uf.pais = pais.codigo
                where 
                    interacao.ativo = 1 and
                    porto.codigo = $porto
                order by interacao.data desc
                limit $limit offset $offset");
                
                while($row = $postsOriginais->fetchArray()){
                    $postsArray[$row['codInteracao']] = $row;

                    $resCitacoesParent = $db->query("select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                    from citacao join perfil on perfil.codigo = citacao.perfil 
                    where 
                        citacao.ativo = 1 and 
                        citacao.interacao = $row[codInteracao]");
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
                        interacao_assunto.ativo = 1 and
                        interacao.codigo = $row[codInteracao]");
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
                        selo.codigo as codSelo,
                        selo.texto as nomeSelo,
                        case 
                            when tmpQtd.qtd is null then 0
                            else tmpQtd.qtd
                        end as qtdInteracao,
                        cidade.nome as nomeCidade,
                        uf.nome as nomeUF,
                        pais.nome as nomePais,
                        perfil.codigo as codPerfil, 
                        perfil.username as nomePerfil,
                        perfil.img as iconPerfil
                    from interacao
                        join perfil on interacao.perfil = perfil.codigo
                        left join seloUser on perfil.codigo = seloUser.perfil and seloUser.porto = $porto
                        left join selo on seloUser.selo = selo.codigo
                        left join cidade on cidade.codigo = interacao.local
                        left join uf on cidade.uf = uf.codigo
                        left join pais on uf.pais = pais.codigo
                        left join (select post, count(*) as qtd from interacao where interacao.postPai is not null and interacao.post is not null and interacao.ativo = 1 group by interacao.post) as tmpQtd on interacao.codigo = tmpQtd.post
                    where
                        interacao.ativo = 1 and 
                        interacao.isSharing is null 
                        and interacao.postPai = $row[codInteracao] 
                        and interacao.post = $row[codInteracao]");
                    $childInteracoes = [];
                    if($temInteracoes){
                        while($row3 = $temInteracoes->fetchArray()){
                            $childInteracoes[$row3['codInteracao']] = $row3;
                            $resCitacoesChild = $db->query("select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                            from citacao 
                                join perfil on perfil.codigo = citacao.perfil 
                            where 
                                citacao.ativo = 1 and 
                                citacao.interacao = $row3[codInteracao]");
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
                                interacao_assunto.ativo = 1 and
                                interacao.codigo = $row3[codInteracao]");
                            $assuntos = [];
                            while ($row5 = $resAssuntosChild->fetchArray()) {
                                $assuntos[] = $row5;
                            }
                            $childInteracoes[$row3['codInteracao']]['assuntos'] = $assuntos;

                            $temInnerInteracoes = $db->query("
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
                                cidade.nome as nomeCidade,
                                uf.nome as nomeUF,
                                pais.nome as nomePais,
                                perfil.codigo as codPerfil, 
                                perfil.username as nomePerfil,
                                perfil.img as iconPerfil,
                                selo.codigo as codSelo,
                                selo.texto as nomeSelo
                            from interacao
                                join perfil on interacao.perfil = perfil.codigo
                                left join seloUser on perfil.codigo = seloUser.perfil and seloUser.porto = $porto
                                left join selo on seloUser.selo = selo.codigo
                                left join cidade on cidade.codigo = interacao.local
                                left join uf on cidade.uf = uf.codigo
                                left join pais on uf.pais = pais.codigo
                                left join (select post, count(*) as qtd from interacao where interacao.postPai is not null and interacao.post is not null and interacao.ativo = 1 group by interacao.post) as tmpQtd on interacao.codigo = tmpQtd.post
                            where 
                                interacao.ativo = 1 and 
                                interacao.isSharing is null and 
                                interacao.postPai = $row[codInteracao] and 
                                interacao.post = $row3[codInteracao]");
                                
                            $grandChildInteracoes = [];
                            $childInteracoes[$row3['codInteracao']]['respostas'] = [];
                            if($temInnerInteracoes){
                                while ($row6 = $temInnerInteracoes->fetchArray()) {
                                    $grandChildInteracoes[$row6['codInteracao']] = $row6;
                                    $resCitacoesGrandChild = $db->query("
                                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                                        join perfil on perfil.codigo = citacao.perfil 
                                    where 
                                        citacao.ativo = 1 and 
                                        citacao.interacao = $row6[codInteracao]");
                                    $citacoes = [];
                                    while ($row7 = $resCitacoesGrandChild->fetchArray()) {
                                        $citacoes[] = $row7;
                                    }
                                    $grandChildInteracoes[$row6['codInteracao']]['citacoes'] = $citacoes;
                                    
                                    $resAssuntosGrandChild = $db->query("
                                    select interacao.codigo as interacao, assunto.codigo as codAssunto, assunto.nome as nomeAssunto from interacao
                                        left join interacao_assunto on interacao.codigo = interacao_assunto.interacao
                                        left join assunto on interacao_assunto.assunto = assunto.codigo
                                    where
                                        interacao_assunto.ativo = 1 and
                                        interacao.codigo = ".$row6['codInteracao']);
                                    $assuntos = [];
                                    while ($row8 = $resAssuntosGrandChild->fetchArray()) {
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
                $db->close();
                return $postsArray;
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
                perfil.img as imgPart,
                selo.codigo as codSelo,
                selo.texto as nomeSelo
                from porto
                    left join porto_participa on porto.codigo = porto_participa.porto
                    left join perfil on porto_participa.perfil = perfil.codigo
                    left join seloUser on perfil.codigo = seloUser.perfil and seloUser.porto = $porto
                    left join selo on seloUser.selo = selo.codigo
                where 
                    porto_participa.ativo = 1 and
                    porto.codigo = $porto
                limit $limit offset $offset");
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                $db->close();
                return $results;
            }
        }
        else exit;
    }
    function getAllPortoParticipants($porto){
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
                    porto.codigo = $porto");
                while ($row = $result->fetchArray()) {
                    array_push($results, $row);
                }
                $db->close();
                return $results;
            }
        }
        else exit;
    }
    function upsertSelo($perfil, $porto){
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
                } else {
                    $db->close();
                    return false;
                }
            }
        }
        else exit;
    }
    /*-----------------------------------*/
    
    /* INTERAÇÕES */
    function addInteracao($perfil, $texto, $perfil_posting, $porto, $isSharing, $post, $postPai, $isReaction, $emote, $local){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            // echo $perfil_posting;
            $response = $db->exec("insert into interacao (perfil, texto, perfil_posting, porto, isSharing, post, postPai, isReaction, emote, local) values 
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
                $res = $db->lastInsertRowID();
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
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update interacao set ativo = 0 where codigo = $post");
            if($response) {
                $response2 = $db->exec("update citacao set ativo = 0 where interacao = $post");
                $response3 = $db->exec("update interacao_assunto set ativo = 0 where interacao = $post");
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
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $txt = [($texto ? "texto = '$texto'" : "texto = ''"),
            ($isReaction ? "isReaction = ".$isReaction : "isReaction = null"),
            ($emote ? "emote = '".$emote."'" : 'emote = null'),
            ($local ? "local = ".$local : 'local = null'),
            "data = CURRENT_TIMESTAMP"];
            $txt = implode($txt, ', ');
            $response = $db->exec("update interacao set $txt where codigo = $interacao");
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
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $check = $db->query("select * from citacao where perfil = $user and interacao = $post");
            $check = $check->fetchArray();
            if($check){
                $response = $db->exec("update citacao set ativo = 1 where perfil = $user and interacao = $post");
                if($response) {
                    $db->close();
                    return $response;
                } else {
                    $db->close();return false;
                }
            } else {
                $response = $db->exec("insert into citacao (perfil, interacao) values ($user, $post)");
                if($response) {
                    $db->close();
                    return $response;
                } else {
                    $db->close();return false;
                }
            }
        }
        else exit;
    }
    function delCitacao($post, $pessoa){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update citacao set ativo = 0 where interacao = $post and perfil = $pessoa");
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
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("insert into interacao_assunto (interacao, assunto) values ($post, $assunto)");
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
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->exec("update interacao_assunto set ativo = 0 where interacao = $post and assunto = $assunto");
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
                $db->close();
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
            $results=[];
            if($interacaoFem){
                while ($row = $interacaoFem->fetchArray()) {
                    array_push($results, $row);
                }
                $db->close();
                return $results;
            }
        }   
    }
    //17
    function getInterationDatabyGender($pais,$meses){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response=$db->query("
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
                                date(interacao.data) between date('now','-$meses month') and  date('now')
                                and pais.codigo=$pais
                        group by perfil.genero,faixaEtaria
                        order by faixaEtaria,perfil.genero desc
                    ");   
            $results=[];
            if($response){
                while ($row = $response->fetchArray()){
                    array_push($results,$row);
                }
                $db->close();
                return($results);
            }
            else {
                $db->close();
                return false;
            }
        } 
        else exit;
    }
    //10)
    function countLikesbyCountry($pais,$dias,$hora,$likes){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response = $db->query("
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
                        pais.codigo=$pais
                        and interacao.isReaction is null
                        and interacao.data between datetime('now','-$dias days') and datetime('now') 
                ) as posts
                join interacao on posts.postagem=interacao.post
                where 
                    interacao.isReaction is not null
                    and interacao.data between datetime(posts.postagem_timestamp) and datetime(posts.postagem_timestamp,'+$hora hours')
                group by posts.postagem
                having count(*)>$likes
            )");
            if($response) {
                $response = $response->fetchArray()['qt'];
                $db->close();
                return $response;
            }
            else {
                $db->close();
                return false;
            }
        }
        if($db_type == 'postgresql'){
            $response = pg_prepare($db, "", "");
            if($response){
                $response = pg_execute($db, "", array(""));
                if($response) {
                    $db->close();
                    return $response;
                } else {
                    $db->close();
                    return false;
                }
            } else {
                $db->close();
                return false;
            }
        }
        else exit;
    };
    //11)
    function getFaixaEtaria($grupo,$dia){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response= $db ->query("
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
                    ) order by qtdReacoes desc"
            );
            if($response) {
                $response = $response->fetchArray();
                $db->close();
                return $response;
            } else {
                $db->close();
                return false;
            }
        };
    };
    //12)
    function getTop($pais,$top,$mes){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response= $db ->query("
                select 
                    pais.nome as pais,
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
                    count(assunto.nome) as qt,
                    DENSE_RANK () OVER ( 
                        ORDER BY count(assunto.nome) desc 
                    ) as rank
                    from interacao 
                        join interacao_assunto on interacao.codigo = interacao_assunto.interacao 
                        join assunto on interacao_assunto.assunto = assunto.codigo
                        join cidade on interacao.local = cidade.codigo
                        join uf on cidade.uf = uf.codigo
                        join pais on uf.pais = pais.codigo
                        where 
                        pais.codigo = $pais 
                        and  date(interacao.data, 'localtime') between date('now', '-$mes months', 'localtime') and date('now', 'localtime')
                    group by strftime('%m',interacao.data),assunto.nome
                    having count(*) in (
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
                            pais.codigo = $pais 
                            and  date(interacao.data, 'localtime') between date('now', '-$mes months', 'localtime') and date('now', 'localtime')
                        group by strftime('%m',interacao.data),assunto.nome
                        order by count(assunto.nome) desc
                        limit $top
                    )
                    order by count(assunto.nome) desc
                    "
            );
            $results=[];
            if($response){
                while($row = $response->fetchArray()){
                    array_push($results,$row);
                }
                {
                    $db->close();
                    return $results;
                }
            }
            else {
                $db->close();
                return false;
            }
        }
        else exit;
    }
    //13
    function getAssuntMoreHyped($pais,$top,$mes){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            $response=$db->query("
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
                        pais.codigo = $pais 
                        and  date(interacao.data, 'localtime') between date('now', '-$mes months', 'localtime') and date('now', 'localtime')
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
                            pais.codigo = $pais 
                            and  date(interacao.data, 'localtime') between date('now', '-$mes months', 'localtime') and date('now', 'localtime')
                        group by strftime('%m',interacao.data),assunto.nome
                        order by count(assunto.nome) desc
                        limit $top
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
                            pais.codigo = $pais 
                            and  date(interacao.data, 'localtime') between date('now', '-$mes months', 'localtime') and date('now', 'localtime')
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
                                pais.codigo = $pais 
                                and  date(interacao.data, 'localtime') between date('now', '-$mes months', 'localtime') and date('now', 'localtime')
                            group by strftime('%m',interacao.data),assunto.nome
                            order by count(assunto.nome) desc
                            limit $top
                        )
                        order by count(assunto.nome) desc
                    ) as result
                    group by result.assunto
                    limit 1
                )
            ");
            $results=[];
            if($response){
                while($row = $response->fetchArray()){
                    array_push($results,$row);
                }
                {
                    $db->close();
                    return $results;
                }
            }
            else {
                $db->close();
                return false;
            }
        }
        else exit;
    }
    //15
    function deactivateAllDeadUsersByCountry($pais,$limityear){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'sqlite'){
            //desativar usuario
            $exec1=$db->exec("
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
                            interacao.data between datetime('now','-$limityear years') and datetime('now')
                            and pais.codigo=$pais
                    )
                    and perfil.dataregis between datetime('now','-2 days') and datetime('now')
            ");
            //desativar interacoes do usuario
            $exec2=$db->exec("update interacao set ativo=0 where interacao.perfil in (select codigo from perfil where ativo=0)");
            //desativar portos do usuario
            $exec3=$db->exec("update porto set ativo=0 where porto.perfil in (select codigo from perfil where ativo=0)");
            //desativa amizades com esse usuario
            $exec4=$db->exec("update amigo set ativo=0 where amigo.perfil in (select codigo from perfil where ativo=0) or amigo.amigo in (select codigo from perfil where ativo=0)");
            //desativa citacoes com esse usuario
            $exec5=$db->exec("update citacao set ativo=0 where citacao.perfil in (select codigo from perfil where ativo=0)");
            //desativa porto_participa desse usuario
            $exec6=$db->exec("update porto_participa set ativo=0 where porto_participa.perfil in (select codigo from perfil where ativo=0)");
            //desativa solicitaçoes de amizade desse usuario
            $exec7=$db->exec("update solicitacao_amigo set ativo=0 where solicitacao_amigo.perfil in (select codigo from perfil where ativo=0) or solicitacao_amigo.amigo in (select codigo from perfil where ativo=0)");

            if($exec1&&$exec2&&$exec3&&$exec4&&$exec5&&$exec6&&$exec7) {
                $db->close();
                return true;
            }
            else {
                $db->close();
                return false;
            }
        }
        else exit;
    }

    /*-----------------------------------*/

   
?>