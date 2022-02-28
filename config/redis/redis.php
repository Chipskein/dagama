<?php
  use Predis\Client as Client;
  class Redis{
    private $redis=false;
    function __construct()
    {
      $url = parse_url($_ENV["REDIS_URL"]);
      $scheme=$url["scheme"];
      $host=$url["host"];
      $port=$url["port"];
      $pass=$url["pass"];
      $user=$url["user"];
      $config=[
        'scheme' =>$scheme,
        'host'   => $host,
        'port'   => $port,
        'username'   =>$user,
        'password'   =>$pass
      ];
      $this->redis=new Client($config);
    }
    
    public function setKey($key,$value)
    {
      if($this->redis)
      {
        $this->redis->set($key,$value);
      }
    }
    public function getKey($key)
    {
      if($this->redis)
      {
        $value=$this->redis->get($key);
        if($value)
        {
          return $value;
        }
        else{
          return false;
        }
      }
    }

  }
?>