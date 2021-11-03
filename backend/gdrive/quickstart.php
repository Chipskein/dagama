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
    $client->setAccessType('offline'); 
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


