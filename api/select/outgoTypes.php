<?php
include_once("../../db.php");
include_once("../../funcs.php");
include_once("../../dev/ChromePhp.php");

$type_id = 1;
$types = getOutGoTypes($connection);

if ($types) {
    $cur = getTypeByTypes($types, $type_id);
    echo json_encode(array("status"=>"success","types" => tree($cur, $types)));
    return false;
}