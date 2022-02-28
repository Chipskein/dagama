<?php

  function getClient(){
      $client = new Google\Client();
      $GOOGLE_OAUTH_CREDENTIALS_PATH="../google-credentials.json";
      $client->setApplicationName("dagama-gdrive");
      $client->setAuthConfig($GOOGLE_OAUTH_CREDENTIALS_PATH);
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
  }
  function insertFile($mimeType, $filename,$folderID,$newfilename) {
      $client=getClient();
      $service=new Google\Service\Drive($client);      
      $file = new Google\Service\Drive\DriveFile();
      $file->setMimeType($mimeType);
      $file->setName($newfilename);
      $file->setParents(array($folderID));
      try {
        $data = file_get_contents($filename);
        $createdFile = $service->files->create($file, array(
          'data' => $data,
          'mimeType' => $mimeType,
        ));
        return $createdFile->id;
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
  }

?>

