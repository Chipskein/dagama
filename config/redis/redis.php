<?php
   use Predis\Client as Redis;
   $redis = new Redis([
    'host'   => '127.0.0.1',
    'port'   => 6379,
  ]);
?>