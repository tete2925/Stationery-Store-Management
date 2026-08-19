<?php

require_once "admin_auth.php";

require_permission("products");

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {

    $stmt = $conn->prepare("
        DELETE FROM products
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: products.php");
exit();

?>