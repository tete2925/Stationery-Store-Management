<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "includes/db.php";


// =========================================================
// CHECK CART
// =========================================================

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}


// =========================================================
// CART TOTAL
// =========================================================

$cart_total = 0;

foreach ($_SESSION['cart'] as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}


// =========================================================
// DEFAULT STEP
// =========================================================

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

if ($step < 1 || $step > 3) {
    $step = 1;
}


// =========================================================
// KEEP DELIVERY INFORMATION IN SESSION
// =========================================================

if (!isset($_SESSION['delivery_info'])) {
    $_SESSION['delivery_info'] = [
        'fulfillment_type' => 'Delivery',
        'customer_name'    => '',
        'customer_phone'   => '',
        'region'           => '',
        'township'         => '',
        'delivery_address' => '',
        'shipping_method'  => '',
        'delivery_fee'     => 0,
        'note'             => ''
    ];
}


// =========================================================
// REGIONS AND TOWNSHIPS
// =========================================================

$regions = [

    'မန္တလေးတိုင်းဒေသကြီး' => [
        'မန္တလေး',
        'အမရပူရ',
        'ပြင်ဦးလွင်',
        'မိတ္ထီလာ',
        'ကျောက်ဆည်'
    ],

    'မွန်ပြည်နယ်' => [
        'မော်လမြိုင်',
        'သထုံ',
        'ကျိုက်ထို',
        'မုဒုံ'
    ],

    'ရခိုင်ပြည်နယ်' => [
        'စစ်တွေ',
        'သံတွဲ',
        'ကျောက်ဖြူ',
        'တောင်ကုတ်'
    ],

    'ရန်ကုန်တိုင်းဒေသကြီး' => [
        'ရန်ကုန်',
        'လှိုင်',
        'မရမ်းကုန်း',
        'အင်းစိန်',
        'သင်္ဃန်းကျွန်း',
        'တောင်ဥက္ကလာပ'
    ],

    'ရှမ်းပြည်နယ်' => [
        'တောင်ကြီး',
        'လားရှိုး',
        'ကျောက်မဲ',
        'ကလော',
        'ညောင်ရွှေ'
    ],

    'ဧရာဝတီတိုင်းဒေသကြီး' => [
        'ကျိုက်လတ်',
        'ကျုံပျော်',
        'ကျောင်းကုန်း',
        'ကြံခင်း',
        'ငပုတော',
        'မအူပင်',
        'ပုသိမ်',
        'ဟင်္သာတ',
        'မြောင်းမြ',
        'ဖျာပုံ',
        'လပွတ္တာ',
        'ဘိုကလေး',
        'ဇလွန်'
    ],

    'ပဲခူးတိုင်းဒေသကြီး' => [
        'ပဲခူး',
        'တောင်ငူ',
        'ပြည်',
        'သာယာဝတီ'
    ],

    'စစ်ကိုင်းတိုင်းဒေသကြီး' => [
        'မုံရွာ',
        'စစ်ကိုင်း',
        'ကလေး',
        'ရွှေဘို',
        'ကသာ'
    ],

    'ကချင်ပြည်နယ်' => [
        'မြစ်ကြီးနား',
        'ဗန်းမော်',
        'မိုးကောင်း',
        'မိုးညှင်း'
    ],

    'ကရင်ပြည်နယ်' => [
        'ဘားအံ',
        'မြဝတီ',
        'ကော့ကရိတ်',
        'ဖာပွန်'
    ],

    'ကယားပြည်နယ်' => [
        'လွိုင်ကော်',
        'ဒီမောဆို',
        'ဖရူဆို'
    ],

    'တနင်္သာရီတိုင်းဒေသကြီး' => [
        'ထားဝယ်',
        'မြိတ်',
        'ကော့သောင်း',
        'လောင်းလုံ'
    ],

    'မကွေးတိုင်းဒေသကြီး' => [
        'မကွေး',
        'ပခုက္ကူ',
        'မင်းဘူး',
        'ချောက်',
        'ရေနံချောင်း'
    ],

    'ချင်းပြည်နယ်' => [
        'ဟားခါး',
        'ဖလမ်း',
        'မင်းတပ်',
        'မတူပီ'
    ],

    'နေပြည်တော်' => [
        'ပျဉ်းမနား',
        'လယ်ဝေး',
        'ဇမ္ဗူသီရိ',
        'ဥတ္တရသီရိ'
    ]
];


// =========================================================
// SHIPPING METHODS
// =========================================================

$shipping_methods = [
    'ကားဂိတ်ချ' => 2000,
    'နေပြည်တော်' => 4500,
    'မန္တလေး' => 5000,
    'ရန်ကုန်' => 4000,
    'အခြားမြို့များ' => 0
];


// =========================================================
// STEP 1
// =========================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'step1'
) {

    $fulfillment_type = $_POST['fulfillment_type'] ?? 'Delivery';

    if (!in_array($fulfillment_type, ['Delivery', 'Pick Up'])) {
        $fulfillment_type = 'Delivery';
    }

    $_SESSION['delivery_info']['fulfillment_type'] = $fulfillment_type;

    header("Location: deli.php?step=2");
    exit();
}


// =========================================================
// STEP 2
// =========================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'step2'
) {

    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');

    $region = trim($_POST['region'] ?? '');
    $township = trim($_POST['township'] ?? '');
    $delivery_address = trim($_POST['delivery_address'] ?? '');
    $shipping_method = trim($_POST['shipping_method'] ?? '');
    $note = trim($_POST['note'] ?? '');

    $fulfillment_type =
        $_SESSION['delivery_info']['fulfillment_type'] ?? 'Delivery';


    // -----------------------------------------------------
    // BASIC VALIDATION
    // -----------------------------------------------------

    if ($customer_name === '') {
        die("Please enter your name.");
    }

    if ($customer_phone === '') {
        die("Please enter your phone number.");
    }


    // -----------------------------------------------------
    // PICK UP
    // -----------------------------------------------------

    if ($fulfillment_type === 'Pick Up') {

        $region = '';
        $township = '';
        $delivery_address = '';
        $shipping_method = '';
        $delivery_fee = 0;

    } else {

        if ($region === '') {
            die("Please select a region.");
        }

        if (!isset($regions[$region])) {
            die("Invalid region.");
        }

        if ($township === '') {
            die("Please select a township.");
        }

        if (!in_array($township, $regions[$region])) {
            die("Invalid township.");
        }

        if ($delivery_address === '') {
            die("Please enter your home address.");
        }

        if (!isset($shipping_methods[$shipping_method])) {
            die("Please select a shipping method.");
        }

        $delivery_fee = $shipping_methods[$shipping_method];
    }


    // -----------------------------------------------------
    // SAVE TO SESSION
    // -----------------------------------------------------

    $_SESSION['delivery_info'] = [
        'fulfillment_type' => $fulfillment_type,
        'customer_name'    => $customer_name,
        'customer_phone'   => $customer_phone,
        'region'           => $region,
        'township'         => $township,
        'delivery_address' => $delivery_address,
        'shipping_method'  => $shipping_method,
        'delivery_fee'     => $delivery_fee,
        'note'             => $note
    ];

    header("Location: deli.php?step=3");
    exit();
}


// =========================================================
// STEP 3 - PLACE ORDER
// =========================================================

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['action']) &&
    $_POST['action'] === 'place_order'
) {

    $info = $_SESSION['delivery_info'];


    $customer_name =
        $info['customer_name'];

    $customer_phone =
        $info['customer_phone'];

    $fulfillment_type =
        $info['fulfillment_type'];

    $delivery_fee =
        (float)$info['delivery_fee'];


    // -----------------------------------------------------
    // PAYMENT METHOD
    // -----------------------------------------------------

    if ($fulfillment_type === 'Pick Up') {

        $payment_method = 'Cash on Pickup';

    } else {

        $payment_method = 'Cash on Delivery';

    }


    // -----------------------------------------------------
    // FINAL TOTAL
    // -----------------------------------------------------

    $final_total =
        $cart_total + $delivery_fee;


    // -----------------------------------------------------
    // CUSTOMER ADDRESS
    // -----------------------------------------------------

    $customer_address =
        $info['delivery_address'];


    // -----------------------------------------------------
    // CREATE ORDER
    // -----------------------------------------------------

    $stmt = $conn->prepare("
        INSERT INTO orders
        (
            customer_name,
            customer_phone,
            customer_address,
            total_amount,
            status,
            fulfillment_type,
            payment_method
        )
        VALUES (?, ?, ?, ?, 'Pending', ?, ?)
    ");

    if (!$stmt) {
        die("Failed to prepare order: " . $conn->error);
    }


    $stmt->bind_param(
        "sssdss",
        $customer_name,
        $customer_phone,
        $customer_address,
        $final_total,
        $fulfillment_type,
        $payment_method
    );


    if (!$stmt->execute()) {
        die("Failed to create order: " . $stmt->error);
    }


    $order_id = $stmt->insert_id;

    $stmt->close();


    // =====================================================
    // CREATE ORDER ITEMS
    // =====================================================

    foreach ($_SESSION['cart'] as $item) {

        $product_id =
            (int)$item['id'];

        $quantity =
            (int)$item['quantity'];

        $price =
            (float)$item['price'];


        $item_stmt = $conn->prepare("
            INSERT INTO order_items
            (
                order_id,
                product_id,
                quantity,
                price
            )
            VALUES (?, ?, ?, ?)
        ");


        if (!$item_stmt) {
            die("Failed to prepare order item: " . $conn->error);
        }


        $item_stmt->bind_param(
            "iiid",
            $order_id,
            $product_id,
            $quantity,
            $price
        );


        if (!$item_stmt->execute()) {
            die("Failed to save order item: " . $item_stmt->error);
        }


        $item_stmt->close();
    }


    // =====================================================
    // CREATE DELIVERY INFORMATION
    // =====================================================

    $delivery_stmt = $conn->prepare("
        INSERT INTO delivery
        (
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
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')
    ");


    if (!$delivery_stmt) {
        die("Failed to prepare delivery information: " . $conn->error);
    }


    $delivery_stmt->bind_param(
        "issssssdds",
        $order_id,
        $info['customer_name'],
        $info['fulfillment_type'],
        $info['region'],
        $info['township'],
        $info['delivery_address'],
        $info['shipping_method'],
        $info['delivery_fee'],
        $final_total,
        $info['note']
    );


    if (!$delivery_stmt->execute()) {
        die(
            "Failed to save delivery information: " .
            $delivery_stmt->error
        );
    }


    $delivery_stmt->close();


    // =====================================================
    // SAVE LAST ORDER ID
    // =====================================================

    $_SESSION['last_order_id'] = $order_id;


    // =====================================================
    // CLEAR CART
    // =====================================================

    $_SESSION['cart'] = [];

    unset($_SESSION['delivery_info']);


    // =====================================================
    // SUCCESS PAGE
    // =====================================================

    header("Location: purchase_success.php");

    exit();
}


// =========================================================
// CURRENT DATA
// =========================================================

$info = $_SESSION['delivery_info'];

$current_fulfillment =
    $info['fulfillment_type'];


// =========================================================
// PAYMENT DISPLAY FOR STEP 3
// =========================================================

if ($current_fulfillment === 'Pick Up') {

    $display_payment_method = 'Cash on Pickup';

} else {

    $display_payment_method = 'Cash on Delivery (COD)';

}


// =========================================================
// HEADER
// =========================================================

include "includes/header.php";

?>

<link rel="stylesheet" href="/stationary/css/deli.css">


<section class="delivery-page">

<div class="delivery-container">


    <!-- =================================================
         PROGRESS
         ================================================= -->

    <div class="delivery-progress">

        <div class="progress-step
            <?php echo ($step >= 1) ? 'active' : ''; ?>">

            <div class="progress-circle"></div>

        </div>


        <div class="progress-line
            <?php echo ($step >= 2) ? 'completed-line' : ''; ?>">
        </div>


        <div class="progress-step
            <?php echo ($step >= 2) ? 'active' : ''; ?>">

            <div class="progress-circle"></div>

        </div>


        <div class="progress-line
            <?php echo ($step >= 3) ? 'completed-line' : ''; ?>">
        </div>


        <div class="progress-step
            <?php echo ($step >= 3) ? 'active' : ''; ?>">

            <div class="progress-circle"></div>

        </div>

    </div>


    <!-- =================================================
         STEP 1
         ================================================= -->

    <?php if ($step === 1): ?>

    <div class="delivery-card">

        <h1>
            Delivery Method
        </h1>

        <p class="delivery-description">
            Choose how you would like to receive your order.
        </p>


        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="step1"
            >


            <div class="fulfillment-options">


                <!-- DELIVERY -->

                <label class="fulfillment-card">

                    <input
                        type="radio"
                        name="fulfillment_type"
                        value="Delivery"
                        <?php
                        echo
                        ($current_fulfillment === 'Delivery')
                        ? 'checked'
                        : '';
                        ?>
                    >

                    <div>

                        <h2>
                            Delivery
                        </h2>

                        <p>
                            Your order will be delivered to your address.
                        </p>

                    </div>

                </label>


                <!-- PICK UP -->

                <label class="fulfillment-card">

                    <input
                        type="radio"
                        name="fulfillment_type"
                        value="Pick Up"
                        <?php
                        echo
                        ($current_fulfillment === 'Pick Up')
                        ? 'checked'
                        : '';
                        ?>
                    >

                    <div>

                        <h2>
                            Pick Up
                        </h2>

                        <p>
                            You can pick up your order from the shop.
                        </p>

                    </div>

                </label>


            </div>


            <div class="delivery-buttons">

                <a
                    href="cart.php"
                    class="delivery-back"
                >
                    Back
                </a>


                <button
                    type="submit"
                    class="delivery-next"
                >
                    Continue
                </button>

            </div>


        </form>

    </div>

    <?php endif; ?>


    <!-- =================================================
         STEP 2
         ================================================= -->

    <?php if ($step === 2): ?>

    <div class="delivery-card">

        <h1>
            Delivery Information
        </h1>

        <p class="delivery-description">
            Please enter your delivery information.
        </p>


        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="step2"
            >


            <!-- NAME -->

            <div class="form-group">

                <label>
                    Name
                </label>

                <input
                    type="text"
                    name="customer_name"
                    value="<?php
                        echo htmlspecialchars(
                            $info['customer_name']
                        );
                    ?>"
                    required
                >

            </div>


            <!-- PHONE -->

            <div class="form-group">

                <label>
                    Phone Number
                </label>

                <input
                    type="tel"
                    name="customer_phone"
                    value="<?php
                        echo htmlspecialchars(
                            $info['customer_phone']
                        );
                    ?>"
                    placeholder="09xxxxxxxxx"
                    inputmode="numeric"
                    maxlength="11"
                    required
                    pattern="09[0-9]{9}"
                    oninput="
                        this.value =
                        this.value
                        .replace(/[^0-9]/g, '')
                        .slice(0, 11);
                    "
                >

            </div>


            <?php if ($current_fulfillment === 'Delivery'): ?>


                <!-- REGION -->

                <div class="form-group">

                    <label>
                        State / Region
                    </label>

                    <select
                        name="region"
                        id="region"
                        required
                    >

                        <option value="">
                            Select State / Region
                        </option>

                        <?php foreach ($regions as $region_name => $townships): ?>

                            <option
                                value="<?php
                                    echo htmlspecialchars(
                                        $region_name
                                    );
                                ?>"
                                <?php
                                echo
                                ($info['region'] === $region_name)
                                ? 'selected'
                                : '';
                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $region_name
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- TOWNSHIP -->

                <div class="form-group">

                    <label>
                        Township
                    </label>

                    <select
                        name="township"
                        id="township"
                        required
                    >

                        <option value="">
                            Select Township
                        </option>

                    </select>

                </div>


                <!-- ADDRESS -->

                <div class="form-group">

                    <label>
                        Home Address
                    </label>

                    <textarea
                        name="delivery_address"
                        rows="4"
                        placeholder="Enter your full home address"
                        required
                    ><?php
                        echo htmlspecialchars(
                            $info['delivery_address']
                        );
                    ?></textarea>

                </div>


                <!-- SHIPPING -->

                <div class="form-group">

                    <label>
                        Shipping Fee
                    </label>


                    <div class="shipping-options">

                        <?php foreach ($shipping_methods as $method => $fee): ?>

                            <label class="shipping-option">

                                <input
                                    type="radio"
                                    name="shipping_method"
                                    value="<?php
                                        echo htmlspecialchars(
                                            $method
                                        );
                                    ?>"
                                    <?php
                                    echo
                                    ($info['shipping_method'] === $method)
                                    ? 'checked'
                                    : '';
                                    ?>
                                    required
                                >

                                <span>

                                    <?php
                                    echo htmlspecialchars(
                                        $method
                                    );
                                    ?>

                                    —

                                    <?php
                                    echo number_format($fee);
                                    ?>

                                    Ks

                                </span>

                            </label>

                        <?php endforeach; ?>

                    </div>

                </div>


                <!-- NOTE -->

                <div class="form-group">

                    <label>
                        Note
                    </label>

                    <textarea
                        name="note"
                        rows="3"
                        placeholder="Enter any special delivery instructions"
                    ><?php
                        echo htmlspecialchars(
                            $info['note']
                        );
                    ?></textarea>

                </div>


            <?php else: ?>


                <!-- PICK UP -->

                <input
                    type="hidden"
                    name="region"
                    value=""
                >

                <input
                    type="hidden"
                    name="township"
                    value=""
                >

                <input
                    type="hidden"
                    name="delivery_address"
                    value=""
                >

                <input
                    type="hidden"
                    name="shipping_method"
                    value=""
                >


                <div class="pickup-message">

                    <h3>
                        Pick Up
                    </h3>

                    <p>
                        You will pick up your order from the shop,
                        so no delivery information is required.
                    </p>

                </div>


                <!-- NOTE -->

                <div class="form-group">

                    <label>
                        Note
                    </label>

                    <textarea
                        name="note"
                        rows="3"
                        placeholder="Enter a note if needed"
                    ><?php
                        echo htmlspecialchars(
                            $info['note']
                        );
                    ?></textarea>

                </div>


            <?php endif; ?>


            <div class="delivery-buttons">

                <a
                    href="deli.php?step=1"
                    class="delivery-back"
                >
                    Back
                </a>


                <button
                    type="submit"
                    class="delivery-next"
                >
                    Continue
                </button>

            </div>


        </form>

    </div>

    <?php endif; ?>


    <!-- =================================================
         STEP 3
         ================================================= -->

    <?php if ($step === 3): ?>

    <div class="delivery-card">

        <h1>
            Confirm Order
        </h1>

        <p class="delivery-description">
            Please review your order information before placing the order.
        </p>


        <div class="confirmation-box">


            <!-- FULFILLMENT -->

            <div class="confirmation-row">

                <span>
                    Fulfillment Method
                </span>

                <strong>

                    <?php
                    if ($info['fulfillment_type'] === 'Pick Up') {
                        echo 'Pick Up';
                    } else {
                        echo 'Delivery';
                    }
                    ?>

                </strong>

            </div>


            <!-- NAME -->

            <div class="confirmation-row">

                <span>
                    Name
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $info['customer_name']
                    );
                    ?>

                </strong>

            </div>


            <!-- PHONE -->

            <div class="confirmation-row">

                <span>
                    Phone
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $info['customer_phone']
                    );
                    ?>

                </strong>

            </div>


            <?php if ($info['fulfillment_type'] === 'Delivery'): ?>


                <!-- REGION -->

                <div class="confirmation-row">

                    <span>
                        State / Region
                    </span>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $info['region']
                        );
                        ?>

                    </strong>

                </div>


                <!-- TOWNSHIP -->

                <div class="confirmation-row">

                    <span>
                        Township
                    </span>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $info['township']
                        );
                        ?>

                    </strong>

                </div>


                <!-- ADDRESS -->

                <div class="confirmation-row">

                    <span>
                        Home Address
                    </span>

                    <strong>

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $info['delivery_address']
                            )
                        );
                        ?>

                    </strong>

                </div>


                <!-- SHIPPING -->

                <div class="confirmation-row">

                    <span>
                        Shipping Fee
                    </span>

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $info['shipping_method']
                        );
                        ?>

                        —

                        <?php
                        echo number_format(
                            $info['delivery_fee']
                        );
                        ?>

                        Ks

                    </strong>

                </div>


            <?php endif; ?>


            <!-- =================================================
                 PAYMENT METHOD
                 ================================================= -->

            <div class="confirmation-row">

                <span>
                    Payment Method
                </span>

                <strong>
    <?php echo htmlspecialchars($display_payment_method); ?>
</strong>

            </div>


            <!-- NOTE -->

            <?php if (!empty($info['note'])): ?>

                <div class="confirmation-row">

                    <span>
                        Note
                    </span>

                    <strong>

                        <?php
                        echo nl2br(
                            htmlspecialchars(
                                $info['note']
                            )
                        );
                        ?>

                    </strong>

                </div>

            <?php endif; ?>


            <!-- PRODUCTS SUBTOTAL -->

            <div class="confirmation-row">

                <span>
                    Products Subtotal
                </span>

                <strong>

                    <?php
                    echo number_format(
                        $cart_total
                    );
                    ?>

                    Ks

                </strong>

            </div>


            <!-- DELIVERY FEE -->

            <div class="confirmation-row">

                <span>
                    Delivery Fee
                </span>

                <strong>

                    <?php
                    echo number_format(
                        $info['delivery_fee']
                    );
                    ?>

                    Ks

                </strong>

            </div>


            <!-- TOTAL -->

            <div class="confirmation-total">

                <span>
                    Total
                </span>

                <strong>

                    <?php
                    echo number_format(
                        $cart_total +
                        $info['delivery_fee']
                    );
                    ?>

                    Ks

                </strong>

            </div>


        </div>


        <form method="POST">

            <input
                type="hidden"
                name="action"
                value="place_order"
            >


            <div class="delivery-buttons">

                <a
                    href="deli.php?step=2"
                    class="delivery-back"
                >
                    Back
                </a>


                <button
                    type="submit"
                    class="place-order-btn"
                >
                    Place Order
                </button>

            </div>

        </form>


    </div>

    <?php endif; ?>


</div>

</section>


<script>

// =========================================================
// REGION → TOWNSHIP
// =========================================================

const regionData = <?php
    echo json_encode(
        $regions,
        JSON_UNESCAPED_UNICODE
    );
?>;


const regionSelect =
    document.getElementById('region');

const townshipSelect =
    document.getElementById('township');


const savedRegion = <?php
    echo json_encode(
        $info['region'],
        JSON_UNESCAPED_UNICODE
    );
?>;


const savedTownship = <?php
    echo json_encode(
        $info['township'],
        JSON_UNESCAPED_UNICODE
    );
?>;


function updateTownships() {

    if (!regionSelect || !townshipSelect) {
        return;
    }


    const selectedRegion =
        regionSelect.value;


    townshipSelect.innerHTML =
        '<option value="">Select Township</option>';


    if (
        selectedRegion &&
        regionData[selectedRegion]
    ) {

        regionData[selectedRegion].forEach(
            function(township) {

                const option =
                    document.createElement('option');

                option.value =
                    township;

                option.textContent =
                    township;


                if (
                    selectedRegion === savedRegion &&
                    township === savedTownship
                ) {

                    option.selected = true;

                }


                townshipSelect.appendChild(
                    option
                );

            }
        );

    }

}


if (regionSelect) {

    regionSelect.addEventListener(
        'change',
        function() {

            townshipSelect.innerHTML =
                '<option value="">Select Township</option>';


            const selectedRegion =
                regionSelect.value;


            if (
                selectedRegion &&
                regionData[selectedRegion]
            ) {

                regionData[selectedRegion].forEach(
                    function(township) {

                        const option =
                            document.createElement('option');

                        option.value =
                            township;

                        option.textContent =
                            township;

                        townshipSelect.appendChild(
                            option
                        );

                    }
                );

            }

        }
    );


    updateTownships();

}

</script>


<?php

include "includes/footer.php";

?>