<?php
include 'config.php';
include 'convert_number_to_words.php';

if (isset($_GET['id'])) {
    $billing_id = $_GET['id'];

    // Ambil data transaksi berdasarkan billing_id
    $sql = "SELECT b.*, c.first_name, c.last_name, c.email, c.phone, c.address, s.start_date, s.end_date, p.speed, p.price 
            FROM billing b
            JOIN customers c ON b.customer_id = c.customer_id
            JOIN subscriptions s ON c.customer_id = s.subscription_id
            join plans p on s.subscription_id = p.plan_id
            WHERE b.billing_id = $billing_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $billing = $result->fetch_assoc();
    } else {
        echo "Billing record not found";
        exit();
    }
} else {
    echo "No billing ID provided";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Nota</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .nota-container {
            max-width: 600px;
            margin: 50px auto;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
        }
        .nota-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .nota-section {
            margin-bottom: 10px;
        }
        .nota-section strong {
            display: inline-block;
            width: 150px;
        }
        .nota-total {
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 20px;
        }
        .nota-terbilang {
            margin-top: 10px;
            font-style: italic;
        }
        .nota-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    
<div class="nota-container">
    <div class="nota-header">
        <h1>STRUK PEMBAYARAN TAGIHAN WIFI</h1>
    </div>

    <div class="nota-section">
        <strong>Tanggal Bayar:</strong> <?php echo $billing['billing_date']; ?>
    </div>
    <div class="nota-section">
        <strong>No. Pelanggan:</strong> <?php echo $billing['customer_id']; ?>
    </div>
    <div class="nota-section">
        <strong>Nama:</strong> <?php echo $billing['first_name'] . ' ' . $billing['last_name']; ?>
    </div>
    <div class="nota-section">
        <strong>Kecepatan:</strong> <?php echo $billing['speed']; ?>
    </div>
    <div class="nota-section">
        <strong>Harga Paket:</strong> Rp. <?php echo number_format($billing['price'], 2, ',', '.'); ?>
    </div>
    <div class="nota-section">
        <strong>Admin Bank:</strong> Rp. 2.500,00
    </div>
    <div class="nota-section nota-total">
        <strong>Total:</strong> Rp. <?php echo number_format($billing['amount'] + 2500, 2, ',', '.'); ?>
    </div>
    <div class="nota-section nota-terbilang">
        <strong>Terbilang:</strong> <?php echo strtoupper(convert_number_to_words($billing['amount'] + 2500)); ?> RUPIAH
    </div>

    <div class="nota-footer">
        “Terima kasih atas kepercayaan Anda membayar melalui loket kami.” <br>
        Simpanlah struk ini sebagai bukti pembayaran Anda. Struk ini merupakan dokumen resmi.
    </div>
</div>


<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>