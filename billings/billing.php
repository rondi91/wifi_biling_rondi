<?php
include '../config.php';

// Default values for month and year
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selected_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

function getIndonesianMonth($monthNumber) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 07 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    return $months[(int)$monthNumber];
}

// Fetch billing data from the database based on filter
$sql = "SELECT b.billing_id, b.customer_id, b.billing_date, b.amount, b.status, 
               c.first_name, c.last_name, p.speed, p.price
        FROM billing b
        JOIN customers c ON b.customer_id = c.customer_id
        JOIN subscriptions s ON c.customer_id = s.plan_id
        JOIN plans p on s.plan_id = p.plan_id
        WHERE MONTH(b.billing_date) = ? AND YEAR(b.billing_date) = ?";

$params = [$selected_month, $selected_year];
$types = 'ii';

if ($selected_status !== 'all') {
    $sql .= " AND b.status = ?";
    $params[] = $selected_status;
    $types .= 's';
}
// QUERY SEARCH 
if (!empty($search_query)) {
    $sql .= " AND (c.first_name LIKE ? OR c.last_name LIKE ?)";
    $search_query = '%' . $search_query . '%';
    $params[] = $search_query;
    $params[] = $search_query;
    $types .= 'ss';

}
$stmt = $conn->prepare($sql);

// Debugging: Check if the parameters and types are correct
// var_dump($types, $params);

if ($types && $params) {
    $stmt->bind_param($types, ...$params);
}
        

$stmt->execute();
$result = $stmt->get_result();
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
    
    <form method="GET" action="" class="form-inline mb-3">
        <div class="form-group mr-2">
            <label for="month" class="mr-2">Month</label>
            <select class="form-control" id="month" name="month">
                <?php for ($m=1; $m<=12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php if ($m == $selected_month) echo 'selected'; ?>>
                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group mr-2">
            <label for="year" class="mr-2">Year</label>
            <select class="form-control" id="year" name="year">
                <?php for ($y=2020; $y<=date('Y'); $y++): ?>
                    <option value="<?php echo $y; ?>" <?php if ($y == $selected_year) echo 'selected'; ?>>
                        <?php echo $y; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group mr-2">
            <label for="status" class="mr-2">Status</label>
            <select class="form-control" id="status" name="status">
                <option value="all" <?php if ($selected_status == 'all') echo 'selected'; ?>>All</option>
                <option value="Lunas" <?php if ($selected_status == 'Lunas') echo 'selected'; ?>>Lunas</option>
                <option value="Belum Dibayar" <?php if ($selected_status == 'Belum Dibayar') echo 'selected'; ?>>Belum Dibayar</option>
            </select>
        </div>

        <!-- SEARCH  -->
        <div class="form-group mr-2">
            <label for="search" class="mr-2">Search</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="Customer Name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                </div>
                    

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

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
                    <td><?php 
                        $date = new DateTime($billing['billing_date']);
                        echo $date->format('d') . ' ' . getIndonesianMonth($date->format('m')) . ' ' . $date->format('Y'); 
                    ?></td>
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
