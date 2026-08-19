<?php

require_once "admin_auth.php";

require_permission("orders");

$result = $conn->query("
    SELECT
        order_items.id,
        products.name AS product_name,
        order_items.quantity,
        order_items.price,
        (order_items.quantity * order_items.price) AS subtotal
    FROM order_items

    INNER JOIN products
        ON order_items.product_id = products.id

    ORDER BY order_items.id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

    <title>Order Items</title>

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
                    BUSINESS
                </p>

                <h1>
                    Order Items
                </h1>

            </div>

        </div>


        <div class="dashboard-panel">

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Product</th>

                            <th>Quantity Sold</th>

                            <th>Unit Price</th>

                            <th>Subtotal</th>

                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($result && $result->num_rows > 0): ?>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    <?= (int)$row['id'] ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($row['product_name']) ?>
                                </td>


                                <td>
                                    <?= (int)$row['quantity'] ?>
                                </td>


                                <td>
                                    <?= number_format($row['price'], 2) ?> MMK
                                </td>


                                <td>
                                    <?= number_format($row['subtotal'], 2) ?> MMK
                                </td>


                                <td>

                                    <div class="table-actions">

                                        <a
                                            href="order_item_delete.php?id=<?= (int)$row['id'] ?>"
                                            class="delete-button"
                                            onclick="return confirm('Delete this order item?');"
                                        >
                                            Delete
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="6"
                                style="text-align:center;">

                                No order items yet.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>


    </main>

</div>

</body>

</html>