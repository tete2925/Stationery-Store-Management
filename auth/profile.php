<?php

session_start();

include "../includes/db.php";


if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}


$user_id = $_SESSION["user_id"];


$stmt = $conn->prepare("
    SELECT
        id,
        name,
        email,
        role,
        created_at
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();


if (!$user) {

    session_destroy();

    header("Location: login.php");

    exit;
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Account - Student Stationery</title>


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >


    <link
        rel="stylesheet"
        href="auth.css"
    >

</head>


<body>


<div class="auth-page">


    <div class="profile-card">


        <!-- AVATAR -->

        <div class="profile-avatar">

            <i class="fa-solid fa-user"></i>

        </div>



        <p class="auth-label">
            MY ACCOUNT
        </p>



        <h1>

            <?php
            echo htmlspecialchars(
                $user["name"]
            );
            ?>

        </h1>



        <!-- ROLE -->

        <span class="profile-role">

            <?php
            echo htmlspecialchars(
                ucfirst($user["role"])
            );
            ?>

        </span>



        <!-- INFORMATION -->

        <div class="profile-information">


            <div class="profile-row">

                <span>
                    Name
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $user["name"]
                    );
                    ?>

                </strong>

            </div>



            <div class="profile-row">

                <span>
                    Email
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $user["email"]
                    );
                    ?>

                </strong>

            </div>



            <div class="profile-row">

                <span>
                    Account Type
                </span>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        ucfirst($user["role"])
                    );
                    ?>

                </strong>

            </div>


        </div>



        <!-- ACTIONS -->

        <div class="profile-actions">


            <a
                href="../index.php"
                class="secondary-button"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Continue Shopping

            </a>



            <a
                href="logout.php"
                class="auth-button"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                Logout

            </a>


        </div>


    </div>

</div>


</body>

</html>