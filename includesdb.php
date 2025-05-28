<?php
class DB extends SQLite3 {
    function __construct() {
        $this->open('chat.db');
    }
}
$db = new DB();

$db->exec('CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE,
    password TEXT,
    name TEXT,
    bio TEXT,
    profile_pic TEXT,
    online INTEGER DEFAULT 0
)');

$db->exec('CREATE TABLE IF NOT EXISTS messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    sender_id INTEGER,
    content TEXT,
    type TEXT DEFAULT "text",
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)');
?>