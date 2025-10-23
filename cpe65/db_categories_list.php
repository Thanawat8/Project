<?php
// 1. นำเข้าไฟล์เชื่อมต่อฐานข้อมูล (สมมติว่าไฟล์นี้อยู่ระดับเดียวกันกับ connDB)
require_once 'include/connDB.php'; 
include_once 'include/elementMod.php';

// 2. ดึงข้อมูลหมวดหมู่
$sql_cat = "SELECT i_CategoryID, c_CategoryName FROM tb_categories ORDER BY c_CategoryName";
$stmt_cat = $pdo->query($sql_cat);

// 3. ดึงข้อมูลทั้งหมด
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการหมวดหมู่สินค้า - Northwind</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
        body {
            background: #2A7B9B;
            background: linear-gradient(90deg, rgba(42, 123, 155, 1) 7%, rgb(3, 72, 193) 50%, rgb(2, 151, 192) 100%);
            font-family: 'Kanit', sans-serif;
        }

        h1 {
            color: white;
        }
    </style>


<body>
    
    <div class="container mt-5">
        <h2 class="mb-4">รายการหมวดหมู่สินค้า </h2>
        
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between">
                ข้อมูลหมวดหมู่
                <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-lg"></i> เพิ่มหมวดหมู่ใหม่
                </button>
            </div>
            <div class="card-body">
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="ค้นหาชื่อหมวดหมู่...">
                    </div>
                </div>

                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อหมวดหมู่</th>
                            <th>คำอธิบาย</th>
                            <th>จัดการ</th> </tr>
                    </thead>
                    <tbody>
                        <?php if (count($categories) > 0): ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cat['i_CategoryID']) ?></td>
                                    <td><?= htmlspecialchars($cat['c_CategoryName']) ?></td>
                                    <td><?= htmlspecialchars($cat['c_Description']) ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm">แก้ไข</button>
                                        
                                        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-<?= $cat['i_CategoryID'] ?>">ลบ</button>
                                    </td>
                                </tr>
                                
                                <div class="modal fade" id="deleteModal-<?= $cat['i_CategoryID'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="deleteModalLabel">ยืนยันการลบ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <div class="modal-body">
                                        คุณต้องการลบหมวดหมู่ <strong><?= htmlspecialchars($cat['c_CategoryName']) ?></strong> จริงหรือไม่?
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                        <a href="delete_category.php?id=<?= $cat['i_CategoryID'] ?>" class="btn btn-danger">ยืนยันการลบ</a>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">ไม่พบข้อมูลหมวดหมู่สินค้า</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">เพิ่มหมวดหมู่ใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="insert_category.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">ชื่อหมวดหมู่</label>
                        <input type="text" class="form-control" id="categoryName" name="categoryName" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</body>
</html>