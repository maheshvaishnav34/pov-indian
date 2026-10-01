<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=pov_indian_db;charset=utf8mb4', 'root', '');
    $stmt = $pdo->query('DESCRIBE listings');
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "=== COLUMNS IN 'listings' TABLE ===\n";
    foreach ($cols as $c) {
        echo $c['Field'] . ' | ' . $c['Type'] . ' | ' . ($c['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
