<?php

include "../includes/db.php";

$error = "";

$edu_result = $conn->query("
    SELECT id, name
    FROM edu_lvls
    ORDER BY id
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $edu_id = intval($_POST['edu_lvls_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $icons = trim($_POST['icons'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fa-solid fa-box');

    if ($edu_id <= 0 || $name === '' || $icons === '') {

        $error = "Please fill in all required fields.";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO categories
            (edu_lvls_id, name, icons, icon)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isss",
            $edu_id,
            $name,
            $icons,
            $icon
        );

        if ($stmt->execute()) {


        header("Location: category.php");
exit();
        } else {

            $error = "Failed to add category.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Category</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <link rel="stylesheet" href="adm.css">

</head>

<body>

<div class="admin-layout">

    <?php include "sidebar.php"; ?>

    <main class="admin-main">

        <div class="admin-topbar">

            <div>
                <p class="dashboard-label">STORE MANAGEMENT</p>
                <h1>Add Category</h1>
            </div>

        </div>

        <?php if ($error): ?>

            <div class="admin-message error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <div class="dashboard-panel">

            <form method="POST" class="admin-form">

                <div class="form-group">

                    <label>Education Level</label>

                    <select name="edu_lvls_id" required>

                        <option value="">
                            Select Education Level
                        </option>

                        <?php while ($edu = $edu_result->fetch_assoc()): ?>

                            <option value="<?= $edu['id'] ?>">
                                <?= htmlspecialchars($edu['name']) ?>
                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

                <div class="form-group">

                    <label>Category Name</label>

                    <input
                        type="text"
                        name="name"
                        placeholder="Example: Pencils"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Icon Class</label>

                    <input
                        type="text"
                        name="icons"
                        placeholder="Example: fa-solid fa-pencil"
                        required
                    >

                </div>

                <div class="form-group">

                    <label>Display Icon</label>

                    <input
                        type="text"
                        name="icon"
                        value="fa-solid fa-box"
                    >

                </div>

                <div class="form-actions">

                    <button class="admin-button" type="submit">

                        <i class="fa-solid fa-save"></i>
                        Save

                    </button>

                    <a href="category.php" class="secondary-button">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>