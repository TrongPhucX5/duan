<?php
session_start();
include 'includes/header.php';
include 'includes/db.php'; // Có thể không cần thiết nếu chỉ là form tĩnh và không lưu vào DB
// include 'vendor/autoload.php'; // Chỉ cần nếu bạn muốn sử dụng PHPMailer để gửi email thật
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

$message = '';
$message_type = ''; // 'success' hoặc 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($content)) {
        $message = 'Vui lòng điền đầy đủ tất cả các trường.';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Địa chỉ email không hợp lệ.';
        $message_type = 'error';
    } else {
        // --- Phần này là để gửi email thật, bạn cần CẤU HÌNH VÀ BỎ COMMENT nếu muốn dùng ---
        // Để gửi email thật sự, bạn cần cài đặt PHPMailer (composer require phpmailer/phpmailer)
        // và cấu hình SMTP server của bạn.
        /*
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.your-email-provider.com'; // VD: smtp.gmail.com
            $mail->SMTPAuth   = true;
            $mail->Username   = 'your_email@example.com';      // Email của bạn
            $mail->Password   = 'your_email_password';          // Mật khẩu email của bạn (hoặc app password nếu dùng Gmail)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Hoặc PHPMailer::ENCRYPTION_SMTPS cho port 465
            $mail->Port       = 587; // TCP port to connect to, use 465 for PHPMailer::ENCRYPTION_SMTPS

            // Recipients
            $mail->setFrom($email, $name);
            $mail->addAddress('your_website_admin_email@example.com', 'Admin Thư viện Ebook'); // Địa chỉ email nhận liên hệ

            // Content
            $mail->isHTML(false); // Set email format to plain text
            $mail->Subject = '[Liên hệ] ' . $subject;
            $mail->Body    = "Tên: " . $name . "\n"
                            . "Email: " . $email . "\n"
                            . "Chủ đề: " . $subject . "\n\n"
                            . "Nội dung:\n" . $content;

            $mail->send();
            $message = 'Cảm ơn bạn đã liên hệ! Tin nhắn của bạn đã được gửi thành công.';
            $message_type = 'success';
            // Xóa dữ liệu form sau khi gửi thành công
            $_POST = array(); 
        } catch (Exception $e) {
            $message = "Không thể gửi tin nhắn. Lỗi: {$mail->ErrorInfo}. Vui lòng thử lại sau.";
            $message_type = 'error';
        }
        */

        // --- Nếu bạn chưa cấu hình PHPMailer, sẽ chỉ hiển thị thông báo thành công ảo ---
        $message = 'Cảm ơn bạn đã liên hệ! Tin nhắn của bạn đã được gửi thành công. (Chức năng gửi email thực tế cần được cấu hình).';
        $message_type = 'success';
        // Xóa dữ liệu form sau khi gửi thành công
        $_POST = array(); 
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên Hệ - Thư viện Ebook</title>
    <link rel="stylesheet" href="assets/styles.css"> <link rel="stylesheet" href="assets/contact.css"> </head>
<body>

<main>
    <section class="contact-section">
        <h1 class="page-main-title">Liên Hệ Với Chúng Tôi</h1>
        <p class="contact-description">
            Nếu bạn có bất kỳ câu hỏi, góp ý hoặc vấn đề cần hỗ trợ, đừng ngần ngại gửi tin nhắn cho chúng tôi qua biểu mẫu dưới đây.
            Chúng tôi sẽ phản hồi bạn trong thời gian sớm nhất.
        </p>

        <?php if ($message): ?>
            <div class="form-message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form action="contact.php" method="post" class="contact-form">
            <div class="form-group">
                <label for="name">Tên của bạn:</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email của bạn:</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="subject">Chủ đề:</label>
                <input type="text" id="subject" name="subject" required 
                       value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="content">Nội dung tin nhắn:</label>
                <textarea id="content" name="content" rows="8" required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="submit-button">Gửi Tin Nhắn</button>
        </form>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>