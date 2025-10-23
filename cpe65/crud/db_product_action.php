<?php
include_once '../include/connDB.php';

// ตรวจสอบว่ามี action ส่งมาหรือไม่
if (!isset($_POST['action'])) {
    die("No action specified.");
}

$action = $_POST['action'];

try {
    if ($action == 'create') {
        // --- Logic สำหรับ "เพิ่ม" (Create) ---
        $sql = "INSERT INTO tb_products (c_ProductName, i_SupplierID, i_CategoryID, c_Unit, i_Price) 
                VALUES (:pname, :supid, :catid, :unit, :price)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pname'  => $_POST['c_ProductName'],
            ':supid'  => $_POST['i_SupplierID'],
            ':catid'  => $_POST['i_CategoryID'],
            ':unit'   => $_POST['c_Unit'],
            ':price'  => $_POST['i_Price']
        ]);

    } elseif ($action == 'update') {
        // --- Logic สำหรับ "แก้ไข" (Update) ---
        $sql = "UPDATE tb_products SET 
                    c_ProductName = :pname, 
                    i_SupplierID = :supid, 
                    i_CategoryID = :catid, 
                    c_Unit = :unit, 
                    i_Price = :price 
                WHERE i_ProductID = :pid";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':pname'  => $_POST['c_ProductName'],
            ':supid'  => $_POST['i_SupplierID'],
            ':catid'  => $_POST['i_CategoryID'],
            ':unit'   => $_POST['c_Unit'],
            ':price'  => $_POST['i_Price'],
            ':pid'    => $_POST['i_ProductID'] // รับ ID จากฟอร์มแก้ไข
        ]);
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// === โค้ดที่แก้ไข: สร้าง URL สำหรับ Redirect กลับ ===

// 1. รับค่าค้นหาที่ส่งมาจากฟอร์ม (Add หรือ Edit)
$cond_catid = $_POST['cond_catid'] ?? null;
$cond_price = $_POST['cond_price'] ?? '';

// 2. สร้าง URL สำหรับ Redirect กลับ
$redirect_url = "../DB_product_search.php";

// 3. ตรวจสอบว่ามีค่า catid (ค่านี้จำเป็น)
if ($cond_catid !== null && $cond_catid !== '') {
    // ถ้ามีค่าค้นหา ให้แนบกลับไปกับ URL (ใช้ http_build_query เพื่อความปลอดภัย)
    $query_string = http_build_query([
        'cond_catid' => $cond_catid,
        'cond_price' => $cond_price
    ]);
    $redirect_url .= "?" . $query_string;
}

// 4. สั่ง Redirect
header("Location: " . $redirect_url);
exit;
?>