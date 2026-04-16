<body>
    <form action="savecart.php" method="post">
    選擇訂購商品:<select name="nBuy">
                <option value="N01">防曬外套 - $780</option>
                <option value="N02">防曬手套 - $350</option>
                <option value="N03">涼感T - $550</option>
                </select>
    <input type="number" name="nNum" value="1" min="1"style="width: 60px;">
    <input type="submit">
    <br><hr>
    </form>
</body>

<?php
    echo "<a href = 'catalog.php'>商品目錄</a>";
    echo "<a href = 'shoppingcart.php'>檢視購物車</a>";
?>
        