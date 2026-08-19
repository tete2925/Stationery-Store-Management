<?php

require_once "admin_auth.php";

require_permission("orders");


// =========================================================
// DELETE DELIVERY
// =========================================================

if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    $stmt = $conn->prepare("
        DELETE FROM delivery
        WHERE id = ?
    ");

    if (!$stmt) {
        die("Delete prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $stmt->close();

    header("Location: delinfo.php");
    exit;
}


// =========================================================
// MARK DELIVERY AS DONE
// =========================================================

if (isset($_GET['done'])) {

    $id = (int)$_GET['done'];

    $stmt = $conn->prepare("
        UPDATE delivery
        SET status = 'Done'
        WHERE id = ?
    ");

    if (!$stmt) {
        die("Done prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $stmt->close();

    header("Location: delinfo.php");
    exit;
}


// =========================================================
// GET DELIVERY INFORMATION
// ONLY DELIVERY - NO PICK UP
// =========================================================

$result = $conn->query("
    SELECT
        id,
        order_id,
        customer_name,
        fulfillment_type,
        region,
        township,
        delivery_address,
        shipping_method,
        delivery_fee,
        total_amount,
        note,
        status,
        created_at

    FROM delivery

    WHERE fulfillment_type = 'Delivery'

    ORDER BY id DESC
");

?>

<!DOCTYPE html>
<html>

<head>

    <title>Delivery Info</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="adm.css"
    >

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
                    Delivery Info
                </h1>

            </div>

        </div>


        <div class="dashboard-panel">

            <div class="admin-table-wrapper">

                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Order</th>

                            <th>Customer</th>

                            <th>Region</th>

                            <th>Township</th>

                            <th>Delivery Address</th>

                            <th>Shipping Method</th>

                            <th>Delivery Fee</th>

                            <th>Total Amount</th>

                            <th>Note</th>

                            <th>Status</th>

                            <th>Date</th>

                            <th>Action</th>

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
                                    #<?= (int)$row['order_id'] ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row['customer_name']
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row['region']
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row['township']
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row['delivery_address']
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row['shipping_method']
                                    ) ?>
                                </td>


                                <td>
                                    <?= number_format(
                                        (float)$row['delivery_fee'],
                                        2
                                    ) ?>
                                    MMK
                                </td>


                                <td>
                                    <?= number_format(
                                        (float)$row['total_amount'],
                                        2
                                    ) ?>
                                    MMK
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row['note']
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row['status']
                                    ) ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $row['created_at']
                                    ) ?>
                                </td>


                                <td>

                                    <div class="table-actions">


                                        <?php if ($row['status'] !== 'Done'): ?>

                                            <a
                                                href="delinfo.php?done=<?= (int)$row['id'] ?>"
                                                class="edit-button"
                                                onclick="return confirm('Mark this delivery as done?');"
                                            >
                                                Done
                                            </a>

                                        <?php endif; ?>


                                        <a
                                            href="delinfo.php?delete=<?= (int)$row['id'] ?>"
                                            class="delete-button"
                                            onclick="return confirm('Delete this delivery information?');"
                                        >
                                            Delete
                                        </a>


                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="13"
                                style="text-align:center;"
                            >

                                No delivery information yet.

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