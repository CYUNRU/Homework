<?php

$fID="aaa";
$fPWD="12345678";
if(isset($_POST["uID"]) && isset($_POST["uPWD"])){

   $uID=$_POST["uID"];  //檢查有沒有值
   $uPWD=$_POST["uPWD"];
   if($fID==$uID && $fPWD==$uPWD){
       header("Location:pilate.php");

    }else{
       echo "失敗";
       header("Refresh:2;url=login.php");
    }
}


?>