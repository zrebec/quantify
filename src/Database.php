<?php
class Database {
    private $pdo;

    public function __construct(string $dbPath) {
        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('PRAGMA foreign_keys = ON');
    }

    public function initialize() {
        $schema = file_get_contents('data/schema.sql');
        $this->pdo->exec($schema);
        echo "Database schema initialized.\n";
    }

    public function getPDO(): PDO {
        return $this->pdo;
    }
}