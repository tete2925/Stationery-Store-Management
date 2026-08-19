<?php

require_once "admin_auth.php";

require_permission("inventory");

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT *
    FROM inventory
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {

    header("Location: inventory.php");
    exit();

}

$products = $conn->query("
    SELECT id, name
    FROM products
    ORDER BY name
");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $product_id = intval($_POST['product_id']);
    $type = $_POST['type'];
    $quantity = intval($_POST['quantity']);
    $note = trim($_POST['note']);

    if ($quantity <= 0) {

        $error = "Quantity must be greater than zero.";

    } else {

        $conn->begin_transaction();

        try {

            /*
             * Reverse old stock movement
             */

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

                $stmt->execute();

                if ($stmt->affected_rows === 0) {
                    throw new Exception("Cannot reverse old stock movement.");
                }

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

                $stmt->execute();
            }


            /*
             * Apply new movement
             */

            if ($type === 'IN') {

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

                $stmt->execute();

            } else {

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
            }


            /*
             * Update inventory record
             */

            $stmt = $conn->prepare("
                UPDATE inventory

                SET product_id = ?,
                    type = ?,
                    quantity = ?,
                    note = ?

                WHERE id = ?
            ");

            $stmt->bind_param(
                "isisi",
                $product_id,
                $type,
                $quantity,
                $note,
                $id
            );

            $stmt->execute();

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
<html>

<head>

<title>Edit Inventory</title>

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

<h1>Edit Inventory</h1>

</div>

<?php if ($error): ?>

<div class="admin-message error">
<?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

<div class="dashboard-panel">

<form method="POST"
class="admin-form">

<div class="form-group">

<label>Product</label>

<select name="product_id"
required>

<?php while ($product = $products->fetch_assoc()): ?>

<option value="<?= $product['id'] ?>"
<?= $product['id'] == $row['product_id'] ? 'selected' : '' ?>>

<?= htmlspecialchars($product['name']) ?>

</option>

<?php endwhile; ?>

</select>

</div>


<div class="form-group">

<label>Type</label>

<select name="type">

<option value="IN"
<?= $row['type'] === 'IN' ? 'selected' : '' ?>>

Stock In

</option>

<option value="OUT"
<?= $row['type'] === 'OUT' ? 'selected' : '' ?>>

Stock Out

</option>

</select>

</div>


<div class="form-group">

<label>Quantity</label>

<input type="number"
name="quantity"
min="1"
value="<?= $row['quantity'] ?>"
required>

</div>


<div class="form-group">

<label>Note</label>

<textarea name="note"><?= htmlspecialchars($row['note'] ?? '') ?></textarea>

</div>


<div class="form-actions">

<button class="admin-button">

Save Changes

</button>

<a href="inventory.php"
class="secondary-button">

Cancel

</a>

</div>

</form>

</div>

</main>

</div>

</body>

</html>