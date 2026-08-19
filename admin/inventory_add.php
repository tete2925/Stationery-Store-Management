<?php

require_once "admin_auth.php";

require_permission("inventory");

$products = $conn->query("
    SELECT id, name
    FROM products
    ORDER BY name
");

$error = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id = intval($_POST['product_id'] ?? 0);
    $type = $_POST['type'] ?? '';
    $quantity = intval($_POST['quantity'] ?? 0);
    $note = trim($_POST['note'] ?? '');


    if ($product_id <= 0 || $quantity <= 0) {

        $error = "Please enter valid information.";

    } elseif (!in_array($type, ['IN', 'OUT'], true)) {

        $error = "Invalid movement type.";

    } else {

        $conn->begin_transaction();

        try {

            /*
            |--------------------------------------------------------------
            | Stock IN
            |--------------------------------------------------------------
            */

            if ($type === 'IN') {

                $stmt = $conn->prepare("
                    INSERT INTO inventory
                    (product_id, type, quantity, note)
                    VALUES (?, 'IN', ?, ?)
                ");

                $stmt->bind_param(
                    "iis",
                    $product_id,
                    $quantity,
                    $note
                );

                if (!$stmt->execute()) {
                    throw new Exception("Could not add inventory record.");
                }


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

                if (!$stmt->execute()) {
                    throw new Exception("Could not update product stock.");
                }

            }


            /*
            |--------------------------------------------------------------
            | Stock OUT
            |--------------------------------------------------------------
            */

            else {

                $stmt = $conn->prepare("
                    UPDATE products
                    SET stock = stock - ?
                    WHERE id = ?
                    AND stock >= ?
                ");

                $stmt->bind_param(
                    "iii",
                    $quantity,
                    $product_id,
                    $quantity
                );

                $stmt->execute();

                if ($stmt->affected_rows === 0) {

                    throw new Exception("Not enough stock.");

                }


                $stmt = $conn->prepare("
                    INSERT INTO inventory
                    (product_id, type, quantity, note)
                    VALUES (?, 'OUT', ?, ?)
                ");

                $stmt->bind_param(
                    "iis",
                    $product_id,
                    $quantity,
                    $note
                );

                if (!$stmt->execute()) {
                    throw new Exception("Could not add inventory record.");
                }
            }


            $conn->commit();

            header("Location: inventory.php");
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

<title>Add Inventory Movement</title>

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

<p class="dashboard-label">INVENTORY</p>

<h1>Add Movement</h1>

</div>

</div>


<div class="dashboard-panel">

<div class="admin-form">

<?php if ($error !== ""): ?>

<div class="form-error">

<?= htmlspecialchars($error) ?>

</div>

<?php endif; ?>


<form method="POST">


<div class="form-group">

<label>Product</label>

<select name="product_id" required>

<option value="">Select Product</option>

<?php if ($products): ?>

<?php while ($product = $products->fetch_assoc()): ?>

<option value="<?= $product['id'] ?>">

<?= htmlspecialchars($product['name']) ?>

</option>

<?php endwhile; ?>

<?php endif; ?>

</select>

</div>


<div class="form-group">

<label>Movement Type</label>

<select name="type" required>

<option value="">Select Type</option>

<option value="IN">Stock In</option>

<option value="OUT">Stock Out</option>

</select>

</div>


<div class="form-group">

<label>Quantity</label>

<input type="number"
       name="quantity"
       min="1"
       required>

</div>


<div class="form-group">

<label>Note</label>

<textarea name="note"
          rows="4"></textarea>

</div>


<div class="form-actions">

<button type="submit"
        class="admin-button">

Save Movement

</button>

<a href="inventory.php"
   class="cancel-button">

Cancel

</a>

</div>


</form>

</div>

</div>

</main>

</div>

</body>

</html>