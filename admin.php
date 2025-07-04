<?php
include 'includes/db.php';
include 'includes/header.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true || !isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: login.php');
    exit;
}
// Xử lý xóa sách
if (isset($_POST['delete_book_id'])) {
    $book_id = intval($_POST['delete_book_id']);
    $conn->query("DELETE FROM books WHERE id = $book_id");
    $msg = "Đã xóa sách ID $book_id";
}

// Xử lý duyệt/xóa bình luận
if (isset($_POST['approve_comment_id'])) {
    $cid = intval($_POST['approve_comment_id']);
    $conn->query("UPDATE comments SET approved=1 WHERE id=$cid");
}
if (isset($_POST['delete_comment_id'])) {
    $cid = intval($_POST['delete_comment_id']);
    $conn->query("DELETE FROM comments WHERE id=$cid");
}

// Xử lý phân quyền/khóa tài khoản
if (isset($_POST['user_id'], $_POST['role'])) {
    $uid = intval($_POST['user_id']);
    $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
    $conn->query("UPDATE users SET role='$role' WHERE id=$uid");
}
if (isset($_POST['ban_user_id'])) {
    $uid = intval($_POST['ban_user_id']);
    $conn->query("UPDATE users SET banned=1 WHERE id=$uid");
}

// Xử lý upload sách
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $views = 0;
    $rating = 0;

    // Ảnh bìa
    $cover_image_data = null;
    $cover_image_type = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
        $cover_image_data = file_get_contents($_FILES['cover_image']['tmp_name']);
        $cover_image_type = $_FILES['cover_image']['type'];
    }

    // File sách (LẤY DỮ LIỆU TRƯỚC)
    $file_data = null;
    $file_name = null;
    if (isset($_FILES['file_path']) && $_FILES['file_path']['error'] == 0) {
        $file_name = basename($_FILES['file_path']['name']);
        $file_data = file_get_contents($_FILES['file_path']['tmp_name']);
    }

    // Chỉ insert các trường cần thiết
    $stmt = $conn->prepare("INSERT INTO books (title, author, category, description, cover_image_data, cover_image_type, file_name, file_data, views, rating)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssssii", // Tất cả là "s" (string) trừ views, rating là "i"
        $title,
        $author,
        $category,
        $description,
        $cover_image_data,
        $cover_image_type,
        $file_name,
        $file_data,
        $views,
        $rating
    );

    if ($stmt->execute()) {
        $msg = "Thêm sách thành công!";
    } else {
        $msg = "Lỗi: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản trị hệ thống</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="assets/styles.css">
    <style>
        .admin-menu { display: flex; justify-content: center; gap: 20px; margin: 30px 0 20px 0;}
        .admin-menu button { padding: 10px 24px; border: none; background: #e3f0fa; color: #2c5ca4; font-weight: 600; border-radius: 6px; cursor: pointer; font-size: 16px;}
        .admin-menu button.active { background: #2c5ca4; color: #fff; }
        .admin-section { display: none; }
        .admin-section.active { display: block; }
        /* ...giữ nguyên các style khác... */
        .admin-upload-form { max-width: 480px; margin: 40px auto; padding: 32px 28px; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(44, 92, 164, 0.08); display: flex; flex-direction: column; gap: 18px;}
        .admin-upload-form label { font-weight: 500; margin-bottom: 6px; color: #2c5ca4;}
        .admin-upload-form input[type="text"], .admin-upload-form textarea, .admin-upload-form input[type="file"] { width: 100%; padding: 10px 12px; border: 1px solid #cceeff; border-radius: 6px; font-size: 15px; background: #f9fbfd; margin-bottom: 8px;}
        .admin-upload-form textarea { resize: vertical; min-height: 60px; max-height: 180px;}
        .admin-upload-form button[type="submit"] { background: var(--primary-color); color: #fff; border: none; border-radius: 6px; padding: 12px 0; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 10px; transition: background 0.2s;}
        .admin-upload-form button[type="submit"]:hover { background: var(--primary-dark);}
        .msg { text-align: center; margin-bottom: 18px; font-weight: 500; color: #388e3c;}
        table { border-collapse: collapse; width: 100%; margin-bottom: 40px;}
        th, td { border: 1px solid #ccc; padding: 8px;}
        th { background: #e3f0fa;}
    </style>
</head>
<body>
    <h2 style="text-align:center;">Trang quản trị hệ thống</h2>
    <?php if (!empty($msg)) echo "<p class='msg'>$msg</p>"; ?>

    <div class="admin-menu">
        <button class="tab-btn active" data-tab="tab-books">Quản lý sách</button>
        <button class="tab-btn" data-tab="tab-comments">Kiểm duyệt bình luận</button>
        <button class="tab-btn" data-tab="tab-users">Quản lý người dùng</button>
    </div>

    <!-- Quản lý sách -->
    <div class="admin-section active" id="tab-books">
        <form method="post" enctype="multipart/form-data" class="admin-upload-form">
            <label>Tên sách:</label>
            <input type="text" name="title" required>
            <label>Tác giả:</label>
            <input type="text" name="author" required>
            <label>Thể loại:</label>
            <input type="text" name="category" required>
            <label>Mô tả:</label>
            <textarea name="description" rows="4" required></textarea>
            <label>Ảnh bìa:</label>
            <input type="file" name="cover_image" accept="image/*" required>
            <label>File sách (PDF):</label>
            <input type="file" name="file_path" accept=".pdf" required>
            <button type="submit">Upload Sách</button>
        </form>
        <h3 style="text-align:center;">Danh sách sách</h3>
        <table>
            <tr>
                <th>ID</th><th>Tên sách</th><th>Tác giả</th><th>Thể loại</th><th>Xóa</th>
            </tr>
            <?php
            $result = $conn->query("SELECT id, title, author, category FROM books ORDER BY id DESC");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['author']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="delete_book_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" onclick="return confirm('Xóa sách này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Kiểm duyệt bình luận -->
    <div class="admin-section" id="tab-comments">
        <h3 style="text-align:center;">Bình luận chờ duyệt</h3>
        <table>
            <tr>
                <th>ID</th><th>Sách</th><th>Người dùng</th><th>Nội dung</th><th>Duyệt</th><th>Xóa</th>
            </tr>
            <?php
            $result = $conn->query("SELECT c.id, b.title, u.username, c.content FROM comments c 
                JOIN books b ON c.book_id = b.id 
                JOIN users u ON c.user_id = u.id 
                WHERE c.approved = 0 ORDER BY c.id DESC");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['content']); ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="approve_comment_id" value="<?php echo $row['id']; ?>">
                        <button type="submit">Duyệt</button>
                    </form>
                </td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="delete_comment_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" onclick="return confirm('Xóa bình luận này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Quản lý người dùng -->
    <div class="admin-section" id="tab-users">
        <h3 style="text-align:center;">Quản lý người dùng</h3>
        <table>
            <tr>
                <th>ID</th><th>Tên đăng nhập</th><th>Email</th><th>Quyền</th><th>Khóa</th>
            </tr>
            <?php
            $result = $conn->query("SELECT id, username, email, role, banned FROM users ORDER BY id DESC");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <select name="role" onchange="this.form.submit()">
                            <option value="user" <?php if($row['role']=='user') echo 'selected'; ?>>User</option>
                            <option value="admin" <?php if($row['role']=='admin') echo 'selected'; ?>>Admin</option>
                        </select>
                    </form>
                </td>
                <td>
                    <?php if (!$row['banned']): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="ban_user_id" value="<?php echo $row['id']; ?>">
                        <button type="submit">Khóa</button>
                    </form>
                    <?php else: ?>
                        Đã khóa
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
        // Tab menu JS
        const tabBtns = document.querySelectorAll('.tab-btn');
        const sections = document.querySelectorAll('.admin-section');
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                tabBtns.forEach(b => b.classList.remove('active'));
                sections.forEach(sec => sec.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(btn.dataset.tab).classList.add('active');
            });
        });
    </script>
</body>
</html>