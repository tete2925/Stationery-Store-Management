<?php

require_once "admin_auth.php";

require_permission("suppliers");

$id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("
    SELECT *
    FROM suppliers
    WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();

if (!$row) {

    header("Location: suppliers.php");
    exit();

}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("
        UPDATE suppliers

        SET name = ?,
            phone = ?,
            email = ?,
            address = ?

        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssssi",
        $name,
        $phone,
        $email,
        $address,
        $id
    );

    $stmt->execute();

    header("Location: suppliers.php");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Supplier</title>

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

<h1>Edit Supplier</h1>

</div>

<div class="dashboard-panel">

<form method="POST"
class="admin-form">

<div class="form-group">

<label>Name</label>

<input type="text"
name="name"
value="<?= htmlspecialchars($row['name']) ?>"
required>

</div>

<div class="form-group">

<label>Phone</label>

<input type="text"
name="phone"
value="<?= htmlspecialchars($row['phone'] ?? '') ?>">

</div>

<div class="form-group">

<label>Email</label>

<input type="email"
name="email"
value="<?= htmlspecialchars($row['email'] ?? '') ?>">

</div>

<div class="form-group">

<label>Address</label>

<input type="text"
name="address"
value="<?= htmlspecialchars($row['address'] ?? '') ?>">

</div>

<div class="form-actions">

<button class="admin-button">

Save Changes

</button>

<a href="suppliers.php"
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