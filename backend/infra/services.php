<?php
    if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")){
        include '../backend/mailer/mailer.php';
        include '../backend/gdrive/driver.php';
        include '../backend/infra/connection.php';
    }
    else{
        include '/app/backend/mailer/mailer.php';
        include '/app/backend/gdrive/driver.php';
        include '/app/backend/infra/connection.php';
    }
    /* BASICS */
    function getAssuntos(){
        $db_connection = db_connection();
        $db = $db_connection['db'];
        $db_type = $db_connection['db_type'];
        if($db){
            $results=[];
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"select * from assunto");
                if($response){
                    while ($row = mysqli_fetch_array($response)) {
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"select codigo, username, img from perfil where ativo = 1");
                if($response){
                    while ($row = mysqli_fetch_array($response)) {
                        array_push($results, $row);
                    }
                    mysqli_close($db);
                    return $results; 
                }
                else { 
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"select codigo, nome from porto where ativo = 1");
                if($response){
                    $results=[];
                    while ($row = mysqli_fetch_array($response)) {
                        array_push($results, $row);
                    }
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                if($where !== ''){
                    echo $where;
                    $response = mysqli_query($db,"select * from perfil where username like '%$where%' limit $limit offset $offset");
                }else {
                    $response = mysqli_query($db,"select * from perfil limit $limit offset $offset");
                }
                if($response){
                    while ($row = mysqli_fetch_array($response)) {
                        array_push($results, $row);
                    }
                    mysqli_close($db);
                    return $results; 
                }
                else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"select count(*)as total from perfil where ativo=1");
                if($response) {
                    $response = mysqli_fetch_array($response)['total'];
                    mysqli_close($db);
                    return $response;
                }
                else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $results = [];
                $result = mysqli_query($db,"select * from pais where ativo=1");
                while ($row = mysqli_fetch_array($result)) {
                    array_push($results,$row);
                }
                //mysqli_close($db);
                return $results;
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
        if($db_type == 'mysql'){
            $response = mysqli_query($db,"insert into assunto (nome) values ('$nome')");
            if($response) {
                $res = $db->insert_id;
                mysqli_close($db);
                return $res;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    function delAssunto($assunto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $response = mysqli_query($db,"update assunto set ativo = 0 where codigo = $assunto");
            if($response) {
                mysqli_close($db);
                return $response;
            } else {
                mysqli_close($db);
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
            if($db_type == 'mysql'){
                $verify=mysqli_query($db,"select codigo,senha as pass,ativo,img,username from perfil where perfil.email='$email'")->fetch_array();
                if(password_verify($password,$verify['pass'])) {
                    mysqli_close($db);
                    return $verify;
                }
                else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $verify=mysqli_query($db,"select codigo,senha as pass,ativo,img,username from perfil where perfil.email='$email'")->fetch_array();
                if("$password"=="$verify[pass]") {
                    mysqli_close($db);
                    return $verify;
                } else {
                    mysqli_close($db);
                    return false;
                }
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
            if($db_type == 'mysql'){
                $verify = mysqli_query($db,"insert into perfil (pais, email, senha, genero, username, datanasc,img) values ('".$pais."', '".$email."', '".$password."', '".$genero."', '".$username."', '".$bdate."', '".$link."'".")");
                if($verify) {
                    mysqli_close($db);
                    return $verify;
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"select email from perfil")->fetch_array();
                if($response) {
                    mysqli_close($db);
                    return $response;
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"select email from perfil where email='$email'");
                if($response) {
                    $response = mysqli_fetch_array($response);
                    mysqli_close($db);
                    return $response;
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"select codigo, email, ativo, img, username, pais from perfil where codigo=$id");
                if($response) return mysqli_fetch_array($response);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"select codigo, email, ativo, img, username, pais from perfil where codigo='$id' and ativo=1 ");
                if($response) return mysqli_fetch_array($response);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"select codigo from perfil where email='$email'");
                if($response) {
                    $response = mysqli_fetch_array($response)['codigo'];
                    mysqli_close($db);
                    return $response;
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"update perfil set ativo='1' where codigo=$id");
                if($response) {
                    $res=mysqli_query($db,"select email,senha as password from perfil where codigo='$id'");
                    if($res) {
                        $res = mysqli_fetch_array($res);
                        mysqli_close($db);
                        return $res;
                    } else {
                        mysqli_close($db);
                        return false;
                    }
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"update perfil set ativo='0' where codigo=$user");
                mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"update perfil set username='$name' where codigo=$id");
                mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"update perfil set email='$email' where codigo=$id");
                mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"update perfil set\\ senha='$senha' where codigo=$id");
                mysqli_close($db);
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
            if($db_type == 'mysql'){
                if($img){
                    $type=$img['type'];
                    $server_path=$img['tmp_name'];
                    $link="https://drive.google.com/uc?export=download&id=".insertFile("$type","$server_path","$FOLDERS[avatares]","avatar");
                    rmFile($oldimgid);
                    $response = mysqli_query($db,"update perfil set img='$link' where codigo=$id");
                    if($response){
                        $response2=mysqli_query($db,"select img from perfil where codigo=$id")->fetch_array()['img'];
                        if($response2) {
                            mysqli_close($db);
                            return $response2;
                        } else {
                            mysqli_close($db);
                            return false;
                        }
                    } else {
                        mysqli_close($db);
                        return false;
                    }
                } else {
                    mysqli_close($db);
                    return false;
                }                
            }
        }
        else exit;
    }
    /*----------------------------------------*/

    /* FEED */
    /*
    function getPosts($user, $offset,$limit,$order){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type == 'mysql'){
                $postsArray=[];
                $postsOriginais = mysqli_query($db,"
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

                if($postsOriginais){              
                    while($row = mysqli_fetch_array($postsOriginais)){
                        $postsArray[$row['codInteracao']] = $row;

                        $resCitacoesParent = mysqli_query($db,"select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                        from citacao join perfil on perfil.codigo = citacao.perfil 
                        where 
                            citacao.ativo = 1 and 
                            citacao.interacao = $row[codInteracao]");
                        $citacoes = [];
                        while ($row2 = mysqli_fetch_array($resCitacoesParent)) {
                            $citacoes[] = $row2;
                        }
                        $postsArray[$row['codInteracao']]['citacoes'] = $citacoes;
                        
                        $resAssuntosParent = mysqli_query($db,"
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
                        $temInteracoes = mysqli_query($db,"
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
                            while($row3 = mysqli_fetch_array($temInteracoes)){
                                $childInteracoes[$row3['codInteracao']] = $row3;
                                $resCitacoesChild = mysqli_query($db,"select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
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
                                $resAssuntosChild = mysqli_query($db,"
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

                                $temInnerInteracoes = mysqli_query($db,"
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
                                    while ($row6 = mysqli_fetch_array($temInnerInteracoes)) {
                                        $grandChildInteracoes[$row6['codInteracao']] = $row6;
                                        $resCitacoesGrandChild = mysqli_query($db,"
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
                                        
                                        $resAssuntosGrandChild = mysqli_query($db,"
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
                mysqli_close($db);
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
            if($db_type == 'mysql'){
                $postsArray=[];
                $postsOriginais = mysqli_query($db,"
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
                    left join cidade on interacao.local = cidade.codigo
                    left join uf on cidade.uf = uf.codigo
                    left join pais on uf.pais = pais.codigo
                    left join porto on interacao.porto = porto.codigo
                where interacao.ativo = 1 and interacao.postPai is null
                group by codPost
                ");
                if($postsOriginais){       
                    while($row = mysqli_fetch_array($postsOriginais)){
                        $postsArray[$row['codInteracao']] = $row;

                        $resCitacoesParent = mysqli_query($db,"select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                        from citacao join perfil on perfil.codigo = citacao.perfil 
                        where 
                            citacao.ativo = 1 and 
                            citacao.interacao = $row[codInteracao]");
                        $citacoes = [];
                        while ($row2 = mysqli_fetch_array($resCitacoesParent)) {
                            $citacoes[] = $row2;
                        }
                        $postsArray[$row['codInteracao']]['citacoes'] = $citacoes;
                        
                        $resAssuntosParent = mysqli_query($db,"
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
                        $temInteracoes = mysqli_query($db,"
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
                            while($row3 = mysqli_fetch_array($temInteracoes)){
                                $childInteracoes[$row3['codInteracao']] = $row3;
                                $resCitacoesChild = mysqli_query($db,"select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
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
                                $resAssuntosChild = mysqli_query($db,"
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

                                $temInnerInteracoes = mysqli_query($db,"
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
                                    while ($row6 = mysqli_fetch_array($temInnerInteracoes)) {
                                        $grandChildInteracoes[$row6['codInteracao']] = $row6;
                                        $resCitacoesGrandChild = mysqli_query($db,"
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
                                        
                                        $resAssuntosGrandChild = mysqli_query($db,"
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
                mysqli_close($db);
                return $postsArray;

            }
        }
        else exit;
    }
    */
    function getOriginalPost($post){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type == 'mysql'){
                $result = mysqli_query($db,"
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
                
                $results2 = mysqli_query($db,"
                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                        join perfil on perfil.codigo = citacao.perfil 
                    where 
                        citacao.ativo = 1 and citacao.interacao = $post");
                $citacoes = [];
                while ($row = mysqli_fetch_array($results2)) {
                    $citacoes[$row['codPerfil']] = $row;
                }
                
                $results3 = mysqli_query($db,"
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
                
                mysqli_close($db);
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
            if($db_type == 'mysql'){
                $result = mysqli_query($db,"
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
                
                $results2 = mysqli_query($db,"
                    select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil from citacao 
                        join perfil on perfil.codigo = citacao.perfil 
                    where 
                        citacao.ativo = 1 and citacao.interacao = $post");
                $citacoes = [];
                while ($row = mysqli_fetch_array($results2)) {
                    $citacoes[$row['codPerfil']] = $row;
                }
                
                $results3 = mysqli_query($db,"
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
                
                mysqli_close($db);
                return $response;
            }
        }
        else exit;
    }
    /* 
    */
    
    function OndasDoMomento($top,$cidade){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type == 'mysql'){
                $response=mysqli_query($db,"
                select 
                    assunto.nome as nome,
                    count(*) as total, 
                    pais.nome as nomeCidade
                from interacao 
                join pais on pais.codigo=interacao.pais
                join INTERACAO_ASSUNTO on interacao.codigo=INTERACAO_ASSUNTO.interacao
                join assunto on INTERACAO_ASSUNTO.assunto=assunto.codigo
                where 
                    interacao.local=$cidade
                group by assunto.codigo
                having count(*) in (
                    select 
                    distinct
                    count(*) as total_per_assunto
                    from interacao 
                    join pais on pais.codigo=pais.local
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
                    while($row = mysqli_fetch_array($response)){
                        array_push($results,$row);
                    }
                    mysqli_close($db);
                    return $results;
                } else {
                    mysqli_close($db);
                    return false;
                }
            }
        }
        else exit;
    }
    
    /*-----------------------------------------*/    

    /* FRIENDS */
    /*
    function suggestFriends($user, $limit, $offset) {
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
    function sendFriendRequest($user, $friend) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $hasRequest = mysqli_query($db,"select * from SOLICITACAO_AMIGO where (perfil = $user and amigo = $friend) or (amigo = $user and perfil = $friend)");
            $hasRequest = mysqli_fetch_array($hasRequest);
            if($hasRequest){
                if($hasRequest['ativo'] == 0) {
                    $friendRequest = mysqli_query($db,"update SOLICITACAO_AMIGO set ativo = 1 where (amigo = $user and perfil = $friend) or (perfil = $user and amigo = $friend)");    
                    if($friendRequest) {mysqli_close($db);return $friendRequest;}
                    else {mysqli_close($db);return false;}
                }
            } else {
                $friendRequest = mysqli_query($db,"insert into SOLICITACAO_AMIGO (perfil, amigo, dateEnvio) values ($user, $friend, CURRENT_TIMESTAMP)");
                if($friendRequest) {
                    mysqli_close($db);
                    return $friendRequest;
                } else {
                    mysqli_close($db);
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
        if($db_type == 'mysql'){
            $friendRequest = mysqli_query($db,"update SOLICITACAO_AMIGO set ativo = 0 where perfil = $user and amigo = $friend");
            if($friendRequest) {
                mysqli_close($db);
                return $friendRequest;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    function confirmFriendRequest($user, $friend) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $friendRequest = mysqli_query($db,"update SOLICITACAO_AMIGO set ativo = 0 where amigo = $user and perfil = $friend");
            $friendAdd = mysqli_query($db,"insert into amigo (amigo, perfil, dateAceito) values ($user, $friend, CURRENT_TIMESTAMP)");
            if($friendAdd) {
                mysqli_close($db);
                return $friendAdd;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    function declineFriendRequest($user, $friend) {
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $friendRequest = mysqli_query($db,"update SOLICITACAO_AMIGO set ativo = 0 where amigo = $user and perfil = $friend");
            if($friendRequest) {
                mysqli_close($db);
                return $friendRequest;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    /*
    function getRequestAndFriends($user, $isOwner){
        $db_connection=db_connection();
        $db=$db_connection['db'];
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
    function delFriend($user, $friend){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $delFriend = mysqli_query($db,"update amigo set ativo = 0 where (perfil = $user and amigo = $friend) or (amigo = $user and perfil = $friend)");
            if($delFriend) {
                mysqli_close($db);
                return $delFriend;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    /*
    function getFriends($user, $offset, $limit, $where){
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
    /* ---------------------------------------*/

    /* PORTO */
    function getAllPorto($user, $isOwner, $offset, $limit, $order){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type == 'mysql'){
                $results=[];
                if($limit){
                    $result=mysqli_query($db,"
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
                    $result=mysqli_query($db,"
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
                while ($row = mysqli_fetch_array($result)) {
                    array_push($results,$row);
                }
                mysqli_close($db);
                return $results;
            }
        }
        else exit;
    }
    function getAllPortos($offset, $limit=10, $order, $where){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        //BUG FIX
        $user=1;
        $isOwner=false;
        if($db){
            if($db_type=='mysql'){
                $results=[];
                if($where != ''){
                    $result=mysqli_query($db,"
                    select *
                    from porto
                    where 
                    nome like '%$where%' and
                        ativo = 1
                        ".($order ? "order by $order" : "")."
                    ".($limit > 0 ? " limit $limit offset $offset" : " "));
                }else {
                    $result=mysqli_query($db,"
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
                while ($row = mysqli_fetch_array($result)) {
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
            if($db_type == 'mysql'){
                if($offset && $limit){
                    $result=mysqli_query($db,"
                    select * from porto
                    where 
                        porto.ativo = 1 and
                        porto.perfil = $user
                    limit $limit offset $offset");
                }else{
                    $result=mysqli_query($db,"
                    select * from porto
                    where 
                        porto.ativo = 1 and
                        porto.perfil = $user");
                }
                if($result) {
                    $results = [];
                    while ($row = mysqli_fetch_array($result)) {
                        array_push($results, $row);
                    }
                    mysqli_close($db);
                    return $results;
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $result=mysqli_query($db,"
                select count(*) as total from porto
                where 
                    porto.ativo = 1 and
                    porto.perfil = $user");
                if($result) {
                    $result = mysqli_fetch_array($result)['total'];
                    mysqli_close($db);
                    return $result;
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $result=mysqli_query($db,"select count(*) as total from porto where ativo=1");
                if($result){
                    $result = mysqli_fetch_array($result)['total'];
                    mysqli_close($db);
                    return $result;
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"
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
                    $response = mysqli_fetch_array($response);
                    mysqli_close($db);
                    return $response;
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $verify = mysqli_query($db,"insert into porto (perfil,nome,descr,img) values ('".$perfil."', '".$nome."', '".$descr."', '".$link."'".")");
                $portoId = $db->insert_id;
                if($portoId) {
                    mysqli_close($db);
                    return $portoId;
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $response = mysqli_query($db,"update porto set ativo = 0 where codigo = $porto");
                if($response) {
                    mysqli_close($db);
                    return $response;
                } else {
                    mysqli_close($db);
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
        if($db_type == 'mysql'){
            $response = mysqli_query($db,"select case 
                when porto_participa.ativo = 0 then 'off' 
                when porto_participa.ativo = 1 then 'on' 
            end as participa from porto_participa 
            where perfil = $user and porto = $porto");
            $response = mysqli_fetch_array($response);
            if($response['participa'] == 'off') {
                $response2 = mysqli_query($db,"update porto_participa set ativo = 1, dataregis = CURRENT_TIMESTAMP where perfil = $user and porto = $porto");
                if($response2) {
                    mysqli_close($db);
                    return $response2;
                } else {
                    mysqli_close($db);
                    return $response2;
                }
            } else {
                $response2 = mysqli_query($db,"insert into porto_participa (perfil, porto) values ($user, $porto)");
                if($response2) {
                    mysqli_close($db);
                    return $response2;
                } else {
                    mysqli_close($db);
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
        if($db_type == 'mysql'){
            $response = mysqli_query($db,"update porto_participa set ativo = 0 where perfil = $user and porto = $porto");
            if($response) {
                mysqli_close($db);
                return $response;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    function editarPorto($porto,$newname,$newdescr,$newimg,$oldimgid){
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
            if($db_type == 'mysql'){
                $verify = mysqli_query($db,"update porto set nome='$newname',descr='$newdescr' ".($link ? ",img='$link'" : " ")." where codigo=$porto and ativo=1");
                if($verify) {
                    mysqli_close($db);
                    return $verify;
                } else {
                    mysqli_close($db);
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
            if($db_type == 'mysql'){
                $postsArray=[];
                $postsOriginais = mysqli_query($db,"
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
                from interacao
                    join porto on interacao.porto = porto.codigo
                    join perfil on interacao.perfil = perfil.codigo
                    left join (select postPai, count(*) as qtd from interacao where postPai is not null and interacao.ativo=1 group by postPai) as tmpQtd on interacao.codigo = tmpQtd.postPai
                    left join pais on interacao.local = pais.codigo
                where 
                    interacao.ativo = 1 and
                    porto.codigo = $porto
                order by interacao.data desc
                limit $limit offset $offset");
                if($postsOriginais){
                    while($row = mysqli_fetch_array($postsOriginais)){
                        $postsArray[$row['codInteracao']] = $row;

                        $resCitacoesParent = mysqli_query($db,"select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
                        from citacao join perfil on perfil.codigo = citacao.perfil 
                        where 
                            citacao.ativo = 1 and 
                            citacao.interacao = $row[codInteracao]");
                        $citacoes = [];
                        while ($row2 = mysqli_fetch_array($resCitacoesParent)) {
                            $citacoes[] = $row2;
                        }
                        $postsArray[$row['codInteracao']]['citacoes'] = $citacoes;
                        
                        $resAssuntosParent = mysqli_query($db,"
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
                        $temInteracoes = mysqli_query($db,"
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
                                $resCitacoesChild = mysqli_query($db,"select citacao.interacao as interacao, perfil.codigo as codPerfil, perfil.username as nomePerfil 
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
                                $resAssuntosChild = mysqli_query($db,"
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

                                $temInnerInteracoes = mysqli_query($db,"
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
                                        $resCitacoesGrandChild = mysqli_query($db,"
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
                                        
                                        $resAssuntosGrandChild = mysqli_query($db,"
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
                mysqli_close($db);
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
            if($db_type == 'mysql'){
                $results=[];
                $result = mysqli_query($db,"                
                select 
                porto.codigo as codPorto,
                porto_participa.dataregis as dataRegis,
                perfil.codigo as codPart,
                perfil.username as nomePart,
                perfil.img as imgPart,
                from porto
                    left join porto_participa on porto.codigo = porto_participa.porto
                    left join perfil on porto_participa.perfil = perfil.codigo
                where 
                    porto_participa.ativo = 1 and
                    porto.codigo = $porto
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
    function getAllPortoParticipants($porto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db){
            if($db_type == 'mysql'){
                $results=[];
                $result = mysqli_query($db,"                
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
                while ($row = mysqli_fetch_array($result)) {
                    array_push($results, $row);
                }
                mysqli_close($db);
                return $results;
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
        if($db_type == 'mysql'){
            // echo $perfil_posting;
            $response = mysqli_query($db,"insert into interacao (perfil, texto, perfil_posting, porto, isSharing, post, postPai, isReaction, emote, local) values 
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
                mysqli_close($db);
                return $res;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    function delInteracao($post){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $response = mysqli_query($db,"update interacao set ativo = 0 where codigo = $post");
            if($response) {
                $response2 = mysqli_query($db,"update citacao set ativo = 0 where interacao = $post");
                $response3 = mysqli_query($db,"update interacao_assunto set ativo = 0 where interacao = $post");
                mysqli_close($db);
                return $response;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    function ediInteracao($interacao, $texto, $isReaction, $emote, $local){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $txt = [($texto ? "texto = '$texto'" : "texto = ''"),
            ($isReaction ? "isReaction = ".$isReaction : "isReaction = null"),
            ($emote ? "emote = '".$emote."'" : 'emote = null'),
            ($local ? "local = ".$local : 'local = null'),
            "data = CURRENT_TIMESTAMP"];
            $txt = implode(', ',$txt);
            $response = mysqli_query($db,"update interacao set $txt where codigo = $interacao");
            if($response) {
                mysqli_close($db);
                return true;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    function addCitacaoInteracao($user, $post){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $check = mysqli_query($db,"select * from citacao where perfil = $user and interacao = $post");
            $check = mysqli_fetch_array($check);
            if($check){
                $response = mysqli_query($db,"update citacao set ativo = 1 where perfil = $user and interacao = $post");
                if($response) {
                    mysqli_close($db);
                    return $response;
                } else {
                    mysqli_close($db);return false;
                }
            } else {
                $response = mysqli_query($db,"insert into citacao (perfil, interacao) values ($user, $post)");
                if($response) {
                    mysqli_close($db);
                    return $response;
                } else {
                    mysqli_close($db);return false;
                }
            }
        }
        else exit;
    }
    function delCitacao($post, $pessoa){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $response = mysqli_query($db,"update citacao set ativo = 0 where interacao = $post and perfil = $pessoa");
            if($response) {
                mysqli_close($db);
                return $response;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    function addAssuntoInteracao($post, $assunto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $response = mysqli_query($db,"insert into interacao_assunto (interacao, assunto) values ($post, $assunto)");
            if($response) {
                mysqli_close($db);
                return $response;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    function delAssuntoInteracao($post, $assunto){
        $db_connection=db_connection();
        $db=$db_connection['db'];
        $db_type=$db_connection['db_type'];
        if($db_type == 'mysql'){
            $response = mysqli_query($db,"update interacao_assunto set ativo = 0 where interacao = $post and assunto = $assunto");
            if($response) {
                mysqli_close($db);
                return $response;
            } else {
                mysqli_close($db);
                return false;
            }
        }
        else exit;
    }
    /*-----------------------------------*/

?>