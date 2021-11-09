<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
  
  $FOLDERS=array("root"=>"14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS","avatares"=>"1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M","portos"=>"1e5T21RxDQ-4Kqw8EDVUBICGPeGIRSNHx","users"=>"1j2ivb8gBxV_AINaQ7FHjbd1OI0otCpEO");
  function getClient(){
      $client = new Google\Client();
      $client->setApplicationName("dagama-gdrive");
      $client->setAuthConfig($_SERVER['DOCUMENT_ROOT'].'/backend/gdrive/credentials.json');
      $client->addScope('https://www.googleapis.com/auth/drive');
      $client->addScope('https://www.googleapis.com/auth/drive.file');
      $client->addScope('https://www.googleapis.com/auth/drive.readonly');
      $client->addScope('https://www.googleapis.com/auth/drive.metadata.readonly');
      $client->addScope('https://www.googleapis.com/auth/drive.metadata');
      $client->addScope('https://www.googleapis.com/auth/drive.photos.readonly');
      return $client;
  }
  function getAllfiles() {
      $service=new Google\Service\Drive(getClient());
      $optParams = array(
          'pageSize' => 10,
          'fields' => 'nextPageToken, files(id, name)'
        );
        $results = $service->files->listFiles($optParams);
        
        if (count($results->getFiles()) == 0) {
            print "No files found.\n";
        } else {
            print "Files:\n<br>";
            foreach ($results->getFiles() as $file) {
                printf("%s (%s)\n<br>", $file->getName(), $file->getId());
            }
        }
  }; 
  function insertFile($mimeType, $filename,$folderID,$newfilename) {
      $client=getClient();
      $service=new Google\Service\Drive($client);      
      $file = new Google\Service\Drive\DriveFile();
      $file->setMimeType($mimeType);
      //set to user id
      $file->setName($newfilename);
      $file->setParents(array($folderID));
      try {
        $data = file_get_contents($filename);
        $createdFile = $service->files->create($file, array(
          'data' => $data,
          'mimeType' => $mimeType,
        ));
    

        return $createdFile;
      } catch (Exception $e) {
        print "An error occurred: " . $e->getMessage();
      }
  }
  function createFolder($name,$FOLDER_ID){
      $client=getClient();
      $service = new \Google\Service\Drive($client);
      $file= new \Google\Service\Drive\DriveFile();     
      $file->setName($name);
      $file->setParents(array($FOLDER_ID));
      $file->setMimeType("application/vnd.google-apps.folder");
      $createFile= $service->files->create($file, array('fields' => 'id'));
      return ['file_name'=>$name,'file_id'=>$createFile->id];
  }
  function rmFile($file_id){
    $client=getClient();
    $service=new \Google\Service\Drive($client);
    try{
        $service->files->delete($file_id);
        return true;
    }
    catch(Exception $e){
      return $e->getMessage();
    }
  };
  /* 
  SEM PERMISSÂO;
    function getFile($file_id){
      $client=getClient();
      $service=new \Google\Service\Drive($client);
      try{
        $result=$service->files->get($file_id);
        echo "<br>";
        echo '<pre>' , var_dump($result) , '</pre>';
        return $result;
      }
      catch(Exception $e){
        return false;
      }
    };
  */
  //getAllfiles();

  /*
  //testando
  if(isset($_FILES["foto"])){
    $file=$_FILES["foto"];
    $server_path=$file["tmp_name"];
    $filename=$file["name"];
    $filetype=$file["type"];
    echo "PATH:$server_path<br>";
    echo "FILE:$filename<br>";
    echo "TYPE:$filetype<br>";
    insertFile("$filetype","$server_path",$FOLDERS['avatares'],"$filename");
  }
  */
?>

