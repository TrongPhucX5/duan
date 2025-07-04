<?php
session_start();
include 'includes/db.php'; // Kết nối CSDL

$message = '';
$is_error_message = false;
$token = $_GET['token'] ?? ''; // Lấy token từ URL (ví dụ: ?token=abcxyz)

// Ban đầu, kiểm tra token ngay khi trang được tải
if (empty($token)) {
    $message = 'Liên kết đặt lại mật khẩu không hợp lệ hoặc thiếu.';
    $is_error_message = true;
    $show_form = false; // Không hiển thị form nếu token không hợp lệ
} else {
    // 1. Kiểm tra token có tồn tại trong CSDL và chưa hết hạn không
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$user) {
        $message = 'Liên kết đặt lại mật khẩu không hợp lệ, đã hết hạn, hoặc đã được sử dụng.';
        $is_error_message = true;
        $show_form = false; // Không hiển thị form
    } else {
        // Token hợp lệ, cho phép hiển thị form đặt lại mật khẩu
        $show_form = true;

        // Xử lý khi người dùng gửi form đặt lại mật khẩu mới
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['new_password'] ?? '';
            $confirm_new_password = $_POST['confirm_new_password'] ?? '';

            if (empty($new_password) || empty($confirm_new_password)) {
                $message = 'Vui lòng nhập đầy đủ mật khẩu mới và xác nhận.';
                $is_error_message = true;
                $show_form = true; // Vẫn hiển thị form để người dùng sửa
            } elseif ($new_password !== $confirm_new_password) {
                $message = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
                $is_error_message = true;
                $show_form = true; // Vẫn hiển thị form
            } else {
                // Hash mật khẩu mới
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Cập nhật mật khẩu và xóa token để nó không thể sử dụng lại
                $stmt = mysqli_prepare($conn, "UPDATE users SET password = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?");
                mysqli_stmt_bind_param($stmt, "si", $hashed_new_password, $user['id']);
                if (mysqli_stmt_execute($stmt)) {
                    $message = 'Mật khẩu của bạn đã được đặt lại thành công! Bạn có thể <a href="login.php">Đăng nhập</a> ngay bây giờ.';
                    $is_error_message = false; // Thành công
                    $show_form = false; // Không hiển thị form nữa
                } else {
                    $message = 'Đã xảy ra lỗi khi đặt lại mật khẩu. Vui lòng thử lại.';
                    $is_error_message = true;
                    $show_form = true; // Vẫn hiển thị form để thử lại
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; // Bao gồm header ?>
    <main class="form-page-main">
        <div class="form-container">
            <h2>Đặt lại mật khẩu</h2>
            <?php if ($message): ?>
                <div class="message <?php echo $is_error_message ? 'error-message' : 'success-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($show_form): // Chỉ hiển thị form nếu token hợp lệ và chưa đặt lại mật khẩu thành công ?>
                <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="post" class="auth-form">
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới:</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_new_password">Xác nhận mật khẩu mới:</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="Nhập lại mật khẩu mới" required>
                    </div>
                    <button type="submit" class="submit-button">Đặt lại mật khẩu</button>
                </form>
            <?php endif; ?>
            <p class="form-footer">
                <a href="login.php">Quay lại Đăng nhập</a>
            </p>
        </div>
    </main>
    <?php include 'includes/footer.php'; // Nếu bạn có footer ?>
</body>
</html>