<?php

function input_text($elementName,$strLabel,$elementType,$elementValue,$strGuide){
   echo "<div class=\"mb-3 mt-3\">
       <label for=\"$elementName\" class=\"form-label\">$strLabel:</label>
       <input type=\"$elementType\" class=\"form-control\" id=\"$elementName\"   
        placeholder=\"$strGuide\" name=\"$elementName\" value=\"$elementValue\" >
   </div>" ;
}

function input_dropdown($pdo,$elementName,$strLabel,$tbName,$fieldID,$fieldName,$elementValue){
   $sql  = "SELECT $fieldID as id , $fieldName as name FROM $tbName ";
   $stmt = $pdo->prepare($sql);
   $stmt->execute();
   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

   echo "<div class=\"mb-3 mt-3\">"; 
   echo "<label for=\"$elementName\" class=\"form-label\">$strLabel:</label>";
   echo "<select class=\"form-select\" name=\"$elementName\" id=\"$elementName\" required>" ;
   echo "<option value=''>-- กรุณาเลือก --</option>"; 
   foreach($rows as $row){
       $id     = $row['id'];
       $name   = $row['name'];
       $opt    = ($elementValue == $id) ? " selected " : "" ;
       
       echo "<option value=\"".htmlspecialchars($id)."\" $opt > ".htmlspecialchars($name)." </option>";
   }
   echo"</select>
   </div>" ;
}

function dropdown_db($pdo, $elementName, $tbName, $fieldId, $fieldName, $selectedValue = null)
{
    $sql = "SELECT DISTINCT $fieldId as id , $fieldName as name from $tbName";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<select class=\"form-select\" name=\"$elementName\" required>";
    echo "<option value=''>-- กรุณาเลือกหมวดหมู่สินค้า --</option>";
    foreach ($products as $product) {
        $id = htmlspecialchars($product['id']);
        $name = htmlspecialchars($product['name']);
        
        $opt = ($id == $selectedValue) ? " selected" : "";
        
        echo "<option value=\"$id\"$opt>$name</option>";
    }
    echo "</select>";
}
?>