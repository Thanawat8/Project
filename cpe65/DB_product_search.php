<?php
include_once 'include/connDB.php';
include_once 'include/elementMod.php'; // (ต้องใช้ elementMod.php ที่แก้ไขแล้ว)

// --- 1. ตั้งค่าเริ่มต้น ---
$param_catid = null;
$param_price_display = ''; // ค่าสำหรับแสดงในช่อง input
$param_price_query = 0;    // ค่าสำหรับ query
$is_searched = false;
$products = [];

// --- 2. แก้ไข: เปลี่ยนจาก $_POST เป็น $_REQUEST ---
if (isset($_REQUEST['cond_catid'])) { // <--- แก้ไข
    // ถ้ามีการค้นหา
    $param_catid = $_REQUEST['cond_catid']; // <--- แก้ไข
    $param_price_display = $_REQUEST['cond_price'] ?? ''; // <--- แก้ไข (ใช้ ?? เพื่อป้องกัน error)
    $param_price_query = !empty($param_price_display) ? (int)$param_price_display : 0; // 0 คือไม่มีขั้นต่ำ

    $sql = "SELECT tb_products.i_ProductID as pid, tb_products.c_ProductName as pname, tb_products.i_Price as pprice
            FROM tb_products
            WHERE tb_products.i_CategoryID = :param_catid
            AND tb_products.i_Price >= :param_price
            ORDER BY tb_products.i_ProductID DESC;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':param_catid' => $param_catid,
        ':param_price' => $param_price_query,
    ]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $is_searched = true; // ตั้งค่าสถานะว่าค้นหาแล้ว
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหาสินค้า</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@0,100;..&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/main.css">

    <style>
        body {
            font-family: 'Kanit', sans-serif;
            color: #000; /* กำหนดสีข้อความหลักเป็นสีดำ */
        }
        h1 {
           color: #000; /* Override ให้ H1 เป็นสีดำ */
        }
    </style>
</head>

<body>
    <?php require_once 'include/navbar.php'; ?>

    <div class="container p-4">

        <div class="text-center mb-4">
             <h1></h1>
        </div>
        <div class="row">

            <div class="col-md-3">
                 <form id="searchForm" action="DB_product_search.php" method="GET">
                    <div class="card">
                        <div class="card-header">
                            ตัวกรองการค้นหา
                        </div>
                        <div class="card-body">
                            <div class="vstack gap-3">
                                <div>
                                    <?php
                                    dropdown_db($pdo, "cond_catid", "tb_categories", "i_CategoryID", "c_CategoryName", $param_catid);
                                    ?>
                                </div>
                                <div>
                                    <input type="number" class="form-control" placeholder="ราคาสินค้าขั้นต่ำ" name="cond_price"
                                           value="<?php echo htmlspecialchars($param_price_display); ?>">
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i>&nbsp;&nbsp;ค้นหา</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div> <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        รายการสินค้าทั้งหมด

                        <?php
                            $add_url = "crud/db_product_add.php";
                            if ($param_catid !== null) {
                                $query_params = http_build_query([
                                    'cond_catid' => $param_catid,
                                    'cond_price' => $param_price_display
                                ]);
                                $add_url .= "?" . $query_params;
                            }
                        ?>
                        <a href="<?php echo $add_url; ?>" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i>&nbsp;เพิ่มสินค้าใหม่
                        </a>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>รหัสสินค้า</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>ราคาสินค้า</th>
                                    <th class="text-center">แก้ไข</th>
                                    <th class="text-center">ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($products) > 0) : ?>
                                    <?php foreach ($products as $product) : ?>
                                        <tr>
                                            <td><?php echo $product['pid']; ?></td>
                                            <td><?php echo htmlspecialchars($product['pname']); ?></td>
                                            <td><?php echo $product['pprice']; ?></td>
                                            <td class="text-center">
                                                <form action="./crud/db_product_edit.php" method="POST" class="d-inline">
                                                    <input type="hidden" name="pid" value="<?php echo $product['pid']; ?>">

                                                    <input type="hidden" name="cond_catid" value="<?php echo htmlspecialchars($param_catid); ?>">
                                                    <input type="hidden" name="cond_price" value="<?php echo htmlspecialchars($param_price_display); ?>">

                                                    <button type="submit" class="btn btn-warning text-white btn-sm">
                                                        <i class="bi bi-pen"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="prepareDelete(
                                                            <?php echo $product['pid']; ?>,
                                                            '<?php echo htmlspecialchars(addslashes($product['pname'])); ?>',
                                                            '<?php echo htmlspecialchars($param_catid); ?>',
                                                            '<?php echo htmlspecialchars($param_price_display); ?>'
                                                        )"
                                                        data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <?php echo $is_searched ? "ไม่พบข้อมูลที่ตรงกัน" : "ยังไม่มีรายการที่ค้นหา"; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> </div> </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteForm" action="./crud/db_product_delete.php" method="POST">
                    <input type="hidden" name="pid" id="delete_pid">

                    <input type="hidden" name="cond_catid" id="delete_cond_catid">
                    <input type="hidden" name="cond_price" id="delete_cond_price">

                    <div class="modal-header">
                        <h5 class="modal-title">ยืนยันการลบ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        คุณต้องการลบสินค้า "<strong id="delete_pname"></strong>" (ID: <span id="delete_pid_display"></span>) ใช่หรือไม่?
                        <p class="text-danger mt-2">การกระทำนี้ไม่สามารถกู้คืนได้</p>
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
        function prepareDelete(pid, pname, catId, price) {
            document.getElementById("delete_pid").value = pid;
            document.getElementById("delete_pid_display").innerText = pid;
            document.getElementById("delete_pname").innerText = pname;
            
            document.getElementById("delete_cond_catid").value = catId;
            document.getElementById("delete_cond_price").value = price;
        }
    </script>

    <?php require_once ''; ?>
    </body>
</html>