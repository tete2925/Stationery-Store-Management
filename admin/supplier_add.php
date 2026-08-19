<?php

require_once "admin_auth.php";

require_permission("suppliers");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("
        INSERT INTO suppliers
        (name, phone, email, address)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssss",
        $name,
        $phone,
        $email,
        $address
    );

    $stmt->execute();

    header("Location: suppliers.php");
    exit();
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Add Supplier</title>

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

<h1>Add Supplier</h1>

</div>

<div class="dashboard-panel">

<form method="POST"
class="admin-form">

<div class="form-group">

<label>Name</label>

<input type="text"
name="name"
required>

</div>

<div class="form-group">

<label>Phone</label>

<input type="text"
name="phone">

</div>

<div class="form-group">

<label>Email</label>

<input type="email"
name="email">

</div>

<div class="form-group">

<label>Address</label>

<input type="text"
name="address">

</div>

<div class="form-actions">

<button class="admin-button">

Save Supplier

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