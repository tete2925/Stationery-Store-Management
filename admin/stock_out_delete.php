<?php

require_once "admin_auth.php";

require_permission("stock_out");


$id = intval($_GET['id'] ?? 0);


if ($id > 0) {

    $stmt = $conn->prepare("
        SELECT product_id, quantity
        FROM inventory
        WHERE id = ?
        AND type = 'OUT'
        LIMIT 1
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();


    if ($row) {

        $conn->begin_transaction();

        try {

            /*
             * OUT originally removed stock.
             * Deleting it gives that stock back.
             */

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

            if (!$stmt->execute()) {
                throw new Exception("Could not restore stock.");
            }


            /*
             * Delete movement.
             */

            $stmt = $conn->prepare("
                DELETE FROM inventory
                WHERE id = ?
                AND type = 'OUT'
            ");

            $stmt->bind_param("i", $id);

            if (!$stmt->execute()) {
                throw new Exception("Could not delete stock-out record.");
            }


            $conn->commit();

        } catch (Exception $e) {

            $conn->rollback();
        }
    }
}


header("Location: stock_out.php");
exit();