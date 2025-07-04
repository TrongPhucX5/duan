<?php
session_start(); // Bắt đầu session
include 'includes/header.php';
include 'includes/db.php'; // Đảm bảo đường dẫn đúng
// Không include 'includes/search.php' ở đây nếu nó không cần thiết cho trang này
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Mục Sách - Thư viện Ebook</title>
    <link rel="stylesheet" href="assets/styles.css"> <link rel="stylesheet" href="assets/categories.css"> </head>
<body>

<main>
    <section class="category-grid-section">
        <?php
        // Nếu có ?name=... trên URL thì hiển thị sách thuộc thể loại đó
        if (isset($_GET['name'])) {
            $categoryName = $_GET['name']; // Lấy tên thể loại từ URL

            echo '<h2 class="category-title-heading">Thể loại: ' . htmlspecialchars($categoryName) . '</h2>';

            // Sử dụng Prepared Statement để truy vấn sách theo thể loại
            $stmt = mysqli_prepare($conn, "SELECT id, title FROM books WHERE category = ? ORDER BY title ASC");
            if ($stmt === false) {
                die('Lỗi prepare statement: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "s", $categoryName); // "s" cho kiểu string
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                echo '<ul class="book-list-by-category">';
                while ($book = mysqli_fetch_assoc($result)) {
                    echo '<li><a href="book.php?id=' . $book['id'] . '">' . htmlspecialchars($book['title']) . '</a></li>';
                }
                echo '</ul>';
            } else {
                echo '<p class="no-books-message">Không tìm thấy sách nào trong thể loại này.</p>';
            }
            mysqli_stmt_close($stmt); // Đóng statement
        } else {
            // Nếu không có ?name=..., hiển thị tất cả danh mục và sách trong từng danh mục
            $query_distinct_categories = "SELECT DISTINCT category FROM books WHERE category IS NOT NULL AND category != '' ORDER BY category ASC";
            $result_distinct_categories = mysqli_query($conn, $query_distinct_categories);

            if ($result_distinct_categories === false) {
                die('Lỗi truy vấn danh mục: ' . mysqli_error($conn));
            }

            echo '<h1 class="page-main-title">Tất cả danh mục sách</h1>'; // Tiêu đề chính của trang

            while ($row_category = mysqli_fetch_assoc($result_distinct_categories)) {
                $category = htmlspecialchars($row_category['category']);
                echo '<div class="category-column-item">';
                // Đường dẫn href trỏ đến chính categories.php với tham số name
                echo '<h3 class="category-column-title"><a href="categories.php?name=' . urlencode($category) . '">' . $category . '</a></h3>';

                // Sử dụng Prepared Statement để truy vấn sách trong từng danh mục
                $book_stmt = mysqli_prepare($conn, "SELECT id, title FROM books WHERE category = ? ORDER BY title ASC LIMIT 5"); // Giới hạn 5 sách để trang không quá dài
                if ($book_stmt === false) {
                    die('Lỗi prepare statement sách: ' . mysqli_error($conn));
                }
                mysqli_stmt_bind_param($book_stmt, "s", $row_category['category']);
                mysqli_stmt_execute($book_stmt);
                $book_result = mysqli_stmt_get_result($book_stmt);

                echo '<ul class="book-list-compact">';
                while ($book = mysqli_fetch_assoc($book_result)) {
                    echo '<li><a href="book.php?id=' . $book['id'] . '">' . htmlspecialchars($book['title']) . '</a></li>';
                }
                echo '</ul>';
                // Thêm liên kết "Xem tất cả" nếu có thể có nhiều sách hơn 5
                if (mysqli_num_rows($book_result) == 5) { // Kiểm tra số lượng trả về
                    echo '<p class="view-all-link"><a href="categories.php?name=' . urlencode($category) . '">Xem tất cả</a></p>';
                }

                mysqli_stmt_close($book_stmt); // Đóng statement

                echo '</div>';
            }
            mysqli_free_result($result_distinct_categories); // Giải phóng bộ nhớ
        }
        ?>
    </section>
</main>

<?php
include 'includes/footer.php'; // Đảm bảo đường dẫn đúng
?>
</body>
</html>