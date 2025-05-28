<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user = check_login();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = trim($_POST['bio'] ?? '');

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['profile_pic']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $new_name = 'profile_' . $user['id'] . '.' . $ext;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'uploads/' . $new_name);
            $stmt = $db->prepare('UPDATE users SET profile_pic = :pic WHERE id = :id');
            $stmt->bindValue(':pic', $new_name, SQLITE3_TEXT);
            $stmt->bindValue(':id', $user['id'], SQLITE3_INTEGER);
            $stmt->execute();
            $user['profile_pic'] = $new_name;
        } else {
            $error = 'نوع الصورة غير مدعوم.';
        }
    }

    $stmt = $db->prepare('UPDATE users SET bio = :bio WHERE id = :id');
    $stmt->bindValue(':bio', $bio, SQLITE3_TEXT);
    $stmt->bindValue(':id', $user['id'], SQLITE3_INTEGER);
    $stmt->execute();
    $user['bio'] = $bio;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<title>الملف الشخصي - ERROR 404</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
  <div style="font-weight: bold; font-size: 2em; color: red; background-color: black; padding: 10px; text-align: center;">
    ERROR 404
  </div>
  <nav style="text-align:center; margin-top: 10px;">
    <a href="index.php">الشات</a>
  </nav>
</header>
<h2>ملفي الشخصي</h2>
<?php if ($error): ?>
<p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <label>الصورة الشخصية:</label><br/>
    <img src="uploads/<?= htmlspecialchars($user['profile_pic'] ?: 'default.png') ?>" alt="profile" style="max-width:150px;" /><br/>
    <input type="file" name="profile_pic" accept="image/*" /><br/><br/>
    <label>نبذة (بايو):</label><br/>
    <textarea name="bio" rows="4"><?= htmlspecialchars($user['bio']) ?></textarea><br/><br/>
    <button type="submit">تحديث الملف</button>
</form>
<footer style="text-align:center; margin-top: 30px; color: #555; font-size: 0.9em;">
  حقوق النشر © gpz.l (يوزري انستقرام)
</footer>
</body>
</html>