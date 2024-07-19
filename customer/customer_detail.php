<?php
include '../config.php';

if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];

    // Ambil data pelanggan
    $sql = "SELECT * FROM customers WHERE customer_id = $customer_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    } else {
        echo "Customer not found";
        exit();
    }

    // Ambil data paket yang diambil pelanggan
    $sql = "SELECT p.speed, p.price, s.start_date, s.end_date 
            FROM plans p
            JOIN subscriptions s on s.plan_id = p.plan_id
            WHERE s.customer_id = $customer_id";
    $usage_result = $conn->query($sql);
    $usage = [];
    if ($usage_result->num_rows > 0) {
        while ($row = $usage_result->fetch_assoc()) {
            $usage[] = $row;
        }
    }
 

    // Ambil data transaksi pembayaran pelanggan
    $sqlb = "SELECT b.billing_id ,b.amount, b.billing_date, b.status, pay.payment_id
            FROM billing b   
            LEFT join payments pay on b.billing_id = pay.billing_id
            WHERE customer_id = $customer_id
            ORDER BY billing_date DESC;";
    $billing_result = $conn->query($sqlb);
    $billing = [];
    if ($billing_result->num_rows > 0) {
        while ($row = $billing_result->fetch_assoc()) {
            $billing[] = $row;
        }
    }
    // print_r($billing);
    // die();
} else {
    echo "No customer ID provided";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Detail</title>
    <link rel="stylesheet" href="styles.css"> <!-- Sesuaikan dengan file CSS Anda -->
</head>
<body>
    <div class="container">
        <h1>Customer Detail</h1>
        
        <!-- Detail Pelanggan -->
        <h2>Customer Information</h2>
        <p><strong>Name:</strong> <?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></p>
        <p><strong>Email:</strong> <?php echo $customer['email']; ?></p>
        <p><strong>Phone:</strong> <?php echo $customer['phone']; ?></p>
        <p><strong>Address:</strong> <?php echo $customer['address']; ?></p>
        <p><strong>Registration Date:</strong> <?php echo $customer['registration_date']; ?></p>

        <!-- Paket yang diambil -->
        <h2>Package Information</h2>
        <?php if (count($usage) > 0): ?>
            <table>
                <tr>
                    <th>Speed</th>
                    <th>Price</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
                <?php foreach ($usage as $u): ?>
                <tr>
                    <td><?php echo $u['speed']; ?></td>
                    <td><?php echo $u['price']; ?></td>
                    <td><?php echo $u['start_date']; ?></td>
                    <td><?php echo $u['end_date'] ?: 'N/A'; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No package information available.</p>
        <?php endif; ?>

        <!-- Daftar Transaksi Pembayaran -->
        <h2>Billing Transactions</h2>
        <?php if (count($billing) > 0): ?>
            <table>
                <tr>
                    <th>Billing ID</th>
                    <th>Amount</th>
                    <th>Billing Date</th>
                    <th>Status</th>
                </tr>
                <?php 
                

                ?>
                <?php foreach ($billing as $b): ?>
                <tr>
                    <td><?php echo $b['billing_id']; ?></td>
                    <td><?php echo $b['amount']; ?></td>
                    <td><?php echo $b['billing_date']; ?></td>
                    <td><?php echo $b['status']; ?></td>
                    
                    <td>
                        <a href="nota.php?payment_id=<?php echo $b['payment_id']; ?>">View Nota</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No billing transactions available.</p>
        <?php endif; ?>

        <p><a href="customers.php">Back to Customers</a></p>
    </div>
</body>
</html>

<?php
$conn->close();
?>
