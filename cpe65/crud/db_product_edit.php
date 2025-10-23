<?php
include_once '../include/connDB.php';
include_once '../include/funcMod.php';
include_once '../include/elementMod.php'; // (ต้องใช้ elementMod.php ที่แก้ไขแล้ว)

// ดึงข้อมูลเดิมมาแสดง
$data = getEdit($pdo, 'tb_products', 'i_ProductID', $_POST['pid']);
// print_r($data);

// === โค้ดที่เพิ่ม: รับค่าค้นหามาสร้าง URL ===
$param_catid = $_POST['cond_catid'] ?? null;
$param_price = $_POST['cond_price'] ?? '';

$cancel_url = "../DB_product_search.php"; // URL ปุ่มยกเลิก
if ($param_catid !== null) {
    // ถ้ามีค่าค้นหา ให้แนบกลับไปกับ URL
    $cancel_url .= "?cond_catid=" . urlencode($param_catid) . "&cond_price=" . urlencode($param_price);
}
// === จบส่วนที่เพิ่ม ===

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@0,100;..&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/main.css">
    <style>
        body { font-family: 'Kanit', sans-serif; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h3 class="mb-0">แก้ไขสินค้า (ID: <?php echo htmlspecialchars($data["i_ProductID"]); ?>)</h3>
            </div>
            <div class="card-body">
                <form id="editForm" action="db_product_action.php" method="post">
                    <input type="hidden" name="action" value="update">

                    <input type="hidden" name="cond_catid" value="<?php echo htmlspecialchars($param_catid); ?>">
                    <input type="hidden" name="cond_price" value="<?php echo htmlspecialchars($param_price); ?>">
                    <?php 
                    // 2. แก้ไข: ทำให้ ProductID เป็น Readonly
                    echo input_text("i_ProductID", "รหัสสินค้า", "text", $data["i_ProductID"], "");
                    echo "<script>document.getElementById('i_ProductID').readOnly = true;</script>";

                    echo input_text("c_ProductName", "ชื่อสินค้า", "text", $data["c_ProductName"], "กรุณากรอกชื่อสินค้า");
                    echo input_text("c_Unit", "หน่วยนับสินค้า", "text", $data["c_Unit"], "กรุณากรอกหน่วยนับสินค้า");
                    echo input_text("i_Price", "ราคาสินค้า", "number", $data["i_Price"], "กรุณากรอกราคาสินค้า");
                    
                    echo input_dropdown($pdo, "i_CategoryID", "หมวดหมู่สินค้า", "tb_categories", "i_CategoryID", "c_CategoryName", $data["i_CategoryID"]);
                    echo input_dropdown($pdo, "i_SupplierID", "ผู้ผลิตสินค้า", "tb_suppliers", "i_SupplierID", "c_SupplierName", $data["i_SupplierID"]);
                    ?>
                    
                    <hr>
                    <div class="text-end">
                         <a href="<?php echo $cancel_url; ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>&nbsp;ยกเลิก
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmModal">
                            <i class="bi bi-floppy"></i>&nbsp;บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ยืนยันการแก้ไข</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    คุณต้องการบันทึกการแก้ไขข้อมูลนี้ใช่หรือไม่?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" form="editForm" class="btn btn-primary">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>