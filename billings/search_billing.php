<?php
include '../config.php';


function getIndonesianMonth($monthNumber) {
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    ];
    return $months[(int)$monthNumber];
}
ob_start();
// Default values for month, year, status, and search
$selected_month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$selected_status = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare SQL query based on selected filters
// Fetch billing data from the database based on filter
$sql = "SELECT b.billing_id, b.customer_id, b.billing_date, b.amount, b.status, 
               c.first_name, c.last_name, p.speed, p.price
        FROM billing b
        JOIN customers c ON b.customer_id = c.customer_id
        left JOIN subscriptions s on c.customer_id = s.customer_id
        left JOIN plans p on s.plan_id =p.plan_id
        WHERE MONTH(b.billing_date) = ? AND YEAR(b.billing_date) = ?";
$params = [$selected_month, $selected_year];
$types = 'ii';

if ($selected_status !== 'all') {
    $sql .= " AND b.status = ?";
    $params[] = $selected_status;
    $types .= 's';
}

if (!empty($search_query)) {
    $sql .= " AND (c.first_name LIKE ? OR c.last_name LIKE ?)";
    $search_query = '%' . $search_query . '%';
    $params[] = $search_query;
    $params[] = $search_query;
    $types .= 'ss';
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
// var_dump($result);
// die();
$billings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['billing_date'] = (new DateTime($row['billing_date']))->format('d') . ' ' . getIndonesianMonth((new DateTime($row['billing_date']))->format('m')) . ' ' . (new DateTime($row['billing_date']))->format('Y');
        $billings[] = $row;
    }
}
// Bersihkan buffer output sebelum mengirimkan JSON ob_end_clean(); 
echo json_encode($billings);

?>
