<?php  
include 'config.php';


// Total semua amount
$total_amount_query = "SELECT SUM(amount) as total_amount FROM payments";
$total_amount_result = $conn->query($total_amount_query);
$total_amount = $total_amount_result->fetch_assoc()['total_amount'];

// Total amount bulan ini
$current_month = date('m');
$current_year = date('Y');
$total_amount_month_query = "SELECT SUM(amount) as total_amount_month FROM payments WHERE MONTH(payment_date) = ? AND YEAR(payment_date) = ?";
$stmt = $conn->prepare($total_amount_month_query);
$stmt->bind_param("ii", $current_month, $current_year);
$stmt->execute();
$total_amount_month_result = $stmt->get_result();
$total_amount_month = $total_amount_month_result->fetch_assoc()['total_amount_month'];

// Jumlah yang sudah membayar
$paid_count_query = "SELECT COUNT(*) as paid_count FROM billing WHERE status = 'Lunas'";
$paid_count_result = $conn->query($paid_count_query);
$paid_count = $paid_count_result->fetch_assoc()['paid_count'];

// Jumlah yang belum membayar
$unpaid_count_query = "SELECT COUNT(*) as unpaid_count FROM billing WHERE status = 'Belum Lunas'";
$unpaid_count_result = $conn->query($unpaid_count_query);
$unpaid_count = $unpaid_count_result->fetch_assoc()['unpaid_count'];

// Jumlah customer
$customer_count_query = "SELECT COUNT(*) as customer_count FROM customers";
$customer_count_result = $conn->query($customer_count_query);
$customer_count = $customer_count_result->fetch_assoc()['customer_count'];
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WiFi Billing Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .info-box {
            display: flex;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .info-box .box {
            flex: 1;
            min-width: 250px;
            padding: 20px;
            margin: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            text-align: center;
        }
        .info-box .box h3 {
            margin-bottom: 10px;
            font-size: 1.5em;
        }
        .info-box .box p {
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <h2>Admin Dashboard</h2>
            <ul>
                <li><a href="/">Dashboard</a></li>
                <li><a href="customer/customers.php">Customers</a></li>
                <li><a href="#">Plans</a></li>
                <li><a href="#">Subscriptions</a></li>
                <li><a href="#">Usage</a></li>
                <li><a href="billings/billing.php">Billing</a></li>
                <li><a href="#">Payments</a></li>
            </ul>
        </nav>
        <main>
            <div class="header">
                <h1>Welcome to WiFi Billing System</h1>
                <a href="logout.php">Logout</a>
            </div>
            <div class="content">
            <div class="container">
    <h1>Dashboard</h1>
    <div class="info-box">
        <div class="box">
            <h3>Total Semua Amount</h3>
            <p>Rp. <?php echo number_format($total_amount, 2, ',', '.'); ?></p>
        </div>
        <div class="box">
            <h3>Total Amount Bulan Ini</h3>
            <p>Rp. <?php echo number_format($total_amount_month, 2, ',', '.'); ?></p>
        </div>
        <div class="box">
            <h3>Jumlah yang Sudah Membayar</h3>
            <p><?php echo $paid_count; ?> pembayaran</p>
        </div>
        <div class="box">
            <h3>Jumlah yang Belum Membayar</h3>
            <p><?php echo $unpaid_count; ?> pembayaran</p>
        </div>
        <div class="box">
            <h3>Jumlah Customer</h3>
            <p><?php echo $customer_count; ?> customer</p>
        </div>
    </div>
</div>
            </div>
        </main>
    </div>
</body>
</html>
