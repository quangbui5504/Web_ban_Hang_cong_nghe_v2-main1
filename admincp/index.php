<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Admincp</title>
    <link rel="stylesheet" href="./style.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php
    include './config/config.php';
    include './config/db.conn.php';
    include './modules/menu.php';
    include './modules/main.php';
    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="//cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script>
        let arrow = document.querySelectorAll(".arrow");
        for (var i = 0; i < arrow.length; i++) {
            arrow[i].addEventListener("click", (e) => {
                let arrowParent = e.target.parentElement.parentElement;
                arrowParent.classList.toggle("showMenu");
            });
        }
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".bx-menu");
        sidebarBtn.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });

        CKEDITOR.replace('tomtat');
        CKEDITOR.replace('noidung');
        CKEDITOR.replace('thongso');

        $(document).ready(function() {

            $("#start-date, #end-date, #start-date1, #end-date1, #start-date2, #end-date2").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true,
                maxDate: new Date(),
                beforeShow: function(input, inst) {
                    $(input).prop('readonly', true);
                },
                onSelect: function(dateText, inst) {
                    
                    var startDate = $("#start-date").datepicker("getDate");
                    var endDate = $("#end-date").datepicker("getDate");

                    if (inst.id === "start-date" && endDate && startDate < endDate) {
                        updateOrderStatistics();
                    } else if (inst.id === "end-date" && startDate && endDate > startDate) {
                        updateOrderStatistics();
                    }

                    var startDate1 = $("#start-date1").datepicker("getDate");
                    var endDate1 = $("#end-date1").datepicker("getDate");
                    if (inst.id === "start-date1" && endDate1 && startDate1 < endDate1) {
                        updateImportStatistics();
                    } else if (inst.id === "end-date1" && startDate1 && endDate1 > startDate1) {
                        updateImportStatistics();
                    }

                    var startDate2 = $("#start-date2").datepicker("getDate");
                    var endDate2 = $("#end-date2").datepicker("getDate");
                    if (inst.id === "start-date2" && endDate2 && startDate2 < endDate2) {
                        updateTopConsumers();
                    } else if (inst.id === "end-date2" && startDate2 && endDate2 > startDate2) {
                        updateTopConsumers();
                    }
                }
            });

           
            let endDate = new Date();
            let startDate = new Date();
            startDate.setDate(endDate.getDate() - 7);
            $("#start-date, #start-date1, #start-date2").datepicker("setDate", startDate);
            $("#end-date, #end-date1, #end-date2").datepicker("setDate", endDate);

            
            var char = new Morris.Area({
                element: 'chart',
                xkey: 'date',
                ykeys: ['order', 'sales', 'quantity'],
                labels: ['Đơn hàng', 'Doanh thu', 'Số lượng bán ra']
            });

            var char1 = new Morris.Bar({
                element: 'chart1',
                xkey: 'ten',
                ykeys: ['quantity'],
                labels: ['Số lượng'],
                xLabelAngle: 90,
                resize: true,
                height: 400
            });

            
            $('.tab-button').click(function() {
                $('.tab-button').removeClass('active');
                $(this).addClass('active');
                $('.tab-content').removeClass('active').hide();
                $('#' + $(this).data('tab')).addClass('active').show();
            });

            
            function updateOrderStatistics() {
                var startDate = $("#start-date").val();
                var endDate = $("#end-date").val();
                var text = `Từ ${startDate} đến ${endDate}`;
                $('#text-date').text(text);
                $.ajax({
                    url: "modules/thongke.php",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(data) {
                        if (data.length === 0) {
                            $('#text-date').text("Không có dữ liệu trong khoảng thời gian này");
                            char.setData([]);
                            return;
                        }
                        char.setData(data);
                        $('#text-date').text(text);
                    }
                });
            }

            
            function updateImportStatistics() {
                var startDate = $("#start-date1").val();
                var endDate = $("#end-date1").val();
                var text = `Từ ${startDate} đến ${endDate}`;
                $('#text-date1').text(text);
                $.ajax({
                    url: "modules/thongkephieunhap.php",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(data) {
                        if (data.length === 0) {
                            $('#text-date1').text("Không có dữ liệu trong khoảng thời gian này");
                            char1.setData([]);
                            return;
                        }
                        char1.setData(data);
                        $('#text-date1').text(text);
                    }
                });
            }

            function updateTopConsumers() {
                var startDate = $("#start-date2").val();
                var endDate = $("#end-date2").val();
                var limit = $("#top-consumers-limit").val();
                var sortOrder = $("#top-consumers-sort").val();
                if (!startDate || !endDate) {
                    return;
                }
                var text = `Từ ${startDate} đến ${endDate}`;
                $('#text-date2').text(text);
                $.ajax({
                    url: "modules/top_consumers.php",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                        limit: limit,
                        sort_order: sortOrder
                    },
                    success: function(data) {
                        var tbody = $("#top-consumers-table tbody");
                        tbody.empty();
                        if (data.error) {
                            tbody.append(`<tr><td colspan="5">Lỗi: ${data.error}</td></tr>`);
                        } else if (data.length === 0) {
                            tbody.append('<tr><td colspan="5">Không có dữ liệu khách hàng trong khoảng thời gian này</td></tr>');
                        } else {
                            $.each(data, function(index, consumer) {
                                tbody.append(`
                                    <tr>
                                        <td>${consumer.fullname}</td>
                                        <td>${consumer.email}</td>
                                        <td>${consumer.order_count}</td>
                                        <td>${consumer.total_spent.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</td>
                                        <td><button class="detail-btn" data-id-khachhang="${consumer.id_user}">Chi tiết</button></td>
                                    </tr>
                                `);
                            });
                        }
                    }
                });
            }

            $("#top-consumers-limit, #top-consumers-sort").change(function() {
                updateTopConsumers();
            });

            $(document).on("click", ".detail-btn", function() {
                var idKhachhang = $(this).data("id-khachhang");
                var startDate = $("#start-date2").val();
                var endDate = $("#end-date2").val();
                $.ajax({
                    url: "modules/consumer_orders.php",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        id_khachhang: idKhachhang,
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(data) {
                        var tbody = $("#consumer-orders-table tbody");
                        tbody.empty();
                        if (data.length === 0) {
                            tbody.append('<tr><td colspan="5">Không có đơn hàng nào</td></tr>');
                        } else {
                            $.each(data, function(index, order) {
                                var status = order.cart_status == 0 ? "Hoàn thành" : "Chưa xử lý";
                                tbody.append(`
                                    <tr>
                                        <td>${order.id_cart}</td>
                                        <td>${order.cart_date}</td>
                                        <td>${order.total_price.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</td>
                                        <td>${status}</td>
                                        <td><button class="view-order-btn"><a href="index.php?action=donhang&query=xemdonhang&id_cart=${order.id_cart}">Xem chi tiết</a></button></td>
                                    </tr>
                                `);
                            });
                        }
                        $("#consumer-orders-modal").show();
                    }
                });
            });

            $(".close-modal").click(function() {
                $("#consumer-orders-modal").hide();
            });

            updateOrderStatistics();
            updateImportStatistics();
            updateTopConsumers();
        });
    </script>
</body>
</html>