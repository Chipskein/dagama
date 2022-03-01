<?php
     use Dagama\Database;
    class PortoController{
        public static function getGrupos(){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $results=[];
                $response = mysqli_query($con,"select codigo, nome from porto where ativo = 1");
                if($response){
                    $results=[];
                    while ($row = mysqli_fetch_array($response)) {
                        array_push($results, $row);
                    }
                    $db->close();
                    return $results;
                }                
                else {
                    return false;
                }
            }
            else exit;
        }
        public static function getAllPorto($user, $isOwner, $offset, $limit, $order){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $results=[];
                if($limit){
                    $result=mysqli_query($con,"
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
                    $result=mysqli_query($con,"
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
                $db->close();
                return $results;
            }
            else exit;
        }
        public static function getAllPortos($offset, $limit=10, $order, $where){
            $db=new Database();
            $con=$db->get_connection();
            //BUG FIX
            $user=1;
            $isOwner=false;
            if($con){
                $results=[];
                if($where != ''){
                    $result=mysqli_query($con,"
                    select *
                    from porto
                    where 
                    nome like '%$where%' and
                        ativo = 1
                        ".($order ? "order by $order" : "")."
                    ".($limit > 0 ? " limit $limit offset $offset" : " "));
                }else {
                    $result=mysqli_query($con,"
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
            else exit;
        }
        public static function getUserPorto($user, $offset, $limit){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                if($offset && $limit){
                    $result=mysqli_query($con,"
                    select * from porto
                    where 
                        porto.ativo = 1 and
                        porto.perfil = $user
                    limit $limit offset $offset");
                }
                else{
                    $result=mysqli_query($con,"
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
                    $db->close();
                    return $results;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        public static function getUserPortoQtd($user){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $result=mysqli_query($con,"
                select count(*) as total from porto
                where 
                    porto.ativo = 1 and
                    porto.perfil = $user");
                if($result) {
                    $result = mysqli_fetch_array($result)['total'];
                    $db->close();
                    return $result;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        public static function getTotalPorto(){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $result=mysqli_query($con,"select count(*) as total from porto where ativo=1");
                if($result){
                    $result = mysqli_fetch_array($result)['total'];
                    $db->close();
                    return $result;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        public static function getPortInfo($porto, $user){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"
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
                    $db->close();
                    return $response;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        public static function addPorto($perfil,$nome,$descr,$img){
            $db=new Database();
            $con=$db->get_connection();
            $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
            $link='https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif';
            if($img){
                $type=$img['type'];
                $server_path=$img['tmp_name'];
                $link="https://drive.google.com/uc?export=download&id=".insertFile("$type","$server_path","$FOLDERS[portos]","porto-avatar");
            }
            if($con){
                $verify = mysqli_query($con,"insert into porto (perfil,nome,descr,img) values ('".$perfil."', '".$nome."', '".$descr."', '".$link."'".")");
                $portoId = mysqli_insert_id($con);
                if($portoId) {
                    $db->close();
                    return $portoId;
                } else {
                    $db->close();
                    return false;
                }
                
            }
            else exit; 
        }
        public static function delPorto($porto){
            $db=new Database();
            $con=$db->get_connection();
            // $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
            // $link='https://upload.wikimedia.org/wikipedia/commons/4/4a/Pirate_icon.gif';
            // if($img){
            //     $type=$img['type'];
            //     $server_path=$img['tmp_name'];
            //     $link="https://drive.google.com/uc?export=download&id=".insertFile("$type","$server_path","$FOLDERS[portos]","porto-avatar");
            // }
            if($con){
                $response = mysqli_query($con,"update porto set ativo = 0 where codigo = $porto");
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
        public static function entrarPorto($user, $porto){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"select case 
                    when porto_participa.ativo = 0 then 'off' 
                    when porto_participa.ativo = 1 then 'on' 
                end as participa from porto_participa 
                where perfil = $user and porto = $porto");
                $response = mysqli_fetch_array($response);
                if($response['participa'] == 'off') {
                    $response2 = mysqli_query($con,"update porto_participa set ativo = 1, dataregis = CURRENT_TIMESTAMP where perfil = $user and porto = $porto");
                    if($response2) {
                        $db->close();
                        return $response2;
                    } else {
                        $db->close();
                        return $response2;
                    }
                } else {
                    $response2 = mysqli_query($con,"insert into porto_participa (perfil, porto) values ($user, $porto)");
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
        public static function sairPorto($user, $porto){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"update porto_participa set ativo = 0 where perfil = $user and porto = $porto");
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
        public static function editarPorto($porto,$newname,$newdescr,$newimg,$oldimgid){
            $db=new Database();
            $con=$db->get_connection();
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
            if($con){
                $verify = mysqli_query($con,"update porto set nome='$newname',descr='$newdescr' ".($link ? ",img='$link'" : " ")." where codigo=$porto and ativo=1");
                if($verify) {
                    $db->close();
                    return $verify;
                } else {
                    $db->close();
                    return false;
                }
            
            }
            else exit; 
        }
        public static function getPostsOnPorto($porto, $offset, $limit=10){
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
        public static function getPortoParticipants($porto, $offset, $limit=10){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $results=[];
                $result = mysqli_query($con,"                
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
                $db->close();
                return $results;
                
            }
            else exit;
        }
        public static function getAllPortoParticipants($porto){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $results=[];
                $result = mysqli_query($con,"                
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
                $db->close();
                return $results;
                
            }
            else exit;
        }
    }
?>