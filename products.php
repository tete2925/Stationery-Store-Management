<?php

include "includes/db.php";
include "includes/header.php";

$search = trim($_GET['search'] ?? '');


// =========================================================
// GET ALL PRODUCTS / SEARCH PRODUCTS
// =========================================================

if ($search !== '') {

    $stmt = $conn->prepare("
        SELECT
            products.id,
            products.name,
            products.description,
            products.price,
            products.stock,
            products.image,
            categories.name AS category_name,
            edu_lvls.name AS edu_name
        FROM products

        INNER JOIN categories
            ON products.category_id = categories.id

        INNER JOIN edu_lvls
            ON categories.edu_lvls_id = edu_lvls.id

        WHERE products.name LIKE ?

        ORDER BY products.id DESC
    ");

    $search_term = "%" . $search . "%";

    $stmt->bind_param("s", $search_term);
    $stmt->execute();

    $products = $stmt->get_result();

} else {

    $products = $conn->query("
        SELECT
            products.id,
            products.name,
            products.description,
            products.price,
            products.stock,
            products.image,
            categories.name AS category_name,
            edu_lvls.name AS edu_name
        FROM products

        INNER JOIN categories
            ON products.category_id = categories.id

        INNER JOIN edu_lvls
            ON categories.edu_lvls_id = edu_lvls.id

        ORDER BY products.id DESC
    ");

}

?>


<section class="products-page">

<div class="container">


    <!-- PAGE HEADER -->

    <div class="products-page-header">

        <p class="page-label">
            SHOP
        </p>

        <h1>
            All Products
        </h1>

        <p>
            Browse all stationery and supplies available
            for every education level.
        </p>

    </div>


    <!-- SEARCH -->

    <div style="
        width: 100%;
        max-width: 1000px;
        margin: 0 auto 50px;
    ">

        <form
            method="GET"
            action="products.php"
            style="
                display: flex;
                width: 100%;
                height: 65px;
                gap: 12px;
            "
        >

            <input
                type="text"
                name="search"
                placeholder="Search products by name..."
                value="<?php echo htmlspecialchars($search); ?>"
                style="
                    flex: 1;
                    width: 100%;
                    height: 65px;
                    padding: 0 24px;
                    border: 2px solid #ddd;
                    border-radius: 8px;
                    background: #fff;
                    color: #222;
                    font-size: 18px;
                    outline: none;
                    box-sizing: border-box;
                "
            >

            <button
                type="submit"
                style="
                    width: 160px;
                    min-width: 160px;
                    height: 65px;
                    border: none;
                    border-radius: 8px;
                    background: #d71920;
                    color: #fff;
                    font-size: 16px;
                    font-weight: bold;
                    cursor: pointer;
                "
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                Search

            </button>

        </form>

    </div>


    <!-- PRODUCTS -->

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

                            <div class="product-no-image">
                                <i class="fa-solid fa-box"></i>
                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- INFORMATION -->

                    <div class="product-info">

                        <div class="product-level">

                            <?php
                            echo htmlspecialchars(
                                $product['edu_name']
                            );
                            ?>

                        </div>


                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $product['name']
                            );
                            ?>

                        </h3>


                        <?php if (!empty($product['description'])): ?>

                            <p class="product-description">

                                <?php
                                echo htmlspecialchars(
                                    $product['description']
                                );
                                ?>

                            </p>

                        <?php endif; ?>


                        <div class="product-bottom">

                            <strong class="product-price">

                                <?php
                                echo number_format(
                                    $product['price']
                                );
                                ?>

                                Ks

                            </strong>


                            <?php if ($product['stock'] > 0): ?>

                                <form
                                    action="cart.php"
                                    method="POST"
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

                <?php if ($search !== ''): ?>

                    <p>
                        No products found for
                        "<strong><?php echo htmlspecialchars($search); ?></strong>".
                    </p>

                <?php else: ?>

                    <p>
                        No products available yet.
                    </p>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

</section>


<?php

include "includes/footer.php";

?>