<?php
include '../config.php';

// twilio N9F9518TUNP4UCJK5FWQ5AT9  

// Ambil data pelanggan berdasarkan customer_id
if (isset($_GET['id'])) {
    $customer_id = $_GET['id'];
    $sql = "SELECT * FROM customers WHERE customer_id = $customer_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $customer = $result->fetch_assoc();
    } else {
        echo "Customer not found";
        exit();
    }
}


// Handle proses update pelanggan
if (isset($_POST['submit'])) {
    $customer_id = $_POST['customer_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $plan_id = $_POST['plan_id'];
// var_dump($plan_id);
// die;
    $sql = "UPDATE customers SET 
            first_name = '$first_name', 
            last_name = '$last_name', 
            email = '$email', 
            phone = '$phone', 
            address = '$address' 
            WHERE customer_id = $customer_id";
    
    if ($conn->query($sql) === TRUE) {
        
        // update subscription
        $sql = "UPDATE subscriptions SET 
            plan_id = '$plan_id'
            WHERE customer_id = $customer_id";
        if ($conn->query($sql) === TRUE) {
        // Redirect ke halaman customers.php setelah berhasil update
        header("Location: customers.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
}

// Fetch plan from the database
$sql = "SELECT * FROM plans
";
$result = $conn->query($sql);

$plan = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $plan[] = $row;
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
    <link rel="stylesheet" href="styles.css"> <!-- Sesuaikan dengan file CSS Anda -->
</head>
<body>
    <div class="container">
        <h1>Edit Customer</h1>
        
        <!-- Form untuk edit pelanggan -->
        <form action="" method="POST">
            <input type="hidden" name="customer_id" value="<?php echo $customer['customer_id']; ?>">
            
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo $customer['first_name']; ?>" required><br><br>
            
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo $customer['last_name']; ?>" required><br><br>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo $customer['email']; ?>" required><br><br>
            
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo $customer['phone']; ?>" required><br><br>
            
            <label for="address">Address:</label>
            <textarea id="address" name="address"><?php echo $customer['address']; ?></textarea><br><br>

            <div class="form-group">
                <label for="plan_id">Speed Plan</label>
                <select class="form-control" id="plan_id" name="plan_id" required>
                    <?php foreach ($plan as $plans): ?>
                        <option value="<?php echo $plans['plan_id']; ?>"<?php echo ($customer['customer_id'] == $plans['plan_id']) ? 'selected' : ''; ?>>
                            <?php echo $plans['speed'] . ' - Rp. ' . number_format($plans['price'], 2, ',', '.'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" name="submit">Update Customer</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>

