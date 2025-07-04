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
    <title>Tác Giả - Thư viện Ebook</title>
    <link rel="stylesheet" href="assets/styles.css"> <link rel="stylesheet" href="assets/authors.css"> </head>
<body>

<main>
    <section class="authors-section">
        <h1 class="page-main-title">Danh Sách Tác Giả</h1>

        <?php
        // Lấy danh sách các tác giả từ bảng books
        // Sử dụng DISTINCT để chỉ lấy các tác giả duy nhất và loại bỏ giá trị rỗng/null
        $authors = [];
        $stmt = mysqli_prepare($conn, "SELECT DISTINCT author FROM books WHERE author IS NOT NULL AND author != '' ORDER BY author ASC");
        
        if ($stmt === false) {
            die('Lỗi prepare statement: ' . mysqli_error($conn));
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo '<ul class="author-list-grid">';
            while ($row = mysqli_fetch_assoc($result)) {
                $authorName = htmlspecialchars($row['author']);
                echo '<li class="author-item">';
                echo '<a href="search_results.php?query=' . urlencode($authorName) . '&type=author">';
                echo $authorName;
                echo '</a>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="no-authors-message">Không có tác giả nào được tìm thấy trong thư viện.</p>';
        }
        mysqli_stmt_close($stmt);
        ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>