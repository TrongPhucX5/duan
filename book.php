<?php
session_start();

include 'includes/db.php';
include 'includes/header.php';

$book = null;
$comments = [];
$is_favorite = false;
$user_id = $_SESSION['user_id'] ?? null;

// Lấy ID sách từ URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $book_id = intval($_GET['id']);

    // Tăng lượt xem
    $update_views_stmt = mysqli_prepare($conn, "UPDATE books SET views = views + 1 WHERE id = ?");
    if ($update_views_stmt) {
        mysqli_stmt_bind_param($update_views_stmt, "i", $book_id);
        mysqli_stmt_execute($update_views_stmt);
        mysqli_stmt_close($update_views_stmt);
    }

    // Lấy thông tin chi tiết sách (lấy cả file_data và file_name)
    $stmt = mysqli_prepare($conn, "SELECT id, title, author, category, description, views, rating, file_data, file_name FROM books WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $book_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $book = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }

    if ($book) {
        // Lấy bình luận cho sách này
        $comment_stmt = mysqli_prepare($conn, "SELECT c.content, c.created_at, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.book_id = ? ORDER BY c.created_at DESC");
        if ($comment_stmt) {
            mysqli_stmt_bind_param($comment_stmt, "i", $book_id);
            mysqli_stmt_execute($comment_stmt);
            $comments_result = mysqli_stmt_get_result($comment_stmt);
            while ($row = mysqli_fetch_assoc($comments_result)) {
                $comments[] = $row;
            }
            mysqli_stmt_close($comment_stmt);
        }

        // Kiểm tra yêu thích
        if ($user_id) {
            $fav_stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM favorites WHERE user_id = ? AND book_id = ?");
            if ($fav_stmt) {
                mysqli_stmt_bind_param($fav_stmt, "ii", $user_id, $book_id);
                mysqli_stmt_execute($fav_stmt);
                mysqli_stmt_bind_result($fav_stmt, $count);
                mysqli_stmt_fetch($fav_stmt);
                if ($count > 0) $is_favorite = true;
                mysqli_stmt_close($fav_stmt);
            }
        }
    } else {
        header("Location: 404.php");
        exit();
    }
} else {
    header("Location: 404.php");
    exit();
}

// Xử lý thêm bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true && $user_id) {
        $comment_content = trim($_POST['comment_content'] ?? '');
        $book_id_post = intval($_POST['book_id'] ?? 0);

        if ($comment_content && $book_id_post === $book['id']) {
            $insert_comment_stmt = mysqli_prepare($conn, "INSERT INTO comments (user_id, book_id, content) VALUES (?, ?, ?)");
            if ($insert_comment_stmt) {
                mysqli_stmt_bind_param($insert_comment_stmt, "iis", $user_id, $book_id_post, $comment_content);
                if (mysqli_stmt_execute($insert_comment_stmt)) {
                    header("Location: book.php?id=" . $book_id_post);
                    exit();
                } else {
                    echo "<p class='error-message'>Lỗi khi thêm bình luận: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
                }
                mysqli_stmt_close($insert_comment_stmt);
            } else {
                echo "<p class='error-message'>Lỗi prepare statement thêm bình luận: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
            }
        } else {
            echo "<p class='error-message'>Bình luận không được để trống hoặc ID sách không hợp lệ.</p>";
        }
    } else {
        echo "<p class='error-message'>Bạn cần đăng nhập để bình luận.</p>";
    }
}

// Xử lý thêm/xóa sách yêu thích
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favorite'])) {
    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true && $user_id) {
        $book_id_post = intval($_POST['book_id'] ?? 0);
        if ($book_id_post === $book['id']) {
            if ($is_favorite) {
                $delete_fav_stmt = mysqli_prepare($conn, "DELETE FROM favorites WHERE user_id = ? AND book_id = ?");
                if ($delete_fav_stmt) {
                    mysqli_stmt_bind_param($delete_fav_stmt, "ii", $user_id, $book_id_post);
                    mysqli_stmt_execute($delete_fav_stmt);
                    mysqli_stmt_close($delete_fav_stmt);
                }
            } else {
                $insert_fav_stmt = mysqli_prepare($conn, "INSERT INTO favorites (user_id, book_id) VALUES (?, ?)");
                if ($insert_fav_stmt) {
                    mysqli_stmt_bind_param($insert_fav_stmt, "ii", $user_id, $book_id_post);
                    mysqli_stmt_execute($insert_fav_stmt);
                    mysqli_stmt_close($insert_fav_stmt);
                }
            }
            header("Location: book.php?id=" . $book_id_post);
            exit();
        }
    } else {
        echo "<p class='error-message'>Bạn cần đăng nhập để thêm/xóa sách yêu thích.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $book ? htmlspecialchars($book['title']) : 'Sách không tìm thấy'; ?> - Thư viện Ebook</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="assets/book_detail.css">
</head>
<body>

<main>
    <?php if ($book): ?>
    <div class="book-detail-container">
        <div class="book-cover">
            <img src="cover_image.php?id=<?php echo $book['id']; ?>" alt="Bìa sách <?php echo htmlspecialchars($book['title']); ?>">
        </div>
        <div class="book-info">
            <h1><?php echo htmlspecialchars($book['title']); ?></h1>
            <p class="author">Tác giả: <?php echo htmlspecialchars($book['author']); ?></p>
            <p class="category">Thể loại: <?php echo htmlspecialchars($book['category']); ?></p>
            <p>Lượt xem: <?php echo number_format($book['views']); ?></p>
            <p>Đánh giá: <?php echo htmlspecialchars($book['rating']); ?>/5</p>

            <div class="book-actions">
                <?php if (!empty($book['file_data'])): ?>
                    <button class="action-button read-book-button" id="readBookBtn">Đọc sách</button>
                    <a href="download.php?id=<?php echo $book['id']; ?>" class="action-button download-book-button">Tải sách</a>
                <?php else: ?>
                    <p class="no-file-message">Không có file sách để đọc/tải.</p>
                <?php endif; ?>
                
                <form action="book.php?id=<?php echo $book['id']; ?>" method="post" class="favorite-form-inline">
                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                    <?php if ($user_id): ?>
                        <button type="submit" name="toggle_favorite" class="action-button favorite-button <?php echo $is_favorite ? 'active' : ''; ?>">
                            <?php echo $is_favorite ? '&#9733; Đã yêu thích' : '&#9734; Yêu thích'; ?>
                        </button>
                    <?php else: ?>
                        <button type="button" class="action-button favorite-button disabled" disabled title="Đăng nhập để yêu thích">
                            &#9734; Yêu thích
                        </button>
                    <?php endif; ?>
                </form>
            </div>
            <p class="description">
                <strong>Mô tả:</strong><br>
                <?php echo nl2br(htmlspecialchars($book['description'])); ?>
            </p>
        </div>
    </div>

    <?php if (!empty($book['file_data'])): ?>
    <div id="pdfViewerContainer" class="pdf-viewer-container" style="display: none;">
        <div class="pdf-viewer-header">
            <h2>Đọc sách: <?php echo htmlspecialchars($book['title']); ?></h2>
            <button id="closePdfViewer" class="close-pdf-viewer-button">X</button>
        </div>
        <iframe id="pdfViewerFrame" src="" frameborder="0" width="100%" height="600px"></iframe>
    </div>
    <?php endif; ?>

    <div class="comments-section">
        <h2>Bình luận</h2>
        <?php if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true && $user_id): ?>
        <form action="book.php?id=<?php echo $book['id']; ?>" method="post" class="comment-form">
            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
            <textarea name="comment_content" rows="4" placeholder="Viết bình luận của bạn tại đây..." required></textarea>
            <button type="submit" name="add_comment" class="button">Gửi bình luận</button>
        </form>
        <?php else: ?>
        <p style="text-align: center;">Vui lòng <a href="login.php">đăng nhập</a> để gửi bình luận.</p>
        <?php endif; ?>

        <div class="comments-list">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <p>
                            <span class="comment-author"><?php echo htmlspecialchars($comment['username']); ?></span>
                            <span class="comment-date"><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></span>
                        </p>
                        <p class="comment-content"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center;">Chưa có bình luận nào cho sách này. Hãy là người đầu tiên!</p>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
    <div style="text-align: center; margin: 50px;">
        <h2>Không tìm thấy sách.</h2>
        <p>Có thể sách bạn tìm không tồn tại hoặc đã bị xóa.</p>
        <a href="index.php" class="button">Quay lại trang chủ</a>
    </div>
    <?php endif; ?>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const readBookBtn = document.getElementById('readBookBtn');
        const pdfViewerContainer = document.getElementById('pdfViewerContainer');
        const pdfViewerFrame = document.getElementById('pdfViewerFrame');
        const closePdfViewer = document.getElementById('closePdfViewer');
        
        if (readBookBtn && pdfViewerContainer && pdfViewerFrame && closePdfViewer) {
            readBookBtn.addEventListener('click', function() {
                // Đọc PDF từ CSDL qua view_pdf.php
                const pdfPath = 'view_pdf.php?id=<?php echo $book['id']; ?>';
                pdfViewerFrame.src = pdfPath;
                pdfViewerContainer.style.display = 'block';
                pdfViewerContainer.scrollIntoView({ behavior: 'smooth' });
            });

            closePdfViewer.addEventListener('click', function() {
                pdfViewerFrame.src = '';
                pdfViewerContainer.style.display = 'none';
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>