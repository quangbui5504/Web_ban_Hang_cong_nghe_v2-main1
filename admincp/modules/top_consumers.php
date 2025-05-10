<?php
include('../config/config.php');
require('../../carbon/autoload.php');

use Carbon\Carbon;

$now = Carbon::today()->format('Y-m-d');
$subdays = Carbon::today()->subDays(7)->format('Y-m-d');
$limit = 5;
$sort_order = 'DESC';

if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    try {
        $start = Carbon::createFromFormat('Y-m-d', $start_date);
        $end = Carbon::createFromFormat('Y-m-d', $end_date);

        if ($start && $end && $start <= $end) {
            $subdays = $start->format('Y-m-d');
            $now = $end->format('Y-m-d');
        }
    } catch (Exception $e) {
        $subdays = Carbon::today()->subDays(7)->format('Y-m-d');
        $now = Carbon::today()->format('Y-m-d');
    }
}

if (isset($_POST['limit']) && in_array($_POST['limit'], [5, 10, 25, 50, 100])) {
    $limit = (int)$_POST['limit'];
}

if (isset($_POST['sort_order']) && in_array(strtoupper($_POST['sort_order']), ['ASC', 'DESC'])) {
    $sort_order = strtoupper($_POST['sort_order']);
}

$sql = "
    SELECT 
        u.id_user,
        u.fullname,
        u.email,
        COUNT(DISTINCT c.id_cart) as order_count,
        SUM(cd.soluongmua * sp.giasp) as total_spent
    FROM tbl_user u
    JOIN tbl_cart c ON u.id_user = c.id_khachhang
    JOIN tbl_cart_details cd ON c.id_cart = cd.id_cart_details
    JOIN tbl_sanpham sp ON cd.id_sanpham = sp.id_sanpham
    WHERE c.cart_date BETWEEN ? AND ?
    AND c.cart_status = 0
    GROUP BY u.id_user, u.fullname, u.email
    ORDER BY total_spent $sort_order
    LIMIT ?
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Database query preparation failed']);
    exit;
}

$stmt->bind_param("ssi", $subdays, $now, $limit);
$stmt->execute();
$result = $stmt->get_result();
$consumers = [];

while ($row = $result->fetch_assoc()) {
    $consumers[] = [
        'id_user' => $row['id_user'],
        'fullname' => $row['fullname'],
        'email' => $row['email'],
        'order_count' => $row['order_count'],
        'total_spent' => $row['total_spent']
    ];
}

$stmt->close();
echo json_encode($consumers);
?>