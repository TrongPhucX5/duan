<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db  = 'ebook_website';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Thay đổi từ "utf8" sang "utf8mb4" để hỗ trợ đầy đủ các ký tự Unicode
mysqli_set_charset($conn, "utf8mb4");
$GLOBALS['conn'] = $conn;
?>