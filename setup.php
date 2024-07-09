<?php
include 'config.php';

// Buat tabel-tabel
$sql_tables = "
CREATE TABLE IF NOT EXISTS customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(255),
    registration_date DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS plans (
    plan_id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(255) NOT NULL,
    speed VARCHAR(50) NOT NULL,
    data_limit VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL
);

CREATE TABLE IF NOT EXISTS subscriptions (
    subscription_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    plan_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(50) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (plan_id) REFERENCES plans(plan_id)
);

CREATE TABLE IF NOT EXISTS usage (
    usage_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    date DATE NOT NULL,
    data_used DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

CREATE TABLE IF NOT EXISTS billing (
    billing_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    subscription_id INT NOT NULL,
    billing_date DATE NOT NULL,
    due_date DATE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id),
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(subscription_id)
);

CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    billing_id INT NOT NULL,
    payment_date DATE NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    FOREIGN KEY (billing_id) REFERENCES billing(billing_id)
);
";

if ($conn->multi_query($sql_tables)) {
    do {
        // Skip all results
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "Tables created successfully<br>";
} else {
    echo "Error creating tables: " . $conn->error;
    $conn->close();
    exit;
}

// Menambahkan data dummy
$sql_customers = "
INSERT INTO customers (first_name, last_name, email, phone, address, registration_date) VALUES
('Alice', 'Smith', 'alice.smith@example.com', '1234567891', '123 Maple Street', '2023-01-01'),
('Bob', 'Johnson', 'bob.johnson@example.com', '1234567892', '456 Oak Street', '2023-02-01'),
('Carol', 'Williams', 'carol.williams@example.com', '1234567893', '789 Pine Street', '2023-03-01'),
('David', 'Brown', 'david.brown@example.com', '1234567894', '101 Cedar Street', '2023-04-01'),
('Eve', 'Davis', 'eve.davis@example.com', '1234567895', '202 Birch Street', '2023-05-01');
";

$sql_plans = "
INSERT INTO plans (plan_name, speed, data_limit, price) VALUES
('Basic Plan', '50 Mbps', '500 GB', 300000.00),
('Standard Plan', '100 Mbps', '1000 GB', 500000.00),
('Premium Plan', '200 Mbps', 'Unlimited', 700000.00),
('Student Plan', '25 Mbps', '200 GB', 150000.00),
('Business Plan', '500 Mbps', 'Unlimited', 1000000.00);
";

$sql_subscriptions = "
INSERT INTO subscriptions (customer_id, plan_id, start_date, end_date, status) VALUES
(1, 1, '2023-01-01', '2024-01-01', 'Aktif'),
(2, 2, '2023-02-01', '2024-02-01', 'Aktif'),
(3, 3, '2023-03-01', '2024-03-01', 'Aktif'),
(4, 4, '2023-04-01', '2024-04-01', 'Aktif'),
(5, 5, '2023-05-01', '2024-05-01', 'Aktif');
";

$sql_usage = "
INSERT INTO usage (customer_id, date, data_used) VALUES
(1, '2023-06-01', 100.50),
(2, '2023-06-01', 200.75),
(3, '2023-06-01', 150.00),
(4, '2023-06-01', 50.25),
(5, '2023-06-01', 300.10);
";

$sql_billing = "
INSERT INTO billing (customer_id, subscription_id, billing_date, due_date, amount, status) VALUES
(1, 1, '2023-07-01', '2023-07-31', 300000.00, 'Belum Dibayar'),
(2, 2, '2023-07-01', '2023-07-31', 500000.00, 'Belum Dibayar'),
(3, 3, '2023-07-01', '2023-07-31', 700000.00, 'Belum Dibayar'),
(4, 4, '2023-07-01', '2023-07-31', 150000.00, 'Belum Dibayar'),
(5, 5, '2023-07-01', '2023-07-31', 1000000.00, 'Belum Dibayar');
";

$sql_payments = "
INSERT INTO payments (billing_id, payment_date, amount, payment_method) VALUES
(1, '2023-07-15', 300000.00, 'Kartu Kredit'),
(2, '2023-07-16', 500000.00, 'Transfer Bank'),
(3, '2023-07-17', 700000.00, 'Kartu Kredit'),
(4, '2023-07-18', 150000.00, 'Transfer Bank'),
(5, '2023-07-19', 1000000.00, 'Kartu Kredit');
";

// Fungsi untuk menjalankan query dan menangani hasilnya
function executeQuery($conn, $sql, $description) {
    if ($conn->query($sql) === TRUE) {
        echo "$description inserted successfully<br>";
    } else {
        echo "Error inserting $description: " . $conn->error . "<br>";
    }
}

// Jalankan query untuk menambahkan data dummy
executeQuery($conn, $sql_customers, "Customers");
executeQuery($conn, $sql_plans, "Plans");
executeQuery($conn, $sql_subscriptions, "Subscriptions");
executeQuery($conn, $sql_usage, "Usage");
executeQuery($conn, $sql_billing, "Billing");
executeQuery($conn, $sql_payments, "Payments");

$conn->close();
?>
