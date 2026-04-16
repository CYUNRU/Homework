<?php
session_start();

if(isset($_SESSION['login'])){
   if($_SESSION['login']=='teacher'){
   echo "<h1>Welcome! Teacher Login Success</h1></br>";
   echo "<a href='logout.php'>Logout</a>";
   }else{
   echo "<h1>非法進入你會看不到東西! 2秒後回到登入頁面</h1>";
   header("Refresh:3;url=login.php");
   }
}else{
    echo "<h1>非法進入你會看不到東西!2秒後回到登入頁面</h1>";
    header("Refresh:3;url=login.php");
}
?>
