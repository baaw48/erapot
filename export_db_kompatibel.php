<?php
/**
 * Database Export Script untuk E-RAPOT
 * Compatible dengan MySQL 5.5, 5.6, 5.7 dan MariaDB
 * Menghapus syntax MySQL 8.0 yang tidak kompatibel
 */

$host = '127.0.0.1';
$port = '3306';
$dbname = 'e_rapot';
$username = 'root';
$password = '';
$outputFile = __DIR__ . '/e_rapot_kompatibel.sql';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");

    echo "Koneksi berhasil!\n";
    echo "Mengexport database: $dbname\n\n";

    $output = "-- =============================================\n";
    $output .= "-- E-RAPOT Database Export (Kompatibel)\n";
    $output .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
    $output .= "-- Database: $dbname\n";
    $output .= "-- Compatible dengan MySQL 5.5+\n";
    $output .= "-- =============================================\n\n";

    $output .= "SET NAMES utf8;\n";
    $output .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($tables as $table) {
        echo "Exporting table: $table\n";

        // Drop table if exists (simple syntax)
        $output .= "-- ------------------------------\n";
        $output .= "DROP TABLE IF EXISTS `$table`;\n";

        // Get create table statement
        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $createSQL = $row[1];

        // Remove MySQL 8.0 specific options
        $createSQL = preg_replace('/\/\*!80016 DEFAULT ENCRYPTION.*?\*\//', '', $createSQL);
        $createSQL = preg_replace('/\/\*!80000 FULL.*?\*\//', '', $createSQL);
        $createSQL = preg_replace('/COLLATE utf8mb4_0900_ai_ci/', 'COLLATE utf8mb4_unicode_ci', $createSQL);
        $createSQL = preg_replace('/ utf8mb4_0900_ai_ci/', ' utf8mb4_unicode_ci', $createSQL);
        $createSQL = preg_replace('/COLLATE utf8mb4_unicode_520_ci/', 'COLLATE utf8mb4_unicode_ci', $createSQL);

        // Replace ENGINE=InnoDB with more compatible version
        $createSQL = str_replace('ENGINE=InnoDB', 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci', $createSQL);

        $output .= $createSQL . ";\n\n";

        // Get table data
        $stmt = $pdo->query("SELECT * FROM `$table`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0) {
            $columns = array_keys($rows[0]);
            $columnList = '`' . implode('`, `', $columns) . '`';

            foreach ($rows as $row) {
                $values = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $values[] = 'NULL';
                    } else {
                        // Escape single quotes and backslashes
                        $escaped = str_replace(['\\', "\0", "\r", "\n", "\t", "'"], ['\\\\', '\\0', '\\r', '\\n', '\\t', "\\'"], $value);
                        $values[] = "'" . $escaped . "'";
                    }
                }
                $output .= "INSERT INTO `$table` ($columnList) VALUES (" . implode(', ', $values) . ");\n";
            }
            $output .= "\n";
        }
    }

    $output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

    // Save to file
    file_put_contents($outputFile, $output);

    echo "\n===========================================\n";
    echo "Export BERHASIL!\n";
    echo "File tersimpan: $outputFile\n";
    echo "Ukuran file: " . number_format(filesize($outputFile) / 1024, 2) . " KB\n";
    echo "===========================================\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}