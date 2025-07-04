<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư viện Ebook</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="assets/sidebar.css">
    <link rel="stylesheet" href="assets/banner.css">
    <link rel="stylesheet" href="assets/benefits.css">
    <script src="js/jquery-3.7.1.js"></script>
</head>
<body>
<?php
session_start(); // Luôn đặt ở đầu file PHP để sử dụng session
require_once 'includes/db.php';
include 'includes/header.php'; // Bao gồm header
?>

<div class="banner-slider">
    <div class="banner-slides">
        <img src="assets/images/banner1.jpg" class="slide active">
        <img src="assets/images/banner1.jpg" class="slide">
    </div>
    <button class="prev">&#10094;</button>
    <button class="next">&#10095;</button>
</div>

<div class="banner-static">
    <img src="assets/images/bannerButton.jpg" alt="Banner 2">
</div>

<script>
$(document).ready(function() {
    let currentIndex = 0;
    const slides = $('.slide');
    const totalSlides = slides.length;

    function showSlide(index) {
        slides.removeClass('active');
        slides.eq(index).addClass('active');
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalSlides;
        showSlide(currentIndex);
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        showSlide(currentIndex);
    }

    $('.next').click(nextSlide);
    $('.prev').click(prevSlide);
    setInterval(nextSlide, 5000);
    showSlide(currentIndex);
});
</script>

<main>
    <?php
    // Kiểm tra nếu người dùng đã đăng nhập
    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
        echo '<div class="welcome-message" style="text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold; color: #333;">';
        echo 'Chào mừng <span style="color: #007bff;">' . htmlspecialchars($_SESSION['username']) . '</span> đến với Thư viện Ebook!';
        echo '</div>';
        // Bạn có thể thêm các phần nội dung riêng cho người dùng đã đăng nhập tại đây
        // Ví dụ: Sách yêu thích, Lịch sử đọc, ...
    } else {
        // Nội dung hiển thị khi chưa đăng nhập
        echo '<div class="welcome-message" style="text-align: center; margin: 20px 0; font-size: 18px; color: #555;">';
        echo 'Chào mừng bạn đến với Thư viện Ebook! Vui lòng <a href="login.php" style="color: #007bff; text-decoration: none;">Đăng nhập</a> hoặc <a href="register.php" style="color: #007bff; text-decoration: none;">Đăng ký</a> để khám phá kho sách đa dạng.';
        echo '</div>';
    }
    ?>

    <section class="featured-books">
        <h2>Sách Nổi Bật</h2>
        <div class="book-list">
            <?php
            $query = "SELECT * FROM books ORDER BY rating DESC LIMIT 4";
            $result = mysqli_query($conn, $query);
            while ($book = mysqli_fetch_assoc($result)) {
                echo '<div class="book-item">';
                echo '<img src="cover_image.php?id=' . $book['id'] . '" alt="Ảnh bìa">';
                echo '<h3>' . htmlspecialchars($book['title']) . '</h3>';
                echo '<p>' . htmlspecialchars($book['author']) . '</p>';
                echo '<a href="book.php?id=' . $book['id'] . '">Xem chi tiết</a>';
                echo '</div>';
            }
            ?>
        </div>
    </section>

    <section class="new-books">
        <h2>Sách Mới Cập Nhật</h2>
        <div class="book-list">
            <?php
            $query = "SELECT * FROM books ORDER BY created_at DESC LIMIT 4";
            $result = mysqli_query($conn, $query);
            while ($book = mysqli_fetch_assoc($result)) {
                echo '<div class="book-item">';
                echo '<img src="cover_image.php?id=' . $book['id'] . '" alt="Ảnh bìa">';
                echo '<h3>' . htmlspecialchars($book['title']) . '</h3>';
                echo '<p>' . htmlspecialchars($book['author']) . '</p>';
                echo '<a href="book.php?id=' . $book['id'] . '">Xem chi tiết</a>';
                echo '</div>';
            }
            ?>
        </div>
    </section>

    <section class="top-read-books">
        <h2>Top Sách Đọc Nhiều</h2>
        <div class="book-list">
            <?php
            $query = "SELECT * FROM books ORDER BY views DESC LIMIT 4";
            $result = mysqli_query($conn, $query);
            while ($book = mysqli_fetch_assoc($result)) {
                echo '<div class="book-item">';
                echo '<img src="cover_image.php?id=' . $book['id'] . '" alt="Ảnh bìa">';
                echo '<h3>' . htmlspecialchars($book['title']) . '</h3>';
                echo '<p>' . htmlspecialchars($book['author']) . '</p>';
                echo '<a href="book.php?id=' . $book['id'] . '">Xem chi tiết</a>';
                echo '</div>';
            }
            ?>
        </div>
    </section>
</main>

<section class="benefits">
    <div class="benefits-container">
        <div class="benefit-item">
            <div class="benefit-icon">🎯</div>
            <div>
                <h3>Hoàn Toàn Miễn Phí – Không Giới Hạn Truy Cập</h3>
                <p>Bạn không cần đăng ký hay thanh toán bất kỳ khoản phí nào. Chỉ cần có kết nối Internet, bạn sẽ mở ra kho tàng tri thức không giới hạn mà không phải lo về chi phí.</p>
            </div>
        </div>
        <div class="benefit-item">
            <div class="benefit-icon">📚</div>
            <div>
                <h3>Thể Loại Đa Dạng</h3>
                <p>Từ tiểu thuyết, kinh doanh, khoa học, tự truyện đến nhiều thể loại khác, đáp ứng mọi nhu cầu đọc sách.</p>
            </div>
        </div>
        <div class="benefit-item">
            <div class="benefit-icon">👌</div>
            <div>
                <h3>Thân Thiện Người Dùng – Giao Diện Dễ Sử Dụng</h3>
                <p>Thiết kế đơn giản, dễ tìm kiếm và chọn sách yêu thích. Trải nghiệm mượt mà và thân thiện với mọi đối tượng người dùng.</p>
            </div>
        </div>
        <div class="benefit-item">
            <div class="benefit-icon">🚀</div>
            <div>
                <h3>Tiện Lợi</h3>
                <p>Bạn có thể đọc sách ở bất cứ đâu, bất cứ lúc nào với thiết bị có kết nối internet.</p>
            </div>
        </div>
    </div>
</section>

<?php
include 'includes/sidebar.php';
include 'includes/footer.php';
?>
</body>
</html>