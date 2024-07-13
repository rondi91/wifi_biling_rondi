<?php
include '../config.php';

// Fetch billing data from the database
$sql = "SELECT b.billing_id, b.customer_id, b.billing_date, b.amount, b.status, 
               c.first_name, c.last_name, p.speed, p.price
        FROM billing b
        JOIN customers c ON b.customer_id = c.customer_id
        JOIN plans p ON c.customer_id = p.plan_id";

$result = $conn->query($sql);
$billings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $billings[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing List</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Billing List</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Billing ID</th>
                <th>Customer Name</th>
                <th>Speed</th>
                <th>Price</th>
                <th>Billing Date</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($billings as $billing): ?>
                <tr>
                    <td><?php echo $billing['billing_id']; ?></td>
                    <td><?php echo $billing['first_name'] . ' ' . $billing['last_name']; ?></td>
                    <td><?php echo $billing['speed']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($billing['price'], 2, ',', '.'); ?></td>
                    <td><?php echo $billing['billing_date']; ?></td>
                    <td><?php echo 'Rp. ' . number_format($billing['amount'], 2, ',', '.'); ?></td>
                    <td><?php echo $billing['status']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
