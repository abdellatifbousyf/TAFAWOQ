<?php
// includes/db.php
$host = '127.0.0.1';
$dbname = 'tafawoq';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("خطأ في الاتصال: " . $e->getMessage());
}

// إنشاء جميع الجداول
$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    icon VARCHAR(50),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category_id INT,
    year INT,
    type ENUM('exam','correction','lesson') DEFAULT 'exam',
    file_path VARCHAR(255),
    downloads INT DEFAULT 0,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS user_downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    exam_id INT,
    downloaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
);

INSERT IGNORE INTO categories (id, name, slug, icon, description) VALUES
(1, 'البكالوريا', 'bac', '🎓', 'امتحانات و دروس البكالوريا'),
(2, 'الإعدادي', 'college', '📚', 'امتحانات و دروس الإعدادي'),
(3, 'التكوين المهني', 'formation', '🔧', 'تكوينات مهنية و شهادات'),
(4, 'التوظيف', 'emploi', '', 'عروض الشغل و المباريات');
";

$pdo->exec($sql);
?>