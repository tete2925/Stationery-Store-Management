<?php

require_once "admin_auth.php";

require_permission("products");

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

    $image = "";

    if (!empty($_FILES['image']['name'])) {

        $image = basename($_FILES['image']['name']);

        $target = "../images/" . $image;

        if (!move_uploaded_file(
            $_FILES['image']['tmp_name'],
            $target
        )) {

            $error = "Could not upload image.";

        }
    }

    if ($error === "") {

        $stmt = $conn->prepare("
            INSERT INTO products
            (category_id, name, description, price, stock, image)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "issdis",
            $category_id,
            $name,
            $description,
            $price,
            $stock,
            $image
        );

        if ($stmt->execute()) {

            // IMPORTANT:
            // Admin product listing file is product.php
            header("Location: product.php");
            exit();

        } else {

            $error = "Could not add product.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Add Product</title>

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

<h1>Add Product</h1>

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

<select name="category_id" required>

<option value="">
Select Category
</option>

<?php if ($categories): ?>

<?php while ($cat = $categories->fetch_assoc()): ?>

<option value="<?= $cat['id'] ?>">

<?= htmlspecialchars($cat['edu_name']) ?>
-
<?= htmlspecialchars($cat['name']) ?>

</option>

<?php endwhile; ?>

<?php endif; ?>

</select>

</div>


<div class="form-group">

<label>Product Name</label>

<input
type="text"
name="name"
required
>

</div>


<div class="form-group">

<label>Description</label>

<textarea
name="description"
></textarea>

</div>


<div class="form-group">

<label>Price</label>

<input
type="number"
step="0.01"
name="price"
required
>

</div>


<div class="form-group">

<label>Stock</label>

<input
type="number"
name="stock"
min="0"
value="0"
required
>

</div>


<div class="form-group">

<label>Image</label>

<input
type="file"
name="image"
accept="image/*"
>

</div>


<div class="form-actions">

<button
class="admin-button"
type="submit"
>

Save Product

</button>


<a
href="product.php"
class="secondary-button"
>

Cancel

</a>

</div>


</form>

</div>

</main>

</div>

</body>

</html>