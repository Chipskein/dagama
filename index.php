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
  <main class="container">
    <article>
    <label for="email">Email: <input type="email"></label>
    <label for="password">Password:<input type="password"></label>
    <br>
    <button id='login'>login</button>
    <br>
    <button id='register'>register</button>
    </article>           
  </main>
  <script>
      const url='https://dagama.herokuapp.com';
      //const url='localhost:8080';
      const button=document.getElementById('register').onclick=()=>{
        window.location.href=`${url}/register.php`;
      };
  </script>
</body>
</html>