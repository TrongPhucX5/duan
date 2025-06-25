<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'ebook_website';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
  die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
$GLOBALS['conn'] = $conn;
?>
