<?php
session_start();

if (isset($_POST["nBuy"])) {
    $ID = $_POST["nBuy"];   // 商品編號 (N01, N02...)
    $Quantity = $_POST["nNum"]; // 購買數量

    setcookie("Cart[$ID]", $Quantity, time() + 3600, "/");

    header("Location: shoppingcart.php");
    exit();

} else {
    header("Location: catalog.php");
    exit();
}
?>