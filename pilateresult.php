<?php

$name=$_POST["nName"];
$email=$_POST["nEmail"];
$gender=$_POST["ngender"];
$class=$_POST["nclass"];
$exp=$_POST["nexp"]?? [];
$note= $_POST["nnote"];

// 輸出結果
echo "<h3>報名資料確認</h3>";
echo "姓名：" . ($name) . "<br/>";
echo "信箱：" . ($email) . "<br/>";

// 性別轉換
if($gender=="m"){
    echo "Your Gender is:男性<br/>";
}else{
     echo "Your Gender is:女性<br/>";
}

// 班別轉換
echo "參加班別：";
if ($class == "basic") echo "基礎核心班";
if ($class == "advanced") echo "進階體態班";
if ($class == "weekend") echo "週末放鬆營";
echo "<br/>";

// 經驗轉換 (處理陣列)
echo "過去經驗：";
foreach ($exp as $value){
    if ($value == "yoga") echo "瑜珈 ";
    if ($value == "gym") echo "健身 ";
    if ($value == "none") echo "無經驗 ";
}
echo "<br/>";

// 說明文字 (換行處理)
echo "備註事項：<br/>" . nl2br(htmlspecialchars($note));
?>