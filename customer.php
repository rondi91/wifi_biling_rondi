<?php
include 'config.php';

// Fungsi untuk mendapatkan semua data pelanggan
function getAllCustomers() {
    global $conn;
    $sql = "SELECT * FROM customers ORDER BY customer_id DESC";
    $result = $conn->query($sql);
    $customers = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $customers[] = $row;
        }
    }
    return $customers;
}

// Ambil semua pelanggan dari database
$customers = getAllCustomers();

// Handle proses tambah pelanggan
if (isset($_POST['submit'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $registration_date = date('Y-m-d'); // Tanggal registrasi hari ini

    $sql = "INSERT INTO customers (first_name, last_name, email, phone, address, registration_date) 
            VALUES ('$first_name', '$last_name', '$email', '$phone', '$address', '$registration_date')";
    
    if ($conn->query($sql) === TRUE) {
        // Refresh halaman setelah berhasil
        header("Location: customers.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle proses hapus pelanggan
if (isset($_GET['delete'])) {
    $customer_id = $_GET['delete'];
    $sql = "DELETE FROM customers WHERE customer_id = $customer_id";
    if ($conn->query($sql) === TRUE) {
        // Refresh halaman setelah berhasil
        header("Location: customers.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="stylesheet" href="styles.css"> <!-- Sesuaikan dengan file CSS Anda -->
</head>
<body>
    <div class="container">
        <h1>Customer Management</h1>
        
        <!-- Form untuk tambah pelanggan -->
        <form action="" method="POST">
            <h2>Add New Customer</h2>
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required><br><br>
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required><br><br>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required><br><br>
            <label for="address">Address:</label>
            <textarea id="address" name="address"></textarea><br><br>
            <button type="submit" name="submit">Add Customer</button>
        </form>

        <hr>

        <!-- Tabel untuk menampilkan daftar pelanggan -->
        <h2>Customers List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Registration Date</th>
                <th>Action</th>
            </tr>
            <?php foreach ($customers as $customer): ?>
            <tr>
                <td><?php echo $customer['customer_id']; ?></td>
                <td><?php echo $customer['first_name'] . ' ' . $customer['last_name']; ?></td>
                <td><?php echo $customer['email']; ?></td>
                <td><?php echo $customer['phone']; ?></td>
                <td><?php echo $customer['address']; ?></td>
                <td><?php echo $customer['registration_date']; ?></td>
                <td>
                    <a href="edit_customer.php?id=<?php echo $customer['customer_id']; ?>">Edit</a>
                    <a href="customers.php?delete=<?php echo $customer['customer_id']; ?>" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
