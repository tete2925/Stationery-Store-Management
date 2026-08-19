<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "includes/db.php";

include "includes/header.php";

$order_id = $_SESSION['last_order_id'] ?? null;
$payment_method = $_SESSION['last_payment_method'] ?? 'Cash on Delivery (COD)';

$payment_method = 'Cash on Delivery (COD)';


// =========================================================
// GET PAYMENT METHOD FROM THE ORDER
// =========================================================

if ($order_id) {

    $stmt = $conn->prepare("
        SELECT payment_method, fulfillment_type
        FROM orders
        WHERE id = ?
        LIMIT 1
    ");

    if ($stmt) {

        $stmt->bind_param(
            "i",
            $order_id
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {

            if ($row['fulfillment_type'] === 'Pick Up') {

                $payment_method = 'Cash on Pickup';

            } else {

                $payment_method = 'Cash on Delivery (COD)';

            }

        }

        $stmt->close();
    }
}

?>

<link rel="stylesheet" href="/stationary/css/deli.css">

<section class="delivery-page">

    <div class="delivery-container">

        <div class="delivery-card">

            <h1>
                Purchase Confirmed
            </h1>

            <p class="delivery-description">
                Thank you for your purchase!
            </p>

            <p class="delivery-description">
                Your order has been successfully placed.
            </p>


            <?php if ($order_id): ?>

                <p class="delivery-description">
                    Order #<?= (int)$order_id ?>
                </p>

            <?php endif; ?>


            <!-- PAYMENT METHOD -->

            


            <div class="delivery-buttons">

                <a
                    href="index.php"
                    class="delivery-next"
                >
                    Home
                </a>

            </div>

        </div>

    </div>

</section>


<?php

unset($_SESSION['last_order_id']);

include "includes/footer.php";

?>