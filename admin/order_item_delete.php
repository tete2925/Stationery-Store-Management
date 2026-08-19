<?php

require_once "admin_auth.php";

require_permission("orders");

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    DELETE FROM order_items
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: order_items.php");
exit();

?>