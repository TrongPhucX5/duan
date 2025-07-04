<?php
include 'includes/db.php';
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT cover_image_data, cover_image_type FROM books WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($data, $type);
        $stmt->fetch();
        if ($data && $type) {
            header("Content-Type: $type");
            echo $data;
            exit;
        }
    }
}
http_response_code(404);
exit;