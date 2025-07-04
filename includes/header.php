<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
?>
<header>
    <div class="top-bar" style="position:relative;">
        <div class="logo">
            <a href="index.php">Thư viện Ebook</a>
        </div>
        <div class="login-container">
            <?php if (!isset($_SESSION['username'])): ?>
                <a href="login.php">Đăng nhập</a>
                <span>|</span>
                <a href="register.php">Đăng ký</a>
            <?php endif; ?>
        </div>
        <?php if (isset($_SESSION['username'])): ?>
        <!-- Profile Dropdown + Xin chào -->
        <div style="position:absolute;top:10px;right:30px;display:flex;align-items:center;gap:10px;">
            <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
            <div class="profile-dropdown" id="profileDropdown">
                <div class="profile-icon" onclick="toggleDropdown()" title="<?php echo htmlspecialchars($username); ?>">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" stroke="#555" stroke-width="2"/>
                        <path d="M4 20c0-4 4-6 8-6s8 2 8 6" stroke="#555" stroke-width="2"/>
                    </svg>
                </div>
                <div class="dropdown-content" id="dropdownContent">
                    <a href="favorite_books.php">Sách yêu thích</a>
                    <a href="downloaded_books.php">Sách đã tải</a>
                    <a href="logout.php">Đăng xuất</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <style>
            .profile-dropdown { position: relative; display: inline-block;}
            .profile-icon { width: 40px; height: 40px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 22px; color: #555; transition: background 0.2s;}
            .profile-icon:hover { background: #ddd;}
            .dropdown-content { display: none; position: absolute; right: 0; background-color: #fff; min-width: 180px; box-shadow: 0 8px 16px rgba(0,0,0,0.15); border-radius: 6px; z-index: 1; margin-top: 8px;}
            .dropdown-content a { color: #333; padding: 12px 18px; text-decoration: none; display: block; border-bottom: 1px solid #f0f0f0; transition: background 0.2s;}
            .dropdown-content a:last-child { border-bottom: none;}
            .dropdown-content a:hover { background-color: #f5f5f5;}
            .profile-dropdown.show .dropdown-content { display: block;}
        </style>
        <script>
            function toggleDropdown() {
                document.getElementById('profileDropdown').classList.toggle('show');
            }
            window.onclick = function(event) {
                if (!event.target.closest('.profile-dropdown')) {
                    var pd = document.getElementById('profileDropdown');
                    if (pd) pd.classList.remove('show');
                }
            }
        </script>
    </div>

    <nav class="main-nav">
        <ul class="nav-links">
            <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Trang Chủ</a></li>
            <li><a href="categories.php" class="<?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">Danh Mục</a></li>
            <li><a href="authors.php" class="<?php echo ($current_page == 'authors.php') ? 'active' : ''; ?>">Tác Giả</a></li>
            <li><a href="genres.php" class="<?php echo ($current_page == 'genres.php') ? 'active' : ''; ?>">Thể Loại</a></li>
            <li><a href="new_books.php" class="<?php echo ($current_page == 'new_books.php') ? 'active' : ''; ?>">Sách Mới</a></li>
            <li><a href="contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Liên Hệ</a></li>
        </ul>
        <div class="search-box">
            <form action="search_results.php" method="get">
                <input type="text" name="query" placeholder="Tìm kiếm sách...">
                <button type="submit">Tìm</button>
            </form>
        </div>
    </nav>
</header>