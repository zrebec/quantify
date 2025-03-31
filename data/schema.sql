-- Drop existing tables if they exist
DROP TABLE IF EXISTS entities;
DROP TABLE IF EXISTS measurements;

-- Entities table to store basic entity information
CREATE TABLE entities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    brand VARCHAR(255) NOT NULL,             -- Entity brand/name
    link VARCHAR(255) DEFAULT NULL,          -- Link to the entity
    net_weight INTEGER NOT NULL,             -- Net weight in grams
    description TEXT,                        -- Full entity description
    design VARCHAR(50)                       -- Design information (e.g. "3/5")
);

-- Measurement results to store individual test results
CREATE TABLE measurements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    value INTEGER NOT NULL,        -- Retention value
    saturation INTEGER NOT NULL,        -- Saturation level
    note TEXT,                -- Additional notes
    date DATE NOT NULL DEFAULT '1970-01-01', -- Date of measurement
    FOREIGN KEY (entity_id) REFERENCES entities(id)
);

-- Indexes for better performance
CREATE INDEX idx_measurement_entity ON measurements(entity_id);