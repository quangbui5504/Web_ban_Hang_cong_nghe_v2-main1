<?php
$sql_se1 = "SELECT * FROM tbl_cart";
$query_se1 = mysqli_query($mysqli, $sql_se1);

$sql_se2 = "SELECT * FROM tbl_comments";
$query_se2 = mysqli_query($mysqli, $sql_se2);

$dem = 0;
while ($row_se1 = mysqli_fetch_array($query_se1)) {
    if ($row_se1['cart_status'] == 1) {
        $dem++;
    }
}

$dem2 = 0;
while ($row_se2 = mysqli_fetch_array($query_se2)) {
    if ($row_se2['tinhtrang'] == 0) {
        $dem2++;
    }
}
?>

<div class="dashboard-container">
    <!-- Stat Cards -->
    <div class="stat-cards">
        <a href="index.php?action=quanlydonhang&query=them" class="stat-card">
            <span class="stat-label">Đơn hàng mới</span>
            <?php if ($dem > 0) { ?>
                <span class="stat-value"><?php echo $dem; ?></span>
            <?php } else { ?>
                <span class="stat-value">0</span>
            <?php } ?>
        </a>
        <a href="index.php?action=quanlycomment&query=them" class="stat-card">
            <span class="stat-label">Comment của khách hàng</span>
            <?php if ($dem2 > 0) { ?>
                <span class="stat-value"><?php echo $dem2; ?></span>
            <?php } else { ?>
                <span class="stat-value">0</span>
            <?php } ?>
        </a>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <button class="tab-button active" data-tab="orders">Đơn hàng</button>
        <button class="tab-button" data-tab="imports">Phiếu nhập</button>
        <button class="tab-button" data-tab="top-consumers">Top khách hàng</button>
    </div>

    <!-- Tab Content -->
    <div class="tab-content active" id="orders">
        <div class="thongke_donhang">
            <p>Thống kê đơn hàng từ: <input type="text" id="start-date" class="date-picker" readonly> 
               đến: <input type="text" id="end-date" class="date-picker" readonly></p>
            <p><span id="text-date"></span></p>
            <div id="chart" style="height: 500px;"></div>
        </div>
    </div>
    <div class="tab-content" id="imports" style="display: none;">
        <div class="thongke_donhang">
            <p>Thống kê phiếu nhập hàng từ: <input type="text" id="start-date1" class="date-picker" readonly> 
               đến: <input type="text" id="end-date1" class="date-picker" readonly></p>
            <p><span id="text-date1"></span></p>
            <div id="chart1" style="height: 500px;"></div>
        </div>
    </div>
    <div class="tab-content" id="top-consumers" style="display: none;">
        <div class="thongke_donhang">
            <p>Top khách hàng từ: <input type="text" id="start-date2" class="date-picker" readonly> 
               đến: <input type="text" id="end-date2" class="date-picker" readonly></p>
            <p><span id="text-date2"></span></p>
            <p>
                Hiển thị: 
                <select id="top-consumers-limit" class="select-limit">
                    <option value="5" selected>Top 5</option>
                    <option value="10">Top 10</option>
                    <option value="25">Top 25</option>
                    <option value="50">Top 50</option>
                    <option value="100">Top 100</option>
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                Sắp xếp theo tổng chi tiêu: 
                <select id="top-consumers-sort" class="select-limit">
                    <option value="desc" selected>Giảm dần</option>
                    <option value="asc">Tăng dần</option>
                </select>
            </p>
            <table id="top-consumers-table" class="consumer-table">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Số đơn hàng</th>
                        <th>Tổng chi tiêu</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div id="consumer-orders-modal" class="modal" style="display: none;">
                <div class="modal-content">
                    <span class="close-modal">×</span>
                    <h2>Chi tiết đơn hàng</h2>
                    <table id="consumer-orders-table" class="consumer-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Tổng tiền</th>
                                <th>Trạng thái</th>
                                <th>Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>