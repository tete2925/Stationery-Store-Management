<?php

require_once "admin_auth.php";

require_permission("orders");


/* =========================================================
   DELETE ORDER
========================================================= */

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    /*
     * Delete related delivery information first.
     */
    $stmt = $conn->prepare("
        DELETE FROM delivery
        WHERE order_id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();


    /*
     * Delete related order items.
     */
    $stmt = $conn->prepare("
        DELETE FROM order_items
        WHERE order_id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();


    /*
     * Delete the order itself.
     */
    $stmt = $conn->prepare("
        DELETE FROM orders
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();


    header("Location: order.php");
    exit;
}


$result = $conn->query("
    SELECT
        orders.id,
        orders.customer_name,
        orders.customer_phone,
        orders.customer_address,
        orders.total_amount,
        orders.status,
        orders.fulfillment_type,
        orders.payment_method,
        orders.created_at,

        GROUP_CONCAT(
            products.name
            SEPARATOR '<br>'
        ) AS products_bought,

        SUM(order_items.quantity) AS total_quantity

    FROM orders

    LEFT JOIN order_items
        ON orders.id = order_items.order_id

    LEFT JOIN products
        ON order_items.product_id = products.id

    GROUP BY
        orders.id,
        orders.customer_name,
        orders.customer_phone,
        orders.customer_address,
        orders.total_amount,
        orders.status,
        orders.fulfillment_type,
        orders.payment_method,
        orders.created_at

    ORDER BY orders.id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

    <title>Orders</title>

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
                    Orders
                </h1>

            </div>

        </div>


        <div class="dashboard-panel">

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Customer</th>

                            <th>Product</th>

                            <th>Quantity</th>

                            <th>Phone</th>

                            <th>Address</th>

                            <th>Total</th>

                            <th>Status</th>

                            <th>Fulfillment</th>

                            <th>Payment</th>

                            <th>Date</th>

                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($result && $result->num_rows > 0): ?>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <tr>

                                <td>
                                    #<?= (int)$row['id'] ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($row['customer_name']) ?>
                                </td>


                                <td>
                                    <?= $row['products_bought'] ?? 'No items' ?>
                                </td>


                                <td>
                                    <?= (int)($row['total_quantity'] ?? 0) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($row['customer_phone']) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($row['customer_address']) ?>
                                </td>


                                <td>
                                    <?= number_format($row['total_amount'], 2) ?> MMK
                                </td>


                                <td>
                                    <?= htmlspecialchars($row['status']) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($row['fulfillment_type']) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($row['payment_method']) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars($row['created_at']) ?>
                                </td>


                                <td>

                                    <div class="table-actions">

                                        <a
                                            href="order.php?delete=<?= (int)$row['id'] ?>"
                                            class="delete-button"
                                            onclick="return confirm('Delete this order and all related order information?');"
                                        >
                                            Delete
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="12"
                                style="text-align:center;">

                                No orders yet.

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