<?php
include_once 'include/connDB.php';

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id === 0) die("ไม่พบเลขที่การขาย");

$sql = "SELECT 
            p.c_ProductName, 
            p.i_Price, 
            od.i_Quantity, 
            (p.i_Price * od.i_Quantity) AS ItemTotalPrice 
        FROM tb_orderdetails od
        JOIN tb_products p ON od.i_ProductID = p.i_ProductID
        WHERE od.i_OrderID = :order_id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':order_id' => $order_id]);
$details = $stmt->fetchAll();

$totalQty = 0;
$totalPrice = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการขาย Order #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/main.css">
    <style> body { font-family: 'Kanit', sans-serif; } </style>
</head>
<body>
    <?php require_once 'include/navbar.php'; ?>

    <div class="container p-4">
        <h1 class="text-center mb-4">รายละเอียดการขายสินค้า</h1>
        <h3 class="text-center text-muted mb-4">เลขที่การขาย: <?php echo $order_id; ?></h3>

        <div class="card">
            <div class="card-header">ตารางรายละเอียดการขาย </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ชื่อสินค้า </th>
                            <th class="text-end">ราคาสินค้า </th>
                            <th class="text-end">จํานวนสินค้า </th>
                            <th class="text-end">ราคารวมทั้งหมด </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $row) : ?>
                            <?php
                            $totalQty += $row['i_Quantity'];
                            $totalPrice += $row['ItemTotalPrice'];
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['c_ProductName']); ?></td>
                                <td class="text-end"><?php echo number_format($row['i_Price'], 2); ?></td>
                                <td class="text-end"><?php echo number_format($row['i_Quantity']); ?></td>
                                <td class="text-end"><?php echo number_format($row['ItemTotalPrice'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-group-divider fw-bold">
                        <tr>
                            <td colspan="2" class="text-center">ผลรวมทั้งหมด</td>
                            <td class="text-end"><?php echo number_format($totalQty); ?></td>
                            <td class="text-end"><?php echo number_format($totalPrice, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer text-center">
                <a href="sales_summary.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> กลับไปยังหน้าตรวจสอบยอดขายสินค้า
                </a>
            </div>
        </div>
    </div>
</body>
</html>