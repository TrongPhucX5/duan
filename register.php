<?php
// Kết nối CSDL
include 'includes/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username === '' || $password === '' || $confirm_password === '' || $email === '') {
        $message = 'Vui lòng điền đầy đủ thông tin.';
    } elseif ($password !== $confirm_password) {
        $message = 'Mật khẩu nhập lại không khớp.';
    } else {
        // Kiểm tra tài khoản đã tồn tại chưa
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $message = 'Tên đăng nhập đã tồn tại.';
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert = mysqli_prepare($conn, "INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "sss", $username, $hashed_password, $email);
            if (mysqli_stmt_execute($stmt_insert)) {
                $message = 'Đăng ký thành công! <a href="login.php">Đăng nhập ngay</a>';
            } else {
                $message = 'Có lỗi xảy ra, vui lòng thử lại.';
            }
            mysqli_stmt_close($stmt_insert);
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="register-container">
        <h2>Đăng ký</h2>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Tên đăng nhập" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mật khẩu" required>
            <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
            <button type="submit">Đăng ký</button>
        </form>
        <div class="login-link">
            Đã có tài khoản? <a href="login.php">Đăng nhập</a>