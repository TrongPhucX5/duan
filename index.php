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
session_start();
require_once 'includes/db.php';
include 'includes/header.php';
include 'includes/search.php';
?>

<!-- Banner 1 - Slideshow -->
<div class="banner-slider">
  <div class="banner-slides">
    <img src="assets/images/banner1.jpg" class="slide active">
    <img src="assets/images/banner1.jpg" class="slide">
  </div>
  <button class="prev">&#10094;</button>
  <button class="next">&#10095;</button>
</div>

<!-- Banner 2 - Static -->
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
  <section class="featured-books">
    <h2>Sách Nổi Bật</h2>
    <div class="book-list">
      <?php
      $query = "SELECT * FROM books ORDER BY rating DESC LIMIT 4";
      $result = mysqli_query($conn, $query);
      while ($book = mysqli_fetch_assoc($result)) {
        echo '<div class="book-item">';
        echo '<img src="uploads/' . htmlspecialchars($book['cover_image']) . '" alt="Ảnh bìa">';
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
        echo '<img src="uploads/' . htmlspecialchars($book['cover_image']) . '" alt="Ảnh bìa">';
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
        echo '<img src="uploads/' . htmlspecialchars($book['cover_image']) . '" alt="Ảnh bìa">';
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
        <p>Thiết kế đơn giản, dễ tìm kiếm và chọn sách yêu thích. Trải nghiệm mượt mà và thân thiện với mọọi đối tượng người dùng.</p>
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