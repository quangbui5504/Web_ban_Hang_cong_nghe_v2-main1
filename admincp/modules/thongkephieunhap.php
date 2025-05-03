<?php
include('../config/config.php');
require('../../carbon/autoload.php');

use Carbon\Carbon;

$now = Carbon::today()->format('Y-m-d');
$subdays = Carbon::today()->subDays(7)->format('Y-m-d'); // Default to last 7 days

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

$sql = "SELECT * FROM tbl_sanpham";
$sql_query = mysqli_query($mysqli, $sql);
$listsp = array();
$chart_data = [];

while ($val = mysqli_fetch_array($sql_query)) {
    $chart_data[] = array(
        'id' => $val['id_sanpham'],
        'ten' => $val['tensanpham'],
        'quantity' => 0
    );
}
$listsp = $chart_data;

$sql = "SELECT * FROM tbl_phieunhap WHERE DATE(thoigian) BETWEEN '$subdays' AND '$now' ORDER BY DATE(thoigian) ASC";
$sql_query = mysqli_query($mysqli, $sql);
while ($val = mysqli_fetch_array($sql_query)) {
    $idtmp = $val['id_phieunhap'];
    $sql1 = "SELECT * FROM tbl_chitietphieunhap WHERE id_phieunhap = $idtmp";
    $sql_query1 = mysqli_query($mysqli, $sql1);
    while ($val1 = mysqli_fetch_array($sql_query1)) {
        foreach ($listsp as &$item) {
            if ($item['id'] == $val1['id_sanpham']) {
                $item['quantity'] += $val1['soluongnhap'];
                break; 
            }
        }
    }
}

foreach ($listsp as &$item) {
    unset($item['id']);
}
echo json_encode($listsp);
?>