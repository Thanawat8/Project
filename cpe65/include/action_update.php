<?php

include_once 'connDB.php';
print_r($_POST);
$tbName = $_POST['tb_name'];

switch($tbNname){
    case 'tb_products':
        $sql = "UPDATE tb_products SET 
            c_ProductsName = ':param_panem',
            i_SupplierID   = ':param_supid', 
            i_CategoryID   = ':paran_catid', 
            c_Unit         = ':param_unit' ,
            i_Price        = ':param_price' 
            WHERE tb_products.i_ProductID = :param_pid ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':param_pname', $_POST['c_ProductsName'], PDO::PARAM_STR);
        $stmt->bindValue(':param_supid', $_POST['i_SupplierID'], PDO::PARAM_INT);
        $stmt->bindValue(':paran_catid', $_POST['i_CategoryID'], PDO::PARAM_INT);
        $stmt->bindValue(':param_unit', $_POST['c_Unit'], PDO::PARAM_STR);
        $stmt->bindValue(':param_price', $_POST['i_Price'], PDO::PARAM_INT);
        $stmt->bindValue(':param_pid', $_POST['i_ProductID'], PDO::PARAM_INT);
                  try{
               $stmt->execute();
               echo $stmt->rowCount() . " records UPDATED successfully";
           }catch(PDOException $e){
               echo "Error: " . $e->getMessage();
           }

        break;
    default:

        break;
}
?>