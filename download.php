<?php
// filepath: download.php
include 'includes/db.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT file_name, file_data FROM books WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($file_name, $file_data);
        $stmt->fetch();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        echo $file_data;
        exit;
    }
}
http_response_code(404);
echo "Không tìm thấy file.";