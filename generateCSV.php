<?php
/**
 * Helper script to convert data into two separate CSV files
 * - products.csv: Contains product-level data
 * - measurements.csv: Contains measurement-level data
 */

// Data from screenshot - already in PHP array format
$data = json_decode(file_get_contents('data/init_data.json'), true);

// Convert data to CSV format
$productsFileName = 'data/products.csv';
$productsFile = fopen($productsFileName, 'w');

// Write column headers
$productHeaders = ['brand', 'net weight', 'design', 'description'];
fputcsv($productsFile, $productHeaders);

// Write data rows
foreach ($data as $row) {
    fputcsv($productsFile, [
        $row['brand'],
        $row['net weight'],
        $row['design'] ?? null,
        $row['description'] ?? null,
    ]);
}

fclose($productsFile);
echo "CSV file '$productsFileName' has been created successfully.\n";

// Create measurements CSV file
$measurementsFileName = 'data/results.csv';
$measurementsFile = fopen($measurementsFileName, 'w');

// Write measurement headers
$measurementHeaders = ['brand', 'date', 'value', 'saturation', 'note'];
fputcsv($measurementsFile, $measurementHeaders);

// Write measurement data
foreach ($data as $row) {
    foreach ($row['measurements'] as $measurement) {
        fputcsv($measurementsFile, [
            $row['brand'],
            $row['date'] ?? '1970-01-01',
            $measurement['value'],
            $measurement['saturation'],
            $measurement['note'] ?? null,
        ]);
    }
}

fclose($measurementsFile);
echo "CSV file '$measurementsFileName' has been created successfully.\n";