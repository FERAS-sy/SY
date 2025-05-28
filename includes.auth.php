<?php
session_start();
require_once 'db.php';

function check_login() {
    global $db;
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $stmt = $db->prepare('SELECT * FROM users WHERE id = :id');
    $stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
    $user = $stmt->execute()->fetchArray(SQLITE3_ASSOC);
    if (!$user) {
        session_destroy();
        header('Location: login.php');
        exit();
    }
    return $user;
}
?>