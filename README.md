# Analysis and Comparsion Tool

A web application for comparing and analyzing entities resuts data.

## Overview

This tool allows users to:
- View a list of entities with their specifications
- Compare multiple entities based on their measurements results
- Analyze detailed entity performance across different testing levels

## Tech Stack

- **Backend**: PHP 7.4+
- **Database**: SQLite
- **Framework**: Slim 4 for routing
- **ORM**: Medoo (lightweight database framework)
- **Templates**: Twig
- **Frontend**: Bootstrap 5, Chart.js

## Project Structure

```
project/
├── composer.json         # Composer dependencies
├── schema.sql            # Database schema
├── import.php            # Import script for migration from CSV
├── public/               # Web root directory
│   ├── index.php         # Front controller
│   ├── css/              # CSS files
│   └── js/               # JavaScript files
├── src/                  # Application code
│   ├── Models/           # Model classes
│   ├── Controllers/      # Controller classes
│   └── Services/         # Service classes
├── templates/            # Twig templates
├── vendor/               # Composer dependencies
└── data/                 # Data directory
    ├── data.sqlite  # SQLite database
    └── data.csv          # Original CSV data

## Installation

1. Clone the repository:
```bash
git clone https://github.com/your-username/analysis-comparsion-tool.git
cd analysis-comparsion-tool
```

2. Install dependencies:
```bash
composer install
```

3. Import data from CSV to SQLite:
```bash
composer run import
```

4. Set up a web server or use PHP's built-in server:
```bash
php -S localhost:8000 -t public
```

5. Access the application at `http://localhost:8000`

## Data Model

## Data Model

The application uses a simple data model with two main tables:

1. **Entities** - Basic entity information
   - ID
   - Brand (entity name)
   - Net weight
   - Description
   - Design information

2. **Measurement Results** - Measurement results for each entity
   - Entity ID
   - Measurement value (ml)


## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the LICENSE file for details.