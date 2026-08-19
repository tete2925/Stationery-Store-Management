<?php

include "includes/db.php";
include "includes/header.php";


// =========================================================
// FIND PRIMARY EDUCATION LEVEL
// =========================================================

$stmt = $conn->prepare("
    SELECT id, name
    FROM edu_lvls
    WHERE LOWER(name) = 'primary'
    LIMIT 1
");

$stmt->execute();

$level_result = $stmt->get_result();
$level = $level_result->fetch_assoc();

$stmt->close();


// =========================================================
// GET PRIMARY CATEGORIES
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
                PRIMARY SCHOOL
            </p>

            <h1>
                Primary School Supplies
            </h1>

            <p>
                Everything students need for
                school, learning, and creativity.
            </p>

        </div>


        <!-- PRIMARY CATEGORIES -->

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