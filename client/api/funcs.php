<?php
function clean($value = "")
{
    $value = trim($value);
    $value = stripslashes($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value);
    return $value;
}
function mysqliToArray($mysqli_result)
{
    if(!$mysqli_result)
        return false;
    $i = 0;
    $data = null;
    while ($new = mysqli_fetch_assoc($mysqli_result)) $data[$i++] = $new;
    return $data;
}
