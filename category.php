<?php
include 'includes/header.php';
include 'includes/search.php';
include 'includes/db.php';
?>

<main>
  <section class="category-grid">
    <?php
    // Nếu có ?name=... trên URL thì hiển thị sách thuộc thể loại đó
    if (isset($_GET['name'])) {
      $categoryName = mysqli_real_escape_string($conn, $_GET['name']);
      echo '<h2 style="text-align:center">Thể loại: ' . htmlspecialchars($categoryName) . '</h2>';

      $query = "SELECT id, title FROM books WHERE category = '$categoryName' ORDER BY title ASC";
      $result = mysqli_query($conn, $query);

      if (mysqli_num_rows($result) > 0) {
        echo '<ul style="max-width:600px;margin:0 auto">';
        while ($book = mysqli_fetch_assoc($result)) {
          echo '<li><a href="book.php?id=' . $book['id'] . '">' . htmlspecialchars($book['title']) . '</a></li>';
        }
        echo '</ul>';
      } else {
        echo '<p style="text-align:center">Không tìm thấy sách nào trong thể loại này.</p>';
      }
    } else {
      // Nếu không có ?name=..., hiển thị tất cả danh mục
      $query = "SELECT DISTINCT category FROM books ORDER BY category ASC";
      $result = mysqli_query($conn, $query);
      while ($row = mysqli_fetch_assoc($result)) {
        $category = htmlspecialchars($row['category']);
        echo '<div class="category-column">';
        echo '<h3><a href="category.php?name=' . urlencode($category) . '">' . $category . '</a></h3>';

        $book_query = "SELECT id, title FROM books WHERE category = '" . mysqli_real_escape_string($conn, $row['category']) . "' ORDER BY title ASC";
        $book_result = mysqli_query($conn, $book_query);
        echo '<ul>';
        while ($book = mysqli_fetch_assoc($book_result)) {
          echo '<li><a href="book.php?id=' . $book['id'] . '">' . htmlspecialchars($book['title']) . '</a></li>';
        }
        echo '</ul>';

        echo '</div>';
      }
    }
    ?>
  </section>
</main>

<?php
include 'includes/footer.php';
?>
