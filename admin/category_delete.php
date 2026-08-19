<?php

require_once "admin_auth.php";

require_permission("categories");

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {

    $stmt = $conn->prepare("
        DELETE FROM categories
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: category.php");
exit();

?>