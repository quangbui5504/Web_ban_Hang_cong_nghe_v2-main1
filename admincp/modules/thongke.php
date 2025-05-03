<?php
include('../config/config.php');
require('../../carbon/autoload.php');

use Carbon\Carbon;

$now = Carbon::today()->format('Y-m-d');
$subdays = Carbon::today()->subDays(365)->format('Y-m-d');

if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    try {
        $start = Carbon::createFromFormat('Y-m-d', $start_date);
        $end = Carbon::createFromFormat('Y-m-d', $end_date);

        if ($start && $end && $start <= $end) {
            $subdays = $start->format('Y-m-d');
            $now = $end->format('Y-m-d');
        } else {
            $subdays = Carbon::today()->subDays(365)->format('Y-m-d');
            $now = Carbon::today()->format('Y-m-d');
        }
    } catch (Exception $e) {
        $subdays = Carbon::today()->subDays(365)->format('Y-m-d');
        $now = Carbon::today()->format('Y-m-d');
    }
}

$sql = "SELECT * FROM tbl_thongke WHERE ngaydat BETWEEN '$subdays' AND '$now' ORDER BY ngaydat ASC";
$sql_query = mysqli_query($mysqli, $sql);
$chart_data = [];

while ($val = mysqli_fetch_array($sql_query)) {
    $chart_data[] = array(
        'date' => $val['ngaydat'],
        'order' => $val['donhang'],
        'sales' => $val['doanhthu'],
        'quantity' => $val['soluongban']
    );
}

echo json_encode($chart_data);
?>