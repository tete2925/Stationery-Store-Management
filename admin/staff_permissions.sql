CREATE TABLE IF NOT EXISTS staff_permissions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,

    products TINYINT(1) NOT NULL DEFAULT 0,
    categories TINYINT(1) NOT NULL DEFAULT 0,
    education_levels TINYINT(1) NOT NULL DEFAULT 0,
    inventory TINYINT(1) NOT NULL DEFAULT 0,
    stock_in TINYINT(1) NOT NULL DEFAULT 0,
    stock_out TINYINT(1) NOT NULL DEFAULT 0,
    suppliers TINYINT(1) NOT NULL DEFAULT 0,
    orders TINYINT(1) NOT NULL DEFAULT 0,

    PRIMARY KEY (id),
    UNIQUE KEY unique_user (user_id),

    CONSTRAINT fk_staff_permissions_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);