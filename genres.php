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
    <title>Thể Loại Sách - Thư viện Ebook</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="assets/genres.css">
</head>
<body>

<main>
    <section class="genres-section">
        <h1 class="page-main-title">Các Thể Loại Sách</h1>

        <?php
        // Lấy tất cả category
        $allGenres = [];
        $stmt = mysqli_prepare($conn, "SELECT category FROM books WHERE category IS NOT NULL AND category != ''");
        if ($stmt === false) {
            die('Lỗi prepare statement: ' . mysqli_error($conn));
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        while ($row = mysqli_fetch_assoc($result)) {
            // Tách các tag bằng dấu phẩy
            $tags = explode(',', $row['category']);
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if ($tag !== '') {
                    $allGenres[] = $tag;
                }
            }
        }
        mysqli_stmt_close($stmt);

        // Loại bỏ trùng lặp và sắp xếp
        $allGenres = array_unique($allGenres);
        sort($allGenres, SORT_LOCALE_STRING);

        if (count($allGenres) > 0) {
            echo '<ul class="genre-list-grid">';
            foreach ($allGenres as $genreName) {
                echo '<li class="genre-item">';
                echo '<a href="search_results.php?query=' . urlencode($genreName) . '&type=category">';
                echo htmlspecialchars($genreName);
                echo '</a>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p class="no-genres-message">Không có thể loại nào được tìm thấy trong thư viện.</p>';
        }
        ?>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
</body>
</html>