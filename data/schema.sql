-- Drop existing tables if they exist
DROP TABLE IF EXISTS entitiy_properties;
DROP TABLE IF EXISTS measurements;
DROP TABLE IF EXISTS entities;
DROP TABLE IF EXISTS entity_images;

-- Entities table to store basic entity information
CREATE TABLE entities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,              -- Entity name
    link VARCHAR(255) DEFAULT NULL,          -- Link to the entity
    image VARCHAR(255) DEFAULT NULL,         -- Image URL
    description TEXT,                        -- Full entity description
    design VARCHAR(50)                       -- Design information (e.g. "3/5")
);

CREATE TABLE entitiy_properties (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    property_name VARCHAR(30) NOT NULL,     -- Property name (e.g. "Retention")
    property_value VARCHAR(255) NOT NULL,         -- Property value (e.g. 1000)
    FOREIGN KEY (entity_id) REFERENCES entities(id)
);

CREATE TABLE entity_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    image_url VARCHAR(255) NOT NULL,        -- Image URL
    image_alt VARCHAR(255) DEFAULT NULL,    -- Image alt text
    FOREIGN KEY (entity_id) REFERENCES entities(id)
);

-- Measurement results to store individual test results
CREATE TABLE measurements (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    entity_id INTEGER NOT NULL,
    value INTEGER NOT NULL,        -- value
    level INTEGER NOT NULL,        -- level
    note TEXT,                -- Additional notes
    date DATE NOT NULL DEFAULT '1970-01-01', -- Date of measurement
    FOREIGN KEY (entity_id) REFERENCES entities(id)
);

-- Indexes for better performance
CREATE INDEX idx_measurement_entity ON measurements(entity_id);