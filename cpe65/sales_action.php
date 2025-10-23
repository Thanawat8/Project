<?php
include_once 'include/connDB.php';

if (!isset($_POST['action'])) {
    die("ไม่พบการดำเนินการ");
}

$action = $_POST['action'];

switch ($action) {

    // ############ CREATE ############
    case 'create':
        // [cite: 46]
        $order_id = $_POST['i_OrderID'];
        $customer_id = $_POST['i_CustomerID'];
        $employee_id = $_POST['i_EmployeeID'];
        $order_date = $_POST['c_OrderDate'];
        $shipper_id = $_POST['i_ShipperID'];
        $products = $_POST['products'];

        $pdo->beginTransaction();
        try {
            // 1. INSERT tb_orders [cite: 46]
            $sql_order = "INSERT INTO tb_orders (i_OrderID, i_CustomerID, i_EmployeeID, c_OrderDate, i_ShipperID)
                          VALUES (:order_id, :customer_id, :employee_id, :order_date, :shipper_id)";
            $stmt_order = $pdo->prepare($sql_order);
            $stmt_order->execute([
                ':order_id' => $order_id,
                ':customer_id' => $customer_id,
                ':employee_id' => $employee_id,
                ':order_date' => $order_date,
                ':shipper_id' => $shipper_id,
            ]);

            // 2. หา i_OrderDetailID สูงสุด (จาก .sql พบว่าไม่ใช่ AUTO_INCREMENT)
            $stmt_max_detail = $pdo->query("SELECT MAX(i_OrderDetailID) AS max_id FROM tb_orderdetails");
            $next_detail_id = $stmt_max_detail->fetch()['max_id'] + 1;

            // 3. INSERT tb_orderdetails [cite: 46]
            $sql_detail = "INSERT INTO tb_orderdetails (i_OrderDetailID, i_OrderID, i_ProductID, i_Quantity)
                           VALUES (:detail_id, :order_id, :product_id, :quantity)";
            $stmt_detail = $pdo->prepare($sql_detail);

            foreach ($products as $product) {
                if (!empty($product['i_ProductID']) && !empty($product['i_Quantity'])) {
                    $stmt_detail->execute([
                        ':detail_id' => $next_detail_id,
                        ':order_id' => $order_id,
                        ':product_id' => $product['i_ProductID'],
                        ':quantity' => $product['i_Quantity'],
                    ]);
                    $next_detail_id++; 
                }
            }

            $pdo->commit();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            die("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage());
        }
        
        header("Location: sales_summary.php");
        exit;

    // ############ UPDATE ############
    case 'update':
        // [cite: 16, 18]
        $order_id = $_POST['i_OrderID'];
        $customer_id = $_POST['i_CustomerID'];
        $employee_id = $_POST['i_EmployeeID'];
        $order_date = $_POST['c_OrderDate'];
        $shipper_id = $_POST['i_ShipperID'];
        $products = $_POST['products'];

        $pdo->beginTransaction();
        try {
            // 1. อัปเดต tb_orders
            $sql_update_order = "UPDATE tb_orders 
                                 SET i_CustomerID = :cid, i_EmployeeID = :eid, c_OrderDate = :odate, i_ShipperID = :sid
                                 WHERE i_OrderID = :oid";
            $stmt_update = $pdo->prepare($sql_update_order);
            $stmt_update->execute([
                ':cid' => $customer_id,
                ':eid' => $employee_id,
                ':odate' => $order_date,
                ':sid' => $shipper_id,
                ':oid' => $order_id,
            ]);

            // 2. ลบรายการสินค้าเดิม
            $pdo->prepare("DELETE FROM tb_orderdetails WHERE i_OrderID = :oid")->execute([':oid' => $order_id]);

            // 3. เพิ่มรายการสินค้าใหม่ (เหมือน Create)
            $stmt_max_detail = $pdo->query("SELECT MAX(i_OrderDetailID) AS max_id FROM tb_orderdetails");
            $next_detail_id = $stmt_max_detail->fetch()['max_id'] + 1;

            $sql_detail = "INSERT INTO tb_orderdetails (i_OrderDetailID, i_OrderID, i_ProductID, i_Quantity)
                           VALUES (:detail_id, :order_id, :product_id, :quantity)";
            $stmt_detail = $pdo->prepare($sql_detail);

            foreach ($products as $product) {
                if (!empty($product['i_ProductID']) && !empty($product['i_Quantity'])) {
                    $stmt_detail->execute([
                        ':detail_id' => $next_detail_id,
                        ':order_id' => $order_id,
                        ':product_id' => $product['i_ProductID'],
                        ':quantity' => $product['i_Quantity'],
                    ]);
                    $next_detail_id++;
                }
            }

            $pdo->commit();

        } catch (Exception $e) {
            $pdo->rollBack();
            die("เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $e->getMessage());
        }

        header("Location: sales_summary.php");
        exit;


    // ############ DELETE ############
    case 'delete':
        // [cite: 16, 18]
        $order_id = $_POST['order_id'];

        $pdo->beginTransaction();
        try {
            // 1. ลบจากตารางลูก (tb_orderdetails)
            $pdo->prepare("DELETE FROM tb_orderdetails WHERE i_OrderID = :oid")->execute([':oid' => $order_id]);

            // 2. ลบจากตารางหลัก (tb_orders)
            $pdo->prepare("DELETE FROM tb_orders WHERE i_OrderID = :oid")->execute([':oid' => $order_id]);

            $pdo->commit();

        } catch (Exception $e) {
            $pdo->rollBack();
            die("เกิดข้อผิดพลาดในการลบข้อมูล: " . $e->getMessage());
        }
        
        header("Location: sales_summary.php");
        exit;
}
?>