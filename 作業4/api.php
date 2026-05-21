<?php
header('Content-Type: application/json');

// 資料庫連線設定
$host = 'localhost';
$db   = 'mail_system';
$user = 'root'; // 請替換成你的資料庫帳號
$pass = '';     // 請替換成你的資料庫密碼
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
     $pdo = new PDO($dsn, $user, $pass, [
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
     ]);
} catch (\PDOException $e) {
     echo json_encode(['error' => '資料庫連線失敗: ' . $e->getMessage()]);
     exit;
}

$action = $_GET['action'] ?? '';

// 行動 1: 獲取要寄送的 Email 列表
if ($action === 'get_targets') {
    $mode = $_POST['mode'] ?? 'all';
    $limit = intval($_POST['limit'] ?? 0);

    if ($mode === 'random' && $limit > 0) {
        // 隨機抽選指定筆數
        $stmt = $pdo->prepare("SELECT email FROM subscribers ORDER BY RAND() LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    } else {
        // 全部發送
        $stmt = $pdo->prepare("SELECT email FROM mail ORDER BY no ASC");
    }
    
    $stmt->execute();
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode(['success' => true, 'emails' => $emails]);
    exit;
}

// 行動 2: 執行單筆寄信
if ($action === 'send_mail') {
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $content = $_POST['content'] ?? '';

    if (empty($email) || empty($subject) || empty($content)) {
        echo json_encode(['success' => false, 'message' => '欄位不完整']);
        exit;
    }

    // 郵件標頭設定
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: 電子報系統 <noreply@yourdomain.com>" . "\r\n";

    // 執行寄信
    if (@mail($email, $subject, $content, $headers)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'PHP mail() 執行失敗']);
    }
    exit;
}

// 收集表單傳來的 Email 並寫入資料庫
if ($action === 'add_email') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        echo json_encode(['success' => false, 'message' => '不合法的 Email 格式']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO mail (email) VALUES (?)");
        $stmt->execute([$email]);
        echo json_encode(['success' => true, 'message' => 'Email 新增成功！']);
    } catch (\PDOException $e) {
        if ($e->getCode() == 23000) { // 錯誤碼 23000 代表 Unique 鍵重複
            echo json_encode(['success' => false, 'message' => '此 Email 已存在於資料庫中']);
        } else {
            echo json_encode(['success' => false, 'message' => '資料庫錯誤: ' . $e->getMessage()]);
        }
    }
    exit;
}