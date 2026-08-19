<?php

include "includes/db.php";


// Get education levels from database

$levels = $conn->query("
    SELECT
        id,
        name,
        icon
    FROM edu_lvls
    ORDER BY id
");


// Get featured products

$products = $conn->query("
    SELECT
        products.*,
        categories.name AS category_name,
        edu_lvls.name AS education_level

    FROM products

    INNER JOIN categories
        ON products.category_id = categories.id

    INNER JOIN edu_lvls
        ON categories.edu_lvls_id = edu_lvls.id

    ORDER BY products.id DESC

    LIMIT 8
");

?>

<?php include "includes/header.php"; ?>


<!-- =====================================
     HERO
===================================== -->

<section
    class="hero"
    style="background-image: url('images/1.png');"
>

    <div class="container hero-content">

        <div class="hero-text">

            <p class="hero-label">
                STUDENT ESSENTIALS
            </p>

            <h1>
                Everything Students
                <br>
                Need in One Place
            </h1>

            <p class="hero-description">
                From primary school supplies
                to university stationery and
                technology.
            </p>

            <a
                href="products.php"
                class="hero-button"
            >
                SHOP NOW →
            </a>

        </div>

    </div>

</section>


<!-- =====================================
     EDUCATION LEVELS
===================================== -->

<section class="education-section">

    <div class="container">

        <div class="section-title">

            <p>
                SHOP BY EDUCATION LEVEL
            </p>

            <h2>
                Find What You Need
            </h2>

        </div>


        <div class="education-grid">

            <?php if ($levels && $levels->num_rows > 0): ?>

                <?php while ($level = $levels->fetch_assoc()): ?>

                    <?php

                    $icon = !empty($level['icon'])
                        ? $level['icon']
                        : "fa-solid fa-school";

                    $class = "education-level-" . $level['id'];

                    ?>


                    <a
                        href="products.php?level=<?php echo $level['id']; ?>"
                        class="education-card <?php echo htmlspecialchars($class); ?>"
                    >

                        <div class="education-icon">

                            <i class="<?php echo htmlspecialchars($icon); ?>"></i>

                        </div>

                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $level['name']
                            );
                            ?>

                        </h3>

                        <span>
                            Shop Now →
                        </span>

                    </a>


                <?php endwhile; ?>

            <?php endif; ?>

        </div>

    </div>

</section>


<!-- =====================================
     FEATURED PRODUCTS
===================================== -->

<section class="products-section">

    <div class="container">


        <div class="section-title product-title">

            <div>

                <p>
                    OUR PRODUCTS
                </p>

                <h2>
                    Featured Products
                </h2>

            </div>


            <!-- SAME ALL PRODUCTS PAGE -->

            <a
                href="products.php"
                class="view-all"
            >
                View All →
            </a>

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



                        <!-- INFORMATION -->

                        <div class="product-info">


                            <p class="product-level">

                                <?php
                                echo htmlspecialchars(
                                    $product['education_level']
                                );
                                ?>

                            </p>


                            <h3>

                                <?php
                                echo htmlspecialchars(
                                    $product['name']
                                );
                                ?>

                            </h3>


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
                        No featured products available yet.
                    </p>

                </div>

            <?php endif; ?>


        </div>

    </div>

</section>


<!-- =====================================
     UNIVERSITY FEATURE
===================================== -->

<section class="university-banner">

    <div class="container university-content">

        <div>

            <p>
                UNIVERSITY ESSENTIALS
            </p>

            <h2>
                Study. Create. Connect.
            </h2>

            <p>
                Get stationery, computer accessories
                and laptops for university life.
            </p>

        </div>


        <a
            href="uni.php?level=3"
            class="university-button"
        >
            EXPLORE UNIVERSITY →
        </a>

    </div>

</section>


<!-- =====================================
     WHY SHOP WITH US
===================================== -->

<section class="features-section">

    <div class="container">

        <div class="section-title">

            <p>
                WHY CHOOSE US
            </p>

            <h2>
                Made For Students
            </h2>

        </div>


        <div class="features-grid">


            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>

                <h3>
                    Student Focused
                </h3>

                <p>
                    Products selected for
                    every education level.
                </p>

            </div>


            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-truck"></i>
                </div>

                <h3>
                    Easy Shopping
                </h3>

                <p>
                    Find your supplies
                    quickly and easily.
                </p>

            </div>


            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-star"></i>
                </div>

                <h3>
                    Quality Products
                </h3>

                <p>
                    Reliable products for
                    school and university.
                </p>

            </div>


            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-laptop"></i>
                </div>

                <h3>
                    University Tech
                </h3>

                <p>
                    Laptops and accessories
                    for university students.
                </p>

            </div>


        </div>

    </div>

</section>


<?php include "includes/footer.php"; ?>