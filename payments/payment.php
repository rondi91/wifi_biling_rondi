<?php
include '../config.php';
function getIndonesianMonth($monthNumber) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $months[(int)$monthNumber];
}

if (isset($_GET['billing_id'])) {
    $billing_id = $_GET['billing_id'];

    // Ambil informasi tagihan berdasarkan billing_id
    $stmt = $conn->prepare("SELECT b.*, c.first_name, c.last_name, p.speed, p.price
                            FROM billing b
                            JOIN customers c ON b.customer_id = c.customer_id
                            left JOIN subscriptions s on c.customer_id = s.customer_id
                            left JOIN plans p on s.plan_id =p.plan_id
                            WHERE b.billing_id = ?");
    $stmt->bind_param("i", $billing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $billing = $result->fetch_assoc();
    // var_dump($billing_id);
    // die();
    
    if ($billing) {
        // Proses pembayaran logika disini
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $payment_method = $_POST['payment_method'];
            $amount = $billing['amount'];
            $payment_date = date('Y-m-d H:i:s'); // Mendapatkan tanggal dan waktu saat ini

            // Simpan detail pembayaran ke tabel payments
            $payment_stmt = $conn->prepare("INSERT INTO payments (billing_id, payment_method, amount, payment_date) VALUES (?, ?, ?, ?)");
            $payment_stmt->bind_param("isss", $billing_id, $payment_method, $amount, $payment_date);
            $payment_stmt->execute();
            $payment_id = $conn->insert_id; // Dapatkan ID pembayaran terakhir yang dimasukkan

            // Update status tagihan menjadi lunas
            $update_stmt = $conn->prepare("UPDATE billing SET status = 'Lunas' WHERE billing_id = ?");
            $update_stmt->bind_param("i", $billing_id);
            $update_stmt->execute();
            

            // Redirect ke halaman billing
            header("Location: ../customer/nota.php?payment_id=$payment_id");
            // header("Location: ../billings/billing.php");       
            exit();
        }
    } else {
        echo "Billing not found!";
        exit();
    }
} else {
    echo "Billing ID not provided!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h1>Payment for Billing ID: <?php echo $billing['billing_id']; ?></h1>
    <p>Customer Name: <?php echo $billing['first_name'] . ' ' . $billing['last_name']; ?></p>
    <p>Speed: <?php echo $billing['speed']; ?> Mbps</p>
    <p>Price: Rp. <?php echo number_format($billing['price'], 2, ',', '.'); ?></p>
    <p>Billing Date: <?php 
        $date = new DateTime($billing['billing_date']);
        echo $date->format('d') . ' ' . getIndonesianMonth($date->format('m')) . ' ' . $date->format('Y'); 
    ?></p>
    <p>Amount: Rp. <?php echo number_format($billing['amount'], 2, ',', '.'); ?></p>
    <p>Status: <?php echo $billing['status']; ?></p>

    <?php if ($billing['status'] == 'Belum Lunas'): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="Kartu Kredit">Kartu Kredit</option>
                    <option value="E-Wallet">E-Wallet</option>
                    <option value="Tunai">Tunai</option>
                </select>
            </div>
            <p>Total Payment: Rp. <?php echo number_format($billing['amount'], 2, ',', '.'); ?></p>
            <button type="submit" class="btn btn-success">Bayar</button>
        </form>
    <?php else: ?>
        <p>Pembayaran sudah dilakukan.</p>
    <?php endif; ?>
</div>
</body>
</html>
