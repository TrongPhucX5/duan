<?php
// includes/config_email.php
define('SMTP_HOST', 'smtp.gmail.com');          // Hoặc SMTP host của dịch vụ email bạn dùng
define('SMTP_USERNAME', 'email_cua_ban@gmail.com'); // <-- ĐẢM BẢO ĐÚNG EMAIL CỦA BẠN
define('SMTP_PASSWORD', 'mat_khau_ung_dung_hoac_mat_khau_goc'); // <-- ĐẢM BẢO ĐÚNG MẬT KHẨU HOẶC APP PASSWORD
define('SMTP_PORT', 587);                       // 587 cho TLS, 465 cho SSL
define('SMTP_ENCRYPTION', 'tls');               // 'tls' hoặc 'ssl'

define('MAIL_FROM_EMAIL', 'email_cua_ban@gmail.com'); // <-- ĐẢM BẢO ĐÚNG EMAIL CỦA BẠN
define('MAIL_FROM_NAME', 'Thư viện Ebook');
?>