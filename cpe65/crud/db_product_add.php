<?php
include_once '../include/connDB.php';
include_once '../include/elementMod.php'; // (ต้องใช้ elementMod.php ที่แก้ไขแล้ว)

// === 1. รับค่าค้นหาจาก URL (GET) ===
$param_catid = $_GET['cond_catid'] ?? null;
$param_price = $_GET['cond_price'] ?? '';

// === 4. สร้าง URL สำหรับปุ่ม "ยกเลิก" ===
$cancel_url = "../DB_product_search.php";
if ($param_catid !== null) {
    $query_params = http_build_query([
        'cond_catid' => $param_catid,
        'cond_price' => $param_price
    ]);
    $cancel_url .= "?" . $query_params;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มสินค้าใหม่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@0,100;..&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../css/main.css"> 
    
    <style>
        body { font-family: 'Kanit', sans-serif; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3 class="mb-0">เพิ่มสินค้าใหม่</h3>
            </div>
            <div class="card-body">
                <form id="addForm" action="db_product_action.php" method="post">
                    <input type="hidden" name="action" value="create">
                    
                    <input type="hidden" name="cond_catid" value="<?php echo htmlspecialchars($param_catid); ?>">
                    <input type="hidden" name="cond_price" value="<?php echo htmlspecialchars($param_price); ?>">
                    
                    <?php 
                    // ใช้ฟังก์ชัน input_text สำหรับกรอกข้อมูลใหม่
                    echo input_text("c_ProductName", "ชื่อสินค้า", "text", "", "กรุณากรอกชื่อสินค้า");
                    echo input_text("c_Unit", "หน่วยนับสินค้า", "text", "", "เช่น 10 boxes x 20 bags");
                    echo input_text("i_Price", "ราคาสินค้า", "number", "", "กรุณากรอกราคาสินค้า");

                    // === 5. (โบนัส) ส่ง $param_catid เป็นค่าที่ถูกเลือก ===
                    echo input_dropdown($pdo, "i_CategoryID", "หมวดหมู่สินค้า", "tb_categories", "i_CategoryID", "c_CategoryName", $param_catid);
                    echo input_dropdown($pdo, "i_SupplierID", "ผู้ผลิตสินค้า", "tb_suppliers", "i_SupplierID", "c_SupplierName", null);
                    ?>

                    <hr>
                    <div class="text-end">
                        <a href="<?php echo $cancel_url; ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i>&nbsp;ยกเลิก
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#confirmModal">
                            <i class="bi bi-floppy"></i>&nbsp;บันทึกข้อมูล
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
                    <h5 class="modal-title">ยืนยันการบันทึก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    คุณต้องการบันทึกสินค้าใหม่นี้ใช่หรือไม่?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" form="addForm" class="btn btn-primary">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>