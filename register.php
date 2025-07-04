<?php
// Kết nối CSDL
include 'includes/db.php'; // Đảm bảo đường dẫn đúng

$message = '';
$is_error_message = false; // Biến để kiểm soát class message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username === '' || $password === '' || $confirm_password === '' || $email === '') {
        $message = 'Vui lòng điền đầy đủ thông tin.';
        $is_error_message = true;
    } elseif ($password !== $confirm_password) {
        $message = 'Mật khẩu nhập lại không khớp.';
        $is_error_message = true;
    } else {
        // Kiểm tra tài khoản đã tồn tại chưa
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $message = 'Tên đăng nhập hoặc Email đã tồn tại.';
            $is_error_message = true;
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert = mysqli_prepare($conn, "INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_insert, "sss", $username, $hashed_password, $email);
            if (mysqli_stmt_execute($stmt_insert)) {
                $message = 'Đăng ký thành công! <a href="login.php">Đăng nhập ngay</a>';
                // $is_error_message = false; // Mặc định là false, không cần gán lại
            } else {
                $message = 'Có lỗi xảy ra, vui lòng thử lại.';
                $is_error_message = true;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Đăng ký</title>
    <link rel="stylesheet" href="assets/styles.css"> </head>
<body>
    <?php include 'includes/header.php'; // Bao gồm header ?>

    <main class="form-page-main"> <div class="form-container">
            <h2>Đăng ký</h2>
            <?php if ($message): ?>
                <div class="message <?php echo $is_error_message ? 'error-message' : 'success-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form action="register.php" method="POST" class="auth-form"> <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" placeholder="Chọn tên đăng nhập" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Nhập địa chỉ email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="Tạo mật khẩu" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Nhập lại mật khẩu</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>
                </div>
                <button type="submit" class="submit-button">Đăng ký</button> </form>
            <p class="form-footer"> Đã có tài khoản? <a href="login.php">Đăng nhập</a>
            </p>
        </div>
    </main>

    <?php include 'includes/footer.php'; // Nếu bạn có footer riêng ?>
</body>
</html>