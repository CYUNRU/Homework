<?php
session_start();

$sID="student";
$sPWD="1234";

$tID="teacher";
$tPWD="12345";

$aID="admin";
$aPWD="123456";

$uID=$_POST["uID"];  //檢查有沒有值
$uPWD=$_POST["uPWD"];

$date=strtotime("+1 day",time());

   if($uID==$sID && $uPWD==$sPWD){
      $_SESSION['login']='student';
      setcookie("uName",$uID,$date);
      header("Refresh:0;url=student.php"); 
       
    }elseif($uID==$tID && $uPWD==$tPWD){
      $_SESSION['login']='teacher';
      setcookie("uName",$uID,$date);
      header("Refresh:0;url=teacher.php"); 
      

    }elseif($uID==$aID && $uPWD==$aPWD){
      $_SESSION['login']='admin';
      setcookie("uName",$uID,$date);
      header("Refresh:0;url=admin.php"); 
      
    }else{
      echo "失敗";
      header("Refresh:2;url=login.php");
    }



?>