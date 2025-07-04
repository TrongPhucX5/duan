<?php
session_start(); // Luôn đặt ở đầu file

include 'includes/db.php'; // Đảm bảo đường dẫn đúng

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
            mysqli_stmt_bind_result($stmt, $id, $user_name, $hashed_password); // Đổi $user thành $user_name để tránh trùng
            mysqli_stmt_fetch($stmt);
            if (password_verify($password, $hashed_password)) {
                // Đăng nhập thành công
                $_SESSION['user_id'] = $id; // Lưu ID người dùng
                $_SESSION['username'] = $user_name; // Lưu username
                $_SESSION['is_logged_in'] = true; // Đặt cờ báo hiệu đã đăng nhập thành công
                
                // Nếu là admin thì chuyển đến trang admin
                if ($user_name === 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: index.php');
                }
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; // Bao gồm header ?>

    <main class="form-page-main">
        <div class="form-container">
            <h2>Đăng nhập</h2>
            <?php if ($message): ?>
                <div class="message <?php echo ($message === 'Vui lòng nhập đầy đủ thông tin.' || $message === 'Sai mật khẩu.' || $message === 'Tài khoản không tồn tại.') ? 'error-message' : ''; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="post" class="auth-form">
                <div class="form-group">
                    <label for="username">Tên đăng nhập:</label>
                    <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập của bạn" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu:</label>
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu của bạn" required>
                </div>
                <button type="submit" class="submit-button">Đăng nhập</button>
            </form>
            <p class="form-footer">
                Chưa có tài khoản? <a href="register.php">Đăng ký</a><br>
                <a href="forgot_password.php">Quên mật khẩu?</a>
            </p>
        </div>
    </main>

    <?php include 'includes/footer.php'; // Nếu bạn có footer riêng ?>
</body>
</html>