<?php

require_once "../includes/session.php";
require_once "../config/database.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");
    exit;
}

if ($_SESSION["role"] !== "customer") {

    header("Location: ../login.php");
    exit;
}


$customer_id =
    (int) $_SESSION["user_id"];

$error = "";


// ==========================================
// GET CURRENT CUSTOMER DATA
// ==========================================

$sql = "
    SELECT
        full_name,
        username,
        email,
        phone,
        address
    FROM users
    WHERE user_id = ?
    LIMIT 1
";

$stmt =
    mysqli_prepare(
        $conn,
        $sql
    );

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $customer_id
);

mysqli_stmt_execute(
    $stmt
);

$result =
    mysqli_stmt_get_result(
        $stmt
    );

$customer =
    mysqli_fetch_assoc(
        $result
    );

mysqli_stmt_close(
    $stmt
);


if (!$customer) {

    header("Location: ../login.php");
    exit;
}


// ==========================================
// UPDATE PROFILE
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name =
        trim($_POST["full_name"] ?? "");

    $email =
        trim($_POST["email"] ?? "");

    $phone =
        trim($_POST["phone"] ?? "");

    $address =
        trim($_POST["address"] ?? "");


    // --------------------------
    // EMPTY FIELD CHECK
    // --------------------------

    if (
        $full_name === "" ||
        $email === "" ||
        $phone === "" ||
        $address === ""
    ) {

        $error =
            "Please fill in all fields.";

    }


    // --------------------------
    // EMAIL VALIDATION
    // --------------------------

    elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Please enter a valid email address.";

    }

    else {

        // ==================================
        // CHECK DUPLICATE EMAIL
        // ==================================

        $check_sql = "
            SELECT user_id
            FROM users
            WHERE email = ?
              AND user_id != ?
            LIMIT 1
        ";

        $check_stmt =
            mysqli_prepare(
                $conn,
                $check_sql
            );

        mysqli_stmt_bind_param(
            $check_stmt,
            "si",
            $email,
            $customer_id
        );

        mysqli_stmt_execute(
            $check_stmt
        );

        $check_result =
            mysqli_stmt_get_result(
                $check_stmt
            );

        $duplicate =
            mysqli_fetch_assoc(
                $check_result
            );

        mysqli_stmt_close(
            $check_stmt
        );


        if ($duplicate) {

            $error =
                "This email is already used by another account.";

        } else {

            // ==================================
            // UPDATE DATABASE
            // ==================================

            $update_sql = "
                UPDATE users
                SET
                    full_name = ?,
                    email = ?,
                    phone = ?,
                    address = ?
                WHERE user_id = ?
            ";

            $update_stmt =
                mysqli_prepare(
                    $conn,
                    $update_sql
                );

            mysqli_stmt_bind_param(
                $update_stmt,
                "ssssi",
                $full_name,
                $email,
                $phone,
                $address,
                $customer_id
            );


            if (
                mysqli_stmt_execute(
                    $update_stmt
                )
            ) {

                mysqli_stmt_close(
                    $update_stmt
                );


                // Session name update
                $_SESSION["full_name"] =
                    $full_name;


                header(
                    "Location: profile.php?updated=1"
                );

                exit;

            } else {

                $error =
                    "Profile could not be updated.";

                mysqli_stmt_close(
                    $update_stmt
                );
            }
        }
    }


    // Error হলে typed data হারাবে না

    $customer["full_name"] =
        $full_name;

    $customer["email"] =
        $email;

    $customer["phone"] =
        $phone;

    $customer["address"] =
        $address;
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

    <title>
        Edit Profile | PawCare
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/edit-profile.css"
    >

</head>


<body>

<div class="edit-page">


    <header class="edit-header">

        <div>

            <h1>
                EDIT PROFILE
            </h1>

            <p>
                Update your PawCare information
            </p>

        </div>


        <a href="profile.php">
            ← BACK TO PROFILE
        </a>

    </header>


    <main class="edit-container">


        <section class="edit-card">


            <div class="edit-card-heading">

                <div class="edit-avatar">

                    <?php
                    echo strtoupper(
                        substr(
                            $customer["username"],
                            0,
                            1
                        )
                    );
                    ?>

                </div>


                <div>

                    <h2>
                        <?php
                        echo htmlspecialchars(
                            $customer["full_name"]
                        );
                        ?>
                    </h2>

                    <p>
                        @<?php
                        echo htmlspecialchars(
                            $customer["username"]
                        );
                        ?>
                    </p>

                </div>

            </div>


            <?php if ($error !== ""): ?>

                <div class="edit-error">

                    <?php
                    echo htmlspecialchars(
                        $error
                    );
                    ?>

                </div>

            <?php endif; ?>


            <form
                method="POST"
                class="edit-form"
            >


                <div class="form-group">

                    <label>
                        FULL NAME
                    </label>

                    <input
                        type="text"
                        name="full_name"
                        value="<?php
                            echo htmlspecialchars(
                                $customer["full_name"]
                            );
                        ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        USERNAME
                    </label>

                    <input
                        type="text"
                        value="<?php
                            echo htmlspecialchars(
                                $customer["username"]
                            );
                        ?>"
                        disabled
                    >

                    <small>
                        Username cannot be changed.
                    </small>

                </div>


                <div class="form-group">

                    <label>
                        EMAIL ADDRESS
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="<?php
                            echo htmlspecialchars(
                                $customer["email"]
                            );
                        ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        PHONE NUMBER
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="<?php
                            echo htmlspecialchars(
                                $customer["phone"] ?? ""
                            );
                        ?>"
                        required
                    >

                </div>


                <div class="form-group full-width">

                    <label>
                        SHIPPING ADDRESS
                    </label>

                    <textarea
                        name="address"
                        rows="4"
                        required
                    ><?php
                        echo htmlspecialchars(
                            $customer["address"] ?? ""
                        );
                    ?></textarea>

                </div>


                <div class="edit-actions">

                    <a href="profile.php">
                        CANCEL
                    </a>

                    <button type="submit">
                        SAVE CHANGES
                    </button>

                </div>


            </form>


        </section>

    </main>

</div>

</body>

</html>