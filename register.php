<?php
require_once 'includes/db.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if ($password !== $password_confirm) {
        $error = 'كلمتا المرور غير متطابقتين';
    } else {
        $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        if ($result->fetchArray(SQLITE3_ASSOC)) {
            $error = 'اسم المستخدم موجود مسبقاً';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $stmt->bindValue(':password', $hashed, SQLITE3_TEXT);
            $stmt->execute();
            header('Location: login.php');
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<title>إنشاء حساب - ERROR 404</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
  <div style="font-weight: bold; font-size: 2em; color: red; background-color: black; padding: 10px; text-align: center;">
    ERROR 404
  </div>
</header>
<h2>إنشاء حساب جديد</h2>
<?php if ($error): ?>
<p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post">
    <input type="text" name="username" placeholder="اسم المستخدم" required /><br/>
    <input type="password" name="password" placeholder="كلمة المرور" required /><br/>
    <input type="password" name="password_confirm" placeholder="تأكيد كلمة المرور" required /><br/>
    <button type="submit">إنشاء حساب</button>
</form>
<p>لديك حساب؟ <a href="login.php">تسجيل الدخول</a></p>
<footer style="text-align:center; margin-top: 30px; color: #555; font-size: 0.9em;">
  حقوق النشر © gpz.l (يوزري انستقرام)
</footer>
</body>
</html>