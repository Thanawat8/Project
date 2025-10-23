<?php
include_once 'include/connDB.php';

// Query หลักสำหรับตารางสรุปรายการขาย
$sql = "SELECT 
            o.i_OrderID,
            o.c_OrderDate,
            CONCAT(e.c_FirstName, ' ', e.c_LastName) AS EmployeeName, --
            c.c_customername AS CustomerName, --
            SUM(od.i_Quantity) AS TotalQuantity, --
            SUM(od.i_Quantity * p.i_Price) AS TotalPrice --
        FROM tb_orders o
        JOIN tb_employees e ON o.i_EmployeeID = e.i_EmployeeID
        JOIN tb_customers c ON o.i_CustomerID = c.i_customerid -- (ใช้ i_customerid ตาม .sql)
        JOIN tb_orderdetails od ON o.i_OrderID = od.i_OrderID
        JOIN tb_products p ON od.i_ProductID = p.i_ProductID
        GROUP BY o.i_OrderID, o.c_OrderDate, EmployeeName, CustomerName
        ORDER BY o.i_OrderID DESC";

$stmt = $pdo->query($sql);
$sales_data = $stmt->fetchAll();

$grandTotalQty = 0;
$grandTotalPrice = 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าจอตรวจสอบยอดขายสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/main.css">
    
    <style> 
        body { font-family: 'Kanit', sans-serif; } 
        
        .content-wrapper {
            max-width: 1400px; 
            margin-left: auto;  
            margin-right: auto; 
        }
        

        .table th, .table td {
            vertical-align: middle; 
            font-size: 1.05rem;     
            padding-left: 0.5rem;   
            padding-right: 0.5rem; 
        }
        
        .col-customer-name {
            padding-right: 0; 
        }
        
        .col-quantity {
            padding-left: 0; 
        }
    </style>
</head>
<body>
    <?php require_once 'include/navbar.php'; ?>

    <div class="container-fluid p-4 content-wrapper">
    <h1 class="text-center mb-4"></h1>

        <div class="card">
            <div class="card-header fs-4">ตารางสรุปยอดขายสินค้า</div>
            <div class="card-body table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">เลขที่การขาย</th>
                            <th class="text-center">วันที่ขาย</th>
                            <th class="text-start">ชื่อ-สกุลพนักงานขาย</th>
                            <th class="text-start col-customer-name">ชื่อ-นามสกุลลูกค้า</th>
                            <th class="text-end col-quantity">จำนวนสินค้ารวม</th>
                            <th class="text-end">ราคารวมทั้งหมด</th>
                            <th class="text-center">รายละเอียดการขาย</th>
                            <th class="text-center">แก้ไข 
                            </th>
                            <th class="text-center">ลบ </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales_data as $row) : ?>
                            <?php
                            $grandTotalQty += $row['TotalQuantity'];
                            $grandTotalPrice += $row['TotalPrice'];
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $row['i_OrderID']; ?></td>
                                <td class="text-center"><?php echo $row['c_OrderDate']; ?></td>
                                <td class="text-start"><?php echo htmlspecialchars($row['EmployeeName']); ?></td> 
                                <td class="text-start col-customer-name"><?php echo htmlspecialchars($row['CustomerName']); ?></td>
                                <td class="col-quantity text-end"><?php echo number_format($row['TotalQuantity']); ?></td>
                                <td class="text-end"><?php echo number_format($row['TotalPrice'], 2); ?></td> 
                                <td class="text-center"> 
                                    <a href="sales_detail.php?order_id=<?php echo $row['i_OrderID']; ?>" class="btn btn-info btn-sm text-white">
                                        <i class="bi bi-search"></i>
                                    </a>
                                </td>
                                <td class="text-center"> 
                                    <a href="sales_edit.php?order_id=<?php echo $row['i_OrderID']; ?>" class="btn btn-warning btn-sm text-white">
                                        <i class="bi bi-pen"></i>
                                    </a>
                                </td>
                                <td class="text-center"> 
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="prepareDelete(<?php echo $row['i_OrderID']; ?>)"
                                            data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-group-divider fw-bold">
                        <tr>
                            <td colspan="4" class="text-center">ผลรวมทั้งหมด</td>
                            <td class="col-quantity text-end"><?php echo number_format($grandTotalQty); ?></td>
                            <td class="text-end"><?php echo number_format($grandTotalPrice, 2); ?></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteForm" action="sales_action.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="order_id" id="delete_order_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">ยืนยันการลบ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        คุณต้องการลบรายการขายนี้ (OrderID: <span id="display_order_id"></span>) ใช่หรือไม่? <br>
                        <strong class="text-danger">การกระทำนี้จะลบข้อมูลและไม่สามารถกู้คืนได้</strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-danger">ยืนยันการลบ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function prepareDelete(orderId) {
            document.getElementById("delete_order_id").value = orderId;
            document.getElementById("display_order_id").innerText = orderId;
        }
    </script>
</body>
</html>