<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "includes/db.php";


// =========================================================
// CREATE CART
// =========================================================

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


// =========================================================
// ADD TO CART
// =========================================================

if (isset($_POST['action']) && $_POST['action'] === 'add') {

    $product_id = (int)($_POST['product_id'] ?? 0);

    if ($product_id > 0) {

        $stmt = $conn->prepare("
            SELECT id, name, price, stock, image
            FROM products
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->bind_param("i", $product_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        $stmt->close();


        if ($product) {

            if (isset($_SESSION['cart'][$product_id])) {

                $_SESSION['cart'][$product_id]['quantity']++;

            } else {

                $_SESSION['cart'][$product_id] = [
                    'id'       => $product['id'],
                    'name'     => $product['name'],
                    'price'    => $product['price'],
                    'image'    => $product['image'],
                    'quantity' => 1
                ];
            }
        }
    }

    header("Location: cart.php");
    exit();
}


// =========================================================
// INCREASE
// =========================================================

if (isset($_GET['increase'])) {

    $product_id = (int)$_GET['increase'];

    if (isset($_SESSION['cart'][$product_id])) {

        $_SESSION['cart'][$product_id]['quantity']++;
    }

    header("Location: cart.php");
    exit();
}


// =========================================================
// DECREASE
// =========================================================

if (isset($_GET['decrease'])) {

    $product_id = (int)$_GET['decrease'];

    if (isset($_SESSION['cart'][$product_id])) {

        $_SESSION['cart'][$product_id]['quantity']--;

        if ($_SESSION['cart'][$product_id]['quantity'] <= 0) {

            unset($_SESSION['cart'][$product_id]);
        }
    }

    header("Location: cart.php");
    exit();
}


// =========================================================
// REMOVE
// =========================================================

if (isset($_GET['remove'])) {

    $product_id = (int)$_GET['remove'];

    unset($_SESSION['cart'][$product_id]);

    header("Location: cart.php");
    exit();
}


// =========================================================
// CLEAR CART
// =========================================================

if (isset($_GET['clear'])) {

    $_SESSION['cart'] = [];

    header("Location: cart.php");
    exit();
}


// =========================================================
// TOTAL
// =========================================================

$cart_total = 0;
$cart_count = 0;

foreach ($_SESSION['cart'] as $item) {

    $cart_total +=
        $item['price'] * $item['quantity'];

    $cart_count +=
        $item['quantity'];
}


include "includes/header.php";

?>


<section class="cart-page">

    <div class="container">

        <h1>
            Shopping Cart
        </h1>


        <?php if (empty($_SESSION['cart'])): ?>

            <div class="empty-cart">

                <i
                    class="fa-solid fa-cart-shopping"
                    style="
                        font-size:50px;
                        margin-bottom:20px;
                    "
                ></i>

                <h2>
                    Your cart is empty
                </h2>

                <p>
                    Add some products to your cart to get started.
                </p>

                <a href="products.php">
                    Continue Shopping
                </a>

            </div>


        <?php else: ?>


            <div class="cart-container">


                <!-- CART ITEMS -->

                <div class="cart-items">

                    <?php foreach ($_SESSION['cart'] as $item): ?>

                        <div class="cart-item">


                            <!-- IMAGE -->

                            <?php if (!empty($item['image'])): ?>

                                <img
                                    src="images/<?php
                                        echo htmlspecialchars(
                                            $item['image']
                                        );
                                    ?>"
                                    alt="<?php
                                        echo htmlspecialchars(
                                            $item['name']
                                        );
                                    ?>"
                                >

                            <?php else: ?>

                                <div
                                    style="
                                        width:110px;
                                        height:110px;
                                        display:flex;
                                        align-items:center;
                                        justify-content:center;
                                        background:#f5f5f5;
                                        font-size:35px;
                                    "
                                >
                                    <i class="fa-solid fa-box"></i>
                                </div>

                            <?php endif; ?>


                            <!-- INFO -->

                            <div class="item-info">

                                <h3>
                                    <?php
                                    echo htmlspecialchars(
                                        $item['name']
                                    );
                                    ?>
                                </h3>


                                <p class="price">

                                    <?php
                                    echo number_format(
                                        $item['price']
                                    );
                                    ?>

                                    MMK

                                </p>


                                <!-- QUANTITY -->

                                <div
                                    style="
                                        display:flex;
                                        align-items:center;
                                        gap:12px;
                                        margin-top:15px;
                                    "
                                >


                                    <!-- MINUS -->

                                    <a
                                        href="cart.php?decrease=<?php
                                            echo (int)$item['id'];
                                        ?>"
                                        style="
                                            width:42px;
                                            height:42px;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            background:#eeeeee;
                                            color:#222;
                                            text-decoration:none;
                                            border-radius:6px;
                                            font-size:24px;
                                            font-weight:bold;
                                        "
                                    >
                                        −
                                    </a>


                                    <!-- QUANTITY -->

                                    <span
                                        style="
                                            min-width:40px;
                                            text-align:center;
                                            font-size:18px;
                                            font-weight:bold;
                                        "
                                    >
                                        <?php
                                        echo (int)$item['quantity'];
                                        ?>
                                    </span>


                                    <!-- PLUS -->

                                    <a
                                        href="cart.php?increase=<?php
                                            echo (int)$item['id'];
                                        ?>"
                                        style="
                                            width:42px;
                                            height:42px;
                                            display:flex;
                                            align-items:center;
                                            justify-content:center;
                                            background:#d71920;
                                            color:#fff;
                                            text-decoration:none;
                                            border-radius:6px;
                                            font-size:24px;
                                            font-weight:bold;
                                            cursor:pointer;
                                        "
                                    >
                                        +
                                    </a>


                                </div>

                            </div>


                            <!-- TOTAL -->

                            <div class="item-total">

                                <strong>

                                    <?php
                                    echo number_format(
                                        $item['price'] *
                                        $item['quantity']
                                    );
                                    ?>

                                    MMK

                                </strong>


                                <a
                                    href="cart.php?remove=<?php
                                        echo (int)$item['id'];
                                    ?>"
                                    class="remove-btn"
                                >
                                    Remove
                                </a>

                            </div>


                        </div>

                    <?php endforeach; ?>


                    <!-- CLEAR -->

                    <a
                        href="cart.php?clear=1"
                        class="clear-btn"
                        onclick="
                            return confirm(
                                'Clear your entire cart?'
                            );
                        "
                    >
                        Clear Cart
                    </a>

                </div>

<!-- SUMMARY -->

<div class="checkout-box">

    <h2>
        Cart Summary
    </h2>


    <div class="summary-row">

        <span>
            Items
        </span>

        <strong>
            <?php echo $cart_count; ?>
        </strong>

    </div>


    <div class="summary-row total">

        <span>
            Total
        </span>

        <strong>

            <?php
            echo number_format($cart_total);
            ?>

            MMK

        </strong>

    </div>


    <!-- CHECKOUT -->

   <div class="checkout-form">

    <h3>
        Checkout
    </h3>

    <a
        href="deli.php"
        class="checkout-btn"
        style="
            display:block;
            text-align:center;
            text-decoration:none;
        "
    >
        Checkout
    </a>

</div>

</div>


            </div>


        <?php endif; ?>

    </div>

</section>


<?php

include "includes/footer.php";

?>