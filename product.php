<?php

include "includes/db.php";
include "includes/header.php";


// =========================================================
// GET CATEGORY
// =========================================================

$category_id = isset($_GET['category']) ? (int) $_GET['category'] : 0;


// =========================================================
// GET CATEGORY INFORMATION
// =========================================================

$category = null;

if ($category_id > 0) {

    $stmt = $conn->prepare("
        SELECT 
            categories.id,
            categories.name AS category_name,
            edu_lvls.name AS edu_name
        FROM categories
        INNER JOIN edu_lvls
            ON categories.edu_lvls_id = edu_lvls.id
        WHERE categories.id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    $category_result = $stmt->get_result();
    $category = $category_result->fetch_assoc();

    $stmt->close();
}


// =========================================================
// GET PRODUCTS IN THIS CATEGORY
// =========================================================

$products = null;

if ($category) {

    $stmt = $conn->prepare("
        SELECT 
            id,
            name,
            description,
            price,
            stock,
            image
        FROM products
        WHERE category_id = ?
        ORDER BY id DESC
    ");

    $stmt->bind_param("i", $category_id);
    $stmt->execute();

    $products = $stmt->get_result();
}

?>


<section class="products-page">

    <div class="container">

        <?php if ($category): ?>

            <div class="products-page-header">

                <p class="page-label">
                    <?php echo htmlspecialchars($category['edu_name']); ?>
                </p>

                <h1>
                    <?php echo htmlspecialchars($category['category_name']); ?>
                </h1>

                <p>
                    Browse <?php echo htmlspecialchars($category['category_name']); ?>
                    for <?php echo htmlspecialchars($category['edu_name']); ?>.
                </p>

            </div>


            <div class="product-grid">

                <?php if ($products && $products->num_rows > 0): ?>

                    <?php while ($product = $products->fetch_assoc()): ?>

                        <div class="product-card">

                            <!-- IMAGE -->

                            <div class="product-image">

                                <?php if (!empty($product['image'])): ?>

                                    <img
                                        src="images/<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>"
                                    >

                                <?php else: ?>

                                    <div class="image-placeholder">
                                        <i class="fa-solid fa-box"></i>
                                    </div>

                                <?php endif; ?>

                            </div>


                            <!-- PRODUCT INFO -->

                            <div class="product-info">

                                <h3>
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </h3>


                                <?php if (!empty($product['description'])): ?>

                                    <p class="product-description">
                                        <?php echo htmlspecialchars($product['description']); ?>
                                    </p>

                                <?php endif; ?>


                                <div class="product-bottom">

                                    <strong class="product-price">
                                        <?php echo number_format($product['price']); ?>
                                        Ks
                                    </strong>


                                    <?php if ($product['stock'] > 0): ?>

                                        <form
                                            action="cart.php"
                                            method="POST"
                                            class="product-cart-form"
                                            style="margin:0;"
                                        >

                                            <input
                                                type="hidden"
                                                name="action"
                                                value="add"
                                            >

                                            <input
                                                type="hidden"
                                                name="product_id"
                                                value="<?php echo $product['id']; ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="add-cart-btn"
                                                style="
                                                    min-width: 155px;
                                                    height: 46px;
                                                    padding: 0 20px;
                                                    border: none;
                                                    border-radius: 6px;
                                                    background: #d71920;
                                                    color: white;
                                                    font-size: 14px;
                                                    font-weight: bold;
                                                    cursor: pointer;
                                                    white-space: nowrap;
                                                "
                                            >

                                                <i class="fa-solid fa-cart-plus"></i>

                                                Add to Cart

                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <button
                                            type="button"
                                            class="add-cart-btn disabled"
                                            disabled
                                            style="
                                                min-width: 155px;
                                                height: 46px;
                                                padding: 0 20px;
                                                border: none;
                                                border-radius: 6px;
                                                background: #999;
                                                color: white;
                                                font-size: 14px;
                                                font-weight: bold;
                                                white-space: nowrap;
                                                cursor: not-allowed;
                                            "
                                        >
                                            Out of Stock
                                        </button>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="no-products">

                        <p>
                            No products available in this category yet.
                        </p>

                    </div>

                <?php endif; ?>

            </div>


        <?php else: ?>

            <div class="products-page-header">

                <p class="page-label">
                    PRODUCTS
                </p>

                <h1>
                    Category Not Found
                </h1>

                <p>
                    This category does not exist.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>


<?php

include "includes/footer.php";

?>