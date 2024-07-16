<?php
include '../config.php';
include 'search_billing.php';
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
    
    <form method="GET" action="" class="form-inline mb-3" id="filter-form">
        <div class="form-group mr-2">
            <label for="month" class="mr-2">Month</label>
            <select class="form-control" id="month" name="month">
                <?php for ($m=1; $m<=12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php if ($m == $selected_month) echo 'selected'; ?>>
                        <?php echo getIndonesianMonth($m); ?>
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
                <option value="Belum Lunas" <?php if ($selected_status == 'Belum Lunas') echo 'selected'; ?>>Belum Lunas</option>
            </select>
        </div>
        <div class="form-group mr-2">
            <label for="search" class="mr-2">Search</label>
            <input type="text" class="form-control" id="search" name="search" placeholder="Customer Name" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    <table class="table table-striped" id="billing-table">
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
$(document).ready(function(){
    $("#search").on("input", function() {
        var searchQuery = $(this).val();
        var month = $("#month").val();
        var year = $("#year").val();
        var status = $("#status").val();
        
        $.ajax({
            url: "search_billing.php",
            method: "GET",
            data: {
                search: searchQuery,
                month: month,
                year: year,
                status: status
            },
            success: function(data) {
                // console.log(data); // Debugging: Lihat respons dari server
                try {
                    var billings = JSON.parse(data);
                    var rows = '';
                    billings.forEach(function(billing) {
                        rows += '<tr>' +
                            '<td>' + billing.billing_id + '</td>' +
                            '<td>' + billing.first_name + ' ' + billing.last_name + '</td>' +
                            '<td>' + billing.speed + '</td>' +
                            '<td>Rp. ' + Number(billing.price).toLocaleString('id-ID', {minimumFractionDigits: 2}) + '</td>' +
                            '<td>' + billing.billing_date + '</td>' +
                            '<td>Rp. ' + Number(billing.amount).toLocaleString('id-ID', {minimumFractionDigits: 2}) + '</td>' +
                            '<td>' + billing.status + '</td>' +
                        '</tr>';
                    });
                    $("#billing-table tbody").html(rows);
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
            }
        });
    });
});
</script>

</body>
</html>
