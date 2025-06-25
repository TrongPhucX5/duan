<?php
session_start();
include 'includes/db.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $message = 'Vui lòng nhập đầy đủ thông tin.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, $id, $user, $hashed_password);
            mysqli_stmt_fetch($stmt);
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user'] = ['id' => $id, 'name' => $user];
                header('Location: index.php');
                exit;
            } else {
                $message = 'Sai mật khẩu.';
            }
        } else {
            $message = 'Tài khoản không tồn tại.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Đăng nhập</h2>
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" name="username" placeholder="username" required>
            <label for="password">Mật khẩu:</label>
            <input type="password" name="password" placeholder="password" required>
            <button type="submit">Đăng nhập</button>
        </form>
        <div class="register-link">
            Chưa có tài khoản? <a href="register.php">Đăng ký</a>
        </div>
    </div>
</body>
</html>