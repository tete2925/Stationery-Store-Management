<?php

require_once "admin_auth.php";

require_permission("products");

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT *
    FROM products
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {

    header("Location: products.php");
    exit();

}

$categories = $conn->query("
    SELECT 
        categories.id,
        categories.name,
        edu_lvls.name AS edu_name
    FROM categories
    INNER JOIN edu_lvls
        ON categories.edu_lvls_id = edu_lvls.id
    ORDER BY edu_lvls.id, categories.name
");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $category_id = intval($_POST['category_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    $image = $row['image'];

    if (!empty($_FILES['image']['name'])) {

        $image = basename($_FILES['image']['name']);

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../images/" . $image
        );
    }

    $stmt = $conn->prepare("
        UPDATE products

        SET category_id = ?,
            name = ?,
            description = ?,
            price = ?,
            stock = ?,
            image = ?

        WHERE id = ?
    ");

    $stmt->bind_param(
        "issdisi",
        $category_id,
        $name,
        $description,
        $price,
        $stock,
        $image,
        $id
    );

    if ($stmt->execute()) {

        header("Location: products.php");
        exit();

    } else {

        $error = "Could not update product.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Product</title>

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

<h1>Edit Product</h1>

</div>

<?php if ($error): ?>

<div class="admin-message error">
<?= htmlspecialchars($error) ?>
</div>

<?php endif; ?>

<div class="dashboard-panel">

<form method="POST"
enctype="multipart/form-data"
class="admin-form">

<div class="form-group">

<label>Category</label>

<select name="category_id"
required>

<?php while ($cat = $categories->fetch_assoc()): ?>

<option value="<?= $cat['id'] ?>"
<?= $cat['id'] == $row['category_id'] ? 'selected' : '' ?>>

<?= htmlspecialchars($cat['edu_name']) ?>
-
<?= htmlspecialchars($cat['name']) ?>

</option>

<?php endwhile; ?>

</select>

</div>


<div class="form-group">

<label>Product Name</label>

<input type="text"
name="name"
value="<?= htmlspecialchars($row['name']) ?>"
required>

</div>


<div class="form-group">

<label>Description</label>

<textarea name="description"><?= htmlspecialchars($row['description']) ?></textarea>

</div>


<div class="form-group">

<label>Price</label>

<input type="number"
step="0.01"
name="price"
value="<?= $row['price'] ?>"
required>

</div>


<div class="form-group">

<label>Stock</label>

<input type="number"
name="stock"
value="<?= $row['stock'] ?>"
min="0"
required>

</div>


<div class="form-group">

<label>New Image</label>

<input type="file"
name="image"
accept="image/*">

</div>


<div class="form-actions">

<button class="admin-button"
type="submit">

Save Changes

</button>

<a href="products.php"
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