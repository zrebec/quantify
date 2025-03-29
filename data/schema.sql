-- Drop existing tables if they exist
DROP TABLE IF EXISTS results;
DROP TABLE IF EXISTS products;

-- Products table to store basic product information
CREATE TABLE products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    brand VARCHAR(255) NOT NULL,             -- Product brand/name
    net_weight INTEGER NOT NULL,             -- Net weight in grams
    description TEXT,                        -- Full product description
    design VARCHAR(50)                       -- Design information (e.g. "3/5")
);

-- Measurement results to store individual test results
CREATE TABLE results (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    value INTEGER NOT NULL,        -- Retention value in ml
    saturation INTEGER NOT NULL,        -- Saturation level
    note TEXT,                -- Additional notes
    date DATE NOT NULL DEFAULT (date('now')),  -- Date of measurement
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Indexes for better performance
CREATE INDEX idx_measurement_product ON results(product_id);