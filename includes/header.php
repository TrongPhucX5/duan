<?php
// includes/header.php
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
  const dropdown = document.querySelector('.main-nav .dropdown');
  const arrow = dropdown.querySelector('.dropdown-arrow');
  const dropdownContent = dropdown.querySelector('.dropdown-content');

  arrow.addEventListener('click', function(e) {
    e.preventDefault();
    dropdownContent.classList.toggle('open');
    arrow.classList.toggle('active');
  });

  // Đóng dropdown khi click ra ngoài
  document.addEventListener('click', function(e) {
    if (!dropdown.contains(e.target)) {
      dropdownContent.classList.remove('open');
      arrow.classList.remove('active');
    }
  });
});
</script>
<header>
  <div class="top-bar">
    <div class="logo">
      <a href="index.php">Thư viện Ebook</a>
    </div>
    <div class="log-container">
      <?php if (isset($_SESSION['user'])): ?>
        <span>Xin chào, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
        <a href="logout.php">Đăng xuất</a>
      <?php else: ?>
        <a href="login.php">Đăng nhập</a>
        <a href="register.php">Đăng ký</a>
      <?php endif; ?>
    </div>
    <nav class="main-nav">
      <ul>
        <li><a href="index.php">Trang Chủ</a></li>
        <li class="dropdown">
          <a href="#">Danh Mục <span class="dropdown-arrow">&#9660;</span></a>
          <div class="dropdown-content">
            <a href="category.php?name=Cổ tích">Cổ tích</a>
            <a href="category.php?name=Thiếu nhi">Thiếu nhi</a>
            <a href="category.php?name=Văn học Việt Nam">Văn học Việt Nam</a>
            <a href="category.php?name=Truyện ngắn">Truyện ngắn</a>
            <a href="category.php?name=Khoa học">Khoa học</a>
            <a href="category.php?name=Tiểu thuyết">Tiểu thuyết</a>
            <a href="category.php?name=Kinh điển">Kinh điển</a>
            <a href="category.php?name=Trinh thám">Trinh thám</a>
            <a href="category.php?name=Kỹ năng sống">Kỹ năng sống</a>
            <a href="category.php?name=Khác">Khác</a>
          </div>
        </li>
        <li><a href="tac-gia.php">Tác Giả</a></li>
        <li><a href="the-loai.php">Thể Loại</a></li>
        <li><a href="sach-moi.php">Sách Mới</a></li>
        <li><a href="lien-he.php">Liên Hệ</a></li>
      </ul>
    </nav>
  </div>
</header>