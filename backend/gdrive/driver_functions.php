<?php
require '../../vendor/autoload.php';
function getClient(){
    $client = new Google\Client();
    $client->setApplicationName("dagama-gdrive");
    $client->setAuthConfig('credentials.json');
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
          print "Files:\n";
          foreach ($results->getFiles() as $file) {
              printf("%s (%s)\n", $file->getName(), $file->getId());
          }
      }
};
//folders
$gdrive_root="14oQWzTorITdqsK7IiFwfTYs91Gh_NcjS";
$gdrive_avatar="1Z3A4iqIe1eMerkdTEkXnjApRPupaPq-M";
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
if(isset($_FILES["foto"])){
  $file=$_FILES["foto"];
  $server_path=$file["tmp_name"];
  $filename=$file["name"];
  $filetype=$file["type"];
  var_dump($file);
  insertFile("$filetype","$server_path",$gdrive_avatar,"$filename");
}


