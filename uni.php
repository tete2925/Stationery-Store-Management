<?php

include "includes/db.php";
include "includes/header.php";


// =========================================================
// FIND UNIVERSITY EDUCATION LEVEL
// =========================================================

$stmt = $conn->prepare("
    SELECT id, name
    FROM edu_lvls
    WHERE LOWER(name) = 'university'
    LIMIT 1
");

$stmt->execute();

$level_result = $stmt->get_result();
$level = $level_result->fetch_assoc();

$stmt->close();


// =========================================================
// GET UNIVERSITY CATEGORIES
// =========================================================

$categories = null;

if ($level) {

    $category_stmt = $conn->prepare("
        SELECT
            id,
            name,
            icon
        FROM categories
        WHERE edu_lvls_id = ?
        ORDER BY id
    ");

    $category_stmt->bind_param("i", $level['id']);
    $category_stmt->execute();

    $categories = $category_stmt->get_result();
}

?>

<section class="products-page">

    <div class="container">

        <div class="products-page-header">

            <p class="page-label">
                UNIVERSITY
            </p>

            <h1>
                University Essentials
            </h1>

            <p>
                Stationery, books, computer accessories,
                and technology for university life.
            </p>

        </div>


        <!-- UNIVERSITY CATEGORIES -->

        <div class="category-grid">

            <?php if ($categories && $categories->num_rows > 0): ?>

                <?php while ($category = $categories->fetch_assoc()): ?>

                    <a
                        href="product.php?category=<?php echo $category['id']; ?>"
                        class="category-card"
                    >

                        <div class="category-icon">

                            <?php if (!empty($category['icon'])): ?>

                                <i class="<?php
                                    echo htmlspecialchars($category['icon']);
                                ?>"></i>

                            <?php else: ?>

                                <i class="fa-solid fa-box"></i>

                            <?php endif; ?>

                        </div>


                        <h3>
                            <?php
                            echo htmlspecialchars($category['name']);
                            ?>
                        </h3>


                        <span>
                            Explore →
                        </span>

                    </a>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="no-products">

                    <p>
                        No categories available yet.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>


<?php

include "includes/footer.php";

?>