<?php

require_once "admin_auth.php";

require_permission("inventory");

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT product_id, type, quantity
    FROM inventory
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row) {

    $conn->begin_transaction();

    try {

        if ($row['type'] === 'IN') {

            $stmt = $conn->prepare("
                UPDATE products
                SET stock = stock - ?
                WHERE id = ?
                AND stock >= ?
            ");

            $stmt->bind_param(
                "iii",
                $row['quantity'],
                $row['product_id'],
                $row['quantity']
            );

        } else {

            $stmt = $conn->prepare("
                UPDATE products
                SET stock = stock + ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "ii",
                $row['quantity'],
                $row['product_id']
            );
        }

        $stmt->execute();

        $stmt = $conn->prepare("
            DELETE FROM inventory
            WHERE id = ?
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $conn->commit();

    } catch (Exception $e) {

        $conn->rollback();
    }
}

header("Location: inventory.php");
exit();

?>