<?php
    use Dagama\Database;
    class AssuntoController{
        public static function getAssuntos(){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $results=[];
                $response = mysqli_query($con,"select * from assunto");
                if($response){
                    while ($row = mysqli_fetch_array($response)) {
                        array_push($results, $row);
                    }
                    return $results; 
                }
                else return false;
                
            }
            else exit;
        }
        public static function addAssunto($nome){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"insert into assunto (nome) values ('$nome')");
                if($response) {
                    $res = $con->insert_id;
                    $db->close();
                    return $res;
                } else {
                    $db->close();
                    return false;
                }
            }
            else exit;
        }
        public static function delAssunto($assunto){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $response = mysqli_query($con,"update assunto set ativo = 0 where codigo = $assunto");
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
        //BUG MARIADB
        /*function OndasDoMomento($top,$cidade){
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
                        join pais on pais.codigo=interacao.local
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
        */
    }
?>