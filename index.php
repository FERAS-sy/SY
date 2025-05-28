<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user = check_login();

if (isset($_POST['logout'])) {
    $db->exec('UPDATE users SET online = 0 WHERE id = ' . $user['id']);
    session_destroy();
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['content']) || isset($_FILES['file']))) {
    $content = trim($_POST['content'] ?? '');
    $type = 'text';

    if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'video/mp4', 'audio/mpeg', 'application/pdf', 'application/zip'];
        if (in_array($_FILES['file']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $new_name);
            $content = $new_name;
            $type = 'file';
        }
    }

    if ($content !== '') {
        $stmt = $db->prepare('INSERT INTO messages (sender_id, content, type) VALUES (:sender_id, :content, :type)');
        $stmt->bindValue(':sender_id', $user['id'], SQLITE3_INTEGER);
        $stmt->bindValue(':content', $content, SQLITE3_TEXT);
        $stmt->bindValue(':type', $type, SQLITE3_TEXT);
        $stmt->execute();
    }
}

$messages = $db->query('SELECT messages.*, users.username, users.profile_pic FROM messages JOIN users ON messages.sender_id = users.id ORDER BY messages.created_at DESC');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<title>شات ERROR 404</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
  <div style="font-weight: bold; font-size: 2em; color: red; background-color: black; padding: 10px; text-align: center;">
    ERROR 404
  </div>
  <nav style="text-align:center; margin-top: 10px;">
    <a href="profile.php">الملف الشخصي</a> |
    <form method="post" style="display:inline;">
      <button type="submit" name="logout" style="background:none;border:none;color:#007700;cursor:pointer;">تسجيل خروج</button>
    </form>
  </nav>
</header>
<form method="post" enctype="multipart/form-data">
    <textarea name="content" placeholder="اكتب رسالتك هنا..." rows="3"></textarea><br/>
    <input type="file" name="file" />
    <button type="submit">إرسال</button>
</form>
<div class="messages">
<?php while ($msg = $messages->fetchArray(SQLITE3_ASSOC)): ?>
    <div class="message">
        <img class="profile-pic" src="uploads/<?= htmlspecialchars($msg['profile_pic'] ?: 'default.png') ?>" alt="profile" />
        <span class="sender"><?= htmlspecialchars($msg['username']) ?></span>:
        <?php if ($msg['type'] === 'text'): ?>
            <?= htmlspecialchars($msg['content']) ?>
        <?php elseif ($msg['type'] === 'file'):
            $ext = pathinfo($msg['content'], PATHINFO_EXTENSION);
            $file_url = 'uploads/' . htmlspecialchars($msg['content']);
            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])): ?>
                <img src="<?= $file_url ?>" alt="img" style="max-width:200px;" />
            <?php elseif (strtolower($ext) === 'mp4'): ?>
                <video controls style="max-width:300px;">
                    <source src="<?= $file_url ?>" type="video/mp4" />
                    متصفحك لا يدعم تشغيل الفيديو.
                </video>
            <?php elseif (strtolower($ext) === 'mp3'): ?>
                <audio controls>
                    <source src="<?= $file_url ?>" type="audio/mpeg" />
                    متصفحك لا يدعم تشغيل الصوت.
                </audio>
            <?php else: ?>
                <a href="<?= $file_url ?>" target="_blank">تحميل الملف</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php endwhile; ?>
</div>
<footer style="text-align:center; margin-top: 30px; color: #555; font-size: 0.9em;">
  حقوق النشر © gpz.l (يوزري انستقرام)
</footer>
</body>
</html>