<?php
session_start();
include 'includes/db.php'; // Kết nối CSDL

// Tùy chọn: Nếu dùng Composer, uncomment dòng này:
require 'vendor/autoload.php';
// Nếu dùng PHPMailer thủ công, cần include các file này:
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
// Các dòng require PHPMailer, SỬA LẠI THÀNH ĐÚNG ĐƯỜNG DẪN NÀY:
require 'vendor/phpmailer/phpmailer/src/Exception.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';

// File cấu hình email bạn vừa tạo
include 'includes/config_email.php'; // Đảm bảo file này nằm trong thư mục includes

$message = ''; // Biến để lưu thông báo cho người dùng
$is_error_message = false; // Để xác định loại thông báo (lỗi hay thành công)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $message = 'Vui lòng nhập địa chỉ email của bạn.';
        $is_error_message = true;
    } else {
        // 1. Kiểm tra email có tồn tại trong CSDL không
        $stmt = mysqli_prepare($conn, "SELECT id, username FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($user) {
            // 2. Tạo mã thông báo ngẫu nhiên và thời gian hết hạn (ví dụ: 1 giờ)
            $token = bin2hex(random_bytes(32)); // Tạo chuỗi ngẫu nhiên 64 ký tự hex
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token hết hạn sau 1 giờ

            // 3. Lưu mã thông báo và thời gian hết hạn vào CSDL cho người dùng này
            $stmt = mysqli_prepare($conn, "UPDATE users SET reset_token = ?, reset_token_expires_at = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $token, $expires, $user['id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // 4. Gửi email chứa liên kết đặt lại mật khẩu
            $mail = new PHPMailer(true);
            try {
                // Cấu hình SMTP (sử dụng thông tin từ config_email.php)
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USERNAME;
                $mail->Password   = SMTP_PASSWORD;
                $mail->SMTPSecure = SMTP_ENCRYPTION;
                $mail->Port       = SMTP_PORT;

                // Cấu hình thông tin người gửi và người nhận
                $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
                $mail->addAddress($email, $user['username']); // Gửi đến email của người dùng yêu cầu

                // Cấu hình nội dung email
                $mail->isHTML(true); // Định dạng email HTML
                $mail->CharSet = 'UTF-8'; // Hỗ trợ tiếng Việt
                $mail->Subject = 'Yeu cau dat lai mat khau cua Thu vien Ebook'; // Tiêu đề email

                // **ĐẶC BIỆT QUAN TRỌNG:** Thay đổi URL này cho phù hợp với dự án của bạn
                // Nếu bạn đang chạy trên localhost/duan, giữ nguyên.
                // Nếu bạn đã upload lên hosting thật, thay 'http://localhost/duan/' bằng 'https://tenmiencuaban.com/'
                $resetLink = "http://localhost/duan1/reset_password.php?token=" . $token; // Đảm bảo đúng 'duan1'

                $mail->Body    = "Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình tại Thư viện Ebook.<br><br>"
                               . "Vui lòng nhấp vào liên kết sau để đặt lại mật khẩu: <br>"
                               . "<a href='" . htmlspecialchars($resetLink) . "'>" . htmlspecialchars($resetLink) . "</a><br><br>"
                               . "Liên kết này sẽ hết hạn sau <b>1 giờ</b>. Vui lòng sử dụng sớm.<br>"
                               . "Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.";
                $mail->AltBody = "Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình tại Thư viện Ebook.\n"
                               . "Vui lòng truy cập liên kết sau để đặt lại mật khẩu: " . $resetLink . "\n"
                               . "Liên kết này sẽ hết hạn sau 1 giờ. Vui lòng sử dụng sớm.\n"
                               . "Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.";

                $mail->send();
                $message = 'Một liên kết đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư đến (và thư mục Spam/Junk nếu không thấy).';
                $is_error_message = false; // Thông báo thành công
            } catch (Exception $e) {
                // Xử lý lỗi khi gửi email
                $message = "Không thể gửi email đặt lại mật khẩu. Lỗi: {$mail->ErrorInfo}";
                $is_error_message = true; // Thông báo lỗi
            }
        } else {
            // Email không tồn tại trong hệ thống, nhưng vẫn nên thông báo chung chung để tránh lộ thông tin tài khoản
            $message = 'Nếu email của bạn tồn tại trong hệ thống, một liên kết đặt lại mật khẩu sẽ được gửi đến. Vui lòng kiểm tra hộp thư đến.';
            $is_error_message = false; // Coi như thành công để không lộ email nào tồn tại
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; // Bao gồm header ?>
    <main class="form-page-main">
        <div class="form-container">
            <h2>Quên mật khẩu</h2>
            <?php if ($message): ?>
                <div class="message <?php echo $is_error_message ? 'error-message' : 'success-message'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form action="forgot_password.php" method="post" class="auth-form">
                <div class="form-group">
                    <label for="email">Email đã đăng ký:</label>
                    <input type="email" id="email" name="email" placeholder="Nhập địa chỉ email của bạn" required>
                </div>
                <button type="submit" class="submit-button">Gửi yêu cầu đặt lại</button>
            </form>
            <p class="form-footer">
                <a href="login.php">Quay lại Đăng nhập</a>
            </p>
        </div>
    </main>
    <?php include 'includes/footer.php'; // Nếu bạn có footer ?>
</body>
</html>