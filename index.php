
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>我的PHP一頁式網站</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>歡迎來到我的網站</h1>
        <p>這是一個簡單的一頁式PHP網站</p>
    </header>

    <main>
        <section class="about">
            <h2>關於我們</h2>
            <p>這是一個使用PHP建立的一頁式網站範例。</p>
        </section>

        <section class="info">
            <h2>伺服器資訊</h2>
            <?php
            // PHP資訊顯示
            echo "<p>當前時間：" . date("Y-m-d H:i:s") . "</p>";
            echo "<p>PHP版本：" . phpversion() . "</p>";
            echo "<p>伺服器：" . $_SERVER['SERVER_SOFTWARE'] . "</p>";
            ?>
        </section>

        <section class="contact">
            <h2>聯絡我們</h2>
            <form method="POST" action="">
                <input type="text" name="name" placeholder="您的姓名" required>
                <input type="email" name="email" placeholder="您的Email" required>
                <textarea name="message" placeholder="留言內容" required></textarea>
                <button type="submit">送出</button>
            </form>

            <?php
            // 處理表單提交
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $name = htmlspecialchars($_POST['name']);
                $email = htmlspecialchars($_POST['email']);
                $message = htmlspecialchars($_POST['message']);
                
                echo "<div class='success-message'>";
                echo "<h3>感謝您的留言！</h3>";
                echo "<p>姓名：$name</p>";
                echo "<p>Email：$email</p>";
                echo "<p>留言：$message</p>";
                echo "</div>";
            }
            ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 我的PHP網站. 版權所有.</p>
    </footer>
</body>
</html>
