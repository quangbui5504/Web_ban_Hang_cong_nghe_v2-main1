<?php
include('../config/config.php');
require('../../carbon/autoload.php');

use Carbon\Carbon;

$now = Carbon::today()->format('Y-m-d');
$subdays = Carbon::today()->subDays(7)->format('Y-m-d');
$id_khachhang = isset($_POST['id_khachhang']) ? (int)$_POST['id_khachhang'] : 0;

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

$sql = "
    SELECT 
        c.id_cart,
        c.cart_date,
        c.cart_status,
        SUM(cd.soluongmua * sp.giasp) as total_price
    FROM tbl_cart c
    JOIN tbl_cart_details cd ON c.id_cart = cd.id_cart_details
    JOIN tbl_sanpham sp ON cd.id_sanpham = sp.id_sanpham
    WHERE c.id_khachhang = ?
    AND c.cart_date BETWEEN ? AND ?
    AND c.cart_status = 0
    GROUP BY c.id_cart, c.cart_date, c.cart_status
    ORDER BY c.cart_date DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iss", $id_khachhang, $subdays, $now);
$stmt->execute();
$result = $stmt->get_result();
$orders = [];

while ($row = $result->fetch_assoc()) {
    $orders[] = [
        'id_cart' => $row['id_cart'],
        'cart_date' => $row['cart_date'],
        'total_price' => $row['total_price'],
        'cart_status' => $row['cart_status']
    ];
}

$stmt->close();
echo json_encode($orders);
?>