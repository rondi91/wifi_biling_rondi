<?php
include '../config.php';
include 'convert_number_to_words.php';

if (isset($_GET['payment_id'])) {
    $payment_id = $_GET['payment_id'];
    

    // Ambil data transaksi berdasarkan billing_id
    $sql = "SELECT pay.payment_id, c.first_name, c.last_name,c.customer_id, b.billing_date, b.due_date, pay.payment_method, pay.amount, pay.payment_date, p.speed, p.price
            FROM payments pay 
            left JOIN billing b on pay.billing_id = b.billing_id
            left JOIN customers c on b.customer_id = c.customer_id
            JOIN subscriptions s on c.customer_id = s.customer_id
            JOIN plans p on s.plan_id = p.plan_id
            WHERE pay.payment_id = $payment_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $payment = $result->fetch_assoc();
    } else {
        echo "payment record not found";
        exit();
    }
} else {
    echo "No payment ID provided";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Nota</title>
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
        .print-button {
            margin-top: 20px;
        }
    </style>
    <script>
        function printNota() {
            window.print();
        }

        function shareToWhatsApp() {
            const paymentId = "<?php echo $payment['payment_id']; ?>";
            const customerName = "<?php echo $payment['first_name'] . ' ' . $payment['last_name']; ?>";
            const billingDate = "<?php echo date('d F Y', strtotime($payment['billing_date'])); ?>";
            const billingPeriod = "<?php echo date('F Y', strtotime($payment['due_date'])); ?>";
            const paymentMethod = "<?php echo $payment['payment_method']; ?>";
            const amount = "<?php echo number_format($payment['amount'], 2, ',', '.'); ?>";
            const paymentDate = "<?php echo date('d F Y H:i:s', strtotime($payment['payment_date'])); ?>";

            const message = `Payment Receipt\n\nPayment ID: ${paymentId}\nCustomer Name: ${customerName}\nBilling Date: ${billingDate}\nBilling Period: ${billingPeriod}\nPayment Method: ${paymentMethod}\nAmount: Rp. ${amount}\nPayment Date: ${paymentDate}\nStatus: Lunas\n\nTerima kasih atas pembayaran Anda!`;

            const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
            window.open(whatsappUrl, '_blank');
        }
    </script>
</head>
<body>
    
<div class="nota-container">
    <div class="nota-header">
        <h1>STRUK PEMBAYARAN TAGIHAN WIFI</h1>
    </div>

    <div class="nota-section">
        <strong>Tanggal Bayar:</strong> <?php echo $payment['payment_date']; ?>
    </div>
    <div class="nota-section">
        <strong>No. Pelanggan:</strong> <?php echo $payment['customer_id']; ?>
    </div>
    <div class="nota-section">
        <strong>Nama:</strong> <?php echo $payment['first_name'] . ' ' . $payment['last_name']; ?>
    </div>
    <div class="nota-section">
        <strong>Kecepatan:</strong> <?php echo $payment['speed']; ?>
    </div>
    <div class="nota-section">
        <strong>Harga Paket:</strong> Rp. <?php echo number_format($payment['price'], 2, ',', '.'); ?>
    </div>
    <div class="nota-section">
        <strong>Admin Bank:</strong> Rp. 2.500,00
    </div>
    <div class="nota-section nota-total">
        <strong>Total:</strong> Rp. <?php echo number_format($payment['amount'] + 2500, 2, ',', '.'); ?>
    </div>
    <div class="nota-section nota-terbilang">
        <strong>Terbilang:</strong> <?php echo strtoupper(convert_number_to_words($payment['amount'] + 2500)); ?> RUPIAH
    </div>

    <div class="nota-footer">
        “Terima kasih atas kepercayaan Anda membayar melalui loket kami.” <br>
        Simpanlah struk ini sebagai bukti pembayaran Anda. Struk ini merupakan dokumen resmi.
    </div>
    
    </div>
    <div class="container">

        <!-- Tombol Print -->
        <button class="btn btn-primary print-button" onclick="printNota()">Print Nota</button>
        
        <!-- Tombol Share ke WhatsApp -->
        <img src="whatsapp_icon.svg" alt="Share to WhatsApp" class="share-button" onclick="shareToWhatsApp()" style="cursor: pointer; width: 120px; height: 120px;">
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