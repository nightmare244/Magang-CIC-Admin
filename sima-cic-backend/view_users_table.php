<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=db_sima_cic', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "USERS_TABLE_SCHEMA:\n";
    foreach ($columns as $c) {
        echo "{$c['Field']} - {$c['Type']} - Null: {$c['Null']} - Key: {$c['Key']}\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
