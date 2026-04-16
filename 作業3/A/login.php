<?php

if(isset($_COOKIE['uName'])){
    echo $_COOKIE['nName']."歡迎回來!!!";
    echo "<a href='cookiedel.php'>刪除COOKIE</a>";
}
?>

<form action="loginchecks.php" method="POST">
ID:<input type="text" name="uID"><br/>
PWD:<input type="password" name="uPWD"><br/>

<input type="submit"><input type="reset">

<?php
echo time();
?>

<form>