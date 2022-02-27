<?php
    use Dagama\Database;
    class LocalController{
        function getPaises(){
            $db=new Database();
            $con=$db->get_connection();
            if($con){
                $results = [];
                $result = mysqli_query($con,"select * from pais where ativo=1");
                while ($row = mysqli_fetch_array($result)) {
                    array_push($results,$row);
                }
                $db->close();
                return $results;
            }
            else exit;
        }
    }
?>