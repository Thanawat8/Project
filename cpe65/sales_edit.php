<?php
include_once 'include/connDB.php';
include_once 'include/funcMod.php';      // ใช้ฟังก์ชัน getEdit
include_once 'include/elementMod.php';   // ใช้ฟังก์ชัน input_text, input_dropdown

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($order_id === 0) die("ไม่พบเลขที่การขาย");

// 1. ดึงข้อมูลหลัก (tb_orders) โดยใช้ฟังก์ชัน getEdit
$order = getEdit($pdo, 'tb_orders', 'i_OrderID', $order_id);
if (!$order) die("ไม่พบข้อมูล Order ID: $order_id");

// 2. ดึงข้อมูลรอง (tb_orderdetails)
$stmt_details = $pdo->prepare("SELECT * FROM tb_orderdetails WHERE i_OrderID = :id");
$stmt_details->execute([':id' => $order_id]);
$order_details = $stmt_details->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขการขาย Order #<?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/main.css">
    <style> body { font-family: 'Kanit', sans-serif; } </style>
</head>
<body>
    <?php require_once 'include/navbar.php'; ?>

    <div class="container p-4">
        

        <form id="editForm" action="sales_action.php" method="POST">
            <input type="hidden" name="action" value="update">
            
            <div class="row">

                <div class="col-md-5">
                    <div class="card mb-4"> 
                        <div class="card-header fs-4">แก้ไขข้อมูลการขาย</div>
                        <div class="card-body">
                            <?php 
                            input_text("i_OrderID", "เลขที่การขายสินค้า", "text", $order['i_OrderID'], "");
                            echo "<script>document.getElementById('i_OrderID').readOnly = true;</script>";

                            input_text("c_OrderDate", "วันที่ขาย", "text", $order['c_OrderDate'], "");

                            input_dropdown($pdo, "i_EmployeeID", "ชื่อสกุลพนักงานขาย", "tb_employees", "i_EmployeeID", "CONCAT(c_FirstName, ' ', c_LastName)", $order['i_EmployeeID']);
                            input_dropdown($pdo, "i_CustomerID", "ชื่อสกุลลูกค้า", "tb_customers", "i_customerid", "c_customername", $order['i_CustomerID']);
                            input_dropdown($pdo, "i_ShipperID", "ชื่อบริษัทส่งสินค้า", "tb_shippers", "i_ShipperID", "c_ShipperName", $order['i_ShipperID']);
                            ?>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-footer text-center">
                            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                <i class="bi bi-floppy"></i> บันทึกการแก้ไข
                            </button>
                            <a href="sales_summary.php" class="btn btn-secondary btn-lg">ยกเลิก</a>
                        </div>
                    </div>
                </div> <div class="col-md-7">
                    <div class="card mb-4"> 
                        <div class="card-header fs-4 d-flex justify-content-between align-items-center">
                            เลือกรายการสินค้า (ไม่เกิน 10 รายการ)
                            <button type="button" class="btn btn-success btn-sm" id="addProductBtn"><i class="bi bi-plus-lg"></i> เพิ่มสินค้า</button>
                        </div>
                        <div class="card-body">
                            <div id="product-list" class="vstack gap-3">
                                
                                <?php foreach ($order_details as $index => $item) : ?>
                                <div class="row g-2 product-row">
                                    <div class="col-md-7">
                                        <?php 
                                        input_dropdown($pdo, "products[$index][i_ProductID]", "สินค้า", "tb_products", "i_ProductID", "c_ProductName", $item['i_ProductID']); 
                                        ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?php
                                        input_text("products[$index][i_Quantity]", "จำนวน", "number", $item['i_Quantity'], "");
                                        ?>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end mb-3">
                                        <button type="button" class="btn btn-danger w-100 removeProductBtn" <?php echo ($index == 0) ? 'disabled' : ''; ?>>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div> </div> </form>
    </div> <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">ยืนยันการแก้ไข</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    คุณต้องการยืนยันการแก้ไขข้อมูลนี้ใช่หรือไม่?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" form="editForm" class="btn btn-primary">ยืนยัน</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const productList = document.getElementById("product-list");
        const addProductBtn = document.getElementById("addProductBtn");
        let productRowCount = productList.querySelectorAll(".product-row").length;
        updateAddButtonState();

        addProductBtn.addEventListener("click", function() {
            if (productRowCount >= 10) {
                alert("สามารถเพิ่มสินค้าได้สูงสุด 10 รายการเท่านั้น");
                return;
            }
            
            const firstRow = productList.querySelector(".product-row");
            if (!firstRow) {
                console.error("ไม่พบ .product-row ต้นแบบ");
                return;
            }
            const newRow = firstRow.cloneNode(true);

            const selectEl = newRow.querySelector("select");
            const inputEl = newRow.querySelector("input[type=number]");
            const labels = newRow.querySelectorAll("label"); 

            const newSelectId = `product_select_${productRowCount}`;
            const newInputId = `product_input_${productRowCount}`;

            selectEl.id = newSelectId;
            selectEl.name = `products[${productRowCount}][i_ProductID]`;
            
            inputEl.id = newInputId;
            inputEl.name = `products[${productRowCount}][i_Quantity]`;

            if (labels[0]) labels[0].htmlFor = newSelectId;
            if (labels[1]) labels[1].htmlFor = newInputId;

            selectEl.selectedIndex = 0;
            inputEl.value = "";

            const removeBtn = newRow.querySelector(".removeProductBtn");
            removeBtn.disabled = false;
            removeBtn.addEventListener("click", handleRemoveClick);

            productList.appendChild(newRow);
            productRowCount++;
            updateAddButtonState();
        });

        productList.querySelectorAll(".removeProductBtn").forEach(btn => {
            btn.addEventListener("click", handleRemoveClick);
        });

        function handleRemoveClick(event) {
            if (productRowCount <= 1) {
                alert("ต้องมีสินค้าอย่างน้อย 1 รายการ");
                return;
            }
            const rowToRemove = event.target.closest(".product-row");
            if (rowToRemove) {
                rowToRemove.parentElement.removeChild(rowToRemove);
                productRowCount--;
                updateAddButtonState();
            }
        }

        function updateAddButtonState() {
            addProductBtn.disabled = (productRowCount >= 10);
            
            const allRemoveBtns = productList.querySelectorAll(".removeProductBtn");
            if (productRowCount <= 1) {
                if(allRemoveBtns[0]) allRemoveBtns[0].disabled = true;
            } else {
                allRemoveBtns.forEach(btn => btn.disabled = false);
            }
        }
    });
    </script>
</body>
</html>