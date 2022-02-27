<?php
   /* Load external routes file */
   use Pecee\SimpleRouter\SimpleRouter;
   require '../routes/routes.php';
   $dotenv_dir='/app/';
   if(preg_match("/localhost/","$_SERVER[HTTP_HOST]")) $dotenv_dir='../';
   if (file_exists($dotenv_dir.'.env')) {
       //.env exists connect with local database
       $dotenv = Dotenv\Dotenv::createImmutable($dotenv_dir, '.env');
       $dotenv->load();
   }
   SimpleRouter::setDefaultNamespace('\Demo\Controllers');
   SimpleRouter::start();
?>