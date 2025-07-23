<?php
// Include the database connection
require_once("connection.php");

try {
    // Check if connection was successful
    if (!isset($pdo)) {
        die("Database connection failed. Please check your database configuration.");
    }

    echo "<h1>🔧 Fix Barber Table Structure</h1>";
    echo "<hr>";

    // Get current table structure
    $stmt = $pdo->prepare("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = 'barber' ORDER BY ordinal_position");
    $stmt->execute();
    $current_columns = $stmt->fetchAll();

    echo "<h2>📋 Current barber table structure:</h2>";
    if ($current_columns) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Column Name</th><th>Data Type</th><th>Nullable</th></tr>";
        foreach ($current_columns as $column) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($column['column_name']) . "</td>";
            echo "<td>" . htmlspecialchars($column['data_type']) . "</td>";
            echo "<td>" . htmlspecialchars($column['is_nullable']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Barber table not found!</p>";
        echo "<p>Please run <a href='create_tables.php'>create_tables.php</a> first.</p>";
        exit;
    }

    // Check for missing columns
    $column_names = array_column($current_columns, 'column_name');
    $required_columns = ['docnic', 'doctel', 'specialties'];
    $missing_columns = array_diff($required_columns, $column_names);

    if (!empty($missing_columns)) {
        echo "<h2>⚠️ Missing columns found:</h2>";
        echo "<ul>";
        foreach ($missing_columns as $missing) {
            echo "<li style='color: red;'>$missing</li>";
        }
        echo "</ul>";

        echo "<h2>🔨 Adding missing columns...</h2>";

        // Add missing columns
        foreach ($missing_columns as $column) {
            try {
                switch ($column) {
                    case 'docnic':
                        $sql = "ALTER TABLE barber ADD COLUMN docnic VARCHAR(20)";
                        $pdo->exec($sql);
                        echo "<p style='color: green;'>✅ Added column: docnic</p>";
                        break;
                    case 'doctel':
                        $sql = "ALTER TABLE barber ADD COLUMN doctel VARCHAR(15)";
                        $pdo->exec($sql);
                        echo "<p style='color: green;'>✅ Added column: doctel</p>";
                        break;
                    case 'specialties':
                        $sql = "ALTER TABLE barber ADD COLUMN specialties TEXT";
                        $pdo->exec($sql);
                        echo "<p style='color: green;'>✅ Added column: specialties</p>";
                        break;
                }
            } catch (PDOException $e) {
                echo "<p style='color: red;'>❌ Error adding column $column: " . $e->getMessage() . "</p>";
            }
        }
    } else {
        echo "<h2 style='color: green;'>✅ All required columns exist!</h2>";
    }

    // Show updated table structure
    echo "<hr>";
    echo "<h2>📋 Updated barber table structure:</h2>";
    $stmt = $pdo->prepare("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name = 'barber' ORDER BY ordinal_position");
    $stmt->execute();
    $updated_columns = $stmt->fetchAll();

    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Column Name</th><th>Data Type</th><th>Nullable</th></tr>";
    foreach ($updated_columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['column_name']) . "</td>";
        echo "<td>" . htmlspecialchars($column['data_type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['is_nullable']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<hr>";
    echo "<h3>🎯 Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Now run <a href='create_barber.php'>create_barber.php</a> to create the barber account</li>";
    echo "<li>Then try logging in at <a href='login.php'>login.php</a></li>";
    echo "</ol>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Database Error:</h2>";
    echo "<p style='background: #ffeeee; padding: 10px; border: 1px solid red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ General Error:</h2>";
    echo "<p style='background: #ffeeee; padding: 10px; border: 1px solid red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>