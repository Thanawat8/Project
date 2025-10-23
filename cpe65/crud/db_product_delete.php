<?php
include_once '../include/connDB.php';

if (isset($_POST['pid'])) {
    $pid = $_POST['pid'];

    try {
        // เตรียมคำสั่ง SQL เพื่อลบ
        $sql = "DELETE FROM tb_products WHERE i_ProductID = :pid";
        $stmt = $pdo->prepare($sql);
        
        // รันคำสั่ง
        $stmt->execute([':pid' => $pid]);

    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
             die("ไม่สามารถลบสินค้านี้ได้ เนื่องจากมีข้อมูลอ้างอิงในตารางอื่น (เช่น รายการขาย)
                  <br><br><a href='../DB_product_search.php'>กลับหน้าค้นหา</a>");
        }
        die("Error deleting record: " . $e->getMessage());
    }
} else {
    die("No Product ID specified for deletion.");
}

// 1. รับค่าค้นหาที่ส่งมาจากฟอร์ม Modal
$cond_catid = $_POST['cond_catid'] ?? null;
$cond_price = $_POST['cond_price'] ?? '';

// 2. สร้าง URL สำหรับ Redirect กลับ
$redirect_url = "../DB_product_search.php";

if ($cond_catid !== null && $cond_catid !== '') {
    $query_string = http_build_query([
        'cond_catid' => $cond_catid,
        'cond_price' => $cond_price
    ]);
    $redirect_url .= "?" . $query_string;
}

header("Location: " . $redirect_url);
exit;
?>