<?php

require_once "admin_auth.php";

require_permission("stock_in");


$id = intval($_GET['id'] ?? 0);


$stmt = $conn->prepare("
    SELECT *
    FROM inventory
    WHERE id = ?
    AND type = 'IN'
    LIMIT 1
");

$stmt->bind_param("i", $id);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();


if (!$row) {

    header("Location: stock_in.php");
    exit();
}


$products = $conn->query("
    SELECT id, name, stock
    FROM products
    ORDER BY name
");


$error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $note = trim($_POST['note'] ?? '');


    if ($product_id <= 0) {

        $error = "Please select a product.";

    } elseif ($quantity <= 0) {

        $error = "Quantity must be greater than zero.";

    } else {

        $conn->begin_transaction();

        try {

            /*
             * Reverse the old stock-in movement.
             *
             * Example:
             * Old movement = +20
             * Product stock = 50
             *
             * Reverse it:
             * Product stock = 30
             */

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

            $stmt->execute();

            if ($stmt->affected_rows === 0) {
                throw new Exception(
                    "This stock-in record cannot be edited because the current stock is too low to reverse the original movement."
                );
            }


            /*
             * Apply the new stock-in movement.
             */

            $stmt = $conn->prepare("
                UPDATE products
                SET stock = stock + ?
                WHERE id = ?
            ");

            $stmt->bind_param(
                "ii",
                $quantity,
                $product_id
            );

            if (!$stmt->execute() || $stmt->affected_rows === 0) {
                throw new Exception("Could not update product stock.");
            }


            /*
             * Update inventory record.
             */

            $stmt = $conn->prepare("
                UPDATE inventory
                SET product_id = ?,
                    quantity = ?,
                    note = ?
                WHERE id = ?
                AND type = 'IN'
            ");

            $stmt->bind_param(
                "iisi",
                $product_id,
                $quantity,
                $note,
                $id
            );

            if (!$stmt->execute()) {
                throw new Exception("Could not update inventory record.");
            }


            $conn->commit();


            header("Location: stock_in.php");
            exit();

        } catch (Exception $e) {

            $conn->rollback();

            $error = $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Stock In</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet"
          href="adm.css">

</head>

<body>

<div class="admin-layout">

    <?php include "sidebar.php"; ?>


    <main class="admin-main">

        <div class="admin-topbar">

            <div>

                <p class="dashboard-label">
                    INVENTORY
                </p>

                <h1>Edit Stock In</h1>

            </div>

        </div>


        <div class="dashboard-panel">


            <?php if ($error !== ''): ?>

                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <form method="POST"
                  class="admin-form">


                <div class="form-group">

                    <label for="product_id">
                        Product
                    </label>

                    <select name="product_id"
                            id="product_id"
                            required>

                        <?php if ($products): ?>

                            <?php while ($product = $products->fetch_assoc()): ?>

                                <option value="<?= $product['id'] ?>"
                                    <?= $product['id'] == $row['product_id'] ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($product['name']) ?>

                                    (Current Stock:
                                    <?= $product['stock'] ?>)

                                </option>

                            <?php endwhile; ?>

                        <?php endif; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label for="quantity">
                        Quantity
                    </label>

                    <input type="number"
                           name="quantity"
                           id="quantity"
                           min="1"
                           value="<?= htmlspecialchars($row['quantity']) ?>"
                           required>

                </div>


                <div class="form-group">

                    <label for="note">
                        Note
                    </label>

                    <textarea name="note"
                              id="note"><?= htmlspecialchars($row['note']) ?></textarea>

                </div>


                <div class="form-actions">

                    <button type="submit"
                            class="admin-button">

                        <i class="fa-solid fa-save"></i>

                        Save Changes

                    </button>


                    <a href="stock_in.php"
                       class="cancel-button">

                        Cancel

                    </a>

                </div>


            </form>

        </div>

    </main>

</div>

</body>
</html>