<?php
include 'config.php';

$sql = "
SELECT 
    customers.first_name,
    customers.last_name,
    plans.speed,
    plans.price,
    billing.amount AS billing_amount
FROM 
    customers
JOIN 
    subscriptions ON customers.customer_id = subscriptions.customer_id
JOIN 
    plans ON subscriptions.plan_id = plans.plan_id
JOIN 
    billing ON customers.customer_id = billing.customer_id
ORDER BY 
    customers.customer_id
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>WiFi Billing System</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>WiFi Billing System</h1>
    <table>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Speed</th>
            <th>Price</th>
            <th>Billing Amount</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["first_name"] . "</td>";
                echo "<td>" . $row["last_name"] . "</td>";
                echo "<td>" . $row["speed"] . "</td>";
                echo "<td>" . $row["price"] . "</td>";
                echo "<td>" . $row["billing_amount"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No data found</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>
