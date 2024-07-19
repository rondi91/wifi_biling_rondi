<?php
include 'config.php';

// Ambil pelanggan yang aktif dan memiliki paket bulanan
$query = "SELECT c.customer_id, s.plan_id ,s.subscription_id, s.start_date
            FROM customers c
            JOIN subscriptions s on c.customer_id = s.customer_id
            WHERE s.status = 'Aktif'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $customer_id = $row['customer_id'];
        $package_id = $row['plan_id'];
        $subscription_id = $row['subscription_id'];
        $start_date = $row['start_date'];
        
        // Ambil harga paket dari tabel plans
        $package_query = "SELECT price FROM plans WHERE plan_id = ?";
        $stmt = $conn->prepare($package_query);
        $stmt->bind_param("i", $package_id);
        $stmt->execute();
        $package_result = $stmt->get_result();
        $package = $package_result->fetch_assoc();

        $amount = $package['price'];

        // Tanggal billing dan periode tagihan (misalnya bulan sebelumnya)
        $billing_date =$start_date;
        // var_dump($start_date);
        // die();
        $due_date = date('Y-m-d', strtotime('+1 month', strtotime($start_date)));
        // $due_date = date('Y-m-01', strtotime('+1 month'));
        // var_dump($due_date);
        // die();

        // Masukkan tagihan ke dalam database
        $insert_query = "INSERT INTO billing (customer_id, subscription_id, billing_date, due_date, amount, status )
                        VALUES (?,?,?, ?, ?, 'Belum Lunas')";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iisss", $customer_id,$subscription_id, $billing_date, $due_date, $amount);
        $stmt->execute();
    }
} else {
    echo "No active customers found.";
}

$conn->close();
?>
