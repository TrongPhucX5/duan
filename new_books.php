<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sách Mới Cập Nhật - Thư viện Ebook</title>
    <link rel="stylesheet" href="assets/styles.css"> <link rel="stylesheet" href="assets/new_books.css"> </head>
<body>

<main>
    <section class="new-books-section">
        <h1 class="page-main-title">Sách Mới Cập Nhật</h1>

        <?php
        $new_books = [];
        // Lấy 12 cuốn sách mới nhất, sắp xếp theo created_at (hoặc id nếu không có created_at) giảm dần
        // Sử dụng Prepared Statement (mặc dù không có tham số đầu vào từ người dùng, đây là thói quen tốt)
        $stmt = mysqli_prepare($conn, "SELECT id, title, author, cover_image FROM books ORDER BY created_at DESC, id DESC LIMIT 12");
        
        if ($stmt === false) {
            die('Lỗi prepare statement: ' . mysqli_error($conn));
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="book-grid">';
            while ($book = mysqli_fetch_assoc($result)) {
                echo '<div class="book-item">';
                echo '<a href="book.php?id=' . $book['id'] . '">';
                echo '<img src="uploads/' . htmlspecialchars($book['cover_image']) . '" alt="Bìa sách ' . htmlspecialchars($book['title']) . '">';
                echo '<h3>' . htmlspecialchars($book['title']) . '</h3>';
                echo '<p>Tác giả: ' . htmlspecialchars($book['author']) . '</p>';
                echo '</a>';
                echo '<a href="book.php?id=' . $book['id'] . '" class="detail-button">Xem chi tiết</a>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p class="no-books-message">Chưa có sách mới nào được thêm vào thư viện.</p>';
        }
        mysqli_stmt_close($stmt);
        ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>