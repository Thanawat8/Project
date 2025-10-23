<?php

function getEdit($pdo, $tbName, $pkName, $pkValue)
{
    $sql = "SELECT * FROM $tbName WHERE $pkName = :param_pid;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['param_pid' => $pkValue]);
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return count($row) == 0 ? null : $row[0];
}

function editSingleData($pdo, $tbName, $pkName, $pkValue, $data) {
    
}
