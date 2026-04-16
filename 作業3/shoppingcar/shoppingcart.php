<?php
    echo "<table border='1'>";
    echo "</th><th>名稱</th><th>價格</th><th>數量</th></tr>";
    
    $total = 0;

    echo "<h3>我的購物車：</h3>";
    if (isset($_COOKIE['Cart'])) {   
        foreach ($_COOKIE['Cart'] as $item => $quantity) {
            switch($item){
                case 'N01':
                    $name = '防曬外套';
                    $price = 780;
                    break;
                case 'N02':
                    $name = '防曬手套';
                    $price = 350;
                    break;
                case 'N03':
                    $name = '涼感T';
                    $price = 550;
                    break;
            }
            echo "<tr>";
            echo "<td><a href='delete.php?Id=" . $item ."'>刪除</a></td>";
            echo "<td>" . $name . "</td>";
            echo "<td>" . $price . "</td>";
            echo "<td>" . $quantity . "</td>";
            echo "</tr>";

            $total += ($price * $quantity);
        }
        echo "<tr><td colspan='4' align='right'>總金額 = NT$" . $total . "元</td></tr>";
        echo "</table>";

        echo "<a href = 'catalog.php'>商品目錄</a>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "<a href = 'shoppingcart.php'>檢視購物車</a>";
    } else {
        echo "購物車目前是空的喔！";
    }
?>