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

    $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $db->exec('UPDATE users SET online = 1 WHERE id = ' . $user['id']);
        header('Location: index.php');
        exit();
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<title>تسجيل الدخول - ERROR 404</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
  <div style="font-weight: bold; font-size: 2em; color: red; background-color: black; padding: 10px; text-align: center;">
    ERROR 404
  </div>
</header>
<h2>تسجيل الدخول</h2>
<?php if ($error): ?>
<p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post">
    <input type="text" name="username" placeholder="اسم المستخدم" required /><br/>
    <input type="password" name="password" placeholder="كلمة المرور" required /><br/>
    <button type="submit">دخول</button>
</form>
<p>لا تملك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>
<footer style="text-align:center; margin-top: 30px; color: #555; font-size: 0.9em;">
  حقوق النشر © gpz.l (يوزري انستقرام)
</footer>
</body>
</html>