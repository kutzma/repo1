<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        form {
            display: inline-block;
            text-align: left;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        select {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .result {
            margin-top: 20px;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Simple Calculator</h1>
    <form method="post" action="">
        <label for="num1">First Number:</label>
        <input type="number" id="num1" name="num1" step="any" required>

        <label for="operation">Operation:</label>
        <select id="operation" name="operation" required>
            <option value="add">Addition (+)</option>
            <option value="subtract">Subtraction (-)</option>
            <option value="multiply">Multiplication (×)</option>
            <option value="divide">Division (÷)</option>
        </select>

        <label for="num2">Second Number:</label>
        <input type="number" id="num2" name="num2" step="any" required>

        <button type="submit">Calculate</button>
    </form>

    <div class="result">
        <?php
        // Database setup
        $db = new SQLite3('calculator.db');
        $db->exec("CREATE TABLE IF NOT EXISTS calculations (id INTEGER PRIMARY KEY, num1 REAL, operation TEXT, num2 REAL, result REAL, timestamp DATETIME DEFAULT CURRENT_TIMESTAMP)");

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $num1 = (float)$_POST['num1'];
            $num2 = (float)$_POST['num2'];
            $operation = $_POST['operation'];
            $result = null;

            switch ($operation) {
                case 'add':
                    $result = $num1 + $num2;
                    echo "Result: $num1 + $num2 = $result";
                    break;
                case 'subtract':
                    $result = $num1 - $num2;
                    echo "Result: $num1 - $num2 = $result";
                    break;
                case 'multiply':
                    $result = $num1 * $num2;
                    echo "Result: $num1 × $num2 = $result";
                    break;
                case 'divide':
                    if ($num2 != 0) {
                        $result = $num1 / $num2;
                        echo "Result: $num1 ÷ $num2 = $result";
                    } else {
                        echo "Error: Division by zero is not allowed.";
                    }
                    break;
                default:
                    echo "Invalid operation.";
            }

            // Save to database
            if ($result !== null) {
                $stmt = $db->prepare("INSERT INTO calculations (num1, operation, num2, result) VALUES (:num1, :operation, :num2, :result)");
                $stmt->bindValue(':num1', $num1, SQLITE3_FLOAT);
                $stmt->bindValue(':operation', $operation, SQLITE3_TEXT);
                $stmt->bindValue(':num2', $num2, SQLITE3_FLOAT);
                $stmt->bindValue(':result', $result, SQLITE3_FLOAT);
                $stmt->execute();
            }
        }
        ?>
    </div>

    <h2>Calculation History</h2>
    <table border="1" style="margin: 0 auto; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>First Number</th>
                <th>Operation</th>
                <th>Second Number</th>
                <th>Result</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $results = $db->query("SELECT * FROM calculations ORDER BY timestamp DESC");
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['num1']}</td>";
                echo "<td>{$row['operation']}</td>";
                echo "<td>{$row['num2']}</td>";
                echo "<td>{$row['result']}</td>";
                echo "<td>{$row['timestamp']}</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
