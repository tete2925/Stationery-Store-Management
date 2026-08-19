<?php

require_once "admin_auth.php";

require_permission("categories");

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT *
    FROM categories
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    header("Location: category.php");
    exit();
}

$levels = $conn->query("
    SELECT id, name
    FROM edu_lvls
    ORDER BY id
");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $edu_id = intval($_POST['edu_lvls_id']);
    $name = trim($_POST['name']);
    $icons = trim($_POST['icons']);
    $icon = trim($_POST['icon']);

    $stmt = $conn->prepare("
        UPDATE categories
        SET edu_lvls_id = ?,
            name = ?,
            icons = ?,
            icon = ?
        WHERE id = ?
    ");

    $stmt->bind_param(
        "isssi",
        $edu_id,
        $name,
        $icons,
        $icon,
        $id
    );

    if ($stmt->execute()) {

        header("Location: category.php");
        exit();

    } else {

        $error = "Could not update category.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Category</title>

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

<h1>Edit Category</h1>

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

<label>Education Level</label>

<select name="edu_lvls_id" required>

<?php while ($level = $levels->fetch_assoc()): ?>

<option value="<?= $level['id'] ?>"
<?= $level['id'] == $row['edu_lvls_id'] ? 'selected' : '' ?>>

<?= htmlspecialchars($level['name']) ?>

</option>

<?php endwhile; ?>

</select>

</div>


<div class="form-group">

<label>Category Name</label>

<input type="text"
name="name"
value="<?= htmlspecialchars($row['name']) ?>"
required>

</div>


<div class="form-group">

<label>Icon Class</label>

<input type="text"
name="icons"
value="<?= htmlspecialchars($row['icons']) ?>"
required>

</div>


<div class="form-group">

<label>Secondary Icon</label>

<input type="text"
name="icon"
value="<?= htmlspecialchars($row['icon'] ?? '') ?>">

</div>


<div class="form-actions">

<button class="admin-button"
type="submit">

Save Changes

</button>

<a href="category.php"
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