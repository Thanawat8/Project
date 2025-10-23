<?php
$host = 'localhost';
$db   = 'db_northwind'; // ต้องมีฐานข้อมูลนี้
$user = 'root';
$pass = 'root';         // ต้องเป็นรหัสผ่านที่ถูกต้อง
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // หากเชื่อมต่อไม่ได้ จะแสดงข้อผิดพลาดนี้
     die("❌ Database Connection Failed: " . $e->getMessage()); 
}
?>