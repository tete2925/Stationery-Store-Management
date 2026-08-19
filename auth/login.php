<?php

session_start();

include "../includes/db.php";

$message = "";
$message_type = "";


if (isset($_SESSION["success_message"])) {

    $message = $_SESSION["success_message"];

    $message_type = "success";

    unset($_SESSION["success_message"]);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare("
            SELECT
                id,
                name,
                email,
                password,
                role
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();


            if (
                password_verify(
                    $password,
                    $user["password"]
                )
            ) {

                session_regenerate_id(true);


                $_SESSION["user_id"] =
                    $user["id"];

                $_SESSION["user_name"] =
                    $user["name"];

                $_SESSION["user_email"] =
                    $user["email"];

                $_SESSION["user_role"] =
                    $user["role"];


                /*
                 * OWNER / STAFF
                 * → Admin Dashboard
                 *
                 * CUSTOMER
                 * → Client Website
                 */

                if (
                    $user["role"] === "owner" ||
                    $user["role"] === "staff"
                ) {

                    header(
                        "Location: ../admin/index.php"
                    );

                } else {

                    header(
                        "Location: ../index.php"
                    );
                }

                exit;

            } else {

                $message =
                    "Incorrect email or password.";

                $message_type = "error";
            }

        } else {

            $message =
                "Incorrect email or password.";

            $message_type = "error";
        }
    }
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

    <title>Login - Student Stationery</title>


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


    <div class="auth-card">


        <!-- LOGO -->

        <a
            href="../index.php"
            class="auth-logo"
        >

            <div class="auth-logo-icon">

                <i class="fa-solid fa-pencil"></i>

            </div>

            <div>

                Student
                <strong>Stationery</strong>

            </div>

        </a>



        <!-- HEADER -->

        <div class="auth-header">

            <p class="auth-label">
                WELCOME BACK
            </p>

            <h1>
                Login
            </h1>

            <p>
                Login to your Student Stationery account.
            </p>

        </div>



        <!-- MESSAGE -->

        <?php if ($message !== ""): ?>

            <div
                class="auth-message <?php echo $message_type; ?>"
            >

                <?php
                echo htmlspecialchars($message);
                ?>

            </div>

        <?php endif; ?>



        <!-- FORM -->

        <form
            method="POST"
            class="auth-form"
        >


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-envelope"></i>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                        autocomplete="email"
                    >

                </div>

            </div>



            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                    >

                </div>

            </div>



            <button
                type="submit"
                class="auth-button"
            >

                Login

                <i class="fa-solid fa-arrow-right"></i>

            </button>


        </form>



        <div class="auth-footer">

            Don't have an account?

            <a href="signup.php">
                Create Account
            </a>

        </div>


    </div>

</div>


</body>

</html>