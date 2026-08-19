<?php

require_once "admin_auth.php";

require_permission("suppliers");

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    DELETE FROM suppliers
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: suppliers.php");
exit();

?>