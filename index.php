<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="imgs/icon.png" type="image/jpg">
    <title>Register</title>
</head>
<body>
  <main>    
    <form action="mar.php" method="POST">
      <label for="email">Email: <input type="email" required></label>
      <br>
      <label for="password">Password:<input type="password" required></label>
      <br>
      <button id='login' type="submit">login</button>
    </form>
  </main>
  <script>
      const url='https://dagama.herokuapp.com';
      //const url='localhost:8080';
      //<button id='register'>registrar</button>
      //const button=document.getElementById('register').onclick=()=>{
        //window.location.href=`${url}/register.php`;
      //};
  </script>
</body>
</html>