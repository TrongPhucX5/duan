<?php
// includes/header.php
?>
<header>
  <div class="top-bar">
    <div class="logo">
      <a href="index.php">Thư viện Ebook</a>
    </div>
    <div class="login-container">
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
        <li><a href="category.php">Danh Mục</a></li>
        <li><a href="tac-gia.php">Tác Giả</a></li>
        <li><a href="the-loai.php">Thể Loại</a></li>
        <li><a href="sach-moi.php">Sách Mới</a></li>
        <li><a href="lien-he.php">Liên Hệ</a></li>
      </ul>
    </nav>
  </div>
</header>
